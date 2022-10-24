<!-- NAVIGATION BAR -->
<nav class="navbar navbar-expand-sm py-0 navbar-light bg-light w-100 border-bottom"
   style="z-index: 50; position: fixed;">
   <div class="container-fluid d-flex justify-content-between align-items-center">
      <a href="../../../home.php" class="navbar-brand me-5 text-dark " style="font-family: Public Sans ExBold;"><img
            class="img-fluid logo" src="../../../img/company-logo.png" alt="logo"> Dairy Raisers ®</a>
      <div class="border-start border-end px-4 gap-3 d-flex justify-content-start align-items-center"
         style="font-family: Public Sans Light;">
         <a href="../../../home.php" style="font-size: 14px;" class="nav-link text-dark me-2"><i
               class="me-2 bi bi-house-fill"></i> Home</a>
         <a href="../../../shop/products.php?page=all" style="font-size: 14px;" class="nav-link text-dark me-2"><i
               class="me-2 bi bi-bag-check-fill"></i> Shop</a>
         <a href="#" style="font-size: 14px;" class="nav-link text-dark me-2"><i
               class="me-2 bi bi-telephone-inbound-fill"></i> Contact</a>
         <a href="#" style="font-size: 14px;" class="nav-link text-dark me-2"><i
               class="me-2 bi bi-patch-question-fill"></i> FAQ</a>
      </div>
      <!-- USER LOGIN -->
      <?php if (isset($_SESSION['users'])) {
        ?>
      <ul class="navbar-nav">
         <p style="font-family: Public Sans Light;font-size: 13.5px; " class="text-center my-auto d-block me-3">Welcome,
            <span style="font-family: Public Sans ExBold; letter-spacing: 1px;"
               class="text-decoration-underline"><?= $user_name; ?></span> !
         </p>
         <li class="nav-item">
            <div class="dropdown">
               <button type="button" class="btn btn-outline-dark" data-bs-toggle="dropdown"><i data-bs-toggle="tooltip"
                     data-bs-placement="bottom" title="User Settings and Logout."
                     class="fa-solid fa-user-gear"></i></button>
               <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item myAccount" href="../../../user/account/profile.php"><i
                           class="fa-solid fa-gears me-2"></i> Settings</a></li>
                  <li>
                     <hr class="dropdown-divider">
                  </li>
                  <li><a class="dropdown-item text-danger" href="../../../../configs/logout.php"><i
                           class="fa-solid fa-right-from-bracket me-2"></i> Logout</a></li>
               </ul>
            </div>
         </li>
         <li class="nav-item fs-5 mx-2 lead d-flex justify-content-center align-items-center">
            <a href="../../../shop/cart.php"
               class="btn btn-outline-primary d-flex justify-content-center align-items-center">
               <i class="fa-solid fa-cart-shopping" data-bs-toggle="tooltip" data-bs-placement="bottom"
                  title="View Cart Items and Checkout."></i>
               <span style="font-family: Public Sans ExBold;" class="ms-2"><?= $item_rows; ?></span>
            </a>
         </li>
      </ul>
      <!-- BY DEFAULT -->
      <?php } else { ?>
      <ul class="navbar-nav gap-3">
         <li class="nav-item">
            <a href="../../../entry/login.php"
               class="btn d-flex align-items-center justify-content-center btn-outline-primary"><i
                  class="fa-solid fs-5 fa-circle-user me-2"></i> Login</a>
         </li>
         <li class="nav-item">
            <a href="../../../entry/signup.php"
               class="btn d-flex align-items-center justify-content-center btn-primary"><i
                  class="fa-solid fa-user-plus me-2"></i> Sign Up</a>
         </li>
      </ul>
   </div>
   <?php } ?>
   </div>
</nav>

<?php
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $url = "https://";
else
    $url = "http://";
// Append the host(domain name, ip) to the URL.   
$url .= $_SERVER['HTTP_HOST'];

// Append the requested resource location to the URL   
$url .= $_SERVER['REQUEST_URI'];

if ($url != 'http://localhost:3000/home.php') {
?>
<div class="w-100" style="z-index :52;height: 20px;"></div>
<?php
}
?>