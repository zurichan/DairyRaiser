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

$title = 'FAQ | Dairy Raisers';

require_once './includes/header.php';
require_once './includes/navbar.php';
?>

<section class="col d-grid gap-5 text-center px-5" style="margin-top: 80px;">

   <h2 class="" style="font-family: Aquino;">Frequently Asked</h2>
   <div class="col">
      <h4>🐮 What time do you deliver?</h4>
      <div class="content">
         <p>Delivery times differ depending on the area.
            We typically start our deliveries at 8 am and end at 6 pm.</p>
      </div>
   </div>
   <div class="col">
      <h4>🐮 Do you offer discounts for bulk buying?</h4>
      <div class="content">
         <p>Yes, we sure do. We don't operate off a set-price list.
            A pricing structure is prepared specifically for each individual
            customer based on the quantity and range of products.</p>
      </div>
   </div>
   <div class="col">
      <h4>🐮 Do you have a physical store?</h4>
      <div class="content">
         <p>Yes, we have. Check our Address on the Contact page.</p>
      </div>
   </div>
   <div class="col">
      <h4>🐮 What is the right temperature at which to store milk?</h4>
      <div class="col">
         <p>Milk should be stored at or below 2° C to ensure it stays fresh for as long as possible.
            Storage above this temperature will drastically reduce the shelf life of the milk.
            If you leave milk on the counter for 1 hour, it loses as much freshness as it does one day in the
            refrigerator.</p>
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