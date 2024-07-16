<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
require 'includes/email.php';
use GeoIp2\Database\Reader;

// Variable Setup
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$reader = new Reader('data/geoip2-city.mmdb');
$sendgrid = new \SendGrid(Config::SENDGRID_KEY);
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$user_ip = get_user_ip_address();
$render = 'view-account-signup';
$token = $_GET['token'];
$email = $_GET['key'];
if (Config::SERVER != 'maintenance' || (Config::SERVER == 'maintenance' && $_GET['status'] == 'bsm')) { // Display site when not in maintenance mode or when bypassing maintenance lock with status
    
    if ($email AND $token) {     
      $user_detail = find_unverified_user(strtolower($email));
      if (filter_var($user_ip, FILTER_VALIDATE_IP)) {
          try {
            $geoip_record = $reader->city($user_ip);
            if ($geoip_record->city->name) {
              $geoip['city'] = $geoip_record->city->name;
              $geoip['country'] = $geoip_record->country->name;
              $geoip['state'] = $geoip_record->mostSpecificSubdivision->isoCode;
            }
          } catch (Exception $e) {

          }
      }

      $user_detail_array = json_decode(json_encode($user_detail[0]), true);
      $sent_date= $user_detail_array['emailSentAt']['$date'];
      $user_new = $user_detail_array['_id']['$oid'];

      $log_verify_window = ($sent_date/1000)+(Config::verification_link_expire * 60 * 60); 
      if ($token == $user_detail_array['emailToken'] && time() <= $log_verify_window) {
        $user_status = update_account_verification($user_new);
 
        if ($user_status == false){
          echo 'Something went wrong';
        } else {      
   
          $new_timestamp = json_decode(json_encode($user_status), true);

          // Make Dave Meyers account follow new user
          $dave_follow = add_user_to_user_following('59aab4da55229441a41d8a0b', $user_new);

          $responder_name = 'Dave Meyers';
          $notification_type = 'follow';
          $notify_users[] = $user_new;

          // // Create new notification
          $notification_new = new_notification($responder_name, '59aab4da55229441a41d8a0b', '59aab4da55229441a41d8a0b', '09db58b8-9e00-4e48-bbee-b01348f7f51a', $notification_type, '', $user_new, '59aab4da55229441a41d8a0b', '', '', $notify_users);

          // Register member to Sendgrid new marketing campaign service (also add to New Members list)
          $first_name = $user_detail_array['firstName'];
          $last_name =  $user_detail_array['lastName']; 
          $email = strtolower($user_detail_array['email']);
          $curl = curl_init();

          curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.sendgrid.com/v3/marketing/contacts",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => "{\"list_ids\":[\"61417f87-4265-4bb4-a441-d87b26912a3a\"],\"contacts\":[{\"email\":\"$email\",\"first_name\":\"$first_name\",\"last_name\":\"$last_name\"}]}",
            CURLOPT_HTTPHEADER => array(
              "authorization: Bearer ".Config::SENDGRID_KEY,
              "content-type: application/json"
            ),
          ));

          $response = curl_exec($curl);
          $err = curl_error($curl);
          $header_data= curl_getinfo($curl);
          curl_close($curl);
          $http_code = substr($header_data['http_code'], 0, 1);

          if ($err OR $http_code !== '2') {
            $activity_data = [];
            $activity_data[] = '61417f87-4265-4bb4-a441-d87b26912a3a';
            $activity_data[] = $email;
            $activity_data[] = $first_name;
            $activity_data[] = $last_name;
            try {
              new_activity_log($user_new, 'failed to register on Sendgrid', $activity_data);
            } catch (Exception $e) {
              echo $e->getMessage();
              die();
            }
          }

          // Register member to Sendgrid old email api service
          $request_body = json_decode('[
            {
              "email": "'.$email.'",
              "first_name": "'.$first_name.'",
              "last_name": "'.$last_name.'"
            }
          ]');
          $response2 = $sendgrid->client->contactdb()->recipients()->post($request_body);
          $json_response2 = json_decode($response2->body(), true);
          $http_code2 = substr($response2->statusCode(), 0, 1);
          if ($http_code2 == '2') {
            // Add member to Full Member List
            if ($json_response2['persisted_recipients'][0]) {
              $list_id = "7215572";
              $recipient_id = $json_response2['persisted_recipients'][0];
              $response3 = $sendgrid->client->contactdb()->lists()->_($list_id)->recipients()->_($recipient_id)->post();
              $json_response3 = json_decode($response3->body(), true);
              $http_code3 = substr($response3->statusCode(), 0, 1);
              if ($http_code3 !== '2') {
                $activity_data = [];
                $activity_data[] = '7215572';
                $activity_data[] = $json_response3['errors'][0]['message'];
                $activity_data[] = $email;
                $activity_data[] = $first_name;
                $activity_data[] = $last_name;
                $activity_data[] = $json_response3;
                try {
                  new_activity_log($user_new, 'failed to register on Sendgrid', $activity_data);
                } catch (Exception $e) {
                  echo $e->getMessage();
                  die();
                }
              }
            }
          } else {
            $activity_data = [];
            $activity_data[] = $email;
            $activity_data[] = $json_response2['errors'][0]['message'];
            $activity_data[] = $first_name;
            $activity_data[] = $last_name;
            $activity_data[] = $json_response2;
            try {
              new_activity_log($user_new, 'failed to register on Sendgrid', $activity_data);
            } catch (Exception $e) {
              echo $e->getMessage();
              die();
            }
          }

          try {
              $user = json_decode(json_encode(find_user($email)), TRUE);
          } catch (Exception $e) {
              echo $e->getMessage();
              die();
          }
          $sessions->newSession($user);

          // // Setup notifications status
          if ($notification_timestamp == NULL) {
          
            $new_timestamp = json_decode(json_encode(update_notifications_timestamp()), true);
          
            if ($new_timestamp != false) {
          
              $notification_timestamp = $new_timestamp['$date']['$numberLong'];
          
            } else {
          
              $notification_timestamp = "nothing";
          
            }
          }      
          $_SESSION['notificationTimestamp'] = $notification_timestamp;

          $activity_data[] = $user_ip;
          $activity_data[] = $user_agent;
          $activity_data[] = $email;
          new_activity_log($user_new, 'email verified', $activity_data);
          
          update_last_login_timestamp();
          update_ip_address();
          update_total_logins();
          redirect('/payment-process.php?status=courtesy');      
        }
      }else{       
        setcookie('user_email', $email, time()+900);        
        redirect('/verification-error.php');
      }
    }else{ 
      redirect('/verification-error.php');
    }
 } else{  
  redirect('/maintenance.php');
  die();
}
?>
