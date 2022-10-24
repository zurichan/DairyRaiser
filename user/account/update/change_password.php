<?php

session_start();
date_default_timezone_set('Asia/Manila');

if (isset($_SESSION['users'])) {
        
    require_once '../../../configs/database.php';
    require_once '../../../includes/classes.php';

    $api = new MyAPI($main_conn);

    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $cart_items_row = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id, true);
    $all_cart_items = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id);

    $user_name = $user_info[0]->firstname;
    $title = 'Change Password | Dairy Raisers';
    require_once '../../includes/header.php';
    require_once '../../includes/navbar.php';
?>

    <main class="container-fluid p-3">
        <div id="userSetting"></div>
        <!-- sidebar -->
        <div class="row px-5">
            <div class="col-sm-3 px-4 py-4">
                <h1 class="fs-5 text-primary"><i class="bi bi-person-circle"></i> <?= $user_info[0]->firstname; ?></h1>
                <hr class="opacity-25 text-secondary">
                <div class="container d-flex flex-column profile-account">
                    <a class="" href="../profile.php"><i class="bi bi-info-circle"></i> Account Info</a>
                    <ul class="mb-3">
                        <li class="my-2"><a href="../profile.php">Profile</a></li>
                        <li class="my-2"><a href=".change_password.php" class="active-link">Change Password</a></li>
                        <li class=" my-2"><a href="../addresses.php" id="address">Addresses</a></li>
                    </ul>
                    <a class="" href="#"><i class="bi bi-clock-history"></i> Order History</a>
                    <a class=" my-3" href="#"><i class="bi bi-search"></i> Order Tracker</a>
                </div>
            </div>
            <!-- content -->
            <div class="col-sm bg-light p-4">
                <p class="fs-3 m-auto">CHANGE YOUR PASSWORD</p>
                <p class="lead fs-5">enter new password</p>
                <hr class="opacity-25 text-secondary">
                <form action="../../../validation/user_update.php" method="POST" class="px-5">
                    <!-- CURRENT PASSWORD -->
                    <div class="form-group input-group mb-3">
                        <div class="form-floating form-floating-group flex-grow-1">
                            <input placeholder="type password" type="password" name="current_password" id="current_password" class="form-control">
                            <label for="current_password">Current Password:</label>
                        </div>
                        <button type="button" id="current_pass-icon-click" class="btn btn-lg btn-outline-dark">
                            <i id="current_pass-eye-icon" class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                    <!-- NEW PASSWORD -->
                    <div class="form-group input-group mb-3">
                        <div class="form-floating form-floating-group flex-grow-1">
                            <input placeholder="type password" type="password" name="new_password" id="new_password" class="form-control">
                            <label for="new_password">New Password:</label>
                        </div>
                        <button type="button" id="new_pass-icon-click" class="btn btn-lg btn-outline-dark">
                            <i id="new_pass-eye-icon" class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                    <!-- RETYPE NEW PASSWORD -->
                    <div class="form-group input-group mb-3">
                        <div class="form-floating form-floating-group flex-grow-1">
                            <input placeholder="type password" type="password" name="rnew_password" id="rnew_password" class="form-control">
                            <label for="rnew_password">Retype New Password:</label>
                        </div>
                        <button type="button" id="rnew_pass-icon-click" class="btn btn-lg btn-outline-dark">
                            <i id="rnew_pass-eye-icon" class="bi bi-eye-fill"></i>
                        </button>
                    </div>
                    <button type="submit" name="change_user_password" class="btn btn-primary mt-2">Change Password</button>
                </form>
            </div>
        </div>
    </main>
<?php
}

require_once('../../../includes/footer.php');

if (isset($_SESSION['update_password-message'])) : ?>
    <script>
        swal(
            "<?= $_SESSION['update_password-message']['title']; ?>",
            "<?= $_SESSION['update_password-message']['body']; ?>",
            "<?= $_SESSION['update_password-message']['type']; ?>"
        );
    </script>
<?php
endif;
unset($_SESSION['update_password-message']);
?>

<?php if(isset($_SESSION['users'])) : ?>

<script src="./scripts/session_timeout.js"></script>

<?php endif; ?>

<script src="../../../scripts/user.profile.js"></script>