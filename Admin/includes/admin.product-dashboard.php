<?php

$date = date('Y-m-d');

$all_products = $api->Read('products', 'all');
$all_products_rows = $api->Read('products', 'all', NULL, NULL, true);
$total_finished_goods = $api->Sum('product_stocks', 'all', 'finished_goods');
$total_raw_materials = $api->Sum('raw_materials', 'all', 'stocks');
?>

<div class="container p-0 d-flex justify-content-between align-items-stretch row m-4">
    <!-- TOTAL PRODUCTS -->
    <div class="col d-flex flex-column justify-content-center align-items-stretch">
        <div class="card text-light" style="height: 100%;">
            <img src="../../img/milk.png" class="card-img-top p-3 mx-auto d-block img-fluid" style="width: 170px;height: 100%;" alt="buffalo image">
            <div class="card-body bg-success text-center d-flex flex-column justify-content-center align-items-stretch">
                <p class="card-text">Total Product: </p>
                <h3 class="card-title"><strong><?= $all_products_rows; ?> Products</strong></h3>
                <p class="card-text"><?= $date; ?></p>
            </div>
        </div>
    </div>
    <!-- CHART -->
    <div class="col-5 card">
        <div class="card-body ">
            <div class=""style="height: 300px;">
                <canvas id="productDashboard"></canvas>
            </div>
        </div>
    </div>
    <!-- FINISHED GOODS AND RAW MATERIALS -->
    <div class="col d-flex flex-column justify-content-center align-items-stretch">
        <div class="card bg-primary text-light" style="height: 100%;">
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-stretch">
                <p class="card-text">Total Finished Goods: </p>
                <h3 class="card-title"><strong><?= $total_finished_goods->output; ?> Goods</strong></h3>
                <p class="card-text"><?= $date; ?></p>
            </div>
        </div>
        <div class="m-2">
        </div>
        <div class="card bg-primary text-light" style="height: 100%;">
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-stretch">
                <p class="card-text">Total Raw Materials :</p>
                <h3 class="card-title"><strong><?= $total_raw_materials->output; ?> Materials</strong></h3>
                <p class="card-text"><?= $date; ?></p>
            </div>
        </div>
    </div>
    <!-- SALES -->
    <div class="col d-flex flex-column justify-content-center align-items-stretch">
        <div class="card text-light" style="height: 100%;">
            <div class="card-body bg-danger text-center d-flex flex-column justify-content-center align-items-stretch">
                <p class="card-text">Total Sales: </p>
                <h3 class="card-title"><strong><?= $all_products_rows; ?> Sales</strong></h3>
                <p class="card-text"><?= $date; ?></p>
            </div>
        </div>
    </div>
</div>