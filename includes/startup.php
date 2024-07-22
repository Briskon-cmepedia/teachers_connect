<?php

require __DIR__ . '/config.php';
require __DIR__ . '/sessions.php';

$sessions = new Sessions();
require __DIR__ . '/../vendor/autoload.php';
//require Config::AUTOLOAD_PATH;  // DO autoload


require __DIR__ . '/searchengine.php';
require __DIR__ . '/database.php';
require __DIR__ . '/plates.php';

function site_url() {
    // $protocol = Config::SECURE ? 'https://' : 'http://';
    $protocol = 'http://';
    return $protocol . $_SERVER['HTTP_HOST'].'/tc_app';
}

function url_build($page) {
    return site_url() . $page;
}

function redirect($page) {
    // echo "<pre>";
    // print_r($page);
    // echo "<br><br>";
    // print_r(site_url());
    // echo "<br><br>";
    // print_r(url_build($page));
    // exit();
    header('Location: ' . url_build($page));

    
}

// Get user ip address
function get_user_ip_address() {
  $userIP = isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
  return $userIP;
}

// Get user ip address
function validate_user_name($userName) {
    try {
        $config = json_decode(json_encode(get_config()), true);
    } catch (Exception $e) {
        echo $e->getMessage();
        die();
    }
    
    // Check for blocked words in content
    foreach ($config[0]['wordMatchBlocked'] as $word) {
        if (stripos($userName, $word) !== false) {
            return false;
        }
    }

    if(preg_match('/^[a-zA-Z][0-9a-zA-Z- ]{1,25}$/', $userName)) {
        return true;
      }
      return false;
  }
