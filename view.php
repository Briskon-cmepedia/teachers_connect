<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

  if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

    // $search_term = $_GET['search'];
    $stopwords = ["a", "about", "above", "after", "again", "against", "all", "am", "an", "and", "any", "are", "as", "at", "be", "because", "been", "before", "being", "below", "between", "both", "but", "by", "could", "did", "do", "does", "doing", "down", "during", "each", "few", "for", "from", "further", "had", "has", "have", "having", "he", "he'd", "he'll", "he's", "her", "here", "here's", "hers", "herself", "him", "himself", "his", "how", "how's", "i", "i'd", "i'll", "i'm", "i've", "if", "in", "into", "is", "it", "it's", "its", "itself", "let's", "me", "more", "most", "my", "myself", "nor", "of", "on", "once", "only", "or", "other", "ought", "our", "ours", "ourselves", "out", "over", "own", "same", "she", "she'd", "she'll", "she's", "should", "so", "some", "such", "than", "that", "that's", "the", "their", "theirs", "them", "themselves", "then", "there", "there's", "these", "they", "they'd", "they'll", "they're", "they've", "this", "those", "through", "to", "too", "under", "until", "up", "very", "was", "we", "we'd", "we'll", "we're", "we've", "were", "what", "what's", "when", "when's", "where", "where's", "which", "while", "who", "who's", "whom", "why", "why's", "with", "would", "you", "you'd", "you'll", "you're", "you've", "your", "yours", "yourself", "yourselves"];
    $wordsFromSearchString = str_word_count($_GET['search'], true, '0..9_äöüÄÖÜ,/');
    $finalWords = array_diff($wordsFromSearchString, $stopwords);
    $search_term_cleaned = implode(" ", $finalWords);
    $search_term = html_entity_decode($_GET['search'], ENT_QUOTES);

    // header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    // header('Cache-Control: no-store, no-cache, must-revalidate');
    // header('Cache-Control: post-check=0, pre-check=0', FALSE);
    // header('Pragma: no-cache');

    // Connect to posts database
    try {
      $post = json_decode(json_encode(get_post($_GET['id'])), true);

      if ($post[0]['audience']) {

        $group = json_decode(json_encode(get_group($post[0]['audience'])), true);
        $group_privacy = $group[0]['privacy'];

        if ( $group_privacy == 'private' AND !in_array($post[0]['audience'], $_SESSION['myGroups']) ) { // Block access to private community posts if you are not a member

          // Create page body
          $page_body = $templates->render('private-community-blocked',
            []
          );

          // Create page header
          $page_header = $templates->render('layout-header',
            []
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

        }

      }

      if($post) { // Collate and pull authors from database

        $author_id = $post[0]['userId'];
        $post_comments = $post[0]['comments'];
        $post_comment_count = count($post_comments);
        $authors = [];
        $commenters = [];
        foreach ($post as $post_single) {
          $author_mid = new MongoDB\BSON\ObjectID($author_id);
          array_push($authors, $author_mid);
        }
        foreach ($post_comments as $post_comment) {
          $author_mid = new MongoDB\BSON\ObjectID($post_comment['userId']);
          array_push($authors, $author_mid);
        }
        foreach ($post_comments as $comment) {
          $commenter_id = new MongoDB\BSON\ObjectID($comment['userId']);
          array_push($commenters, $commenter_id);
        }
        $posts_authors = json_decode(json_encode(get_authors($authors)), true);
        $posts_commenters = json_decode(json_encode(get_authors($commenters)), true);

      }
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    // Only show post by trusted members (or untrusted post to author)
    if ($posts_authors[$post[0]['userId']]['trusted'] == TRUE OR ($posts_authors[$post[0]['userId']]['trusted'] == FALSE AND $post[0]['userId'] == $_SESSION['uid'])) {

      if ($post[0]['type'] == 'question') {
        $title_post_type = "Question";
      } else {
        $title_post_type = "Post";
      }

      if ($post[0]['isAnonymous'] == 1) {
        $title_status = "TC " . $title_post_type . " View - Anonymous";
      } else {
        $title_status = "TC " . $title_post_type . " View - " . ucfirst($posts_authors[$author_id]['firstName']) . ' ' . ucfirst($posts_authors[$author_id]['lastName']);
      }

      if (count($post) > 0) {

        $photos_display = [];
        $file_display = [];
        $file_attachments = $post[0]['photos'];
        // print_r($file_attachments);
        // exit();

        // Collate file attachments for display
        foreach ($file_attachments as $file_attachment) {
           
          if (is_array($file_attachment)) {

              if (in_array($file_attachment[2], $image_filetypes) ) {
                
                $photos_display[] = array($file_attachment[0], $file_attachment[1], 'image.php?id='.$file_attachment[0].'&height=400', 'image.php?id='.$file_attachment[0].'&height=900');
               
              }

              if (in_array($file_attachment[2], $pdf_filetypes) ) {
                $file_display[] = array($file_attachment[0], $file_attachment[1], 'pdf', $file_attachment[3]);
              }

              if (in_array($file_attachment[2], $word_filetypes) ) {
                $file_display[] = array($file_attachment[0], $file_attachment[1], 'doc', $file_attachment[3]);
              }

              if (in_array($file_attachment[2], $powerpoint_filetypes) ) {
                $file_display[] = array($file_attachment[0], $file_attachment[1], 'ppt', $file_attachment[3]);
              }

              if (in_array($file_attachment[2], $excel_filetypes) ) {
                $file_display[] = array($file_attachment[0], $file_attachment[1], 'xls', $file_attachment[3]);
              }

              if (in_array($file_attachment[2], $pages_filetypes) ) {
                $file_display[] = array($file_attachment[0], $file_attachment[1], 'pages', $file_attachment[3]);
              }

              if (in_array($file_attachment[2], $key_filetypes) ) {
                $file_display[] = array($file_attachment[0], $file_attachment[1], 'key', $file_attachment[3]);
              }

              if (in_array($file_attachment[2], $numbers_filetypes) ) {
                $file_display[] = array($file_attachment[0], $file_attachment[1], 'numbers', $file_attachment[3]);
              }

          } else {

            $file_ext = strtolower(pathinfo(parse_url($file_attachment)['path'], PATHINFO_EXTENSION));
            $photos_display[] = array($file_attachment, '', 'image.php?id='.$file_attachment.'&height=400', 'image.php?id='.$file_attachment.'&height=900');

          }

        }

        // Make sure post views is accurate for current page visit
        $post_views = $post[0]['totalViews'] + 1;

        // Count only comments authored by trusted members
        $post_trusted_comment_count = 0;
        foreach ($post_comments as $comment) {
          if ($posts_commenters[$comment['userId']]['trusted'] == TRUE ) {
            $post_trusted_comment_count++;
          }
        }

        // Remove any existing anchor links while retaining surrounded text
        $post_content_delinked = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $post[0]['text']);
        $post_content_retargeted = preg_replace("/<a(.*?)>/", "<a$1 target=\"_blank\">", $post[0]['text']);

        // // Highlight search terms
        // if ($search_term) {
        //   $post_content_retargeted = highlight($post_content_retargeted, urldecode($search_term));
        // }

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

        // Determine if any account violations currently active
        if ($posts_authors[$post[0]['userId']]['violations']) {
          foreach ($posts_authors[$post[0]['userId']]['violations'] as $violation) {
            if ($violation['status'] == "reported") {
              $violations = 1;
            }
          }
        }

        // Check if visitor is following post
        if (is_array($post[0]['following'])) {
          $post_following = $post[0]['following'];
        } else {
          $post_following[] = $post[0]['following'];
        }
        if (in_array($_SESSION['uid'], $post_following)) {
            $post_followed = 1;
        }

        $flag_content = "";
        // Create page body
        $page_body = $templates->render('view-card',
          [
            'post_id' => $post[0]['_id']['$oid'],
          	'post_type' => $post[0]['type'],
          	'post_anon' => $post[0]['isAnonymous'],
            'post_views' => $post_views,
          	'post_author' => $post[0]['userId'],
            'post_author_fullname' => ucfirst($posts_authors[$author_id]['firstName']) . ' ' . ucfirst($posts_authors[$author_id]['lastName']),
            'post_author_avatar' => $posts_authors[$author_id]['avatar'],
            'post_author_trust' => $posts_authors[$post[0]['userId']]['trusted'],
            'post_author_violations' => $violations,
          	'post_content' => $post_content_retargeted,
            'youtube_video' => strip_tags($youtube_video),
          	'post_photos' => $post[0]['photos'],
            'photos_count' => count($photos_display),
            'photos_display' => $photos_display,
            'file_count' => count($file_display),
            'file_display' => $file_display,
            'post_followed' => $post_followed,
            'post_time' => $post[0]['time']['$date'],
            'post_edit_time' => $post[0]['last-edit']['$date'],
            'post_audience' => $post[0]['audience'],
          	'post_comment_count' => count($post[0]['comments']),
            'post_trusted_comment_count' => $post_trusted_comment_count,
          	'post_helpful_count' => count($post[0]['reactions']['thumbsup']),
          	'post_metoo_count' => count($post[0]['reactions']['highfive']),
            'flag_content' =>$post[0]['questionableContent']['flagContent'],
            'userRole' => $_SESSION['userRole'],
            'flag_type'=>'flag_post'             
          ]
        );

        if ($post_comment_count > 0) {

          foreach ($post_comments as $post_comment) {

            $comment_author = $post_comment['userId'];
            $photos_display = [];
            $file_display = [];
            $flag_comment_content="";
            $file_attachments = $post_comment['photos'];

            // Only show comments by trusted members (or untrusted comment to author)
            if ($posts_authors[$comment_author]['trusted'] == TRUE OR ($posts_authors[$comment_author]['trusted'] == FALSE AND $comment_author == $_SESSION['uid'])) {

              // Remove any existing anchor links while retaining surrounded text
              $post_comment_delinked = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $post_comment['text']);
              $post_comment_retargeted = preg_replace("/<a(.*?)>/", "<a$1 target=\"_blank\">", $post_comment['text']);

              // // Highlight search terms
              // if ($search_term) {
              //   $post_comment_retargeted = highlight($post_comment_retargeted, urldecode($search_term));
              // }

              // Collate file attachments for display
              foreach ($file_attachments as $file_attachment) {

                if (is_array($file_attachment)) {

                    if (in_array($file_attachment[2], $image_filetypes) ) {
                      $photos_display[] = array($file_attachment[0], $file_attachment[1], 'image.php?id='.$file_attachment[0].'&height=400', 'image.php?id='.$file_attachment[0].'&height=900');
                    }

                    if (in_array($file_attachment[2], $pdf_filetypes) ) {
                      $file_display[] = array($file_attachment[0], $file_attachment[1], 'pdf', $file_attachment[3]);
                    }

                    if (in_array($file_attachment[2], $word_filetypes) ) {
                      $file_display[] = array($file_attachment[0], $file_attachment[1], 'doc', $file_attachment[3]);
                    }

                    if (in_array($file_attachment[2], $powerpoint_filetypes) ) {
                      $file_display[] = array($file_attachment[0], $file_attachment[1], 'ppt', $file_attachment[3]);
                    }

                    if (in_array($file_attachment[2], $excel_filetypes) ) {
                      $file_display[] = array($file_attachment[0], $file_attachment[1], 'xls', $file_attachment[3]);
                    }

                    if (in_array($file_attachment[2], $pages_filetypes) ) {
                      $file_display[] = array($file_attachment[0], $file_attachment[1], 'pages', $file_attachment[3]);
                    }

                    if (in_array($file_attachment[2], $key_filetypes) ) {
                      $file_display[] = array($file_attachment[0], $file_attachment[1], 'key', $file_attachment[3]);
                    }

                    if (in_array($file_attachment[2], $numbers_filetypes) ) {
                      $file_display[] = array($file_attachment[0], $file_attachment[1], 'numbers', $file_attachment[3]);
                    }

                } else {

                  $file_ext = strtolower(pathinfo(parse_url($file_attachment)['path'], PATHINFO_EXTENSION));
                  $photos_display[] = array($file_attachment, '', 'image.php?id='.$file_attachment.'&height=400', 'image.php?id='.$file_attachment.'&height=900');

                }

              }

              if(!(Config::FLAG_QUESTIONABLE_ENABLE == 1 && ($_SESSION['userRole']=='user'||$_SESSION['userRole']=='') && ($post[0]['questionableContent']['flagContent'] == "reported" || $post[0]['questionableContent']['flagContent'] == "blocked")))
              {        
                  $page_body = $page_body . $templates->render('view-comment',
                    [
                      'post_id' => $post_comment['_id']['$oid'],
                      'post_author_original' => $post[0]['userId'],
                      'post_anon_original' => $post[0]['isAnonymous'],
                      'post_author' => $post_comment['userId'],
                      'post_author_fullname' => ucfirst($posts_authors[$comment_author]['firstName']) . ' ' . ucfirst($posts_authors[$comment_author]['lastName']),
                      'post_author_avatar' => $posts_authors[$comment_author]['avatar'],
                      'post_author_trust' => $posts_authors[$comment_author]['trusted'],
                      'post_author_violations' => $violations,
                      'post_comment' => $post_comment_retargeted,
                      'post_photos' => $post_comment['photos'],
                      // 'post_photos_count' => count($post_comment['photos']),

                      'photos_count' => count($photos_display),
                      'photos_display' => $photos_display,
                      'file_count' => count($file_display),
                      'file_display' => $file_display,

                      'post_time' => $post_comment['time']['$date'],
                      'post_edit_time' => $post_comment['last-edit']['$date'],
                      'post_helpful_count' => count($post_comment['reactions']['thumbsup']),
                      'flag_comment_content'=> $post_comment['questionableContent']['flagContent'],
                      'userRole' => $_SESSION['userRole'],
                      'main_post_id' => $post[0]['_id']['$oid'],  
                      'flag_type'=>'flag_comment'
                    ]
                  );
              }
            }

          }
        }

        if(!(Config::FLAG_QUESTIONABLE_ENABLE == 1 && ($_SESSION['userRole']=='user'||$_SESSION['userRole']=='') && ($post[0]['questionableContent']['flagContent'] == "reported" || $post[0]['questionableContent']['flagContent'] == "blocked")))
        { 
          $page_body = $page_body . $templates->render('post-comment',
            [
              'page' => 'view',
              'post_type' => $post[0]['type'],
              'post_id' => $post[0]['_id']['$oid'],
              'post_author_original' => $post[0]['userId'],
              'post_anon_original' => $post[0]['isAnonymous'],
              'post_author' => $post_comment['userId']
            ]
          ); 

        }
        else{
          $page_body = $page_body . $templates->render('post-comment',
            [
              'page' => 'view',
              'alert_message' => 'flag-post'
            ]
          ); 

        }

      } else {

        // Create page body
        $page_body = $templates->render('error',
          [
            'page' => 'post'
          ]
        );

      }

    } else {

      // Create page body
      $page_body = $templates->render('error',
        [
          'page' => 'post'
        ]
      );

    }

      // Create page header
      $page_header = $templates->render('layout-header',
        [
          'page' => 'view',
          'title' => $title_status,
          'alert' => $_GET['alert']
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'view',
          'title' => $title_status,
          'post_id' => $post[0]['_id']['$oid'],
          'search' => $search_term_cleaned
        ]
      );

      update_view_count($post[0]['_id']['$oid']);
      update_member_view_count();

      // Display page header
      echo $page_header;

      // Display page body
      echo $page_body;

      // Display page footer
      echo $page_footer;


  } else {

    session_destroy();
    redirect('/maintenance.php');
    die();

  }

} else { // Redirect user to login page if no valid session
  $HTTPS = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
  $REQUEST_URI = $_SERVER['REQUEST_URI'];
  $url = $HTTPS . "://$_SERVER[HTTP_HOST]$REQUEST_URI";
  $path = parse_url($url, PHP_URL_PATH);

  // print_r($path);
  // exit();
  echo $path.' '.$_GET['id'];
  if ($path && $_GET['id']) {
    save_request_uri($REQUEST_URI); // store request uri
  }
  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}
?>
