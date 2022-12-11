<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../../includes/classes.php';

$api = new MyAPI($main_conn);
$bi_list = $api->Read('bi_list', 'all');

$title_page = "";
$header = "";
$scripts = "";

unset($_SESSION['code']);
unset($_SESSION['edit_bi']);
unset($_SESSION['create_bi']);
unset($_SESSION['edit_bi_otherFee']);

if (isset($_GET['bi_code']) && isset($_GET['page'])) {

   $invoice_row = 0;
   $sold_buffalo_row = $api->Read('bi_session', 'all', NULL, NULL, true);
   $sold_milk_liters = 0;
   $return_bi_row = $api->Read('bi_list', 'set', 'marked_as', "'retrieved'", true);
   $category = array('milk', 'buffalo');
   $total_sales = 0;
   $buffalo_total_sales = 0;
   $milk_total_sales = 0;
   foreach ($bi_list as $bi) {
      if ($bi->marked_as == NULL) {
         $sales = str_replace(',', '', $bi->amount);
         $total_sales += $sales;
      }
      if ($bi->marked_as == NULL && $bi->category == $category[0]) {
         $sold_milk_liters += $bi->items;
         $sales = str_replace(',', '', $bi->amount);
         $milk_total_sales += $sales;
      } else if ($bi->marked_as == NULL && $bi->category == $category[1]) {
         $sales = str_replace(',', '', $bi->amount);
         $buffalo_total_sales += $sales;
      }
   }
   $total_sales = number_format($total_sales);
   $buffalo_total_sales = number_format($buffalo_total_sales);
   $milk_total_sales = number_format($milk_total_sales);
   foreach ($bi_list as $bi) {
      ($bi->marked_as == NULL) ? $invoice_row++ : NULL;
   }

   $page = $_GET['page'];

   $ptoken = 0;
   $page_arr = array('edit', 'view', 'create', 'sold_buffalo', 'return', 'all');

   (in_array($page, $page_arr)) ? $ptoken++ : NULL;

   if (isset($_GET['bi_code'])) {
      $code = $_GET['bi_code'];

      $_SESSION['edit_bi_otherFee'] = 0;
      $_SESSION['code'] = $code;

      $fetch_bi = $api->Read('bi_list', 'set', 'code', "'$code'");
      $fetch_session = '';
      $fetch_session = $api->Read('bi_session', 'set', 'code', "'$code'");
      $fetch_session_row = $api->Read('bi_session', 'set', 'code', "'$code'", true);

      foreach ($bi_list as $bi) {
         ($bi->code == $code) ? $ptoken++ : NULL;
      }
   } else {
      $code = "";
   }
   if ($ptoken == 2 || $page == 'view' || $page == 'create' || $page == 'sold_buffalo' || $page == 'return' || $page == 'all') {

      $buffalos = $api->Read('buffalos', 'all');
      $row = $api->Read('buffalos', 'all', NULL, NULL, true);
      $bi_category = '';

      if ($page == 'edit' && $fetch_bi[0]->category == $category[1]) {
         unset($_SESSION['edit_bi']);
         $_SESSION['edit_bi'] = array();
         $_SESSION['code'] = $code;

         $title_page = 'Edit Buffalo Invoice Record';
         $header = 'Edit Buffalo Invoice | Dairy Raisers';

         $fetch_edit_result = array();

         foreach ($fetch_session as $bi) {
            $fetch_buffalo = $api->Read('buffalos', 'set', 'Buffalo_id', $bi->buffalo_id);
            array_push($fetch_edit_result, [
               'buffalo_id' => $fetch_buffalo[0]->Buffalo_id,
               'buffalo_name' => $fetch_buffalo[0]->Name,
               'buffalo_gender' => $fetch_buffalo[0]->Gender,
               'buffalo_weight' => $fetch_buffalo[0]->Weight
            ]);
         }

         foreach ($buffalos as $bi) {
            if ($bi->Marked_As == NULL) {
               array_push($fetch_edit_result, [
                  'buffalo_id' => $bi->Buffalo_id,
                  'buffalo_name' => $bi->Name,
                  'buffalo_gender' => $bi->Gender,
                  'buffalo_weight' => $bi->Weight
               ]);
            }
         }

         foreach ($fetch_session as $bi) {
            array_push($_SESSION['edit_bi'],  [
               'buffalo_id' => $bi->buffalo_id,
               'buffalo_name' => $bi->buffalo_name,
               'buffalo_gender' => $bi->buffalo_gender,
               'buffalo_weight' => $bi->buffalo_weight,
               'price' => $bi->price,
               'date' => $bi->date
            ]);
         }

         $discount = $fetch_bi[0]->discount;
         $total = $fetch_bi[0]->amount;
         $subTotal = $fetch_bi[0]->subTotal;
         $client = $fetch_bi[0]->client;
         $remarks = $fetch_bi[0]->remarks;
         $otherFees = $fetch_bi[0]->other_fees;
      } else if ($page == 'edit' && $fetch_bi[0]->category == $category[0]) {
         $title_page = 'Edit Buffalo Milk Invoice Record';
         $header = 'Edit Buffalo Milk Invoice | Dairy Raisers';
         $subTotalw_otherFees = '';
         $subTotalw_otherFees = $fetch_bi[0]->other_fees + $fetch_bi[0]->subTotal;
      } else if ($page == 'view' && $fetch_bi[0]->category == $category[1]) {

         $header = "View Buffalo Invoice Record | Dairy Raisers";
         $title_page = "View Buffalo Invoice Record";

         $amountpcs = '';
         ($fetch_session_row > 1) ? $amountpcs = 'buffalos' : $amountpcs = 'buffalo';
      } else if ($page == 'view' && $fetch_bi[0]->category == $category[0]) {
         $header = "View Buffalo Milk Invoice Record | Dairy Raisers";
         $title_page = "View Buffalo Milk Invoice Record";
      } else if ($page == 'create' && $code == $category[1]) {
         $header = 'Create Buffalo Invoice Record | Dairy Raisers';
         $title_page = 'Create Buffalo Invoice Record';

         $subTotal = 0;
         if (isset($_SESSION['create_bi'])) {
            foreach ($_SESSION['create_bi'] as $bi) {
               $subTotal += $bi["price"];
            }
         }
      } else if ($page == 'create' && $code == $category[0]) {
         $header = 'Create Buffalo Milk Invoice Record | Dairy Raisers';
         $title_page = 'Create Buffalo Milk Invoice Record';
      } else if ($page == 'sold_buffalo') {
         $title_page = 'Sold Buffalos List';
         $header = 'Sold Buffalos List | Dairy Raisers';

         $fetch_sold_buffalos = $api->Read('bi_session', 'all');
      } else if ($page == 'return') {
         $title_page = 'Returned Invoice List';
         $header = 'Returned Invoice List | Dairy Raisers';

         $fetch_return_bi = $api->Read('bi_list', 'set', 'marked_as', "'retrieved'");
      } else if ($page == 'all') {
         $title_page = 'Buffalo Invoice List';
         $header = 'Buffalo Invoice List | Dairy Raisers';
      } else {
         $header = 'Error Page';
         $title = "Error Page | Dairy Raisers";
         $title_page = 'Buffalo Invoice List';
      }
   } else if ($ptoken == 0) {

      $header = 'Error Page';
      $title = "Error Page | Dairy Raisers";
      $title_page = 'Buffalo Invoice List';
   }
} else {
   $header = 'Error Page';
   $title = "Error Page | Dairy Raisers";
   $title_page = 'Buffalo Invoice List';
}

/** HEADER */
$path = 2;
$title = $header;
require_once '../includes/admin.header.php';
require_once '../includes/admin.sidebar.php';

?>

<style>
@media print {
   #footer {
      visibility: visible;
      display: block;
   }
}
</style>


<?php
if (isset($_GET['bi_code']) && isset($_GET['page'])) {
   if (in_array($page, $page_arr)) {
?>
<!-- HEADER CONTAINER -->
<div class="border-bottom d-flex flex-row justify-content-between align-items-center overflow-hidden pb-2 mb-3">
   <div class="d-flex justify-content-center align-items-center flex-row">
      <div class="header-container bg-primary d-flex flex-row justify-content-end align-items-center">
         <img src="../../img/buffalo3.svg" alt="buffalo" class="img-fluid me-4" style="width: 70px;">
      </div>
      <div class="d-flex flex-column justify-content-center align-items-start me-4">
         <h1 class="lead py-0"><?= $title_page; ?> <i class="bi bi-view-list ms-1"></i></h1>
         <div class="nav-item d-flex justify-content-center align-items-center">
            <i class="bi bi-filter-circle-fill me-2"></i>
            <a href="./invoice_list.php?page=all&bi_code=none"
               class="btn btn-sm btn-outline-primary me-2 <?php if (isset($_GET['page']) && $page == 'all') echo 'active'; ?>">All</a>
            <a href="./invoice_list.php?page=sold_buffalo&bi_code=none"
               class="btn btn-sm btn-outline-primary <?php if (isset($_GET['page']) && $page == 'sold_buffalo') echo 'active'; ?> me-2">Sold
               Buffalo</a>
            <a href="./invoice_list.php?page=return&bi_code=none"
               class="btn btn-sm btn-outline-danger <?php if (isset($_GET['page']) && $page == 'return') echo 'active'; ?> me-2">Return</a>
         </div>
      </div>

      <!-- TOTALS OF SALES -->
      <div
         class="d-flex flex-row justify-content-between gap-4 align-items-center py-2 px-2 rounded bg-primary text-light">
         <div class="">
            <h1 class="lead py-0 mb-3" style="font-size: 27px;">Total Sales <i class="bi bi-check2-circle"></i> <span
                  class="ms-2">:</span> <span class="fw-bold ms-2">₱<?= $total_sales; ?>.00</span></h1>
            <div class="d-flex flex-row justify-content-between align-items-center">
               <p class="text-light">Milk : <span class="fw-bold ms-2">₱<?= $milk_total_sales; ?>.00</span></p>
               <p class="text-light">Buffalo : <span class="fw-bold ms-2">₱<?= $buffalo_total_sales; ?>.00</span></p>
            </div>
         </div>
         <div class="d-flex flex-column justify-content-between align-items-start text-start text-light">
            <h1 class="lead py-0 opacity-75 d-flex justify-content-between align-items-center  me-4"
               style="font-size: 12px;">Invoice <i class="bi bi-receipt mx-1"></i> : <span
                  class="ms-1 fw-bold"><?= $invoice_row; ?></span></h1>
            <h1 class="lead py-0 opacity-75 d-flex justify-content-between align-items-center  me-4"
               style="font-size: 12px;">Sold Buffalo <i class="bi bi-coin mx-1"></i> : <span
                  class="ms-1 fw-bold"><?= $sold_buffalo_row; ?> buffalo/s</span></h1>
            <h1 class="lead py-0 opacity-75 d-flex justify-content-between align-items-center  me-4"
               style="font-size: 12px;">Milk Liters <i class="bi bi-coin mx-1"></i> : <span
                  class="ms-1 fw-bold"><?= $sold_milk_liters; ?> liter/s</span></h1>
            <h1 class="lead py-0 opacity-75 d-flex justify-content-between align-items-center  me-4"
               style="font-size: 12px;">Return <i class="bi bi-box-arrow-in-left mx-1"></i>: <span
                  class="ms-1 fw-bold"><?= $return_bi_row; ?></span></h1>
         </div>
      </div>
   </div>
   <?php if (isset($_GET['page']) && $page == 'all') { ?>
   <div class="modal fade" id="CreateInvoice" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
      aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
         <form class="modal-content" method="POST" action="../../Admin/process/process_invoice.php"
            enctype="multipart/form-data">
            <div class="modal-header">
               <h5 class="modal-title" id="staticBackdropLabel">Create New Invoice</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               <div class="mb-3">
                  <p class="text-center lead" style="font-size: 18px;">Select Category:</p>
                  <div class="w-100 d-flex justify-content-center align-items-stretch">
                     <div class="form-check me-3">
                        <input class="form-check-input" type="radio" name="selectInvoiceCategory" value="buffalo"
                           id="buffaloInvoice" checked>
                        <label class="form-check-label" for="buffaloInvoice">
                           Buffalo
                        </label>
                     </div>
                     <div class="form-check">
                        <input class="form-check-input" type="radio" name="selectInvoiceCategory" value="milk"
                           id="milkInvoice">
                        <label class="form-check-label" for="milkInvoice">
                           Milk
                        </label>
                     </div>
                  </div>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type="submit" name="CreateNewInvoice" class="btn btn-primary">Submit</button>
               </div>
            </div>
         </form>
      </div>
   </div>
   <div class="">
      <button type="button" class="btn btn-sm btn-success" data-bs-target="#CreateInvoice" data-bs-toggle="modal">Create
         New Invoice</button>
      <!-- <a href="./invoice_list.php?page=create&bi_code=none" class="btn btn-sm btn-success"><i class="bi bi-receipt me-2"></i>Create New Invoice</a> -->
   </div>
   <?php } else if (isset($_GET['page']) && $_GET['page'] == 'edit') { ?>
   <div class="">
      <h1 class="lead">Sales Code: <?= $code; ?></h1>
   </div>
   <?php } else if (isset($_GET['page']) && $_GET['page'] == 'view') { ?>
   <div class="">
      <button type="button" onclick="doPrint()" class="btn btn-primary me-2"><i class="bi bi-printer"></i>
         Print</button>
      <a href="./invoice_list.php?page=all&bi_code=none" class="btn btn-secondary">Back</a>
   </div>
   <?php } else if (isset($_GET['page']) && $_GET['page'] == 'create') { ?>
   <div class="">
      <a href="./invoice_list.php?page=all&bi_code=none" class="btn btn-secondary">Back</a>
   </div>
   <?php } ?>
</div>
<?php } ?>

<!-- MAIN CONTENT -->
<?php
   if (isset($_GET['page']) && $page == 'edit' && $fetch_bi[0]->category == $category[1]) { ?>
<div class="d-flex flex-row justify-content-between align-items-center mt-4">
   <div class="p-3 border-bottom d-flex flex-column justify-content-center align-items-center" style="width: 100%;">
      <form method="POST" action="../process/process-buffalo.php"
         class="mb-4 d-flex flex-row justify-content-between align-items-center w-100">
         <h1 class="lead text-start">Input Form</h1>
         <div class="d-flex flex-row justify-content-start align-items-center w-100">
            <div class="form-floating me-2 w-100">
               <select name="selected_buffalo" id="selected_buffalo" class="form-select">
                  <?php
                        foreach ($fetch_edit_result as $selected_buffalo) {
                           echo "<option value=" . $selected_buffalo['buffalo_id'] . ">" . $selected_buffalo['buffalo_name'] . "[" . $selected_buffalo['buffalo_id'] . "]</option>";
                        }
                        ?>
               </select>
               <label for="selected_buffalo" class="form-label">Buffalo :</label>
            </div>
            <div class="form-floating me-2 w-100">
               <input type="number" class="form-control" name="sell_price" id="sell_price" placeholder="price" required>
               <label for="form-label">Price (₱):</label>
            </div>
            <button type="button" name="buffalo_invoice_submit" id="buffalo_invoice_submit"
               class="btn btn-primary">Add</button>
         </div>
      </form>
      <table class="table table-responsive table-striped display" id="buffaloTable" style="width: 100%;">
         <thead class="text-center bg-primary text-light">
            <tr>
               <td>#</td>
               <td>Description</td>
               <td>Gender</td>
               <td>Weight</td>
               <td>Price</td>
               <td>Action</td>
            </tr>
         </thead>
         <tbody id="tableData" class="text-center">
            <?php

                  $indexes = 1;
                  $subTotal = 0;
                  $Total = 0;
                  foreach ($fetch_session as $bi) {
                     echo '<tr>';
                     echo "<td>$indexes</td>";
                     echo '<td> <span class="fullName_bi">' . $bi->buffalo_name . '</span> [<span class="id_bi">' . $bi->buffalo_id . '</span>]</td>';
                     echo '<td class="">' . $bi->buffalo_gender . '</td>';
                     echo '<td class="">' . $bi->buffalo_weight . ' kg</td>';
                     echo '<td>₱' . $bi->price . '.00</td>';
                     echo "<td><button class='remove_btn btn btn-sm btn-outline-danger'><i class='bi bi-x'></i></button></td>";
                     echo '</tr>';
                     $indexes++;
                     $subTotal += $bi->price;
                  }
                  ?>
         </tbody>
      </table>
   </div>

   <div class="mx-2"></div>
   <div class="card" style="width: 100%;">
      <form class="card-body" method="POST" action="../process/process_invoice.php">
         <div class="d-flex flex-row justify-content-center align-items-center">
            <div class="form-floating mb-3 w-100">
               <textarea class="form-control" placeholder="Leave a remarks here" style="height: 100px;" name="Remarks"
                  id="floatingTextarea"><?= $remarks; ?></textarea>
               <label for="floatingTextarea">Remarks</label>
            </div>
         </div>
         <div class="form-floating mb-4 w-100">
            <input type="text" class="form-control" placeholder="buyer name" value="<?= $client; ?>" name="client_name"
               id="client_name">
            <label for="client_name">Client Name:</label>
         </div>
         <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">₱</span>
            <div class="form-floating">
               <input type="number" class="form-control" value="<?= $otherFees; ?>" placeholder="other fees"
                  name="other_fees" id="other_fees">
               <label for="other_fees">Other Fees:</label>
            </div>
         </div>
         <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="card-subtitle mb-2 text-muted">Sub Total: ₱<span id="subTotal"><?= $subTotal; ?></span>.00</h6>
            <h6 class="card-subtitle mb-2 text-muted text-center d-flex justify-content-center align-items-center">
               Discount:
               <input style="width: 45px;" type="number" aria-describedby="discount" value="<?= $discount; ?>"
                  class="form-control mx-2" oninput="maxlength()" name="discount" id="discount" placeholder="0">
               %
            </h6>
         </div>
         <h5 class="card-title">Total: ₱<span id="Total"><?= $total; ?></span>.00</h5>
         <p class="card-text">Update A Sales Record for Buffalo Sales List.</p>
         <button type="submit" name="update_buffalo_invoice" class="card-link btn btn-primary">Update</button>
         <a href="./invoice_list.php?page=all&bi_code=none" class="card-link btn btn-secondary">Back</a>
      </form>
   </div>
</div>
<?php } else if (isset($_GET['page']) && $page == 'edit' && $fetch_bi[0]->category == $category[0]) { ?>
<form class="mx-4" method="POST" action="../process/process_invoice.php">
   <div class="d-flex flex-row justify-content-center align-items-center">
      <div class="form-floating mb-3 w-100">
         <textarea class="form-control" placeholder="Leave a remarks here" style="height: 100px;" name="edit_remarks"
            id="floatingTextarea"><?= $fetch_bi[0]->remarks; ?></textarea>
         <label for="floatingTextarea">Remarks</label>
      </div>
   </div>
   <div class="form-floating mb-3 w-100">
      <input type="text" class="form-control" placeholder="buyer name" value="<?= $fetch_bi[0]->client; ?>"
         name="edit_client_name" id="edit_client_name">
      <label for="edit_client_name">Client Name:</label>
   </div>
   <div class="input-group mb-3">
      <span class="input-group-text" id="basic-addon1">₱</span>
      <div class="form-floating">
         <input type="number" class="form-control" placeholder="price per liter" value="<?= $fetch_bi[0]->items; ?>"
            name="edit_price_per_liter" id="price_per_liter">
         <label for="price_per_liter">Price per liter:</label>
      </div>
      <span class="input-group-text" id="basic-addon1">/liter</span>
   </div>
   <div class="input-group mb-3">
      <div class="form-floating">
         <input type="number" class="form-control" placeholder="liters" value="<?= $fetch_bi[0]->milk_liter; ?>"
            name="edit_milk_liter" id="milk_liter">
         <label for="milk_liter">Liter/s:</label>
      </div>
   </div>
   <div class="input-group mb-3">
      <span class="input-group-text" id="basic-addon1">₱</span>
      <div class="form-floating">
         <input type="number" class="form-control" placeholder="other fees" value="<?= $fetch_bi[0]->other_fees; ?>"
            name="edit_other_fees" id="other_fees">
         <label for="other_fees">Other Fees:</label>
      </div>
   </div>
   <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="card-subtitle mb-2 text-muted">Sub Total: ₱<span id="subTotal"><?= $fetch_bi[0]->subTotal; ?></span>.00
      </h6>
      <h6 class="card-subtitle mb-2 text-muted">Sub Total w/other fess: ₱<span
            id="subTotalw_otherFees"><?= $subTotalw_otherFees; ?></span>.00</h6>
      <h6 class="card-subtitle mb-2 text-muted text-center d-flex justify-content-center align-items-center">
         Discount:
         <input style="width: 45px;" type="number" aria-describedby="discount" class="form-control mx-2"
            oninput="maxlength()" value="<?= $fetch_bi[0]->discount; ?>" name="edit_discount" id="discount"
            placeholder="0">
         %
      </h6>
   </div>
   <h5 class="card-title mb-4">Total: ₱<span id="Total"><?= $fetch_bi[0]->amount; ?></span>.00</h5>
   <p class="card-text">Create A New Sales Record for Milk Sales List.</p>
   <button type="submit" name="update_bi_invoice_milk" class="card-link btn btn-primary">Submit</button>
   <a href="./invoice_list.php?page=all&bi_code=none" class="card-link btn btn-secondary">Back</a>
</form>
<?php } else if (isset($_GET['page']) && $page == 'view' && $fetch_bi[0]->category == $category[1]) {
      $fetch_bi_list = $api->Read('bi_list', 'set', 'code', "'$code'");
      if (!empty($fetch_bi_list)) {
      ?>
<div id="print-container">
   <div class="px-2">
      <!-- header -->
      <div class="d-flex justify-content-between align-items-center">
         <div class="d-flex flex-column justify-content-start align-items-center">
            <img src="../../img/company-logo.png" class="img-fluid" style="width: 100px;" alt="company logo">
            <p class="lead" style="font-size: 15px;">General Trias Dairy Raisers Multi-Purpose Cooperative®</p>
         </div>
         <div class="d-flex flex-column justify-content-between align-items-end">
            <p class="lead" style="font-size: 15px;"> Brgy. Santiago, General Trias, Cavite</p>
            <p class="lead" style="font-size: 15px;">Tel. No. : 0923.737.1579</p>
            <p class="lead" style="font-size: 15px;">Mobile No. : 0920.908.6694</p>
         </div>
      </div>
      <div class="d-flex justify-content-center align-items-center mb-5">
         <p class="lead" style="font-size: 17px;"><span class="fw-bold">Tax Invoice</span></p>
      </div>
      <div class="d-flex justify-content-between align-items-center">
         <p class="lead" style="font-size: 15px;"><span class="fw-bold">Customer Name:
            </span><?= $fetch_bi[0]->client; ?></p>
         <p class="lead" style="font-size: 15px;"><span class="fw-bold">Invoice: </span> <?= $code; ?></p>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-3">
         <p class="lead" style="font-size: 15px;"><span class="fw-bold">Customer Address: </span>Tanza, Cavite</p>
         <p class="lead" style="font-size: 15px;"><span class="fw-bold">Date: </span> <?= $fetch_bi[0]->date; ?></p>
      </div>
      <!-- table -->
      <div class="table-responsive">
         <table class="table table-light table-bordered" style="width: 100%;">
            <thead align="center" class="align-middle fw-bold">
               <tr>
                  <td>Description</td>
                  <td>Gender</td>
                  <td>Weight</td>
                  <td>Amount</td>
                  <td>Price</td>
               </tr>
            </thead>
            <tbody>
               <?php

                        foreach ($fetch_session as $bi) {
                        ?>
               <tr>
                  <td class="text-center"><?= $bi->buffalo_name; ?>[<?= $bi->buffalo_id; ?>]</td>
                  <td class="text-center"><?= $bi->buffalo_gender; ?></td>
                  <td class="text-center"><?= $bi->buffalo_weight; ?> kg</td>
                  <td class="text-center">1 pc</td>
                  <td class="text-end">₱<?= number_format($bi->price); ?>.00</td>
               </tr>
               <?php
                        }
                        ?>
            </tbody>
            <tfoot class="text-end">
               <tr>
                  <td colspan="100%" class="text-center">***</td>
               </tr>
               <tr>
                  <td colspan="4" class="fw-bold">Total Item:</td>
                  <td><?= $fetch_session_row . ' ' . $amountpcs; ?></td>
               </tr>
               <tr>
                  <td colspan="4" class="fw-bold">Sub Total:</td>
                  <td>₱<?= $fetch_bi[0]->subTotal; ?>.00</td>
               </tr>
               <tr>
                  <td colspan="4" class="fw-bold">Other Fee:</td>
                  <td>₱<?= $fetch_bi[0]->other_fees; ?>.00</td>
               </tr>
               <tr>
                  <td colspan="4" class="fw-bold">Discount:</td>
                  <td><?= $fetch_bi[0]->discount; ?>%</td>
               </tr>
               <tr>
                  <td colspan="4" class="fw-bold">Total:</td>
                  <td class="fw-bold">₱<?= $fetch_bi[0]->amount; ?>.00</td>
               </tr>
            </tfoot>
         </table>
         <div class=" px-2 d-flex flex-column justify-content-center align-items-start">
            <p class="lead" style="font-size: 15px;"><span class="fw-bold">Remarks: </span></p>
            <p class="lead" style="font-size: 15px;"><?= $fetch_bi[0]->remarks; ?></p>
         </div>
      </div>
   </div>
</div>
<?php
      } else {
      ?>
<div class="d-flex flex-column justify-content-around align-items-center my-4 w-100">
   <img src="../../img/undraw_page_not_found_re_e9o6.svg" class="img-fluid mb-4" style="width: 400px;"
      alt="page not found">
   <h1 class="fw-bold">Page Not Found</h1>
</div>
<?php
      }
      ?>
<?php } else if (isset($_GET['page']) && $page == 'view' && $fetch_bi[0]->category == $category[0]) { ?>
<div id="print-container">
   <div class="px-2">
      <!-- header -->
      <div class="d-flex justify-content-between align-items-center">
         <div class="d-flex flex-column justify-content-start align-items-center">
            <img src="../../img/company-logo.png" class="img-fluid" style="width: 100px;" alt="company logo">
            <p class="lead" style="font-size: 15px;">General Trias Dairy Raisers Multi-Purpose Cooperative®</p>
         </div>
         <div class="d-flex flex-column justify-content-between align-items-end">
            <p class="lead" style="font-size: 15px;"> Brgy. Santiago, General Trias, Cavite</p>
            <p class="lead" style="font-size: 15px;">Tel. No. : 0923.737.1579</p>
            <p class="lead" style="font-size: 15px;">Mobile No. : 0920.908.6694</p>
         </div>
      </div>
      <div class="d-flex justify-content-center align-items-center mb-5">
         <p class="lead" style="font-size: 17px;"><span class="fw-bold">Tax Invoice</span></p>
      </div>
      <div class="d-flex justify-content-between align-items-center">
         <p class="lead" style="font-size: 15px;"><span class="fw-bold">Customer Name:
            </span><?= $fetch_bi[0]->client; ?></p>
         <p class="lead" style="font-size: 15px;"><span class="fw-bold">Invoice: </span> <?= $code; ?></p>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-3">
         <p class="lead" style="font-size: 15px;"><span class="fw-bold">Customer Address: </span>Tanza, Cavite</p>
         <p class="lead" style="font-size: 15px;"><span class="fw-bold">Date: </span> <?= $fetch_bi[0]->date; ?></p>
      </div>
      <!-- table -->
      <div class="table-responsive">
         <table class="table table-light table-bordered" style="width: 100%;">
            <thead align="center" class="align-middle fw-bold">
               <tr>
                  <td>Quantity</td>
                  <td>Price</td>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td><?= $fetch_bi[0]->milk_liter; ?> Liter/s</td>
                  <td>₱<?= $fetch_bi[0]->items; ?>.00 per Liter</td>
               </tr>
            </tbody>
            <tfoot class="text-end">
               <tr>
                  <td colspan="100%" class="text-center">***</td>
               </tr>
               <tr>
                  <td class="fw-bold">Sub Total:</td>
                  <td>₱<?= $fetch_bi[0]->subTotal; ?>.00</td>
               </tr>
               <tr>
                  <td class="fw-bold">Other Fee:</td>
                  <td>₱<?= $fetch_bi[0]->other_fees; ?>.00</td>
               </tr>
               <tr>
                  <td class="fw-bold">Discount:</td>
                  <td><?= $fetch_bi[0]->discount; ?>%</td>
               </tr>
               <tr>
                  <td class="fw-bold">Total:</td>
                  <td class="fw-bold">₱<?= $fetch_bi[0]->amount; ?>.00</td>
               </tr>
            </tfoot>
         </table>
         <div class=" px-2 d-flex flex-column justify-content-center align-items-start">
            <p class="lead" style="font-size: 15px;"><span class="fw-bold">Remarks: </span></p>
            <p class="lead" style="font-size: 15px;"><?= $fetch_bi[0]->remarks; ?></p>
         </div>
      </div>
   </div>
</div>
<?php } else if (isset($_GET['page']) && $page == 'create' && $code == $category[0]) {
      if (isset($_SESSION['create_bi_milk'])) {
         print_R($_SESSION['create_bi_milk']);
      } ?>
<form class="mx-4" method="POST" action="../process/process_invoice.php">
   <div class="d-flex flex-row justify-content-center align-items-center">
      <div class="form-floating mb-3 w-100">
         <textarea class="form-control" placeholder="Leave a remarks here" style="height: 100px;" name="Remarks"
            id="floatingTextarea"></textarea>
         <label for="floatingTextarea">Remarks</label>
      </div>
   </div>
   <div class="form-floating mb-3 w-100">
      <input type="text" class="form-control" placeholder="buyer name" name="client_name" id="client_name">
      <label for="client_name">Client Name:</label>
   </div>
   <div class="input-group mb-3">
      <span class="input-group-text" id="basic-addon1">₱</span>
      <div class="form-floating">
         <input type="number" class="form-control" placeholder="price per liter" name="price_per_liter"
            id="price_per_liter">
         <label for="price_per_liter">Price per liter:</label>
      </div>
      <span class="input-group-text" id="basic-addon1">/liter</span>
   </div>
   <div class="input-group mb-3">
      <div class="form-floating">
         <input type="number" class="form-control" placeholder="liters" name="milk_liter" id="milk_liter">
         <label for="milk_liter">Liter/s:</label>
      </div>
   </div>
   <div class="input-group mb-3">
      <span class="input-group-text" id="basic-addon1">₱</span>
      <div class="form-floating">
         <input type="number" class="form-control" placeholder="other fees" name="other_fees" id="other_fees">
         <label for="other_fees">Other Fees:</label>
      </div>
   </div>
   <div class="d-flex justify-content-between align-items-center mb-2">
      <h6 class="card-subtitle mb-2 text-muted">Sub Total: ₱<span id="subTotal">0</span>.00</h6>
      <h6 class="card-subtitle mb-2 text-muted">Sub Total w/other fess: ₱<span id="subTotalw_otherFees">0</span>.00</h6>
      <h6 class="card-subtitle mb-2 text-muted text-center d-flex justify-content-center align-items-center">
         Discount:
         <input style="width: 45px;" type="number" aria-describedby="discount" class="form-control mx-2"
            oninput="maxlength()" name="discount" id="discount" placeholder="0">
         %
      </h6>
   </div>
   <h5 class="card-title mb-4">Total: ₱<span id="Total">0</span>.00</h5>
   <p class="card-text">Create A New Sales Record for Milk Sales List.</p>
   <button type="submit" name="create_bi_invoice_milk" class="card-link btn btn-primary">Submit</button>
   <a href="./invoice_list.php?page=all&bi_code=none" class="card-link btn btn-secondary">Back</a>
</form>

<?php } else if (isset($_GET['page']) && $page == 'create' && $code == $category[1]) { ?>
<div class="d-flex flex-row justify-content-between align-items-center mt-4">
   <div class="p-3 border-bottom d-flex flex-column justify-content-center align-items-center" style="width: 100%;">
      <form method="POST" action="../process/process-buffalo.php"
         class="mb-4 d-flex flex-row justify-content-between align-items-center w-100">
         <h1 class="lead text-start">Input Forms</h1>
         <div class="d-flex flex-row justify-content-start align-items-center w-100">
            <div class="form-floating me-2 w-100">
               <select name="selected_buffalo" id="selected_buffalo" class="form-select">
                  <?php
                        echo "<option value='0' selected>select</option>";
                        foreach ($buffalos as $selected_buffalo) {
                           if ($selected_buffalo->Marked_As == NULL) {
                              echo "<option value='$selected_buffalo->Buffalo_id'>$selected_buffalo->Name [$selected_buffalo->Buffalo_id]</option>";
                           }
                        }
                        ?>
               </select>
               <label for="selected_buffalo" class="form-label">Buffalo :</label>
            </div>
            <div class="form-floating me-2 w-100">
               <input type="number" class="form-control" name="sell_price" id="sell_price" placeholder="price" required>
               <label for="form-label">Price (₱):</label>
            </div>
            <button type="button" name="buffalo_invoice_submit" id="buffalo_invoice_submit"
               class="btn btn-primary">Add</button>
         </div>
      </form>
      <table class="table table-responsive table-striped display" id="buffaloTable" style="width: 100%;">
         <thead class="text-center bg-primary text-light">
            <tr>
               <td>#</td>
               <td>Description</td>
               <td>Gender</td>
               <td>Weight</td>
               <td>Price</td>
               <td>Action</td>
            </tr>
         </thead>
         <tbody id="tableData" class="text-center">
            <?php
                  if (isset($_SESSION['create_bi'])) {
                     $indexes = 1;
                     $subTotal = 0;
                     $Total = 0;
                     foreach ($_SESSION['create_bi'] as $bi) {
                        echo '<tr>';
                        echo "<td>$indexes</td>";
                        echo '<td> <span class="fullName_bi">' . $bi["buffalo_name"] . '</span> [<span class="id_bi">' . $bi["buffalo_id"] . '</span>]</td>';
                        echo '<td class="">' . $bi["buffalo_gender"] . '</td>';
                        echo '<td class="">' . $bi["buffalo_weight"] . ' kg</td>';
                        echo '<td>₱' . $bi["price"] . '.00</td>';
                        echo "<td><button class='remove_btn btn btn-sm btn-outline-danger'><i class='bi bi-x'></i></button></td>";
                        echo '</tr>';
                        $indexes++;
                        $subTotal += $bi["price"];
                     }
                  }

                  ?>
         </tbody>
      </table>
   </div>

   <div class="mx-2"></div>
   <div class="card" style="width: 100%;">
      <form class="card-body" method="POST" action="../process/process_invoice.php">
         <div class="d-flex flex-row justify-content-center align-items-center">
            <div class="form-floating mb-3 w-100">
               <textarea class="form-control" placeholder="Leave a remarks here" style="height: 100px;" name="Remarks"
                  id="floatingTextarea"></textarea>
               <label for="floatingTextarea">Remarks</label>
            </div>
         </div>
         <div class="form-floating mb-3 w-100">
            <input type="text" class="form-control" placeholder="buyer name" name="client_name" id="client_name">
            <label for="client_name">Client Name:</label>
         </div>
         <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">₱</span>
            <div class="form-floating">
               <input type="number" class="form-control" placeholder="other fees" name="other_fees" id="other_fees">
               <label for="other_fees">Other Fees:</label>
            </div>
         </div>
         <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="card-subtitle mb-2 text-muted">Sub Total: ₱<span id="subTotal"><?= $subTotal; ?></span>.00</h6>
            <h6 class="card-subtitle mb-2 text-muted text-center d-flex justify-content-center align-items-center">
               Discount:
               <input style="width: 45px;" type="number" aria-describedby="discount" class="form-control mx-2"
                  oninput="maxlength()" name="discount" id="discount" placeholder="0">
               %
            </h6>
         </div>
         <h5 class="card-title mb-4">Total: ₱<span id="Total"><?= $subTotal; ?></span>.00</h5>
         <p class="card-text">Create A New Sales Record for Buffalo Sales List.</p>
         <button type="submit" name="create_bi_invoice_buffalo" class="card-link btn btn-primary">Submit</button>
         <a href="./invoice_list.php?page=all&bi_code=none" class="card-link btn btn-secondary">Back</a>
      </form>
   </div>
</div>
<?php } else if (isset($_GET['page']) && $page == 'sold_buffalo') { ?>
<div class="table-responsive">
   <table class="table table-hover table-bordered" id="soldTable" style="width: 100%; font-size: 14px;">
      <thead>
         <tr>
            <th>#</th>
            <th>Description</th>
            <th>Gender</th>
            <th>Weight</th>
            <th>Client</th>
            <th>Invoice Code</th>
            <th>Amount</th>
            <th>Date</th>
         </tr>
      </thead>
      <tbody id="listData" class="text-center">
         <?php

               $indexes = 1;
               foreach ($fetch_sold_buffalos as $bi) {
               ?>
         <tr>
            <td><?= $indexes; ?></td>
            <td><?= $bi->buffalo_name; ?>[<?= $bi->buffalo_id; ?>]</td>
            <td><?= $bi->buffalo_gender; ?></td>
            <td><?= $bi->buffalo_weight; ?> kg</td>
            <td><?= $bi->client; ?></td>
            <td data-target="bi_code"><?= $bi->code; ?></td>
            <td>₱<?= $bi->price; ?>.00</td>
            <td><?= $bi->date; ?></td>
            <!-- <td>
                                <a class=" dropdown-toggle btn btn-primary" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Action</a>
                                <ul class="dropdown-menu dropdown-sm">
                                    <li><a href="./invoice_list.php?page=view&bi_code=<?= $bi->code; ?>" class="dropdown-item">View</a></li>
                                    <li><a href="./invoice_list.php?page=edit&bi_code=<?= $bi->code; ?>" class="dropdown-item">Edit</a></li>
                                    <li><button type="button" class="dropdown-item" data-btn="remove">Remove</button></li>
                                </ul>
                            </td> -->
         </tr>
         <?php
                  $indexes++;
               }


               ?>
      </tbody>
   </table>
</div>
<?php } else if (isset($_GET['page']) && $page == 'return') { ?>
<div class="table-responsive">
   <table class="table table-bordered table-striped" id="returnTable" style="width: 100%; font-size: 14px;">
      <thead>
         <tr>
            <th>Invoice Code</th>
            <th>Client</th>
            <th>Category</th>
            <th>Items</th>
            <th>Sub Total</th>
            <th>Other Fees</th>
            <th>Discount</th>
            <th>Total</th>
            <th>Date</th>
            <th>Remarks</th>
            <th>Actions</th>
         </tr>
      </thead>
      <tbody id="listData" class="text-center">
         <?php

               $indexes = 1;
               if (!empty($fetch_return_bi)) {
                  foreach ($fetch_return_bi as $bi) {

                     $details = '';
                     $pcs = '';
                     if ($bi->category == $category[1]) {
                        ($bi->items > 1) ? $pcs = 'buffalos' : $pcs = 'buffalo';
                        $details = $bi->items . ' ' . $pcs;
                     } else if ($bi->category == $category[0]) {
                        $details = $bi->milk_liter . 'L : ₱' . $bi->items . ' per liter';
                     }
               ?>
         <tr>
            <td data-target="bi_code"><?= $bi->code; ?></td>
            <td><?= $bi->client; ?></td>
            <td><?= $bi->category; ?></td>
            <td><?= $details; ?></td>
            <td>₱<?= $bi->subTotal; ?>.00</td>
            <td>₱<?= $bi->other_fees; ?>.00</td>
            <td><?= $bi->discount; ?>%</td>
            <td>₱<?= $bi->amount; ?>.00</td>
            <td><?= $bi->date; ?></td>
            <td><?= $bi->remarks; ?></td>
            <td>
               <a class=" dropdown-toggle btn btn-sm btn-primary" data-bs-toggle="dropdown" href="#" role="button"
                  aria-expanded="false">Action</a>
               <ul class="dropdown-menu dropdown-sm">
                  <li><a href="./invoice_list.php?page=view&bi_code=<?= $bi->code; ?>" class="dropdown-item">View</a>
                  </li>
                  <li><button type="button" class="dropdown-item" data-btn="remove">Remove</button></li>
               </ul>
            </td>
         </tr>
         <?php
                     $indexes++;
                  }
               }
               ?>
      </tbody>
   </table>
</div>
<?php } else if (isset($_GET['page']) && $page == 'all') { ?>
<div class="table-responsive">
   <table class="table table-hover table-bordered table-striped" id="buffaloTable"
      style="width: 100%; height: 100%; font-size: 14px;">
      <thead>
         <tr>
            <th>Invoice Code</th>
            <th>Client</th>
            <th>Category</th>
            <th>Items</th>
            <th>Sub Total</th>
            <th>Other Fees</th>
            <th>Discount</th>
            <th>Total</th>
            <th>Date</th>
            <th>Remarks</th>
            <th>Actions</th>
         </tr>
      </thead>
      <tbody id="listData" class="text-center">
         <?php

               $indexes = 1;
               if (!empty($bi_list)) {
                  foreach ($bi_list as $bi) {

                     $details = '';
                     $pcs = '';
                     if ($bi->marked_as == NULL) {
                        if ($bi->category == $category[1]) {
                           ($bi->items > 1) ? $pcs = 'buffalos' : $pcs = 'buffalo';
                           $details = $bi->items . ' ' . $pcs;
                        } else if ($bi->category == $category[0]) {
                           $details = $bi->milk_liter . 'L : ₱' . $bi->items . ' per liter';
                        }
               ?>
         <tr>
            <td data-target="bi_code"><?= $bi->code; ?></td>
            <td><?= $bi->client; ?></td>
            <td><?= $bi->category; ?></td>
            <td><?= $details; ?></td>
            <td>₱<?= $bi->subTotal; ?>.00</td>
            <td>₱<?= $bi->other_fees; ?>.00</td>
            <td><?= $bi->discount; ?>%</td>
            <td>₱<?= $bi->amount; ?>.00</td>
            <td><?= $bi->date; ?></td>
            <td><?= $bi->remarks; ?></td>
            <td>
               <div class="btn-group-vertical gap-1">
                  <a href="./invoice_list.php?page=view&bi_code=<?= $bi->code; ?>" class="btn btn-sm btn-primary"
                     data-bs-toggle="tooltip" data-bs-title="View" da-bs-placement="left"><i
                        class="bi bi-binoculars-fill"></i></a>

                  <a href="./invoice_list.php?page=edit&bi_code=<?= $bi->code; ?>" class="btn btn-sm btn-success"
                     data-bs-toggle="tooltip" data-bs-title="Edit" da-bs-placement="left"><i
                        class="bi bi-pencil-fill"></i></a>

               </div>
               <div class="btn-group-vertical gap-1">
                  <button type="button" class="btn btn-sm btn-warning" data-btn="retrieve"><i
                        class="bi bi-box-arrow-in-left" data-bs-toggle="tooltip" data-bs-title="Return"
                        da-bs-placement="left"></i>
                  </button>
                  <button type="button" class="btn btn-sm btn-danger" data-btn="remove"><i class="bi bi-x-circle-fill"
                        data-bs-toggle="tooltip" data-bs-title="Delete" da-bs-placement="left"></i>
                  </button>
               </div>
               <!-- <a class=" dropdown-toggle btn btn-sm btn-primary" data-bs-toggle="dropdown" href="#" role="button"
                  aria-expanded="false">Action</a>
               <ul class="dropdown-menu dropdown-sm">
                  <li><a href="./invoice_list.php?page=view&bi_code=<?= $bi->code; ?>" class="dropdown-item">View</a>
                  </li>
                  <li><a href="./invoice_list.php?page=edit&bi_code=<?= $bi->code; ?>" class="dropdown-item">Edit</a>
                  </li>
                  <li><button type="button" class="dropdown-item" data-btn="retrieve">Return</button></li>
                  <li><button type="button" class="dropdown-item" data-btn="remove">Remove</button></li>
               </ul> -->
            </td>
         </tr>
         <?php
                        $indexes++;
                     }
                  }
               }
               ?>
      </tbody>
   </table>
</div>
<?php } else { ?>
<div class="d-flex flex-column justify-content-around align-items-center my-4 w-100">
   <img src="../../img/undraw_page_not_found_re_e9o6.svg" class="img-fluid mb-4" style="width: 400px;"
      alt="page not found">
   <h1 class="fw-bold">Page Not Found</h1>
</div>
<?php } ?>
<?php } else { ?>
<div class="d-flex flex-column justify-content-around align-items-center my-4 w-100">
   <img src="../../img/undraw_page_not_found_re_e9o6.svg" class="img-fluid mb-4" style="width: 400px;"
      alt="page not found">
   <h1 class="fw-bold">Page Not Found</h1>
</div>
<?php } ?>

<!-- FOOTER -->
<?php require_once '../includes/admin.footer.php'; ?>
<!-- <script src="https://unpkg.com/jspdf-invoice-template@1.4.0/dist/index.js"></script> -->

<?php if (isset($_SESSION['buffalo-message'])) : ?>
<script>
swal(
   "<?= $_SESSION['buffalo-message']['title']; ?>",
   "<?= $_SESSION['buffalo-message']['body']; ?>",
   "<?= $_SESSION['buffalo-message']['type']; ?>"
);
</script>
<?php endif;
unset($_SESSION['buffalo-message']);
?>

<?php if (isset($_GET['page']) && $page == 'edit' && $fetch_bi[0]->category == $category[1]) { ?>
<script>
$('#buffaloTable').dataTable();

var removeBtn = Array.from(document.querySelectorAll('.remove_btn'));
var buffalo_names = Array.from(document.querySelectorAll('.fullName_bi'));
var buffalo_ids = Array.from(document.querySelectorAll('.id_bi'));

var discount = document.querySelector('#discount');
var other_fees = document.querySelector('#other_fees');
var invalidChars = [
   "-",
   "+",
   "e",
];

var search = $('input[type=search]');

search.keydown(() => {
   removeBtn = Array.from(document.querySelectorAll('.remove_btn'));
   buffalo_names = Array.from(document.querySelectorAll('.fullName_bi'));
   buffalo_ids = Array.from(document.querySelectorAll('.id_bi'));
   removeBtns();
})

discount.addEventListener("keydown", function(e) {
   if (invalidChars.includes(e.key)) {
      e.preventDefault();
   };
   if (/^0/.test(this.value)) {
      this.value = this.value.replace(/^0/, "");
   };
});

discount.addEventListener("change", function(e) {
   $.ajax({
      type: 'POST',
      url: '../process/process_invoice.php',
      data: {
         update_bis: true,
         compute_bi: true,
         otherFee: $('#other_fees').val(),
         discount: $('#discount').val(),
      },
      success: ((response) => {
         const result = JSON.parse(response);
         var subTotal = result['subTotal'];
         var Total = result['Total'];

         $('#subTotal').html(subTotal);
         $('#Total').html(Total);
      })
   });
});

other_fees.addEventListener("change", function(e) {
   $.ajax({
      type: 'POST',
      url: '../process/process_invoice.php',
      data: {
         update_bis: true,
         compute_bi: true,
         otherFee: $('#other_fees').val(),
         discount: $('#discount').val()
      },
      success: ((response) => {
         const result = JSON.parse(response);
         var subTotal = result['subTotal'];
         var Total = result['Total'];
         console.log(result);

         $('#subTotal').html(subTotal);
         $('#Total').html(Total);
      })
   });
});

function maxlength(e) {
   if (discount.value.length > 2) {
      discount.value = discount.value.slice(0, 2);
   };
};

$('#buffalo_invoice_submit').click((e) => {
   e.preventDefault();
   $.ajax({
      type: 'POST',
      url: '../process/process_invoice.php',
      data: {
         update_bis: true,
         edit_bi: true,
         buffalo: $('#selected_buffalo').val(),
         price: $('#sell_price').val()
      },
      success: ((response) => {
         $('#tableData').html(response);
         $('#selected_buffalo option').eq(0).prop('selected', true);
         $('#sell_price').val("");
         removeBtn = Array.from(document.querySelectorAll('.remove_btn'));
         buffalo_names = Array.from(document.querySelectorAll('.fullName_bi'));
         buffalo_ids = Array.from(document.querySelectorAll('.id_bi'));
         removeBtns();
         console.log(response);

      })
   });

   $.ajax({
      type: 'POST',
      url: '../process/process_invoice.php',
      data: {
         update_bis: true,
         compute_bi: true,
         otherFee: $('#other_fees').val(),
         discount: $('#discount').val()
      },
      success: ((response) => {
         const result = JSON.parse(response);
         var subTotal = result['subTotal'];
         var Total = result['Total'];

         $('#subTotal').html(subTotal);
         $('#Total').html(Total);

      })
   });

});

function removeBtns() {
   removeBtn.forEach((element, i) => {
      $(element).click(() => {
         swal({
            title: "Removing Item: " + buffalo_ids[i].innerHTML,
            text: "Once deleted, you will not be able to recover.",
            icon: "warning",
            closeOnClickOutside: false,
            buttons: true,
            dangerMode: true,
         }).then((willDelete) => {
            if (willDelete) {
               $.ajax({
                  type: 'POST',
                  url: '../process/process_invoice.php',
                  data: {
                     update_bis: true,
                     remove_bi: true,
                     remove_item: buffalo_ids[i].innerHTML
                  },
                  success: ((response) => {
                     $('#tableData').html(response);
                     $('#selected_buffalo option').eq(0).prop('selected', true);
                     $('#sell_price').val("");
                     removeBtn = Array.from(document.querySelectorAll('.remove_btn'));
                     buffalo_names = Array.from(document.querySelectorAll(
                        '.fullName_bi'));
                     buffalo_ids = Array.from(document.querySelectorAll('.id_bi'));
                     removeBtns();
                  })
               });
               $.ajax({
                  type: 'POST',
                  url: '../process/process_invoice.php',
                  data: {
                     update_bis: true,
                     compute_bi: true,
                     otherFee: $('#other_fees').val(),
                     discount: $('#discount').val()
                  },
                  success: ((response) => {
                     console.log(response);
                     const result = JSON.parse(response);
                     var subTotal = result['subTotal'];
                     var Total = result['Total'];

                     $('#subTotal').html(subTotal);
                     $('#Total').html(Total);

                  })
               });
               swal("Poof! Your Item has been deleted!", {
                  icon: "success",
                  closeOnClickOutside: false
               });
            }
         });
      });
   });
}

removeBtns();
</script>
<?php } else if (isset($_GET['page']) && $page == 'edit' && $fetch_bi[0]->category == $category[0]) { ?>
<script>
var discount = document.querySelector('#discount');
var price_per_liter = document.querySelector('#price_per_liter');
var milk_liter = document.querySelector('#milk_liter');
var other_fees = document.querySelector('#other_fees');
var invalidChars = [
   "-",
   "+",
   "e",
];

console.log(discount);

discount.addEventListener("keydown", function(e) {
   if (invalidChars.includes(e.key)) {
      e.preventDefault();
   };
   if (/^0/.test(this.value)) {
      this.value = this.value.replace(/^0/, "");
   };
});

function display_result() {
   $.ajax({
      type: 'POST',
      url: '../process/process_invoice.php',
      data: {
         create_bis: true,
         compute_bi_milk: true,
         price_per_liter: $('#price_per_liter').val(),
         other_fees: $('#other_fees').val(),
         milk_liter: $('#milk_liter').val(),
         discount: $('#discount').val()
      },
      error: function(request, status, error) {
         alert(request.responseText);
         console.log(status);
      },
      success: ((response) => {
         console.log(response);
         const result = JSON.parse(response);
         var subTotal = result['subTotal'];
         var subTotalw_otherFees = result['subTotalw_otherFees'];
         var Total = result['Total'];

         $('#subTotalw_otherFees').html(subTotalw_otherFees);
         $('#subTotal').html(subTotal);
         $('#Total').html(Total);
      })
   });
}

discount.addEventListener("change", function(e) {
   display_result();
});

milk_liter.addEventListener("change", function(e) {
   display_result();
});

price_per_liter.addEventListener("change", function(e) {
   display_result();
});

other_fees.addEventListener("change", function(e) {
   display_result();
});

function maxlength(e) {
   if (discount.value.length > 2) {
      discount.value = discount.value.slice(0, 2);
   };
};
</script>
<?php } else if (isset($_GET['page']) && $page == 'create' && $code == $category[0]) { ?>
<script>
var discount = document.querySelector('#discount');
var price_per_liter = document.querySelector('#price_per_liter');
var milk_liter = document.querySelector('#milk_liter');
var other_fees = document.querySelector('#other_fees');
var invalidChars = [
   "-",
   "+",
   "e",
];

discount.addEventListener("keydown", function(e) {
   if (invalidChars.includes(e.key)) {
      e.preventDefault();
   };
   if (/^0/.test(this.value)) {
      this.value = this.value.replace(/^0/, "");
   };
});

function display_result() {
   $.ajax({
      type: 'POST',
      url: '../process/process_invoice.php',
      data: {
         create_bis: true,
         compute_bi_milk: true,
         price_per_liter: $('#price_per_liter').val(),
         other_fees: $('#other_fees').val(),
         milk_liter: $('#milk_liter').val(),
         discount: $('#discount').val()
      },
      error: function(request, status, error) {
         alert(request.responseText);
         console.log(status);
      },
      success: ((response) => {
         console.log(response);
         const result = JSON.parse(response);
         var subTotal = result['subTotal'];
         var subTotalw_otherFees = result['subTotalw_otherFees'];
         var Total = result['Total'];

         $('#subTotalw_otherFees').html(subTotalw_otherFees);
         $('#subTotal').html(subTotal);
         $('#Total').html(Total);
      })
   });
}

discount.addEventListener("change", function(e) {
   display_result();
});

milk_liter.addEventListener("change", function(e) {
   display_result();
});

price_per_liter.addEventListener("change", function(e) {
   display_result();
});

other_fees.addEventListener("change", function(e) {
   display_result();
});

function maxlength(e) {
   if (discount.value.length > 2) {
      discount.value = discount.value.slice(0, 2);
   };
};
</script>
<?php } else if (isset($_GET['page']) && $page == 'create' && $code == $category[1]) { ?>

<script>
$('#buffaloTable').dataTable();

var removeBtn = Array.from(document.querySelectorAll('.remove_btn'));
var buffalo_names = Array.from(document.querySelectorAll('.fullName_bi'));
var buffalo_ids = Array.from(document.querySelectorAll('.id_bi'));

var discount = document.querySelector('#discount');
var other_fees = document.querySelector('#other_fees');
var invalidChars = [
   "-",
   "+",
   "e",
];
var search = $('input[type=search]');

search.keydown(() => {
   removeBtn = Array.from(document.querySelectorAll('.remove_btn'));
   buffalo_names = Array.from(document.querySelectorAll('.fullName_bi'));
   buffalo_ids = Array.from(document.querySelectorAll('.id_bi'));
   removeBtns();
})

discount.addEventListener("keydown", function(e) {
   if (invalidChars.includes(e.key)) {
      e.preventDefault();
   };
   if (/^0/.test(this.value)) {
      this.value = this.value.replace(/^0/, "");
   };
});

discount.addEventListener("change", function(e) {
   $.ajax({
      type: 'POST',
      url: '../process/process_invoice.php',
      data: {
         create_bis: true,
         compute_bi: true,
         otherFee: $('#other_fees').val(),
         discount: $('#discount').val()
      },
      success: ((response) => {
         const result = JSON.parse(response);
         var subTotal = result['subTotal'];
         var Total = result['Total'];

         $('#subTotal').html(subTotal);
         $('#Total').html(Total);
      })
   });
});

other_fees.addEventListener("change", function(e) {
   $.ajax({
      type: 'POST',
      url: '../process/process_invoice.php',
      data: {
         create_bis: true,
         compute_bi: true,
         otherFee: $('#other_fees').val(),
         discount: $('#discount').val()
      },
      success: ((response) => {
         const result = JSON.parse(response);
         var subTotal = result['subTotal'];
         var Total = result['Total'];
         console.log(result);

         $('#subTotal').html(subTotal);
         $('#Total').html(Total);
      })
   });
});

function maxlength(e) {
   if (discount.value.length > 2) {
      discount.value = discount.value.slice(0, 2);
   };
};

$('#buffalo_invoice_submit').click((e) => {
   e.preventDefault();
   $.ajax({
      type: 'POST',
      url: '../process/process_invoice.php',
      data: {
         create_bis: true,
         create_bi: true,
         buffalo: $('#selected_buffalo').val(),
         price: $('#sell_price').val()
      },
      success: ((response) => {

         if (response) {
            $('#tableData').html(response);
         }
         $('#selected_buffalo option').eq(0).prop('selected', true);
         $('#sell_price').val("");
         removeBtn = Array.from(document.querySelectorAll('.remove_btn'));
         buffalo_names = Array.from(document.querySelectorAll('.fullName_bi'));
         buffalo_ids = Array.from(document.querySelectorAll('.id_bi'));
         removeBtns();

      })
   });

   $.ajax({
      type: 'POST',
      url: '../process/process_invoice.php',
      data: {
         create_bis: true,
         otherFee: $('#other_fees').val(),
         compute_bi: true,
         discount: $('#discount').val()
      },
      success: ((response) => {
         const result = JSON.parse(response);
         var subTotal = result['subTotal'];
         var Total = result['Total'];

         $('#subTotal').html(subTotal);
         $('#Total').html(Total);

      })
   });

});

function removeBtns() {
   removeBtn.forEach((element, i) => {
      $(element).click(() => {
         swal({
            title: "Removing Item: " + buffalo_ids[i].innerHTML,
            text: "Once deleted, you will not be able to recover.",
            icon: "warning",
            closeOnClickOutside: false,
            buttons: true,
            dangerMode: true,
         }).then((willDelete) => {
            if (willDelete) {
               $.ajax({
                  type: 'POST',
                  url: '../process/process_invoice.php',
                  data: {
                     create_bis: true,
                     remove_bi: true,
                     remove_item: buffalo_ids[i].innerHTML
                  },
                  success: ((response) => {
                     $('#tableData').html(response);
                     <?php
                           if (isset($_SESSION['create_bi'])) {
                              print_r($_SESSION['create_bi']);
                           };
                           ?>
                     $('#selected_buffalo option').eq(0).prop('selected', true);
                     $('#sell_price').val("");
                     removeBtn = Array.from(document.querySelectorAll('.remove_btn'));
                     buffalo_names = Array.from(document.querySelectorAll(
                        '.fullName_bi'));
                     buffalo_ids = Array.from(document.querySelectorAll('.id_bi'));
                     removeBtns();
                  })
               });
               $.ajax({
                  type: 'POST',
                  url: '../process/process_invoice.php',
                  data: {
                     create_bis: true,
                     compute_bi: true,
                     discount: $('#discount').val(),
                     otherFee: $('#other_fees').val(),
                  },
                  success: ((response) => {
                     const result = JSON.parse(response);
                     var subTotal = result['subTotal'];
                     var Total = result['Total'];

                     $('#subTotal').html(subTotal);
                     $('#Total').html(Total);

                  })
               });
               swal("Poof! Your Item has been deleted!", {
                  icon: "success",
                  closeOnClickOutside: false
               });
            }
         });
      });
   });
}

removeBtns();
</script>
<?php } else if (isset($_GET['page']) && $page == 'view' && $fetch_bi[0]->category == $category[0]) { ?>
<script>
function doPrint() {
   $(document).ready(() => {
      var body = document.querySelector('#body').innerHTML;
      var footer = document.querySelector('#footer').innerHTML;
      var data = document.querySelector('#print-container').innerHTML;
      document.querySelector('#body').innerHTML = data;
      setTimeout(() => {
         window.print();
         document.querySelector('#body').innerHTML = body;
      }, 1500);
   })
}
</script>
<?php } else if (isset($_GET['page']) && $page == 'view' && $fetch_bi[0]->category == $category[1]) { ?>
<script>
function doPrint() {
   $(document).ready(() => {
      var body = document.querySelector('#body').innerHTML;
      var footer = document.querySelector('#footer').innerHTML;
      var data = document.querySelector('#print-container').innerHTML;
      document.querySelector('#body').innerHTML = data;
      setTimeout(() => {
         window.print();
         document.querySelector('#body').innerHTML = body;
      }, 1500);
   })
}
</script>
<?php } else if (isset($_GET['page']) && $page == 'sold_buffalo') { ?>
<script>
$('#soldTable').dataTable();
</script>
<?php } else if (isset($_GET['page']) && $page == 'return') { ?>
<script>
$('#returnTable').dataTable();

function remove_BI() {
   var bi_id = '';


}
</script>
<?php } else if (isset($_GET['page']) && $page == 'all') { ?>
<script>
$('#buffaloTable').dataTable();
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
   return new bootstrap.Tooltip(tooltipTriggerEl)
})

var remove_btn = Array.from($('[data-btn="remove"]'));
var retrieve_btn = Array.from($('[data-btn="retrieve"]'));
var bi_code = Array.from($('[data-target="bi_code"]'));


var search = $('input[type=search]');

search.keydown(() => {
   remove_btn = Array.from($('[data-btn="remove"]'));
   retrieve_btn = Array.from($('[data-btn="retrieve"]'));
   bi_code = Array.from($('[data-target="bi_code"]'));
   RetrieveBtns();
   RemoveBtns();
})

function RemoveBtns() {
   remove_btn.forEach((data, i) => {
      $(data).click(() => {
         swal({
            title: "Remove Invoice: " + bi_code[i].innerText,
            text: "Once deleted, you will not be able to recover.",
            icon: "warning",
            closeOnClickOutside: false,
            buttons: true,
            dangerMode: true,
         }).then((willDelete) => {
            if (willDelete) {
               $.ajax({
                  type: 'POST',
                  url: '../process/process_invoice.php',
                  data: {
                     remove_invoice: bi_code[i].innerText
                  }
               });
               swal("Poof! Your Sales Invoice has been deleted!", {
                     icon: "success",
                     closeOnClickOutside: false
                  })
                  .then((value) => {
                     location.reload();
                  });
            }
         });
      });
   });
};

function RetrieveBtns() {
   retrieve_btn.forEach((data, i) => {
      $(data).click(() => {
         swal({
            title: "Return Invoice: " + bi_code[i].innerText + " ?",
            closeOnClickOutside: false,
            buttons: true,
         }).then((willDelete) => {
            if (willDelete) {
               $.ajax({
                  type: 'POST',
                  url: '../process/process_invoice.php',
                  data: {
                     retrieve_invoice: bi_code[i].innerText
                  }
               });
               swal("Poof! Your Sales Invoice has been returned!", {
                     icon: "success",
                     closeOnClickOutside: false
                  })
                  .then((value) => {
                     location.reload();
                  });
            }
         });
      });
   });
}

RetrieveBtns();
RemoveBtns();
</script>

<?php } ?>