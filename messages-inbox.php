<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
  $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

  if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

    // Connect to database
    try {
      $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    if ($user[0]['trusted'] == TRUE) {

      if (intval($_GET['page'])) {
        $current_page = $_GET['page'];
      } else {
        $current_page = 1;
      }
      if ($current_page >= 2) {
        $prev_page = $current_page - 1;
      }
      $next_page = $current_page + 1;
      $offset = ($current_page - 1)*10;

      // Connect to database
      try {
        $conversations = json_decode(json_encode(get_conversations_by_user(10, $offset)), true);

        $total_count = count($conversations);

        if ($total_count < 10) {
          $last_page = 1;
        }

        // Define previous and next links for pagination
        if (!$last_page) {
          $url_prev = site_url() . "/messages-inbox.php?page=" . $next_page;
        }

        if ($current_page != 1) {
          $url_next = site_url() . "/messages-inbox.php?page=" . ($current_page - 1);
        }

        if($conversations) {

          $config = HTMLPurifier_Config::createDefault();
          $purifier = new HTMLPurifier($config);

          $participants = [];
          $participantsBson = [];

          foreach ($conversations as $convKey => &$conversation) {
            foreach ($conversation['participants'] as $participant) {

              // Create list of participants
              $user_id = new MongoDB\BSON\ObjectID($participant);
              array_push($participantsBson, $user_id);
              array_push($participants, $participant);

              // Create list of participants excluding current user
              if ($participant !== $_SESSION['uid']) {
                $conversation['otherParticipants'][] = $participant;
              }

            }

            $participantInfo = json_decode(json_encode(get_authors($participantsBson)), true);

            // Remove untrusted members from participants list
            foreach ($participants as $key => $participant) {
              if ($participantInfo[$participant]['trusted'] == FALSE) {
                $infoKey = array_search($participant, $participantInfo);
                unset($conversation['otherParticipants'][$infoKey]);
                unset($participantInfo[$participant]);
                unset($participants[$key]);
              }
            }

            // Create short list of participants (inbox)
            if ($conversation['otherParticipants']) {
              $participantShortList = [];
              foreach (array_slice($conversation['otherParticipants'], 0, 4) as $otherParticipants) {
                $participantShortList[] = $participantInfo[$otherParticipants]['firstName'] . " " . $participantInfo[$otherParticipants]['lastName'];
              }
              $conversation['firstParticipants'] = implode(', ', $participantShortList);
              if (count($conversation['otherParticipants']) > 4) {
                $conversation['firstParticipants'] = $conversation['firstParticipants'] . "...";
              }
            }

            // Reduce content for preview and clean up any tags
            $lastArray = end($conversation['messages']);
            $preview_content = substr($lastArray['text'], 0, 335);
            if (strlen($lastArray['text']) > 335) { $preview_content = $preview_content . '...'; }
            $preview_content = $purifier->purify($preview_content);

            // Remove any existing anchor links while retaining surrounded text


            // $conversation['firstMessage'] = strip_tags($preview_content);

            if (strlen($preview_content) > 305) { // Strip long messages down to 335 characters

              $conversation['firstMessage'] = substr(strip_tags(str_replace('<', ' <', $preview_content)), 0, 305) . '...';

            } else {

              $conversation['firstMessage'] = strip_tags(str_replace('<', ' <', $preview_content));

            }



            $conversation['firstTime'] = timestamp($lastArray['time']['$date'], 'j M g:iA');

            $conversation['rawTime'] = $lastArray['time']['$date'];

            if ($participantInfo[$conversation['owner']]['trusted'] == FALSE) {
              unset($conversations[$convKey]);
            }

          }

          $page_body = $templates->render('messages-inbox',
             [
              'conversations' => $conversations,
              'participantInfo' => $participantInfo,
              'participantShortList' => $participantShortList
             ]
           );

        } else {

          // No messages found
          $page_body = $templates->render('error',
            [
              'page' => 'messages'
            ]
          );

          echo "<style>
          #messages-inbox {
        		max-width: 1200px;
        		margin: 0 auto;
        		margin-top: 90px;
        		padding-bottom: 0px;
        	}
          .page-title.no-head {
          	text-align: center;
          	display: inline-block;
          	margin-left: 40px !important;
          }
          .page-title h1 {
          	display: inline-block;
          	color: #224F59 !important;
          }
          #button-new-message {
          	float: right;
          	margin: 20px 20px 0px 0px;
          	padding: 10px 15px;
          }
          .icon-large {
          	display: inline-block;
          	margin-right: 10px;
          	vertical-align: middle;
          	height: 35px !important;
          	margin-bottom: 10px !important;
          }
          </style>";

        }



      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      $title_status = "Conversations";



      // Create page header
      $page_header = $templates->render('layout-header',
        [
          'page' => 'messages',
          'view' => 'inbox',
          'title' => $title_status,
          'alert' => $_GET['alert'],
          'current_page' => $current_page,
          'prev_page' => $prev_page,
          'next_page' => $next_page,
          'url_next' => $url_next,
          'url_prev' => $url_prev,
          'last_page' => $last_page,
          'total_count' => $total_count
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'messages',
          'view' => 'inbox',
          'title' => $title_status,
          'current_page' => $current_page,
          'prev_page' => $prev_page,
          'next_page' => $next_page,
          'url_next' => $url_next,
          'url_prev' => $url_prev,
          'last_page' => $last_page,
          'total_count' => $total_count
        ]
      );

      // Display page header
      echo $page_header;

      // Display page body
      echo $page_body;

      // Display page footer
      echo $page_footer;

      die();

    } else {

      redirect('/home.php');

    }


  } else {

    session_destroy();
    redirect('/maintenance.php');
    die();

  }

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}
