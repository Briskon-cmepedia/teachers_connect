<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
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
    $offset = ($current_page - 1)*20;

    // Update groups session variables
    update_groups_session();

    // Connect to database
    try {
      $notifications = json_decode(json_encode(get_notifications(20, $offset)), true);
      $new_timestamp = json_decode(json_encode(update_notifications_timestamp()), true);

      $old_timestamp = $_SESSION['notificationTimestamp'];

      if ($new_timestamp) {

        $_SESSION['notificationTimestamp'] = $new_timestamp;

      }

    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }


    $title_status = "TC Notifications - " . ucfirst($_SESSION['firstName']) . ' ' . ucfirst($_SESSION['lastName']);
    $last_count = 0;
    $total_count = count($notifications);
    $notification_list = '';

    if ($total_count < 20) {
      $last_page = 1;
    }

    // Define previous and next links for pagination
    if (!$last_page) {
      $url_prev = site_url() . "/notifications.php?page=" . $next_page;
    }

    if ($current_page != 1) {
      $url_next = site_url() . "/notifications.php?page=" . ($current_page - 1);
    }

    if ($notifications) {

      $notification_list = $notification_list . "<div class='notifications-list'>";

      foreach ($notifications as $notification) {

        if ($notification['notificationType'] == 'account violation') {

          $notification_text = 'There is a problem with your account. Please click here to find out more.';

        } else {

          if (strlen($notification['initialPcontent']) > 305) { // Strip long posts down to 335 characters

            $initial_pcontent = substr(strip_tags($notification['initialPcontent']), 0, 305) . '...';

          } else {

            $initial_pcontent = strip_tags($notification['initialPcontent']);

          }

          if ($notification['initialId'] == $_SESSION['uid']) { // Correct grammar for anonymous questions

            if ($notification['initialName'] == 'anonymous') {

              $post_owner = 'your anonymous';

            } else {

              $post_owner = 'your';

            }

          } elseif ($notification['initialName'] == 'anonymous') {

            $post_owner = 'an anonymous';
            //$post_owner = 'a';

          } elseif ($notification['initialId'] == $notification['responderId']) {

            $post_owner = 'their own';

          } else {

            $post_owner = ucwords(strtolower($notification['initialName'])) . '\'s';

          }

          if ($notification['responderId'] == $_SESSION['uid']) {

            $responderName = 'You';

          // } elseif ($notification['initialName'] == 'anonymous') {
          //
          //   $responderName = 'Someone';

          } else {

            $responderName = ucwords(strtolower($notification['responderName']));

          }

          if ($notification['notificationType'] == 'answer') {

            if ($notification['initialName'] == 'anonymous' AND $notification['initialId'] == $notification['responderId']) {

              $notification_text = 'Someone responded to ' . $post_owner . ' question:';

            } else {

              $notification_text = $responderName . ' responded to ' . $post_owner . ' question:';

            }

          } elseif ($notification['notificationType'] == 'follow') {

            $notification_text = $responderName . ' started following you.';

          } elseif ($notification['notificationType'] == 'membership approved') {

            $notification_text = $responderName . ' has approved your community membership.';

          } else {

            $notification_text = $responderName . ' commented on ' . $post_owner . ' post:';

          }

        }



          $notification_list = $notification_list . '<div data-pid="' . $notification['initialPid'] . '"  data-cid="' . $notification['responderPid'] . '" class="notification';

          if ($notification['time']['$date'] > $old_timestamp) {

            $notification_list = $notification_list . ' unseen';

          }

          if ($notification['notificationType'] == 'follow') {

            $notification_list = $notification_list . ' follow';

          } elseif ($notification['notificationType'] == 'membership approved') {

            $notification_list = $notification_list . ' approved';

          } elseif ($notification['notificationType'] == 'account violation') {

            $notification_list = $notification_list . ' violation';

          } else {

            $notification_list = $notification_list . ' post';

          }

            $notification_list = $notification_list . '">';

          $notification_list = $notification_list . '<div class="notification-user-pic pic55">';

          if ($notification['notificationType'] == 'account violation') { // Show TC logo for account violations

            $notification_list = $notification_list . '<img class="avatar" src="img/' . $notification['responderImage'] . '">';

          } elseif ($notification['notificationType'] == 'membership approved' AND $notification['responderImage'] != NULL) { // If member approved and avatar isn't empty, display group tile avatar image

            $notification_list = $notification_list . '<img class="avatar" src="image.php?id=' . $notification['responderImage'] . '">';

          } elseif (is_array($notification['responderImage'])) { // Show avatar if embedded in array (how does this happen?)
            $defaultImage = 'img/robot.svg';
            $notification_list = $notification_list . '<img class="avatar" alt="avatar" src="image.php?id=' . $notification['responderImage'][0] . '&height=200"  onerror="this.src='.$defaultImage.'">';

          } elseif ($notification['responderImage'] != NULL) { // If avatar isn't empty, display profile avatar image
            $defaultImage = 'img/robot.svg';
            $notification_list = $notification_list . '<img class="avatar" alt="avatar" src="image.php?id=' . $notification['responderImage'] . '&height=200"  onerror="this.src='.$defaultImage.'">';
          } else {

            $notification_list = $notification_list . '<img class="avatar" src="img/robot.svg" alt="robot avatar">';

          }

          $notification_list = $notification_list . '</div>';
          $notification_list = $notification_list . '<div class="notification-text"><div class="notification-text-header">' . $notification_text . '</div>';
          if ($notification['notificationType'] != 'follow' AND $notification['notificationType'] != 'membership approved' AND $notification['notificationType'] != 'account violation') {
            $notification_list = $notification_list . '<div class="notification-text-content">"' . $initial_pcontent . '"</div>';
          }
          $notification_list = $notification_list . '</div>';
          $notification_list = $notification_list . '<div data-id="' . $notification['time']['$date'] . '" class="notification-date right"><span class="timestamp" style="color: 4B5353;">' . timestamp($notification['time']['$date'], 'j M g:ia') . '</span><img class="arrow-right" src="/img/arrow-right.svg" alt="' . $responderName . '\'s profile"></div>';
          $notification_list = $notification_list . '</div>';

          if ($last_count == 0) {

            $start_date = timestamp($notification['time']['$date'], 'j M');
            $start_date_raw = $notification['time']['$date'];

          }

          if ( $last_count == ( $total_count - 1 ) ) {

            $end_date = timestamp($notification['time']['$date'], 'j M');
            $end_date_raw = $notification['time']['$date'];

          }

          $last_count++;

        } // end foreach notifications

        $notification_list = $notification_list . "</div>";

      } else {

        // Create no notifications found messaging
        $notification_list = $templates->render('error',
          [
            'page' => 'notifications'
          ]
        );

      }

      // Create page header
      $page_header = $templates->render('layout-header',
        [
          'page' => 'notifications',
          'title' => $title_status,
          'current_page' => $current_page,
          'prev_page' => $prev_page,
          'next_page' => $next_page,
          'url_next' => $url_next,
          'url_prev' => $url_prev,
          'last_page' => $last_page,
          'total_count' => $total_count,
          'start_date_raw' => $start_date_raw,
          'end_date_raw' => $end_date_raw,
          'end_date' => $end_date,
          'start_date' => $start_date
        ]);

    // Create page footer
    $page_footer = $templates->render('layout-footer',
      [
        'page' => 'notifications',
        'title' => $title_status,
        'current_page' => $current_page,
        'prev_page' => $prev_page,
        'next_page' => $next_page,
        'url_next' => $url_next,
        'url_prev' => $url_prev,
        'total_count' => $total_count
      ]);

      // Display page header
      echo $page_header;

      // Display notifications list
      echo $notification_list;

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
