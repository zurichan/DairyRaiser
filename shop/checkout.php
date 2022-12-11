<?php

session_start();
date_default_timezone_set('Asia/Manila');
require_once '../configs/database.php';
require_once '../includes/classes.php';
$api = new MyAPI($main_conn);

require_once '../includes/remember_me.php';

if (isset($_SESSION['users'])) {

   $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $item_rows = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id, true);
   $all_cart_items = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id);
   $total_quantity = $api->Sum('shopping_session', 'set', 'total', 'session_id', $user_shopping_session[0]->session_id);
   $user_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $user_address_rows = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id, true);

   $user_name = $user_info[0]->firstname;
   $title = 'Checkout | Dairy Raisers';
   require_once '../includes/header.php';
   require_once '../includes/navbar.php';
   if ($user_address_rows > 0) {
      if ($all_cart_items > 0) {
?>
<!-- Update Modal -->
<div class="modal fade" id="select_address" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
   aria-labelledby="staticBackdropLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <form class="modal-content" method="POST" action="../validation/address-process.php"
         enctype="multipart/form-data">
         <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Change Address: </span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <?php
                     $index = 1;
                     foreach ($user_address as $address) {
                        if ($address->isDefault == 'yes') { ?>
            <div class="form-check mb-3">
               <input class="form-check-input" value="<?= $address->address_id; ?>" type="radio" name="select_address"
                  id="<?= 'address' . $index; ?>" checked>
               <label class="form-check-label" for="<?= 'address' . $index; ?>">
                  <?= $address->complete_address; ?>
               </label>
            </div>
            <?php
                        } else { ?>
            <div class="form-check mb-3">
               <input class="form-check-input" value="<?= $address->address_id; ?>" type="radio" name="select_address"
                  id="<?= 'address' . $index; ?>">
               <label class="form-check-label" for="<?= 'address' . $index; ?>">
                  <?= $address->complete_address; ?>
               </label>
            </div>
            <?php
                        }
                        $index++;
                     }
                     ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" id="update-product" name="change_address" class="btn btn-primary">Select
               Address</button>
         </div>
      </form>
   </div>
</div>

<main style="width: 100%; margin-top: 70px; padding-left: 180px; padding-right: 180px;">
   <section class="border p-3">
      <div style="font-family: Roboto;"
         class="container bg-light d-flex flex-column justify-content-between gap-3 align-items-center">
         <div class="d-flex justify-content-between gap-3 align-items-center">
            <div class="w-100 text-center px-3">
               <h1 style="font-family: Aquino;">Checkout Products</h1>
            </div>
            <div class="p-3 border rounded" style="font-family: Public Sans Light;">
               <div class=" d-flex justify-content-between align-items-center">
                  <h5 style="font-family: Public Sans ExBold;">Delivery Address :</h5>
                  <button type="button" data-bs-target="#select_address" data-bs-toggle="modal"
                     class="btn btn-sm btn-primary">Change
                     Address</button>
               </div>
               <hr>
               <div style="font-size: 13px;" class="w-100">
                  <div class="w-100 text-center d-flex justify-content-between align-items-center">
                     <p style="font-size: inherit; white-space: nowrap;" class="w-100 text-start"><span
                           class="text-muted">Full Name :
                        </span><span><?= $_SESSION['users'][0]->firstname . ' ' . $_SESSION['users'][0]->lastname; ?></span>
                     </p>
                     <p style="font-size: inherit; white-space: nowrap;" class="w-100 text-start"><span
                           class="text-muted">Phone Number(+63) :
                        </span><span><?= $user_info[0]->mobile_no; ?></span></p>
                  </div>
                  <p style="font-size: inherit; white-space: nowrap;" class="w-100 text-start">
                     <span class=" text-muted">
                        Complete
                        Address :</span>
                     <span>
                        <?php
                                 foreach ($user_address as $address) {
                                    if ($address->isDefault == 'yes') {
                                       echo $address->complete_address;
                                       break;
                                    }
                                 }
                                 ?>
                     </span>
                  </p>
                  <p style="font-size: inherit; white-space: nowrap;" class="w-100 text-start"><span
                        class="text-muted">Landmark : </span><span class=""><?= $user_address[0]->landmark; ?></span>
                  </p>
               </div>
            </div>
         </div>
         <div class="container my-2">
            <table class="table table-striped table-bordered" id="checkout-table" style="width: 100%;">
               <thead class="text-center">
                  <tr>
                     <th>Products</th>
                     <th>Unit Price</th>
                     <th>Quantity</th>
                     <th class="text-end">Item Subtotal</th>
                  </tr>
               </thead>
               <tbody class="text-center">
                  <?php
                           $index = 1;
                           foreach ($all_cart_items as $item) {
                              $productItem = $api->Read('products', 'set', 'product_id', $item->product_id);

                           ?>
                  <tr class="cart_item">

                     <!-- PRODUCT -->
                     <td>
                        <div class="d-flex justify-content-center align-items-center">
                           <img class="img-fluid logo" src="./img/<?= $productItem[0]->img_url; ?>" alt="" srcset="">
                           <p class=" productname mx-3"><?= $productItem[0]->productname; ?></p>
                        </div>
                     </td>
                     <!-- UNIT PRICE -->
                     <td>
                        <p>₱<?= $productItem[0]->price; ?>.00</p>
                     </td>
                     <!-- QUANTITY -->
                     <td>
                        <p><?= $item->quantity; ?></p>
                     </td>
                     <!-- ITEM SUB TOTAL -->
                     <td class="text-end">
                        <span class="sumProducts">₱<?= $item->total_unitPrice;  ?>.00</span>
                     </td>
                  </tr>
                  <?php
                              $index++;
                           } ?>
               </tbody>
               <tfoot class="fw-bolder" style="font-size: 15px;">
                  <td colspan="3" class="text-end">
                     Total:
                  </td>
                  <td class="text-end">
                     ₱<?= $user_shopping_session[0]->total; ?>.00
                  </td>
               </tfoot>
            </table>
         </div>
         <form method="POST" action="../validation/checkout-process.php" class="w-100 border rounded p-2">
            <div class="w-100 container p-3 d-flex justify-content-between align-items-stretch">
               <div class="w-100">
                  <div class="form-floating mb-2">
                     <textarea class="form-control form-control-sm" placeholder="Add a Instruction"
                        id="AddInstruction"></textarea>
                     <label for="AddInstruction">Additional Instruction</label>
                  </div>
                  <div class="form-floating">
                     <select class="form-select form-select-sm" name="payment_method" id="payment_method">
                        <option value="COD" selected>Cash on Delivery</option>
                     </select>
                     <label for="payment_method">Payment Method:</label>
                  </div>
               </div>
               <div class="w-100 d-flex flex-column justify-content-center align-items-end">
                  <p>Merchandise Total: <span>₱<?= $user_shopping_session[0]->total; ?>.00</span></p>
                  <p>Shipping Fee: <span>₱50.00</span></p>
                  <p>Total Payment: <span
                        class="fw-bolder text-primary text-decoration-underline">₱<?= $user_shopping_session[0]->total + 50; ?>.00</span>
                  </p>
               </div>
            </div>
            <hr>
            <div class="container d-flex gap-3 justify-content-end align-items-center py-2">
               <a href="./cart.php" class="btn btn-danger">Go Back</a>
               <button type="submit" name="place-order" class="btn btn-primary">Place Order</button>
            </div>
         </form>
      </div>
   </section>
</main>
<?php
      } else {
      ?>
<div class="container-fluid d-flex flex-column justify-content-center align-items-center w-100"
   style="margin-block: 100px;">
   <img src="../img/no-cart-item.png" class="img-fluid mb-4" style="width: 400px;" alt="page not found">
   <h2 style="font-family: Public Sans ExBold;" class="fw-bold mb-3">No Items on your Cart </h2>
   <a href="../shop/products.php?page=all" class="btn btn-primary">Shop Now!</a>
</div>
<?php
      }
   } else {
      ?>
<div class="container-fluid d-flex flex-column justify-content-center align-items-center w-100"
   style="margin-block: 100px;">
   <img src="../img/undraw_Directions_re_kjxs.png" class="img-fluid mb-4" style="width: 400px;" alt="page not found">
   <h2 style="font-family: Public Sans ExBold;" class="fw-bold mb-3">There is no Address set in your account </h2>
   <a href="../user/account/addresses.php" class="btn btn-primary">Set your Address Here</a>
</div>
<?php
   }
} else {
   header('Location: ../home.php');
   exit();
}
require_once '../includes/footer.php';
if (isset($_SESSION['checkout-message'])) : ?>
<script>
swal(
   "<?= $_SESSION['checkout-message']['title']; ?>",
   "<?= $_SESSION['checkout-message']['body']; ?>",
   "<?= $_SESSION['checkout-message']['type']; ?>"
);
</script>
<?php
endif;
unset($_SESSION['checkout-message']);
?>

<script>
$(document).ready(() => {
   $('#checkout-table').DataTable();
})
</script>