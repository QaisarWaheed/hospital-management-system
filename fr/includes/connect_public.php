<?php
/**
 * DB + session for FR branch verification / login pages (no fr_id required).
 */
require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';
date_default_timezone_set('Asia/Karachi');
$current_date = date('Y-m-d H:i:s');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/company_info.php';
$con = ycdo_db_connect();
$GLOBALS['con'] = $con;
