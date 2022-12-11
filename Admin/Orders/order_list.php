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

if (isset($_SESSION['admins'])) {
   $order_qry = "SELECT * FROM `order_details` ORDER BY `date`";
   $order_stmt = $main_conn->prepare($order_qry);
   $order_stmt->execute();
   $order_details = $order_stmt->fetchAll();
} else {
   header('Location: ../home.php');
   exit();
}

$title = 'Orders | Dairy Raisers';
$path = 2;
require_once '../includes/admin.header.php';
require_once '../includes/admin.sidebar.php';

?>

<!-- HEADER CONTAINER -->

<section class="border-bottom d-flex flex-row justify-content-between align-items-center overflow-hidden pb-2 mb-3">
   <div class="d-flex justify-content-start align-items-center flex-row">
      <div class="header-container bg-primary d-flex flex-row justify-content-end align-items-center">
         <i class="fa-solid fa-cart-arrow-down text-light img-fluid me-5" style="font-size: 60px;"></i>
      </div>
      <div class="d-flex flex-column justify-content-center align-items-start me-5">
         <h4 class="fw-bold py-0">List of Orders <i class="fa-solid fa-list ms-1"></i></h4>
         <div class="nav-item d-flex flex-row justify-content-center align-items-center">
            <p><i class="fa-solid fa-clock"></i> <span id="clock"></span></p>
         </div>
      </div>
      <div
         class="ms-1 d-flex flex-column justify-content-between align-items-center py-2 px-3 rounded bg-primary text-light">
      </div>
   </div>
   <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#add-product"><i
         class="bi bi-plus-circle"></i> Add New Order</button>
</section>

<!-- ORDER LIST -->
<div class="container-fluid">
   <section id="order_storage" class="table-responsive row row-cols-4">
   </section>
</div>

<?php require_once '../includes/admin.footer.php'; ?>

<?php if (isset($_SESSION['order-message'])) : ?>
<script>
swal(
   "<?= $_SESSION['order-message']['title']; ?>",
   "<?= $_SESSION['order-message']['body']; ?>",
   "<?= $_SESSION['order-message']['type']; ?>"
);
</script>
<?php endif;
unset($_SESSION['order-message']);
?>

<script>
$(document).ready(() => {
   $('#orderTable').DataTable();
})

var data_storage = document.querySelector('#order_storage');
call_order();
setInterval(() => {
   call_order();
}, 5000);

function call_order() {
   $.ajax({
      type: 'POST',
      url: '../../validation/order_call.php',
      data: {
         order_call: true
      },
      success: ((response) => {
         console.log(response)
         if (response) {
            $(data_storage).html(response);
         }
      })
   });
}

function RealTimeClock() {
   var rtClock = new Date();

   var day = rtClock.getDay();
   var hours = rtClock.getHours();
   var minutes = rtClock.getMinutes();
   var seconds = rtClock.getSeconds();

   var amPm = (hours < 12) ? "AM" : "PM";

   hours = (hours > 12) ? hours - 12 : hours;

   hours = ("0" + hours).slice(-2);
   minutes = ("0" + minutes).slice(-2);
   seconds = ("0" + seconds).slice(-2);

   document.getElementById('clock').innerHTML = rtClock;

   var t = setTimeout(RealTimeClock, 500);
}
</script>