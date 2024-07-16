<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

  if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

    // // Connect to database
    // try {
    //   $visitor = json_decode(json_encode(get_user($_SESSION['uid'])), true);
    // } catch (Exception $e) {
    //   echo $e->getMessage();
    //   die();
    // }

    if ($_GET['id']) {
      $profileId = $_GET['id'];
    } else {
      $profileId = $_SESSION['uid'];
    }

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

    // Connect to database
    try {

      $user = json_decode(json_encode(get_user($profileId)), true);
      $you = json_decode(json_encode(get_user($_SESSION['uid'])), true);
      $groups = json_decode(json_encode(get_groups_by_user($profileId)), true);
      $followers_by_user = json_decode(json_encode(get_followers_by_user($profileId)), true);
      $posts_and_comments_by_user = json_decode(json_encode(get_posts_and_comments_by_user($profileId)), true);

    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    if ($_SESSION['uid'] == $profileId) {
      $title_profile_type = "Private";
    } else {
      $title_profile_type = "Public";
    }
    $title_status = "TC Profile View - " . ucfirst($user[0]['firstName']) . ' ' . ucfirst($user[0]['lastName']) . ' - ' . $title_profile_type;

    // Display user profile header
    if ($user) {

      $user_experience = $user[0]['experience'];
      $user_education = $user[0]['educations'];

      // Sort education history (newest-oldest)
      usort($user_education, function($a, $b) {
          return $b['yearCompleted'] - $a['yearCompleted'];
      });

      foreach ($user_experience as &$experience) {
    		if ($experience['datesWorked']['selectedEnd']=='Present') {
          $experience['datesWorked']['selectedEnd'] = date('Y');
    			$user_school = $experience['school'];
    		}
    	}

      // Sort experience history (newest-oldest)
      usort($user_experience, function($a, $b) {
          return $b['datesWorked']['selectedEnd'] - $a['datesWorked']['selectedEnd'];
      });

      // Calculate activity statistics
      $date = new DateTime(timestamp($user[0]['lastActive']['$date'], 'D j M Y-m-d G:i:s'));
      $now = new DateTime();
      $diff = $now->diff($date);
      if($diff->days < 14) {
        $lastActive = "was active recently";
      } elseif($diff->days < 42) {
        $lastActive = "was active a while ago";
      } else {
        $lastActive = "has not been active in a while";
      }

      // Calculate date joined
      if ($user[0]['time'] != NULL) {
        $dateJoined = timestamp($user[0]['time']['$date'], 'F Y');
      } else {
        $dateJoined = 'October 2017';
      }

      // Collate user status message
      $user_status = ucwords($user[0]['firstName']) . " has been a member of TeachersConnect since " . $dateJoined;

      if ($lastActive) {
        $user_status = $user_status . " and " . $lastActive;
      }

      $user_status = $user_status . ".";

      // Calculate follower/following count
      $user_following = $user[0]['following'];

      if ($user_following == NULL) {
        $user_following = [];
      }

      $user_following_count = count($user_following);
      $user_follower_count = count($followers_by_user);

      // Calculate total contributions count
      $count_contributions = 0;
      if ($_SESSION['uid'] == $profileId) { // If profile owner, count all contributions

        foreach ($posts_and_comments_by_user as $post_by_user) {
          if ($post_by_user['userId'] == $profileId) {
            $count_contributions++;
          }
          foreach ($post_by_user['comments'] as $comment_by_user) {
            if ($comment_by_user['userId'] == $profileId) {
              $count_contributions++;
            }
          }
        }

      } else { // If profile visitor, count all contributions except anonymous questions

        foreach ($posts_and_comments_by_user as $post_by_user) {
          if ( ($post_by_user['userId'] == $profileId) AND ($post_by_user['isAnonymous'] == FALSE) ) {
            $count_contributions++;
          }
          foreach ($post_by_user['comments'] as $comment_by_user) {
            if ($comment_by_user['userId'] == $profileId) {
              $count_contributions++;
            }
          }
        }
      }

      // Calculate current total number of helpfuls

      $user_helpfuls_count = 0;
      $helpful_count_post = 0;
      $helpful_count_posts = 0;
      $helpful_count_comment = 0;
      $helpful_count_comments = 0;
      foreach ($posts_and_comments_by_user as $post_by_user) {
        if ($post_by_user['userId'] == $profileId) {
          $helpful_count_post = count($post_by_user['reactions']['thumbsup']);
          $helpful_count_posts = $helpful_count_posts + $helpful_count_post;
        }
        foreach ($post_by_user['comments'] as $comment_by_user) {
          if ($comment_by_user['userId'] == $profileId) {
            $helpful_count_comment = count($comment_by_user['reactions']['thumbsup']);
            $helpful_count_comments = $helpful_count_comments + $helpful_count_comment;
          }
        }
      }
      $user_helpfuls_count = $helpful_count_posts + $helpful_count_comments;

      // Check if visitor is following member
      if (is_array($you[0]['following'])) {
        $you_following = $you[0]['following'];
      } else {
        $you_following[] = $you[0]['following'];
      }
      if ( ($_SESSION['uid'] != $profileId) AND (in_array($profileId, $you_following)) ) {
          $user_followed = 1;
      }

      // Check for current account Violations
      if ($you[0]['violations'] AND ($_SESSION['uid'] == $profileId)) {

        $violations = 0;
        foreach ($you[0]['violations'] as $violation) {
          if ($violation['status'] != 'resolved') {
            $violations = 1;
          }
        }

      }

      // Create page body
      $page_body = $templates->render('view-profile',
        [
          'user_id' => $profileId,
          'dateJoined' => $dateJoined,
          'user_firstName' => $user[0]['firstName'],
          'user_lastName' => $user[0]['lastName'],
          'user_avatar' => $user[0]['avatar'],
          'user_bio' => $user[0]['bio'],
          'user_following_count' => $user_following_count,
          'user_follower_count' => $user_follower_count,
          'posts_and_comments_by_user' => $posts_and_comments_by_user,
          'user_helpfuls_count' => $user_helpfuls_count,
          'helpful_count_posts' => $helpful_count_posts,
          'helpful_count_comments' => $helpful_count_comments,
          'user_followed' => $user_followed,
          'user_school' => $user_school,
          'user_experience' => $user_experience,
          'user_education' => $user_education,
          'affiliates_subscribed' => $groups,
          'user_status' => $user_status,
          'post_count' => $count_contributions,
          'action' => $_GET['action'],
          'alert' => $_GET['alert'],
          'comid' => $_GET['comid'],
          'visitor_trusted' => $you[0]['trusted'],
          'violations' => $violations,
          'user_access' => $db_access
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

    if (count($posts_and_comments_by_user) < 30) {
      $last_page = 1;
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
        'feed_number' => $feed_number,
        'user_id' => $profileId,
        'next_page' => $next_page,
        'last_page' => $last_page
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
