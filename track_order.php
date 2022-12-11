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
   $user_order_details = $api->Read('order_details', 'set', 'user_id', $user_info[0]->user_id);
   $user_order_track = $api->Read('order_track', 'set', 'user_id', $user_info[0]->user_id);
   $user_order_item = $api->Read('order_items', 'set', 'user_id', $user_info[0]->user_id);

   $order_details_row = $api->Read('order_details', 'set', 'user_id', $user_info[0]->user_id, TRUE);
   $order_track_row = $api->Read('order_track', 'set', 'user_id', $user_info[0]->user_id, TRUE);
   $order_item_row = $api->Read('order_items', 'set', 'user_id', $user_info[0]->user_id, TRUE);
} else {
   $user_name = '';
   $item_rows = '';
}
function order_validate($key, $target)
{
   $result = '';
   if ($target == 0) {
      $result = 'active';
   } else {
      if ($key >= $target) {
         $result = 'active';
      } else {
         $result = '';
      }
   }

   return $result;
}

$title = 'Track Your Order | Dairy Raisers';

require_once './includes/header.php';
require_once './includes/navbar.php';



if ($order_details_row > 0 && $order_track_row > 0 && $order_item_row > 0) {
?>

<!-- MAIN CONTENT -->
<section class="px-5" style="margin-top: 75px;">
   <h3 class="text-center mb-4" style="font-family: Aquino;">Track Your Orders</h3>
   <p class="text-center mb-4">Please refresh the page to keep updated.</p>
   <?php
      $index = 1;
      foreach ($user_order_track as $order_track) :
         $status_arr = array('order placement', 'preparing', 'on the way', 'delivered');
         $k = array_search($order_track->order_status, $status_arr);

      ?>
   <div class="card mb-5">
      <div class="card-body">
         <h5 class="text-center card-title" style="font-family: Public Sans ExBold;">Order ID :
            <?= $order_track->order_details_id; ?>
         </h5>
         <div class="d-flex justify-content-center align-items-center">
            <div class="wrapper">
               <div class="margin-area">
                  <div class="dot one <?= order_validate($k, 0); ?>"><i class="fa-solid fa-cart-arrow-down"
                        style="font-size: 20px;"></i></div>
                  <div class="dot two <?= order_validate($k, 1); ?>"><i class="fa-solid fa-spinner"
                        style="font-size: 20px;"></i>
                  </div>
                  <div class="dot three <?= order_validate($k, 2); ?>"><i class="fa-solid fa-truck"
                        style="font-size: 20px;"></i>
                  </div>
                  <div class="dot four <?= order_validate($k, 3); ?>"><i class="fa-solid fa-check-to-slot"
                        style="font-size: 20px;"></i></div>
                  <div class="progress-bar first <?= order_validate($k, 1); ?>"></div>
                  <div class="progress-bar second <?= order_validate($k, 2); ?>"></div>
                  <div class="progress-bar third <?= order_validate($k, 3); ?>"></div>
                  <div class="message message-1 <?= order_validate($k, 0); ?>" style="font-family: Public Sans Light;">
                     Order
                     Placed<div>
                        <div class="message message-2 <?= order_validate($k, 1); ?>"
                           style="font-family: Public Sans Light;">
                           Preparing<div>
                              <div class="message message-3 <?= order_validate($k, 2); ?>"
                                 style="font-family: Public Sans Light;">Out for delivery<div>
                                    <div class="message message-4 <?= order_validate($k, 3); ?>"
                                       style="font-family: Public Sans Light;">Delivered<div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card-footer d-flex flex-column align-items-center jusitfy-content-center">
         <div class="collapse" id="navbarToggleExternalContent<?= $index; ?>">
            <div class="bg-light p-4">
               <table class="border table table-sm table-borderless table-light" style="width: 500px; font-size: 14px;">
                  <thead>
                     <tr>
                        <td>
                           <p class="fw-bolder">Placed on <?= $order_track->date; ?></p>
                        </td>
                     </tr>
                     <tr class="border">
                     </tr>
                     <tr>
                        <td>
                           <p>Delivery from <?= $order_track->user_address; ?></p>
                           Order Details:
                        </td>
                     </tr>
                     <tr class="border">
                     </tr>
                     <tr>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                     </tr>
                  </thead>
                  <tr class="border">
                  </tr>
                  <tbody style="font-size: 14px;">
                     <?php
                           foreach ($user_order_item as $item) :
                              if ($item->order_details_id == $order_track->order_details_id) :
                                 $product = $api->Read('products', 'set', 'product_id', $item->product_id);
                           ?>
                     <tr>
                        <td><?= $product[0]->productname; ?></td>
                        <td><?= $item->quantity; ?></td>
                        <td>₱<?= $item->total; ?>.00</td>
                     </tr>
                     <?php
                              endif;
                           endforeach;
                           ?>
                  </tbody>
                  <tr class="border">
                  </tr>
                  <tfoot>
                     <tr>
                        <td colspan="2">Sub Total</td>
                        <td>₱<?= $order_track->total; ?>.00</td>
                     </tr>
                     <tr>
                        <td colspan="2">Delivery Fee</td>
                        <td>₱50.00</td>
                     </tr>
                     <tr class="fw-bolder fs-5">
                        <td colspan="2">Total</td>
                        <td>₱<?= $order_track->total + 50; ?>.00</td>
                     </tr>
                  </tfoot>
               </table>
            </div>
         </div>
         <nav class="navbar navbar-light bg-light">
            <div class="container-fluid">
               <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                  data-bs-target="#navbarToggleExternalContent<?= $index; ?>"
                  aria-controls="navbarToggleExternalContent<?= $index; ?>" aria-expanded="false"
                  aria-label="Toggle navigation">
                  <span class="navbar-toggler-icon"></span>
               </button>
            </div>
         </nav>
      </div>
      <div class="col h-100 text-center">
         <p></p>
      </div>
   </div>
   <?php
         $index++;
      endforeach;
      ?>
</section>

<!-- FOOTER -->
<?php

   require_once('./includes/footer.php');
} else {
?>
<div class="container-fluid d-flex flex-column justify-content-center align-items-center w-100"
   style="margin-block: 100px;">
   <img src="./img/" class="img-fluid mb-4" style="width: 400px;" alt="page not found">
   <h1 style="font-family: Public Sans ExBold;" class="fw-bold">Page Not Found</h1>
</div>
<?php
}

if (isset($_SESSION['track-order-message'])) : ?>
<script>
swal(
   "<?= $_SESSION['track-order-message']['title']; ?>",
   "<?= $_SESSION['track-order-message']['body']; ?>",
   "<?= $_SESSION['track-order-message']['type']; ?>"
);
</script>
<?php

endif;
unset($_SESSION['track-order-message']);
?>

<script>
$(document).ready(() => {
   $('#trackorderTable').DataTable();
});
</script>