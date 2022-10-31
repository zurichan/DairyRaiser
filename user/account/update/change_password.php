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

    $user_name = $user_info[0]->firstname;
    $title = 'Change Your Password | Dairy Raisers';
    require_once '../../../includes/header.php';
    require_once '../../../includes/navbar.php';

?>

<main style="margin-top: 70px; width: 800px" class="container-fluid">
   <div style="width: 100%;" class="d-flex justify-content-center align-items-stretch border">
      <!-- SIDEBAR -->
      <?php require_once '../../../includes/sidebar_settings.php'; ?>
      <!-- content -->
      <div class="col-sm bg-light p-4">
         <h4 class="m-auto" style="font-family: Public Sans ExBold;">CHANGE YOUR PASSWORD</h4>
         <hr class="opacity-25 text-secondary">
         <form action="../../../validation/user_update.php" method="POST" class="px-3">
            <!-- CURRENT PASSWORD -->
            <div class="form-group input-group mb-3">
               <div class="form-floating form-floating-group flex-grow-1">
                  <input placeholder="type password" type="password" name="current_password" id="current_password"
                     class="form-control">
                  <label for="current_password">Current Password:</label>
               </div>
               <button type="button" id="current_pass-icon-click" class="btn btn-lg btn-outline-dark">
                  <i id="current_pass-eye-icon" class="bi bi-eye-fill"></i>
               </button>
            </div>
            <!-- NEW PASSWORD -->
            <div class="form-group input-group mb-3">
               <div class="form-floating form-floating-group flex-grow-1">
                  <input placeholder="type password" type="password" name="new_password" id="new_password"
                     class="form-control">
                  <label for="new_password">New Password:</label>
               </div>
               <button type="button" id="new_pass-icon-click" class="btn btn-lg btn-outline-dark">
                  <i id="new_pass-eye-icon" class="bi bi-eye-fill"></i>
               </button>
            </div>
            <!-- RETYPE NEW PASSWORD -->
            <div class="form-group input-group mb-3">
               <div class="form-floating form-floating-group flex-grow-1">
                  <input placeholder="type password" type="password" name="rnew_password" id="rnew_password"
                     class="form-control">
                  <label for="rnew_password">Retype New Password:</label>
               </div>
               <button type="button" id="rnew_pass-icon-click" class="btn btn-lg btn-outline-dark">
                  <i id="rnew_pass-eye-icon" class="bi bi-eye-fill"></i>
               </button>
            </div>
            <div class="d-flex justify-content-between align-items-center">
               <button type="submit" name="change_user_password" class="btn btn-primary mt-2">Change Password</button>
               <a href="../profile.php" class="btn btn-danger"><i class="fa-solid fa-rotate-left"></i> Back</a>
            </div>
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

<?php if (isset($_SESSION['users'])) : ?>

<script src="./scripts/session_timeout.js"></script>

<?php endif; ?>

<script src="../../../scripts/user.profile.js"></script>