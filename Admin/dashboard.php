<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../configs/database.php';
require_once '../includes/classes.php';


$api = new MyAPI($main_conn);

$total_product = $api->Read('products', 'all', NULL, NULL, true);
$total_buffalo = $api->Read('buffalos', 'all', NULL, NULL, true);

$title = "Dashboard | Dairy Raisers";
$path = 1;
require_once './includes/admin.header.php';
require_once './includes/admin.sidebar.php';

?>
<?php
if (isset($_SESSION['admins'])) {
?>

   <div class="container-fluid border p-3">
      <div class="row gap-3 px-2">
         <div class="col border">
            <div class="d-flex justify-content-between align-items-center p-2 pt-3">
               <div style="font-family: Public Sans Light;" class="text-start">
                  <p style="font-size: 11px;" class="text-muted mt-0 mb-1">BUFFALOS</p>
                  <h3 style="font-family: Kayak Bold;" class="mb-3">50</h3>
                  <div style="font-size: 11px;" class="text-muted d-flex gap-4 justify-content-center align-items-evenly w-100">
                     <p><i class="fa-solid fa-mars"></i> : 50</p>
                     <p><i class="fa-solid fa-venus"></i> : 50</p>
                     <p><i class="fa-solid fa-viruses"></i> : 50</p>
                     <p><i class="fa-solid fa-skull-crossbones"></i> : 50</p>
                  </div>
               </div>
               <i style="font-size: 35px;" class="fa-solid fa-cow text-light p-2 border bg-primary border-primary rounded-circle"></i>
            </div>
         </div>
         <div class="col border">
            <div class="d-flex justify-content-between align-items-center p-2 pt-3">
               <div style="font-family: Public Sans Light;" class="text-start">
                  <p style="font-size: 11px;" class="text-muted mt-0 mb-1">PRODUCTS</p>
                  <h3 style="font-family: Kayak Bold;" class="mb-3">50</h3>
                  <div style="font-size: 11px;" class="text-muted d-flex gap-4 justify-content-center align-items-evenly w-100">
                     <p><span class="me-1">stock</span><i class="fa-solid fa-layer-group"></i> : <span></span> 50</p>
                     <p><span class="me-1">holding</span><i class="fa-solid fa-hands-holding"></i> : <span></span> 50</p>
                  </div>
               </div>
               <div class=" p-2 px-3 border bg-primary border-primary rounded-circle">
                  <i style="font-size: 35px;" class="fa-solid fa-bottle-water text-light"></i>
               </div>
            </div>
         </div>
         <div class="col border">

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