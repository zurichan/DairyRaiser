<?php

session_start();
date_default_timezone_set('Asia/Manila');

require_once '../configs/database.php';
require_once '../includes/classes.php';

$api = new MyAPI($main_conn);

if(isset($_POST['intro_section_filter'])) {
    
}

?>
