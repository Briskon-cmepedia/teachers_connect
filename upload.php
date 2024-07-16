<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

  if (is_uploaded_file($_FILES['profilepic']['tmp_name'])) { // Update user profile image if supplied

    include('s3.php');

    $profilePic_update = update_userImage($_SESSION['uid'], $image_display[0][0]);

    if ($profilePic_update == false){

      echo 'Something went wrong';

    } else {

      $_SESSION['avatar'] = $image_display[0];
      redirect('/edit-information.php');

    }

  } else {

    echo 'something missing';

  }

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}
