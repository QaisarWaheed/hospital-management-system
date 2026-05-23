<?php
require_once __DIR__ . '/../includes/ycdo_bootstrap.php';
$conn = ycdo_db_connect();
$GLOBALS['con'] = $conn;
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d G:i:s A');
error_reporting(1);
require_once __DIR__ . '/../includes/db_connect.php';
