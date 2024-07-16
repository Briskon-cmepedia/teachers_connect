<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
// require 'includes/email.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Connect to database
  try {
    $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);
  } catch (Exception $e) {
    echo $e->getMessage();
    die();
  }

  if ($user[0]['trusted'] == TRUE) {

  // if ($user[0]['time']['$date'] < '1540860323014') {

    // Variable Setup
    $p = file_get_contents('php://input');
    $x = json_decode($p, true);
    $action = json_decode($x['action']);
    $text = json_decode($x['text']);
    $cid = json_decode($x['cid']);
    $conversationName = json_decode($x['conversationName']);
    $addParticipants = json_decode($x['addParticipants']);
    $removeParticipants = json_decode($x['removeParticipants']);
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    $sanitized_text = $purifier->purify($text);
    $sanitized_name = $purifier->purify($conversationName);

    if ($cid AND $action == 'update_conversation') {

      $conversation = json_decode(json_encode(get_conversation_by_id($cid)), true);

      if ($conversation) {

        if ($conversation[0]['owner'] == $_SESSION['uid']) {

          $updates = [];
          $member_additions = '';
          $newParticipants = [];
          $numberExistingParticipants = count($conversation[0]['participants']);

          // Update conversation name
          $conversation_update_name = json_decode(json_encode(update_conversation_name($cid, $sanitized_name)), true);

          // Remove participants
          if ($removeParticipants) {

            $removeParticipantsArray = explode(",", $removeParticipants);
            $numberRemovedParticipants = count($removeParticipantsArray);

            foreach($removeParticipantsArray as $removeParticipant) {

              $conversation_remove_members = json_decode(json_encode(remove_conversation_members($cid, $removeParticipant)), true);

              if ($conversation_remove_members === true) {

                $updates['removals'][] = $removeParticipant;

              }

            }

          }

          // Add new participants
          if ($addParticipants) {

            $addParticipantsArray = explode(",", $addParticipants);
            $numberParticipantsToAdd = count($addParticipantsArray);
            $numberRemainingParticipants = $numberExistingParticipants - $numberRemovedParticipants;
            $totalProspectiveParticipants = $numberParticipantsToAdd + $numberRemainingParticipants;

            // Limit total participants to 20 in a conversation
            if ($totalProspectiveParticipants > 20 ) {

              $updates['error'] = "You can only add 20 people to a conversation.";
              $numberParticipantsAllowed = (20 - $numberRemainingParticipants);
              $addParticipantsArray = array_slice($addParticipantsArray, 0, $numberParticipantsAllowed);

            }

            foreach($addParticipantsArray as $conversationParticipant) {

              $conversation_add_members = json_decode(json_encode(add_conversation_members($cid, $conversationParticipant)), true);

              if ($conversation_add_members === true) {

                $newParticipants[] = new MongoDB\BSON\ObjectID($conversationParticipant);

              }

            }

            if (!empty($newParticipants)) {

              $conversation_get_members = json_decode(json_encode(get_members_by_id($newParticipants)), true);

              foreach($conversation_get_members as $conversation_get_member) {

                $member_additions .= '<div class="author" id="'.$conversation_get_member['_id']['$oid'].'"><div class="post-header col-avatar small">';
                $defaultImage = "img/robot.svg";
                if ($conversation_get_member['avatar']) {
                  $member_additions .= '<img class="avatar" alt="avatar" src="image.php?id='.$conversation_get_member['avatar'].'&height=200" onerror="this.src='.$defaultImage.'">';
                } else {
                  $member_additions .= '<img class="avatar" src="img/robot.svg" alt="robot avatar">';
                }
                $member_additions .= '</div><div class="post-header"><div class="author-name">'.$conversation_get_member['firstName'].' '.$conversation_get_member['lastName'].'</div></div><div class="checkbox-action"><input class="checkbox-remove" type="checkbox" name="remove[]" value="'.$conversation_get_member['_id']['$oid'].'" id="box-'.$conversation_get_member['_id']['$oid'].'"><label for="box-'.$conversation_get_member['_id']['$oid'].'"></label></div></div>';

              }

              $updates['additions'] = $member_additions;

            }

          }

          // Update frontend with changes
          echo json_encode($updates);

        }

      }

      die();

    }

    if ($cid AND $action == 'new_message') { // Post message if conversation id supplied

      // include('s3.php');

      $conversation = json_decode(json_encode(get_conversation_by_id($cid)), true);

      if ($conversation) {

        $message_new = json_decode(json_encode(new_message($cid, $sanitized_text)), true);

        $activity_data[] = $message_new;
        $activity_data[] = $cid;
        $activity_data[] = $sanitized_text;
        new_activity_log($_SESSION['uid'], 'created message', $activity_data);

        update_conversation_timestamp($cid);

        die();

      }

    }

    die();

  }

}

die();
