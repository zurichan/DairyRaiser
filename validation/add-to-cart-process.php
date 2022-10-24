<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../configs/database.php';
require_once '../includes/classes.php';

$api = new MyAPI($main_conn);


if (isset($_SESSION['users'])) {

    if (isset($_SESSION['productname']) && isset($_POST['quantity']) && isset($_SESSION['users'])) {
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
        str_replace('+', '', $quantity);
        $product_name = filter_input(INPUT_POST, 'productname', FILTER_SANITIZE_SPECIAL_CHARS);
        if (($quantity > 0)) {  

            $products = $api->Read('products', 'set', 'productname',  $_SESSION['productname']);
            $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
            $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);

            $user_items = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id);

            $same_item = false;
            foreach ($user_items as $item) {
                if ($item->product_id == $products[0]->product_id) {
                    $same_item = true;
                    $prevQuantity = $item->quantity;
                    $cart_id = $item->cart_id;
                    break;
                }
            }

            if ($same_item == true) {
                $newQuantity = $prevQuantity + $quantity;
                $api->Update('cart_item', 'cart_id', [
                    '1' => ['quantity', $newQuantity],
                    '2' => ['total_unitPrice', $products[0]->price * $newQuantity]
                ], $cart_id);
            } else if ($same_item == false) {
                $api->Create('cart_item', [
                    '1' => ['product_id', $products[0]->product_id],
                    '2' => ['session_id', $user_shopping_session[0]->session_id],
                    '3' => ['quantity',  $quantity],
                    '4' => ['total_unitPrice', $products[0]->price * $quantity]
                ]);
            }

            $total_price = $api->Sum('cart_item', 'set', 'total_unitPrice', 'session_id', $user_shopping_session[0]->session_id);
            $api->Update('shopping_session', 'user_id', [
                '1' => ['total', $total_price->output]
            ], $user_info[0]->user_id);

            $_SESSION['products-message'] = array(
                "title" => 'Added to Cart Successfully !',
                "body" => 'Item: ' . $_SESSION['productname'] . ' has been added to your cart',
                "type" => 'success'
            );

            header('Location: ../shop/products.php?page=all');
        } else {
            $_SESSION['products-message'] = array(
                "title" => 'Invalid Quantity!',
                "body" => 'Must be greater than 0',
                "type" => 'error'
            );

            header('Location: '.$_SERVER['HTTP_REFERER']);
        }
    } else {
        header('Location: ../shop/products.php?page=all');
    }
} else {
    $_SESSION['products-message'] = array(
        "title" => 'Sign Up Now!',
        "body" => 'Please Sign up or Log In First to purchase product.',
        "type" => 'error'
    );

    header('Location: '.$_SERVER['HTTP_REFERER']);
}
