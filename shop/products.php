<?php

session_start();
date_default_timezone_set('Asia/Manila');
ob_start();

require_once '../configs/database.php';
require_once '../includes/classes.php';
$api = new MyAPI($main_conn);

require_once '../includes/remember_me.php';

$all_products = $api->Read('products', 'all');
$user_name;
$item_rows;

$title = '';

if (isset($_GET['page'])) {
   $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS);
   $pageArr = array('all', 'item');
   $err = 0;
   (!in_array($page, $pageArr)) ? $err++ : NULL;

   if ($err == 0) {
      switch ($page) {
         case 'all':
            $title = 'Products | Dairy Raisers';
            break;
         case 'item':
            $title = 'Item | Dairy Raisers';
            break;
      }
   } else {
      $title = '404 Page Not Found | Dairy Raisers';
   }
}

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

require_once '../includes/header.php';
require_once '../includes/navbar.php';
?>

<?php if (isset($page) && $page == 'all') { ?>

<main style="margin-top: 55px;" class="bg-light container-fluid p-4">
   <div class=" mb-1 d-flex flex-column justify-content-center align-items-center gap-1">
      <h1 style="font-family: Aquino;font-size: 50px;" class="text-primary text-center">Shop at Dairy Raisers</h1>
      <h5 style="font-family: Aquino; letter-spacing: 6px;" class="text-center first-letter-head">This is Sob Title.
      </h5>
      <a href="#" class="lead text-center nav-link p-0 my-3" style="font-size: 16px;">Terms of Services</a>
   </div>
   <div style="background-color: #fff;" class="border d-flex justify-content-between align-items-start p-3 gap-4">
      <div class="mx-0 border-top border-bottom py-3 h-100">
         <h5 style="white-space: nowrap;font-family: Public Sans Light;" class="text-center border-bottom pb-3">Category
            <i class="bi bi-tags text-primary"></i> :
         </h5>
         <div class="d-flex flex-column my-4 gap-2">
            <div class="form-check user-select-none pe-auto">
               <input class="form-check-input" type="checkbox" value="" id="all-products-category" checked>
               <label class="form-check-label" for="all-products-category">
                  All Products
               </label>
            </div>
            <div class="form-check user-select-none pe-auto">
               <input class="form-check-input" type="checkbox" value="" id="milk-category" checked>
               <label class="form-check-label" for="milk-category">
                  Milk
               </label>
            </div>
            <div class="form-check user-select-none pe-auto">
               <input class="form-check-input" type="checkbox" value="" id="yoghurt-category" checked>
               <label class="form-check-label" for="yoghurt-category">
                  Yoghurt
               </label>
            </div>
            <div class="form-check user-select-none pe-auto">
               <input class="form-check-input" type="checkbox" value="" id="ice-cream-category" checked>
               <label class="form-check-label" for="ice-cream-category">
                  Ice Cream
               </label>
            </div>
            <div class="form-check user-select-none pe-auto">
               <input class="form-check-input" type="checkbox" value="" id="ice-candy-category" checked>
               <label class="form-check-label" for="ice-candy-category">
                  Ice Candy
               </label>
            </div>
            <div class="form-check user-select-none pe-auto">
               <input class="form-check-input" type="checkbox" value="" id="pastillas-category" checked>
               <label class="form-check-label" for="pastillas-category">
                  Pastillas
               </label>
            </div>
            <div class="form-check user-select-none pe-auto">
               <input class="form-check-input" type="checkbox" value="" id="cheese-category" checked>
               <label class="form-check-label" for="cheese-category">
                  Cheese
               </label>
            </div>
         </div>
      </div>
      <div class=" pt-0 pb-3 px-2 border-start row d-flex justify-content-start align-items-start p-0">
         <div class="d-flex justify-content-between align-items-center mb-2">
            <h4 style="font-family: Public Sans ExBold;" class="">Products</h4>
            <button type="button" class="btn bt-sm btn-dark rounded-pill py-0" id="help-tooltip-shop"
               data-bs-toggle="tooltip" data-bs-placement="left" title="This is a ToolTip Help Button."><i
                  class="bi bi-info-lg p-0"></i></button>
         </div>
         <?php
            foreach ($all_products as $product) :
               $product_stock = $api->Read('product_stocks', 'set', 'product_id', $product->product_id);

               if ($product_stock[0]->finished_goods > 0) {
            ?>
         <div class="col-6 col-md-4 col-lg-2 mb-3">
            <div class="card product-item">
               <a class="text-dark text-decoration-none px-0 py-0"
                  href="./products.php?page=item&item=<?= $product->productname; ?>">
                  <img src="./img/<?= $product->img_url; ?>" class="product-image img-fluid" alt="product">
                  <div class="border-top border-primary card-body text-center px-1 w-100"
                     style="font-family: Public Sans Light;font-size: 13px;white-space: no-wrap;">
                     <p class="card-text my-0 mb-2"><?= $product->productname; ?></p>
                     <p class="card-text text-primary">₱<?= $product->price; ?>.00</p>
                  </div>
               </a>
            </div>
         </div>
         <?php
               } else {
               ?>
         <div class="col-6 col-md-4 col-lg-2 mb-3">
            <a class="card product-item btn btn-outline-secondary disabled text-decoration-none text-dark px-0 py-0"
               href="./products.php?page=item&item=<?= $product->productname; ?>" tabindex="-1" role="button"
               aria-disabled="true">
               <div class="d-flex flex-column justify-content-center align-items-center border  h-100">
                  <span style="font-size: 35px;" class="mt-3"><i class="bi bi-x-octagon fw-bold"></i></span>
                  <p class="mb-4 text-center my-0" style="font-family: Public Sans ExBold; font-size: 15px;">Out of
                     Stock</p>
               </div>
               <div
                  class="border-top card-body d-flex flex-column justify-content-center align-items-center px-2 gap-1 h-100 w-100"
                  style="font-family: Public Sans Light;font-size: 12px;white-space: no-wrap;">
                  <p class="card-text my-0"><?= $product->productname; ?></p>
                  <p class="card-text">₱<?= $product->price; ?>.00</p>
               </div>
            </a>
         </div>
         <?php
               }
            endforeach;
            ?>
      </div>
   </div>
</main>
<?php } else if (isset($page) && $page == 'item') { ?>
<?php if (isset($_GET['item'])) {

      $item = filter_input(INPUT_GET, 'item', FILTER_SANITIZE_SPECIAL_CHARS);
      $get_product = $api->Read('products', 'set', 'productname', "'$item'");

      $_SESSION['productname'] = $item;

      $err = 0;

      if (!empty($get_product)) {
         $products_byName = $api->Read('products', 'set', 'productname', "'$item'");
         $product_stock = $api->Read('product_stocks', 'set', 'product_id', $products_byName[0]->product_id);
         ($product_stock[0]->finished_goods <= 0) ? $err++ : NULL;

         if ($err == 0) {
   ?>
<main class="container border mb-4" style="margin-top: 70px;">
   <div class=" bg-light d-flex justify-content-evenly align-items-stretch px-2 py-3">
      <div class="col-5 col-md-6 col-lg-6">
         <img src="<?= $products_byName[0]->img_url; ?>" class="img-fluid product-image" alt="product image">
      </div>

      <div class="border-start ps-3">
         <form class="h-100 text-wrap d-flex flex-column justify-content-between align-items-center"
            style="font-family: Public Sans Light;" method="POST" action="../validation/add-to-cart-process.php">
            <div class="border-bottom w-100 pb-4">
               <input type="hidden" name="product_id" value="<?= $products_byName[0]->product_id; ?>">
               <span class="lead text-muted" style="font-size: 14.5px;">category > milk</span>
               <h5 style="font-family: Public Sans ExBold; font-size: 30px;">
                  <?= $products_byName[0]->productname; ?></h5>
            </div>
            <div class="w-100 d-flex justify-content-between align-items-start py-3">
               <div class="d-flex flex-column justify-content-start align-items-start">
                  <p class="lead text-muted m-0 mb-2" style="font-size: 14.5px;">price:</p>
                  <p class=" text-primary m-0" style="font-family: Public Sans ExBold;font-size: 30px">₱
                     <?= $products_byName[0]->price ?>.00</p>
               </div>
               <div class="d-flex flex-column justify-content-center align-items-center">
                  <label class="form-label text-center text-muted" style="font-size: 14.5px;"
                     for="quantity">quantity:</label>
                  <div class="input-group" style="height: 15px;">
                     <button type="button" class=" input-group-text btn btn-outline-secondary decrement-btn">-</button>
                     <input type="number" name="quantity" id="quantity"
                        class=" form-control form-control-sm text-center" style="width: 50px;" id="quantity" value="1">
                     <button type="button" class=" input-group-text btn btn-outline-secondary increment-btn">+</button>
                  </div>
               </div>
            </div>
            <div class="w-100 d-flex justify-content-between align-items-center mb-4">
               <button type="submit" id="addtocart" class=" btn btn-primary d-flex align-items-center"
                  name="addtocart"><i class='bx bxs-cart-add bx-tada-hover bx-sm me-2'></i> Add to Cart</button>
               <a href="<?= $_SERVER['HTTP_REFERER']; ?>" class="btn btn-outline-danger d-flex align-items-center"><i
                     class='bx bx-rotate-right bx-spin-hover bx-sm me-2'></i> Go Back</a>
            </div>
            <div class="w-100 h-100 d-flex flex-column text-wrap border-top pt-4 pb-2">
               <h5 class="text-start mb-2" style="font-family: Public Sans ExBold;font-size: 20px;">Description:</h5>
               <p style="font-size: 14px;font-family: Public Sans Light;text-align: justify;">
                  <?= $products_byName[0]->description; ?></p>
            </div>
            <div class="d-flex flex-column justify-content-between align-items-center">
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
                           class='bx bxs-credit-card-front bx-tada-hover me-2' style="font-size: 18px;"></i> Credit /
                        Debit</p>
                  </div>
                  <div class="col d-flex align-items-center">
                     <p class="text-muted d-flex align-items-center"
                        style="font-size: 12px;font-family: Public Sans Light;"> <i
                           class='bx bxs-wallet bx-tada-hover me-2' style="font-size: 18px;"></i> Digital Wallet</p>
                  </div>
                  <div class="col d-flex align-items-center">
                     <p class="text-muted d-flex align-items-center"
                        style="font-size: 12px;font-family: Public Sans Light;"> <i
                           class='bx bxs-hand bx-tada-hover me-2' style="font-size: 18px;"></i> Cash on Delivery</p>
                  </div>
               </div>
            </div>
         </form>
      </div>
   </div>
</main>
<?php
         } else {
            $_SESSION['products-message'] = array(
               "title" => 'Out of Stock',
               "body" =>  $_SESSION['productname'],
               "type" => 'error'
            );
            header("Location: ./products.php?page=all");
            ob_end_flush();
            exit();
         }
      } else {
         require_once '../includes/404_page.php';
      }
   } else {
      require_once '../includes/404_page.php';
   }
   ?>
<?php } else {
   require_once '../includes/404_page.php';
} ?>

<!-- FOOTER -->
<?php

require_once('../includes/footer.php');

if (isset($_SESSION['products-message'])) : ?>
<script>
swal(
   "<?= $_SESSION['products-message']['title']; ?>",
   "<?= $_SESSION['products-message']['body']; ?>",
   "<?= $_SESSION['products-message']['type']; ?>"
);
</script>
<?php

endif;
unset($_SESSION['products-message']);
?>
<?php if (isset($page) && $page == 'all') { ?>
<script>
var exampleEl = document.getElementById('help-tooltip-shop')
var tooltip = new bootstrap.Tooltip(exampleEl, {
   boundary: document.body // or document.querySelector('#boundary')
})
</script>
<?php } else if (isset($page) && $page == 'item') { ?>
<script src="../scripts/quantity-btn.js"></script>
<?php } ?>