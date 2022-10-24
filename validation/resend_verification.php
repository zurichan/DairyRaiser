<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../configs/database.php';
require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';
require_once '../PHPMailer-master/src/Exception.php';
require_once '../includes/classes.php';

$api = new MyAPI($main_conn);
$date = date('Y-m-d h:i:s');

if (isset($_POST['resend_vkey']) && isset($_SESSION['unverified_email'])) {

    $user_info = $api->Read('user', 'set', 'email', $_SESSION['unverified_email']);
    $fname = $user_info[0]->firstname;
    $email = $_SESSION['unverified_email'];

    if ($user_info[0]->verify_tracker == 3) {

        if ($user_info[0]->retry_date_stamp == NULL) {

            $retryExpirationDate = date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s')) + (60 * 1440));
            $api->Update('user', 'email', [
                'key1' => ['retry_date_stamp', "'$retryExpirationDate'"]
            ], $_SESSION['unverified_email']);
        }

        $user_info = $api->Read('user', 'set', 'email', $_SESSION['unverified_email']);

        if ($user_info[0]->retry_date_stamp <= $date) {

            $vkeyExpirationDate = date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s')) + (60 * 10));
            $api->Update('user', 'email', [
                'key1' => ['verify_tracker', 0],
                'key2' => ['date_stamp', "'$vkeyExpirationDate'"],
                'key3' => ['retry_date_stamp', 'NULL']
            ], $_SESSION['unverified_email']);
        }
    }

    $user_info = $api->Read('user', 'set', 'email', $_SESSION['unverified_email']);

    if (empty($user_info[0]->retry_date_stamp)) {
        echo $user_info[0]->date_stamp . ' = ' . $date;
        if ($user_info[0]->date_stamp <= $date && $user_info[0]->verify_tracker != 3) {
            echo 'asd';
            $new_vkey = md5(time() . $fname);
            $resend_counter = $user_info[0]->verify_tracker + 1;
            $vkeyExpirationDate = date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s')) + (60 * 10));

            $api->Update('user', 'email', [
                'key1' => ['date_stamp', "'$vkeyExpirationDate'"],
                'key2' => ['ActivationCode', "'$new_vkey'"],
                'key3' => ['verify_tracker', $resend_counter]
            ], $_SESSION['unverified_email']);
           
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
            $mail->addAddress($email, $fname);
            $mail->addReplyTo('dairyraisers@gmail.com', 'Information');
    
            $mail->isHTML(true);
            $mail->Subject = '[Dairy Raisers] Email Verification.';
    
            // https://www.dairyraisers.com/
    
            $mail->Body = "
                <div>
        <h5 style='font-size: 25px;'>Greetings <span style='font-weight: bolder;'>$fname !</span> You Have Registered at Dairy Raisers</h5>
            <p style='font-size: 17px;'>Verify your Email Address to Login with the given Token below:</p>
            <a style='text-decoration: none; color: navy; font-weight: bolder; border: 2px solid navy; padding: 10px;' href='http://localhost:3000/entry/email_verification.php?verification_key=$verification_code&user_email=$email'>Verify Email</a>
            <p>Your Verification Token will expire within 10 minutes.</p>        
            ";
            $mail->send();

            if (!$mail->send()) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            } else {
                $_SESSION['verifying_email-message'] = array(
                    "title" => 'New Verification has been Sent!',
                    "body" => 'Please check your email inbox.',
                    "type" => 'success'
                );
            }

            header('Location: ../entry/verifying_email.php');
            exit();

        } else {

            $_SESSION['verifying_email-message'] = array(
                "title" => 'Verification Code is not yet Expired.',
                "body" => '',
                "type" => 'error'
            );

            header('Location: ../entry/verifying_email.php');
            exit();
        }
    } else {

        $_SESSION['verifying_email-message'] = array(
            "title" => 'Too many attempt. Please wait.',
            "body" => '',
            "type" => 'error'
        );

        header('Location: ../entry/verifying_email.php');
        exit();
    }
} else {
    
    header('Location: ../entry/verifying_email.php');
    exit();
}
