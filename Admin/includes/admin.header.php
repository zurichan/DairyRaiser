<?php
if (!isset($_SESSION['admins'])) {
   header('Location: ../../../index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" href="../../../img/company-logo.png" type="image/x-icon">

   <!-- Custom Styles -->
   <link rel="stylesheet" href="../../css/footer.css">
   <link rel="stylesheet" href="../../css/admin.navbar.css">
   <link rel="stylesheet" href="../../css/admin.css">
   <link rel="stylesheet" href="../../css/preLoader.css">
   <link rel="stylesheet" href="../../css/variables.css">

   <!-- Box Icons -->
   <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
   <!-- Font Awesome -->
   <script src="https://kit.fontawesome.com/95c5b29ec4.js" crossorigin="anonymous"></script>
   <!-- AJAX -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
   <!-- Bootstrap 5 -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
      integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
   <link rel="stylesheet" href="https://cdn.datatables.net/select/1.4.0/css/select.dataTables.min.css">
   <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.dataTables.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">
   <!-- Bootstrap 5 Data Tables -->
   <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
   <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.2/css/buttons.bootstrap5.min.css">
   <!-- Bootstrap 5 icon -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.3/font/bootstrap-icons.css">

   <!-- prevent resubmission -->


   <!-- Document Title -->
   <title><?php echo $title; ?></title>

</head>

<body id="body" onload="RealTimeClock()" class="body">
   <!-- pre loader -->
   <div class="preLoader">
      <div class="loadingio-spinner-ellipsis-zntmt5v33yr">
         <div class="ldio-5dmmhahjwqd">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
            <div></div>
         </div>
      </div>
   </div>

   <main class="overflow-auto mx-auto d-block my-container">

      <?php
      if ($path == 2) {
         require_once '../../Admin/includes/admin.topbar.php';
      } else if ($path == 1) {
         require_once '../Admin/includes/admin.topbar.php';
      }
      ?>
      <div class="w-100 mb-2" style="height: 43px;"></div>

      <!-- MAIN CONTAINER -->
      <div class="overflow-hidden main-content container-fluid d-block mx-auto px-2 pb-3">
         <!-- SECOND CONTAINER -->
         <div class="border p-3 bg-light">