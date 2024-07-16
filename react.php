<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

  if ($_POST['pid'] AND $_POST['uid']) {

    if ($_POST['pt'] == "followtopic") { // Follow open community

      $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);

      $topics_followed = $user[0]['topicsFollowed'];

      if (!$topics_followed) { $topics_followed = []; }

      if (in_array($_POST['pid'], $topics_followed)) { // If user is already group follower, remove them

  			if (($key = array_search($_POST['pid'], $topics_followed)) !== false) {

  		    unset($topics_followed[$key]);

  			}

  			$reaction = update_topics_followed($_SESSION['uid'], $topics_followed);
        if ($reaction) {
          echo 'Follow';
        }

        // $activity_data[] = $_POST['pid'];
        // new_activity_log($_SESSION['uid'], 'unfollowed post', $activity_data);

  		} else { // If user is not group follower, add them

  			$topics_followed[] = $_POST['pid'];
        $reaction = update_topics_followed($_SESSION['uid'], $topics_followed);
        if ($reaction) {
          echo 'Unfollow';
        }

        // $activity_data[] = $_POST['pid'];
        // new_activity_log($_SESSION['uid'], 'followed post ', $activity_data);

  		}

    } elseif ($_POST['pt'] == "followcommunity") { // Follow open community

      $group = json_decode(json_encode(get_group($_POST['pid'])), true);

      $group_privacy = $group[0]['privacy'];

      if ($group_privacy == 'public') {

    		$group_users = $group[0]['users'];
        if (!$group_users) { $group_users = []; }

        if (in_array($_SESSION['uid'], $group_users)) { // If user is already group follower, remove them

    			if (($key = array_search($_SESSION['uid'], $group_users)) !== false) {

    		    unset($group_users[$key]);

    			}

    			$reaction = update_group($_POST['pid'], $group_users);
          if ($reaction) {
            echo 'Follow';
          }

          // $activity_data[] = $_POST['pid'];
          // new_activity_log($_SESSION['uid'], 'unfollowed post', $activity_data);

    		} else { // If user is not group follower, add them

    			$group_users[] = $_SESSION['uid'];
          $reaction = update_group($_POST['pid'], $group_users);
          if ($reaction) {
            echo 'Unfollow';
          }

          // $activity_data[] = $_POST['pid'];
          // new_activity_log($_SESSION['uid'], 'followed post ', $activity_data);

    		}

      }

    } elseif ($_POST['pt'] == "followpost") { // Subscribe to new updates by following post

      $post = json_decode(json_encode(get_post($_POST['pid'])), true);

  		$react_array = $post[0]['following'];
      if (!$react_array) { $react_array = []; }

      if (in_array($_SESSION['uid'], $react_array)) { // If user has already reacted, remove them

  			if (($key = array_search($_SESSION['uid'], $react_array)) !== false) {

  		    unset($react_array[$key]);

  			}

  			$reaction = follow_post($_POST['pid'], $react_array);
        if ($reaction) {
          echo 'Follow';
        }

        $activity_data[] = $_POST['pid'];
        new_activity_log($_SESSION['uid'], 'unfollowed post', $activity_data);

  		} else { // If user hasn't already reacted, add them

  			$react_array[] = $_SESSION['uid'];
        $reaction = follow_post($_POST['pid'], $react_array);
        if ($reaction) {
          echo 'Unfollow';
        }

        $activity_data[] = $_POST['pid'];
        new_activity_log($_SESSION['uid'], 'followed post ', $activity_data);

  		}

    } elseif ($_POST['pt'] == "comment") { // Comment reaction

      $post = json_decode(json_encode(get_comment($_POST['pid'])), true);
      //$react_array = $post[0]['reactions']['thumbsup'];
      $comment_array = [];
      $comments_array = $post[0]['comments'];
      $notify_users = $post[0]['following'];
      $post_id = $post[0]['_id']['$oid'];

      foreach ($comments_array as $comment) {
        $comment_array[] = ['id' => $comment['_id']['$oid'], 'thumbsup' => $comment['reactions']['thumbsup']];
      }

      $key = array_search($_POST['pid'], array_column($comment_array, 'id'));

      $reaction_array = $comment_array[$key]['thumbsup'];

      if ($reaction_array == NULL) {
        $reaction_array = [];
      }

      if (in_array($_SESSION['uid'], $reaction_array)) { // If user has already reacted, remove them

  			if (($reaction_key = array_search($_SESSION['uid'], $reaction_array)) !== false) {

  		    unset($reaction_array[$reaction_key]);

  			}

  			$reaction = react_comment($_POST['pid'], $reaction_array, $post_id);
        echo $reaction;

        $activity_data[] = $_POST['pid'];
        new_activity_log($_SESSION['uid'], 'unmarked helpful', $activity_data);

  		} else { // If user hasn't already reacted, add them

  			$reaction_array[] = $_SESSION['uid'];
        $reaction = react_comment($_POST['pid'], $reaction_array, $post_id);
        echo $reaction;

        // Auto follow post when new reaction made
        if (!$notify_users) { $notify_users = []; }
        if (in_array($_SESSION['uid'], $notify_users)) { } else {
          $notify_users[] = $_SESSION['uid'];
          $follow_post = follow_post($post_id, $notify_users);
        }

        $activity_data[] = $_POST['pid'];
        new_activity_log($_SESSION['uid'], 'marked helpful', $activity_data);

  		}


    } else { // Post reaction

      $post = json_decode(json_encode(get_post($_POST['pid'])), true);
      $notify_users = $post[0]['following'];

      if ($post[0]['type'] == 'question') { // If post is a question, focus on Me Too reactions

    		$react_array = $post[0]['reactions']['highfive'];
        $react_type = 'highfive';
        $activity_type = 'same here';

      } else { // If post is not a question, focus on Helpful reactions

        $react_array = $post[0]['reactions']['thumbsup'];
        $react_type = 'thumbsup';
        $activity_type = 'helpful';

      }

      if (in_array($_SESSION['uid'], $react_array)) { // If user has already reacted, remove them

  			if (($key = array_search($_SESSION['uid'], $react_array)) !== false) {

  		    unset($react_array[$key]);

  			}

  			$reaction = react_post($_POST['pid'], $react_array, $react_type);
        echo $reaction;

        $activity_data[] = $_POST['pid'];
        new_activity_log($_SESSION['uid'], 'unmarked '.$activity_type, $activity_data);

  		} else { // If user hasn't already reacted, add them

  			$react_array[] = $_SESSION['uid'];
        $reaction = react_post($_POST['pid'], $react_array, $react_type);
        echo $reaction;

        // Auto follow post when new reaction made
        if (!$notify_users) { $notify_users = []; }
        if (in_array($_SESSION['uid'], $notify_users)) { } else {
          $notify_users[] = $_SESSION['uid'];
          $follow_post = follow_post($_POST['pid'], $notify_users);
        }

        $activity_data[] = $_POST['pid'];
        new_activity_log($_SESSION['uid'], 'marked '.$activity_type, $activity_data);

  		}

    }



  } else { // Do nothing if no data submitted



  }

} else { // Do nothing if no valid session



}
