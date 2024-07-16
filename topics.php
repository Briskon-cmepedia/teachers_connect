<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

  if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

    // Connect to database
    try {

      $topics = json_decode(json_encode(get_all_topics()), true);

    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    // Get topics followed by user
    $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);
    if (is_array($user[0]['topicsFollowed'])) {
      $topics_followed = $user[0]['topicsFollowed'];
    } else {
      $topics_followed = [];
    }

    $title_status = "TeachersConnect Topics";

    // Create page body
    $page_body = $templates->render('list-topics',
      [
        'page' => 'topics',
        'topics' => $topics,
        'topics_followed' => $topics_followed
      ]
    );

    // Create page header
    $page_header = $templates->render('layout-header',
      [
        'page' => 'topics',
        'title' => $title_status
      ]
    );

    // Create page footer
    $page_footer = $templates->render('layout-footer',
      [
        'page' => 'topics',
        'title' => $title_status
      ]
    );

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

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}
