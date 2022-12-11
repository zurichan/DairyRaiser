<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../configs/database.php';
require_once '../includes/classes.php';

$date = date('Y-m-d h:i:s');

$api = new MyAPI($main_conn);


if (isset($_GET['verification_key']) && $_GET['user_email']) {

    $vkey = filter_input(INPUT_GET, 'verification_key', FILTER_SANITIZE_EMAIL);
    $email = filter_input(INPUT_GET, 'user_email', FILTER_SANITIZE_EMAIL);

    $user_info = $api->Read('user', 'set', 'email', "'$email'");

    $err = 0;

    (empty($vkey)) ? $err++ : NULL;
    (empty($email)) ? $err++ : NULL;
    (empty($user_info)) ? $err++ : NULL;

    if ($err == 0) {

        if ($user_info[0]->verificationStatus != 1 && $user_info[0]->ActivationCode == $vkey && $user_info[0]->date_stamp >= $date) {
            $api->Update('user', 'email', [
                '1' => ['verificationStatus', 1],
                '2' => ['date_stamp', 'NULL'],
                '3' => ['Modified_at', "'$date'"],
                '4' => ['verify_tracker', 0]
            ], "$email");

            $_SESSION['users'] = $user_info;

            $_SESSION['index-message'] = array(
                "title" => 'Your Account is Verified!',
                "body" =>  '',
                "type" => 'success'
            );

            header('Location: ../home.php');
            exit();
        } else {
            die('Something went wrong.');
        }
    } else {
        die('Something went wrong.');
    }
} else {
    die('Something went wrong.');
}