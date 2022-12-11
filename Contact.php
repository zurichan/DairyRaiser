<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once './configs/database.php';
require_once './includes/classes.php';

$api = new MyAPI($main_conn);

$all_products = $api->Read('products', 'all');
$user_name;
$item_rows;

require_once './includes/remember_me.php';

if (isset($_SESSION['users'])) {
   $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $cart_items_row = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id, true);
   $user_name = $user_info[0]->firstname;

   $item_rows = $cart_items_row;
} else {
   $user_name = '';
   $item_rows = '';
}

$title = 'Contact Page | Dairy Raisers';

require_once './includes/header.php';
require_once './includes/navbar.php';
?>

<section class="d-flex flex-column gap-3 justify-content-center align-items-center text-center px-5"
   style="margin-top: 80px;">

   <h2 class="" style="font-family: Aquino;">Contact and Follow Us</h2>
   <div class="card text-nowrap px-4" style="width: 700px">
      <div class="card-body d-flex flex-column justify-content-center align-items-center">
         <h3 class="card-title" style="font-family: Public Sans ExBold;">Contact Info:</h3>
         <p class="card-text">Phone Number : 0949-851-9213</p>
         <p class="card-text">Email Address : dairyraiser@gmail.com</p>
         <p class="card-text">Address : Brgy. Sanatiago, General Trias City, Cavite, Philippines - 4107</p>
      </div>
      <div class="card-footer">
         <h4>Follow us in:</h4>
         <a href="https://www.facebook.com/gentridairy"><i class="bi bi-facebook text-primary fs-1"></i></a>
      </div>
   </div>
</section>

<!-- FOOTER -->
<?php

require_once('./includes/footer.php');

if (isset($_SESSION['index-message'])) : ?>
<script>
swal(
   "<?= $_SESSION['index-message']['title']; ?>",
   "<?= $_SESSION['index-message']['body']; ?>",
   "<?= $_SESSION['index-message']['type']; ?>"
);
</script>
<?php

endif;
unset($_SESSION['index-message']);
?>