<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once './configs/database.php';
require_once './includes/classes.php';

$api = new MyAPI($main_conn);

$all_products = $api->Read('products', 'all');

if (isset($_SESSION['users'])) {

    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $item_rows = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id, true);
    $all_cart_items = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id);
    $user_name = $user_info[0]->firstname;
} else {

    $user_name = '';
    $item_rows = '';
}

$title = 'Products | Dairy Raisers';
require_once './includes/header.php';
require_once './includes/navbar.php';

if (isset($_GET['item'])) {

    $item = $_GET['item'];
    $get_product = $api->Read('products', 'set', 'productname', "$item");

    $_SESSION['productname'] = $item;

    $err = 0;
    $stocks_err = 0;
    $get_product_err = 0;
    $same_item_err = 0;

    if (!empty($get_product)) {
        $products_byName = $api->Read('products', 'set', 'productname', $_GET['item']);
        $product_stock = $api->Read('product_stocks', 'set', 'product_id', $products_byName[0]->product_id);
        $find_item = $api->Read('cart_item', 'set', 'product_id', $get_product[0]->product_id);

        if ($product_stock[0]->finished_goods <= 0) {
            $err++;
            $stocks_err++;
        }
        if (!empty($find_item)) {
            $err++;
            $same_item_err++;
        }
        if ($err == 0) {
?>
            <main class="container p-3 mt-5">
                <div class="row bg-light p-4">
                    <div class="col-5 col-md-6 col-lg-6 mx-5">
                        <img src="<?= $products_byName[0]->img_url; ?>" class="img-fluid product-image" alt="product image">
                    </div>
                    <div class="col mx-3 d-flex flex-end">
                        <form class="text-wrap d-flex justify-content-evenly align-items-stretch flex-column" method="POST" action="../validation/add-to-cart-process.php">
                            <span class="lead fs-5 opacity-50">Products</span>
                            <h5 class="fs-3 productname mb-3"><?= $products_byName[0]->productname; ?></h5>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="fs-3 text-primary">₱<?= $products_byName[0]->price ?>.00</span>
                                <div class="d-flex justify-content-center align-items-center">
                                    <label class="form-label me-3" for="quantity">Quantity:</label>
                                    <div class="input-group">
                                        <button type="button" class="input-group-text decrement-btn">-</button>
                                        <input type="number" name="quantity" class="form-control text-center" id="quantity" value="1">
                                        <button type="button" class="input-group-text increment-btn">+</button>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="addtocart" class="mb-4 btn btn-sm btn-primary" name="addtocart">submit</button>
                            <div class="d-flex flex-column text-wrap">
                                <h5 class="fs-4">Description:</h5>
                                <p class="lead">
                                    <?= $products_byName[0]->description; ?>
                                    Lorem ipsum, dolor sit amet consectetur adipisicing elit. Voluptatibus accusantium pariatur sunt neque saepe minima perferendis! Beatae quia placeat, doloribus praesentium architecto quibusdam illum, voluptatem aut vitae tenetur illo earum?
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        <?php
        } else {

            if ($stocks_err == 1) {
                $_SESSION['index-message'] = array(
                    "title" => 'Out of Stock',
                    "body" =>  $_SESSION['productname'],
                    "type" => 'error'
                );
            } else if ($same_item_err == 1) {
                $_SESSION['index-message'] = array(
                    "title" => 'Item already on Cart',
                    "body" => 'Item: ' . $_SESSION['productname'] . ' is already on Cart.',
                    "type" => 'error'
                );
            }
            header('Location: ../index.php');
            ob_end_flush();
        }
    } else {
        ?>
        <div class="d-flex flex-column justify-content-around align-items-center my-4 w-100">
            <img src="./img/undraw_page_not_found_re_e9o6.svg" class="img-fluid mb-4" style="width: 400px;" alt="page not found">
            <h1 class="fw-bold">Page Not Found</h1>
        </div>
    <?php
    }
} else {
    ?>
    <div class="d-flex flex-column justify-content-around align-items-center my-4 w-100">
        <img src="./img/undraw_page_not_found_re_e9o6.svg" class="img-fluid mb-4" style="width: 400px;" alt="page not found">
        <h1 class="fw-bold">Page Not Found</h1>
    </div>
<?php
}
?>

<!-- FOOTER -->
<?php

require_once('./includes/footer.php');

if (isset($_SESSION['addtocart-message'])) : ?>
    <script>
        swal(
            "<?= $_SESSION['addtocart-message']['title']; ?>",
            "<?= $_SESSION['addtocart-message']['body']; ?>",
            "<?= $_SESSION['addtocart-message']['type']; ?>"
        );
    </script>
<?php endif;

unset($_SESSION['addtocart-message']);

?>

<script src="./scripts/quantity-btn.js"></script>