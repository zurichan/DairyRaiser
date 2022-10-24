<?php
session_start();
// INCLUDES DATABASE
require_once('../../configs/database.php');

require_once('../../libs/get_stocks.php');

require_once('../../libs/all_products.php');

//UPDATE A STOCK

if(isset($_SESSION['admins']) && isset($_POST['update-stock'])) {

    $update_rack = filter_input(INPUT_POST, 'update_rack', FILTER_SANITIZE_SPECIAL_CHARS);

    $update_finished_goods = filter_input(INPUT_POST, 'update_finished_goods', FILTER_SANITIZE_NUMBER_INT);

    $product_id = filter_input(INPUT_POST, 'product_id_stocks', FILTER_SANITIZE_NUMBER_INT);

    date_default_timezone_set('Asia/Manila');

    $date = date('Y-m-d h:i:s');
    
    $stock_error = 0;

    $rack = array('Rack-A', 'Rack-B', 'Rack-C', 'Rack-D');

    (in_array($update_rack, $rack)) ? null : $stock_error++;

    echo $update_rack;
    
    empty($update_rack) ? $stock_error++ : null;

    ($update_finished_goods != '') ? null : $stock_error++;

    empty($product_id) ? $stock_error++ : null;

    echo $stock_error;

    if($stock_error === 0) {

        $update_stock_sql = 
        "UPDATE `product_stocks` SET `location_rack` = :update_rack, `finished_goods` = :update_goods
        WHERE `product_id` = :product_id";

        $update_material_stmt = $main_conn->prepare($update_stock_sql);

        $update_material_stmt->execute([
            'update_rack' => $update_rack,
            'update_goods' => $update_finished_goods,
            'product_id' => $product_id
        ]);

        $_SESSION['stock-message'] = array(
            "title" => 'Success!',
            "body" => 'Product: '.$product_id.' has been Updated.',
            "type" => 'success'
        );

        header('Location: ../Inventory/stocks.php');

    } else {

        $_SESSION['stock-message'] = array(
            "title" => 'Invalid!',
            "body" => 'Invalid Stock Input. Please Try Again',
            "type" => 'error'
        );

        header('Location: ../Inventory/stocks.php');
    }
}

//REMOVE A EXPIRED PRODUCTS

if (isset($_POST['raw_material']) && isset($_SESSION['admins'])) {

    $remove_material_sql = "DELETE FROM `raw_materials` WHERE materials = :raw_material";

    $remove_material_stmt = $main_conn->prepare($remove_material_sql);

    $remove_material_stmt->execute([
        'raw_material' => $_POST['raw_material']
    ]);
    
}
?>