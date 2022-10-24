<?php

session_start();
date_default_timezone_set('Asia/Manila');

if (isset($_SESSION['users'])) {

    require_once '../../configs/database.php';
    require_once '../../includes/classes.php';

    $api = new MyAPI($main_conn);

    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $item_rows = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id, true);
    $all_cart_items = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id);
    $user_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);

    $user_name = $user_info[0]->firstname;
    $title = 'Your Profile | Dairy Raisers';
    require_once '../../includes/header.php';
    require_once '../../includes/navbar.php';

?>

    <!-- PROFILE CONTENTS -->
    <main class="container-fluid p-3">
        <div id="userSetting"></div>
        <!-- SIDEBAR -->
        <div class="row px-5">
            <div class="col-sm-3 px-4 py-4">
                <h1 class="fs-5 text-primary"><i class="bi bi-person-circle"></i> <?= $user_info[0]->firstname; ?></h1>
                <hr class="opacity-25 text-secondary">
                <div class="container d-flex flex-column profile-account">
                    <a class="" href="./profile.php"><i class="bi bi-info-circle"></i> Account Info</a>
                    <ul class="mb-3">
                        <li class="my-2"><a href="./profile.php" class="active-link">Profile</a></li>
                        <li class="my-2"><a href="./update/change_password.php">Change Password</a></li>
                        <li class=" my-2"><a href="./addresses.php" id="address">Addresses</a></li>
                    </ul>
                    <a class="" href="#"><i class="bi bi-clock-history"></i> Order History</a>
                    <a class=" my-3" href="#"><i class="bi bi-search"></i> Order Tracker</a>
                </div>
            </div>
            <!-- MAIN CONTENT -->
            <div class="col-sm bg-light p-4">
                <p class="fs-3 m-auto">YOUR PROFILE</p>
                <p class="lead fs-5">edit your account</p>
                <hr class="opacity-25 text-secondary">
                <form action="../../validation/user_update.php" method="POST" class="px-5">
                    <div class="form-floating mb-3">
                        <input type="text" name="fname" id="fname" value="<?= $user_info[0]->firstname; ?>" class="form-control" placeholder="first name" required>
                        <label for="fname">First Name</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="text" name="lname" id="lname" value="<?= $user_info[0]->lastname; ?>" class="form-control" placeholder="last name" required>
                        <label for="lname">Last Name</label>
                    </div>
                    <div class="form-floating mb-4">
                        <input type="number" name="phone_number" id="phone_number" value="<?= $user_info[0]->mobile_no; ?>" oninput="maxlength(this)" class="form-control" required>
                        <label for="phone_number">(+63) Phone Number</label>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="mb-1 ">
                            <p class="lead">Email Address:</p>
                            <p class="fw-bold"><?= $user_info[0]->email; ?></p>
                        </div>
                        <div class=" btn-group-vertical">
                            <a href="./update/change_password.php" class="btn btn-danger mb-2">Change Password <i class="fs-5 bi bi-link-45deg"></i></a>
                            <button type="submit" name="update_info" class="btn btn-primary">Update Info</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>
    <!-- FOOTER -->
<?php

}

require_once('../../includes/footer.php');

if (isset($_SESSION['update_profile-message'])) : ?>
    <script>
        swal(
            "<?= $_SESSION['update_profile-message']['title']; ?>",
            "<?= $_SESSION['update_profile-message']['body']; ?>",
            "<?= $_SESSION['update_profile-message']['type']; ?>"
        );
    </script>
<?php
endif;
unset($_SESSION['update_profile-message']);
?>

<script>
    var phoneNumber = document.querySelector('#phone_number');

    function maxlength(phoneNumber) {
        if (phoneNumber.value.length > 10) {
            phoneNumber.value = phoneNumber.value.slice(0, 10);
        };
    };
</script>
