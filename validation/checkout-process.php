<?php

session_start();
date_default_timezone_set('Asia/Manila');
require_once '../configs/database.php';
require_once '../includes/classes.php';
$api = new MyAPI($main_conn);
$date = date('Y-m-d h:i:s');

if (isset($_POST['place-order']) && isset($_SESSION['users'])) {

   $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $session_id = $api->Read('shopping_session', 'set', 'user_id', $user_info[0]->user_id);
   $user_all_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $user_cart = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $user_item = $api->Read('cart_item', 'set', 'session_id', $user_cart[0]->session_id);
   $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_SPECIAL_CHARS);
   $methodArr = array('COD');

   $err = 0;
   (empty($user_all_address)) ? $err++ : NULL;
   (empty($user_cart)) ? $err++ : NULL;
   (empty($user_item)) ? $err++ : NULL;
   (empty($payment_method)) ? $err++ : NULL;
   (!in_array($payment_method, $methodArr)) ? $err++ : NULL;

   if ($err == 0) {
      foreach ($user_all_address as $addresses) {
         if ($addresses->isDefault == 'yes') {
            $complete_address = $addresses->complete_address;
            $landmark = $addresses->landmark;
            break;
         }
      }
      $full_name = $_SESSION['users'][0]->firstname . ' ' . $_SESSION['users'][0]->lastname;

      function checkKeys($api, $randStr)
      {
         $order_details = $api->Read('order_details', 'all');
         foreach ($order_details as $order) {
            if ($order->order_details_id == $randStr) {
               $keyExists = true;
               break;
            } else {
               $keyExists = false;
            }
         }
         return $keyExists;
      }

      function generateKeys($api)
      {
         $keyLength = 4;
         $str = "1234567890";
         $randStr = substr(str_shuffle($str), 0, $keyLength);
         $checkKey = checkKeys($api, $randStr);

         while ($checkKey == true) {
            $randStr = substr(str_shuffle($str), 0, $keyLength);
            $checkKey = checkKeys($api, $randStr);
         }

         return $randStr;
      }

      $invoice_id = $user_info[0]->user_id . '-' . generateKeys($api);

      $order_id = generateKeys($api);

      $api->Create('order_details', [
         '1' => ['user_id', $user_info[0]->user_id],
         '2' => ['user_name', "'$full_name'"],
         '3' => ['user_address', "'$complete_address'"],
         '4' => ['user_landmark', "'$landmark'"],
         '5' => ['total', $user_cart[0]->total],
         '6' => ['date', "'$date'"],
         '7' => ['payment_method', "'$payment_method'"],
         '8' => ['order_details_id', $order_id],
         '9' => ['invoice_id', "'$invoice_id'"],
         '10' => ['payment_status', 0]
      ]);

      $api->Create('order_track', [
         '1' => ['order_details_id', $order_id],
         '2' => ['order_status', "'order placement'"],
         '3' => ['date', "'$date'"],
         '4' => ['total', $user_cart[0]->total]
      ]);

      foreach ($user_item as $item) {
         $productItem = $api->Read('products', 'set', 'product_id', $item->product_id);
         $api->Create('order_items', [
            '1' => ['order_details_id', $order_id],
            '2' => ['user_id', $user_info[0]->user_id],
            '3' => ['product_id', $item->product_id],
            '4' => ['quantity', $item->quantity],
            '5' => ['date', "'$date'"],
            '6' => ['total', $item->total_unitPrice]
         ]);
      };

      $api->Update('shopping_session', 'user_id', [
         '1' => ['total', 0]
      ], $user_info[0]->user_id);

      $api->Delete('cart_item', 'session_id', $session_id[0]->session_id);

      $_SESSION['index-message'] = array(
         "title" => 'Order Placed',
         "body" => 'You can track your order at Order Track Page',
         "type" => 'success'
      );
   } else {
      $_SESSION['index-message'] = array(
         "title" => 'Something Went Wrong in Ordering Placement',
         "body" => '',
         "type" => 'error'
      );
   }

   header('Location: ../../home.php');
   exit();
}