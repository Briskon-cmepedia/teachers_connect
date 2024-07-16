<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';

if ($_SESSION['referralcode']) {
  redirect('/refer.php');
  die();
}

// Create page body
$page_body = $templates->render('view-signup-usertype',
  [
    'page' => 'signup'
  ]
);

// Create page header
$page_header = $templates->render('layout-headout',
  [
    'page' => 'signup',
    'title' => 'TC User Selection'
  ]
);

// Create page footer
$page_footer = $templates->render('layout-footout',
  [
    'page' => 'signup'
  ]
);

// Display page header
echo $page_header;

// Display page body
echo $page_body;

// Display page footer
echo $page_footer;
