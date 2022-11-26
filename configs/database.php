<?php

// $DB_HOST = 'ftp://191.101.230.51';
// $DB_USER = 'u699971744';
// $DB_PASS = '042901Cjay@';
// $DB_MAIN = 'u699971744_dairy_raisers';
define('DB_HOST', 'localhost');
define('DB_USER', 'Christian');
define('DB_PASS', '042901cjay');
define('DB_NAME', 'dairy raisers db');

try {
    $main_conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
    $main_conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $ex) {
    exit("Error: " . $ex->getMessage());
}