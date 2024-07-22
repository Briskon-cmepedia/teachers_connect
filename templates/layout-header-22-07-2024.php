<html lang="en">
  <head>
    <title><?=$this->e($title)?></title>
    <meta name="viewport" content="width=device-width, initial-scale = 1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-title" content="TeachersConnect">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <?php if (Config::SERVER == 'staging') { ?>
    <meta name="robots" content="noindex" />
    <?php } ?>
    <meta property="og:image" content="<?php site_url();?>/img/promo-teachersconnect.png">
    <meta property="og:image:type" content="image/png">
    <meta property="og:image:width" content="800">
    <meta property="og:image:height" content="419">
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php site_url();?>/"/>
    <meta property="og:title" content="TeachersConnect - An Uncompromisingly Teacher-centric Online Community of Teachers." />
    <meta property="og:description" content="TeachersConnect is a safe space to collaborate for teachers. Share ideas, ask questions, and collaborate with teachers like you." />
    <link rel="apple-touch-icon" href="img/icon-tc-app.png">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>   
    <script type="text/javascript" src="js/jquery.modal.min.js" async></script>
    <link rel="stylesheet" type="text/css" href="css/jquery.modal.min.css">

   
    <?php if ($page == 'signup') { ?>
      <link rel="stylesheet" type="text/css" href="css/selectize.custom.css">
    <?php } ?>
    <?php if (Config::SERVER == 'production') { ?>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-69936049-13"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-69936049-13');
      gtag('config', 'AW-934849342');
    </script>
    
  <?php } ?>
  
  <?php if (Config::SERVER != 'staging' || Config::SERVER == 'production') {
  echo "   
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-N25P2GS');</script>
<!-- End Google Tag Manager -->
  ";
  } ?>
  </head>
  <body class="<?=$page?> <?=$block?>"> 
  <script>
    $(document).on('click', '.confirm-validation-popup', function() { 
        var email = $(this).attr('data-id');  
        $.ajax({
                url: '<?php site_url();?>/email-resend.php',
                type: 'GET',
                //dataType: 'json',
                data: {
                    'email': email                 
                },
                error: function() {
                    callback();
                },
                success: function(result) {
                  if(result==2){
                      $("#confirm-validation-popup").modal('show');
                  }else{
                      $("#confirm-validation-error-popup").modal('show');
                  }
                }
            });
      });   
    </script> 
    <?php if (Config::SERVER == 'staging') {
      echo "<h1 class='server_env'>STAGING</h1>";
    } else {
      echo " 
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src='https://www.googletagmanager.com/ns.html?id=GTM-N25P2GS'
height='0' width='0' style='display:none;visibility:hidden'></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->  
      ";
    } ?>
    
    <?php if ($block == 'login-fail') { ?>
        <div class="login-alert-fail">
          <img src="img/icon-alert.svg"> Invalid email or password.  <a class="button-secondary" href="<?=site_url() . '/reset.php'?>">Need help?</a>
        </div>
    <?php } ?>
