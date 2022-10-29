<?php

session_start();
date_default_timezone_set('Asia/Manila');
require_once '../../../configs/database.php';
require_once '../../../includes/classes.php';
$api = new MyAPI($main_conn);

require_once '../../../includes/remember_me.php';

if (!isset($_POST['edit_address'])) {
    header('Location: ../../../user/account/addresses.php');
    exit();
}

if (isset($_SESSION['users'])) {

    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $item_rows = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id, true);
    $all_cart_items = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id);
    $user_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $user_address_rows = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id, true);

    $user_name = $user_info[0]->firstname;
    $title = 'Edit Address | Dairy Raisers';
    require_once '../../../includes/header.php';
    require_once '../../../includes/navbar.php';

?>

<main style="margin-top: 70px; width: 800px;" class="container-fluid">
   <div style="width: 100%;" class="d-flex justify-content-center align-items-stretch border">
      <!-- SIDEBAR -->
      <?php require_once '../../../includes/sidebar_settings.php'; ?>
      <!-- contents -->
      <div style="width: 100%;" class="h-100 p-3" style="font-family: Public Sans Light;">
         <h4 class="m-auto" style="font-family: Public Sans ExBold;">EDIT ADDRESS</h4>
         <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="../addresses.php">Addresses</a></li>
               <li class="breadcrumb-item active" aria-current="page">Edit Address</li>
            </ol>
         </nav>
         <hr class="opacity-25 text-secondary">
         <form action="../../../validation/address-process.php" method="POST" class=" mb-4 px-1">
            <input type="hidden" name="address_id" value="<?= $_POST['address_id']; ?>">
            <p class="my-3">
               <span>Current Address:</span>
               <span style="font-family: Public Sans ExBold;" class="">
                  <?= $_POST['complete_address']; ?>
               </span>
            </p>
            <div class="d-flex flex-row justify-content-between align-items-center mb-3">
               <div class="form-floating ">
                  <select class="form-select" aria-placeholder="enter province" name="edit_province" id="province">
                     <option value="none">Select Province</option>

                  </select>
                  <label class="form-label" for="province">Province: </label>
               </div>
               <div class="form-floating">
                  <select class="form-select" name="edit_municipality" id="municipality">
                     <option value="none">Select Municipality</option>

                  </select>
                  <label class="form-label" for="municipality">Municipality: </label>
               </div>
               <div class="form-floating">
                  <select class="form-select" name="edit_barangay" id="barangay">
                     <option value="none" selected>Select Barangay</option>
                  </select>
                  <label class="form-label" for="barangay">Barangay: </label>
               </div>
               <div class="form-floating">
                  <input type="number" class="form-control" oninput="maxlength(this)"
                     value="<?= $_POST['address_postal_code']; ?>" name="edit_postal_code" id="postal_code"
                     placeholder="enter postal code" required>
                  <label class="form-label" for="postal_code">Postal Code:</label>
               </div>
            </div>
            <div class="mb-3">
               <div class="form-floating ">
                  <input type="text" class="form-control" name="edit_house_number"
                     value="<?= $_POST['address_houseNumber']; ?>" id="edit_house_number"
                     placeholder="enter postal code" required>
                  <label class="form-label" for="edit_house_number">House/Building/Street Number, Street Name</label>
               </div>
            </div>
            <div class="mb-4">
               <div class="form-floating ">
                  <input type="text" class="form-control" name="edit_near_landmark"
                     value="<?= $_POST['address_landmark']; ?>" id="edit_near_landmark" placeholder="enter postal code"
                     required>
                  <label class="form-label" for="edit_near_landmark">Near Landmark</label>
               </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
               <button type="submit" name="update_address" class="btn btn-outline-primary">Update Address</button>
               <a href="../../../user/account/addresses.php" class="btn btn-danger"><i
                     class="fa-solid fa-rotate-left"></i> back</a>
            </div>
         </form>
      </div>
   </div>
</main>
<?php
}
require_once '../../../includes/footer.php';

if (isset($_SESSION['edit-address-message'])) : ?>
<script>
swal(
   "<?= $_SESSION['edit-address-message']['title']; ?>",
   "<?= $_SESSION['edit-address-message']['body']; ?>",
   "<?= $_SESSION['edit-address-message']['type']; ?>"
);
</script>
<?php

endif;
unset($_SESSION['edit-address-message']);

?>

<script src="../../../scripts/address.js"></script>