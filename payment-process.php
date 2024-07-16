<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

  if ($_GET['status'] == 'courtesy') { // Update user account if courtesy membership

    $payment_record = [];
    $payment_record['time'] = new MongoDB\BSON\UTCDateTime(time()*1000);
    $payment_record['referer'] = $_SERVER['HTTP_REFERER'];
    $payment_record['browser'] = $_SERVER['HTTP_USER_AGENT'];
    $payment_record['ip'] = get_user_ip_address();
    $payment_record['status'] = 'courtesy';

    // Update account with payment reference
    try {
      $payment = json_decode(json_encode(write_access_payment($_SESSION['uid'], $payment_record)), true);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    // Update account access level
    try {
      $access = json_decode(json_encode(write_access_level($_SESSION['uid'], 99)), true);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    //redirect('/success.php');
    redirect('/account-signup.php');
    die();

  } elseif ($_GET['id']) { // Update user account if successful Payment

    $payment_record = [];
    $payment_record['time'] = new MongoDB\BSON\UTCDateTime(time()*1000);
    $payment_record['referer'] = $_SERVER['HTTP_REFERER'];
    $payment_record['browser'] = $_SERVER['HTTP_USER_AGENT'];
    $payment_record['ip'] = get_user_ip_address();
    $payment_record['checkoutId'] = $_GET['id'];

    // Update account with payment reference
    try {
      $payment = json_decode(json_encode(write_access_payment($_SESSION['uid'], $payment_record)), true);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    // Update account access level
    try {
      $access = json_decode(json_encode(write_access_level($_SESSION['uid'], 1)), true);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    if ($payment AND $access) {

      redirect('/success.php');
      die();

    }

  }

  redirect('/payment-options.php');
  die();

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}

?>
