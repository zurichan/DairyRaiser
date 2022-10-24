<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once './configs/database.php';
require_once './includes/classes.php';

$api = new MyAPI($main_conn);

$all_products = $api->Read('products', 'all');
$user_name;
$item_rows;

$ip_address = $api->IP_address();
$remember_me = $api->Read('remember_me', 'set', 'ip_address', "$ip_address");
if (!empty($remember_me)) {
    $email = $remember_me[0]->email;
    $get_user = $api->Read('user', 'set', 'email', "$email");
    $_SESSION['users'] = $get_user;
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

$title = 'Home Page | Dairy Raisers';

require_once './includes/header.php';
require_once './includes/navbar.php';
?>

<!-- <script src="https://www.paypal.com/sdk/js?client-id=AQgNY8h_OleHBW0NqUKKc8l2YWqTqqabExw_NZp8s4AHa6QaeFBMiqwMNMZekCmCrMZbdd1XvDXwBm1p"></script> -->

<main class="container-fluid intro_section" style="z-index: 0;">
    <div class="glide">
        <div class="glide__track" data-glide-el="track">
            <ul class="glide__slides">
                <li class="glide__slide position-relative ">
                    <div class="jumbotron d-flex flex-column justify-content-center align-items-center">
                        <img src="../img/company-logo.png" class="img-fluid" style="width: 130px;" alt="company logo">

                        <!-- <h1 class="text-center">Welcome to Dairy Raisers!</h1>
                        <h5>General Trias Dairy Raisers Multipurpose Cooperative</h5> -->
                    </div>
                    <img src="../img/farm.jpg" class="background_img" alt="farm background">
                </li>
                <li class="glide__slide position-relative ">
                    <div class="jumbotron d-flex flex-column justify-content-center align-items-center">
                        <h1>Pure by Nature</h1>
                        <h5>It does a body good!</h5>
                    </div>
                    <img src="../img/farm2.jpg" class="background_img" alt="farm background">
                </li>
                <li class="glide__slide">
                    <div class="jumbotron d-flex flex-column justify-content-center align-items-center">
                        <h1>Have You Had Your Dairy Today?</h1>
                        <h5>Buffalo Milk is a Nutritional Purchase</h5>
                    </div>
                    <img src="../img/farm5.jpg" class="background_img" alt="farm background">
                </li>
            </ul>
        </div>

        <div class="glide__arrows" data-glide-el="controls">
            <button class="glide__arrow glide__arrow--left" data-glide-dir="<"><i class="bi bi-arrow-left-circle-fill fs-2 fw-bolder"></i></button>
            <button class="glide__arrow glide__arrow--right" data-glide-dir=">"><i class="bi bi-arrow-right-circle-fill fs-2 fw-bolder"></i></button>
        </div>

        <div class="glide__bullets" data-glide-el="controls[nav]">
            <button class="glide__bullet" data-glide-dir="=0"></button>
            <button class="glide__bullet" data-glide-dir="=1"></button>
            <button class="glide__bullet" data-glide-dir="=2"></button>
        </div>
    </div>
</main>

<main class="container-fluid p-3 mt-2">
    <div class="p-3 bg-light">
        <form action="./validation/intro_section.php" method="POST" class="p-2  d-flex justify-content-start align-items-stretch">
            <div class="col-1 me-3 form-floating">
                <select class="form-control" name="intro_filter" id="intro_filter">
                    <option value="All" selected>All</option>
                    <option value="Milk">Milk</option>
                    <option value="Yoghurt">Yoghurt</option>
                    <option value="Pastillas">Pastillas</option>
                    <option value="Ice Cream">Ice Cream</option>
                </select>
                <label for="intro_filter">Filter :</label>
            </div>
            <div class="col-4 form-floating">
                <input type="search" class="form-control" name="intro_search" id="intro_search" placeholder="Search">
                <label for="intro_search">Search :</label>
            </div>
        </form>
        <!-- <div id="paypal-button-container"></div> -->
        <div class="row p-3" id="ProductData">
            <?php
            foreach ($all_products as $product) :
                $product_stock = $api->Read('product_stocks', 'set', 'product_id', $product->product_id);
                if ($product_stock[0]->finished_goods > 0) {
            ?>
                    <div class="col-6 col-md-4 col-lg-2 mb-4">
                        <a class="card btn btn-outline-primary text-decoration-none link-dark" href="./product.php?item=<?= $product->productname; ?>">
                            <img src="./img/<?= $product->img_url; ?>" class="product-image img-fluid" alt="product">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= $product->productname; ?></h5>
                                <p class="card-text">₱<?= $product->price; ?>.00</p>
                            </div>
                        </a>
                    </div>
                <?php
                } else {
                ?>
                    <div class="col-6 col-md-4 col-lg-2 mb-4">
                        <a class="card none_stock btn btn-secondary opacity-50 text-decoration-none link-dark" href="./product.php?item=<?= $product->productname; ?>">
                            <i class="bi bi-x-octagon stock_ban fw-bolder product-image"></i>
                            <p class="lead">Out of Stock</p>
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= $product->productname; ?></h5>
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

<script>
    const config = {
        type: "carousel",
        perView: 1
    }
    new Glide('.glide', config).mount()

    // paypal.Buttons({
    //     createOrder: (data, actions) => {

    //         return actions.order.create({
    //             purchase_units: [{
    //                 amount: {
    //                     value: "100",
    //                     current_code : "PHP"
    //                 },
    //             }]
    //         })
    //     },
    //     onApprove: (data, actions) => {
    //         console.log('Data = '+data);
    //         console.log('Actions = '+actions);

    //         return actions.order.capture().then((details) => {
    //             console.log('Details = '+details);
    //         })
    //     }
    // }).render('#paypal-button-container');
</script>