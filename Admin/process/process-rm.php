<?php
session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../../includes/classes.php';

$api = new MyAPI($main_conn);
$date = date('Y-m-d h:i:s');

if (isset($_SESSION['admins'])) {
   /** ADD NEW SUPPLIER */
   if (isset($_POST['add-new-supplier'])) {
      $all_suppliers = $api->Read('supplier', 'all');
      $new_supplier = filter_input(INPUT_POST, 'new-supplier', FILTER_SANITIZE_SPECIAL_CHARS);
      $validate_supplier = str_replace(" ", "", $new_supplier);
      $validate_supplier = strtolower($validate_supplier);
      $err = 0;
      (empty($new_supplier)) ? $err++ : NULL;
      foreach ($all_suppliers as $supplier) {
         $supplier = str_replace(" ", "", $supplier->supplier_name);
         $supplier = strtolower($supplier);
         if ($validate_supplier == $supplier) {
            $err++;
            break;
         }
      }
      if ($err == 0) {
         $api->Create('supplier', [
            '1' => ['supplier_name', "'$new_supplier'"]
         ]);
         $_SESSION['material-message'] = array(
            "title" => 'Success',
            "body" => 'Supplier has been Added.',
            "type" => 'success'
         );
      } else {
         $_SESSION['material-message'] = array(
            "title" => 'Error',
            "body" => 'Something went wrong or Input may already added',
            "type" => 'error'
         );
      }
      header('Location: ../Inventory/raw-materials.php?page=view');
      exit();
   }

   /** ADD NEW MATERIAL */
   if (isset($_POST['add-new-material'])) {
      $supplier = filter_input(INPUT_POST, 'supplier', FILTER_SANITIZE_NUMBER_INT);
      $material = filter_input(INPUT_POST, 'material', FILTER_SANITIZE_SPECIAL_CHARS);
      $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_INT);
      $stocks = filter_input(INPUT_POST, 'stocks', FILTER_SANITIZE_NUMBER_INT);
      (empty($supplier)) ? $err++ : NULL;
      (empty($material)) ? $err++ : NULL;
      (empty($price)) ? $err++ : NULL;
      (empty($stocks)) ? $stocks = 0 : NULL;

      /** SANITIZE INPUT */
      $supplier = abs($supplier);
      $price = abs($price);
      $stocks = abs($stocks);

      /** VALIDATE INPUT */
      (!is_numeric($supplier)) ? $err++ : NULL;
      (!is_numeric($price)) ? $err++ : NULL;
      (!is_numeric($stocks)) ? $err++ : NULL;
      $has_supplier = $api->Read('supplier', 'set', 'supplier_id', $supplier);
      (empty($has_supplier) || !isset($has_supplier)) ? $err++ : NULL;
      if ($err == 0) {
         $api->Create('material_stock', [
            '1' => ['supplier_id', $supplier],
            '2' => ['material_name', "'$material'"],
            '3' => ['price', $price],
            '4' => ['stocks', $stocks],
            '5' => ['date', "'$date'"]
         ]);
         $_SESSION['material-message'] = array(
            "title" => 'Success',
            "body" => 'Material has been Added.',
            "type" => 'success'
         );
      } else {
         $_SESSION['material-message'] = array(
            "title" => 'Error',
            "body" => 'Something went wrong in your input',
            "type" => 'error'
         );
      }
      header('Location: ../Inventory/raw-materials.php?page=view');
      exit();
   }

   if (isset($_POST['select_supplier'])) {
      $select_supplier = $api->Read('material_stock', 'set', 'supplier_id', $_POST['select_supplier']);
      echo json_encode($select_supplier);
   }
}