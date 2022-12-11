<?php
session_start();
date_default_timezone_set('Asia/Manila');

require_once '../configs/database.php';
require_once '../PHPMailer-master/src/PHPMailer.php';
require_once '../PHPMailer-master/src/SMTP.php';
require_once '../PHPMailer-master/src/Exception.php';
require_once '../includes/classes.php';

$api = new MyAPI($main_conn);

if (isset($_GET['google_signup_cancel'])) {

    unset($_SESSION['google_signup']);
    unset($_SESSION['givenname']);
    unset($_SESSION['familyname']);
    unset($_SESSION['email']);
    unset($_SESSION['picture']);

    header('Location: ../entry/signup.php');
    exit();
}
if (isset($_GET['google_signup'])) {

    $givenname = filter_input(INPUT_GET, 'givenname', FILTER_SANITIZE_SPECIAL_CHARS);
    $familyname = filter_input(INPUT_GET, 'familyname', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);
    $picture = filter_input(INPUT_GET, 'image', FILTER_VALIDATE_URL);
    $err = 0;
    $all_email = $api->Read('user', 'all');

    (empty($givenname)) ? $err++ : NULL;
    (empty($familyname)) ? $err++ : NULL;
    (empty($email)) ? $err++ : NULL;

    if ($err == 0) {
        $duplicate = 0;
        foreach ($all_email as $user_email) {
            if ($user_email->email == $email) {
                $duplicate++;
                break;
            }
        }

        if ($duplicate == 0) {
            $_SESSION['google_signup'] = true;
            $_SESSION['givenname'] = $givenname;
            $_SESSION['familyname'] = $familyname;
            $_SESSION['email'] = $email;
            $_SESSION['picture'] = $picture;
        } else {
            $_SESSION['signup-message'] = array(
                "title" => 'Gmail is already in used',
                "body" =>  '',
                "type" => 'error'
            );
        }

        header('Location: ../entry/signup.php');
        exit();
    }
}

// if (isset($_POST['signup'])) {
//     $err = 0;
//     $message = '';
//     $result = [];

//     if (isset($_POST['name'])) {
//         $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
//         if (!empty($name)) {
//             if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
//                 $err++;
//                 $message = 'Only white space and letters are allowed.';
//             }
//         } else {
//             $err++;
//             $message = 'Name should not be empty.';
//         }
//     }

//     if (isset($_POST['email'])) {
//         $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
//         if (!empty($email)) {
//             $all_email = $api->Read('user', 'all');
//             foreach ($all_email as $user_email) {
//                 if ($user_email->email == $email) {
//                     $err++;
//                     $message = 'Email Address is already exist.';
//                     break;
//                 }
//             }
//             if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
//                 $err++;
//                 $message = 'Invalid Email format.';
//             }
//         } else {
//             $err++;
//             $message = 'Email Address is empty.';
//         }
//     }
//     if (isset($_POST['phone_number'])) {
//         $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_NUMBER_INT);
//         if (!empty($phone_number) || $phone_number != '') {
//             $phoneNumberConvertString = strval($phone_number);
//             $phoneNumberLength = strlen($phoneNumberConvertString);
//             if ($phoneNumberLength != 10 || $phoneNumberLength < 0 || $phone_number = 0 || $phoneNumberConvertString[0] != '9') {
//                 $err++;
//                 $message = 'Invalid Phone Number Format.';
//             }
//         } else {
//             $err++;
//             $message = 'Phone Number is empty or is not a number.';
//         }
//     }

//     if (isset($_POST['password'])) {
//         $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
//         if (!empty($password)) {
//             if (isset($_POST['rpassword'])) {
//                 $rpassword = filter_input(INPUT_POST, 'rpassword', FILTER_SANITIZE_SPECIAL_CHARS);
//                 if ($rpassword != $password) {
//                     $err++;
//                     $message = "Those passwords did not match.";
//                 }
//             } else {
//                 $len = strval($password);
//                 $pass_len = strlen($len);

//                 if ($pass_len < 5 || !preg_match("/^(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]+$/", $password)) {
//                     $err++;
//                     $message = 'Password must contain (5+) length, and atleast (1) uppercase and digit.';
//                 }
//             }
//         } else {
//             $err++;
//             $message = 'Password should not be empty.';
//         }
//     }

//     ($err == 0) ? $message = 'nothing' : NULL;
//     $result = [
//         'error' => $err,
//         'message' => $message,
//     ];

//     echo json_encode($result);
// }

if (isset($_POST['signup-submit'])) {
    $err = 0;

    if (isset($_SESSION['google_signup'])) {
        $givenname = $_SESSION['givenname'];
        $familyname = $_SESSION['familyname'];
        $email = $_SESSION['email'];
        $picture = $_SESSION['picture'];

        (!isset($_SESSION['givenname'])) ? $err++ : NULL;
        (!isset($_SESSION['familyname'])) ? $err++ : NULL;
        (!isset($_SESSION['email'])) ? $err++ : NULL;
        (!isset($_SESSION['picture'])) ? $err++ : NULL;
    } else {
        $givenname = filter_input(INPUT_POST, 'fname', FILTER_SANITIZE_SPECIAL_CHARS);
        $familyname = filter_input(INPUT_POST, 'lname', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $picture = '';

        (empty($givenname)) ? $err++ : NULL;
        (empty($familyname)) ? $err++ : NULL;
        (empty($email)) ? $err++ : NULL;
        (!preg_match("/^[a-zA-Z-' ]*$/", $givenname)) ? $err++ : NULL;
        (!preg_match("/^[a-zA-Z-' ]*$/", $familyname)) ? $err++ : NULL;
        (!filter_var($email, FILTER_VALIDATE_EMAIL)) ? $err++ : NULL;

        $all_email = $api->Read('user', 'all');
        foreach ($all_email as $user_email) {
            if ($user_email->email == $email) {
                $err++;
                break;
            }
        }
    }

    $phone_number = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_NUMBER_INT);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
    $rpassword = filter_input(INPUT_POST, 'rpassword', FILTER_SANITIZE_SPECIAL_CHARS);

    (empty($phone_number)) ? $err++ : NULL;
    (empty($password)) ? $err++ : NULL;
    (empty($rpassword)) ? $err++ : NULL;

    $phoneNumberConvertString = strval($phone_number);
    $phoneNumberLength = strlen($phoneNumberConvertString);
    ($phoneNumberLength != 10 || $phoneNumberLength < 0 || $phone_number == 0 || $phoneNumberConvertString[0] != '9') ? $err++ : NULL;

    $password_str = strval($password);
    $password_len = strlen($password_str);
    ($password_len < 5 || !preg_match("/^(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]+$/", $password)) ? $err++ : NULL;
    ($rpassword != $password) ? $err++ : NULL;

    if ($err == 0) {
        $phone_number = (int) $phone_number;
        $_SESSION['success'] = 'user ' . $familyname . ', ' . $givenname;
        $ipaddress = $api->IP_address();
        $verification_code = md5(time() . $givenname);
        // $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        // OTP TIME
        $date = date('Y-m-d h:i:s');
        $OTPDate = date('Y-m-d h:i:s', strtotime(date('Y-m-d h:i:s')) + (60 * 10));

        if (isset($_SESSION['google_signup'])) {

            $api->Create('user', [
                'key1' => ['firstname', "'$givenname'"],
                'key2' => ['lastname', "'$familyname'"],
                'key3' => ['email', "'$email'"],
                'key4' => ['password', "'$password'"],
                'key5' => ['mobile_no', $phone_number],
                'key6' => ['user_ip', "'$ipaddress'"],
                'key7' => ['verificationStatus', 1],
                'key10' => ['RegistrationDate', "'$date'"],
                'key11' => ['Modified_at', "'$date'"],
            ]);
            $get_user_info = $api->Read('user', 'set', 'email', "$email");
            $api->Create('shopping_session', [
                'key1' => ['user_id', $get_user_info[0]->user_id]
            ]);

            $_SESSION['users'] = array($get_user_info[0]);
            $_SESSION['TIME'] = time();
            $api->Delete('login_attempts', 'login_id', $login_attempts[0]->login_id);

            $_SESSION['index-message'] = array(
                "title" => 'Welcome, ' . $givenname,
                "body" =>  '',
                "type" => 'success'
            );
            header('Location: ../home.php');
            exit();
        } else {

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
            $mail->addAddress($email, $givenname);
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

            if ($mail->send()) {
                // INPUT USER, USER ADDRESS, AND ADD A SHOPPING SESSION DATA IN DATABASE
                $api->Create('user', [
                    'key1' => ['firstname', "'$givenname'"],
                    'key2' => ['lastname', "'$familyname'"],
                    'key3' => ['email', "'$email'"],
                    'key4' => ['password', "'$password'"],
                    'key5' => ['mobile_no', $phone_number],
                    'key6' => ['user_ip', "'$ipaddress'"],
                    'key7' => ['verificationStatus', 0],
                    'key8' => ['ActivationCode', "'$verification_code'"],
                    'key10' => ['RegistrationDate', "'$date'"],
                    'key11' => ['Modified_at', "'$date'"],
                    'key9' => ['date_stamp', "'$OTPDate'"]
                ]);
                $get_user_info = $api->Read('user', 'set', 'email', "'$email'");
                $api->Create('shopping_session', [
                    'key1' => ['user_id', $get_user_info[0]->user_id]
                ]);

                $_SESSION['login-message'] = array(
                    "title" => 'Verify Your Email',
                    "body" =>  'We have sent an email verification to your email address.',
                    "type" => 'success'
                );

                if (isset($_SESSION['google_signup'])) {
                    unset($_SESSION['google_signup']);
                    unset($_SESSION['givenname']);
                    unset($_SESSION['familyname']);
                    unset($_SESSION['email']);
                    unset($_SESSION['picture']);
                }

                header('Location: ../entry/login.php');
                exit();
            } else {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                $_SESSION['signup-message'] = array(
                    "title" => 'Something Went Wrong',
                    "body" =>  $mail->ErrorInfo,
                    "type" => 'error'
                );
                exit();
            }
            header('Location: ../entry/signup.php');
            exit();
        }
    } else {
        $_SESSION['signup-message'] = array(
            "title" => 'Something Went Wrong',
            "body" =>  '',
            "type" => 'error'
        );
        // header('Location: ../entry/signup.php');
        // exit();
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
        $get_user_info = $api->Read('user', 'set', 'email', "'$email'");
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