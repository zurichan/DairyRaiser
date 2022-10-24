<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../../configs/database.php';
require_once '../../includes/classes.php';

$api = new MyAPI($main_conn);
$date = date('Y-m-d h:i:s');
// ADD A MATERIALS

if(isset($_SESSION['admins']) && isset($_POST['add-material'])) {
    
    $supplier = filter_input(INPUT_POST, 'supplier', FILTER_SANITIZE_SPECIAL_CHARS);
    $material = filter_input(INPUT_POST, 'material', FILTER_SANITIZE_SPECIAL_CHARS);
    $price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_SPECIAL_CHARS);
    $stocks = filter_input(INPUT_POST, 'material_stocks', FILTER_SANITIZE_SPECIAL_CHARS);
    
    $material_error = 0;
    
    empty($supplier) ? $material_error++ : null;
    empty($material) ? $material_error++ : null;
    empty($price) ? $material_error++ : null;
    empty($stocks) ? $material_error++ : null;
    
    if($material_error === 0) {

        $api->Create('raw_materials', [
            'key1' => ['supplier', "'$supplier'"],
            'key2' => ['materials', "'$material'"],
            'key3' => ['price', $price],
            'key4' => ['stocks', $stocks],
            'key5' => ['lastUpdate', "'$date'"]
        ]);

        $_SESSION['material-message'] = array(
            "title" => 'Success!',
            "body" => 'Material has been Added.',
            "type" => 'success'
        );

        header('Location: ../Inventory/raw-materials.php');
    } else {

        $_SESSION['material-message'] = array(
            "title" => 'Invalid!',
            "body" => 'Invalid Material Input. Please Try Again',
            "type" => 'error'
        );

        header('Location: ../Inventory/raw-materials.php');
    }
}

//UPDATE A MATERIAL

if(isset($_SESSION['admins']) && isset($_POST['update-material'])) {

    $material_id = filter_input(INPUT_POST, 'material_id', FILTER_SANITIZE_NUMBER_INT);
    $update_supplier = filter_input(INPUT_POST, 'update_supplier', FILTER_SANITIZE_SPECIAL_CHARS);
    $update_material = filter_input(INPUT_POST, 'update_material', FILTER_SANITIZE_SPECIAL_CHARS);
    $update_price = filter_input(INPUT_POST, 'update_price', FILTER_SANITIZE_NUMBER_INT);
    $update_stocks = filter_input(INPUT_POST, 'update_material_stocks', FILTER_SANITIZE_NUMBER_INT);
    
    $material_error = 0;
    
    empty($update_supplier) ? $material_error++ : null;
    empty($update_material) ? $material_error++ : null;
    empty($update_price) ? $material_error++ : null;
    empty($update_stocks) ? $material_error++ : null;

    if($material_error === 0) {

        $api->Update('raw_materials', 'materials_id', [
            'key1' => ['supplier', "'$update_supplier'"],
            'key2' => ['materials', "'$update_material'"],
            'key3' => ['price', $update_price],
            'key4' => ['stocks', $update_stocks],
            'key5' => ['lastUpdate', "'$date'"]
        ], $material_id);

        $_SESSION['material-message'] = array(
            "title" => 'Success!',
            "body" => 'Material: '.$material_id.' has been Updated.',
            "type" => 'success'
        );

        header('Location: ../Inventory/raw-materials.php');

    } else {

        $_SESSION['material-message'] = array(
            "title" => 'Invalid!',
            "body" => 'Invalid Material Input. Please Try Again',
            "type" => 'error'
        );

        header('Location: ../Inventory/raw-materials.php');
    }
}

//REMOVE A MATERIAL

if (isset($_POST['raw_material']) && isset($_SESSION['admins'])) {

    $api->Delete('raw_materials', 'materials', $_POST['raw_material']);
    
}
?>