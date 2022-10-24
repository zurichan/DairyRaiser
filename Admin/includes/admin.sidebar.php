<nav class="sidebar-wrapper navbar navbar-dark bg-dark pt-0" style="height: 100vh;
    width: 200px;
    transition: all 0.3s ease-in-out;
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    overflow: hidden;">

   <div class="container-fluid px-0 d-flex flex-column justify-content-center align-items-center w-100">
      <div class="w-100 text-center d-flex justify-content-evenly align-items-center" style=" height: 70px;">
         <img src="../../img/company-logo.png" class="img-fluid" style="width: 50px;" alt="company logo">
         <p class="navbar-brand" style="font-family: Aquino; font-size: 16px;"> Dairy Raisers</p>
      </div>
      <div class="w-100" style="background-color: #343a40;">
         <ul class="navbar-nav my-auto mb-2 mb-lg-0">
            <li class="nav-item d-flex py-auto justify-content-between align-items-center px-2">
               <a class="nav-link d-flex justify-content-start align-items-center" href="../../Admin/dashboard.php"
                  style="font-size: 13px;">
                  <img src="../../img/dashboard3.svg" style="width: 16px;" alt="dashboard" class="img-fluid me-2">
                  Dashboard
               </a>
            </li>
            <hr class="bg-dark p-0 m-0 py-2">
            <!-- BUFFALOS -->
            <li class="nav-item d-flex py-auto justify-content-center align-items-center py-0 px-2">
               <button type="button" data-bs-toggle="collapse" data-bs-target="#BuffalosNavBar"
                  class="nav-link border-0 d-flex justify-content-between align-items-center w-100"
                  style="font-size: 13px; background-color: #343a40;">
                  <div class="d-flex justify-content-start align-items-center">
                     <img src="../../img/buffalo3.svg" style="width: 16px;" alt="buffalo" class="img-fluid me-2">
                     Buffalo
                  </div>
                  <i class="bi bi-caret-down-fill"></i>
               </button>
            </li>
            <div class="collapse" id="BuffalosNavBar">
               <div style="font-size: 13px;"
                  class="bg-dark d-flex flex-column justify-content-center aligm-items-center text-end py-1 pe-4 w-100">
                  <a class="nav-link text-decoration-none"
                     href="../../../Admin/Manage_Buffalos/buffalo_list.php?page=all">Buffalos List</a>
                  <a class="nav-link text-decoration-none" href="../../../Admin/Manage_Buffalos/stocks&yield.php">Milk
                     Stocks & Yield</a>
                  <a class="nav-link text-decoration-none"
                     href="../../../Admin/Manage_Buffalos/invoice_list.php?page=all&bi_code=none">Invoice List</a>
               </div>
            </div>

            <!-- INVENTORY -->
            <li class="nav-item d-flex py-auto justify-content-center align-items-center py-0 px-2">
               <button type="button" data-bs-toggle="collapse" data-bs-target="#InventoryNavBar"
                  class="nav-link border-0 d-flex justify-content-between align-items-center w-100"
                  style="font-size: 13px; background-color: #343a40;">
                  <div class="d-flex justify-content-start align-items-center">
                     <img src="../../img/inventory.svg" style="width: 16px;" alt="inventory" class="img-fluid me-2">
                     Inventory
                  </div>
                  <i class="bi bi-caret-down-fill"></i>
               </button>
            </li>
            <div class="collapse" id="InventoryNavBar">
               <div style="font-size: 13px;"
                  class="bg-dark d-flex flex-column justify-content-center aligm-items-center text-end py-1 pe-4 w-100">
                  <a class="nav-link text-decoration-none" href="../../../Admin/Inventory/product_list.php">Products</a>
                  <a class="nav-link text-decoration-none" href="../../../Admin/Inventory/raw-materials.php">Raw
                     Materials</a>
               </div>
            </div>
         </ul>
      </div>


   </div>
</nav>