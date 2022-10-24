<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../includes/classes.php';
require_once('../configs/database.php');

// CONVERT AJAX RESPONSE INTO JSON OBJECT
$json_obj = array();

$api = new MyAPI($main_conn);

if (isset($_SESSION['users']) && isset($_POST['quantities']) && isset($_POST['product_name'])) {
    if ($_POST['quantities'] != 0) {

        $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
        $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
        $products = $api->Read('products', 'set', 'productname', $_POST['product_name']);

        $selected_item_session = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id);
        foreach ($selected_item_session as $item) {
            if ($item->product_id === $products[0]->product_id && $item->session_id === $user_shopping_session[0]->session_id) {
                $selected_item_product = $api->Read('cart_item', 'set', 'cart_id', $item->cart_id);
                break;
            }
        }

        $api->Update('cart_item', 'cart_id', [
            '1' => ['quantity', $_POST['quantities']],
            '2' => ['total_unitPrice',  $products[0]->price * $_POST['quantities']]
        ], $selected_item_product[0]->cart_id);

        $total_price = $api->Sum('cart_item', 'set', 'total_unitPrice', 'session_id', $user_shopping_session[0]->session_id);
        $total_quantity = $api->Sum('cart_item', 'set', 'quantity', 'session_id', $user_shopping_session[0]->session_id);

        $api->Update('shopping_session', 'user_id', [
            '1' => ['total', $total_price->output]
        ], $user_info[0]->user_id);

        $shopping_cart = $api->Read('shopping_session', 'set', 'user_id', $user_info[0]->user_id);

        $json_obj['subtotal'] = '₱' . $products[0]->price * $_POST['quantities'] . '.00';
        $json_obj['ordertotal'] = '₱' . $shopping_cart[0]->total . '.00';

        $json = json_encode($json_obj);
        echo $json;
    }
}
if (isset($_POST['remove_item']) && isset($_SESSION['users'])) {

    $user_info = $api->Read('user', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $user_shopping_session = $api->Read('shopping_session', 'set', 'user_id', $_SESSION['users'][0]->user_id);
    $products = $api->Read('products', 'set', 'productname', $_POST['remove_item']);

    $selected_item_session = $api->Read('cart_item', 'set', 'session_id', $user_shopping_session[0]->session_id);
    foreach ($selected_item_session as $item) {
        if ($item->product_id == $products[0]->product_id && $item->session_id == $user_shopping_session[0]->session_id) {
            $selected_item_product = $api->Read('cart_item', 'set', 'cart_id', $item->cart_id);
            break;
        }
    }

    $api->Delete('cart_item', 'cart_id', $selected_item_product[0]->cart_id);
    $total_price = $api->Sum('cart_item', 'total_unitPrice', 'session_id', $user_shopping_session[0]->session_id);

    $api->Update('shopping_session', 'user_id', [
        '1' => ['total', $total_price->output]
    ], $user_info[0]->user_id);
}
