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

    if ($_GET['search']) { // If keywords supplied, perform search on member firstName and lastName

      // Connect to database
      try {
        $members_list = json_decode(json_encode(SearchEngine::boot()->search_members($_GET['search'], 30, $offset)), true);
        echo '<pre>';
        print_r($members_list);
        exit();
        // $members_list = json_decode(json_encode(search_members($_GET['search'], 30, $offset)), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      $feed_title = "Member search results for: " . htmlspecialchars_decode($_GET['search'], ENT_QUOTES);
      $partner_name = "Member search results for: " . htmlspecialchars_decode($_GET['search'], ENT_QUOTES);
      $partner_logo = "icon-search-header.svg";
      $feed_number = "0";
      $search_term = $_GET['search'];

      $total_count = count($members_list);

      $activity_data[] = $search_term;
      new_activity_log($_SESSION['uid'], 'searched members', $activity_data);

      if ($total_count < 30) {
        $last_page = 1;
      }

      if (!$last_page) {
        $url_prev = site_url() . "/members.php?search=" . urlencode($_GET['search']) . "&page=" . $next_page;
      }

      if ($current_page != 1) {
        $url_next = site_url() . "/members.php?search=" . urlencode($_GET['search']) . "&page=" . ($current_page - 1);
      }

      // Collate post cards for display
      if ($total_count > 0) {

        foreach ($members_list as $member_single) {

          if($member_single['bio']) {

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
              'user_id' => $member_single['id'],
              'user_fullname' => ucfirst($member_single['firstName']) . ' ' . ucfirst($member_single['lastName']),
              'user_avatar' => $member_single['avatar'],
              'preview_bio' => $preview_bio
            ]
          );

          // $card_count++;

          if ($last_count == 0) {

            $from_first_name = $member_single['firstName'];

          }

          if ( $last_count == ( $total_count - 1 ) ) {

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

    } elseif ($_GET['id']) { // if group ID supplied, display only members by that group

      // Get group data
      try {
        $group = json_decode(json_encode(get_group($_GET['id'])), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      // // Get subscribed groups
      // try {
      //   $user_groups = json_decode(json_encode(get_groups_by_user($_SESSION['uid'])), true);
      // } catch (Exception $e) {
      //   echo $e->getMessage();
      //   die();
      // }
      //
      // $myGroups = [];
      // foreach ($user_groups as $user_group) {
      //   $myGroups[] = $user_group['_id']['$oid'];
      // }

      $partner_name = $group[0]['name'];
      $group_members = $group[0]['users'];
      $partner_logo = $group[0]['logo'];
      $partner_description = $group[0]['description'];
      $feed_number = $group[0]['_id']['$oid'];
      $group_privacy = $group[0]['privacy'];
      $feed_title = $partner_name . " Members View";

      if ( $group_privacy == 'private' AND !in_array($_GET['id'], $myGroups) ) { // Block access to private community feeds if you are not a member

        // Create page body
        $page_body = $templates->render('private-community-blocked',
          []
        );

        // Create page header
        $page_header = $templates->render('layout-header',
          [
            'partner_name' => $partner_name,
            'title' => 'Access Restricted - ' . $partner_name . ' Members'
          ]
        );

        // Create page footer
        $page_footer = $templates->render('layout-footer',
          []
        );

        // Display page header
        echo $page_header;

        // Display page body
        echo $page_body;

        // Display page footer
        echo $page_footer;

        die();

      }

      if ($group_members == NULL) { $group_members = []; }

      // Check if visitor is following community
      if (in_array($_SESSION['uid'], $group_members)) {
          $community_followed = 1;
      }

      $group_members_count = count($group_members);

      if ($group_members_count > 0) {

        $members = [];
        foreach ($group_members as $group_member) {

          $member_id = new MongoDB\BSON\ObjectID($group_member);
          array_push($members, $member_id);

        }

      }

      if ($group_members_count > 0) {

        // Connect to database
        try {

          $members_list = json_decode(json_encode(get_authors($members, 30, 'firstName', 1, $offset)), true);

        } catch (Exception $e) {

          echo $e->getMessage();
          die();

        }

      }

      $total_count = count($members_list);

      if ($total_count < 30) {
        $last_page = 1;
      }

      if (!$last_page) {
        $url_prev = site_url() . "/members.php?id=" . $feed_number . "&page=" . $next_page;
      }

      if ($current_page != 1) {
        $url_next = site_url() . "/members.php?id=" . $feed_number . "&page=" . ($current_page - 1);
      }

      // Collate post cards for display
      if (count($group_members) > 0) {

        foreach ($members_list as $member_single) {

          if($members_list[ $member_single['_id']['$oid'] ]['bio']) {

            $user_bio = $members_list[ $member_single['_id']['$oid'] ]['bio'];

            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);
            $preview_bio = substr($user_bio, 0, 140);
            if (strlen($user_bio) > 140) { $preview_bio = $preview_bio . '...'; }
            $preview_bio = $purifier->purify($preview_bio);

          } else {

            $date = new DateTime(timestamp($members_list[ $member_single['_id']['$oid'] ]['lastActive']['$date'], 'D j M Y-m-d G:i:s'));
            $now = new DateTime();
            $diff = $now->diff($date);
            if($diff->days < 14) {
              $lastActive = "was active recently";
            } elseif($diff->days < 42) {
              $lastActive = "was active a while ago";
            } else {
              $lastActive = "has not been active in a while";
            }

            if ($members_list[ $member_single['_id']['$oid'] ]['time'] != NULL) {
              $dateJoined = timestamp($members_list[ $member_single['_id']['$oid'] ]['time']['$date'], 'F Y');
            } else {
              $dateJoined = 'October 2017';
            }

            $preview_bio = ucwords($members_list[ $member_single['_id']['$oid'] ]['firstName']) . " has been a member of TeachersConnect since " . $dateJoined;

            if ($lastActive) {
              $preview_bio = $preview_bio . " and " . $lastActive;
            }

            $preview_bio = $preview_bio . ".";

          }




          // Render posts using feedcard template
          $feed_cards = $feed_cards . $templates->render('member-card',
            [
              'user_id' => $member_single['_id']['$oid'],
              'user_fullname' => ucfirst($members_list[ $member_single['_id']['$oid'] ]['firstName']) . ' ' . ucfirst($members_list[ $member_single['_id']['$oid'] ]['lastName']),
              'user_avatar' => $members_list[ $member_single['_id']['$oid'] ]['avatar'],
              'preview_bio' => $preview_bio
            ]
          );

          if ($last_count == 0) {

            $from_first_name = $members_list[ $member_single['_id']['$oid'] ]['firstName'];

          }

          if ( $last_count == ( $total_count - 1 ) ) {

            $to_first_name = $members_list[ $member_single['_id']['$oid'] ]['firstName'];

          }

          $last_count++;

        }

      } else {

        // Create page body
        $feed_cards = $templates->render('error',
          [
            'page' => 'members'
          ]
        );

      }



    } else { // If no group ID supplied, get outta here!

      redirect('/home.php');

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
        'search_term' => $search_term,
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
        'to_first_name' => $to_first_name,
        'community_followed' => $community_followed
      ]
    );

    // Create page footer
    $page_footer = $templates->render('layout-footer',
      [
        'page' => 'members',
        'title' => $feed_title,
        'feed_number' => $feed_number,
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
