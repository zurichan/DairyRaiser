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

$item_rows = '';
$user_name = '';

$title = 'Email Verification | Dairy Raisers';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

if (isset($_SESSION['users'])) {

    header('Location: ../index.php');
    exit();
    
} else if(!isset($_SESSION['unverified_email'])) {

    header('Location: ../index.php');
    exit();

} else {
?>

    <main class="container-fluid" style="width: 800px;">
        <!-- main row -->
        <div class=" d-flex justify-content-center align-items-center">
            <!-- column 2 -->
            <div class=" m-5 px-2 py-3 bg-light border">
                <form action="../validation/resend_verification.php" method="POST" class="d-flex flex-column px-5">
                    <div class="d-flex flex-column justify-content-center align-items-center">
                        <img src="../img/company-logo.png" class="img-fluid logo" alt="company logo">
                        <p class="fs-4 text-center fw-bold mx-auto mb-4">Email Verification</p>
                        <p class=" text-muted"><?= $_SESSION['unverified_email']; ?></p>
                        <p class="text-center">We have sent already your verification token to your email address. Please check your inbox.</p>
                        <button class="btn btn-primary mb-3" type="submit" name="resend_vkey">Resend Verification</button>
                        <a class="btn btn-sm btn-secondary" href="../entry/login.php">Go Back</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

<?php

}
require_once '../includes/footer.php';

if (isset($_SESSION['verifying_email-message'])) : ?>
    <script>
        swal(
            "<?= $_SESSION['verifying_email-message']['title']; ?>",
            "<?= $_SESSION['verifying_email-message']['body']; ?>",
            "<?= $_SESSION['verifying_email-message']['type']; ?>"
        );
    </script>
<?php

endif;
unset($_SESSION['verifying_email-message']);
?>