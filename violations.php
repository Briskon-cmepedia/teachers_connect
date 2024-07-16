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
      $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    $title_status = "TC Account Violations - " . $user[0]['firstName'] . ' ' . $user[0]['lastName'];

    // Display account violations
    if ($user) {

      if ($user[0]['violations']) {

        $violations = [];
        foreach ($user[0]['violations'] as $violation) {
          if ($violation['status'] != 'resolved') {
            $violations[] = $violation['type'];
          }
        }

        if ($violations) {

          // Create page body
          $page_body = $templates->render('violations',
            [
              'user_id' => $_SESSION['uid'],
              'user_firstName' => $user[0]['firstName'],
              'user_lastName' => $user[0]['lastName'],
              'user_email' => $user[0]['email'],
              'user_avatar' => $user[0]['avatar'],
              'violations' => $violations
            ]
          );

        } else {

          // Create page body
          $page_body = $templates->render('error',
            [
              'page' => 'violations'
            ]
          );

        }

      } else {

        // Create page body
        $page_body = $templates->render('error',
          [
            'page' => 'violations'
          ]
        );

      }

    } else {  // No user found

      // Create page body
      $page_body = $templates->render('error',
        [
          'page' => 'user'
        ]
      );

    }

    // Create page header
    $page_header = $templates->render('layout-header',
      [
        'page' => 'profile',
        'title' => $title_status
      ]
    );

    // Create page footer
    $page_footer = $templates->render('layout-footer',
      [
        'page' => 'profile',
        'title' => $title_status,
        'action' => 'edit'
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
