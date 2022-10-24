<?php

session_start();
date_default_timezone_set('Asia/Manila');
require_once '../configs/database.php';
require_once '../includes/classes.php';
$api = new MyAPI($main_conn);
$date = date('Y-m-d h:i:s');

if (isset($_POST['place-order']) && isset($_SESSION['users'])) {

   $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $user_all_address = $api->Read('user_address', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $user_cart = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
   $user_item = $api->Read('cart_item', 'set', 'session_id', $user_cart[0]->session_id);
   $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_SPECIAL_CHARS);
   // $methodArr = array('Cash on Delivery')

   $err = 0;
   (empty($user_all_address)) ? $err++ : NULL;
   (empty($user_cart)) ? $err++ : NULL;
   (empty($user_item)) ? $err++ : NULL;
   (empty($payment_method)) ? $err++ : NULL;

   if ($err == 0) {
      foreach ($user_all_address as $addresses) {
         if ($addresses->isDefault == 'yes') {
            $complete_address = $addresses->complete_address;
            $landmark = $addresses->landmark;
            break;
         }
      }

      echo $landmark;
      $full_name = $_SESSION['users'][0]->firstname . ' ' . $_SESSION['users'][0]->lastname;

      // $api->Create('order_details', [
      //    '1' => ['user_id', $user_info[0]->user_id],
      //    '2' => ['user_name', "'$full_name'"],
      //    '3' => ['user_address', "'$complete_address'"],
      //    '4' => ['user_landmark', "'$landmark'"],
      //    '5' => ['total', $user_cart[0]->total],
      //    '6' => ['modified_at', "'$date'"]
      // ]);
   }
}