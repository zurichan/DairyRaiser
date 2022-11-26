<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../../includes/classes.php';

$api = new MyAPI($main_conn);
$date = date('Y-m-d h:i:s');

// ADDING A PRODUCT
if (isset($_POST['add-product']) && isset($_SESSION['admins'])) {

    $product_code = filter_input(INPUT_POST, 'product-code', FILTER_SANITIZE_SPECIAL_CHARS);
    $product_name = filter_input(INPUT_POST, 'product-name', FILTER_SANITIZE_SPECIAL_CHARS);
    $description = filter_input(INPUT_POST, 'product-description', FILTER_SANITIZE_SPECIAL_CHARS);
    $price = filter_input(INPUT_POST, 'product-price', FILTER_SANITIZE_SPECIAL_CHARS);
    $stock_avail = filter_input(INPUT_POST, 'product-stock-avail', FILTER_VALIDATE_INT);
    $holding_stock = filter_input(INPUT_POST, 'product-holding-stock', FILTER_VALIDATE_INT);

    $product_code = strtoupper($product_code);
    $product_code = str_replace(" ", "", $product_code);

    $all_product = $api->Read('products', 'all');
    $error_count = 0;

    (empty($product_name)) ? $error_count++ : NULL;
    (empty($description)) ? $error_count++ : NULL;
    (empty($price)) ? $error_count++ : NULL;
    (empty($stock_avail)) ? $error_count++ : NULL;
    (empty($holding_stock)) ? $error_count++ : NULL;
    (empty($product_code)) ? $error_count++ : NULL;
    ($price < 0) ? $error_count++ : NULL;
    ($stock_avail < 0) ? $error_count++ : NULL;
    ($holding_stock < 0) ? $error_count++ : NULL;

    $code_len = strlen($product_code);
    ($code_len != 6) ? $error_count++ : NULL;

    (!isset($_FILES['product-image'])) ? $error_count++ : NULL;

    $ext = array('jpg', 'jpeg', 'png');
    $file_name = $_FILES['product-image']['name'];
    $file_size = $_FILES['product-image']['size'];
    $file_tmp = $_FILES['product-image']['tmp_name'];
    $target_dir = "../../img/{$file_name}";
    $file_ext = explode('.', $file_name);
    $file_ext = strtolower(end($file_ext));

    (in_array($file_ext, $ext)) ? NULL :  $error_count++;
    (!($file_size <= 1000000)) ? $error_count++ : NULL;
    if ($error_count == 0) {

        $is_duplicate = false;
        $product_code = 'DR_' . $product_code;
        foreach ($all_product as $product) {
            if ($product->productcode == $product_code) {
                $is_duplicate = true;
                break;
            }
        }

        if ($is_duplicate == false) {
            move_uploaded_file($file_tmp, $target_dir);
            $api->Create('products', [
                'key1' => ['img_url', "'$target_dir'"],
                'key2' => ['productname', "'$product_name'"],
                'key3' => ['description', "'$description'"],
                'key4' => ['price', $price],
                'key5' => ['update', "'$date'"],
                'key6' => ['productcode', "'$product_code'"]
            ]);

            $_SESSION['product-message'] = array(
                "title" => 'Success',
                "body" => 'Product has been Added.',
                "type" => 'success'
            );
        } else {
            $_SESSION['product-message'] = array(
                "title" => 'Product Code is Duplicated',
                "body" => 'Please Try Again.',
                "type" => 'error'
            );
        }
    } else {
        $_SESSION['product-message'] = array(
            "title" => 'Invalid!',
            "body" => 'Invalid Product Input. Please Try Again.',
            "type" => 'error'
        );
    }
    header('Location: ../Inventory/product_list.php');
}

//REMOVING A PRODUCT
if (isset($_POST['remove_product']) && isset($_SESSION['admins'])) {

    $fetch_product = $api->Read('products', 'set', 'product_id', $_POST['remove_product']);
    if (!empty($fetch_product)) {

        $api->Delete('products', 'product_id', $_POST['remove_product']);
        $_SESSION['product-message'] = array(
            "title" => 'Product has been removed.',
            "body" => '',
            "type" => 'success'
        );
    } else {
        $_SESSION['product-message'] = array(
            "title" => 'Something Went Wrong',
            "body" => '',
            "type" => 'error'
        );
    }
}

/** UPDATING PRODUCT DESCRIPTION */
if (isset($_POST['post-update-product-description']) && isset($_SESSION['admins'])) {

    $product_id = filter_input(INPUT_POST, 'update-product-id', FILTER_SANITIZE_NUMBER_INT);
    $product_name = filter_input(INPUT_POST, 'update-product-name', FILTER_SANITIZE_SPECIAL_CHARS);
    $product_description = filter_input(INPUT_POST, 'update-product-description', FILTER_SANITIZE_SPECIAL_CHARS);
    $product_price = filter_input(INPUT_POST, 'update-product-price', FILTER_SANITIZE_NUMBER_INT);

    str_replace('+', '', $product_id);
    str_replace('-', '', $product_id);

    $err = 0;

    (empty($product_id)) ? $err++ : NULL;
    (empty($product_name)) ? $err++ : NULL;
    (empty($product_description)) ? $err++ : NULL;
    (empty($product_price)) ? $err++ : NULL;

    $fetch_product = $api->Read('products', 'set', 'product_id', $product_id);

    if (!empty($fetch_product) && $err == 0) {

        str_replace('+', '', $product_price);
        str_replace('-', '', $product_price);

        $sec_err = 3;

        foreach ($fetch_product as $product) {
            (str_replace(' ', '', $product->productname) == str_replace(' ', '', $product_name)) ? $sec_err++ : $sec_err--;
            (str_replace(' ', '', $product->description) == str_replace(' ', '', $product_description)) ? $sec_err++ : $sec_err--;;
            ($product->price == $product_price) ? $sec_err++ : $sec_err--;
        }

        if ($sec_err < 6) {

            $clean_product_name = $api->filter_string_polyfill($product_name);
            $clean_product_description = $api->filter_string_polyfill($product_description);

            $api->Update('products', 'product_id', [
                '1' => ['productname', "'$clean_product_name'"],
                '2' => ['description', "'$clean_product_description'"],
                '3' => ['price', $product_price],
                '4' => ['update', "'$date'"]
            ], $product_id);

            $_SESSION['product-message'] = array(
                "title" => 'Success',
                "body" => 'Product has been Updated.',
                "type" => 'success'
            );
        } else {

            $_SESSION['product-message'] = array(
                "title" => 'Error',
                "body" => 'Already Same as Before.',
                "type" => 'error'
            );
        }
    } else {
        $_SESSION['product-message'] = array(
            "title" => 'Error',
            "body" => 'Something Went Wrong or Invalid Input. Please try Again.',
            "type" => 'error'
        );
    }

    header('Location: ../Inventory/product_list.php');
}

/** UPDATING PRODUCT STOCKS */
if (isset($_POST['post-update-product-stock']) && isset($_SESSION['admins'])) {

    $product_id = filter_input(INPUT_POST, 'update-product-id', FILTER_SANITIZE_NUMBER_INT);
    $stock_available = filter_input(INPUT_POST, 'update-product-stock-avail', FILTER_SANITIZE_NUMBER_INT);
    $holding_stock = filter_input(INPUT_POST, 'update-product-holding-stock', FILTER_SANITIZE_NUMBER_INT);

    str_replace('+', '', $product_id);
    str_replace('-', '', $product_id);

    $err = 0;

    (empty($product_id)) ? $err++ : NULL;
    (empty($stock_available)) ? 0 : NULL;
    (empty($holding_stock)) ? 0 : NULL;

    $fetch_product = $api->Read('products', 'set', 'product_id', $product_id);

    if ($fetch_product && $err == 0) {

        str_replace('+', '', $stock_available);
        str_replace('-', '', $stock_available);
        str_replace('+', '', $holding_stock);
        str_replace('-', '', $holding_stock);

        $sec_err = 2;

        foreach ($fetch_product as $product) {
            ($product->stock_avail == $stock_available) ? $sec_err++ : $sec_err--;
            ($product->holding_stock == $holding_stock) ? $sec_err++ : $sec_err--;;
        }

        if ($sec_err < 4) {

            $api->Update('products', 'product_id', [
                '1' => ['stock_avail', $stock_available],
                '2' => ['holding_stock', $holding_stock],
                '3' => ['update', "'$date'"]
            ], $product_id);

            $_SESSION['product-message'] = array(
                "title" => 'Success',
                "body" => 'Product has been Updated.',
                "type" => 'success'
            );
        } else {
            $_SESSION['product-message'] = array(
                "title" => 'Error',
                "body" => 'Already Same as Before.',
                "type" => 'error'
            );
        }
    } else {
        $_SESSION['product-message'] = array(
            "title" => 'Error',
            "body" => 'Something Went Wrong or Invalid Input. Please try Again.',
            "type" => 'error'
        );
    }
    header('Location: ../Inventory/product_list.php');
}