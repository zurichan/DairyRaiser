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

   $total_qty = 0;
   foreach ($all_cart_items as $item) {
      $total_qty += $item->quantity;
   }
   $portion = '';
   ($total_qty > 1) ? $portion = 'pcs' : $portion = 'pc';

   $user_name = $user_info[0]->firstname;
   $title = 'Shopping Cart | Dairy Raisers';
   require_once '../includes/header.php';
   require_once '../includes/navbar.php';
?>

<!-- CART CONTENTS -->
<main class="container-fluid p-3" style="margin-top: 90px;">
   <div class="p-3 bg-light">
      <div class="d-flex justify-content-evenly align-items-center gap-2">
         <?php
            if ($item_rows !== 0) {
            ?>
         <table class="table caption-top table-striped table-bordered text-center" id="cartTable" style="width: 100%;">
            <thead class="text-center">
               <tr>
                  <th>#</th>
                  <th>Product</th>
                  <th>Unit Price</th>
                  <th>Quantity</th>
                  <th>Item Subtotal</th>
                  <th>Action</th>
               </tr>
            </thead>
            <tbody class="text-center">
               <?php
                     $index = 1;
                     foreach ($all_cart_items as $item) {
                        $products_byID = $api->Read('products', 'set', 'product_id', $item->product_id);
                     ?>
               <tr class="cart_item fw-bold">
                  <td style="vertical-align: middle;">
                     <?= $index; ?>.
                  </td>
                  <!-- PRODUCT -->
                  <td style="vertical-align: middle;">
                     <img class="img-fluid logo" src="<?= $products_byID[0]->img_url; ?>" alt="" srcset="">
                     <p class="lead2 productname"><?= $products_byID[0]->productname; ?></p>
                  </td>
                  <!-- UNIT PRICE -->
                  <td style="vertical-align: middle;">
                     <p>₱<?= $products_byID[0]->price; ?>.00</p>
                  </td>
                  <!-- QUANTITY -->
                  <td style="vertical-align: middle;" class="cart-item">
                     <div class="input-group input-group-sm">
                        <button type="button" class="input-group-text decrement-btn">-</button>
                        <input type="number" class="form-control text-center quantity" style="width: 5px;" id="quantity"
                           name="quantity" value="<?= $item->quantity; ?>">
                        <button type="button" class="input-group-text increment-btn">+</button>
                     </div>
                  </td>
                  <!-- ITEM SUB TOTAL -->
                  <td style="vertical-align: middle;">
                     <span class="sumProducts">₱<?= $item->total_unitPrice;  ?>.00</span>
                  </td>
                  <!-- ACTIONS -->
                  <td style="vertical-align: middle;">
                     <button type="button" class="remove-cartItem btn btn-lg text-danger"><i
                           class="bi bi-x-circle-fill"></i></button>
                  </td>
               </tr>
               <?php
                        $index++;
                     }
                     ?>
            </tbody>
         </table>
         <div class="align-self-start">
            <h1 class="text-center mb-3" style="font-family: Aquino;">Your Shopping Cart</h1>
            <form action="./checkout.php" method="POST" class="mt-3 py-3 px-5 d-flex flex-row justify-content-between">
               <div class="card" style="font-family: Public Sans Light;">
                  <div class="card-body pt-4">
                     <div class="px-1 mb-4 d-flex flex-column justify-content-between align-items-center">
                        <p class="text-primary" style="font-size: 14px;">Available Payments:</p>
                        <div class="row">
                           <div class="col d-flex align-items-center">
                              <p class="text-muted d-flex align-items-center"
                                 style="font-size: 12px;font-family: Public Sans Light;"> <i
                                    class='bx bxs-bank bx-tada-hover me-2' style="font-size: 18px;"></i> Bank</p>
                           </div>
                           <div class="col d-flex align-items-center">
                              <p class="text-muted d-flex align-items-center"
                                 style="font-size: 12px;font-family: Public Sans Light;"> <i
                                    class='bx bxs-credit-card-front bx-tada-hover me-2' style="font-size: 18px;"></i>
                                 Credit / Debit</p>
                           </div>
                           <div class="col d-flex align-items-center">
                              <p class="text-muted d-flex align-items-center"
                                 style="font-size: 12px;font-family: Public Sans Light;"> <i
                                    class='bx bxs-wallet bx-tada-hover me-2' style="font-size: 18px;"></i> Digital
                                 Wallet</p>
                           </div>
                           <div class="col d-flex align-items-center">
                              <p class="text-muted d-flex align-items-center"
                                 style="font-size: 12px;font-family: Public Sans Light;"> <i
                                    class='bx bxs-hand bx-tada-hover me-2' style="font-size: 18px;"></i> Cash on
                                 Delivery</p>
                           </div>
                        </div>
                     </div>
                     <div class="border-top pt-3 d-flex justify-content-evenly align-items-center">
                        <div class="text-muted d-flex flex-column justify-content-center align-items-start gap-2 mb-3">
                           <h6 class="card-text" style="font-size: 15px;">Item: <span style="font-family: Kayak Bold"
                                 class=""><?= $item_rows; ?></span> </h6>
                           <h6 class="card-text" style="font-size: 15px;">Quantity: <span
                                 style="font-family: Kayak Bold;" class=""><?= $total_qty . ' ' . $portion; ?></span>
                           </h6>
                        </div>
                        <div class="">
                           <h5 class="card-text text-primary " style="font-family: Public Sans ExBold;">Order Total:
                           </h5>
                           <h2 style="font-family: Kayak Bold; font-size: 25px;" class="">
                              ₱<?= $user_shopping_session[0]->total; ?>.00</h2>
                        </div>
                     </div>
                     <div class="mt-3 py-3 card-footer text-center">
                        <button type="submit" class="btn btn-primary">Checkout</button>
                     </div>
                  </div>
                  <!-- <div class="d-flex flex-column justify-content-center align-items-stretch">
                                <h5 class="total-order-pieces">Order Total:</h5>
                                <h2 class="total-order-price text-warning">₱<?= $user_shopping_session[0]->total; ?>.00</h2>
                                <button type="submit" name="checkout" class="btn btn-warning">checkout</button>
                            </div> -->
            </form>
         </div>
         <?php
            } else {
            ?>
         <main class="container-fluid d-flex flex-column justify-content-center align-items-center">
            <span class="lead fs-2 mt-2">No Items on Cart.</span>
            <img src="./img/no-cart-item.png" class="img-fluid" alt="no cart item">
         </main>
         <?php
            }
            ?>
      </div>
   </div>
</main>

<!-- FOOTER -->
<?php

   require_once '../includes/footer.php';
} else {
   $_SESSION['index-message'] = array(
      "title" => 'Sign Up Now!',
      "body" => 'Please Log In First',
      "type" => 'error'
   );
   header('Location: ../home.php');
} ?>

<script src="../scripts/cart.js"></script>
<script>
$(document).ready(() => {
   $('#cartTable').DataTable();
})
</script>