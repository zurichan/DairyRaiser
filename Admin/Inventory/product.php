<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../includes/admin.sidebar.php';
require_once '../../includes/classes.php';
require_once '../includes/admin.header.php';

echo Title('Dairy Raisers');

if (isset($_GET['reset-btn'])) {
    unset($_GET['reset-btn']);
    unset($_GET['product-search']);
    unset($_GET['product_filter']);
    header('Location: ./product.php');
}

$api = new MyAPI($main_conn);
?>

<style>
    .dt-buttons,
    .dataTables_filter {
        margin-top: 10px;
    }
</style>

<!-- Add Modal -->
<div class="modal fade" id="add-product" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="../process/process-product.php" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Add Product</span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label" for="product_id">Product Code:</label>
                <input type="text" class="form-control mb-5" name="product_id" id="product_id" readonly disabled required>

                <label class="form-label" for="productname">Product Name:</label>
                <input type="text" class="form-control mb-5" name="productname" id="productname" required>

                <div class="mb-5">
                    <label for="productimg">Product Image: </label>
                    <input type="file" class="form-control" name="productimg" id="productimg" required>
                    <span class="opacity-50">Select Image with valid Extensions: JPG, JPEG, PNG</span>
                    <div class="container-img"></div>
                </div>

                <label class="form-label" for="description">Description:</label>
                <textarea class="form-control mb-5" name="description" id="description" cols="20" rows="10" required></textarea>

                <label class="form-label" for="productprice">Product Price (₱):</label>
                <input type="number" class="form-control mb-5" name="productprice" id="productprice" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" id="add-product" name="add-product" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="update-product" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <form class="modal-content" method="POST" action="../process/process-product.php" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Update Product: </span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label" for="product_id_update">Product Code:</label>
                <input type="number" class="form-control mb-5" name="product_id_update" id="product_id_update" readonly required>

                <label class="form-label" for="productname_update">Product Name:</label>
                <input type="text" class="form-control mb-5" name="productname_update" id="productname_update" required>

                <label class="form-label" for="description_update">Description:</label>
                <textarea class="form-control mb-5" name="description_update" id="description_update" cols="20" rows="10" required></textarea>

                <label class="form-label" for="productprice_update">Product Price (₱):</label>
                <input type="number" class="form-control mb-5" name="productprice_update" id="productprice_update" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" id="update-product" name="update-product" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
</div>

<!-- management main container -->
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
                    <a href="./product.php" class="btn mb-2 btn-primary active" aria-current="page">Products</a>
                    <a href="./raw-materials.php" class="btn mb-2 btn-outline-primary">Raw Materials</a>
                    <a href="./stocks.php" class="btn mb-2 btn-outline-primary">Stocks</a>
                    <a href="./sales.php" class="btn btn-outline-primary">Sales</a>
                </div>
            </div>
        </div>
    </div>

    <!-- list of items -->
    <div class="table-responsive container-fluid border-top mt-2 p-2">
        <!-- contents -->
        <table class="table caption-top table-striped" id="productTable" style="width: 100%;">
            <caption class="text-center mb-3">LIST OF PRODUCTS</caption>
            <thead class="text-center bg-primary text-light">
                <tr>
                    <th>Number</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>I.D.</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Update On</th>
                    <!-- <th>Status</th> -->
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="contents" class="text-center">
                <?php
                $index = 1;

                foreach ($all_products as $product) :

                ?>
                    <tr id="<?= $index; ?>">
                        <td><?= $index; ?></td>
                        <td data-target="productimg"><img src="../img/<?= $product->img_url; ?>" class="logo img-fluid" alt="product"></td>
                        <td data-target="productname" class="productname"><?= $product->productname; ?></td>
                        <td data-target="productcode" class="productcode"><?= $product->product_id; ?></td>
                        <td data-target="price"><?= '₱' . $product->price . '.00'; ?></td>
                        <td data-Target="description"><?= $product->description; ?></td>
                        <td><?= $product->lastUpdate; ?></td>
                        <!-- <td>
                            <?php
                            $product_stocks = $api->Read('product_stocks', 'set', 'product_id', $product->product_id);

                            if ($product_stocks[0]->finished_goods != 0) {
                            ?>
                                <p class="lead text-center btn btn-sm btn-success">Available</p>
                            <?php
                            } else {
                            ?>
                                <p class="lead text-center btn btn-sm btn-danger">Not Available</p>
                            <?php
                            }
                            ?>
                        </td> -->
                        <td>
                            <a href="#" data-role="update" data-id="<?= $index; ?>" class="update-product btn btn-sm btn-primary">update</a>
                            <button type="button" class="remove-product btn btn-sm btn-danger">remove</button>
                        </td>
                    </tr>
                <?php $index++;
                endforeach;
                ?>
            </tbody>
            <tfoot class="text-center bg-primary text-light">
                <tr>
                    <th>Number</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>I.D.</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Update On</th>
                    <!-- <th>Status</th> -->
                    <th>Actions</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="container mt-4 d-flex align-items-center justify-content-between text-center">
        <div>
            <div class="btn-group">
            </div>
            <div class="d-flex">
            </div>
        </div>
    </div>
    <?php require_once('../includes/admin.footer.php'); ?>
</main>


<script src="../scripts/admin.products.js"></script>
<script src="../scripts/admin.product-chart.js"></script>

<?php if (isset($_SESSION['add-product-message'])) : ?>
    <script>
        swal(
            "<?= $_SESSION['add-product-message']['title']; ?>",
            "<?= $_SESSION['add-product-message']['body']; ?>",
            "<?= $_SESSION['add-product-message']['type']; ?>"
        );
    </script>
<?php endif;
unset($_SESSION['add-product-message']);

?>

<script>
    $(document).ready(function() {
        $('#productTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                '<button type="button" class="btn btn-primary me-2" id="add-product-btn" data-bs-toggle="modal" data-bs-target="#add-product">Add Product</button>',
            ]
        });
    });
</script>