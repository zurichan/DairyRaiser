<?php

session_start();
date_default_timezone_set('Asia/Manila');

if (isset($_SESSION['users'])) {

    require_once '../../../configs/database.php';
    require_once '../../../includes/classes.php';
    require_once '../../../includes/header.php';
    require_once '../../../includes/navbar.php';

    $api = new MyAPI($main_conn);

    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $item_rows = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id, true);
    $all_cart_items = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id);
    $user_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $user_address_rows = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id, true);

    echo Title('Add Address | Dairy Raisers');
    echo navbar($user_info[0]->firstname, $item_rows);

    if($user_address_rows == 3) {

        $_SESSION['address-message'] = array(
            "title" => 'You have reach limit adding address.',
            "body" => '',
            "type" => 'error'
        );

        header('Location: ../addresses.php');

    }

?>

    <main class="container-fluid p-3">

        <!-- SIDEBAR -->
        <div class="row px-5">
            <div class="col-sm-3 px-4 py-4">
                <h1 class="fs-5 text-primary"><i class="bi bi-person-circle"></i> <?= $user_info[0]->firstname; ?></h1>
                <hr class="opacity-25 text-secondary">
                <div class="container d-flex flex-column profile-account">
                    <a class="" href="../profile.php"><i class="bi bi-info-circle"></i> Account Info</a>
                    <ul class="mb-3">
                        <li class="my-2"><a href="../profile.php">Profile</a></li>
                        <li class="my-2"><a href="../update/change_password.php">Change Password</a></li>
                        <li class=" my-2"><a href="../addresses.php" class="active-link" id="address">Addresses</a></li>
                    </ul>
                    <a class="" href="#"><i class="bi bi-clock-history"></i> Order History</a>
                    <a class=" my-3" href="#"><i class="bi bi-search"></i> Order Tracker</a>
                </div>
            </div>
            <!-- contents -->
            <div class="col-sm bg-light p-4">
                <p class="fs-3 m-auto">ADD ADDRESSES</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="../addresses.php">Addresses</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Add Address</li>
                    </ol>
                </nav>
                <hr class="opacity-25 text-secondary">
                <form action="../../../validation/address-process.php" method="POST" class=" mb-4 px-1">
                    <div class="d-flex flex-row justify-content-between align-items-center mb-3">
                        <div class="form-floating ">
                            <select class="form-select" aria-placeholder="enter province" name="province" id="province">
                                <option value="0" selected>Select Province</option>
                            </select>
                            <label class="form-label" for="province">Province: </label>
                        </div>
                        <div class="form-floating ">
                            <select class="form-select" name="municipality" id="municipality">
                                <option value="0" selected>Select Municipality</option>
                            </select>
                            <label class="form-label" for="municipality">Municipality: </label>
                        </div>
                        <div class="form-floating ">
                            <select class="form-select" name="barangay" id="barangay">
                                <option value="0" selected>Select Barangay</option>
                            </select>
                            <label class="form-label" for="barangay">Barangay: </label>
                        </div>
                        <div class="form-floating ">
                            <input type="number" class="form-control" oninput="maxlength(this)" name="postal_code" id="postal_code" placeholder="enter postal code" required>
                            <label class="form-label" for="postal_code">Postal Code:</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-floating ">
                            <input type="text" class="form-control" name="house_number" id="house_number" placeholder="enter postal code" required>
                            <label class="form-label" for="house_number">House/Building/Street Number, Street Name</label>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="form-floating ">
                            <input type="text" class="form-control" name="near_landmark" id="near_landmark" placeholder="enter postal code" required>
                            <label class="form-label" for="near_landmark">Near Landmark</label>
                        </div>
                    </div>
                    <button type="submit" name="add-address-process" class="btn btn-outline-primary">Add Address</button>
                </form>
            </div>

    </main>
<?php
}
require_once('../../../includes/footer.php');

if (isset($_SESSION['add-address-message'])) : ?>
    <script>
        swal(
            "<?= $_SESSION['add-address-message']['title']; ?>",
            "<?= $_SESSION['add-address-message']['body']; ?>",
            "<?= $_SESSION['add-address-message']['type']; ?>"
        );
    </script>
<?php

endif;
unset($_SESSION['add-address-message']);

?>

<script src="../../../scripts/address.js"></script>