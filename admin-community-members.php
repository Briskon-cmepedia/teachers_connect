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
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

  if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

    if ($_GET['id']) { // if group ID supplied, display only members by that group

      // Get group data
      try {
        $group = json_decode(json_encode(get_group($_GET['id'])), true);
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

      foreach ($group_members as $member) {
        $member_ids[] = new MongoDB\BSON\ObjectID($member);
      }

      try {
        $users = json_decode(json_encode(get_users_by_id($member_ids)), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      if($users) { $user_count = count($users); } else { $user_count = 0; }

      // Display member table
      $i = 0;
      $members = [];
      foreach ($users as $user) {
          $members[$i]['id'] = $user['_id']['$oid'];
          $members[$i]['firstName'] = $user['firstName'];
          $members[$i]['lastName'] = $user['lastName'];
          $members[$i]['email'] = $user['email'];
          $members[$i]['bio'] = $user['bio'];
          $members[$i]['avatar'] = $user['avatar'];
          $members[$i]['time'] = $user['time'];
          $i++;
      }

      // Create page body
      $page_body = $templates->render('admin-table-community-members',
        [
          'members' => $members,
          'group_name' => $partner_name,
          'group_id' => $feed_number
        ]
      );

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
        'community_admin' => 1,
        'member_remove' => $_GET['member_remove'],
        'group_privacy' => $group_privacy
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

    // Display post cards
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
