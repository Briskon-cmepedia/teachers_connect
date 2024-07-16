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

    if ($_GET['search']) {

      $stopwords = ["a", "about", "above", "after", "again", "against", "all", "am", "an", "and", "any", "are", "as", "at", "be", "because", "been", "before", "being", "below", "between", "both", "but", "by", "could", "did", "do", "does", "doing", "down", "during", "each", "few", "for", "from", "further", "had", "has", "have", "having", "he", "he'd", "he'll", "he's", "her", "here", "here's", "hers", "herself", "him", "himself", "his", "how", "how's", "i", "i'd", "i'll", "i'm", "i've", "if", "in", "into", "is", "it", "it's", "its", "itself", "let's", "me", "more", "most", "my", "myself", "nor", "of", "on", "once", "only", "or", "other", "ought", "our", "ours", "ourselves", "out", "over", "own", "same", "she", "she'd", "she'll", "she's", "should", "so", "some", "such", "than", "that", "that's", "the", "their", "theirs", "them", "themselves", "then", "there", "there's", "these", "they", "they'd", "they'll", "they're", "they've", "this", "those", "through", "to", "too", "under", "until", "up", "very", "was", "we", "we'd", "we'll", "we're", "we've", "were", "what", "what's", "when", "when's", "where", "where's", "which", "while", "who", "who's", "whom", "why", "why's", "with", "would", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves"];
      $wordsFromSearchString = str_word_count($_GET['search'], true, '0..9_äöüÄÖÜ,/');
      $finalWords = array_diff($wordsFromSearchString, $stopwords);
      $search_term_cleaned = implode(" ", $finalWords);
      // $search_term_cleaned = preg_replace('~\b[a-z]{1,2}\b~', '', $_GET['search']);
      // $search_term_cleaned = html_entity_decode($search_term_cleaned, ENT_QUOTES);
      $search_term = html_entity_decode($_GET['search'], ENT_QUOTES);
      $search_domain = $_GET['search-domain'];

      // print_r($accessibleGroups);
      // print_r($_SESSION['myGroups']);

      // Connect to database
      try {        
        $public_groups = json_decode(json_encode(get_groups_by_privacy()), true);
        $accessibleGroups = array();
        foreach($public_groups as $public){       
          $accessibleGroups[] = $public['_id']['$oid'];
        }

        if ($_GET['sort-order'] == 'newest') {
          $posts = json_decode(json_encode(SearchEngine::boot()->search_filtered_posts_ordered_relevancy($search_term, $accessibleGroups, 30, $offset, 'newest')), true);
        } else {
          $posts = json_decode(json_encode(SearchEngine::boot()->search_filtered_posts_ordered_relevancy($search_term, $accessibleGroups, 30, $offset)), true);
        }

        if($posts) {
          $authors = [];
          $commenters = [];
          foreach ($posts as $post) {
            $author_id = $post['userId'];
            array_push($authors, $author_id);
            foreach ($post['comments'] as $comment) {
              $commenter_id = $comment['userId'];
              array_push($commenters, $commenter_id);
            }
          }
          $posts_authors = json_decode(json_encode(SearchEngine::boot()->get_users($authors)), true);
          $posts_commenters = json_decode(json_encode(SearchEngine::boot()->get_users($commenters)), true);
        }
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      $feed_title = "Results: " . htmlspecialchars_decode($search_term, ENT_QUOTES);
      $partner_logo = "icon-search-header.svg";
      $feed_number = "0";

      $activity_data[] = $search_term;
      new_activity_log($_SESSION['uid'], 'searched content', $activity_data);

    } elseif ($_GET['id']) { // if group ID supplied, display only posts by that group

      // Get group data
      try {
        $group = json_decode(json_encode(get_group($_GET['id'])), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      // // Get subscribed groups
      // try {
      //   $user_groups = json_decode(json_encode(get_groups_by_user($_SESSION['uid'])), true);
      // } catch (Exception $e) {
      //   echo $e->getMessage();
      //   die();
      // }
      //
      // $myGroups = [];
      // foreach ($user_groups as $user_group) {
      //   $myGroups[] = $user_group['_id']['$oid'];
      // }

      $partner_name = $group[0]['name'];
      $partner_logo = $group[0]['logo'];
      $partner_description = $group[0]['description'];
      $group_admins = $group[0]['admins'];
      $group_members = $group[0]['users'];
      $group_privacy = $group[0]['privacy'];
      $group_type = $group[0]['type'];

      if ( $group_privacy == 'private' AND !in_array($_GET['id'], $myGroups) ) { // Block access to private community feeds if you are not a member

        // Create page body
        $page_body = $templates->render('private-community-blocked',
          []
        );

        // Create page header
        $page_header = $templates->render('layout-header',
          [
            'partner_name' => $partner_name,
            'title' => 'Access Restricted - ' . $partner_name . ' Feed'
          ]
        );

        // Create page footer
        $page_footer = $templates->render('layout-footer',
          []
        );

        // Display page header
        echo $page_header;

        // Display page body
        echo $page_body;

        // Display page footer
        echo $page_footer;

        die();

      } else {

        // Check if visitor is following community
        if (is_array($group_members)) { } else {
          $group_members[] = $group_members;
        }
        if (in_array($_SESSION['uid'], $group_members)) {
            $community_followed = 1;
        }
        if (in_array($_SESSION['uid'], $group_admins)) {
            $community_admin = 1;
        }

        // if ($group_type == 'TPP') {
        //   $posts = json_decode(json_encode(get_posts_by_group($_GET['id'], $group_members, 30, $offset)), true);
        // } else {
          $posts = json_decode(json_encode(get_posts_by_exclusive_group($_GET['id'], 30, $offset)), true);
        // }

        if($posts) {
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
        }

      }

      $feed_title = $partner_name . " Community";
      $feed_number = $group[0]['_id']['$oid'];

    } else { // If no group ID supplied, display all posts

      // Connect to database
      try {
      
      $public_groups = json_decode(json_encode(get_groups_by_privacy()), true);
      $audience = array();
      foreach($public_groups as $public){       
        $audience[] = $public['_id']['$oid'];
      }     
      $posts = json_decode(json_encode(get_all_public_posts(30, $offset, $audience)), true);       
     
      if($posts) {      
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
        }
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      $feed_title = "TeachersConnect Community";
      $feed_number = "0";
      $partner_logo = "logo-teachersconnect.svg";
      $partner_description = "Join a conversation and find teachers like you.  Ask or answer a question, share your thoughts in a post, or join a conversation that’s already started.";
      //$feed_number = "001";

    }

    $last_count = 0;
    $feed_cards = "";
    $total_count = count($posts);

    if ($total_count < 30) {
      $last_page = 1;
    }

    // Define previous and next links for pagination
    if (!$last_page AND $feed_number) {
      $url_prev = site_url() . "/feed.php?id=" . $feed_number . "&page=" . $next_page;
    }
    if (!$last_page AND !$feed_number) {
      $url_prev = site_url() . "/feed.php?page=" . $next_page;
    }
    if ($_GET['search']) {
      $url_prev = site_url() . "/feed.php?search=" . $_GET['search'] . "&page=" . $next_page . "&sort-order=" . $_GET['sort-order'];
    }

    if ($current_page != 1) {
      if (!$feed_number AND !$_GET['search']) {
        $url_next = site_url() . "/feed.php?page=" . ($current_page - 1);
      }
      if ($feed_number AND !$_GET['search']) {
        $url_next = site_url() . "/feed.php?id=" . $feed_number . "&page=" . ($current_page - 1);
      }
      if ($_GET['search']) {
        $url_next = site_url() . "/feed.php?search=" . $_GET['search'] . "&page=" . ($current_page - 1) . "&sort-order=" . $_GET['sort-order'];
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


          // Highlight search terms
          if ($search_term) {
            $preview_content_delinked_highlighted = highlight($preview_content_delinked, $search_term_cleaned);
            $full_post_content_delinked_highlighted = highlight($full_post_content_delinked, $search_term_cleaned);
            if (strpos($preview_content_delinked_highlighted, 'class="search-highlight') == false) {
              $notice_search_deeper = "<span class=\"search-notice-highlight\">Your search terms appear deeper in this post.</span>";
            }
            if (strpos($full_post_content_delinked_highlighted, 'class="search-highlight"') == false) {
              $notice_search_deeper = "<span class=\"search-notice-secondary\">Related search terms appear in this post.</span>";
            }
          }

          // // Extract first YouTube url within post content (if any)
          // preg_match_all('!https?://\S+!', $post_content_retargeted, $matches);
          // $all_urls = implode($matches[0]);
          // parse_str( parse_url( $all_urls, PHP_URL_QUERY ), $url_variables );
          // $youtube_video = $url_variables['v'];

          // Extract YouTube url within post content (if any)
          preg_match_all('!https?://\S+!', $post_content_delinked, $matches);
          $first_url = $matches[0];
          $youtube_video = getYoutubeId(strip_tags($first_url[0]));
          $youtube_video = rtrim($youtube_video, ',');
          $youtube_video = rtrim($youtube_video, '.');

          // Render posts using feedcard template
          $feed_cards = $feed_cards . $templates->render('feed-card',
            [
              'post_id' => $post['id'] ?? $post['_id']['$oid'], //User $post['id'] if opensearch and $post['_id']['$oid'] for mongo
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
              'flag_content'=> $post['questionableContent']['flagContent'],
              'userRole' => $_SESSION['userRole']  
            ]
          );

          $last_count++;

        } else {  }

      }

    } else {


      if ($_GET['search']) {

        // Display no posts found messaging for search
        $feed_cards = $feed_cards . $templates->render('error',
          [
            'page' => 'search',
          ]
        );

      } else {

        // Display no posts found messaging
        $feed_cards = $feed_cards . $templates->render('error',
          [
            'page' => 'posts',
            'fullName' => $user[0]['firstName'] . ' ' . $user[0]['lastName'],
            'firstName' => $user[0]['firstName'],
            'lastName' => $user[0]['lastName']
          ]
        );

      }

    }

    // Create page header
    $page_header = $templates->render('layout-header',
      [
        'page' => 'feed',
        'title' => $feed_title,
        'partner_logo' => $partner_logo,
        'partner_description' => $partner_description,
        'group_privacy' => $group_privacy,
        'feed_number' => $feed_number,
        'search_term' => htmlentities($search_term, ENT_QUOTES),
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
        'search_order' => $_GET['sort-order'],
        'search_domain' => $search_domain,
        'community_followed' => $community_followed,
        'community_admin' => $community_admin
      ]
    );

    // Create page footer
    $page_footer = $templates->render('layout-footer',
      [
        'page' => 'feed',
        'title' => $feed_title,
        'feed_number' => $feed_number,
        'search_term' => htmlentities($search_term, ENT_QUOTES),
        'group_id' => $_GET['id'],
        'create_post' => $_GET['createpost'],
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
        'search' => $search_term_cleaned
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
