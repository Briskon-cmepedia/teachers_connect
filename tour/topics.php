<?php
// System Setup
// require_once __DIR__ . '/../vendor/autoload.php';
require '../includes/startup.php';
require '../includes/checkup.php';
// require '../vendor/autoload.php';
// echo "hii";
// exit();
// require_once __DIR__ . '/../includes/startup.php';
// require_once __DIR__ . '/../includes/checkup.php';
// require_once __DIR__ . 'vendor/autoload.php';


// echo "hii";
// exit();
if ($sessions->sessionCheck()) { // Display view if user has valid session

  if($_SERVER['REQUEST_METHOD'] == 'POST') { // If posted interests, update profile and redirect to tour

    if ($_POST['interests']) {

      $reaction = update_topics_followed($_SESSION['uid'], $_POST['interests']);

    }

    redirect('/home.php');
    die();

  }

  $topics_sorted = [];

  // Connect to database
  try {
    $topics = json_decode(json_encode(get_all_topics()), true);
  } catch (Exception $e) {
    echo $e->getMessage();
    die();
  }

  foreach ($topics as $topic) {
    foreach ($topic['topics'] as $term) {
      $topics_sorted[] = $term;
    }
  }
  sort($topics_sorted, SORT_NATURAL);

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
  <?php if (Config::SERVER == 'production' || Config::SERVER != 'staging') {  ?>
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
<body id="tour" class="topics">
<?php if (Config::SERVER == 'production' || Config::SERVER != 'staging') { ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N25P2GS"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
<?php } ?>
  <form method="post">
<div id="tour-header" class="center">
    <img class="tour-logo" src="../img/tour-logo.png">
</div>
<div id="tour-navigation">
  <div class="tour-button col25">
  </div>
  <div class="tour-description col50">
    <h3>What are your Interests?</h3>
    <div class="text">
      We can connect you to the topics that interest you.  Select your interests from the tags below and click Next to continue.
    </div>
  </div>
  <div class="tour-button col25">
    <input class="button right" type="submit" value="Next">
  </div>
</div>
<div id="tour-view">
  <div class="topic-list">
    <?php foreach ($topics_sorted as $term) { ?>
          <input type="checkbox" id="<?=$term?>" name="interests[]" value="<?=$term?>" /><label class="topic-term" for="<?=$term?>"><?=ucwords($term)?></label>
    <?php } ?>
  </div>
</div>
<div id="tour-footer" class="clear">
  <div class="col25">
    &nbsp;
  </div>
  <div class="col50">
    <img src="../img/tour-extra.png"> You can alter these choices at any time later.
  </div>
  <div class="col25">
  </div>
</div>
</form>
</body>
</html>

<?php

} else { // Redirect user to login page if no valid session

  redirect('/auth.php?location=' . urlencode($_SERVER['REQUEST_URI']));

}

?>
