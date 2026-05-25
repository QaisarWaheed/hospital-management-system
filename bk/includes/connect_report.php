<?php
/**
 * BK session + DB with extended read timeout for heavy print reports.
 */
require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';
date_default_timezone_set('Asia/Karachi');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!empty($_SESSION['bk_id'])) {
    $bk_id = (int) $_SESSION['bk_id'];
    $bk_name = $_SESSION['bk_name'] ?? '';
} elseif (!empty($_SESSION['hr_id'])) {
    $bk_id = (int) $_SESSION['hr_id'];
    $bk_name = $_SESSION['hr_name'] ?? '';
} elseif (!empty($_SESSION['dr_id'])) {
    $bk_id = (int) $_SESSION['dr_id'];
    $bk_name = $_SESSION['dr_name'] ?? '';
} elseif (!empty($_SESSION['fr_id'])) {
    $bk_id = (int) $_SESSION['fr_id'];
    $bk_name = $_SESSION['admin_name'] ?? ($_SESSION['fr_name'] ?? '');
} else {
    ycdo_print_auth_failed_page();
}

$bk_branch_id = (int) ($_SESSION['branch_id'] ?? 0);
$bk_is_admin = (int) ($_SESSION['is_admin'] ?? 0);
$bk_is_incharge = (int) ($_SESSION['is_incharge'] ?? 0);

if ($bk_id < 1) {
    ycdo_print_auth_failed_page();
}

require_once __DIR__ . '/company_info.php';

$con = ycdo_db_connect_report();
if (!$con) {
    http_response_code(503);
    exit('Database connection failed.');
}
$GLOBALS['con'] = $con;
$user_id = $bk_id;
$branch_id = $bk_branch_id;
$branch_name = $_SESSION['branch_name'] ?? '';
