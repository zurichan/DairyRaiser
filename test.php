<?php
session_start();
date_default_timezone_set('Asia/Manila');

require_once './configs/database.php';
require_once './PHPMailer-master/src/PHPMailer.php';
require_once './PHPMailer-master/src/SMTP.php';
require_once './PHPMailer-master/src/Exception.php';
require_once './includes/classes.php';

$api = new MyAPI($main_conn);

$fname = 'stingred';
$email = 'sting.red29@gmail.com';
$verification_code = md5(time() . $fname);
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
            <div style='display: flex;'>
               <h4 style='text-align: center; margin-right: 10px;'>Dairy Raisers</h4>
               <img src='cid:companylogo' style='width: 100px;'>
            </div>
            <p>Hi <span style='font-weight: bolder;'>$fname !</span> You are now registered at Dairy Raisers. Please verify this Email Address by clicking the link below:</p>
            <a style='font-weight: bolder;' href='http://localhost:3000/entry/email_verification.php?verification_key=$verification_code&user_email=$email'>Verify Email</a>
            <p>This Verification Token will expire within 10 minutes. Please DO NOT share this link to anyone.</p>
            ";
$mail->addEmbeddedImage(dirname(__DIR__) . '/OfficialDairyRaisers/img/company-logo.png', 'companylogo', 'company-logo.png');
$mail->send();
echo $mail->ErrorInfo;