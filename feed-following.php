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



    // Connect to database
    try {
      $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);

      $feed_title = "TC Your Connections View - " . $full_name;
      $user_following = $user[0]['following'];

      if ($user_following == NULL) { $user_following = []; }

      $user_following_count = count($user_following);

      if ($user_following_count > 0) {

        $posts = json_decode(json_encode(get_filtered_posts_by_users(30, $user_following, $_SESSION['myGroups'], $offset)), true);

        if($posts) {
          $authors = [];
          foreach ($posts as $post) {
            $author_id = new MongoDB\BSON\ObjectID($post['userId']);
            array_push($authors, $author_id);
          }
          $posts_authors = json_decode(json_encode(get_authors($authors)), true);
        }

      }

    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }



    $last_count = 0;
    $feed_cards = "";
    $total_count = count($posts);

    if ($total_count < 30) {
      $last_page = 1;
    }

    // Define previous and next links for pagination
    if (!$last_page) {
      $url_prev = site_url() . "/feed-following.php?page=" . $next_page;
    }

    if ($current_page != 1) {
      $url_next = site_url() . "/feed-following.php?page=" . ($current_page - 1);
    }

    // Collate post cards for display
    if ($total_count > 0) {

      // Count totals and stack arrays for different filetypes
      foreach ($posts as $post) {

        $i = 1;
        $file_num = 0;
        $photo_num = 0;
        $post_photos = [];
        $post_files = [];

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

        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $preview_content = substr($post['text'], 0, 335);
        if (strlen($post['text']) > 335) { $preview_content = $preview_content . '...'; }
        $preview_content = $purifier->purify($preview_content);

        // Remove any existing anchor links while retaining surrounded text
        $preview_content_delinked = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $preview_content);
        $post_content_delinked = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $post['text']);

        // // Extract first YouTube url within post content (if any)
        // preg_match_all('!https?://\S+!', $post_content_delinked, $matches);
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
            'post_id' => $post['_id']['$oid'],
          	'post_type' => $post['type'],
          	'post_anon' => $post['isAnonymous'],
          	'post_author' => $post['userId'],
            'post_author_fullname' => ucfirst($posts_authors[$post['userId']]['firstName']) . ' ' . ucfirst($posts_authors[$post['userId']]['lastName']),
            'post_author_avatar' => $posts_authors[$post['userId']]['avatar'],
            'preview_content' => $preview_content_delinked,
          	// 'post_content' => $post['text'],
            'youtube_video' => strip_tags($youtube_video),
            'post_photos' => $post_photos,
            'post_photos_count' => $photo_num,
            // 'post_files' => $post_files,
            'post_files_count' => $file_num,
          	// 'post_photos' => $post['photos'],
            // 'post_photos_count' => count($post['photos']),
          	'post_comments' => $post['comments'],
            'post_time' => $post['time']['$date'],
          	'post_comment_count' => count($post['comments']),
          	'post_helpful_count' => count($post['reactions']['thumbsup']),
          	'post_metoo_count' => count($post['reactions']['highfive'])
          ]
        );

        if ($last_count == 0) {

          $start_date = timestamp($post['time']['$date'], 'j M Y');
          $start_date_raw = $post['time']['$date'];

        }

        if ( $last_count == ( $total_count - 1 ) ) {

          $end_date = timestamp($post['time']['$date'], 'j M Y');
          $end_date_raw = $post['time']['$date'];

        }

        $last_count++;

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
            'page' => 'connections',
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
        'page' => 'feed-following',
        'title' => $feed_title,
        'partner_logo' => $partner_logo,
        'partner_description' => $partner_description,
        'feed_number' => $feed_number,
        'search_term' => $search_term,
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
        'alert' => $_GET['alert']
      ]
    );

    // Create page footer
    $page_footer = $templates->render('layout-footer',
      [
        'page' => 'feed',
        'title' => $feed_title,
        'feed_number' => $feed_number,
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
