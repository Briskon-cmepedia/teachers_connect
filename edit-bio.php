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

    $title_status = "TC Profile Edit  - " . $user[0]['firstName'] . ' ' . $user[0]['lastName'] . ' - Bio';

    if (!$_POST) { // Display edit affiliate form if no data submitted

      // Display user profile
      if ($user) {

        // Create page body
        $page_body = $templates->render('edit-bio',
          [
            'user_bio' => $user[0]['bio']
          ]
        );

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
          'title' => $title_status,
          'action' => 'edit-affiliate'
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'profile',
          'title' => $title_status,
          'action' => 'edit-bio'
        ]
      );

      // Display page header
      echo $page_header;

      // Display page body
      echo $page_body;

      // Display page footer
      echo $page_footer;



    } else { // Process form data if supplied

        $bio_update = update_user_bio($_POST['bioText']);

        if ($bio_update == false){

          echo 'Something went wrong';

        } else {

          redirect('/profile.php?id=' . $_SESSION['uid'] . '&alert=success');

        }

    }

  } else {

    session_destroy();
    redirect('/maintenance.php');
    die();

  }

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}
