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

      $title_status = "Conversations";

      if ($_GET['id']) {

        $conversation = json_decode(json_encode(get_conversation_by_id($_GET['id'])), true);

        if ($conversation) {

          $participants = [];
          $participantsBson = [];

          foreach ($conversation[0]['participants'] as $participant) {

            // Create list of participants
            $user_id = new MongoDB\BSON\ObjectID($participant);
            array_push($participantsBson, $user_id);
            array_push($participants, $participant);

          }

          $participantInfo = json_decode(json_encode(get_authors($participantsBson)), true);

          $messages = $conversation[0]['messages'];

          // Remove untrusted members from participants list
          foreach ($participants as $key => $participant) {
            if ($participantInfo[$participant]['trusted'] == FALSE) {
              unset($participantInfo[$participant]);
              unset($participants[$key]);
            }
          }

          // Remove untrusted members messages from display
          foreach ($messages as $key => $message) {
            if ($participantInfo[$message['userId']]['trusted'] == FALSE) {
              unset($messages[$key]);
            }
          }

          // echo "Messages: ".count($messages);

          if (count($messages) > 20) {

            uasort($messages, function($a, $b) {
              return $b['time']['$date'] - $a['time']['$date'];
              // return $a['time']['$date'] - $b['time']['$date'];
            });

            $messages = array_slice($messages, 0, 20);

            uasort($messages, function($a, $b) {
              // return $b['time']['$date'] - $a['time']['$date'];
              return $a['time']['$date'] - $b['time']['$date'];
            });

          }

          // Create page body
          $page_body = $templates->render('messages-conversation',
             [
              'conversation' => $conversation,
              'messages' => $messages,
              'participantInfo' => $participantInfo,
              'participants' => $participants
             ]
          );

          update_conversation_timestamp($_GET['id']);

        } else {

          // Create page body
          $page_body = $templates->render('error',
            [
              'view' => 'conversation'
            ]
          );

        }

      } else {

        redirect('/messages-inbox.php');

        die();

      }


      // Create page header
      $page_header = $templates->render('layout-header',
        [
          'page' => 'messages',
          'title' => $title_status
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'messages',
          'title' => $title_status,
          'view' => 'conversation',
          'participants' => $participants
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
