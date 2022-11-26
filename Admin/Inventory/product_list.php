<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../../includes/classes.php';

$api = new MyAPI($main_conn);

$products = $api->Read('products', 'all');

$total_product = $api->Read('products', 'all', NULL, NULL, true);
$all_stock_available = 0;
$all_holding_stock = 0;

foreach ($products as $product) {
   $all_stock_available += $product->stock_avail;
   $all_holding_stock += $product->holding_stock;
}

$title = 'Product List | Dairy Raisers';
$path = 2;
require_once '../includes/admin.header.php';
require_once '../includes/admin.sidebar.php';

?>

<!-- HEADER CONTAINER -->
<div class="border-bottom d-flex flex-row justify-content-between align-items-center overflow-hidden pb-2 mb-3">
   <div class="d-flex justify-content-start align-items-center flex-row">
      <div class="header-container bg-primary d-flex flex-row justify-content-end align-items-center">
         <i class="fa-solid fa-bottle-water text-light img-fluid me-5" style="font-size: 60px;"></i>
      </div>
      <div class="d-flex flex-column justify-content-center align-items-start me-5">
         <h1 class="lead py-0">Inventory List <i class="bi bi-view-list ms-1"></i></h1>
         <div class="nav-item d-flex flex-row justify-content-center align-items-center">
            <i class="bi bi-filter-circle-fill me-2"></i>
         </div>
      </div>
      <div
         class="ms-1 d-flex flex-column justify-content-between align-items-center py-2 px-3 rounded bg-primary text-light">
         <h1 class="lead py-0 mb-3" style="font-size: 27px;">Total Product <i class="bi bi-check2-circle"></i> <span
               class="ms-4">:</span> <span class="fw-bold ms-2"><?= $total_product; ?> item</span></h1>
         <div class="w-100 d-flex flex-column justify-content-center align-items-center text-light">
            <h1 class="lead py-0 opacity-75 text-center  font-monospace " style="font-size: 14px;">Total Stock
               Availability <i class="mx-2 bi bi-box-seam"></i> : <span
                  class="ms-1 fw-bold"><?= $all_stock_available; ?></span></h1>
            <h1 class="lead py-0 opacity-75 text-center  font-monospace " style="font-size: 14px;">Total Stock Holding
               <i class="mx-2 bi bi-handbag"></i> : <span class="ms-1 fw-bold"><?= $all_holding_stock; ?></span>
            </h1>
         </div>
      </div>
   </div>
   <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-product"><i
         class="bi bi-plus-circle"></i> Add New Product</button>
</div>

<!-- CREATE PRODUCT -->
<div class="modal fade" id="add-product" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
   aria-labelledby="staticBackdropLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <form class="modal-content" method="POST" action="../process/process-product.php" enctype="multipart/form-data">
         <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">Create New Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <div class="form-group input-group mb-3">
               <span class="input-group-text">
                  DR_
               </span>
               <div class="form-floating form-floating-group flex-grow-1">
                  <input type="text" class="form-control form-control-sm" name="product-code" id="product-code"
                     placeholder="enter product code" required>
                  <label for="product-code" class="form-label">Product Code: </label>
               </div>
               <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="top"
                  title="Code must have atleast 3 digits and letters.">
                  <i class="bi bi-info-circle"></i>
               </span>
            </div>
            <div class="form-floating mb-3">
               <input type="text" class="form-control form-control-sm" name="product-name" id="product-name"
                  placeholder="enter product name" required>
               <label for="product-name" class="form-label">Product Name: </label>
            </div>
            <div class="form-floating mb-3">
               <textarea name="product-description" class="form-control" id="product-description" style="height: 120px"
                  placeholder="enter product description" required></textarea>
               <label for="product-description" class="form-label">Product Description: </label>
            </div>
            <div class="mb-3">
               <label for="product-image" class="form-label ">Input Product Image</label>
               <input class="form-control form-control-sm" type="file" name="product-image" id="product-image" multiple
                  required>
            </div>
            <div class="form-group input-group mb-3">
               <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                  title="<u> Value will be recognize as Peso Sign [₱]. </u>">₱</span>
               <div class="form-floating form-floating-group flex-grow-1">
                  <input type="number" class="form-control form-control-sm" name="product-price" id="product-price"
                     placeholder="enter product price" required>
                  <label for="product-price" class="form-label">Product Price: </label>
               </div>
            </div>
            <div class="form-floating mb-3">
               <input type="number" class="form-control form-control-sm input_number" name="product-stock-avail"
                  id="product-stock-avail" placeholder="enter product stock available" required>
               <label for="product-stock-avail" class="form-label">Stock Available: </label>
            </div>
            <div class="form-floating mb-3">
               <input type="number" class="form-control form-control-sm input_number" name="product-holding-stock"
                  id="product-holding-stock" placeholder="enter product holding stock" required>
               <label for="product-holding-stock" class="form-label">Holding Stock: </label>
            </div>
         </div>
         <div class="modal-footer">
            <button type="submit" name="add-product" class="btn btn-primary">Create</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
         </div>
      </form>
   </div>
</div>

<!-- UPDATE DESCRIPTION -->
<div class="modal fade" id="update-description" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
   aria-labelledby="staticBackdropLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <form class="modal-content" method="POST" action="../process/process-product.php" enctype="multipart/form-data">
         <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel"><i data-bs-toggle="tooltip" data-bs-placement="top"
                  data-bs-html="true" title="<small>Update Product Name, Description and Price.</small>"
                  class="bi bi-arrow-clockwise"></i> Update Description Product Code: <span
                  id="product-code-display-description"></span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <input type="hidden" name="update-product-id" id="update-product-id-description" readonly>
            <div class="form-floating mb-3">
               <input type="text" class="form-control form-control-sm" name="update-product-name"
                  id="update-product-name" placeholder="enter product name" required>
               <label for="update-product-name" class="form-label">Product Name: </label>
            </div>
            <div class="form-floating mb-3">
               <textarea name="update-product-description" class="form-control" id="update-product-description"
                  style="height: 120px" placeholder="enter product description" required></textarea>
               <label for="update-product-description" class="form-label">Product Description: </label>
            </div>
            <div class="form-group input-group mb-3">
               <span class="input-group-text" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true"
                  title="<u> Value will be recognize as Peso Sign [₱]. </u>">₱</span>
               <div class="form-floating form-floating-group flex-grow-1">
                  <input type="number" class="form-control form-control-sm" name="update-product-price"
                     id="update-product-price" placeholder="enter product price" required>
                  <label for="update-product-price" class="form-label">Product Price: </label>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="submit" name="post-update-product-description" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
         </div>
      </form>
   </div>
</div>

<!-- UPDATE STOCK -->
<div class="modal fade" id="update-stock" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
   aria-labelledby="staticBackdropLabel" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <form class="modal-content" method="POST" action="../process/process-product.php" enctype="multipart/form-data">
         <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel"><i data-bs-toggle="tooltip" data-bs-placement="top"
                  data-bs-html="true" title="<small>Update Product Stock Availability and Holding.</small>"
                  class="bi bi-box-seam"></i> Update Stock Product Code: <span id="product-code-display-stock"></span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <input type="hidden" name="update-product-id" id="update-product-id-stock" readonly>
            <div class="form-floating mb-3">
               <input type="number" class="form-control form-control-sm input_number" name="update-product-stock-avail"
                  id="update-product-stock-avail" placeholder="enter product stock available" required>
               <label for="update-product-stock-avail" class="form-label">Stock Available: </label>
            </div>
            <div class="form-floating mb-3">
               <input type="number" class="form-control form-control-sm input_number"
                  name="update-product-holding-stock" id="update-product-holding-stock"
                  placeholder="enter product holding stock" required>
               <label for="update-product-holding-stock" class="form-label">Holding Stock: </label>
            </div>
         </div>
         <div class="modal-footer">
            <button type="submit" name="post-update-product-stock" class="btn btn-primary">Update</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
         </div>
      </form>
   </div>
</div>

<div class="table-responsive">
   <table class="table table-bordered table-hover table-striped" id="productTable"
      style="width: 100%; font-size: 13px;">
      <thead>
         <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Name</th>
            <th>Description</th>
            <th>Image</th>
            <th>Price</th>
            <th>Stock Available</th>
            <th>Holding Stock</th>
            <th>Update</th>
            <th>Action</th>
         </tr>
      </thead>
      <tbody id="productData">
         <?php
         $index = 1;
         foreach ($products as $product) :
         ?>
         <tr>
            <td class="fw-bold" data-target="product-id"><?= $product->product_id; ?></td>
            <td class="fw-bold" data-target="product-code"><?= $product->productcode; ?></td>
            <td data-target="product-name"><?= $product->productname; ?></td>
            <td data-target="product-description"><?= $product->description; ?></td>
            <td> <img src="<?= $product->img_url; ?>" class="img-fluid" style="width: 35px; height: 35px;"
                  alt="<?= $product->productname; ?> image"></td>
            <td>₱<span data-target="product-price"><?= $product->price; ?></span>.00</td>
            <td data-target="stock-available"><?= $product->stock_avail; ?></td>
            <td data-target="holding-stock"><?= $product->holding_stock; ?></td>
            <td><?= $product->update; ?></td>
            <td class="">
               <div class="flex-column">
                  <div class="mb-1 btn-group">
                     <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                        data-bs-target="#update-description" data-btn="product_description"><i data-bs-toggle="tooltip"
                           data-bs-placement="top" data-bs-html="true"
                           title="<small>Update Product Name, Description and Price.</small>"
                           class="bi bi-arrow-clockwise"></i></button>
                     <!-- <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#update-price" data-btn="product_price"><i data-bs-toggle="tooltip" data-bs-placement="top" data-bs-html="true" title="<small>Update Product Price.</small>" class="bi bi-tags"></i></button> -->
                  </div>
                  <div class="mb-1 btn-group">
                     <button type="button" class="btn btn-sm btn-success me-1" data-bs-toggle="modal"
                        data-bs-target="#update-stock" data-btn="product_stock"><i data-bs-toggle="tooltip"
                           data-bs-placement="top" data-bs-html="true"
                           title="<small>Update Product Stock Availability and Holding.</small>"
                           class="bi bi-box-seam"></i></button>
                     <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                        data-bs-placement="top" data-bs-html="true" title="<small>Remove Product.</small>"
                        data-btn="product_remove"><i class="bi bi-file-earmark-x"></i></button>
                  </div>
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
            <th>ID</th>
            <th>Code</th>
            <th>Name</th>
            <th>Description</th>
            <th>Image</th>
            <th>Price</th>
            <th>Stock Available</th>
            <th>Holding Stock</th>
            <th>Update</th>
            <th>Action</th>
         </tr>
      </tfoot>
   </table>
</div>

<?php require_once '../includes/admin.footer.php'; ?>

<?php if (isset($_SESSION['product-message'])) : ?>
<script>
swal(
   "<?= $_SESSION['product-message']['title']; ?>",
   "<?= $_SESSION['product-message']['body']; ?>",
   "<?= $_SESSION['product-message']['type']; ?>"
);
</script>
<?php endif;
unset($_SESSION['product-message']);
?>

<script>
$(document).ready(() => {

   var Table = $("#productTable").dataTable();

   var remove_btn = Array.from($('[data-btn="product_remove"]'));
   var update_description_btn = Array.from($('[data-btn="product_description"]'));
   var update_stock_btn = Array.from($('[data-btn="product_stock"]'));
   var pagination = document.querySelector('.pagination');

   var product_id = Array.from($('[data-target="product-id"]'));
   var product_code = Array.from($('[data-target="product-code"]'));
   var product_name = Array.from($('[data-target="product-name"]'));
   var product_price = Array.from($('[data-target="product-price"]'));
   var product_description = Array.from($('[data-target="product-description"]'));
   var stock_available = Array.from($('[data-target="stock-available"]'));
   var holding_stock = Array.from($('[data-target="holding-stock"]'));

   function updateProductDescription() {
      update_description_btn.forEach((data, i) => {
         $(data).click(() => {
            $('#update-product-price').val(product_price[i].innerText);
            $('#update-product-name').val(product_name[i].innerText);
            $('#update-product-description').val(product_description[i].innerText);

            $('#product-code-display-description').text(product_id[i].innerText);
            $('#update-product-id-description').val(product_id[i].innerText);
         });
      });
   }

   function updateProductStock() {
      update_stock_btn.forEach((data, i) => {
         $(data).click(() => {
            $('#update-product-stock-avail').val(stock_available[i].innerText);
            $('#update-product-holding-stock').val(holding_stock[i].innerText);

            $('#product-code-display-stock').text(product_id[i].innerText);
            $('#update-product-id-stock').val(product_id[i].innerText);
         })
      })
   }

   function removeProduct() {
      remove_btn.forEach((data, i) => {
         $(data).click(() => {
            swal({
               title: "Remove Product ID " + product_code[i].innerText + "?",
               text: "Once removed, it cannot be undo.",
               icon: "warning",
               closeOnClickOutside: false,
               buttons: true,
               dangerMode: true,
            }).then((response) => {
               if (response) {
                  $.ajax({
                     type: 'POST',
                     url: '../process/process-product.php',
                     data: {
                        remove_product: product_id[i].innerText
                     },
                     success: ((response) => {
                        location.reload();
                     })
                  });
               }
            })
         })
      })
   }

   function paginationClick() {
      pagination.addEventListener("click", (e) => {

         remove_btn = Array.from($('[data-btn="product_remove"]'));
         update_description_btn = Array.from($('[data-btn="product_description"]'));
         update_stock_btn = Array.from($('[data-btn="product_stock"]'));
         pagination = document.querySelector('.pagination');

         product_id = Array.from($('[data-target="product-id"]'));
         product_code = Array.from($('[data-target="product-code"]'));
         product_name = Array.from($('[data-target="product-name"]'));
         product_price = Array.from($('[data-target="product-price"]'));
         product_description = Array.from($('[data-target="product-description"]'));
         stock_available = Array.from($('[data-target="stock-available"]'));
         holding_stock = Array.from($('[data-target="holding-stock"]'));

         updateProductDescription();
         updateProductStock();
         removeProduct();
         paginationClick();
      })
   }

   updateProductDescription();
   updateProductStock();
   removeProduct();
   paginationClick();

   var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
   var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl)
   });

   var input_number = Array.from(document.querySelectorAll('.input_number'));
   var invalidChars = ["-", "+", "e"];

   input_number.forEach((element) => {
      element.addEventListener("keydown", function(e) {
         if (invalidChars.includes(e.key)) {
            e.preventDefault();
         }
      });

      $(element).on('focus', 'input[type=number]', function(e) {
         $(this).on('wheel.disableScroll', function(e) {
            e.preventDefault()
         })
      })
      $(element).on('blur', 'input[type=number]', function(e) {
         $(this).off('wheel.disableScroll')
      })
   });
});
</script>