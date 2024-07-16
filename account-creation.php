<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
if (Config::SERVER != 'maintenance' || (Config::SERVER == 'maintenance' && $_GET['status'] == 'bsm')) { // Display site when not in maintenance mode or when bypassing maintenance lock with status
?>
<title>TeachersConnect Successful Account Creation</title>
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

<body id="payment" style="background-color:#f9ce28">
<?php if (Config::SERVER == 'production' || Config::SERVER != 'staging') { ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N25P2GS"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php } ?>
  <div id="nav-bar" class="nav-bar row">
    <div class="header-account-block header-logo">
      <a href="home.php"><center><img class="logo" alt="TeachersConnect" src="img/tc.png"></center></a>    
      <div id="tour-navigation">
        <div class="tour-button col25"></div>
        <div class="tour-description col50">
          <img class="congratulations" src="img/account-creation.svg">
          <h3>Almost There!</h3>
          <div class="text">
          Check your email inbox* to finish the registration process.
          </div><br/><br/>
          <div class="small-text">
          *The link expires in one hour. Use the email you used to create the account. Sender is Dave from TeachersConnect. Check your junk folder. 
          </div>
        </div>
        <div class="tour-button col25">   &nbsp;    </div>
      </div>
    </div>
  </div> 
</body>
<?php } else{  
  redirect('/maintenance.php');
  die();
}
?>
