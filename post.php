<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
require 'includes/email.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  $blocked = 0;
  $words_found = "";
  $activity_data = [];

  // Get config settings
  try {
    $config = json_decode(json_encode(get_config()), true);
  } catch (Exception $e) {
    echo $e->getMessage();
    die();
  }

  // Check for blocked words in content
  foreach ($config[0]['wordMatchBlocked'] as $word) {
    if (stripos($_POST['text'], $word) !== false) {
      $blocked++;
      $words_found .= $word." ";
    }
  }

  // If banned words found, make user untrusted
  if ($blocked > 0 AND stripos($_SESSION['email'], '@teachersconnect.com') === false) {
    $activity_data[] = $words_found;
    try {
      $user_trust = write_user_trust($_SESSION['uid'], 0);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }
    try {
      new_activity_log($_SESSION['uid'], 'untrusted due to blocked content', $activity_data);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }
  }

  // Get current record of author
  try {
    $visitor = json_decode(json_encode(get_user($_SESSION['uid'])), true);
  } catch (Exception $e) {
    echo $e->getMessage();
    die();
  }

    // Variable Setup
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    $sanitized_text = $purifier->purify($_POST['text']);
    $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

    if ( ($_POST['action'] == "edit") AND $_POST['pid'] AND $sanitized_text) { // Update post if post id and action supplied

      $post = json_decode(json_encode(get_post($_POST['pid'])), true);

      if ($post) { // Collate information about the original post

        $post_author = json_decode(json_encode(get_author($post[0]['userId'])), true);

      }

      if ($_SESSION['uid'] == $post_author[0]['_id']['$oid']) {

        $post_update = json_decode(json_encode(update_post($_POST['pid'], $sanitized_text)), true);

        if ($post_update == false){

          echo 'Something went wrong - post update failed!';

        } else { // Redirect after successful post edit

          $activity_data[] = $_POST['pid'];
          $activity_data[] = $post;
          $activity_data[] = $sanitized_text;
          new_activity_log($_SESSION['uid'], 'edited post', $activity_data);

          redirect('/view.php?id=' . $_POST['pid'] . '&alert=success-post-edited');

        }

      } else {

        echo 'Something went wrong - post author mismatch!';
        die();

      }

    } elseif ( ($_POST['action'] == "edit") AND $_POST['cid'] AND $sanitized_text) { // Update comment if comment id and action supplied

        $post = json_decode(json_encode(get_comment($_POST['cid'])), true);

        if ($post) {

          $post_id = $post[0]['_id']['$oid'];

          foreach ($post[0]['comments'] as $comment) {

            if ( ($comment['_id']['$oid'] == $_POST['cid']) AND ($comment['userId'] == $_SESSION['uid']) ) {

              $comment_edit = update_comment($_POST['cid'], $sanitized_text, $post_id);

              if ($comment_edit == false){

                echo 'Something went wrong - comment update failed!';
                die();

              } else {

                $activity_data[] = $post_id;
                $activity_data[] = $post;
                $activity_data[] = $sanitized_text;
                new_activity_log($_SESSION['uid'], 'edited comment', $activity_data);

                redirect('/view.php?id=' . $post_id . '&alert=success-comment-edited');

              }

            }

          }

        }


    } elseif ($_POST['pid'] AND $sanitized_text) { // Post comment/answer if post id supplied

      include('s3.php');

      $comment_new = json_decode(json_encode(new_comment($_POST['pid'], $sanitized_text, $image_display)), true);
      $post = json_decode(json_encode(get_post($_POST['pid'])), true);

      if ($post) { // Collate information about the original post

        $post_author = json_decode(json_encode(get_author($post[0]['userId'])), true);

        $post_audience = $post[0]['audience'];

        // if ($post[0]['reactions']['highfive']) {
        //   $notify_users = $post[0]['reactions']['highfive'];
        // } else {
        //   $notify_users = [];
        // }

        $notify_users = $post[0]['following'];

        $responder_name = ucfirst($_SESSION['firstName']) . ' ' . ucfirst($_SESSION['lastName']);

        if ($post[0]['isAnonymous'] == true) {
          $initial_name = 'anonymous';
        } else {
          $initial_name = ucfirst($post_author[0]['firstName']) . ' ' . ucfirst($post_author[0]['lastName']);
        }

        if ($post[0]['type'] ==  'question') {
          $notification_type = 'answer';
          $initial_ptype = 'question';
        } else {
          $notification_type = 'comment';
          $initial_ptype = 'post';
        }

        $link = site_url() . '/view.php?id=' . $_POST['pid'] . '#' . $comment_new;

        if ($visitor[0]['trusted'] == TRUE) { // Publish notification if member is trusted

          // Create new notification
          $notification_new = new_notification($responder_name, $_SESSION['uid'], $comment_new, $_SESSION['avatar'], $notification_type, $initial_name, $post[0]['userId'], $_POST['pid'], $initial_ptype, $post[0]['text'], $notify_users);

          // Check email notification settings and send emails where appropriate
          foreach ($notify_users as $uid) {

            if ($uid != $_SESSION['uid']) {

              // Connect to database
              try {
                $user = json_decode(json_encode(get_user($uid)), true);
              } catch (Exception $e) {
                echo $e->getMessage();
                die();
              }

              // echo $user[0]['firstName'].$user[0]['lastName'].$uid.$post[0]['userId'].$user[0]['emailNotifications']['interact'];

              if (
                ($uid !== $post[0]['userId'] AND $user[0]['emailNotifications']['interact'] == 1)  OR
                ($uid == $post[0]['userId'] AND $user[0]['emailNotifications']['comment'] == 1 AND $initial_ptype == 'post') OR
                ($uid == $post[0]['userId'] AND $user[0]['emailNotifications']['answer'] == 1 AND $initial_ptype == 'question')
              ) {

                // Reduce content for post preview and clean up any tags
                $preview_content = strip_tags($post[0]['text']);
                if (strlen($preview_content) > 335) {
                  $preview_content = substr($preview_content, 0, 335);
                  $preview_content = $preview_content . '...';
                }

                $email_format = formatEmail($notification_type, $user[0]['firstName'], $responder_name, $link, $user[0]['emailNotifications'], $initial_name, $preview_content);

                if($email_format) {

                  $email_sent = sendEmail($user[0]['firstName'] . " " . $user[0]['lastName'], $user[0]['email'], $email_format);

                }

              }

            }

          }

          // Auto follow post when comment/answer made
          if (!$notify_users) { $notify_users = []; }
          if (in_array($_SESSION['uid'], $notify_users)) { } else {
            $notify_users[] = $_SESSION['uid'];
            $follow_post = follow_post($_POST['pid'], $notify_users);
          }

        }

      }

      $activity_data[] = $_POST['pid'];
      $activity_data[] = $comment_new;
      $activity_data[] = $post_audience;
      new_activity_log($_SESSION['uid'], 'created '.$notification_type, $activity_data);

      redirect('/view.php?id=' . $_POST['pid'] . '#' . $comment_new);

      // echo '<a href="'.site_url() . '/view.php?id=' . $_POST['pid'] . '#' . $comment_new.'">'.site_url() . '/view.php?id=' . $_POST['pid'] . '#' . $comment_new.'</a>';

      // }

    } elseif (!$_POST['pid'] AND $sanitized_text) { // Post new post if post text supplied

      include('s3.php');

      // echo "<pre>";
      // print_r($clientS3);
      // print_r($image_display);
      // echo "</pre>";

      if ($_POST['audience'] == "0") {
        $post_new = new_post($sanitized_text, $_POST['type'], $_POST['privacy'], $image_display);
      } else {
        $post_new = new_post($sanitized_text, $_POST['type'], $_POST['privacy'], $image_display, $_POST['audience']);
      }

      // if ($post_new == false){
      //
      //   echo 'Something went wrong - new post failed!';
      //   die();
      //
      // } else {

        if (!$_POST['type']) {
      		$_POST['type'] = 'post';
      	}
        $activity_data[] = $post_new;
        $activity_data[] = $_POST['audience'];
        new_activity_log($_SESSION['uid'], 'created '.$_POST['type'], $activity_data);

        // if ($_POST['type'] == 'question') {
        //   redirect('/feed.php?id=' . $_POST['audience'] . '&alert=success-question-created');
        // } else {
        //   redirect('/feed.php?id=' . $_POST['audience'] . '&alert=success-post-created');
        // }

        if ($post_author_trust == TRUE) {
          redirect('/feed.php?id=' . $_POST['audience'] . '&alert=success-'.$_POST['type'].'-created');
        } else {
          redirect('/view.php?id=' . $post_new . '&alert=success-'.$_POST['type'].'-created&status=moderation');
        }

      // }

    } elseif (!$_POST['pid']) { // Display post form if post id not supplied

      header("location:javascript://history.go(-1)");

    } else {

      echo 'Something went wrong - no post id supplied!';
      die();

    }


} else { // Redirect user to login page if no valid session

  save_request_uri($_SERVER['REQUEST_URI']); // store request uri

  redirect('/auth.php');

}
