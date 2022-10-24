<?php

$DB_HOST = 'localhost';
$DB_USER = 'Christian';
$DB_PASS = '042901cjay';
$DB_MAIN = 'dairy raisers db';

try {
    $main_conn = new PDO("mysql:host=$DB_HOST;dbname=$DB_MAIN", $DB_USER, $DB_PASS);
    $main_conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $ex) {
    echo 'Something went wrong...';
}

$con = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_MAIN) or die();


