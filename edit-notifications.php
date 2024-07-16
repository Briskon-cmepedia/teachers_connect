<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
require 'includes/email.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  // Variable Setup
  $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

  if (Config::SERVER != 'maintenance' OR (Config::SERVER == 'maintenance' AND $_SESSION['bsm'])) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

	// Connect to database
	try {
	  $user = json_decode(json_encode(get_user($_SESSION['uid'])), true);
	} catch (Exception $e) {
	  echo $e->getMessage();
	  die();
	}

	//if form is not submitted display existing settings
	if(!$_POST) {

  	// check notification settings
	  if ($user) {

	    // Create page body
	    $page_body = $templates->render('edit-notifications',
	    	[
	    	'user' => $user[0]['firstName'],
	    	'comment' => $user[0]['emailNotifications']['comment'],
	    	'answer' => $user[0]['emailNotifications']['answer'],
	    	'interact' => $user[0]['emailNotifications']['interact'],
        'follow' => $user[0]['emailNotifications']['follow']
	    	]
		);

	  } else {  // No user found

	    // Create page body
	    $page_body = $templates->render('error',
	      [
	        'page' => 'user'
	      ]
	    );
		}

	$title_status = "TC Profile Edit Notifications - " . $user[0]['firstName'] . ' ' . $user[0]['lastName'];

    // Create page header
    $page_header = $templates->render('layout-header',
        [
          'page' => 'profile',
          'title' => $title_status,
          'action' => 'notification-settings'
        ]
      );

     // Create page footer
     $page_footer = $templates->render('layout-footer',
        [
          'page' => 'profile',
          'title' => $title_status,
          'action' => 'notification-settings'
        ]
      );

      echo $page_header;

      echo $page_body;

      echo $page_footer;

	}

    //if form has been submitted change notification settings
    if ($_POST) {

      $email_settings = [];

    	//Update email_settings based on checkboxes
    	if($_POST['comment'] == 1) {
    		$email_settings['comment'] = 1;
    	} else {
    		$email_settings['comment'] = 0;
    	}
      if($_POST['answer'] == 1) {
    		$email_settings['answer'] = 1;
    	} else {
    		$email_settings['answer'] = 0;
    	}
      if($_POST['interact'] == 1) {
    		$email_settings['interact'] = 1;
    	} else {
    		$email_settings['interact'] = 0;
    	}
      if($_POST['follow'] == 1) {
    		$email_settings['follow'] = 1;
    	} else {
    		$email_settings['follow'] = 0;
    	}

    //update notifications settings
    $update = json_decode(json_encode(update_email_notifications($email_settings)), true);

    if ($update == false){

        echo 'Something went wrong';

      } else {
      	//echo "settings successfully updated!";
       redirect('/profile.php?id=' . $_SESSION['uid'] . '&alert=success');

      }

    }

    } else {

    session_destroy();
    redirect('/maintenance.php');
    die();

  }

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}

?>
