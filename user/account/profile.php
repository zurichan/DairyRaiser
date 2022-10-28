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
<main style="margin-top: 70px; width: 800px" class="container-fluid">
   <div style="width: 100%;" class="d-flex justify-content-center align-items-stretch border">
      <!-- SIDEBAR -->
      <?php require_once '../../includes/sidebar_settings.php'; ?>
      <!-- MAIN CONTENT -->
      <div style="width: 100%;" class="h-100 p-3" style="font-family: Public Sans Light;">
         <h4 class="m-auto" style="font-family: Public Sans ExBold;">YOUR PROFILE</h4>
         <p class="fs-5">Edit your account</p>
         <hr class="opacity-25 text-secondary">
         <form action="../../validation/user_update.php" method="POST"
            class="mt-4 d-flex justify-content-evenly align-items-center">
            <div class="d-flex justify-content-center align-items-center flex-column">
               <div class="form-floating mb-3">
                  <input type="text" name="fname" id="fname" value="<?= $user_info[0]->firstname; ?>"
                     class="form-control" placeholder="first name" required>
                  <label for="fname">First Name</label>
               </div>
               <div class="form-floating mb-3">
                  <input type="text" name="lname" id="lname" value="<?= $user_info[0]->lastname; ?>"
                     class="form-control" placeholder="last name" required>
                  <label for="lname">Last Name</label>
               </div>
               <div class="form-floating">
                  <input type="number" name="phone_number" id="phone_number" value="<?= $user_info[0]->mobile_no; ?>"
                     oninput="maxlength(this)" class="form-control" required>
                  <label for="phone_number">(+63) Phone Number</label>
               </div>
            </div>
            <div class="d-flex h-100 gap-3 flex-column justify-content-start align-items-start text-center">
               <div class=" bg-primary text-light p-3 rounded">
                  <h5 class="lead" style="font-family: Public Sans ExBold;">Email Address <i
                        class="fa-regular fa-envelope ms-3"></i></h5>
                  <p><?= $user_info[0]->email; ?></p>
               </div>
               <div class=" btn-group-vertical">
                  <a href="./update/change_password.php" class="btn btn-danger mb-2">Change Password <i
                        class="bi bi-link-45deg"></i></a>
                  <button type="submit" name="update_info" class="btn btn-success">Update Info <i
                        class="fa-solid fa-circle-info"></i></button>
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