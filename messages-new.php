<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Connect to database
  try {
    $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);
  } catch (Exception $e) {
    echo $e->getMessage();
    die();
  }

  if ($user[0]['trusted'] == TRUE) {

    // Variable Setup
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    $sanitized_text = $purifier->purify($_POST['text']);
    $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
    $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
    $uid = $_GET['id'];

    if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

      if ($_POST['conversationParticipants']) {

        $participants = explode( ',', $_POST['conversationParticipants'] );
        $participants[] = $_SESSION['uid'];

        $conversation_new = json_decode(json_encode(new_conversation($participants, $sanitized_text)), true);

        if ($conversation_new == false){

          echo 'Something went wrong - conversation creation failed!';

          die();

        } else { // Redirect after successful conversation creation

          $activity_data[] = $conversation_new;
          $activity_data[] = $_POST['conversationParticipants'];
          $activity_data[] = $sanitized_text;
          new_activity_log($_SESSION['uid'], 'created conversation', $activity_data);

          redirect('/messages-conversation.php?id=' . $conversation_new);

          die();

        }

      } elseif ($uid) {

        $user = json_decode(json_encode(get_author($uid)), true);

      } else {

        $user = [];

      }

        // Create page body
        $page_body = $templates->render('messages-new',
          [
            'uid' => $user[0]['_id']['$oid'],
            'fullName' => $user[0]['firstName'].' '.$user[0]['lastName'],
            'avatar' => $user[0]['avatar']
          ]
        );

        // Create page header
        $page_header = $templates->render('layout-header',
          [
            'page' => 'messages-new'
          ]
        );

        // Create page footer
        $page_footer = $templates->render('layout-footer',
          [
            'page' => 'messages-new',
            'uid' => $user[0]['_id']['$oid']
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

      session_destroy();
      redirect('/maintenance.php');
      die();

    }

} else {

  redirect('/home.php');

}

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}
