<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../configs/database.php';
require_once '../includes/classes.php';


$api = new MyAPI($main_conn);

$total_product = $api->Read('products', 'all', NULL, NULL, true);
$total_buffalo = $api->Read('buffalos', 'all', NULL, NULL, true);

$all_buffalo = $api->Read('buffalos', 'all');
$male_buffalo = 0;
$female_buffalo = 0;
$sick_buffalo = 0;
$deceased_buffalo = 0;
foreach ($all_buffalo as $buffalo) {
   if ($buffalo->Marked_As == 'Deceased') {
      $deceased_buffalo++;
   } else {
      ($buffalo->Gender == 'Male') ? $male_buffalo++ : NULL;
      ($buffalo->Gender == 'Female') ? $female_buffalo++ : NULL;
      ($buffalo->Health_Status == 'Sick') ? $sick_buffalo++ : NULL;
   }
}

$sum_StockProduct = $api->Sum('products', 'all', 'stock_avail');
$sum_HoldProduct = $api->Sum('products', 'all', 'holding_stock');

$title = "Dashboard | Dairy Raisers";
$path = 1;
require_once './includes/admin.header.php';
require_once './includes/admin.sidebar.php';

?>
<?php
if (isset($_SESSION['admins'])) {
?>

<div class="container-fluid border p-3">
   <div style="font-family: Aquino;" class="row">
      <h3>Dairy Raisers Dashboard</h3>
   </div>
   <div class="row gap-3 px-2">
      <div class="col border">
         <div class="d-flex justify-content-between align-items-center p-1 pt-3">
            <div style="font-family: Public Sans Light;" class="text-start">
               <p style="font-size: 11px;" class="text-muted mt-0 mb-1">SALES</p>
               <h3 style="font-family: Kayak Bold;" class="mb-3">₱<?= $total_product; ?>.00</h3>
               <div style="font-size: 11px;"
                  class="text-muted d-flex flex-column justify-content-evenly align-items-start w-100">
                  <p><span class="me-1">buffalos</span><i class="fa-solid fa-cow"></i> <span class="mx-1">:</span>
                     <?= $sum_StockProduct->output; ?></p>
                  <p><span class="me-1">products</span><i class="fa-solid fa-bottle-water"></i> <span
                        class="mx-1">:</span>
                     <?= $sum_HoldProduct->output; ?></p>
               </div>
            </div>
            <div class=" p-2 border bg-primary border-primary rounded-circle">
               <i style="font-size: 45px;" class="fa-solid fa-coins text-light"></i>
            </div>
         </div>
      </div>
      <div class="w-100 col border">
         <div class="d-flex justify-content-between align-items-center p-1 pt-3">
            <div style="font-family: Public Sans Light;" class="text-start w-100">
               <p style="font-size: 11px;" class="text-muted mt-0 mb-1">BUFFALOS</p>
               <h3 style="font-family: Kayak Bold;" class="mb-3"><?= $total_buffalo; ?></h3>
               <div style="font-size: 11px;" class="container">
                  <div class="row">
                     <div class="col  ">
                        <p><span class="me-1">male</span><i class="fa-solid fa-mars"></i> <span class="mx-1">:</span>
                           <?= $male_buffalo; ?></p>
                     </div>
                     <div class="col  ">
                        <p><span class="me-1">female</span><i class="fa-solid fa-venus"></i> <span class="mx-1">:</span>
                           <?= $female_buffalo; ?></p>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col  ">
                        <p><span class="me-1">sick</span><i class="fa-solid fa-viruses"></i> <span class="mx-1">:</span>
                           <?= $sick_buffalo; ?></p>
                     </div>
                     <div class="col  ">
                        <p><span class="me-1">deceased</span><i class="fa-solid fa-skull-crossbones"></i> <span
                              class="mx-1">:</span> <?= $deceased_buffalo; ?></p>
                     </div>
                  </div>
               </div>
            </div>
            <i style="font-size: 45px;"
               class="fa-solid fa-cow text-light p-2 border bg-primary border-primary rounded-circle"></i>
         </div>
      </div>
      <div class="col border">
         <div class="d-flex justify-content-between align-items-center p-1 pt-3">
            <div style="font-family: Public Sans Light;" class="text-start">
               <p style="font-size: 11px;" class="text-muted mt-0 mb-1">PRODUCTS</p>
               <h3 style="font-family: Kayak Bold;" class="mb-3"><?= $total_product; ?></h3>
               <div style="font-size: 11px;"
                  class="text-muted d-flex flex-column justify-content-center align-items-evenly w-100">
                  <p><span class="me-1">stock</span><i class="fa-solid fa-layer-group"></i> <span class="mx-1">:</span>
                     <?= $sum_StockProduct->output; ?></p>
                  <p><span class="me-1">holding</span><i class="fa-solid fa-hands-holding"></i> <span
                        class="mx-1">:</span>
                     <?= $sum_HoldProduct->output; ?></p>
               </div>
            </div>
            <div class=" py-2 px-3 border bg-primary border-primary rounded-circle">
               <i style="font-size: 45px;" class="fa-solid fa-bottle-water text-light"></i>
            </div>
         </div>
      </div>
   </div>
   <!-- <div class="card w-100">
         <div class="row g-0">
            <div class="col-md-4 bg-primary d-flex px-3">
               <img src="../img/buffalo3.svg" alt="buffalo" class="img-fluid rounded-start">
            </div>
            <div class="col-md-8">
               <div class="card-body text-center" style="font-family: Public Sans Light;">
                  <h5 class="card-title" style="font-family: Public Sans ExBold; ">TOTAL BUFFALO :
                     50</h5>
                  <div class="py-0 text-center mt-3 pt-3 border-top row row-cols-2" style="font-size: 18px;">
                     <p class="card-text col"><i class="fa-solid fa-mars me-2"></i> : <span class="ms-1">10</span></p>
                     <p class="card-text col"><i class="fa-solid fa-viruses me-2"></i> : <span class="ms-1">10</span>
                     </p>
                     <p class="card-text col"><i class="fa-solid fa-venus me-2"></i> : <span class="ms-1">10</span></p>
                     <p class="card-text col"><i class="fa-solid fa-skull-crossbones me-2"></i> : <span
                           class="ms-1">10</span></p>
                  </div>
               </div>
            </div>
         </div>
      </div> -->
   <!-- <div class="card w-100">
         <div class="row g-0">
            <div class="text-center col-md-4 bg-primary d-flex jusstify-content-center align-items-center px-3">
               <i class="fa-solid fa-bottle-water text-light mx-auto d-block" style="font-size: 120px;"></i>
            </div>
            <div class="col-md-8">
               <div class="card-body text-center" style="font-family: Public Sans Light;">
                  <h5 class="card-title" style="font-family: Public Sans ExBold;">TOTAL PRODUCTS : 50</h5>
                  <div class="text-center mt-3 pt-3 border-top row row-cols-2" style="font-size: 18px;">
                     <p class="card-text col"><i class="fa-solid fa-layer-group me-2"></i> : <span
                           class="ms-1">10</span></p>
                     <p class="card-text col"><i class="fa-solid fa-hands-holding me-2"></i> : <span
                           class="ms-1">10</span>
                     </p>
                  </div>
               </div>
            </div>
         </div>
      </div> -->
</div>


<?php
   require_once './includes/admin.footer.php';
} else {
   header('Location: ../entry/login.php');
}
?>