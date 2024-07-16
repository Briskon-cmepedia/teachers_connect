<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Connect to database
  try {
    $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);
  } catch (Exception $e) {
    echo $e->getMessage();
    die();
  }

  if ($user[0]['trusted'] == TRUE) {

    // Variable Setup
    $p = file_get_contents('php://input');
    $x = json_decode($p, true);
    $cid = json_decode($x['cid']);
    $action = json_decode($x['action']);
    $msgNum = json_decode($x['msgNum']);

    if ($cid AND $action == 'update') { // Check for new messages

      $messagesNew = '';

      $messages = json_decode(json_encode(get_messages_by_conversation($cid)), true);

      if ($messages) {

        $participants = [];

        foreach ($messages[0]['messages'] as $participant) {

          // Create list of participants
          $user_id = new MongoDB\BSON\ObjectID($participant['userId']);
          $participants[] = $user_id;

        }

        $participantInfo = json_decode(json_encode(get_authors($participants)), true);

        foreach ($messages[0]['messages'] as $message) {

          if ($_SESSION['conversations'][$cid] < $message['time']['$date']) {

            if ($participantInfo[$message['userId']]['avatar']) {
              $avatarImage = 'image.php?id='.$participantInfo[$message['userId']]['avatar'].'&height=200';
            } else {
              $avatarImage = 'img/robot.svg';
            }

            $messagesNew .= '<div class="notification animated fadeIn"><div class="author"><a href="#"><div class="post-header col-avatar small"><img class="avatar" src="'.$avatarImage.'" alt="avatar"></div><div class="post-header"><div class="author-name">'.$participantInfo[$message['userId']]['firstName'].' '.$participantInfo[$message['userId']]['lastName'].'</div><div class="post-time notification-date" data-id="'.$message['time']['$date'].'"><span class="timestamp">'.timestamp($message['time']['$date'], 'j M g:iA').'</span></div></div></a></div><div class="content"><div class="comment-content"><div>'.$message['text'].'</div></div></div></div>';

          }

        }

        echo json_encode($messagesNew);

        update_conversation_timestamp($cid);

        die();

      }

    } elseif ($cid AND $action == 'load') { // Load previous messages in history

      $messagesOld = [];

      $messagesAll = json_decode(json_encode(get_messages_by_conversation($cid)), true);

      if ($messagesAll) {

        $participants = [];

        $messages = $messagesAll[0]['messages'];

        uasort($messages, function($a, $b) {
          return $b['time']['$date'] - $a['time']['$date'];
          // return $a['time']['$date'] - $b['time']['$date'];
        });

        $messages = array_slice($messages, $msgNum, 20);

        // foreach ($messages[0]['messages'] as $participant) {
        foreach ($messages as $participant) {

          // Create list of participants
          $user_id = new MongoDB\BSON\ObjectID($participant['userId']);
          $participants[] = $user_id;

        }

        $participantInfo = json_decode(json_encode(get_authors($participants)), true);

        if ($messages) {

          foreach ($messages as $message) {

            // if ($_SESSION['conversations'][$cid] < $message['time']['$date']) {
              $defaultImage = "img/robot.svg";
              $messagesOld[] = '<div class="notification animated fadeIn"><div class="author"><a href="#"><div class="post-header col-avatar small"><img class="avatar" alt="avatar" src="image.php?id='.$participantInfo[$message['userId']]['avatar'].'&height=200" onerror="this.src='.$defaultImage.'"></div><div class="post-header"><div class="author-name">'.$participantInfo[$message['userId']]['firstName'].' '.$participantInfo[$message['userId']]['lastName'].'</div><div class="post-time notification-date" data-id="'.$message['time']['$date'].'"><span class="timestamp">'.timestamp($message['time']['$date'], 'j M g:iA').'</span></div></div></a></div><div class="content"><div class="comment-content"><div>'.$message['text'].'</div></div></div></div>';

            // }

          }

          $messagesOld = array_reverse($messagesOld);

          $updates['additions'] = '<div id="'.$msgNum.'">' . implode($messagesOld) . '</div>';

          if (count($messages) < 20) {
            $updates['stop'] = 1;
          }

          echo json_encode($updates);

        }

      }

      die();

    }

    die();

  }

}

die();
