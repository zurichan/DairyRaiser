<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../configs/database.php';
require_once '../includes/classes.php';

$title = 'Forgot Password | Dairy Raisers';
require_once '../includes/header.php';
require_once '../includes/navbar.php';

?>

<main class="container-fluid" style="width: 700px;">
    <!-- main row -->
    <div class="row d-flex justify-content-center align-items-center">
        <!-- column 2 -->
        <div class="border col-md-7 m-5 px-3 py-4 bg-light">
            <form action="../validation/forgot_password-process.php" method="POST" class="d-flex flex-column px-5">
                <div class="d-flex flex-column justify-content-center align-items-center">
                    <img src="../img/company-logo.png" class="img-fluid logo" alt="company logo">
                    <p class="fs-5 text-center mx-auto mb-4 lead">Forgot Password</p>
                </div>
                <div class="form-floating mb-3">
                    <input type="text" name="fpw_email" id="fpw_email" class="form-control" placeholder="enter email address" required>
                    <label for="fpw_email">Enter Email Address:</label>
                </div>
                <!-- submit -->
                <div class="d-flex justify-content-between align-items-center">
                    <a href="./login.php" class="lead btn btn-secondary">Back</a>
                    <button class="btn btn-primary" type="submit" name="forgot_password">Submit</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once '../includes/footer.php'; ?>

<?php if (isset($_SESSION['forgotpassword-message'])) : ?>
    <script>
        swal(
            "<?= $_SESSION['forgotpassword-message']['title']; ?>",
            "<?= $_SESSION['forgotpassword-message']['body']; ?>",
            "<?= $_SESSION['forgotpassword-message']['type']; ?>"
        );
    </script>
<?php endif;
unset($_SESSION['forgotpassword-message']);
