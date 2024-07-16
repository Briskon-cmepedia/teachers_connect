<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

error_reporting(E_ALL | E_STRICT);
error_reporting( error_reporting() & ~E_NOTICE );
ini_set("display_errors", 2);

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

  if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

    if ($_POST['group_id']) { // if group ID supplied, display only members by that group

      // Get group data
      try {
        $group = json_decode(json_encode(get_group($_POST['group_id'])), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      $partner_name = $group[0]['name'];
      $group_admins = $group[0]['admins'];
      $group_members = $group[0]['users'];
      $partner_logo = $group[0]['logo'];
      $partner_description = $group[0]['description'];
      $feed_number = $group[0]['_id']['$oid'];
      $group_privacy = $group[0]['privacy'];
      $feed_title = $partner_name . " Community Members";

      if (!in_array($_SESSION['uid'], $group_admins)) { // Restrict access to community admins

        redirect('/feed.php?id=' . $feed_number);
        die();

      }

      if ($_POST['group_action'] == 'approve') { // Approve member to group

        if ($_POST['id'] != NULL) {

          foreach ($_POST['id'] as $id) {
            // Remove member from knocking and add to group
            $add_to_group = json_decode(json_encode(add_group_user($_POST['group_id'], $id)), true);
            unset($uid);
            $uid[] = $id;
            $remove_knocking_user = json_decode(json_encode(remove_knocking_user($_POST['group_id'], $uid)), true);
            // Create new notification
            $notification_new = new_notification($_POST['group_name'], $_POST['group_id'], '', $_POST['group_tile'], 'membership approved', '', $id, $_POST['group_id'], '', '', array($id));
          }

        }

        if ($add_to_group == TRUE) {
          $member_approve = success;
        } else {
          $member_approve = failure;
        }

        redirect('/admin-community-members.php?id=' . $feed_number . '&member_approve=' . $member_approve);
        die();

      } elseif($_POST['confirm'] == 1) { // Remove from group if action confirmed

        if ($_POST['id'] != NULL) {

          foreach ($_POST['id'] as $id) {
            unset($uid);
            $uid[] = $id;
            $remove_group = json_decode(json_encode(remove_group_user($_POST['group_id'], $uid)), true);
          }

        }

        if ($remove_group == TRUE) {
          $member_remove = success;
        } else {
          $member_remove = failure;
        }

        redirect('/admin-community-members.php?id=' . $feed_number . '&member_remove=' . $member_remove);
        die();

      } elseif($_POST) { // No removal confirmation - Find user and confirm

          if ($_POST['id'] != NULL) {

            foreach ($_POST['id'] as $id) {
              $member_ids[] = new MongoDB\BSON\ObjectID($id);
            }

            // Connect to database
            try {

              $users = json_decode(json_encode(get_users_by_id($member_ids)), true);

            } catch (Exception $e) {

              echo $e->getMessage();
              die();

            }

            // Define error messages if applicable
            $error = "";
            if (!$users) {
              $error = "User not found. ";
            }
            if (!$_POST['id']) {
              $error = $error . "No information available.";
            }

            // Create page body
            $page_body = $templates->render('admin-process-members',
              [
                'members' => $users,
                'location' => $location,
                'error' => $error,
                'group_name' => $partner_name,
                'group_id' => $feed_number
              ]
            );

          }

        }

    } else { // If no group ID supplied, get outta here!

      redirect('/home.php');
      die();

    }

    // Create page header
    $page_header = $templates->render('layout-header',
      [
        'page' => 'members',
        'title' => $feed_title,
        'partner_name' => $partner_name,
        'partner_logo' => $partner_logo,
        'partner_description' => $partner_description,
        'group_privacy' => $group_privacy,
        'feed_number' => $feed_number,
        'community_followed' => $community_followed,
        'community_admin' => 1
      ]
    );

    // Create page footer
    $page_footer = $templates->render('layout-footer',
      [
        'page' => 'members',
        'title' => $feed_title,
        'feed_number' => $feed_number,
        'community_admin' => 1
      ]
    );

    // Display page header
    echo $page_header;

    // Display page body
    echo $page_body;

    // Display page footer
    echo $page_footer;


  } else { // Maintenance mode active - redirect to notice

    session_destroy();
    redirect('/maintenance.php');
    die();

  }

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}
