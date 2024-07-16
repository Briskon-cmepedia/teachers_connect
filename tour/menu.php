<?php
// System Setup
require '../includes/startup.php';
require '../includes/checkup.php';

if ($sessions->sessionCheck()) { // Display view if user has valid session

?>

<html lang="en">
<head>
  <title>TeachersConnect Guided Tour - Main Menu</title>
  <meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="apple-mobile-web-app-title" content="TeachersConnect">
  <link rel="apple-touch-icon" href="../img/icon-tc-app.png">
  <link rel="manifest" href="../manifest.json">
  <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="../css/normalize.css">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700|Patrick+Hand" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="../css/styles.css?04042018">
  <?php if (Config::SERVER == 'production' || Config::SERVER != 'staging') { ?>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-69936049-13"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-69936049-13');
  </script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-N25P2GS');</script>
<!-- End Google Tag Manager -->
  <?php } ?>

</head>
<body id="tour">
<?php if (Config::SERVER == 'production' || Config::SERVER != 'staging') { ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N25P2GS"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php } ?>
<div id="tour-header">
  <div class="col50">
    <img class="tour-logo" src="../img/tour-logo.png"> Guided Tour
  </div>
  <div class="col50">
    <div class="tour-exit right">
      <a id="tour-exit-menu" href="../home.php"><img src="../img/tour-exit.png"> Exit</a>
    </div>
  </div>
</div>
<div id="tour-navigation">
  <div class="tour-button col25">
    &nbsp;
  </div>
  <div class="tour-description col50">
    <h3>Main Menu</h3>
    <div class="text">
      The main menu bar allows you to navigate around the site no matter where you are.
    </div>
  </div>
  <div class="tour-button col25">
    <a href="home.php">
      <div class="button right">
        Next
      </div>
    </a>
  </div>
</div>
<div id="tour-view">
  <img src="../img/tour-mainmenu.png">
</div>
<div id="tour-footer">
  <div class="col50">

  </div>
</div>
</body>
</html>

<?php

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}

?>
