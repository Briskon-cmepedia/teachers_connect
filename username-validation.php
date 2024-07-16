 <?php
require 'includes/startup.php';
require 'includes/checkup.php';
require 'includes/email.php';
 $_GET  = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
 $userName = $_GET['username'];
 try {
        $config = json_decode(json_encode(get_config()), true);
    } catch (Exception $e) {
        echo $e->getMessage();
        die();
    }
    
    // Check for blocked words in content
    foreach ($config[0]['wordMatchBlocked'] as $word) {
        if (stripos($userName, $word) !== false) {
            echo 2;
            die();
        }
    }

    if(preg_match('/^[a-zA-Z][0-9a-zA-Z- ]{1,25}$/', $userName)) {
      echo 1;
      die();
    }
    echo 2;
     die();

 ?>     