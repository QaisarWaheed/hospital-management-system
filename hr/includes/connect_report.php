<?php
/**
 * HR session + DB with extended read timeout for heavy print reports.
 */
require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';
date_default_timezone_set('Asia/Karachi');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['hr_id'])) {
    ycdo_print_auth_failed_page();
}

$hr_id = (int) $_SESSION['hr_id'];
$hr_name = $_SESSION['hr_name'] ?? '';
$user_id = $hr_id;
$hr_branch_id = (int) ($_SESSION['branch_id'] ?? 0);
$hr_is_admin = (int) ($_SESSION['is_admin'] ?? 0);
$hr_is_incharge = (int) ($_SESSION['is_incharge'] ?? 0);
$hr_branch_name = $_SESSION['branch_name'] ?? '';
$hr_branch_address = $_SESSION['branch_address'] ?? '';
$hr_branch_phone = $_SESSION['branch_phone'] ?? '';

if ($hr_id < 1) {
    ycdo_print_auth_failed_page();
}

require_once __DIR__ . '/company_info.php';

$con = ycdo_db_connect_report();
if (!$con) {
    http_response_code(503);
    exit('Database connection failed.');
}
$GLOBALS['con'] = $con;
define('YCDO_HR_CONNECT_LOADED', true);
