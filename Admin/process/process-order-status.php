<?php

session_start();
date_default_timezone_set('Asia/Manila');
require_once '../../configs/database.php';
require_once '../../includes/classes.php';

$api = new MyAPI($main_conn);

if (isset($_GET['process-order-status-id'])) {
   $order_id = filter_input(INPUT_GET, 'process-order-status-id', FILTER_SANITIZE_NUMBER_INT);

   $err = 0;

   (empty($order_id)) ? $err++ : NULL;

   $track_qry = "SELECT * FROM `order_track` WHERE `order_details_id` = $order_id ORDER BY `date` DESC";
   $details_qry = "SELECT * FROM `order_details` WHERE `order_details_id` = '$order_id' ORDER BY `date` DESC";
   $items_qry = "SELECT * FROM `order_items` WHERE `order_details_id` = $order_id ORDER BY `date` DESC";

   $track_stmt = $main_conn->prepare($track_qry);
   $detail_stmt = $main_conn->prepare($details_qry);
   $item_stmt = $main_conn->prepare($items_qry);

   $track_stmt->execute();
   $detail_stmt->execute();
   $item_stmt->execute();

   $order_track = $track_stmt->fetchAll();
   $order_detail = $detail_stmt->fetchAll();

   (empty($order_detail)) ? $err++ : NULL;
   (empty($order_track)) ? $err++ : NULL;

   if ($err == 0) {

      $status = '';
      switch ($order_track[0]->order_status) {
         case 'order placement':
            $status = 'preparing';
            break;
         case 'preparing':
            $status = 'on the way';
            break;
         case 'on the way':
            $status = 'delivered';
            break;
      }

      $api->Update('order_track', 'order_details_id', [
         '1' => ['order_status', "'$status'"]
      ], $order_id);

      $_SESSION['order-message'] = array(
         "title" => "order id : $order_id status updated",
         "body" => '',
         "type" => 'success'
      );
   } else {
      $_SESSION['order-message'] = array(
         "title" => "order id : $order_id status failed to update",
         "body" => 'Theres something went wrong',
         "type" => 'error'
      );
   }

   header('Location: ../Orders/order_list.php');
   exit();
}