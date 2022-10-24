<?php
session_start();
date_default_timezone_set('Asia/Manila');

require_once '../configs/database.php';
require_once '../includes/classes.php';

$date = date('Y-m-d h:i:s');
$api = new MyAPI($main_conn);

if (isset($_GET['verification_key']) && isset($_GET['user_email']) && !isset($_SESSION['users'])) {

    $verification_key = filter_input(INPUT_GET, 'verification_key', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_GET, 'user_email', FILTER_SANITIZE_EMAIL);

    $get_user = $api->Read('user', 'set', 'email', "$email");
    $err = 0;
    $_SESSION['reset_fpwe_email'] = $email;

    (empty($verification_key)) ? $err++ : NULL;
    (empty($email)) ? $err++ : NULL;
    (empty($get_user)) ? $err++ : NULL;

    if ($err == 0) {
        if ($get_user[0]->date_stamp != NULL && $get_user[0]->ActivationCode != NULL && $get_user[0]->verificationStatus == 1) {

            $title = 'Reset Password | Dairy Raisers';
            require_once '../includes/header.php';
            require_once '../includes/navbar.php';

?>

            <main class="container-fluid" style="width: 700px;">
                <!-- main row -->
                <div class="row d-flex justify-content-center align-items-center">
                    <!-- column 2 -->
                    <div class="border col-md-7 m-5 px-3 py-4 bg-light">
                        <form action="../../../validation/forgot_password-process.php" method="POST" class="d-flex flex-column px-3">
                            <div class="d-flex flex-column justify-content-center align-items-center">
                                <img src="../img/company-logo.png" class="img-fluid logo" alt="company logo">
                                <p class=" text-center mx-auto mb-4 lead">Reset Password from <span class="text-muted"><?= $email; ?></span></p>
                            </div>
                            <!-- NEW PASSWORD -->
                            <div class="form-group input-group mb-3">
                                <div class="form-floating form-floating-group flex-grow-1">
                                    <input placeholder="type password" type="password" name="new_password" id="new_password" class="form-control">
                                    <label for="new_password">New Password:</label>
                                </div>
                                <button type="button" id="new_pass-icon-click" class="btn btn-lg btn-outline-secondary">
                                    <i id="new_pass-eye-icon" class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                            <!-- RETYPE NEW PASSWORD -->
                            <div class="form-group input-group mb-3">
                                <div class="form-floating form-floating-group flex-grow-1">
                                    <input placeholder="type password" type="password" name="rnew_password" id="rnew_password" class="form-control">
                                    <label for="rnew_password">Retype New Password:</label>
                                </div>
                                <button type="button" id="rnew_pass-icon-click" class="btn btn-lg btn-outline-secondary">
                                    <i id="rnew_pass-eye-icon" class="bi bi-eye-fill"></i>
                                </button>
                            </div>
                            <button type="submit" name="fpwe_reset_password" class="btn btn-primary mt-2">Reset Password</button>
                        </form>
                    </div>
                </div>
            </main>

            <?php require_once '../includes/footer.php'; ?>

            <script>
                $(document).ready(() => {

                    $('#current_pass-icon-click').click((e) => {
                        e.preventDefault();
                        $('#current_pass-eye-icon').toggleClass('bi-eye-slash-fill');

                        if ($('#current_password').attr('type') === 'password') {
                            $('#current_password').attr('type', 'text');
                        } else {
                            $('#current_password').attr('type', 'password');
                        }
                    });

                    $('#new_pass-icon-click').click((e) => {
                        e.preventDefault();
                        $('#new_pass-eye-icon').toggleClass('bi-eye-slash-fill');

                        if ($('#new_password').attr('type') === 'password') {
                            $('#new_password').attr('type', 'text');
                        } else {
                            $('#new_password').attr('type', 'password');
                        }
                    });

                    $('#rnew_pass-icon-click').click((e) => {
                        e.preventDefault();
                        $('#rnew_pass-eye-icon').toggleClass('bi-eye-slash-fill');

                        if ($('#rnew_password').attr('type') === 'password') {
                            $('#rnew_password').attr('type', 'text');
                        } else {
                            $('#rnew_password').attr('type', 'password');
                        }
                    });
                });
            </script>

            <?php
            if (isset($_SESSION['reset-password-message'])) : ?>
                <script>
                    swal(
                        "<?= $_SESSION['reset-password-message']['title']; ?>",
                        "<?= $_SESSION['reset-password-message']['body']; ?>",
                        "<?= $_SESSION['reset-password-message']['type']; ?>"
                    );
                </script>
<?php

            endif;
            unset($_SESSION['reset-password-message']);
        } else {
            echo 'Something Went Wrong';
        }
    }
}
?>