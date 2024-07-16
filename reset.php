<?php
// System Setup
require 'includes/startup.php';
require 'includes/email.php';

// Variable Setup
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
// $db = new PDO('sqlite:login.sqlite3');
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$user_ip = get_user_ip_address();;
$user_time = time();
$status = $_GET['status'];
$id = $_GET['id'];
$pass1 = $_POST['pass1'];
$pass2 = $_POST['pass2'];
$pid = $_POST['id'];

if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_GET['status'] == 'bsm')) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

if ($pass1 && $pass2) { // Reset account password

  if ($pass1 != '' && $pass2 != '' && $pid != '' && ($pass1 == $pass2)) { // Reset password

    if (isValid($pid)) {

      // Connect to database
      try {
        $resetlog = json_decode(json_encode(get_reset_log($pid)), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

    }

    // Connect to database
    try {
      $resetpass = json_decode(json_encode(update_user_password($resetlog[0]['user_id'], $pass1)), true);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    if ($resetpass) {  // Password reset - redirect to login

      // Connect to database
      try {
        $resetlogupdate = json_decode(json_encode(update_reset_log($resetlog[0]['_id']['$oid'], 'completed')), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      redirect('/auth.php?status=reset');
      die();

    } else {  // Display reset error

      // Display HTML header
      echo $templates->render('layout-headout', [
        'page' => 'login',
        'title' => 'TC Reset Password Error'
        ]
      );

    // Display reset view
      echo $templates->render('view-reset', [
        'page' => 'reset-error-pass'
        ]
      );

    }

  } else { // Display password error

    // Display HTML header
    echo $templates->render('layout-headout', [
      'page' => 'login',
      'title' => 'TC Reset Password Error'
      ]
    );

  // Display reset view
    echo $templates->render('view-reset', [
      'page' => 'reset-error-pass'
      ]
    );

  }

} elseif ($id) { // Display password reset form

    $valid_request = false;

    if (isValid($id)) {

      // Connect to database
      try {
        $resetlog = json_decode(json_encode(get_reset_log($id)), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      // Check validity window for request (one hour)
      $log_reset_window = ($resetlog[0]['time']['$date']/1000)+(1 * 60 * 60);
      if ($resetlog[0]['status'] == 'started' && time() <= $log_reset_window) {
        $valid_request = true;
      }

    }

    if ($valid_request) { // Display form

      // Display HTML header
      echo $templates->render('layout-headout', [
        'page' => 'login',
        'title' => 'TC Reset Password Processing'
        ]
      );

      // Display reset view
      echo $templates->render('view-reset', [
        'page' => 'reset-pass',
        'id' => $id
        ]
      );

    } else { // Display error

      // Display HTML header
      echo $templates->render('layout-headout', [
        'page' => 'login',
        'title' => 'TC Reset Password Processing'
        ]
      );

      // Display reset view
      echo $templates->render('view-reset', [
        'page' => 'reset-error'
        ]
      );

    }

  } elseif ($status == 'ready') { // Display reset confirmation

    // Display HTML header
    echo $templates->render('layout-headout', [
      'page' => 'login',
      'title' => 'TC Reset Password Started'
      ]
    );

  // Display reset view
    echo $templates->render('view-reset', [
      'page' => 'reset-start'
      ]
    );

  } elseif (!$_POST) { // Display password reset form if nothing posted

    // Display HTML header
    echo $templates->render('layout-headout', [
      'page' => 'login',
      'title' => 'TC Reset Password'
      ]
    );

  // Display reset view
    echo $templates->render('view-reset', [
      'page' => 'reset-request',
      'email' => $_GET['email']
      ]
    );

  } else { // Check account and start reset process

    $user_email = strtolower($_POST['user']);

    // Connect to database
    try {
      $user = json_decode(json_encode(find_user($user_email)), true);
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    $user_id = $user[0]['_id']['$oid'];
    $user_firstName = $user[0]['firstName'];
    $user_lastName = $user[0]['lastName'];

    // Connect to database
    try {
      if(isset($user_id) && $user_id!=''){
          $lastresetlog = json_decode(json_encode(get_last_reset_log($user_id)), true);
      }
    } catch (Exception $e) {
      echo $e->getMessage();
      die();
    }

    // Check validity window for request (one hour)
    $log_reset_window = ($lastresetlog[0]['time']['$date']/1000)+(1 * 60 * 60);
    if ($lastresetlog[0]['status'] == 'started' && time() <= $log_reset_window) {
      $valid_request = false;
    } else {
      $valid_request = true;
    }

    if ($valid_request && $user_id!='') { // Send email to reset password

      try {
        $reset_start = json_decode(json_encode(new_reset_log($user_id, $user_email, $user_ip, $user_agent)), true);
      } catch (Exception $e) {
        echo $e->getMessage();
        die();
      }

      if ($reset_start && $user_id!='') {

        $email_format = formatEmailReset($reset_start, $user_firstName);

        if($email_format) {

          $email_sent = sendEmail($user_firstName . " " . $user_lastName, $user_email, $email_format);

        }

      }

    }

    redirect('/reset.php?status=ready');
    die();

  }

} else {

  session_destroy();
  redirect('/maintenance.php');
  die();

}
