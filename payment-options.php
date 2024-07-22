<?php
// System Setup
require 'includes/startup.php';
require 'includes/checkup.php';
require 'vendor/autoload.php';




if ($sessions->sessionCheck()) { // Display view if user has valid session


  $stripe_secret_key = "sk_test_51Pcm7QRul9A8ZSsKPZCYlQCdzXqbg1Rownd3FRJ1cwuUxMz3uWaKbJ8NyRkR93cW2SAIB20QGozIvaplplZzQUqg00Z6q4n8le";
\Stripe\Stripe::setApiKey($stripe_secret_key);
$checkout_session = \Stripe\Checkout\Session::create([
  "mode" => "payment",
  "success_url" => "http://localhost/tc_app_new/payment-process.php?id={CHECKOUT_SESSION_ID}", 
  "cancel_url" => "http://localhost/tc_app_new/payment-options.php",
  "line_items" => [
    [
      "quantity" => 1,
      "price_data" => [
        "currency" => "usd",
        "unit_amount" => 24900,
        "product_data" => [
          "name" => "Lifetime membership"
        ]
      ]
    ]
  ]
]);



$checkout_session2 = \Stripe\Checkout\Session::create([
  "mode" => "payment",
  "success_url" => "http://localhost/tc_app_new/payment-process.php?id={CHECKOUT_SESSION_ID}", 
  "cancel_url" => "http://localhost/tc_app_new/payment-options.php",
  "line_items" => [
    [
      "quantity" => 1,
      "price_data" => [
        "currency" => "usd",
        "unit_amount" => 4900,
        "product_data" => [
          "name" => "Annual membership"
        ]
      ]
    ]
  ]
]);

// echo json_encode($checkout_session2);

http_response_code(303);
// header("Location:". $checkout_session->url);
  // // Determine courtesy status
  // $courtesy = 0;
  //
  // try {
  //   $affiliates_subscribed = json_decode(json_encode(get_groups_by_user($_SESSION['uid'])), true);
  // } catch (Exception $e) {
  //   echo $e->getMessage();
  //   die();
  // }
  //
  // foreach ($affiliates_subscribed as $group) {
  //   if ($group['paid'] == 1) {
  //     $courtesy = 1;
  //   }
  // }
  //
  // if ($_SESSION['freechoice'] == 1) {
  //   $courtesy = 1;
  // }

  // Provide all users with access to free membership
  $courtesy = 1;

?>

<html lang="en">
<head>
  <title>TeachersConnect Payment Options</title>
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
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-N25P2GS');</script>
<!-- End Google Tag Manager -->
  <?php } ?>
</head>
<body id="payment" class="pay">
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
    <h3>You matter.</h3>
    <div class="text">
      Teachers are the single most important factor in students’ academic success. Join a diverse family of kindred teaching spirits who lift you up, challenge you, and keep you growing.
      <br><br>
      Choose your plan today: Join for free or make a payment. Payments ensure that TeachersConnect is accessible to pre-service teachers, paraprofessionals, and teachers working multiple jobs.
    </div>
    <h4>Join today. Reach one more student tomorrow.</h4>
    <!-- <h3>You matter.<br>You're worth it.</h3>
    <div class="text">
      You’re the single most important factor in your students’ academic success. Join a diverse family of kindred teaching spirits who lift you up, challenge you, and keep you growing.
      <br><br>
      Choose your plan today, and reach one more student tomorrow.
    </div> -->
  </div>
  <div class="tour-button col25">
    &nbsp;
  </div>
</div>
<div id="tour-footer">
  <div class="col25">
    &nbsp;
  </div>
  <div class="col50">
    <?php if ($courtesy == 1) { ?>
      <a href="#" id="checkout-button-sku_courtesy" role="link"><img class="membership-courtesy" src="img/payment-option-4.png"></a>
      <script>
      (function() {
        var checkoutButton = document.getElementById('checkout-button-sku_courtesy');
        checkoutButton.addEventListener('click', function () {
          window.location = "<?=site_url()?>/payment-process.php?status=courtesy";
        });
      })();
      </script>
    <?php } ?>
  </div>
  <div class="col25">
    &nbsp;
  </div>
</div>
<div id="tour-options">
  <a href="#" onclick="redirectToCheckout2()" role="link"><img src="img/payment-option-1.png"></a>
  <!-- <a href="#" id="checkout-button-plan_GNYmWve5khT1Gq" role="link"><img src="img/payment-option-2.png"></a> -->
  <a href="#" onclick="redirectToCheckout()" role="link"><img src="img/payment-option-2.png"></a><br>
  <div id="error-message"></div>
</div>
<div id="tour-footer">
</div>
<!-- Load Stripe.js on your website. -->
<script src="https://js.stripe.com/v3"></script>
<script>
// (function() {
//   var stripe = Stripe('pk_test_51Pcm7QRul9A8ZSsK9lmU82DKt5qLnXSI2DXsButz01ntb2QQRaOkkreqdxiHGoQImuFwrhsemjOm4VP6BMhiIxki00oFhO2rd0');
//   var checkoutButton = document.getElementById('checkout-button-plan_GNYmR2iHCgPhrT');
//   checkoutButton.addEventListener('click', function () {
//     stripe.redirectToCheckout({
//       items: [{plan: 'plan_GNYmR2iHCgPhrT', quantity: 1}],
//       successUrl: '<?=site_url()?>/payment-process.php?id={CHECKOUT_SESSION_ID}',
//       cancelUrl: '<?=site_url()?>/payment-options.php',
//     })
//     .then(function (result) {
//       if (result.error) {
//         var displayError = document.getElementById('error-message');
//         displayError.textContent = result.error.message;
//       }
//     });
//   });
// })();
// (function() {
//   var stripe = Stripe('pk_test_51Pcm7QRul9A8ZSsK9lmU82DKt5qLnXSI2DXsButz01ntb2QQRaOkkreqdxiHGoQImuFwrhsemjOm4VP6BMhiIxki00oFhO2rd0');
//   var checkoutButton = document.getElementById('checkout-button-plan_GNYmWve5khT1Gq');
//   checkoutButton.addEventListener('click', function () {
//     stripe.redirectToCheckout({
//       items: [{plan: 'plan_GNYmWve5khT1Gq', quantity: 1}],
//       successUrl: '<?=site_url()?>/payment-process.php?id={CHECKOUT_SESSION_ID}',
//       cancelUrl: '<?=site_url()?>/payment-options.php',
//     })
//     .then(function (result) {
//       if (result.error) {
//         var displayError = document.getElementById('error-message');
//         displayError.textContent = result.error.message;
//       }
//     });
//   });
// })();
// (function() {
//   var stripe = Stripe('pk_test_51Pcm7QRul9A8ZSsK9lmU82DKt5qLnXSI2DXsButz01ntb2QQRaOkkreqdxiHGoQImuFwrhsemjOm4VP6BMhiIxki00oFhO2rd0');
//   var checkoutButton = document.getElementById('checkout-button-sku_GNYfyMxpAH6Djy');
//   checkoutButton.addEventListener('click', function () {
//     stripe.redirectToCheckout({
//       items: [{sku: 'sku_GNYfyMxpAH6Djy', quantity: 1}],
//       successUrl: '<?=site_url()?>/payment-process.php?id={CHECKOUT_SESSION_ID}',
//       cancelUrl: '<?=site_url()?>/payment-options.php',
//     })
//     .then(function (result) {
//       if (result.error) {
//         var displayError = document.getElementById('error-message');
//         displayError.textContent = result.error.message;
//       }
//     });
//   });
// })();

function redirectToCheckout() {
            var checkoutUrl = "<?php echo $checkout_session->url; ?>";
            window.location.href = checkoutUrl;
        }

        function redirectToCheckout2() {
            var checkoutUrl2 = "<?php echo $checkout_session2->url; ?>";
            window.location.href = checkoutUrl2;
        }
</script>

<div id="site-footer" class="site-footer col100">
  <ul class="footer-menu">
    <li><a target="_blank" rel="noopener" href="https://staging7.briskon.com/tc-marketing/terms-of-use/">Terms of Use</a></li>
    <li><a target="_blank" rel="noopener" href="https://staging7.briskon.com/tc-marketing/privacy-policy/">Privacy Policy</a></li>
    <li><a target="_blank" rel="noopener" href="https://staging7.briskon.com/tc-marketing/2017/08/10/community-guidelines/">Community Guidelines</a></li>
    <li><a target="_blank" rel="noopener" href="https://staging7.briskon.com/tc-marketing/support/">Support</a></li>
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
