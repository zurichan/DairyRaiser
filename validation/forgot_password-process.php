<?php
session_start();
date_default_timezone_set('Asia/Manila');

require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';
require_once '../PHPMailer-master/src/Exception.php';

require_once '../configs/database.php';
require_once '../includes/classes.php';

$date = date('Y-m-d h:i:s');
$api = new MyAPI($main_conn);

if (isset($_POST['forgot_password'])) {

    $email = filter_input(INPUT_POST, 'fpw_email', FILTER_SANITIZE_EMAIL);
    $get_user = $api->Read('user', 'set', 'email', "$email");

    $err = 0;

    (empty($email)) ? $err++ : NULL;
    (empty($get_user)) ? $err++ : NULL;

    if ($err == 0) {

        if ($get_user[0]->verificationStatus == 1) {

            $given_name = $get_user[0]->firstname;
            $verification_code = md5(time() . $given_name);

            $OTPDate = date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s')) + (60 * 10));

            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->Username = 'dairyraisers@gmail.com';
            $mail->Password = 'qloiqlteoajeunsu';
            $mail->SMTPSecure = 'tls';
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;

            $mail->setFrom('dairyraisers@gmail.com', 'Dairy Raisers');
            $mail->addAddress($email, $given_name);
            $mail->addReplyTo('dairyraisers@gmail.com', 'Information');

            $mail->isHTML(true);
            $mail->Subject = '[Dairy Raisers] Forgot Password';

            $mail->Body = "
        <div>
    <h5 style='font-size: 25px;'>Greetings <span style='font-weight: bolder;'>$given_name !</span> This is </h5>
    <p style='font-size: 17px;'>Verify your Email Address to Login with the given Token below:</p>
    <a style='text-decoration: none; color: navy; font-weight: bolder; border: 2px solid navy; padding: 10px;' href='http://localhost:3000/validation/email-forgotten-password.php?verification_key=$verification_code&user_email=$email'>Verify Email</a>
    <p>Your Verification Token will expire within 10 minutes.</p>        
    ";
            if ($mail->send()) {

                $api->Update('user', 'email', [
                    '1' => ['ActivationCode', "'$verification_code'"],
                    '2' => ['date_stamp', "'$OTPDate'"]
                ], "$email");

                $_SESSION['forgotpassword-message'] = array(
                    "title" => 'Email Confirmation Sent',
                    "body" => '',
                    "type" => 'success'
                );
            } else {
                $_SESSION['forgotpassword-message'] = array(
                    "title" => 'Something Went Wrong.',
                    "body" => $mail->ErrorInfo,
                    "type" => 'error'
                );
            }
        } else {
            $_SESSION['forgotpassword-message'] = array(
                "title" => 'This Email is not yet Verified',
                "body" => '',
                "type" => 'error'
            );
        }
    } else {
        $_SESSION['forgotpassword-message'] = array(
            "title" => 'Something Went Wrong.',
            "body" => '',
            "type" => 'error'
        );
    }

    header('Location: ../entry/forgot_password.php');
    exit();
}

if (isset($_POST['fpwe_reset_password'])) {
    echo 'asd';
    $email = $_SESSION['reset_fpwe_email'];
    $user_info = $api->Read('user', 'set', 'email', "$email");

    $new_password = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_SPECIAL_CHARS);
    $rnew_password = filter_input(INPUT_POST, 'rnew_password', FILTER_SANITIZE_SPECIAL_CHARS);
    $new_pass_len = strval($new_password);
    $new_pass_len = strlen($new_pass_len);
    $change_password_error = 0;

    (empty($new_password)) ? $change_password_error++ : NULL;
    (empty($rnew_password)) ? $change_password_error++ : NULL;

    if (empty($new_password)) {
        $change_password_error++;
        $error_msg['new_password'] = 'New Password must not be empty.';
    } else if ($new_pass_len > 5 && !preg_match("/^(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]+$/", $new_password)) {
        $change_password_error++;
        $error_msg['new_password'] = 'Password must be atleast one(1) Uppercase and Lowercase letter, one(1) Digit, and six(6) length.';
    }

    if (empty($rnew_password)) {
        $change_password_error++;
        $error_msg['rnew_password'] = 'Retype Password must not be empty.';
    } else if ($rnew_password != $new_password) {
        $change_password_error++;
        $error_msg['rnew_password'] = 'Those passwords didn’t match';
    }

    if ($change_password_error == 0 && $user_info[0]->date_stamp != NULL && $user_info[0]->ActivationCode != NULL && $user_info[0]->verificationStatus == 1) {
        $api->Update('user', 'email', [
            'key1' => ['password', "'$new_password'"],
            'key2' => ['Modified_at', "'$date'"]
        ], "$email");

        unset($_SESSION['reset_fpwe_email']);

        $_SESSION['login-message'] = array(
            "title" => 'Password Reset!',
            "body" => 'Password has been Reset Successfully.',
            "type" => 'success'
        );

        header('Location: ../entry/login.php');
        exit();
    } else {
        echo 'asd';
        $_SESSION['reset-password-message'] = array(
            "title" => 'Reset Password Failed.',
            "body" =>  $error_msg['current_password'] . ' ' . $error_msg['new_password'] . ' ' . $error_msg['rnew_password'],
            "type" => 'error'
        );
        echo $_SERVER['HTTP_REFERER'];
        header('Location:' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
