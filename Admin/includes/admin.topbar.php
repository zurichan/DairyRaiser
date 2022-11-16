<!-- top navigation bar -->
<nav class="user-select-none topbar-wrapper navbar navbar-expand-sm py-0 px-2 mb-3 navbar-light bg-light border-bottom"
   style="position: fixed; z-index: 50;">
   <div class="container-fluid text-center h-100">
      <div class="d-flex justify-content-center align-items-stretch border-0">
         <button class="px-2 me-3 nav-link border-0" style="background-color:  inherit;" type="button" id="sidebar_btn">
            <i class="fa-solid fa-bars"></i>
         </button>
         <div class="d-flex justify-content-start align-items-center text-primary">
            <a href="../../Admin/dashboard.php" class="nav-link border-0 me-4" style="background-color: inherit;">
               Dashboard
            </a>
            <a href="../../Admin/dashboard.php" class="nav-link border-0" style="background-color: inherit;">
               Settings
            </a>
         </div>
      </div>

      <!-- ADMIN ACCOUNT -->
      <div class="d-flex justify-content-center align-items-stretch h-100">
         <!-- Split dropstart button -->
         <div class="btn-group dropstart">
            <button type="button" class="btn btn-dark dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"
               aria-expanded="false">
               <span class="visually-hidden">Toggle Dropstart</span>
            </button>
            <ul class="dropdown-menu">
               <!-- Dropdown menu links -->
               <li><a href="../../../../../configs/logout.php" class="dropdown-item"><i
                        class="bi bi-gear me-1"></i>Settings</a></li>
               <li>
                  <hr class="dropdown-divider">
               </li>
               <li><a href="../../../../../configs/logout.php" class="dropdown-item text-danger"><i
                        class="bi bi-door-closed me-1"></i>Logout</a></li>
            </ul>
            <button type="button" class="btn btn-outline-dark">
               <?= $_SESSION['admins'][0]->firstname; ?>
            </button>
         </div>
      </div>
</nav>