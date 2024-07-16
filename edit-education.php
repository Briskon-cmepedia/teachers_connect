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

    $title_status = "TC Profile Edit  - " . $user[0]['firstName'] . ' ' . $user[0]['lastName'] . ' - Education';

    if (!$_POST) { // Display edit education form if no data submitted

      // Display user profile
      if ($user) {

        if ($_GET['id']) {
          foreach ($user[0]['educations'] as $education) {
            if ($education['_id']['$oid'] == $_GET['id']) {
              $teachLicenseLocation = $education['institude'];
              $teachLicenseComplete = $education['yearCompleted'];
            }
          }
        }

        // Create page body
        $page_body = $templates->render('edit-education',
          [
            'user_id' => $_SESSION['uid'],
            'user_firstName' => $user[0]['firstName'],
            'user_lastName' => $user[0]['lastName'],
            'user_email' => $user[0]['email'],
            'user_avatar' => $user[0]['avatar'],
            'user_interests' => $user[0]['interests'],
            'user_experience' => $user[0]['experience'],
            'user_educations' => $user[0]['educations'],
            'education_id' => $_GET['id'],
            'teachLicenseLocation' => $teachLicenseLocation,
            'teachLicenseComplete' => $teachLicenseComplete
          ]
        );

      } else {

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
          'action' => 'edit-education'
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'profile',
          'title' => $title_status,
          'action' => 'edit-education'
        ]
      );

      // Display page header
      echo $page_header;

      // Display page body
      echo $page_body;

      // Display page footer
      echo $page_footer;


    } elseif ($_POST['teachLicenseLocation'] AND $_POST['teachLicenseComplete']) { // Process form data if supplied

      if ($_POST['educationId']) { // Update existing record if ID supplied

        $education_update = update_user_education($_POST['educationId'], $_POST['teachLicenseLocation'], $_POST['teachLicenseComplete']);

        if ($education_update == false){

          echo 'Something went wrong';

        } else {

          redirect('/profile.php?id=' . $_SESSION['uid'] . '&alert=success');

        }

      } else { // Create new record if no ID supplied

        // Find group id and users by name
        $user_group = json_decode(json_encode(find_group($_POST['teachLicenseLocation'])), true);
        $groups = json_decode(json_encode(get_groups_by_user($_SESSION['uid'])), true);

        if ($user_group) { // If group exists, add user to it

          $user_exists = 0;
          if ($groups) {
            foreach ($groups as $group) {
              if ($group[name] == $user_group[0]['name']) {
                $user_exists = 1;
              }
            }
          }

          if ($user_exists == 0) {

            $gid = $user_group[0]['_id']['$oid'];
            $user_array = $user_group[0]['users'];
            $group_tile = $user_group[0]['tile'];

            if ($user_array == NULL) {
              $user_array = [];
            }

            // Add user to group array
            $user_array[] = $_SESSION['uid'];

            // Add user to group
            $user_group_add = update_group($gid, $user_array);

            // Add group to tiles array
            $_SESSION['partners'][$gid] = $group_tile;

          }

        }

        $education_new = add_user_education($_SESSION['uid'], $_POST['teachLicenseLocation'], $_POST['teachLicenseComplete']);

        if ($education_new == false){

          echo 'Something went wrong';

        } else {

          redirect('/profile.php?id=' . $_SESSION['uid'] . '&alert=success');

        }

      }

    } else { // Display edit education form with any errors

      // Display user profile
      if ($user) {

        $teachLicenseLocation = $_POST['teachLicenseLocation'];
        $teachLicenseComplete = $_POST['teachLicenseComplete'];

        // Create page body
        $page_body = $templates->render('edit-education',
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
            'education_id' => $_GET['id'],
            'teachLicenseLocation' => $teachLicenseLocation,
            'teachLicenseComplete' => $teachLicenseComplete
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
          'action' => 'edit-education'
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'profile',
          'title' => $title_status,
          'action' => 'edit-education'
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
