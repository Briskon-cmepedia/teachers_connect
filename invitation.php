<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
use GeoIp2\Database\Reader;

// Variable Setup
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
$reader = new Reader('data/geoip2-city.mmdb');
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$user_ip = get_user_ip_address();
$user_time = time();
$user_type = 'invite';
$render = 'view-signup-invite';
$refid = $_GET['ref'];

// echo $refid;

if ($refid AND !$_SESSION['referralcode']) {
  $_SESSION['referralcode'] = $refid;
}

if (!$refid AND $_SESSION['referralcode']) {
  $refid = $_SESSION['referralcode'];
}

if (isValid($refid)) { // Check referral code format is correct

  // Get group that referred user
  try {
    $partner = json_decode(json_encode(get_group($refid)), true);
  } catch (Exception $e) {
    echo $e->getMessage();
    die();
  }

  if (!$partner) {

    redirect('/reg.php?usertype=other');
    die();

  } else {

    $partner_logo = $partner[0]['logo'];
    $partner_name = $partner[0]['name'];
    $partner_privacy = $partner[0]['privacy'];
    $partner_description = $partner[0]['description'];
    $_SESSION['freechoice'] = $partner[0]['paid'];

  }

} else {

  redirect('/reg.php?usertype=other');
  die();

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
    $page_body = $templates->render($render,
      [
        'page' => 'signup',
        'refid' => $refid,
        'partner_logo' => $partner_logo,
        'partner_name' => $partner_name,
        'partner_description' => $partner_description
      ]
    );

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

    die();

} elseif ( $_POST['userType'] == 'invite' AND $_POST['firstName'] AND $_POST['lastName'] AND filter_var($_POST['user'], FILTER_VALIDATE_EMAIL) AND $_POST['pass'] AND $_POST['termsAgreement'] )  { // Create new user when required fields are submitted

  // CREATE FORM VALIDATION LOGIC HERE

  // Search for existing email record (duplicate account warning)
  try {
    $email_check = find_user(strtolower($_POST['user']));
  } catch (Exception $e) {
    echo $e->getMessage();
    die();
  }

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

    // Create a new user
    try {
      $user_new = new_user($_POST['firstName'], $_POST['lastName'], strtolower($_POST['user']), $_POST['pass'], $_POST['teachLocationName'], $_POST['teachLocationCity'], $_POST['teachLocationState'], $_POST['teachGrades'], $_POST['teachSubjects'], $_POST['teachStart'], $_POST['teachEnd'], $_POST['teachLicenseLocation'], $_POST['teachLicenseComplete'], $geoip['country'], $_POST['userRef']);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    if ($user_new == false){

      echo 'Something went wrong';

    } else {

      // Find group id and users by name
      try {
        $user_group = json_decode(json_encode(find_group($_POST['teachLicenseLocation'])), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      // if ($user_group AND $user_group[0]['privacy'] == 'public') { // If public group exists, add user to it

      if ($user_group) { // If TPP group exists, add user to it

        $gid = $user_group[0]['_id']['$oid'];
        // $user_array = $user_group[0]['users'];
        // $group_tile = $user_group[0]['tile'];
        //
        // if ($user_array == NULL) {
        //   $user_array = [];
        // }
        //
        // if (in_array($user_new, $user_array)) { // If user already in group, do nothing
        //
        // } else { // If user not in group, add them
        //
        //   // Add user to group array
        //   $user_array[] = $user_new;
        //
        //   // Add user to group
        //   try {
        //     $user_group_add = update_group($gid, $user_array);
        //   } catch (Exception $e) {
        //     echo $e->getMessage();
        //     die();
        //   }
        //
        // }

        try {
          $user_group_add = json_decode(json_encode(add_group_user($gid, $user_new)), true);
        } catch (Exception $e) {
          echo $e->getMessage();
          die();
        }

      }

      // Get invite partner group details
      try {
        $invite_group = json_decode(json_encode(get_group($_POST['userRef'])), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      if ($invite_group[0]['privacy'] == 'public') { // If invite partner group is public, add user to it

        // Join partner group that invited user
        try {
          $add_invited_user = json_decode(json_encode(add_group_user($_POST['userRef'], $user_new)), true);
        } catch (Exception $e) {
          echo $e->getMessage();
          die();
        }

      }

      // Create new notification timestamp for user
      try {
        $new_timestamp = json_decode(json_encode(update_notifications_timestamp($user_new)), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      // Make Dave Meyers account follow new user
      try {
        $dave_follow = add_user_to_user_following('59aab4da55229441a41d8a0b', $user_new);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      $responder_name = 'Dave Meyers';
      $notification_type = 'follow';
      $notify_users[] = $user_new;

      // Create new notification
      try {
        $notification_new = new_notification($responder_name, '59aab4da55229441a41d8a0b', '59aab4da55229441a41d8a0b', '09db58b8-9e00-4e48-bbee-b01348f7f51a', $notification_type, '', $user_new, '59aab4da55229441a41d8a0b', '', '', $notify_users);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      $_SESSION['uid'] = $user_new;
      $_SESSION['firstName'] = $first_name;
      $_SESSION['lastName'] = $last_name;
      $_SESSION['user'] = $user_ip;
      $_SESSION['email'] = $email;

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
      'refid' => $refid,
      'partner_logo' => $partner_logo,
      'partner_name' => $partner_name,
      'partner_description' => $partner_description,
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
