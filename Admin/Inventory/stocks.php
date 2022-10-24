<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../includes/admin.sidebar.php';
require_once '../../includes/classes.php';
require_once '../includes/admin.header.php';


if (isset($_GET['reset-btn'])) {
    unset($_GET['reset-btn']);
    unset($_GET['material_search']);
    unset($_GET['search_filter']);
    header('Location: ./stocks.php');
}

$api = new MyAPI($main_conn);

$all_products = $api->Read('products', 'all');
$all_products_rows = $api->Read('products', 'all', NULL, NULL, true);
$total_finished_goods = $api->Sum('product_stocks', 'all', 'finished_goods');
$total_raw_materials = $api->Sum('raw_materials', 'all', 'stocks');
$stocks = $api->Read('product_stocks', 'all');

echo Title('Dairy Raisers');

?>

<style>
    .dt-buttons,
    .dataTables_filter {
        margin-top: 10px;
    }
</style>

<main class="overflow-auto mx-auto d-block main-container resize">
    <?php
    require_once('../includes/admin.topbar.php');
    echo topbar('Inventory');
    ?>
    <!-- NAVIGATION BAR -->
    <div class="d-flex flex-column justify-content-center align-items-stretch px-3">
        <h1 class="lead mt-3 mx-3">Product Dashboard</h1>
        <div class="d-flex flex-row">
            <?php require_once('../includes/admin.product-dashboard.php'); ?>
            <div class="d-flex justify-content-center align-items-center">
                <div class="btn-group-vertical p-2">
                    <div class="d-flex flex-column text-center text-light bg-primary p-2 mb-3">
                        <p class="text-center lead fw-bold">Navigation Bar</p>
                        <i class="bi bi-caret-down fs-5"></i>
                    </div>
                    <a href="./product.php" class="btn mb-2 btn-outline-primary">Products</a>
                    <a href="./raw-materials.php" class="btn mb-2 btn-outline-primary">Raw Materials</a>
                    <a href="./stocks.php" class="btn mb-2 btn-primary active" aria-current="page">Stocks</a>
                    <a href="./sales.php" class="btn btn-outline-primary">Sales</a>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="table-responsive container-fluid border-top mt-2 p-2">
        <!-- contents -->
        <table class="table caption-top table-striped" id="stocksTable" style="width: 100%;">
            <caption class="text-center mb-3">LIST OF PRODUCT STOCKS</caption>
            <thead class="text-center bg-primary text-light">
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>I.D.</th>
                    <th>Location Rack</th>
                    <th>Finished Goods</th>
                    <th>Expired Goods</th>
                    <th>Updated On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php
                $index = 1;
                foreach ($stocks as $stock) :
                    $get_product = $api->Read('products', 'set', 'product_id', $stock->product_id);
                ?>

                    <tr id="<?= $index; ?>">
                        <td><?= $index; ?></td>
                        <td><?= $get_product[0]->productname; ?></td>
                        <td data-target="product_id"><?= $stock->product_id; ?></td>
                        <td data-target="location_rack" class="location_rack"><?= $stock->location_rack; ?></td>
                        <td data-target="finished_goods"><?= $stock->finished_goods; ?></td>
                        <td data-target="expired_goods"><?= $stock->expired_goods; ?></td>
                        <td><?= $stock->lastUpdate; ?></td>
                        <td>
                            <button type="button" class="dispose-stock btn btn-sm btn-danger">Disposed</button>
                            <a href="#" data-role="update" data-id="<?= $index; ?>" class="update-stock btn btn-sm btn-primary">update</a>
                        </td>
                    </tr>
                <?php

                    $index++;
                endforeach;
                ?>
            </tbody>
            <tfoot class="text-center bg-primary text-light">
                <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>I.D.</th>
                    <th>Location Rack</th>
                    <th>Finished Goods</th>
                    <th>Expired Goods</th>
                    <th>Updated On</th>
                    <th>Actions</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php require_once('../includes/admin.footer.php'); ?>
</main>

<!-- Update Stock Modal -->
<div class="modal fade" id="update-stocks" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="../process/process-stocks.php" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Update Stocks:</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label" for="update_material_id">product I.D.: </label>
                <input class="form-control mb-4" type="text" name="product_id_stocks" id="update_product_stock_id" required readonly>

                <label class="form-label" for="update_rack">Location Rack: </label>
                <select class="form-select mb-4" name="update_rack" id="update_rack">
                    <option value="Rack-A">Rack-A</option>
                    <option value="Rack-B">Rack-B</option>
                    <option value="Rack-C">Rack-C</option>
                    <option value="Rack-D">Rack-D</option>
                </select>

                <label class="form-label" for="update_finished_goods">Finished goods: </label>
                <input class="form-control mb-4" type="number" name="update_finished_goods" id="update_finished_goods">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" id="update-stock" name="update-stock" class="btn btn-primary">Update Material</button>
            </div>
        </form>
    </div>
</div>

<!-- Disposed Stock Modal -->
<div class="modal fade" id="dispose-stocks" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="../process/process-stocks.php" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Disposed Expired Goods:</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label" for="dispose_stock_id">product I.D.: </label>
                <input class="form-control mb-4" type="text" name="dispose_stock_id" id="dispose_stock_id" required readonly>

                <div class="d-flex flex-row justify-content-center align-items-center mb-4">
                    <label class="form-label" for="disposed_expired_goods">Total Expired Goods: <span id="total-expired-goods"></span></label>
                    <input class="form-control" type="number" name="disposed_expired_goods" id="disposed_expired_goods" value="0">
                    <button type="button" class="btn btn-sm btn-warning px-4">MAX</button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" id="update-stock" name="update-stock" class="btn btn-primary">Update Material</button>
            </div>
        </form>
    </div>
</div>

<script src="../scripts/admin.sidebar.js"></script>
<script src="../scripts/admin.product-chart.js"></script>
<script src="../scripts/admin.stocks.js"></script>

<?php if (isset($_SESSION['stock-message'])) : ?>
    <script>
        swal(
            "<?= $_SESSION['stock-message']['title']; ?>",
            "<?= $_SESSION['stock-message']['body']; ?>",
            "<?= $_SESSION['stock-message']['type']; ?>"
        );
    </script>
<?php endif;
unset($_SESSION['stock-message']);
?>

<script>
    $(document).ready(function() {
        $('#stocksTable').DataTable({

        });
    });
</script>