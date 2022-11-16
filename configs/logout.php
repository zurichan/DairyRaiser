<?php
session_start();
require_once './database.php';
require_once '../includes/classes.php';
$api = new MyAPI($main_conn);

if (isset($_SESSION['users'])) {
    $ip_address = $api->IP_address();
    $sql = "SELECT * FROM `remember_me` WHERE `ip_address` = '$ip_address' LIMIT 1";
    $stmt = $main_conn->query($sql);
    $stmt->execute();
    $remember_me = $stmt->fetchAll();
    if (!empty($remember_me)) {
        $api->Delete('remember_me', 'ip_address', "$ip_address");
    }

    unset($_SESSION['users']);
    unset($_SESSION['TIME']);
}

session_destroy();
header('Location: ../home.php');