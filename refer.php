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
$user_time = time();
$user_type = "other";
$render = 'view-signup-ref';
$refid = $_GET['ref'];

if ($refid AND !$_SESSION['referralcode']) {
  $_SESSION['referralcode'] = $refid;
}

if (!$refid AND $_SESSION['referralcode']) {
  $refid = $_SESSION['referralcode'];
}


if (!$_POST) { // Display registration form

  // Create page header
  $page_header = $templates->render('layout-headout',
    [
      'page' => 'signup',
      'title' => 'TeachersConnect - Create Your Account'
    ]
  );

  // Create page body
  $page_body = $templates->render($render,
    [
      'page' => 'signup',
      'refid' => $refid
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

} elseif (
  (($_POST['userType'] == 'other') AND $_POST['firstName'] AND $_POST['lastName'] AND filter_var($_POST['user'], FILTER_VALIDATE_EMAIL) AND $_POST['pass'] AND $_POST['termsAgreement'])
) { // Create new user when required fields are submitted
   
    $firstNameCheck = validate_user_name($_POST['firstName']);
    $lastNameCheck = validate_user_name($_POST['lastName']);
    if(!$firstNameCheck || !$lastNameCheck){
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
        'formSubmission' => 'fail_username',
        'firstName' => $_POST['firstName'],
        'firstNameCheck' => $firstNameCheck,
        'lastNameCheck' => $lastNameCheck,
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
        'termsAgreement' => $_POST['termsAgreement'],
        'refid' => $_POST['userRef']
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
    }else{
      // CREATE FORM VALIDATION LOGIC HERE
      $email_check = find_existing_user(strtolower($_POST['user']));
      if (count($email_check)>0) {
        $user_detail_array = json_decode(json_encode($email_check[0]), true);
        if($user_detail_array['userStatus']=='verified'){          
          $user_info['status'] = 'duplicate';
          $user_info['email']  = $_POST['user'];      
          setcookie('user_checking', json_encode($user_info), time()+900);
          redirect('/auth.php');
        }elseif($user_detail_array['userStatus']=='unverified'){
          $user_info['status'] = 'duplicate-unverified';
          $user_info['email']  = $_POST['user'];  
          setcookie('user_checking', json_encode($user_info), time()+900);
          redirect('/auth.php');
        }elseif($user_detail_array['userStatus']=='inactive'){
          $user_info['status'] = 'duplicate-inactive';
          $user_info['email']  = $_POST['user'];
          setcookie('user_checking', json_encode($user_info), time()+900);
          redirect('/auth.php');
        } 
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

        $ref_id_db = get_user_referrer($user_ip)[0];
        $user_ref = $ref_id_db ?? $_POST['userRef'];
        
        $verification_token = md5($_POST['user']).rand(10,9999);
        
        $verification_url = Config::MAIN_URL."account-verify.php?key=".$_POST['user']."&token=".$verification_token;  
      
        $user_new = new_user($_POST['firstName'], $_POST['lastName'], strtolower($_POST['user']), $_POST['pass'], $_POST['teachLocationName'], $_POST['teachLocationCity'], $_POST['teachLocationState'], $_POST['teachGrades'], $_POST['teachSubjects'], $_POST['teachStart'], $_POST['teachEnd'], $_POST['teachLicenseLocation'], $_POST['teachLicenseComplete'], $geoip['country'], $user_ref, $verification_token);
        
        if ($user_new == false){

          echo 'Something went wrong';
        } else {
          $email_format = formatEmailVerification($verification_url, $_POST['firstName']);

          if($email_format) {
            $email_sent = sendEmail($_POST['firstName'] . " " . $_POST['lastName'], strtolower($_POST['user']), $email_format);
            update_request_uri($user_ip, strtolower($_POST['user']));
          }

          $activity_data[] = $user_ip;
          $activity_data[] = $user_agent;
          $activity_data[] = $email;
          new_activity_log($user_new, 'email sent for verification', $activity_data);

          echo $templates->render('layout-headout', [
          'page' => 'login',
          'title' => 'TeachersConnect Successful Account Creation'
            ]
          );

          // Display reset view
          echo $templates->render('account-creation', [
            'page' => 'account-creation'
            ]
          );   
        }
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
      'termsAgreement' => $_POST['termsAgreement'],
      'refid' => $_POST['userRef']
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
