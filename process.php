<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
require 'includes/email.php';

if ($sessions->sessionCheck()) { // Only process if user has valid session

  // Variable Setup
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

  if (($_GET['type'] == "conversation") AND $_GET['id']) { // Delete conversation if ID supplied

    try {

      $conversation = json_decode(json_encode(get_conversation_by_id($_GET['id'])), true);

      if ($conversation) {

        if ($conversation[0]['owner'] == $_SESSION['uid']) {

          $conversationAction = json_decode(json_encode(delete_conversation($_GET['id'])), true);

          $activity_data[] = $_GET['id'];
          $activity_data[] = $conversation;
          new_activity_log($_SESSION['uid'], 'deleted conversation', $activity_data);

          redirect('/messages-inbox.php?alert=success-conversation-deleted');

          die();

        } else {

          $conversationAction = json_decode(json_encode(leave_conversation($_GET['id'])), true);

          $activity_data[] = $_GET['id'];
          new_activity_log($_SESSION['uid'], 'left conversation', $activity_data);

          redirect('/messages-inbox.php?alert=success-conversation-left');

          die();

        }

      }

    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

  } elseif (($_GET['type'] == "post") AND $_GET['id']) { // Delete post if ID supplied

    try {
      $post = json_decode(json_encode(get_post($_GET['id'])), true);

      if ($post[0]['audience'] != NULL) {

        $feed_number = $post[0]['audience'];

      }

      if ($post) { // Collate and pull authors from database

        $author_id = $post[0]['userId'];

        if ($_SESSION['uid'] == $author_id) {

          $post_delete = delete_post($_GET['id']);

          if ($post_delete == false){

            echo 'Something went wrong';
            die();

          } else {

            $activity_data[] = $_GET['id'];
            $activity_data[] = $post;
            $activity_data[] = $post[0]['text'];
            new_activity_log($_SESSION['uid'], 'deleted post', $activity_data);

            if ($feed_number) {

              redirect('/feed.php?id=' . $feed_number . '&alert=success-post-deleted');

            } else {

              redirect('/feed.php?alert=success-post-deleted');

            }

          }

        }

      }
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

  } elseif (($_GET['type'] == "comment") AND $_GET['id']) { // Delete comment if ID supplied

    try {

      $post = json_decode(json_encode(get_comment($_GET['id'])), true);

      if ($post) {

        $post_id = $post[0]['_id']['$oid'];

        foreach ($post[0]['comments'] as $comment) {

          if ( ($comment['_id']['$oid'] == $_GET['id']) AND ($comment['userId'] == $_SESSION['uid']) ) {

            $comment_delete = delete_comment($post_id, $_GET['id']);

            if ($comment_delete == false){

              echo 'Something went wrong';
              die();

            } else {

              $activity_data[] = $post_id;
              $activity_data[] = $post;
              $activity_data[] = $comment['text'];
              new_activity_log($_SESSION['uid'], 'deleted comment', $activity_data);

              redirect('/view.php?id=' . $post_id . '&alert=success-comment-deleted');

            }

          }

        }

      }

    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

  } elseif (($_GET['type'] == "education") AND $_GET['id']) { // Delete education history if ID supplied

    try {

          // Find and parse user education records
          $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);

          foreach ($user[0]['educations'] as $education) {
            if ($education['_id']['$oid'] == $_GET['id']) {
              $name = $education['institude'];
            }
          }

          // Find group id and users by name
          $user_group = json_decode(json_encode(find_group($name)), true);

          if ($user_group) { // If user in group, remove on delete

            $gid = $user_group[0]['_id']['$oid'];
            $user_array = $user_group[0]['users'];

            if ($user_array == NULL) {
              $user_array = [];
            }

            if (in_array($_SESSION['uid'], $user_array)) { // Remove user from group array
              $uid[] = $_SESSION['uid'];
              $user_group = json_decode(json_encode(remove_group_user($gid, $uid)), true);
            }

            // Make sure user_array is an actual array
            if ($user_array == NULL) {
              $user_array = [];
            }

            if (is_array($user_array)) { } else {
              $user_array = array($user_array);
            }

            // Delete user from group
            $user_group_delete = update_group($gid, $user_array);

            $groups = json_decode(json_encode(get_groups_by_user($_SESSION['uid'])), true);

            // Build list of affiliated partners
            $_SESSION['partners'] = [];
            $_SESSION['myGroups'] = [];
            foreach ($groups as $group) {
                $_SESSION['partners'][] = array('id' => $group['_id']['$oid'], 'name' => $group['name'], 'image' => $group['tile']);
                $_SESSION['myGroups'][] = $group['_id']['$oid'];
            }


          }

          // Delete education from user record
          $user_education_delete = delete_user_education($_SESSION['uid'], $_GET['id']);

          if ($user_education_delete == false){

            echo 'Something went wrong';
            die();

          } else {

            redirect('/edit-profile.php?alert=removed');

          }

    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

  } elseif (($_GET['type'] == "experience") AND $_GET['id']) { // Delete experience history if ID supplied

          $user_experience_delete = delete_user_experience($_SESSION['uid'], $_GET['id']);

          if ($user_experience_delete == false){

            echo 'Something went wrong';
            die();

          } else {

            redirect('/edit-profile.php?alert=removed');

          }


  } elseif (($_GET['type'] == "affiliate") AND $_GET['id']) { // Unsubscribe from affiliate if ID supplied

    try {

          // Find group id and users by name
          // $user_group = json_decode(json_encode(find_group($_GET['id'])), true);
          $user_group = json_decode(json_encode(get_group($_GET['id'])), true);

          if ($user_group) { // Remove user if in group

            $gid = $user_group[0]['_id']['$oid'];
            $user_array = $user_group[0]['users'];

            if ($user_array == NULL) {
              $user_array = [];
            }

            if (in_array($_SESSION['uid'], $user_array)) { // Remove user from group array
              $uid[] = $_SESSION['uid'];
              $user_group = json_decode(json_encode(remove_group_user($gid, $uid)), true);
            }

            $groups = json_decode(json_encode(get_groups_by_user($_SESSION['uid'])), true);

            // Build list of affiliated partners
            $_SESSION['partners'] = [];
            $_SESSION['myGroups'] = [];
            foreach ($groups as $group) {
                $_SESSION['partners'][] = array('id' => $group['_id']['$oid'], 'name' => $group['name'], 'image' => $group['tile']);
                $_SESSION['myGroups'][] = $group['_id']['$oid'];
            }

            $activity_data[] = $_GET['id'];
            new_activity_log($_SESSION['uid'], 'removed community', $activity_data);

          }

          redirect('/edit-profile.php?alert=removed');

    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

  } elseif (($_GET['type'] == "follow") AND $_GET['id']) { // Follow user

    $responder_name = ucfirst($_SESSION['firstName']) . ' ' . ucfirst($_SESSION['lastName']);
    $link = site_url() . '/profile.php?id=' . $_SESSION['uid'];
    $notification_type = 'follow';
    $notify_users[] = $_GET['id'];

    try {
      // Follow new user
      $user_following_add = add_user_following($_GET['id']);

      // Create new notification
      $notification_new = new_notification($responder_name, $_SESSION['uid'], $_SESSION['uid'], $_SESSION['avatar'], $notification_type, '', $_GET['id'], $_SESSION['uid'], '', '', $notify_users);

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

          $email_format = formatEmail($notification_type, $user[0]['firstName'], $responder_name, $link, $user[0]['emailNotifications'], '', '');
          if($email_format) {
            $email_sent = sendEmail($user[0]['firstName'] . " " . $user[0]['lastName'], $user[0]['email'], $email_format);
          }
        }
      }

      $activity_data[] = $_GET['id'];
      new_activity_log($_SESSION['uid'], 'followed member', $activity_data);
      redirect('/profile.php?id=' . $_GET['id'] . '&action=follow&alert=success');
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

  } elseif (($_GET['type'] == "unfollow") AND $_GET['id']) { // Unfollow user

    $uid[] = $_GET['id'];

    try {

          $user_following_remove = remove_user_following($uid);

          $activity_data[] = $_GET['id'];
          new_activity_log($_SESSION['uid'], 'unfollowed member', $activity_data);

          redirect('/profile.php?id=' . $_GET['id'] . '&action=unfollow&alert=success');

    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

  } elseif (($_GET['type'] == "unblock_post") AND $_GET['id']) { // flag post if ID supplied
    try {
      $id = $_GET['id'];
      //Check user is admin or super admin id f yes
      if ($_SESSION['userRole']== 'admin'|| $_SESSION['userRole']== 'super admin') {
        $post = json_decode(json_encode(get_post($_GET['id'])), true);

        if ($post && ($post[0]['questionableContent']['flagContent'] =='blocked' || $post[0]['questionableContent']['flagContent'] =='reported')) {
          
          $update_post_flag = update_post_flag($_GET['id'],'unblocked', $_SESSION['uid']);
            if ($update_post_flag == false){
              echo 'Something went wrong';
              die();
            } else { 
              $update_post_data[] = $_GET['id'];
              $update_post_data[] = $post;
              $activity_data[] = $post[0]['text'];
              new_activity_log($_SESSION['uid'], 'unblocked post', $activity_data);

              //Send Email to content author on unblock post
              $contentUserId[] = $post[0]['userId'];//author user id
              $link = site_url() . '/view.php?id=' . $_GET['id'];
              $contentAuthor = json_decode(json_encode(get_users_by_id_raw($contentUserId)), true);

              $email_format_author = formatEmailFlagContent('unblock content',  $contentAuthor[0]['firstName']. " " . $contentAuthor[0]['lastName'], '', $link,'','','');
              
              if($email_format_author) {
                $email_sent = sendEmail($contentAuthor[0]['firstName']. " " . $contentAuthor[0]['lastName'], $contentAuthor[0]['email'], $email_format_author);
              }
              redirect('/view.php?id=' . $_GET['id'] . '&alert=success-post-updated');
            }
         }else{
               redirect('/view.php?id=' . $_GET['id']);           
         }
      }
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }      
  } elseif (($_GET['type'] == "block_post") AND $_GET['id']) { // flag post if ID supplied
    try {
        $id = $_GET['id'];
        //Check user is admin or super admin id f yes
        if ($_SESSION['userRole']== 'admin'|| $_SESSION['userRole']== 'super admin') {
          $post = json_decode(json_encode(get_post($_GET['id'])), true);

          if ($post && ($post[0]['questionableContent']['flagContent'] =='unblocked' || $post[0]['questionableContent']['flagContent'] =='reported')) {
            
            $update_post_flag = update_post_flag($_GET['id'],'blocked', $_SESSION['uid']);
              if ($update_post_flag == false){
                echo 'Something went wrong';
                die();
              } else { 
                $update_post_data[] = $_GET['id'];
                $update_post_data[] = $post;
                $activity_data[] = $post[0]['text'];
                new_activity_log($_SESSION['uid'], 'blocked post', $activity_data);

                //Send Email to content author on block post
                $contentUserId[] = $post[0]['userId'];//author user id
  
                $contentAuthor = json_decode(json_encode(get_users_by_id_raw($contentUserId)), true);
                
                $link = site_url() . '/view.php?id=' . $_GET['id'];
                $email_format_author = formatEmailFlagContent('block content',  $contentAuthor[0]['firstName']. " " . $contentAuthor[0]['lastName'], '', $link,'','','');
                
                if($email_format_author) {
                  $email_sent = sendEmail($contentAuthor[0]['firstName']. " " . $contentAuthor[0]['lastName'], $contentAuthor[0]['email'], $email_format_author);
                }
                redirect('/view.php?id=' . $_GET['id'] . '&alert=success-post-updated');
              }
          }else{
                redirect('/view.php?id=' . $_GET['id']);           
          }
        }
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }
  } elseif (($_GET['type'] == "block_comment") AND $_GET['id']) { // flag post if ID supplied
    try {
        $ids = explode('_',$_GET['id']);
        $commentId = $ids[0];
        $postId = $ids[1];
        //Check user is admin or super admin id f yes
        if ($_SESSION['userRole']== 'admin'|| $_SESSION['userRole']== 'super admin') {         
          $post = json_decode(json_encode(get_comment($ids[0])), true);           
          if ($post) {
                $post_id = $post[0]['_id']['$oid'];
                foreach ($post[0]['comments'] as $comment) {
                   if ( $comment['_id']['$oid'] == $commentId) {
                      if ($comment && ($comment['questionableContent']['flagContent'] =='unblocked' || $comment['questionableContent']['flagContent'] =='reported')) {
                        $update_post_flag = update_comment_flag($commentId,'blocked', $_SESSION['uid']);
                        if ($update_post_flag == false){
                          echo 'Something went wrong';
                          die();
                        } else { 
                          $update_post_data[] = $commentId;
                          $update_post_data[] = $post;
                          $activity_data[] = $post[0]['comments'];
                          new_activity_log($_SESSION['uid'], 'blocked comment', $activity_data);

                          //Send Email to content author on block comment
                          $contentUserId[] = $comment['userId'];//author user id
                          $link = site_url() . '/view.php?id=' . $postId;
                          $contentAuthor = json_decode(json_encode(get_users_by_id_raw($contentUserId)), true);

                          $email_format_author = formatEmailFlagContent('block content',  $contentAuthor[0]['firstName']. " " . $contentAuthor[0]['lastName'], '', $link,'','','');
                          if($email_format_author) {
                            $email_sent = sendEmail($contentAuthor[0]['firstName']. " " . $contentAuthor[0]['lastName'], $contentAuthor[0]['email'], $email_format_author);
                          }
                          redirect('/view.php?id=' . $postId . '&alert=success-post-updated');
                      }
                    }else{
                        redirect('/view.php?id=' . $postId);           
                    }
                  }
                }   
          }          
        }
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }
  } elseif (($_GET['type'] == "unblock_comment") AND $_GET['id']) { // flag post if ID supplied
    try {
        $ids = explode('_',$_GET['id']);
        $commentId = $ids[0];
        $postId = $ids[1];
        //Check user is admin or super admin id f yes
        if ($_SESSION['userRole']== 'admin'|| $_SESSION['userRole']== 'super admin') {         
          $post = json_decode(json_encode(get_comment($ids[0])), true);
          
          if ($post) {
                $post_id = $post[0]['_id']['$oid'];
                foreach ($post[0]['comments'] as $comment) {
                  if ( $comment['_id']['$oid'] == $commentId) {
                    if ($comment && ($comment['questionableContent']['flagContent'] =='blocked' || $comment['questionableContent']['flagContent'] =='reported')) {
                        $update_post_flag = update_comment_flag($commentId,'unblocked', $_SESSION['uid']);
                        if ($update_post_flag == false){
                          echo 'Something went wrong';
                          die();
                        } else { 
                          $update_post_data[] = $commentId;
                          $update_post_data[] = $post;
                          $activity_data[] = $post[0]['comments'];
                          new_activity_log($_SESSION['uid'], 'blocked comment', $activity_data);

                          //Send Email to content author on unblock comment
                          $contentUserId[] = $comment['userId'];//author user id
                          $link = site_url() . '/view.php?id=' . $postId;
                          $contentAuthor = json_decode(json_encode(get_users_by_id_raw($contentUserId)), true);

                          $email_format_author = formatEmailFlagContent('unblock content',  $contentAuthor[0]['firstName']. " " . $contentAuthor[0]['lastName'], '', $link,'','','');
                        
                          if($email_format_author) {
                            $email_sent = sendEmail($contentAuthor[0]['firstName']. " " . $contentAuthor[0]['lastName'], $contentAuthor[0]['email'], $email_format_author);
                          }
                          redirect('/view.php?id=' . $postId . '&alert=success-post-updated');
                       }
                   }else{
                      redirect('/view.php?id=' . $postId);           
                   }
                }
              }   
          }          
        }
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }
     
  } elseif (($_GET['type'] == "flag_post") AND $_GET['id']) { // flag post if ID supplied
    try {
      $posts = json_decode(json_encode(get_post( $_GET['id'])), true);   
      // echo '<pre>';
      // print_r($posts)   ;
      // exit();
      if ($posts) {  
      //    echo '<pre>';
      // print_r('chck')   ;
      // exit();
          foreach ($posts as $post) {
            if(isset($post['questionableContent']['flagContent']) && ($post['questionableContent']['flagContent']=='reported'|| $post['questionableContent']['flagContent']=='blocked')){
                echo 'Something went wrong';
                die();
            }else {
      //          echo '<pre>';
      // print_r('else')   ;
      // exit();
              $comment_edit = flag_post($_GET['id'], 'reported', $_SESSION['uid']);
             
              if ($comment_edit == false){
                // echo '<pre>';
                // print_r($comment_edit)   ;
                // exit();
                echo 'Something went wrong - post update failed!';
                die();
              } else {
                // echo '<pre>';
                // print_r('else')   ;
                // exit();
                  $activity_data[] = $_GET['id'];
                  $activity_data[] = $post;
                  $activity_data[] = $post['text'];
                  new_activity_log($_SESSION['uid'], 'flag post', $activity_data);

                  //For sending email notification to content author and admin
                  $contentUserId[] = $post['userId'];//author user id
                  $contentAuthor = json_decode(json_encode(get_users_by_id_raw($contentUserId)), true);
                                  
                  $link = site_url() . '/view.php?id=' . $_GET['id'];
                  $email_format_admin = formatEmailFlagContent('flag post admin', $contentAuthor[0]['firstName']. " " . $contentAuthor[0]['lastName'],'',$link,'','','');

                  //Get all users whoes user type is 'admin' or 'super admin'
                  $roles = array("admin","super admin");
                  $allAdmin = json_decode(json_encode(get_users_by_role($roles)), true);

                  foreach($allAdmin as $admin){
                    if($email_format_admin) {
                      $email_sent = sendEmail($admin['firstName'] . " " . $admin['lastName'], $admin['email'], $email_format_admin);
                    }
                  }

                  $email_format_author = formatEmailFlagContent('flag post author',  $contentAuthor[0]['firstName']. " " . $contentAuthor[0]['lastName'], '', $link,'','','');
                  
                  if($email_format_author) {
                    $email_sent = sendEmail($contentAuthor[0]['firstName']. " " . $contentAuthor[0]['lastName'], $contentAuthor[0]['email'], $email_format_author);

                  }

                  echo 1; 
                  die();
              }
            }
          } 
      } 
     
    }  catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

  } elseif (($_GET['type'] == "flag_comment") AND $_GET['id']) { // flag comment if ID supplied
    try{
      $ids = explode('_', $_GET['id']);
      $post = json_decode(json_encode(get_comment($ids[0])), true);  
      if ($post) {  
          foreach ($post[0]['comments'] as $comment) {
            if ( $comment['_id']['$oid'] == $ids[0]) {
              if(isset($comment['questionableContent']['flagContent']) && ($comment['questionableContent']['flagContent']=='reported'|| $comment['questionableContent']['flagContent']=='blocked')){
                  echo 'Something went wrong';
                  die();
              }else {       
                  $comment_edit = flag_comment($ids[0], 'reported', $_SESSION['uid']);
                  if ($comment_edit == false){
                    echo 'Something went wrong - comment update failed!';
                    die();
                  } else {
                    $activity_data[] = $ids[0];                    
                    $activity_data[] = $comment['text'];
                    new_activity_log($_SESSION['uid'], 'flag comment', $activity_data);

                     //For sending email notification to content author and admin
                    $contentUserId[] = $comment['userId'];//author user id
                    $contentAuthor = json_decode(json_encode(get_users_by_id_raw($contentUserId)), true);
                    $link = site_url() . '/view.php?id=' . $ids[1];
                    $email_format_admin = formatEmailFlagContent('flag post admin', $contentAuthor[0]['firstName'] . " ".$contentAuthor[0]['lastName'],'',$link,'','','');
                    
                    //Get all users whoes user type is 'admin' or 'super admin'
                    $roles = $array = array("admin","super admin");
                    $allAdmin = json_decode(json_encode(get_users_by_role($roles)), true);

                    foreach($allAdmin as $admin){
                      if($email_format_admin) {
                        $email_sent = sendEmail($admin['firstName'] . " " . $admin['lastName'], $admin['email'], $email_format_admin);
                      }
                    }
                    
                    $email_format_author = formatEmailFlagContent('flag post author', $contentAuthor[0]['firstName']. " ". $contentAuthor[0]['lastName'], '', $link,'','','');
                   
                    if($email_format_author) {
                      $email_sent = sendEmail($contentAuthor[0]['firstName'] . " " . $contentAuthor[0]['lastName'], $contentAuthor[0]['email'], $email_format_author);
                    }

                    echo 1; 
                    die();
                  }
                }
            }
          }
      }     
    }
      catch (Exception $e) {
        echo $e->getMessage();
        die();
    } 
  } else {
    echo 'something missing';
    die();
  }
} else { // Redirect user to login page if no valid session
  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));
}
