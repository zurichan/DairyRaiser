<?php

session_start();
date_default_timezone_set('Asia/Manila');
require_once '../../../configs/database.php';
require_once '../../../includes/classes.php';
$api = new MyAPI($main_conn);

require_once '../../../includes/remember_me.php';

if (isset($_SESSION['users'])) {

   $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $item_rows = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id, true);
   $all_cart_items = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id);
   $user_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $user_address_rows = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id, true);

   $user_name = $user_info[0]->firstname;
   $title = 'Add Address | Dairy Raisers';
   require_once '../../../includes/header.php';
   require_once '../../../includes/navbar.php';

   $json_data = file_get_contents("../../../scripts/cavite.json");

   $locations = json_decode($json_data, JSON_OBJECT_AS_ARRAY);

   if ($user_address_rows == 3) {

      $_SESSION['address-message'] = array(
         "title" => 'You have reach limit adding address.',
         "body" => '',
         "type" => 'error'
      );

      header('Location: ../addresses.php');
   }

?>

<main style="margin-top: 70px; width: 800px;" class="container-fluid">
   <div style="width: 100%;" class="d-flex justify-content-center align-items-stretch border">
      <!-- SIDEBAR -->
      <?php require_once '../../../includes/sidebar_settings.php'; ?>

      <!-- MAIN CONTENT -->
      <div style="width: 100%;" class="h-100 p-3" style="font-family: Public Sans Light;">
         <h4 class="m-auto" style="font-family: Public Sans ExBold;">ADD ADDRESS</h4>
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
                  <input type="number" class="form-control" oninput="maxlength(this)" name="postal_code"
                     id="postal_code" placeholder="enter postal code" required>
                  <label class="form-label" for="postal_code">Postal Code:</label>
               </div>
            </div>
            <div class="mb-3">
               <div class="form-floating ">
                  <input type="text" class="form-control" name="house_number" id="house_number"
                     placeholder="enter postal code" required>
                  <label class="form-label" for="house_number">House/Building/Street Number, Street Name</label>
               </div>
            </div>
            <div class="mb-4">
               <div class="form-floating ">
                  <input type="text" class="form-control" name="near_landmark" id="near_landmark"
                     placeholder="enter postal code">
                  <label class="form-label" for="near_landmark">Nearest Landmark (Optional)</label>
               </div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
               <button type="submit" name="add-address-process" class="btn btn-outline-primary">Add Address</button>
               <a href="../../../user/account/addresses.php" class="btn btn-danger"><i
                     class="fa-solid fa-rotate-left"></i> back</a>
            </div>
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