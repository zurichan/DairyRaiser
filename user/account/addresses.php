<?php

session_start();
date_default_timezone_set('Asia/Manila');

if (isset($_SESSION['users'])) {

    require_once '../../configs/database.php';
    require_once '../../includes/classes.php';
    require_once '../../includes/header.php';
    require_once '../../includes/navbar.php';

    $api = new MyAPI($main_conn);

    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $item_rows = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id, true);
    $all_cart_items = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id);
    $user_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $user_address_rows = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id, true);

    echo Title('Your Address | Dairy Raisers');
    echo navbar($user_info[0]->firstname, $item_rows);

    $json_data = file_get_contents("../../scripts/cavite.json");

    $locations = json_decode($json_data, JSON_OBJECT_AS_ARRAY);

?>

    <main class="container-fluid p-3">

        <!-- SIDEBAR -->
        <div class="row px-5">
            <div class="col-sm-3 px-4 py-4">
                <h1 class="fs-5 text-primary"><i class="bi bi-person-circle"></i> <?= $user_info[0]->firstname; ?></h1>
                <hr class="opacity-25 text-secondary">
                <div class="container d-flex flex-column profile-account">
                    <a class="" href="./profile.php"><i class="bi bi-info-circle"></i> Account Info</a>
                    <ul class="mb-3">
                        <li class="my-2"><a href="./profile.php">Profile</a></li>
                        <li class="my-2"><a href="./update/change_password.php">Change Password</a></li>
                        <li class=" my-2"><a href="./addresses.php" class="active-link" id="address">Addresses</a></li>
                    </ul>
                    <a class="" href="#"><i class="bi bi-clock-history"></i> Order History</a>
                    <a class=" my-3" href="#"><i class="bi bi-search"></i> Order Tracker</a>
                </div>
            </div>
            <!-- contents -->
            <div class="col-sm bg-light p-4">
                <p class="fs-3 m-auto">YOUR ADDRESSES</p>
                <p class="lead fs-5">edit your address</p>
                <hr class="opacity-25 text-secondary">

                <a style="vertical-align: middle;" href="./update/add-address.php" class="btn btn-outline-secondary  px-5 py-1 container-fluid d-flex flex-row justify-content-around align-items-center mb-2">
                    <span class="fs-5">Create a New Address.</span>
                    <img class="img-fluid w-25" src="../../img/undraw_Address_re_yaoj.png" alt="no address">
                    <i style="font-size: 125px;" class=" opacity-25 bi bi-plus-circle-dotted"></i>
                </a>

                <?php
                if ($user_address_rows <= 0) {
                    print_r($user_address_rows);

                ?>

                    <div class="text-center my-4">
                        <h3 class="lead">No Address Saved.</h3>
                    </div>

                <?php
                } else {
                ?>

                    <div class="my-4">
                        <div class="my-4">
                            <?php
                            $indexes = 1;

                            foreach ($user_address as $address) {
                            ?>
                                <form method="POST" action="./update/edit-address.php" class="p-4 mb-4 border">
                                    <div class="mb-3 d-flex justify-content-between align-items-center">
                                        <h3 class="lead"> Address <?= $indexes; ?>: </h3>

                                        <?php
                                        if ($address->isDefault == 'yes') {
                                        ?>

                                            <p class="lead text-center fw-bolder text-light p-3 bg-primary">DEFAULT</p>

                                        <?php
                                        }
                                        ?>
                                        <div class="button-group">
                                            <button type="button" class="make-default-btn btn btn-outline-primary me-3">Make Default</button>
                                            <button type="submit" name="edit_address" class="edit-address-btn btn btn-outline-success me-3">Edit</button>
                                            <button type="button" class="remove-address-btn btn btn-outline-danger me-3">Remove</button>
                                        </div>
                                    </div>

                                    <div class="border px-4 py-3 d-flex flex-row justify-content-between align-items-center">
                                        <div class="">
                                            <input type="hidden" name="address_postal_code" value="<?= $address->postalCode; ?>">
                                            <input type="hidden" name="address_houseNumber" value="<?= $address->house_number; ?>">
                                            <input type="hidden" name="address_landmark" value="<?= $address->landmark; ?>">
                                            <input type="hidden" name="complete_address" value="<?= $address->complete_address; ?>">
                                            <input type="hidden" name="address_id" value="<?= $address->address_id; ?>">
                                            <h3 class="lead">Complete Address: </h3>
                                            <span data-target="complete-address" class="fw-bold complete-address"><?= $address->complete_address; ?></span data-target="complete-address">
                                        </div>
                                        <div class="">
                                            <h3 class="lead">Landmark: </h3>
                                            <h5 class="fw-bold"><?= $address->landmark; ?></h5>
                                        </div>
                                    </div>
                                </form>

                            <?php

                                $indexes++;
                            }

                            ?>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>

    </main>

    <!-- Update Modal -->
    <div class="modal fade" id="update-address" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="../process/process-product.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <input type="hidden" name="complete_address_id" id="complete_address_id">

                    <h5 class="modal-title" id="staticBackdropLabel">Update Address: </span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-row justify-content-between align-items-center mb-3">
                        <div class="form-floating">
                            <select class="form-select" aria-placeholder="enter province" name="update_province" id="update_province">
                                <option value="0">Select Province</option>
                            </select>
                            <label class="form-label" for="province">Province: </label>
                        </div>
                        <div class="form-floating ">
                            <select class="form-select" name="municipality" id="municipality">
                                <option value="0">Select Municipality</option>
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
                            <input type="number" class="form-control" name="postal_code" id="postal_code" placeholder="enter postal code" required>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="update-product" name="update-product" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>

<?php
} else {
    header('Location: ../../../index.php');
}
require_once('../../includes/footer.php');

if (isset($_SESSION['address-message'])) : ?>
    <script>
        swal(
            "<?= $_SESSION['address-message']['title']; ?>",
            "<?= $_SESSION['address-message']['body']; ?>",
            "<?= $_SESSION['address-message']['type']; ?>"
        );
    </script>
<?php

endif;
unset($_SESSION['address-message']);

?>

<?php if(isset($_SESSION['users'])) : ?>

<script src="./scripts/session_timeout.js"></script>

<?php endif; ?>

<script src="../../scripts/address.js"></script>