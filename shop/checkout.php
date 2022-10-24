<?php

session_start();
date_default_timezone_set('Asia/Manila');

if (isset($_SESSION['users'])) {

   require_once '../configs/database.php';
   require_once '../includes/classes.php';

   $api = new MyAPI($main_conn);

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
} else {
   header('../home.php');
}

?>

<?php
if (isset($_SESSION['users'])) {
   if ($user_address_rows > 0) {


?>

<!-- Update Modal -->
<div class="modal fade" id="select_address" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
   aria-labelledby="staticBackdropLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <form class="modal-content" method="POST" action="../process/process-product.php" enctype="multipart/form-data">
         <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Change Address: </span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <?php
                  $index = 1; ?>
            <div class="form-check mb-3">
               <input class="form-check-input" type="radio" name="Select_Address" id="<?= 'address' . $index; ?>"
                  checked>
               <label class="form-check-label" for="<?= 'address' . $index; ?>">
                  <?php
                        foreach ($user_address as $address) {
                           if ($address->isDefault == 'yes') {
                              echo $address->complete_address;
                              break;
                           }
                        }
                        $index++;
                        ?>
               </label>
            </div>
            <?php
                  foreach ($user_address as $address) {
                  ?>
            <?php
                     if ($address->isDefault !== 'yes') { ?>
            <div class="form-check mb-3">
               <input class="form-check-input" type="radio" name="Select_Address" id="<?= 'address' . $index; ?>">
               <label class="form-check-label" for="<?= 'address' . $index; ?>">
                  <?= $address->complete_address; ?>
               </label>
            </div>
            <?php
                     }
                  }
                  $index++;
                  ?>

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" id="update-product" name="select_addres" class="btn btn-primary">Select
               Address</button>
         </div>
      </form>
   </div>
</div>

<main style="font-family: Roboto;"
   class="border container p-4 mt-3 mb-3 bg-light d-flex justify-content-between align-items-center">
   <div class="w-100">
      <h1 class="fw-bolder">Checkout Products</h1>
   </div>
   <div class="w-100 p-3 bg-light text-black border border-primary rounded">
      <div class="mb-4 d-flex justify-content-between align-items-center">
         <h2 class="fw-bolder">DELIVERY ADDRESS :</h2>
         <button type="button" data-bs-target="#select_address" data-bs-toggle="modal" class="btn btn-primary">Change
            Address</button>
      </div>
      <hr>
      <div class="w-100 d-flex flex-column justify-content-between align-items-center">
         <div class="w-100 text-center d-flex justify-content-between align-items-center">
            <div class="w-100 text-center d-flex justify-content-start align-items-center">
               <p class="lead text-muted" style="font-size: 15px;">Full Name : </p>
               <p class="lead ms-2 fw-bold" style="font-size: 15px;">
                  <?= $_SESSION['users'][0]->firstname . ' ' . $_SESSION['users'][0]->lastname; ?></p>
            </div>
         </div>
         <div class="w-100 text-center d-flex justify-content-start align-items-center">
            <p class="lead text-muted" style="font-size: 15px;">Phone Number(+63) :</p>
            <p class="lead ms-2 fw-bold" style="font-size: 15px;"><?= $user_info[0]->mobile_no; ?></p>
         </div>
         <div class="w-100 text-center d-flex justify-content-start align-items-center">
            <p class="lead text-muted" style="font-size: 15px; text-overflow: ellipsis; white-space: nowrap;">Complete
               Address :</p>
            <p class="lead ms-2 fw-bold" style="font-size: 15px; text-overflow: ellipsis; white-space: nowrap;">
               <?php
                     foreach ($user_address as $address) {
                        if ($address->isDefault == 'yes') {
                           echo $address->complete_address;
                           break;
                        }
                     }
                     ?>
            </p>
         </div>
         <div class="w-100 text-center d-flex justify-content-start align-items-center">
            <p class="lead text-muted" style="font-size: 15px;">Landmark : </p>
            <p class="lead ms-2 fw-bold" style="font-size: 15px;"><?= $user_address[0]->landmark; ?></p>
         </div>
      </div>
   </div>
</main>
<main class="s container p-3 mt-3 mb-3 px-5 bg-light">
   <table class="table table-striped table-bordered mb-3">
      <thead class="text-center">
         <tr>
            <th>#</th>
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
            <td><?= $index; ?>.</td>
            <!-- PRODUCT -->
            <td colspan="" class="d-flex justify-content-center align-items-center">
               <img class="img-fluid logo" src="./img/<?= $productItem[0]->img_url; ?>" alt="" srcset="">
               <p class=" productname mx-3"><?= $productItem[0]->productname; ?></p>
            </td>
            <!-- UNIT PRICE -->
            <td colspan="s">
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
         <tr>
            <td rowspan="2" colspan="4">
               <form action="" method="POST" class="form row">
                  <div class="col form-floating">
                     <textarea class="form-control" name="instructions" id="instructions" maxlength="255" cols="10"
                        rows="1"></textarea>
                     <label for="instructions">Order Special Instructions: </label>
                  </div>
                  <div class="col form-floating">
                     <select class="form-control" name="payment_method" id="payment_method">
                        <option value="COD" selected>Cash on Delivery</option>
                     </select>
                     <label for="payment_method">Payment Method:</label>
                  </div>
               </form>
            </td>
         </tr>
         <tr class="text-end">
            <td colspan="4">
               <span class="total-order-pieces">Order Total (<?= $item_rows; ?> item):</span>
               <h4 class="total-order-price fw-bolder">₱<?= $user_shopping_session[0]->total; ?>.00</h4>
            </td>
         </tr>
      </tbody>
   </table>
</main>
<div class="border container py-3 px-5 bg-light">
   <div class="d-flex justify-content-between">
      <div class="d-flex flex-column">
         <span class="mb-2">Merchandise Total: </span>
         <span class="mb-2">Shipping Fee: </span>
         <span>Total Payment: </span>
      </div>
      <div class="d-flex flex-column">
         <span class="mb-2">₱<?= $user_shopping_session[0]->total; ?>.00</span>
         <span class="mb-2">₱50.00</span>
         <h4 class="text-primary fw-bolder">₱<?= $user_shopping_session[0]->total + 50; ?>.00</h4>
      </div>
   </div>
</div>
<form method="POST" action="../validation/checkout-process.php"
   class="border container d-flex justify-content-between py-3 px-5 bg-light">
   <div class="form-floating">
      <select class="form-select" name="payment_method" id="payment_method">
         <option value="COD" selected>Cash on Delivery</option>
      </select>
      <label for="payment_method">Payment Method:</label>
   </div>
   <a href="./cart.php" class="btn btn-danger">Go Back</a>
   <button type="submit" name="place-order" class="btn btn-primary">Place Order</button>
</form>

<?php
   } else {

      header('Location: ../user/account/addresses.php');
      exit();
   }
} else {
   header('Location: ../home.php');
   exit();
}
require_once '../includes/footer.php';
?>