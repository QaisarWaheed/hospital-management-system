<?php
/**
 * FR session + DB with extended read timeout for heavy print reports.
 */
require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';
date_default_timezone_set('Asia/Karachi');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['fr_id'])) {
    header('Location: logout.php');
    exit;
}

$fr_id = (int) $_SESSION['fr_id'];
$user_id = $fr_id;
$user_name = $_SESSION['fr_name'] ?? '';
$branch_id = (int) ($_SESSION['branch_id'] ?? 0);
$is_admin = (int) ($_SESSION['is_admin'] ?? 0);
$is_incharge = (int) ($_SESSION['is_incharge'] ?? 0);
$role_id = (int) ($_SESSION['role_id'] ?? 0);
$branch_name = $_SESSION['branch_name'] ?? '';
$branch_address = $_SESSION['branch_address'] ?? '';
$branch_phone = $_SESSION['branch_phone'] ?? '';

if ($fr_id < 1) {
    header('Location: logout.php');
    exit;
}

require_once __DIR__ . '/company_info.php';

$con = ycdo_db_connect_report();
if (!$con) {
    http_response_code(503);
    exit('Database connection failed.');
}
$GLOBALS['con'] = $con;
$GLOBALS['branch_id'] = $branch_id;
$GLOBALS['user_id'] = $user_id;
define('YCDO_FR_CONNECT_LOADED', true);
