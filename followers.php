<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

  if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

    if (intval($_GET['page'])) {
      $current_page = $_GET['page'];
    } else {
      $current_page = 1;
    }
    if ($current_page >= 2) {
      $prev_page = $current_page - 1;
    }
    $next_page = $current_page + 1;
    $offset = ($current_page - 1)*30;

    if ($_GET['id']) { // if group ID supplied, display only members by that group

      // Connect to database
      try {

        $user = json_decode(json_encode(get_user($_GET["id"])), true);
        $followers_by_user = json_decode(json_encode(get_followers_by_user($_GET["id"], 30, 'firstName', 1, $offset)), true);

      } catch (Exception $e) {

        echo $e->getMessage();
        die();

      }

    } else { // If no group ID supplied, get outta here!

      redirect('/home.php');

    }

    $full_name = ucfirst($user[0]['firstName']) . ' ' . ucfirst($user[0]['lastName']);
    $title_status = "TC Followers View - " . $full_name;

    $total_count = count($followers_by_user);
    if ($total_count < 30) {
      $last_page = 1;
    }

    if (!$last_page) {
      $url_prev = site_url() . "/followers.php?id=" . $_GET["id"] . "&page=" . $next_page;
    }

    if ($current_page != 1) {
      $url_next = site_url() . "/followers.php?id=" . $_GET["id"] . "&page=" . ($current_page - 1);
    }



      if (count($followers_by_user) > 0) {

        foreach ($followers_by_user as $member_single) {

          if ($member_single['bio']) {

            $user_bio = $member_single['bio'];

            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);
            $preview_bio = substr($user_bio, 0, 140);
            if (strlen($user_bio) > 140) { $preview_bio = $preview_bio . '...'; }
            $preview_bio = $purifier->purify($preview_bio);

          } else {

            $date = new DateTime(timestamp($member_single['lastActive']['$date'], 'D j M Y-m-d G:i:s'));
            $now = new DateTime();
            $diff = $now->diff($date);
            if($diff->days < 14) {
              $lastActive = "was active recently";
            } elseif($diff->days < 42) {
              $lastActive = "was active a while ago";
            } else {
              $lastActive = "has not been active in a while";
            }

            if ($member_single['time'] != NULL) {
              $dateJoined = timestamp($member_single['time']['$date'], 'F Y');
            } else {
              $dateJoined = 'October 2017';
            }

            $preview_bio = ucwords($member_single['firstName']) . " has been a member of TeachersConnect since " . $dateJoined;

            if ($lastActive) {
              $preview_bio = $preview_bio . " and " . $lastActive;
            }

            $preview_bio = $preview_bio . ".";

          }

          // Render posts using feedcard template
          $feed_cards = $feed_cards . $templates->render('member-card',
            [
            	'user_id' => $member_single['_id']['$oid'],
              'user_fullname' => $member_single['firstName'] . ' ' . $member_single['lastName'],
              'user_avatar' => $member_single['avatar'],
            	'preview_bio' => $preview_bio
            ]
          );

          if ($last_count == 0) {

            $from_first_name = $member_single['firstName'];

          }

          if ($last_count == ($total_count-1)) {

            $to_first_name = $member_single['firstName'];

          }

          $last_count++;

        }

      } else {  // No members found

        // Create page body
        $feed_cards = $templates->render('error',
          [
            'page' => 'members'
          ]
        );

      }

      // Create page header
      $page_header = $templates->render('layout-header',
        [
          'page' => 'followers',
          'title' => $title_status,
          'fullName' => $full_name,
          'user_avatar' => $user[0]['avatar'],
          'user_id' => $_GET['id'],
          'prev_page' => $prev_page,
          'current_page' => $current_page,
          'next_page' => $next_page,
          'url_next' => $url_next,
          'url_prev' => $url_prev,
          'last_page' => $last_page,
          'total_count' => $total_count,
          'from_first_name' => $from_first_name,
          'to_first_name' => $to_first_name
        ]
      );

      // Create page footer
      $page_footer = $templates->render('layout-footer',
        [
          'page' => 'followers',
          'title' => $title_status,
          'user_id' => $_GET['id'],
          'prev_page' => $prev_page,
          'current_page' => $current_page,
          'next_page' => $next_page,
          'url_next' => $url_next,
          'url_prev' => $url_prev,
          'last_page' => $last_page,
          'total_count' => $total_count
        ]
      );

      // Display page header
      echo $page_header;

      // Display post cards
      echo $feed_cards;

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
