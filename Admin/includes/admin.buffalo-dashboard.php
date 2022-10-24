<?php
$buffalos = $api->Read('buffalos', 'all');
$buffalo_rows = $api->Read('buffalos', 'all', NULL, NULL, TRUE);
$date = date('Y-m-d');
?>

<div class="container p-0 d-flex justify-content-between align-items-stretch row m-4">
    <div class="col d-flex flex-column justify-content-center align-items-stretch">
        <div class="card text-light" style="height: 100%;">
            <img src="../../img/cow.png" class="card-img-top p-3 mx-auto d-block img-fluid" style="width: 170px;height: 100%;" alt="buffalo image">
            <div class="card-body bg-success text-center d-flex flex-column justify-content-center align-items-stretch">
                <p class="card-text">Total Buffalo: </p>
                <h3 class="card-title"><strong><?= $buffalo_rows; ?> Buffalos</strong></h3>
                <p class="card-text"><?= $date; ?></p>
            </div>
        </div>
    </div>
    <div class="col card">
        <div class="card-body ">
            <div class="dashboardChartBox">
                <canvas id="buffaloDashboard"></canvas>
            </div>
        </div>
    </div>
    <div class="col d-flex flex-column justify-content-center align-items-stretch">
        <div class="card bg-info text-light" style="height: 100%;">
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-stretch">
                <p class="card-text">Total Pregnant: </p>
                <h3 class="card-title"><strong><?= $buffalo_rows; ?> Lactating</strong></h3>
                <p class="card-text"><?= $date; ?></p>
            </div>
        </div>
        <div class="m-2">
        </div>
        <div class="card bg-primary text-light" style="height: 100%;">
            <div class="card-body text-center d-flex flex-column justify-content-center align-items-stretch">
                <p class="card-text">Total Milk Harvest :</p>
                <h3 class="card-title"><strong>12321 Liters</strong></h3>
                <p class="card-text"><?= $date; ?></p>
            </div>
        </div>
    </div>
    <div class="col d-flex flex-column justify-content-center align-items-stretch">
        <div class="card text-light" style="height: 100%;">
            <div class="card-body bg-danger text-center d-flex flex-column justify-content-center align-items-stretch">
                <p class="card-text">Total Sales: </p>
                <h3 class="card-title"><strong><?= $buffalo_rows; ?> Buffalos</strong></h3>
                <p class="card-text"><?= $date; ?></p>
            </div>
        </div>
    </div>
</div>