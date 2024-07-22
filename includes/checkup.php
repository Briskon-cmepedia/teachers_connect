<?php

// Block unwanted IPs
$deny = ['84.53.81.154','157.45.200.253','85.203.44.27','85.203.44.135','85.*','84.*','99.203.81.227','185.107.47.215','154.125.240.223','194.143.136.66'];
if (in_array($_SERVER['REMOTE_ADDR'] , $deny)) {
    die();
}

// Check member status
if ($_SESSION['uid']) {

  // Get open groups data
  try {
    $open_groups = json_decode(json_encode(get_open_groups()), true);
  } catch (Exception $e) {
    echo $e->getMessage();
    die();
  }

  // Get subscribed groups
  try {
    $user_groups = json_decode(json_encode(get_groups_by_user($_SESSION['uid'])), true);
  } catch (Exception $e) {
    echo $e->getMessage();
    die();
  }

  $myGroups = [];
  $openGroups = [];
  $accessibleGroups = [];
  $accessibleGroupNames = [];
  foreach ($user_groups as $user_group) {
    $myGroups[] = $user_group['_id']['$oid'];
    $accessibleGroups[] = $user_group['_id']['$oid'];
    $accessibleGroupNames[$user_group['_id']['$oid']]['id'] = $user_group['_id']['$oid'];
    $accessibleGroupNames[$user_group['_id']['$oid']]['name'] = $user_group['name'];
  }
  foreach ($open_groups as $open_group) {
    $openGroups[] = $open_group['_id']['$oid'];
    $accessibleGroups[] = $open_group['_id']['$oid'];
    $accessibleGroupNames[$open_group['_id']['$oid']]['id'] = $open_group['_id']['$oid'];
    $accessibleGroupNames[$open_group['_id']['$oid']]['name'] = $open_group['name'];
  }
  $_SESSION['accessibleGroupNames'] = $accessibleGroupNames;

  $user_trust = json_decode(json_encode(read_user_trust($_SESSION['uid'])), true);
  if ($user_trust) { // If valid user, check and set trust status locally

    if ($user_trust[0]['trusted'] == TRUE) {
      $db_trusted = 'yes';
    } else {
      $db_trusted = 'no';
    }

    if ($user_trust[0]['access'] > 0) {
      $_SESSION['access'] = 'yes';
    } else {
      $_SESSION['access'] = 'no';
    }

    // $_SESSION['access'] = 'yes';

    // Update permissions if trust level has changed while logged in
    if ($db_trusted !== $_SESSION['trusted']) {
      if ($user_trust[0]['trusted'] == TRUE) {
        $_SESSION['trusted'] = 'yes';
      } else {
        $_SESSION['trusted'] = 'no';
      }
    }

    $current_page = basename($_SERVER['PHP_SELF']);

    // if ($_SESSION['access'] != 'yes' AND ($current_page != 'profile.php' OR $current_page != 'edit-profile.php' OR $current_page != 'edit-information.php' OR $current_page != 'upload.php' OR $current_page != 'edit-notifications.php' OR $current_page != 'auth.php' OR $current_page != 'payment-options.php' OR $current_page != 'payment-process.php' OR $current_page != 'success.php')) {

    if ($_SESSION['access'] !== 'yes') {

      if ( $current_page !== 'reset.php' AND $current_page !== 'reg.php' AND $current_page !== 'refer.php' AND $current_page !== 'invitation.php' AND $current_page !== 'image.php' AND $current_page !== 'profile.php' AND $current_page !== 'edit-profile.php' AND $current_page !== 'edit-information.php' AND $current_page !== 'upload.php' AND $current_page !== 'edit-notifications.php' AND $current_page !== 'auth.php' AND $current_page !== 'payment-options.php' AND $current_page !== 'payment-process.php' AND $current_page !== 'success.php' ) {

        redirect('/payment-options.php');
        die();

      }

    }

  } else { // Not valid user, redirect to auth
    session_destroy();
    redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));
    die();
  }
}


