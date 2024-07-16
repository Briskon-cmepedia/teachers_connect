<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
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

      $user = json_decode(json_encode(get_user($_GET["id"])), true);
      $groups = json_decode(json_encode(get_groups_by_user($_GET["id"])), true);

      if ($_SESSION['uid'] == $_GET["id"]) { // Query DB for your own posts

        $posts = json_decode(json_encode(get_posts_by_user(30, $_GET["id"], $offset)), true);

      } else { // Query DB for other people's posts (filtered by own group membership)

        $posts = json_decode(json_encode(get_filtered_posts_by_user(30, $_GET["id"], $_SESSION['myGroups'], $offset)), true);

      }

    } catch (Exception $e) {

      echo $e->getMessage();
      die();

    }

    if ($_SESSION['uid'] == $_GET["id"]) {

      $title_profile_type = "Private";
      $posts_display_explanation = "Only you can see your anonymous questions here. Content posted exclusively to teacher communities will only be seen by those community members.";

    } else {

      $title_profile_type = "Public";
      $posts_display_explanation = "These posts do not include comments, responses, anonymous questions, and exclusive posts to teacher communities you haven't joined.";

    }

    $title_status = "TC Posts View - " . ucfirst($user[0]['firstName']) . ' ' . ucfirst($user[0]['lastName']) . ' - ' . $title_profile_type;

    $last_count = 0;
    $feed_cards = "";
    $total_count = count($posts);

    if ($total_count < 30) {
      $last_page = 1;
    }

    if (!$last_page) {
      $url_prev = site_url() . "/posts.php?id=" . $_GET["id"] . "&page=" . $next_page;
    }

    if ($current_page != 1) {
      $url_next = site_url() . "/posts.php?id=" . $_GET["id"] . "&page=" . ($current_page - 1);
    }

    if ($user) {

      // Collate post cards for display
      if ($total_count > 0) {

        foreach ($posts as $post) {

          // Only show post by trusted members (or untrusted post to author)
          if ($user[0]['trusted'] == TRUE OR ($user[0]['trusted'] == FALSE AND $_GET["id"] == $_SESSION['uid'])) {

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

            // Reduce content for preview and clean up any tags
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

            // Create post view text for display
            if ($post['totalViews'] == 0) {
              $post_views = "0 Views";
            } elseif  ($post['totalViews'] == 1) {
              $post_views = "1 View";
            } else {
              $post_views = $post['totalViews'] . " Views";
            }

            // Render posts using feedcard template
            $feed_cards = $feed_cards . $templates->render('feed-card',
              [
                'post_id' => $post['_id']['$oid'],
              	'post_type' => $post['type'],
              	'post_anon' => $post['isAnonymous'],
              	'post_author' => $post['userId'],
                'post_author_fullname' => ucfirst($user[0]['firstName']) . ' ' . ucfirst($user[0]['lastName']),
                'post_author_trust' => $user[0]['trusted'],
                'post_author_avatar' => $user[0]['avatar'],
                'preview_content' => $preview_content_delinked,
              	// 'post_content' => $post_content_delinked,
                'youtube_video' => strip_tags($youtube_video),
                'post_views' => $post_views,
                'post_photos' => $post_photos,
                'post_photos_count' => $photo_num,
                // 'post_files' => $post_files,
                'post_files_count' => $file_num,
              	// 'post_photos' => $post['photos'],
                // 'post_photos_count' => count($post['photos']),
              	'post_comments' => $post['comments'],
                'post_time' => $post['time']['$date'],
                'post_audience' => $post['audience'],
              	'post_comment_count' => count($post['comments']),
              	'post_helpful_count' => count($post['reactions']['thumbsup']),
              	'post_metoo_count' => count($post['reactions']['highfive']),
                'my_posts_feed' => 1
              ]
            );

            if ($last_count == 0) {

              $start_date = timestamp($post['time']['$date'], 'j M Y');
              $start_date_raw = $post['time']['$date'];

            }

            if ( $last_count == ( $total_count - 1 ) ) {

              // $end_date = $total_count;
              $end_date = timestamp($post['time']['$date'], 'j M Y');
              $end_date_raw = $post['time']['$date'];

            }

            $last_count++;

          }

          // $feed_cards = $feed_cards . '</div>'; // End of .grid
          // $feed_cards = $feed_cards . '</div>'; // End of #feed-title
          // $feed_cards = $feed_cards . '</div>'; // End of #feed-title-wrapper
          // $feed_cards = $feed_cards . '</div>'; // End of #feed-view

        }

      } else {

        // Display no posts found messaging
        $feed_cards = $feed_cards . $templates->render('error',
          [
            'page' => 'posts',
            'fullName' => ucfirst($user[0]['firstName']) . ' ' . ucfirst($user[0]['lastName']),
            'firstName' => $user[0]['firstName'],
            'lastName' => $user[0]['lastName']
          ]
        );

      }

      // Create page header
      $page_header = $templates->render('layout-header',
        [
          'page' => 'posts',
          'user_id' => $_GET["id"],
          'title' => $title_status,
          'fullName' => ucfirst($user[0]['firstName']) . ' ' . ucfirst($user[0]['lastName']),
          'user_avatar' => $user[0]['avatar'],
          'posts_display_explanation' => $posts_display_explanation,
          'current_page' => $current_page,
          'next_page' => $next_page,
          'url_next' => $url_next,
          'url_prev' => $url_prev,
          'start_date_raw' => $start_date_raw,
          'end_date_raw' => $end_date_raw,
          'end_date' => $end_date,
          'start_date' => $start_date,
          'total_count' => $total_count
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'posts',
          'title' => $title_status,
          'user_id' => $_GET["id"],
          'current_page' => $current_page,
          'next_page' => $next_page,
          'url_next' => $url_next,
          'url_prev' => $url_prev,
          'last_page' => $last_page,
          'total_count' => $total_count,
          'start_date_raw' => $start_date_raw,
          'end_date_raw' => $end_date_raw,
          'end_date' => $end_date,
          'start_date' => $start_date,
          'total_count' => $total_count
        ]);


        // Display page header
        echo $page_header;

        // Display post cards
        echo $feed_cards;

        // Display page footer
        echo $page_footer;


    } else {  // No user found



      // Create page header
      $page_header = $templates->render('layout-header',
        [
          'page' => 'error'
        ]
      );


      // Create page body
      $page_body = $templates->render('error',
        [
          'page' => 'user'
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'error'
        ]
      );

      // Display page header
      echo $page_header;

      // Display page body
      echo $page_body;

      // Display page footer
      echo $page_footer;

    }



  } else {

    session_destroy();
    redirect('/maintenance.php');
    die();

  }

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}
