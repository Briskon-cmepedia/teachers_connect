<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
require 'vendor/autoload.php';

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
    $stripe_secret_key = "sk_test_51Pcm7QRul9A8ZSsKPZCYlQCdzXqbg1Rownd3FRJ1cwuUxMz3uWaKbJ8NyRkR93cW2SAIB20QGozIvaplplZzQUqg00Z6q4n8le";
    \Stripe\Stripe::setApiKey($stripe_secret_key);
    $session = \Stripe\Checkout\Session::retrieve($_GET['id']);

    // Access session details
    $amount = $session->amount_total;
    $currency = $session->currency;

    // You can access other details like customer information, payment status, etc.
    $customerId = $session->customer;
    $paymentStatus = $session->payment_status;

    if($amount = 4900){
      $Membership = 'Annual membership';
    }else if($amount = 24900){
      $Membership = 'Lifetime membership';
    }else{
      $Membership = 'No membership';
    }

    $payment_record = [];
    $payment_record['time'] = new MongoDB\BSON\UTCDateTime(time()*1000);
    $payment_record['referer'] = $_SERVER['HTTP_REFERER'];
    $payment_record['browser'] = $_SERVER['HTTP_USER_AGENT'];
    $payment_record['ip'] = get_user_ip_address();
    $payment_record['checkoutId'] = $_GET['id'];
    $payment_record['amount'] = $amount;
    $payment_record['currency'] = $currency;
    $payment_record['customerId'] = $_SESSION['uid'];
    $payment_record['paymentStatus'] = $paymentStatus;
    $payment_record['Membership'] = $Membership;

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
