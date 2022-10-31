<?php

session_start();
date_default_timezone_set('Asia/Manila');

if (isset($_SESSION['users'])) {

    require_once '../configs/database.php';
    require_once '../includes/classes.php';

    $api = new MyAPI($main_conn);

    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);

    if (isset($_POST['update_info'])) {

        $fname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_NUMBER_INT);
        $phoneNumberConvertString = strval($phone_number);
        $phoneNumberLength = strlen($phoneNumberConvertString);
        $date = date('Y-m-d h:i:s');
        $error_count = 0;

        (strlen($fname) >= 50) ? $error_count++ : null;
        (strlen($lname) >= 50) ? $error_count++ : null;
        (empty($fname)) ? $error_count++ : null;
        (empty($lname)) ? $error_count++ : null;
        (empty($phone_number)) ? $error_count++ : null;
        (!preg_match("/^[a-zA-Z-' ]*$/", $fname)) ? $error_count++ : null;
        (!preg_match("/^[a-zA-Z-' ]*$/", $lname)) ? $error_count++ : null;
        ($phoneNumberLength > 10) ? $error_count++ : null;
        ($phoneNumberLength < 0) ? $error_count++ : null;
        ($phoneNumberLength < 0) ? $error_count++ : null;
        ($phoneNumberConvertString[0] != '9') ? $error_count++ : null;

        if ($error_count == 0) {

            $api->Update('user', 'user_id', [
                'key1' => ['firstname', "'" . $fname . "'"],
                'key2' => ['lastname', "'" . $lname . "'"],
                'key3' => ['mobile_no', $phone_number],
                'key4' => ['Modified_at', "'" . $date . "'"]
            ], $_SESSION['users'][0]->user_id);

            $_SESSION['update_profile-message'] = array(
                "title" => 'Account Updated!',
                "body" => 'Your Account has been Successfully Updated.',
                "type" => 'success'
            );
        } else {
            $_SESSION['update_profile-message'] = array(
                "title" => 'Invalid Input',
                "body" => '',
                "type" => 'error'
            );
        }
        header('Location: ../user/account/profile.php');

        exit();
    }

    if (isset($_POST['change_user_password'])) {

        $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);

        $current_password = filter_input(INPUT_POST, 'current_password', FILTER_SANITIZE_SPECIAL_CHARS);
        $new_password = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_SPECIAL_CHARS);
        $rnew_password = filter_input(INPUT_POST, 'rnew_password', FILTER_SANITIZE_SPECIAL_CHARS);
        $new_pass_len = strval($new_password);
        $new_pass_len = strlen($new_pass_len);
        $date = date('Y-m-d h:i:s');
        $error_msg = [];
        $change_password_error = 0;

        if (empty($current_password)) {
            $change_password_error++;
            $error_msg['current_password'] = 'Current Password must not be empty.';
        }

        if ($current_password != $user_info[0]->password) {
            $change_password_error++;
            $error_msg['current_password'] = 'Invalid Current Password.';
        }

        if (empty($error_msg['current_password'])) {
            if (empty($new_password)) {
                $change_password_error++;
                $error_msg['new_password'] = 'New Password must not be empty.';
            } else if ($new_pass_len > 5 && !preg_match("/^(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]+$/", $new_password)) {
                $change_password_error++;
                $error_msg['new_password'] = 'Password must be atleast one(1) Uppercase and Lowercase letter, one(1) Digit, and six(6) length.';
            }
        }

        if (!empty($error_msg['new_password'])) {
            if (empty($rnew_password)) {
                $change_password_error++;
                $error_msg['rnew_password'] = 'Retype Password must not be empty.';
            } else if ($rnew_password != $new_password) {
                $change_password_error++;
                $error_msg['rnew_password'] = 'Those passwords didn’t match';
            }
        }

        if ($change_password_error == 0) {

            $api->Update('user', 'user_id', [
                'key1' => ['password', "'$new_password'"],
                'key2' => ['Modified_at', "'$date'"]
            ], $_SESSION['users'][0]->user_id);

            $_SESSION['update_profile-message'] = array(
                "title" => 'Password Changed !',
                "body" => 'Password has been Changed Successfully.',
                "type" => 'success'
            );
            header('Location: ../user/account/profile.php');
        } else {
            $_SESSION['update_password-message'] = array(
                "title" => 'Change Password Failed.',
                "body" =>  $error_msg['current_password'] . ' ' . $error_msg['new_password'] . ' ' . $error_msg['rnew_password'],
                "type" => 'error'
            );
        }

        header('Location: ../user/account/update/change_password.php');
    }
} else {
    die('something went wrong.');
}