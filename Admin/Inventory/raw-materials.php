<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../../includes/classes.php';


if (isset($_GET['reset-btn'])) {
   unset($_GET['reset-btn']);
   unset($_GET['material_search']);
   unset($_GET['search_filter']);
   header('Location: ./raw-materials.php');
}

$api = new MyAPI($main_conn);

$all_products = $api->Read('products', 'all');
$all_products_rows = $api->Read('products', 'all', NULL, NULL, true);
$total_finished_goods = $api->Sum('product_stocks', 'all', 'finished_goods');
$total_raw_materials = $api->Sum('raw_materials', 'all', 'stocks');
$supplier = $api->Read('supplier', 'all');
$material_stock = $api->Read('material_stock', 'all');
$raw_materials = $api->Read('raw_materials', 'all');

$header = '';
$title = 'Raw Materials';
$page_pass = 0;
if (isset($_GET['page'])) {
   $page = $_GET['page'];
   $page_arr = array('view', 'purchase_material');
   (in_array($page, $page_arr)) ? $page_pass++ : NULL;
   if ($page_pass == 1) {
      if ($page == $page_arr[0]) {
         $header = 'Raw Materials List';
         $title = 'Raw Materials';
         $all_suppliers = $api->Read('supplier', 'all');
      } else if ($page == $page_arr[1]) {
         $header = 'Purchase Material';
         $title = 'Purchase Materials';
      }
   }
}

$title = $title . ' | Dairy Raisers';
$path = 2;
require_once '../includes/admin.header.php';
require_once '../includes/admin.sidebar.php';

if ($page_pass == 1) {
?>

<!-- HEADER CONTAINER -->
<div class="border-bottom d-flex flex-row justify-content-between align-items-center overflow-hidden pb-2 mb-3">
   <div class="d-flex justify-content-start align-items-center flex-row">
      <div class="header-container bg-primary d-flex flex-row justify-content-end align-items-center">
         <i class="fa-solid fa-boxes-packing text-light img-fluid me-4" style="font-size: 50px;"></i>
         <!-- <img src="../../img/inventory.svg" alt="inventory" class="img-fluid me-4" style="width: 70px;"> -->
      </div>
      <div class="d-flex flex-column justify-content-center align-items-start me-5">
         <h1 class="lead py-0">Raw Materials List <i class="bi bi-view-list ms-1"></i></h1>
         <div class="nav-item d-flex flex-row justify-content-center align-items-center">
            <i class="bi bi-filter-circle-fill me-2"></i>
         </div>
      </div>
      <div
         class="ms-1 d-flex flex-column justify-content-between align-items-center py-2 px-3 rounded bg-primary text-light">
         <h1 class="lead py-0 mb-3" style="font-size: 27px;">Total Product <i class="bi bi-check2-circle"></i> <span
               class="ms-4">:</span> <span class="fw-bold ms-2"><?= 0; ?> item</span></h1>
         <div class="w-100 d-flex flex-column justify-content-center align-items-center text-light">
            <h1 class="lead py-0 opacity-75 text-center  font-monospace " style="font-size: 14px;">Total Stock
               Availability <i class="mx-2 bi bi-box-seam"></i> : <span class="ms-1 fw-bold"><?= 0; ?></span></h1>
            <h1 class="lead py-0 opacity-75 text-center  font-monospace " style="font-size: 14px;">Total Stock Holding
               <i class="mx-2 bi bi-handbag"></i> : <span class="ms-1 fw-bold"><?= 0; ?></span>
            </h1>
         </div>
      </div>
   </div>
   <div class="btn-group-vertical gap-2">
      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
         data-bs-target="#add-new-supplier"><i class="bi bi-plus-circle"></i> Add New
         Supplier</button>
      <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
         data-bs-target="#add-new-material"><i class="bi bi-plus-circle"></i> Add New
         Material</button>
      <a href="./raw-materials.php?page=purchase_material" class="btn btn-sm btn-success"><i
            class="bi bi-box-arrow-down"></i> Purchase
         Material</a>
   </div>
</div>
<?php
}
?>
<?php if (isset($page) && $page == 'view') { ?>
<!-- MAIN CONTENT -->
<div class="table-responsive">
   <table class="table table-hover table-striped" id="materialTable" style="width: 100%; font-size: 13px;">
      <thead>
         <tr>
            <th>Supplier</th>
            <th>Materials Name</th>
            <th>Purchasing Price</th>
            <th>Stocks</th>
            <th>Date</th>
            <th>Actions</th>
         </tr>
      </thead>
      <tbody class="text-center">
         <?php
            $index = 1;
            foreach ($material_stock as $materials) : ?>
         <tr id="<?= $index; ?>">
            <td>
               <?php
                     $supplier = $api->Read('supplier', 'set', 'supplier_id', $materials->supplier_id);
                     ?>
               <span class=""><?= $supplier[0]->supplier_name; ?></span>
            </td>
            <td><?= $materials->material_name; ?></td>
            <td>₱<?= $materials->price; ?>.00</td>
            <td><?= $materials->stocks; ?></td>
            <td><?= $materials->date; ?></td>
            <td>
               <div class="btn-group gap-2">
                  <a href="#" class="btn btn-sm btn-primary"><i class="bi bi-arrow-clockwise"></i></a>
                  <button type="button" class="remove-materials btn btn-sm btn-danger"><i
                        class="bi bi-file-earmark-x"></i></button>
               </div>
            </td>
         </tr>
         <?php
               $index++;
            endforeach;
            ?>
      </tbody>
      <tfoot>
         <tr>
            <th>Supplier</th>
            <th>Materials Name</th>
            <th>Purchasing Price</th>
            <th>Stocks</th>
            <th>Date</th>
            <th>Actions</th>
         </tr>
      </tfoot>
   </table>
</div>
<?php } else if (isset($page) && $page == 'purchase_material') { ?>
<?php } else {
   require_once '../../includes/404_page.php';
}
require_once('../includes/admin.footer.php'); ?>

<!-- ADD NEW MATERIAL -->
<div class="modal fade" id="add-new-material" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
   aria-labelledby="staticBackdropLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <form class="modal-content" method="POST" action="../process/process-rm.php" enctype="multipart/form-data">
         <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Add Material:</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body d-grid gap-3">
            <div class="form-floating">
               <select name="supplier" id="supplier" class="form-select form-select-sm">
                  <?php
                  foreach ($all_suppliers as $supplier) {
                  ?>
                  <option value="<?= $supplier->supplier_id; ?>"><?= $supplier->supplier_name; ?></option>
                  <?php
                  }
                  ?>
               </select>
               <label for="supplier" class="form-label">Supplier:</label>
            </div>
            <div class="form-floating">
               <input class="form-control form-control-sm" type="text" name="material" id="material" required>
               <label class="form-label" for="material">Material: </label>
            </div>
            <div class="form-floating">
               <input class="form-control form-control-sm" type="number" name="price" id="price" required>
               <label class="form-label" for="price">Price: </label>
            </div>
            <div class="form-floating">
               <input class="form-control form-control-sm" type="number" name="stocks" id="stocks">
               <label class="form-label" for="stocks">Stocks: </label>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="add-new-material" class="btn btn-primary">Submit</button>
         </div>
      </form>
   </div>
</div>

<!-- ADD NEW SUPPLIER -->
<div class="modal fade" id="add-new-supplier" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
   aria-labelledby="staticBackdropLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <form class="modal-content" method="POST" action="../process/process-rm.php" enctype="multipart/form-data">
         <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Add Supplier:</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body d-grid gap-3">
            <div class="form-floating">
               <input type="text" name="new-supplier" id="new-supplier" class="form-control form-control-sm" required>
               <label for="new-supplier" class="form-label">Supplier name:</label>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
               <button type="submit" name="add-new-supplier" class="btn btn-primary">Submit</button>
            </div>
      </form>
   </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="update-materials" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
   aria-labelledby="staticBackdropLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <form class="modal-content" method="POST" action="../process/process-materials.php" enctype="multipart/form-data">
         <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Update Material:</span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <label class="form-label" for="update_material_id">material id: </label>
            <input class="form-control mb-4" type="text" name="material_id" id="update_material_id" required readonly>

            <label class="form-label" for="update_supplier">supplier: </label>
            <input class="form-control mb-4" type="text" name="update_supplier" id="update_supplier" required>

            <label class="form-label" for="update_material">material: </label>
            <input class="form-control mb-4" type="text" name="update_material" id="update_material" required>

            <label class="form-label" for="update_price">price: </label>
            <input class="form-control mb-4" type="number" name="update_price" id="update_price" required>

            <label class="form-label" for="update_material_stocks">stocks: </label>
            <input class="form-control mb-4" type="number" name="update_material_stocks" id="update_material_stocks"
               required>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" id="update-material" name="update-material" class="btn btn-primary">Update
               Material</button>
         </div>
      </form>
   </div>
</div>

<script src="../scripts/admin.sidebar.js"></script>
<script src="../scripts/admin.product-chart.js"></script>
<script src="../scripts/admin.materials.js"></script>

<?php if (isset($_SESSION['material-message'])) : ?>
<script>
swal(
   "<?= $_SESSION['material-message']['title']; ?>",
   "<?= $_SESSION['material-message']['body']; ?>",
   "<?= $_SESSION['material-message']['type']; ?>"
);
</script>
<?php endif;
unset($_SESSION['material-message']);
?>

<script>
$(document).ready(function() {
   $('#materialTable').DataTable({
      dom: 'Bfrtip',
      lengthChange: true,
      buttons: ['copy', 'excel', 'pdf', 'colvis']
   });


});
</script>