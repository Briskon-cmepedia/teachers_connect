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

    $title_status = "TC Profile Edit  - " . $user[0]['firstName'] . ' ' . $user[0]['lastName'] . ' - Personal Information';

    if (!$_POST) { // Display edit education form if no data submitted

      // Display user profile
      if ($user) {

        // Create page body
        $page_body = $templates->render('edit-information',
          [
            'user_id' => $_SESSION['uid'],
            'user_firstName' => $user[0]['firstName'],
            'user_lastName' => $user[0]['lastName'],
            'user_email' => $user[0]['email'],
            'user_avatar' => $user[0]['avatar']
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
          'action' => 'edit-information'
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'profile',
          'title' => $title_status,
          'action' => 'edit-information',
          'user_avatar' => $user[0]['avatar']
        ]
      );

      // Display page header
      echo $page_header;

      // Display page body
      echo $page_body;

      // Display page footer
      echo $page_footer;


    } elseif ($_POST['user_firstName'] AND $_POST['user_lastName'] AND filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) { // Process form data if supplied

        $user_update = update_user_information($_POST['user_firstName'], $_POST['user_lastName'], strtolower($_POST['user_email']), $_POST['password']);

        if ($user_update == false){

          echo 'Something went wrong';

        } else {

          redirect('/edit-profile.php?alert=success');

        }

    } else { // Display edit education form with any errors

      // Display user profile
      if ($user) {

        // Create page body
        $page_body = $templates->render('edit-information',
          [
            'formSubmission' => 'fail',
            'user_id' => $_SESSION['uid'],
            'user_firstName' => $_POST['user_firstName'],
            'user_lastName' => $_POST['user_lastName'],
            'user_email' => $_POST['user_email'],
            'user_avatar' => $user[0]['avatar']
          ]
        );

      } else { // No user found

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
          'action' => 'edit-information'
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'profile',
          'title' => $title_status,
          'action' => 'edit-information',
          'user_avatar' => $user[0]['avatar']
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