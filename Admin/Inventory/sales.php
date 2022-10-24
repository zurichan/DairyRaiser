<?php

session_start();
// INCLUDES DATABASE
require_once('../../configs/database.php');

require_once('../../libs/get_buffalo.php');

require_once('../../libs/all_products.php');

require_once('../../libs/all_flavor.php');

require_once('../includes/admin.sidebar.php');



// INCLUDES HEADER
require_once('../includes/admin.header.php');
echo Title('Dairy Raisers');

if (isset($_GET['reset-btn'])) {
    unset($_GET['reset-btn']);
    unset($_GET['product-search']);
    unset($_GET['product_filter']);
    header('Location: ./product.php');
}

?>

<main class="overflow-auto mx-auto d-block main-container resize">
<?php require_once('../includes/admin.topbar.php');
    echo topbar('Stocks'); ?>
    <!-- reports of products -->
    <div class="row m-4 lead">
        <div class="col-3 p-3 d-flex justify-content-between bg-light">
            <div class="d-flex flex-column text-center">
                <p class="fs-5 text-black-50">Total Products:</p>
                <h1 class="display-3"><strong><?= $get_all_products_STMT->rowCount(); ?></strong></h1>
            </div>
            <img src="../../img/milk.png" class="img-fluid milk m-2" alt="">
        </div>
        <div class="col-2 p-3 d-flex justify-content-center align-items-center mx-3 bg-light">
            <div class="d-flex flex-column text-center position-relative">
                <i class='bx bxs-dish position-absolute bg-sign'></i>
                <p class="fs-5 text-black-50">Best Products:</p>
                <h1 class="lead fs-1"><strong>Fresh Milk</strong></h1>
                <p class="notes text-black-75">based on current sales.</p>
            </div>
        </div>
        <div class="col-2 p-3 d-flex justify-content-center align-items-center bg-light">
            <div class="d-flex flex-column text-center position-relative">
                <i class='bx bxs-sticker bg-sign position-absolute'></i>
                <p class="fs-5 text-black-50">Total Product Stocks:</p>
                <h1 class="display-3"><strong></strong></h1>
                <p class="notes">.</p>
            </div>
        </div>
        <div class="col p-3 d-flex justify-content-evenly align-items-center mx-3 bg-light">
            <div class="d-flex flex-column text-center position-relative">
                <i class='bx bxs-chevrons-down bg-sign position-absolute'></i>
                <p class="fs-5 text-black-50">Current Low Stocks:</p>
                <h1 class="display-3"><strong></strong></h1>
                <p class="notes text-black-75">based on current sales.</p>
            </div>
            <div class="d-flex flex-column text-center position-relative">
                <i class='bx bxs-chevrons-up bg-sign position-absolute'></i>
                <p class="fs-5 text-black-50">Current High Stocks:</p>
                <h1 class="display-3"><strong></strong></h1>
                <p class="notes text-black-75">based on current sales.</p>
            </div>
        </div>
    </div>

    <div class="container-fluid px-5 mb-4">
    <table class="table table-bordered caption-top table-striped mt-4">
            <caption>List of Stocks</caption>
            <thead class="text-center">
                <tr>
                    <th>Number</th>
                    <th>Name</th>
                    <th>SKU</th>
                    <th></th>
                </tr>
            </thead>
    </table>
    </div>
    <?php require_once('../includes/admin.footer.php'); ?>
</main>

<script src="../scripts/admin.sidebar.js"></script>
<script src="../scripts/admin.modal.js"></script>
<script src="../scripts/admin.products.js"></script>