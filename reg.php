<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
require 'includes/email.php';
use GeoIp2\Database\Reader;

// Variable Setup
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$sendgrid = new \SendGrid(Config::SENDGRID_KEY);
$reader = new Reader('data/geoip2-city.mmdb');
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$user_ip = get_user_ip_address();
$user_time = time();
$user_type = $_GET['usertype'];
if ($user_type == 'student') {
  $render = 'view-signup-student';
} elseif ($user_type == 'other') {
  $render = 'view-signup-other';
} else {
  $render = 'view-signup-teacher';
}

if (!$_POST) { // Display registration form

  // Create page header
  $page_header = $templates->render('layout-headout',
    [
      'page' => 'signup',
      'title' => 'TC User Registration ' . ucfirst($user_type)
    ]
  );

  // Create page body
  $page_body = $templates->render($render, ['page' => 'signup']);

  // Create page footer
  $page_footer = $templates->render('layout-footout',
    [
      'page' => 'signup',
      'userType' => $user_type
    ]
  );

  // Display page header
  echo $page_header;

  // Display page body
  echo $page_body;

  // Display page footer
  echo $page_footer;

} elseif (
  (($_POST['userType'] == 'teacher') AND $_POST['firstName'] AND $_POST['lastName'] AND filter_var($_POST['user'], FILTER_VALIDATE_EMAIL) AND $_POST['pass'] AND $_POST['teachLocationName'] AND $_POST['teachGrades'] AND $_POST['teachSubjects'] AND $_POST['teachStart'] AND $_POST['teachEnd'] AND $_POST['teachLicenseLocation'] AND $_POST['teachLicenseComplete'] AND $_POST['termsAgreement'])
  OR
  (($_POST['userType'] == 'student') AND $_POST['firstName'] AND $_POST['lastName'] AND filter_var($_POST['user'], FILTER_VALIDATE_EMAIL) AND $_POST['pass'] AND $_POST['teachLicenseLocation'] AND $_POST['teachLicenseComplete'] AND $_POST['termsAgreement'])
  OR
  (($_POST['userType'] == 'other') AND $_POST['firstName'] AND $_POST['lastName'] AND filter_var($_POST['user'], FILTER_VALIDATE_EMAIL) AND $_POST['pass'] AND $_POST['termsAgreement'])
) { // Create new user when required fields are submitted

  // CREATE FORM VALIDATION LOGIC HERE

  $email_check = find_user(strtolower($_POST['user']));

  if (count($email_check)>0) {
    $user_info['status'] = 'duplicate';
    $user_info['email']  = $_POST['user'];      
    setcookie('user_checking', json_encode($user_info), time()+900);
    redirect('/auth.php');

  } else {

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

    $user_new = new_user($_POST['firstName'], $_POST['lastName'], strtolower($_POST['user']), $_POST['pass'], $_POST['teachLocationName'], $_POST['teachLocationCity'], $_POST['teachLocationState'], $_POST['teachGrades'], $_POST['teachSubjects'], $_POST['teachStart'], $_POST['teachEnd'], $_POST['teachLicenseLocation'], $_POST['teachLicenseComplete'], $geoip['country']);

    if ($user_new == false){

      echo 'Something went wrong';

    } else {

      // Find group id and users by name
      $user_group = json_decode(json_encode(find_group($_POST['teachLicenseLocation'])), true);

      // if ($user_group AND $user_group[0]['privacy'] == 'public') { // If public group exists, add user to it

      if ($user_group) { // If TPP group exists, add user to it

        $gid = $user_group[0]['_id']['$oid'];
        $user_array = $user_group[0]['users'];
        $group_tile = $user_group[0]['tile'];

        if ($user_array == NULL) {
          $user_array = [];
        }

        if (in_array($user_new, $user_array)) { // If user already in group, do nothing

        } else { // If user not in group, add them

          // Add user to group array
          $user_array[] = $user_new;

          // Add user to group
          $user_group_add = update_group($gid, $user_array);

          // Add group to tiles array
          $_SESSION['partners'][$gid] = $group_tile;

        }

      }

      // Create new notification timestamp for user
      $new_timestamp = json_decode(json_encode(update_notifications_timestamp($user_new)), true);

      // Make Dave Meyers account follow new user
      $dave_follow = add_user_to_user_following('59aab4da55229441a41d8a0b', $user_new);

      $responder_name = 'Dave Meyers';
      $notification_type = 'follow';
      $notify_users[] = $user_new;

      // Create new notification
      $notification_new = new_notification($responder_name, '59aab4da55229441a41d8a0b', '59aab4da55229441a41d8a0b', '09db58b8-9e00-4e48-bbee-b01348f7f51a', $notification_type, '', $user_new, '59aab4da55229441a41d8a0b', '', '', $notify_users);

      // Register member to Sendgrid new marketing campaign service (also add to New Members list)
      $first_name = $_POST['firstName'];
      $last_name = $_POST['lastName'];
      $email = strtolower($_POST['user']);
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

      $_SESSION['uid'] = $user_new;
      $_SESSION['firstName'] = $first_name;
      $_SESSION['lastName'] = $last_name;
      $_SESSION['user'] = $user_ip;
      $_SESSION['email'] = $email;

      // // Setup notifications status
      // if ($notification_timestamp == NULL) {
      //
      //   $new_timestamp = json_decode(json_encode(update_notifications_timestamp()), true);
      //
      //   if ($new_timestamp != false) {
      //
      //     $notification_timestamp = $new_timestamp['$date']['$numberLong'];
      //
      //   } else {
      //
      //     $notification_timestamp = "nothing";
      //
      //   }
      // }
      //
      // $_SESSION['notificationTimestamp'] = $notification_timestamp;

      update_last_login_timestamp();
      update_ip_address();
      update_total_logins();

      $activity_data[] = $user_ip;
      $activity_data[] = $user_agent;
      $activity_data[] = $email;
      new_activity_log($_SESSION['uid'], 'logged in', $activity_data);

      // redirect('/auth.php?status=ready');
      redirect('/payment-options.php');

    }

  }

} else {  // Display registration form with any errors

  // Create page header
  $page_header = $templates->render('layout-headout', [
      'page' => 'signup',
      'title' => 'TC User Registration ' . ucfirst($user_type) . ' Errors'
    ]
  );

  // Create page body
  $page_body = $templates->render($render,
    [
      'page' => 'signup',
      'formSubmission' => 'fail',
      'firstName' => $_POST['firstName'],
      'lastName' => $_POST['lastName'],
      'user' => $_POST['user'],
      'pass' => $_POST['pass'],
      'teachLocationName' => $_POST['teachLocationName'],
      'teachLocationCity' => $_POST['teachLocationCity'],
      'teachLocationState' => $_POST['teachLocationState'],
      'teachLocationCountry' => $_POST['teachLocationCountry'],
      'teachGrades' => $_POST['teachGrades'],
      'teachSubjects' => $_POST['teachSubjects'],
      'teachStart' => $_POST['teachStart'],
      'teachEnd' => $_POST['teachEnd'],
      'teachLicenseLocation' => $_POST['teachLicenseLocation'],
      'teachLicenseComplete' => $_POST['teachLicenseComplete'],
      'termsAgreement' => $_POST['termsAgreement']
    ]
  );

  // Create page footer
  $page_footer = $templates->render('layout-footout',
    [
      'page' => 'signup',
      'userType' => $_POST['userType']
    ]
  );

  // Display page header
  echo $page_header;

  // Display page body
  echo $page_body;

  // Display page footer
  echo $page_footer;

}
