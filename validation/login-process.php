<?php

session_start();
date_default_timezone_set('Asia/Manila');
require_once '../configs/database.php';
require_once '../includes/classes.php';

$api = new MyAPI($main_conn);
$date = date('Y-m-d h:i:s');

if (isset($_POST['login']) && !isset($_SESSION['users'])) {

    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    (isset($_POST['remember_me'])) ? $remember_me = true : $remember_me = false;
    $reg_Error = [];
    $reg_ErrorMsg = [];

    $ipaddress = $api->IP_address();
    $all_users = $api->Read('user', 'all');
    $admin = $api->Read('admin', 'all');
    $search_login_attempts = $api->Search('login_attempts', 'ip_addr', $ipaddress, true);

    if ($search_login_attempts == 0) {

        $api->Create('login_attempts', [
            'key1' => ['ip_addr', "'$ipaddress'"],
            'key2' => ['login_attempt_tracker', 0],
            'key3' => ['date_attempt', 'NULL']
        ]);
    }

    $login_attempts = $api->Read('login_attempts', 'set', 'ip_addr', $ipaddress);

    if ($login_attempts[0]->login_attempt_tracker == 5) {

        if ($login_attempts[0]->date_attempt == NULL) {

            $addTime_1H = date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s')) + (60 * 60));
            $api->Update('login_attempts', 'login_id', [
                'key1' => ['date_attempt', "'$addTime_1H'"]
            ], $login_attempts[0]->login_id);
        }

        $login_attempts = $api->Read('login_attempts', 'set', 'ip_addr', $ipaddress);
        $current_date = date('Y-m-d h:i:s');

        if ($current_date >= $login_attempts[0]->date_attempt) {

            $api->Update('login_attempts', 'login_id', [
                'key1' => ['login_attempt_tracker', 0],
                'key2' => ['date_attempt', 'NULL']
            ], $login_attempts[0]->login_id);
        }
    }

    $login_attempts = $api->Read('login_attempts', 'set', 'ip_addr', $ipaddress);

    if ($login_attempts[0]->login_attempt_tracker < 5) {

        // validation
        foreach ($admin as $administrator) {

            if ($administrator->admin_unique == $email && $administrator->password == $password) {
                $api->Update('login_attempts', 'login_id', [
                    'key1' => ['login_attempt_tracker', 0],
                    'key2' => ['date_attempt', 'NULL']
                ], $login_attempts[0]->login_id);

                $_SESSION['admins'] = array($administrator);
                $_SESSION['TIME'] = time();
                $api->Delete('login_attempts', 'login_id', $login_attempts[0]->login_id);

                header('Location: ../Admin/dashboard.php');
                exit();
                break;
            }
        }

        $error_count = 0;

        foreach ($all_users as $user) {

            if ($user->email == $email && $user->password == $password) {

                if ($user->verificationStatus == 1) {

                    $api->Update('login_attempts', 'login_id', [
                        'key1' => ['login_attempt_tracker', 0],
                        'key2' => ['date_attempt', 'NULL']
                    ], $login_attempts[0]->login_id);

                    $api->Update('user', 'email', [
                        '1' => ['ActivationCode' , 'NULL'],
                        '2' => ['date_stamp', 'NULL']
                    ], "$email");

                    if ($remember_me = true) {
                        
                        $api->Create('remember_me', [
                            '1' => ['ip_address', "'$ipaddress'"],
                            '3' => ['email', "'$email'"]
                        ]);
                    }
                    $_SESSION['users'] = array($user);
                    $_SESSION['TIME'] = time();
                    $api->Delete('login_attempts', 'login_id', $login_attempts[0]->login_id);

                    $_SESSION['index-message'] = array(
                        "title" => 'Welcome Back, ' .  $_SESSION['users'][0]->firstname,
                        "body" => '',
                        "type" => 'success'
                    );

                    header('Location: '.$_SERVER['HTTP_REFERER']);
                    break;
                } else if ($user->verificationStatus == 0) {

                    unset($_SESSION['unverified_email']);
                    header('Location: ../entry/verifying_email.php');
                    $_SESSION['unverified_email'] = $email;
                    break;
                }
            } else {

                $error_count++;
            }
        }
        if ($error_count > 0) {

            $login_attempts = $api->Read('login_attempts', 'set', 'ip_addr', $ipaddress);

            if ($login_attempts[0]->login_attempt_tracker != 5) {

                $login_attempt_count = $login_attempts[0]->login_attempt_tracker + 1;
                $api->Update('login_attempts', 'login_id', [
                    'key1' => ['login_attempt_tracker', $login_attempt_count]
                ], $login_attempts[0]->login_id);
            }
            $_SESSION['login-message'] = array(
                "title" => 'Invalid Credentials',
                "body" => '',
                "type" => 'error'
            );
            header('Location: ../entry/login.php');

        }
    } else {

        $_SESSION['login-message'] = array(
            "title" => 'Too many login attempts. Please wait an Hour.',
            "body" => '',
            "type" => 'warning'
        );

        header('Location: ../entry/login.php');

    }
}

if (isset($_GET['google_signin']) && isset($_GET['givenname']) && isset($_GET['familyname']) && isset($_GET['email']) && !isset($_SESSION['users'])) {

    $given_name = filter_input(INPUT_GET, 'givenname', FILTER_SANITIZE_SPECIAL_CHARS);
    $family_name = filter_input(INPUT_GET, 'familyname', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_EMAIL);

    $fetch_user = $api->Read('user', 'set', 'email', "$email");

    if (!empty($fetch_user)) {

        if ($fetch_user[0]->verificationStatus == 1) {
            $_SESSION['users'] = $fetch_user;
            $_SESSION['index-message'] = array(
                "title" => 'Welcome Back, ' .  $_SESSION['users'][0]->firstname,
                "body" => '',
                "type" => 'success'
            );

            header("Location: ".$_SERVER['HTTP_REFERER']);
        } else {
            $_SESSION['unverified_email'] = $email;
            header("Location: ../entry/verifying_email.php");
        }
    } else {
        $_SESSION['google_not_bind'] = $email;
        $_SESSION['google_given_name'] = $given_name;
        $_SESSION['google_family_name'] = $family_name;
        header('Location: ../entry/login.php');
    }
}