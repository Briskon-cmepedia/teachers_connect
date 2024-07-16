<?php
// System Setup
require 'includes/startup.php';

// echo "hii";
// exit();

// Variable Setup
$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

if (Config::SERVER != 'maintenance' || (Config::SERVER == 'maintenance' && $_GET['status'] == 'bsm')) { // Display site when not in maintenance mode or when bypassing maintenance lock with status

    if ($_GET['logout']) {

        new_activity_log($_SESSION['uid'], 'logged out');

        // Destroy session and return user to login screen
        session_destroy();
        redirect('/auth.php');
        die();

    }
    elseif (!$_POST) { // Display login form if no credentials posted

        $status_data = json_decode($_COOKIE['user_checking'], true);
        $status = $status_data['status'];
        $email = $status_data['email'];
       
        if ($_GET['status'] == 'ready') { // Display appropriate page title for Google Analytics
            $title_status = 'TC Signup Successful';
        }
        elseif ($status== 'duplicate') {
            $title_status = 'TC Signup Unsuccessful Duplicate Email';
        }elseif ($status == 'duplicate-unverified') {
            $verification_token = md5($email).rand(10,9999);        
            $verification_url = Config::MAIN_URL."account-verify.php?key=".$email."&token=".$verification_token;  
            $title_status = 'TC Signup Unsuccessful Duplicate Unverified Email';
        } elseif ($status == 'duplicate-inactive') {
            $title_status = 'TC Signup Unsuccessful Inactive Email';
        }      
        elseif ($_GET['status'] == 'reset') {
            $title_status = 'TC Password Reset Successful';
        }
        else {
            $title_status = 'TC Signin';
        }

        if(isset($_COOKIE['user_checking'])) {
            $user_info['status'] = '';
            $user_info['email']  = ''; 
            setcookie('user_checking', json_encode($user_info), time()-900);
        }

        // Display HTML header
        echo $templates->render('layout-headout', [
                'page' => 'login',
                'title' => $title_status,
            ]
        );

        // Display login view
        echo $templates->render('view-login',
            [
                'page' => 'login',
                'email' => $email,
                'status' => $status,
                'verification_url'=>$verification_url
            ]);

    }
    else { // Check and verify credentials if posted
       
        $user_email = strtolower($_POST['user']);
        $pass = $_POST['pass'];

        // Connect to database
        try {
            $user = json_decode(json_encode(find_user($user_email)), TRUE);
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }

        $user_pass = $user[0]['password'];
        if (password_verify($pass, $user_pass)) { // Set session and redirect user if credentials verified

            $sessions->newSession($user);
            // If first time visitor or haven't logged in since April 1 2019, redirect to guided tour
            $user_lastLogin = $sessions->getUserLastLogin();
            if (!$user_lastLogin || $user_lastLogin < 1554076800) {
                require_once 'includes/detect-mobile.php';
                $detect = new Mobile_Detect;
                if ($detect->isMobile()) {
                    // Don't redirect
                }
                else {                    
                    // redirect to a post/page if user used a link with a post/page
                
                    $uri = get_request_uri($user_email);
                   // $redirect_uri = $uri[0] ?? '/tc_app/tour/topics.php';
                    // redirect($redirect_uri);
                    die();
                }
            }

           
            if ($_GET['location']) {
                // echo "hme";
                // print_r($_GET['location']);
                //  exit();
              
                redirect($_GET['location']);
            }
            else {
                // echo "hme";
                // exit();
                redirect('/home.php');
            }
            die();

        }
        else { // Display error message and login form if credentials don't verify

            // $user_outcome = 'fail';
            // try {
            //   $db->exec("INSERT INTO attempts (user_time, user_ip, user_agent, user_email, user_outcome) VALUES ('$user_time', '$user_ip', '$user_agent', '$user_email', '$user_outcome');");
            // } catch (Exception $e) {
            //   echo $e->getMessage();
            //   die();
            // }

            $activity_data[] = $sessions->getUserIP();
            $activity_data[] = $sessions->getUserAgent();
            $activity_data[] = $user_email;
            new_activity_log(0, 'failed login', $activity_data);

            // Display HTML header
            echo $templates->render('layout-headout', [
                    'page' => 'login',
                    'title' => 'TC Signin Unsuccessful',
                    'block' => 'login-fail',
                ]
            );

            // Display login view
            echo $templates->render('view-login', ['page' => 'login-fail']);

        }

    }

}
else {

    session_destroy();
    redirect('/maintenance.php');
    die();

}
