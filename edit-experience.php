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

    $title_status = "TC Profile Edit  - " . $user[0]['firstName'] . ' ' . $user[0]['lastName'] . ' - Experience';

    if (!$_POST) { // Display edit education form if no data submitted

      // Display user profile
      if ($user) {

        // Create page body
        $page_body = $templates->render('edit-experience',
          [
            'user_id' => $_SESSION['uid'],
            'user_firstName' => $user[0]['firstName'],
            'user_lastName' => $user[0]['lastName'],
            'user_email' => $user[0]['email'],
            'user_avatar' => $user[0]['avatar'],
            'user_interests' => $user[0]['interests'],
            'user_experience' => $user[0]['experience'],
            'user_educations' => $user[0]['educations']
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
          'action' => 'edit-experience'
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'profile',
          'title' => $title_status,
          'action' => 'edit-experience'
        ]
      );

      // Display page header
      echo $page_header;

      // Display page body
      echo $page_body;

      // Display page footer
      echo $page_footer;


    } elseif ($_POST['teachLocationName'] AND $_POST['teachGrades'] AND $_POST['teachSubjects'] AND $_POST['teachStart'] AND $_POST['teachEnd']) { // Process form data if supplied

        $experience_new = add_user_experience($_SESSION['uid'], $_POST['teachLocationName'], $_POST['teachLocationCity'], $_POST['teachLocationState'], $_POST['teachGrades'], $_POST['teachSubjects'], $_POST['teachStart'], $_POST['teachEnd']);

        if ($experience_new == false){

          echo 'Something went wrong';

        } else {

          redirect('/profile.php?id=' . $_SESSION['uid'] . '&alert=success');

        }

    } else { // Display edit education form with any errors

      // Display user profile
      if ($user) {

        // Create page body
        $page_body = $templates->render('edit-experience',
          [
            'formSubmission' => 'fail',
            'user_id' => $_SESSION['uid'],
            'user_firstName' => $user[0]['firstName'],
            'user_lastName' => $user[0]['lastName'],
            'user_email' => $user[0]['email'],
            'user_avatar' => $user[0]['avatar'],
            'user_interests' => $user[0]['interests'],
            'user_experience' => $user[0]['experience'],
            'user_educations' => $user[0]['educations'],
            'teachLocationName' => $_POST['teachLocationName'],
            'teachLocationCity' => $_POST['teachLocationCity'],
            'teachLocationState' => $_POST['teachLocationState'],
            'teachLocationCountry' => $_POST['teachLocationCountry'],
            'teachGrades' => $_POST['teachGrades'],
            'teachSubjects' => $_POST['teachSubjects'],
            'teachStart' => $_POST['teachStart'],
            'teachEnd' => $_POST['teachEnd']
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
          'formSubmission' => 'fail',
          'title' => $title_status,
          'action' => 'edit-experience'
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'profile',
          'title' => $title_status,
          'action' => 'edit-experience'
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
