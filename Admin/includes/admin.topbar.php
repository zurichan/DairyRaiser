<!-- top navigation bar -->
<nav class="user-select-none topbar-wrapper navbar navbar-expand-sm py-0 px-2 mb-3 navbar-light bg-light border-bottom overflow-hidden" style="position: fixed; z-index: 50;">
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
            <div class="admin_nav text-center">
                <div class="navbar-brand d-flex flex-row justify-content-between align-items-center" style="font-size: 16px;">
                    <i class="bi bi-person-circle px-2 fs-5"></i>
                    <span class="pe-3 mask flex-center"><?php echo $_SESSION['admins'][0]->firstname; ?></span>
                    <button type="buton" class="p-1 btn btn-sm btn-outline-dark" id="account_btn" style="font-size: 13px;">
                        <i class="bi bi-caret-left-fill"></i>
                    </button>
                </div>
            </div>
         
            <div class="admin_configs border bg-dark text-light d-flex justify-content-between align-items-center">
                <a href="../../../../../configs/logout.php" class="nav-link mx-3"  style="font-size: 13px;"><i class="bi bi-gear me-1"></i>Settings</a>
                <a href="../../../../../configs/logout.php" class="nav-link mx-3" style="font-size: 13px;"><i class="bi bi-door-closed me-1"></i>Logout</a>
            </div>
           
            
        </div>
</nav>