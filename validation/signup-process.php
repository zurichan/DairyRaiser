<?php
session_start();
date_default_timezone_set('Asia/Manila');

require_once '../configs/database.php';
require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';
require_once '../PHPMailer-master/src/Exception.php';
require_once '../includes/classes.php';

$api = new MyAPI($main_conn);

if (isset($_GET['google_signup'])) {

    $givenname = filter_input(INPUT_GET, 'givenname', FILTER_SANITIZE_SPECIAL_CHARS);
    $familyname = filter_input(INPUT_GET, 'familyname', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);
    $picture = filter_input(INPUT_GET, 'image', FILTER_VALIDATE_URL);
    $err = 0;

    (empty($givenname)) ? $err++ : NULL;
    (empty($familyname)) ? $err++ : NULL;
    (empty($email)) ? $err++ : NULL;

    if ($err == 0) {
        echo $givenname . '<br>' . $familyname . '<br>' . $email . '<br>';
        $_SESSION['google_signup'] = true;
        $_SESSION['givenname'] = $givenname;
        $_SESSION['familyname'] = $familyname;
        $_SESSION['email'] = $email;
        $_SESSION['picture'] = $picture;

        header('Location: ../entry/signup.php');
    }
}

if (isset($_POST['submit'])) {

    $all_users = $api->Read('user', 'all');

    // get user input

    if (isset($_SESSION['google_signup'])) {
        $fname = $_SESSION['givenname'];
        $lname = $_SESSION['lastname'];
        $email = $_SESSION['email'];
        $picture = $_SESSION['picture'];
    } else {
        $fname = htmlspecialchars($_POST['fname']);
        $lname = htmlspecialchars($_POST['lname']);
        $email = htmlspecialchars($_POST['email']);
    }
    $phoneNumber = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_NUMBER_INT);
    $password = htmlspecialchars($_POST['password']);
    $rpassword = htmlspecialchars($_POST['rpassword']);
    $pass_len = strval($password);
    $pass_len = strlen($pass_len);
    $reg_Error = [];
    $reg_ErrorMsg = [];

    // F I R S T  N A M E
    if (!preg_match("/^[a-zA-Z-' ]*$/", $fname)) {
        $reg_Error['fname_err'] = 'is-invalid';
        $reg_ErrorMsg['fname_errMsg'] = 'Only letters and white space allowed.';
    } else if (empty($fname)) {
        $reg_Error['fname_err'] = 'is-invalid';
        $reg_ErrorMsg['fname_errMsg'] = 'Enter first name';
    }
    // L A S T  N A M E
    if (!preg_match("/^[a-zA-Z-' ]*$/", $lname)) {
        $reg_Error['lname_err'] = 'is-invalid';
        $reg_ErrorMsg['lname_errMsg'] = 'Only letters and white space allowed.';
    } else if (empty($lname)) {
        $reg_Error['lname_err'] = 'is-invalid';
        $reg_ErrorMsg['lname_errMsg'] = 'Enter last name';
    }
    // E M A I L  A D D R E S S
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $reg_Error['email_err'] = 'is-invalid';
        $reg_ErrorMsg['email_errMsg'] = 'Invalid email format';
    } else if (empty($email)) {
        $reg_Error['email_err'] = 'is-invalid';
        $reg_ErrorMsg['email_errMsg'] = 'Enter email address';
    }
    // E M A I L  E X I S T S
    foreach ($all_users as $user_email) {
        if ($user_email->email === $email) {
            $reg_Error['email_err'] = 'is-invalid';
            $reg_ErrorMsg['email_errMsg'] = 'email already exist.';
        }
    }
    // P H O N E  N U M B E R
    $phoneNumberConvertString = strval($phoneNumber);
    $phoneNumberLength = strlen($phoneNumberConvertString);
    if ($phoneNumberLength > 10 || $phoneNumberLength < 0 || $phoneNumberConvertString[0] != '9') {
        $reg_Error['phoneNumber_err'] = 'is-invalid';
        $reg_ErrorMsg['phoneNumber_errMsg'] = 'Invalid Phone Number Format';
    }
    if (empty($phoneNumber)) {
        $reg_Error['phoneNumber_err'] = 'is-invalid';
        $reg_ErrorMsg['phoneNumber_errMsg'] = 'Enter Phone Number';
    }
    // P A S S W O R D  R E Q U I R E M E N T S
    if ($pass_len > 5 && !preg_match("/^(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]+$/", $password)) {
        $reg_Error['pass_err'] = 'is-invalid';
        $reg_ErrorMsg['pass_errMsg'] = 'Password must contain atleast one Uppercase and one Digit';
    } else if (empty($password)) {
        $reg_Error['pass_err'] = 'is-invalid';
        $reg_ErrorMsg['pass_errMsg'] = 'Enter a password';
    }
    // P A S S W O R D  V A L I D A T I O N
    if (empty($reg_Error['pass_err'])) {
        if ($rpassword != $password) {
            $reg_Error['rpass_err'] = 'is-invalid';
            $reg_ErrorMsg['rpass_errMsg'] = 'Those passwords didn’t match. Try again.';
        }
    }

    // ENCODE ERRORS INTO JSON FORMAT
    json_encode($reg_Error);

    // no error
    if (empty($reg_Error)) {

        $_SESSION['success'] = 'user ' . $lname . ', ' . $fname;
        $ipaddress = $api->IP_address();
        $verification_code = md5(time() . $fname);

        // OTP TIME
        $date = date('Y-m-d h:i:s');
        $OTPDate = date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s')) + (60 * 10));

        // $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        //GENERATE EMAIL VERIFICATION CODE

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

        // INPUT USER, USER ADDRESS, AND ADD A SHOPPING SESSION DATA IN DATABASE

        $api->Create('user', [
            'key1' => ['firstname', "'$fname'"],
            'key2' => ['lastname', "'$lname'"],
            'key3' => ['email', "'$email'"],
            'key4' => ['password', "'$password'"],
            'key5' => ['mobile_no', $phoneNumber],
            'key6' => ['user_ip', "'$ipaddress'"],
            'key7' => ['verificationStatus', 0],
            'key8' => ['ActivationCode', "'$verification_code'"],
            'key9' => ['date_stamp', "'$OTPDate'"],
            'key10' => ['RegistrationDate', "'$date'"],
            'key11' => ['Modified_at', "'$date'"]
        ]);
        $get_user_info = $api->Read('user', 'set', 'email', "$email");
        $api->Create('shopping_session', [
            'key1' => ['user_id', $get_user_info[0]->user_id]
        ]);

        $_SESSION['signup-message'] = array(
            "title" => 'Verify Your Email',
            "body" =>  'We have sent an email verification to your email address.',
            "type" => 'success'
        );

        header('Location: ../entry/signup.php');

        exit();

        if (!$mail->send()) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}