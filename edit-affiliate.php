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

    $affiliates_available = [];
    $affiliate_keys = [];

    $affiliates = json_decode(json_encode(get_groups_ordered()), true);

    // $affiliates = json_decode(json_encode(get_groups()), true);
    $affiliates_subscribed = json_decode(json_encode(get_groups_by_user($_SESSION['uid'])), true);
    $affiliates_pending = json_decode(json_encode(get_knocks_by_user($_SESSION['uid'])), true);

    $subscribed_affiliates = [];
    foreach ($affiliates_subscribed as $affiliate_subscribed) {
      $subscribed_affiliates[] = $affiliate_subscribed['_id']['$oid'];
    }

    $pending_affiliates = [];
    foreach ($affiliates_pending as $affiliate_pending) {
      $pending_affiliates[] = $affiliate_pending['_id']['$oid'];
    }

    $affiliates_followed = [];
    $affiliates_private = [];
    foreach ($affiliates as $affiliate) {
        if (in_array($affiliate['_id']['$oid'], $subscribed_affiliates) ) {

          $affiliates_followed[] = array('name' => $affiliate['name'], 'description' => $affiliate['description'], 'logo' => $affiliate['logo'], 'image' => $affiliate['tile'], 'num_users' => count($affiliate['users']), 'id' => $affiliate['_id']['$oid'], 'privacy' => $affiliate['privacy']);

        } elseif (in_array($affiliate['_id']['$oid'], $pending_affiliates) ) {

          $affiliates_private[] = array('name' => $affiliate['name'], 'description' => $affiliate['description'], 'logo' => $affiliate['logo'], 'image' => $affiliate['tile'], 'num_users' => count($affiliate['users']), 'id' => $affiliate['_id']['$oid'], 'privacy' => $affiliate['privacy'], 'knocked' => 1);

        } elseif ($affiliate['privacy'] == 'private') {

          $affiliates_private[] = array('name' => $affiliate['name'], 'description' => $affiliate['description'], 'logo' => $affiliate['logo'], 'num_users' => count($affiliate['users']), 'id' => $affiliate['_id']['$oid'], 'privacy' => $affiliate['privacy']);

        } else {

          $affiliates_available[] = array('name' => $affiliate['name'], 'description' => $affiliate['description'], 'logo' => $affiliate['logo'], 'image' => $affiliate['tile'], 'num_users' => count($affiliate['users']), 'id' => $affiliate['_id']['$oid'], 'privacy' => $affiliate['privacy']);

        }
    }


    // echo "<pre>";
    // print_r($affiliates_subscribed);
    // echo "</pre>";
    // echo "<pre>";
    // print_r($affiliates);
    // echo "</pre>";

    $title_status = "TC Profile Edit  - " . $user[0]['firstName'] . ' ' . $user[0]['lastName'] . ' - Teacher Communities';

    if ($_GET['id']) { // Process form data if supplied

      // Find group id and users by name
      $user_group = json_decode(json_encode(get_group($_GET['id'])), true);

      if ($user_group) { // If group exists, add user to it

        $gid = $user_group[0]['_id']['$oid'];
        $user_array = $user_group[0]['users'];
        $group_name = $user_group[0]['name'];
        $group_tile = $user_group[0]['tile'];
        $group_logo = $user_group[0]['logo'];
        $group_privacy = $user_group[0]['privacy'];
        $group_users_knocking = $user_group[0]['users_knocking'];
        $group_users_approved = $user_group[0]['users_approved'];
        $knock_request = $_GET['knock'];
        $title_community = $group_name . ' Community';

        if ($user_array == NULL) {
          $user_array = [];
        }
        if ($group_users_knocking == NULL) {
          $group_users_knocking = [];
        }
        if ($group_users_approved == NULL) {
          $group_users_approved = [];
        }

        if ( in_array($_SESSION['uid'], $user_array) OR in_array( $_SESSION['uid'], $group_users_knocking ) ) { // If user already in group or knocking list, do nothing

          redirect('/edit-affiliate.php');
          die();

        } elseif ( in_array($_SESSION['email'], $group_users_approved) ) { // If user is already approved, join private group

          // Add user to private group
          $user_group_add = add_group_user($gid, $_SESSION['uid']);

          // Update groups session variables
          update_groups_session();

          // Create page body
          $page_body = $templates->render('private-community-accepted',
            [
              'user_id' => $_SESSION['uid'],
              'title' => $title_community,
              'group_id' => $gid,
              'group_logo' => $group_logo
            ]
          );

        } elseif ($group_privacy == 'private') { // If group is private, add to knocking list

          if ($knock_request == 1) {

            // Add user to knocking list of private group
            $user_knocking_add = add_knocking_user($gid, $_SESSION['uid']);

            // Create page body
            $page_body = $templates->render('private-community-knocked',
              [
                'user_id' => $_SESSION['uid'],
                'title' => $title_community,
                'group_id' => $gid,
                'group_logo' => $group_logo
              ]
            );

          } else {

            // Display privacy explanation

            // Create page body
            $page_body = $templates->render('private-community-explanation',
              [
                'user_id' => $_SESSION['uid'],
                'title' => $title_community,
                'group_id' => $gid,
                'group_logo' => $group_logo
              ]
            );

          }

        } else { // If user not in group, add them

          // Add user to group
          $user_group_add = add_group_user($gid, $_SESSION['uid']);

          $groups = json_decode(json_encode(get_groups_by_user($_SESSION['uid'])), true);

          // Build list of affiliated partners
          $_SESSION['partners'] = [];
          $_SESSION['myGroups'] = [];
          foreach ($groups as $group) {
              $_SESSION['partners'][] = array('id' => $group['_id']['$oid'], 'name' => $group['name'], 'image' => $group['tile']);
              $_SESSION['myGroups'][] = $group['_id']['$oid'];
          }

          $activity_data[] = $_GET['id'];
          new_activity_log($_SESSION['uid'], 'added community', $activity_data);

          if ($_GET['main_menu_referer']) {

            redirect('/feed.php?alert=success&id=' . $_GET['id']);

          } else {

            redirect('/profile.php?id=' . $_SESSION['uid'] . '&alert=success&comid=' . $_GET['id']);

          }

          die();

        }

      }

    } elseif (!$_POST) { // Display edit affiliate form if no data submitted

      // // Create page header
      // $page_header = $templates->render('layout-header',
      //   [
      //     'page' => 'profile',
      //     'title' => $title_status,
      //     'action' => 'edit-affiliate'
      //   ]Communities You Follow
      // );

      // Display user profile
      if ($user) {

        // Create page body
        $page_body = $templates->render('edit-affiliate',
          [
            'user_id' => $_SESSION['uid'],
            'affiliates_available' => $affiliates_available,
            'main_menu_referer' => $_GET['menu'],
            'affiliates_followed' => $affiliates_followed,
            'affiliates_private' => $affiliates_private
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

      // // Create page footer
      // $page_footer = $templates->render('layout-footer',
      //   [
      //     'page' => 'profile',
      //     'title' => $title_status,
      //     'action' => 'edit-affiliate'
      //   ]
      // );
      //
      // // Display page header
      // echo $page_header;
      //
      // // Display page body
      // echo $page_body;
      //
      // // Display page footer
      // echo $page_footer;
      //
      // die();

    }

  } else {

    session_destroy();
    redirect('/maintenance.php');
    die();

  }

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));
  die();

}

if ($page_body) {

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
      'action' => 'edit-affiliate'
    ]
  );

  // Display page header
  echo $page_header;

  // Display page body
  echo $page_body;

  // Display page footer
  echo $page_footer;

  die();

}
