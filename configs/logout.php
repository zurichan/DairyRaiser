<?php
session_start();
require_once './database.php';
require_once '../includes/classes.php';
$api = new MyAPI($main_conn);

if (isset($_SESSION['users'])) {
    $ip_address = $api->IP_address();
    $remember_me = $api->Read('remember_me', 'set', 'ip_address', "$ip_address");
    if (!empty($remember_me)) {
        $api->Delete('remember_me', 'ip_address', "$ip_address");
    }
    
    unset($_SESSION['users']);
    unset($_SESSION['TIME']);
}

session_destroy();
header('Location: ../home.php');
