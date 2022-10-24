<?php

session_start();
date_default_timezone_set('Asia/Manila');

$_POST['stock_percentage'] = true;

if (isset($_POST['stock_percentage'])) {

    require_once '../../configs/database.php';
    require_once '../../includes/classes.php';

    $api = new MyAPI($main_conn);

    $finished_goods = $api->Read('product_stocks', 'all');
    $total_finished_goods = $api->Sum('product_stocks', 'all', 'finished_goods');

    $value_storage = [];
    foreach ($finished_goods as $key => $goods) {
        $product_name = $api->Read('products', 'set', 'product_id', $goods->product_id);
        $value_storage['name']['ID: '.$product_name[0]->product_id] = number_format(($goods->finished_goods / $total_finished_goods->output) * 100, 2);
    }

    echo json_encode($value_storage);
}
