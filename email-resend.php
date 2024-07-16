<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
require 'includes/email.php';
  // Variable Setup
$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$user_ip = get_user_ip_address();
$user_agent = $_SERVER['HTTP_USER_AGENT'];

if (Config::SERVER != 'maintenance' || (Config::SERVER == 'maintenance' && $_GET['status'] == 'bsm')) { // Display site when not in maintenance mode or when bypassing maintenance lock with status
  if ($_GET['email']) {  
        $email_check = find_existing_user(strtolower($_GET['email']));

        if (!count($email_check)>0) {
          echo 1;
          die();
        } else {   
          $user_detail_array = json_decode(json_encode($email_check[0]), true);
          $user_new = $user_detail_array['_id']['$oid'];
          if($user_detail_array['userStatus']=='unverified'){
            $verification_token = md5($_GET['email']).rand(10,9999);          
            $verification_url = Config::MAIN_URL."account-verify.php?key=".$_GET['email']."&token=".$verification_token;  
                    
            $user_new_status = update_resend_link($verification_token, $user_new);
            if ($user_new_status == false){
              echo 1;
              die();

            } else {              
              $email_format = formatEmailVerification($verification_url, $user_detail_array['firstName']);
              if($email_format) {
                $email_sent = sendEmail($user_detail_array['firstName'] . " " . $user_detail_array['lastName'], strtolower($_GET['email']), $email_format);
              }

              $activity_data[] = $user_ip;
              $activity_data[] = $user_agent;
              $activity_data[] = $_GET['email'];
              new_activity_log($user_new, 'email resent for verification', $activity_data);

              echo 2;
              die();
            }
          }  
        }
    } else {
      echo 'error';
      die();
    }
}else {
  redirect('/maintenance.php');
  die();
}