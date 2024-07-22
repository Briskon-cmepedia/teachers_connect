<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

// print_r($sessions->sessionCheck());
// echo "hii";
// exit();
if ($sessions->sessionCheck()) { // Display view if user has valid session


  // Variable Setup
  $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

  if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

    $page_title = "TeachersConnect Home";
    $dateTwoWeeksAgo = time()-(28 * 24 * 60 * 60);
    $dateToday = time();
    $posts = [];

    // Get most popular posts from TC community (two weeks)
    try {
      $postsPopular = json_decode(json_encode(get_most_popular_posts($dateTwoWeeksAgo, $dateToday, 20)), true);

    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }
  
    
    $posts['popularposts'] = $postsPopular;


    // Get latest posts from TC community
    try {
      $public_groups = json_decode(json_encode(get_groups_by_privacy()), true);
      $audience = array();
      foreach($public_groups as $public){
        $audience[] = $public['_id']['$oid'];
      }

      $posts[0] = json_decode(json_encode(get_all_public_posts(20, NULL, $audience)), true);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    // // Get latest posts from partner communities
    // if ($_SESSION['partners']) {
    //   foreach ($_SESSION['partners'] as $partner) {
    //     try {
    //       $posts[$partner['id']] = json_decode(json_encode(get_posts_by_exclusive_group($partner['id'], 20)), true);
    //     } catch (Exception $e) {
    //       echo $e->getMessage();
    //       die();
    //     }
    //   }
    // }

    // Get latest posts from partner communities
    if ($user_groups) {
      foreach ($user_groups as $partner) {
        try {
          $posts[$partner['_id']['$oid']] = json_decode(json_encode(get_posts_by_exclusive_group($partner['_id']['$oid'], 20)), true);
        } catch (Exception $e) {
          echo $e->getMessage();
          die();
        }
      }
    }

    // Get latest posts from connections
    try {
      $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);
      $user_following = $user[0]['following'];
      if ($user_following == NULL) { $user_following = []; }
      $user_following_count = count($user_following);
      if ($user_following_count > 0) {
        $posts['connections'] = json_decode(json_encode(get_filtered_posts_by_users(20, $user_following, $_SESSION['myGroups'])), true);
      }
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    // Get unanswered questions from TC community
    try {
      $posts['questions'] = json_decode(json_encode(get_unanswered_questions()), true);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    // Get topics followed by user
    $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);

    if (is_array($user[0]['topicsFollowed'])) {
      $topics_followed = array_values($user[0]['topicsFollowed']);
    } else {
      $topics_followed = [];
    }

    if ($topics_followed) {
      // Get latest posts from followed topics
      try {
        $posts['followedtopics'] = json_decode(json_encode(get_posts_by_topics($topics_followed, $accessibleGroups, 20)), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }
    }

    // echo "<pre>";
    // print_r($row_posts['followedtopics']);
    // echo "</pre>";
    // die();

    if ($posts) {
      $row_posts = [];
      $authors = [];
      $commenters = [];
      $post_row_display = [];
      foreach ($posts ?? [] as $partner_id => $post_row) {
        $num = 0;
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);

        // Get authors and commenters info
        if (isset($post_row)) {
          foreach ($post_row as $post) {
            $author_id = new MongoDB\BSON\ObjectID($post['userId']);
            array_push($authors, $author_id);
            foreach ($post['comments'] as $comment) {
              $commenter_id = new MongoDB\BSON\ObjectID($comment['userId']);
              array_push($commenters, $commenter_id);
            }
          }
        }
        try {
          $posts_authors = json_decode(json_encode(get_authors($authors)), true);
        } catch (Exception $e) {
          echo $e->getMessage();
          die();
        }
        try {
          $posts_commenters = json_decode(json_encode(get_authors($commenters)), true);
        } catch (Exception $e) {
          echo $e->getMessage();
          die();
        }

        // Process posts for display
        foreach ($post_row ?? [] as $post) {
          $count_comments = 0;
          $count_files = 0;
          $count_helpfuls = 0;
          if ($posts_authors[$post['userId']]['trusted'] == TRUE) {
            $row_posts[$partner_id][$num]['id'] = $post['_id']['$oid'];
            $row_posts[$partner_id][$num]['author'] = $post['userId'];
            // Reduce content for preview and clean up any tags
            $preview_content = $purifier->purify($post['text']);
            $preview_content_delinked = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $preview_content);
            $row_posts[$partner_id][$num]['text'] = $preview_content_delinked;
            // Extract YouTube url within post content (if any)
            preg_match_all('!https?://\S+!', $preview_content_delinked, $matches);
            $first_url = $matches[0];
            $youtube_video = getYoutubeId(strip_tags($first_url[0]));
            $youtube_video = rtrim($youtube_video, ',');
            $youtube_video = rtrim($youtube_video, '.');
            $row_posts[$partner_id][$num]['youtube'] = $youtube_video;
            $row_posts[$partner_id][$num]['date'] = $post['time']['$date'];
            $row_posts[$partner_id][$num]['anon'] = $post['isAnonymous'];
            $row_posts[$partner_id][$num]['views'] = $post['totalViews'];
            $row_posts[$partner_id][$num]['categories'] = $post['categories'];
            $row_posts[$partner_id][$num]['sameheres'] = count($post['reactions']['highfive']);
            $row_posts[$partner_id][$num]['countComments'] = $post['countComments'];

            // 10 points for each comment
            $row_posts[$partner_id][$num]['commentsScore'] = count($post['comments']) * 10;

            // 3 points for each post reaction, including reactions on comments
            $numberOfCommentReactions = 0;
            foreach ($post['comments'] as $comment) {
              $numberOfCommentReactions += count($comment['reactions']['highfive']) + count($comment['reactions']['thumbsup']);
            }
            $row_posts[$partner_id][$num]['helpfulsScore'] = ($numberOfCommentReactions + (count($post['reactions']['highfive']) + count($post['reactions']['thumbsup']))) * 3;

            // 1 points for each view
            $row_posts[$partner_id][$num]['viewsScore'] = $post['totalViews'];
            $row_posts[$partner_id][$num]['totalScore'] = $row_posts[$partner_id][$num]['commentsScore'] + $row_posts[$partner_id][$num]['helpfulsScore'] + $row_posts[$partner_id][$num]['viewsScore'];
            $count_helpfuls = count($post['reactions']['thumbsup']);
            $count_files = count($post['photos']);
            // Isolate first image from attachments
            if (is_array($post['photos'])) {
              $file_ext = strtolower(pathinfo(parse_url($post['photos'][0][0])['path'], PATHINFO_EXTENSION));
              if ( in_array( $file_ext, $image_filetypes ) ) {
                $row_posts[$partner_id][$num]['featuredphoto'] = $post['photos'][0][0];
              }
            }
            foreach ($post['comments'] as $comment) {
              if ($posts_commenters[$comment['userId']]['trusted'] == TRUE) {
                $count_helpfuls = $count_helpfuls + count($comment['reactions']['thumbsup']);
                $count_files = $count_files + count($comment['photos']);
                $count_comments++;
              }
            }
            $row_posts[$partner_id][$num]['comments'] = $count_comments;
            $row_posts[$partner_id][$num]['helpfuls'] = $count_helpfuls;
            $row_posts[$partner_id][$num]['files'] = $count_files;
            $post_detail = json_decode(json_encode(get_posts_by_id($post['_id']['$oid'])), true);

            $row_posts[$partner_id][$num]['flag_content']='';
            if(isset($post_detail[0]['questionableContent']['flagContent'])){
              $row_posts[$partner_id][$num]['flag_content'] = $post_detail[0]['questionableContent']['flagContent'];
            }
            $num++;
          }
        }
      }
    }

    // If popular posts exist, collate popular topics
    if ($row_posts['popularposts']) {
      foreach ($row_posts['popularposts'] as $popular_post) {
        if ($popular_post['categories']) {
          foreach ($popular_post['categories'] as $category) {
            $topics[] = $category;
          }
        }
      }
      if (!$topics) { $topics = []; }
      $topics_counted = array_count_values($topics);
      $topics_counted = array_slice($topics_counted, 0, 20);
    }

    // $promos[0]['text'] = "<img src='img/promo-home.png'><h3>Welcome home ".$_SESSION['firstName']."</h3>See what is popular and current from teachers and communities that interest you. <span class='mobile-hide'>Follow others and join communities to see more on your customized homepage.</span>";
    // // $promos[9]['text'] = "<img src='img/promo-pd.png'><h3>Back to school tips <span class='mobile-hide'>& tricks</span></h3><a href='https://www.teachersconnect.online/live.php'>Register here</a> to join three highly respected facilitators in our 'Pop-up' professional development series on Monday 12 August at 7pm EDT. <span class='mobile-hide'>Get the first couple of weeks right, and students will be positioned for a year of success!</span>";
    // // $promos[9]['text'] = "<img src='img/promo-pd.png'><h3>Back to school tips <span class='mobile-hide'>& tricks</span></h3>Did you miss our live 'Pop-up' PD event? You can <a href='https://www.teachersconnect.online/live.php'>view the recorded session here</a> for a limited time. <span class='mobile-hide'>Get the first couple of weeks right, and students will be positioned for a year of success!</span>";
    // $promos[10]['text'] = "<img src='img/promo-find-people.png'><h3>Find your pack</h3>Use member search, member directories, and community feeds to find and follow teachers that click with your schtick! <span class='mobile-hide'>Tap Follow on their profile page and their posts will appear here on your homepage.</span>";
    // $promos[15]['text'] = "<img src='img/promo-discuss.png'><h3>Connect with organizations</h3><a href='edit-affiliate.php'>Join our partner communities</a> to find new resources and relationships, or reconnect with your college alumni and staff. <span class='mobile-hide'>New community posts will appear here on your homepage.</span>";
    // $promos[20]['text'] = "<img src='img/promo-profile.png'><h3>Meet new people</h3>Make it easy for other teachers to connect with you. <a href='edit-profile.php'>Complete your profile</a> to tell others who you are, what you have done and what you are interested in.";

    // // Create homepage promo row
    // $post_row_display[] = $templates->render('home-row-top',
    //   [
    //     'posts' => $promos
    //   ]
    // );

    // Create homepage featured row
    // $post_row_display[] = $templates->render('home-row-featured', []);

    if ($topics_counted) {
      // Create most popular topics row
      $post_row_display[] = $templates->render('home-row-topics',
        [
          'topics' => $topics_counted
        ]
      );
    }

    // Remove posts > 28 days old from display
    $recentPosts = [];
    foreach ($row_posts['popularposts'] ?? [] as $post) {
      $currentDateInMilliseconds = round(microtime(true) * 1000);
      $postDateAsStringInMilliseconds = strval($post['date']);
      $todayMinus28Days = $currentDateInMilliseconds - (28*1000*60*60*24);
      if ($postDateAsStringInMilliseconds >= $todayMinus28Days) {
        array_push($recentPosts, $post);
      }
    }

    $row_posts['popularposts'] = $recentPosts;

    // If post scores are equal, sort by most recent to least recent, else sort by totalScore
    function order_by_score_date($a, $b) {
      if ($a['totalScore'] == $b['totalScore']) {
          // score is the same, sort by date
          if ($b['date'] > $a['date']) {
              return 1;
          }
      }

      // sort the higher score first:
      return $b['totalScore'] - $a['totalScore'];
    }

    // sort Trending Posts based on Total Score
    if ($row_posts['popularposts']) {
      usort($row_posts['popularposts'], "order_by_score_date");
    }

    // Create most popular posts row
    $post_row_display[] = $templates->render('home-row',
      [
        'row_title' => 'Trending Posts',
        // 'row_url' => 'feed.php',
        'posts' => $row_posts['popularposts'],
        'posts_authors' => $posts_authors,
        'userRole' => $_SESSION['userRole']
      ]
    );

    // if ($topics_followed) {
      // Create followed topics row
      $post_row_display[] = $templates->render('home-row',
        [
          'row_title' => 'Followed Topics',
          'row_url' => 'topics.php',
          'posts' => $row_posts['followedtopics'],
          'posts_authors' => $posts_authors,
          'userRole' => $_SESSION['userRole']
        ]
      );
    // }

    // Create unanswered questions row
    $post_row_display[] = $templates->render('home-row',
      [
        'row_title' => 'Unanswered Questions',
        'row_url' => '',
        'view_all' => is_countable($row_posts['questions']) ? count($row_posts['questions']) : 0 > 20,
        'posts' => is_array($row_posts['questions']) ? array_slice($row_posts['questions'], 0, 20) : array(),
        'posts_authors' => $posts_authors,
        'userRole' => $_SESSION['userRole']
      ]
    );

    // Create TC community row
    $post_row_display[] = $templates->render('home-row',
      [
        'row_title' => 'TeachersConnect Community',
        'row_url' => 'feed.php',
        'posts' => $row_posts[0],
        'posts_authors' => $posts_authors,
        'userRole' => $_SESSION['userRole']
      ]
    );

    // Create partner rows
    if ($user_groups) {
      foreach ($user_groups as $partner) {
        $post_row_display[] = $templates->render('home-row',
          [
            'row_title' => $partner['name'],
            'row_url' => 'feed.php?id='.$partner['_id']['$oid'],
            'posts' => $row_posts[$partner['_id']['$oid']],
            'posts_authors' => $posts_authors,
            'userRole' => $_SESSION['userRole']
          ]
        );
      }
    }

    // Create connections row
    $post_row_display[] = $templates->render('home-row',
      [
        'row_title' => 'Your Connections',
        'row_url' => 'feed-following.php',
        'posts' => $row_posts['connections'],
        'posts_authors' => $posts_authors,
        'userRole' => $_SESSION['userRole']
      ]
    );

    // Create page header
    $page_header = $templates->render('layout-header',
      [
        'page' => 'home',
        'title' => $page_title,
        'alert' => $_GET['alert']
      ]
    );

    // Create page footer
    $page_footer = $templates->render('layout-footer',
      [
        'page' => 'home',
        'search_focus' => $_GET['searchfocus']
      ]
    );

    // Display page header
    echo $page_header;

    // Display post cards
    echo implode($post_row_display);

    // Display page footer
    echo $page_footer;


  } else { // Maintenance mode active - redirect to notice

    session_destroy();
    redirect('/maintenance.php');
    die();

  }

} else { // Redirect user to login page if no valid session
//  echo "hii";
//  exit();
  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}
