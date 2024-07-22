<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
if ($sessions->sessionCheck()) { // Display view if user has valid session
  
  $user_email = $_SESSION['email'];
  if(!$_SESSION['notificationTimestamp']){
    $user = json_decode(json_encode(find_user($user_email)), TRUE);
    $sessions->newSession($user);
  }
  // redirect to a post/page if user used a link with a post/page
  $uri = get_request_uri($user_email);
  $redirect_uri = $uri[0] ?? '/tc_app_new/tour/topics.php';
?>

<html lang="en">
<head>
  <title>TeachersConnect Successful Payment</title>
  <meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=1.0, user-scalable=no" />
  <meta name="apple-mobile-web-app-title" content="TeachersConnect">
  <link rel="apple-touch-icon" href="img/icon-tc-app.png">
  <link rel="manifest" href="manifest.json">
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="css/normalize.css">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700|Patrick+Hand" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="css/styles.css?04042018">
  <?php if (Config::SERVER == 'production' || Config::SERVER != 'staging') { ?>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-69936049-13"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', 'UA-69936049-13');
  </script>
  <!-- Event snippet for Website sale conversion page -->
  <script>
    gtag('event', 'conversion', { 'send_to': 'AW-934849342/GnLqCNuknsMBEL7W4r0D', 'transaction_id': '' });
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
<body id="payment" class="pay" style="background-color:#f9ce28;">
<?php if (Config::SERVER == 'production' || Config::SERVER != 'staging') { ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N25P2GS"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php } ?>
<div id="">
  <div id="nav-bar" class="nav-bar row">
    <div class="header-block header-logo">
      <a href="home.php"><img class="logo" alt="TeachersConnect" src="img/tclogo-small.png"></a>
    </div>
    <div class="header-block right top-bar-button-group">
      <a class="top-bar-button" href="profile.php">
        <img class="top-bar-profile" src="img/icon-menu-profile.png" alt="icon menu profile">
      </a>
    </div>
  </div>
</div>
<div id="tour-navigation">
  <div class="tour-button col25">
  </div>
  <div class="tour-description col50">
    <img class="congratulations" src="img/icon-congratulations.png">
    <h3>Congratulations!</h3>
    <div class="text">
    You're steps away from the most relentlessly positive online community of teachers that exists.
    </div>
  </div>
  <div class="tour-button col25">
    &nbsp;
  </div>
</div>
<div id="tour-options">
  <a style='border-color: #123b45;border: none;background:#123b45;color:#fff;text-decoration:none;;font-size:1.0em;padding:8px 15px;border-radius:4px;' href="<?=$redirect_uri?>">Continue</a>
</div>
<div id="tour-footer">
  <div class="col25">
    &nbsp;
  </div>
  <div class="col50">
    &nbsp;
  </div>
  <div class="col25">
    &nbsp;
  </div>
</div>
<div id="site-footer" class="site-footer col100">
  <ul class="footer-menu">
    <li><a target="_blank" rel="noopener" href="https://www.teachersconnect.com/terms-of-use/">Terms of Use</a></li>
    <li><a target="_blank" rel="noopener" href="https://www.teachersconnect.com/privacy-policy/">Privacy Policy</a></li>
    <li><a target="_blank" rel="noopener" href="https://www.teachersconnect.com/2017/08/10/community-guidelines/">Community Guidelines</a></li>
    <li><a rel="noopener" href="/tour/menu.php">Guided Tour</a></li>
    <li><a target="_blank" rel="noopener" href="https://www.teachersconnect.com/support/">Support</a></li>
    <li><a rel="noopener" href="<?=site_url()?>/auth.php?logout=1">Logout</a></li>
  </ul>
  <div class="footer-note">
    © Great Teachers Inc <?php echo date("Y"); ?> - All rights reserved
  </div>
</div>
</body>
</html>

<?php

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}

?>
