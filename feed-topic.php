<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

  if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

    if (intval($_GET['page'])) {
      $current_page = $_GET['page'];
    } else {
      $current_page = 1;
    }
    if ($current_page >= 2) {
      $prev_page = $current_page - 1;
    }
    $next_page = $current_page + 1;
    $offset = ($current_page - 1)*30;

    if ($_GET['topic']) { // If topic supplied, display posts attributed to them

      $topic[] = $_GET['topic'];

      // Connect to database
      try {
        $posts = json_decode(json_encode(get_posts_by_topic($topic, $accessibleGroups, 30, $offset)), true);
        if($posts) {
          // print_r($posts);
          $authors = [];
          $commenters = [];
          foreach ($posts as $post) {
            $author_id = new MongoDB\BSON\ObjectID($post['userId']);
            array_push($authors, $author_id);
            foreach ($post['comments'] as $comment) {
              $commenter_id = new MongoDB\BSON\ObjectID($comment['userId']);
              array_push($commenters, $commenter_id);
            }
          }
          $posts_authors = json_decode(json_encode(get_authors($authors)), true);
          $posts_commenters = json_decode(json_encode(get_authors($commenters)), true);
          // print_r($posts_commenters);
        }
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      $topic_term = html_entity_decode($_GET['topic'], ENT_QUOTES);
      $feed_title = "Posts about the topic: " . htmlspecialchars_decode($topic_term, ENT_QUOTES);
      // $partner_logo = "icon-search-header.svg";
      $feed_number = "0";

    }

    $last_count = 0;
    $feed_cards = "";
    $total_count = count($posts);

    if ($total_count < 30) {
      $last_page = 1;
    }

    // Define previous and next links for pagination
    if (!$last_page AND $feed_number) {
      $url_prev = site_url() . "/feed-topic.php?id=" . $feed_number . "&page=" . $next_page;
    }
    if (!$last_page AND !$feed_number) {
      $url_prev = site_url() . "/feed-topic.php?page=" . $next_page;
    }
    if ($_GET['topic']) {
      $url_prev = site_url() . "/feed-topic.php?topic=" . $_GET['topic'] . "&page=" . $next_page . "&sort-order=" . $_GET['sort-order'];
    }

    if ($current_page != 1) {
      if (!$feed_number AND !$_GET['search']) {
        $url_next = site_url() . "/feed-topic.php?page=" . ($current_page - 1);
      }
      if ($feed_number AND !$_GET['search']) {
        $url_next = site_url() . "/feed-topic.php?id=" . $feed_number . "&page=" . ($current_page - 1);
      }
      if ($_GET['topic']) {
        $url_next = site_url() . "/feed-topic.php?topic=" . $_GET['topic'] . "&page=" . ($current_page - 1) . "&sort-order=" . $_GET['sort-order'];
      }
    }

    // Collate post cards for display
    if ($total_count > 0) {

      // Count totals and stack arrays for different filetypes
      foreach ($posts as $post) {

        if ($last_count == 0) {

          $start_date = timestamp($post['time']['$date'], 'j M Y');
          $start_date_raw = $post['time']['$date'];

        }

        $end_date = timestamp($post['time']['$date'], 'j M Y');
        $end_date_raw = $post['time']['$date'];

        if ($posts_authors[$post['userId']]['trusted'] == TRUE ) {

          $i = 1;
          $file_num = 0;
          $photo_num = 0;
          $post_photos = [];
          $post_files = [];
          $notice_search_deeper = '';
          $config = HTMLPurifier_Config::createDefault();
          $purifier = new HTMLPurifier($config);

          foreach ($post['photos'] as $post_attachment) {

            if (is_array($post_attachment)) { // Isolate S3 filename for display
              $post_attachment = $post_attachment[0];
            }

            $file_ext = strtolower(pathinfo(parse_url($post_attachment)['path'], PATHINFO_EXTENSION));

            if ( in_array( $file_ext, $image_filetypes ) ) {

              $post_photos[] = $post_attachment;
              $photo_num++;

            } else {

              // $post_files[] = $post_attachment;
              $file_num++;

            }

            $i++; if($i > 4) break;

          }

          // Reduce content for preview and clean up any tags
          $preview_content = substr($post['text'], 0, 335);
          if (strlen($post['text']) > 335) { $preview_content = $preview_content . '...'; }
          $preview_content = $purifier->purify($preview_content);

          // Pull together all text from a post
          $full_post = $post['text'];
          $comment_count = 0;
          foreach ($post['comments'] as $comment) {
            if ($posts_commenters[$comment['userId']]['trusted'] == TRUE ) {
              $full_post = $full_post . $comment['text'];
              $comment_count++;
            }
          }

          // Remove any existing anchor links while retaining surrounded text
          $preview_content_delinked = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $preview_content);
          $post_content_delinked = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $post['text']);
          $full_post_content_delinked = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $full_post);

          // Extract YouTube url within post content (if any)
          preg_match_all('!https?://\S+!', $post_content_delinked, $matches);
          $first_url = $matches[0];
          $youtube_video = getYoutubeId(strip_tags($first_url[0]));
          $youtube_video = rtrim($youtube_video, ',');
          $youtube_video = rtrim($youtube_video, '.');

          // Render posts using feedcard template
          $feed_cards = $feed_cards . $templates->render('feed-card',
            [
              'post_id' => $post['_id']['$oid'],
            	'post_type' => $post['type'],
            	'post_anon' => $post['isAnonymous'],
            	'post_author' => $post['userId'],
              //fixed missing quotes
              'post_author_fullname' => ucfirst($posts_authors[$post['userId']]['firstName']) . ' ' . ucfirst($posts_authors[$post['userId']]['lastName']),
              'post_author_avatar' => $posts_authors[$post['userId']]['avatar'],
              'post_author_trust' => $posts_authors[$post['userId']]['trusted'],
              'preview_content' => $preview_content_delinked,
            	// 'post_content' => $post_content_delinked,
              'youtube_video' => strip_tags($youtube_video),
              'post_photos' => $post_photos,
              'post_photos_count' => $photo_num,
              // 'post_files' => $post_files,
              'post_files_count' => $file_num,
            	// 'post_photos' => $post['photos'],
              // 'post_photos_count' => count($post['photos']),
            	'post_comments' => $post['comments'],
              'post_time' => $post['time']['$date'],
            	// 'post_comment_count' => count($post['comments']),
              'post_comment_count' => $comment_count,
            	'post_helpful_count' => count($post['reactions']['thumbsup']),
            	'post_metoo_count' => count($post['reactions']['highfive']),
              'notice_search_deeper' => $notice_search_deeper,
              'search_term' => htmlentities($search_term, ENT_QUOTES),
              'userRole' => $_SESSION['userRole'],
              'flag_content' => $post['questionableContent']['flagContent']
            ]
          );



          $last_count++;

        } else {   }

      }

    } else {


      if ($_GET['topic']) {

        // Display no posts found messaging for search
        $feed_cards = $feed_cards . $templates->render('error',
          [
            'page' => 'topic',
          ]
        );

      }

    }

    // Determine if current topic followed by user
    $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);
    $topics_followed = $user[0]['topicsFollowed'];
    if (!$topics_followed) { $topics_followed = []; }
    if (in_array(strtolower($_GET['topic']), $topics_followed)) {
        $topic_followed = 1;
    }

    // Create page header
    $page_header = $templates->render('layout-header',
      [
        'page' => 'feed-topic',
        'title' => $feed_title,
        'partner_logo' => $partner_logo,
        'partner_description' => $partner_description,
        'feed_number' => $feed_number,
        'topic_term' => htmlentities($topic_term, ENT_QUOTES),
        'topic_term_trimmed' => str_replace(" ", "-", $topic_term),
        'current_page' => $current_page,
        'prev_page' => $prev_page,
        'next_page' => $next_page,
        'url_next' => $url_next,
        'url_prev' => $url_prev,
        'last_page' => $last_page,
        'total_count' => $total_count,
        'start_date_raw' => $start_date_raw,
        'end_date_raw' => $end_date_raw,
        'end_date' => $end_date,
        'start_date' => $start_date,
        'alert' => $_GET['alert'],
        'topic_followed' => $topic_followed
      ]
    );

    // Create page footer
    $page_footer = $templates->render('layout-footer',
      [
        'page' => 'feed',
        'title' => $feed_title,
        'feed_number' => $feed_number,
        'topic_term' => htmlentities($topic_term, ENT_QUOTES),
        'group_id' => $_GET['id'],
        'current_page' => $current_page,
        'prev_page' => $prev_page,
        'next_page' => $next_page,
        'url_next' => $url_next,
        'url_prev' => $url_prev,
        'last_page' => $last_page,
        'total_count' => $total_count,
        'start_date_raw' => $start_date_raw,
        'end_date_raw' => $end_date_raw,
        'end_date' => $end_date,
        'start_date' => $start_date
      ]
    );


    // Display page header
    echo $page_header;

    // Display post cards
    echo $feed_cards;

    // Display page footer
    echo $page_footer;



  } else { // Maintenance mode active - redirect to notice

    session_destroy();
    redirect('/maintenance.php');
    die();

  }

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}
