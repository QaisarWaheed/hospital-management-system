<?php
/**
 * Session + DB for JSON/XHR endpoints only.
 * Do not include includes/connect.php here — it prints HTML <script> and breaks response.json().
 */
date_default_timezone_set('Asia/Karachi');
$current_date = date('Y-m-d H:i:s');
session_start();

function connect_ajax_fail($message)
{
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode(array('ok' => false, 'error' => $message));
    exit;
}

if (!isset($_SESSION['ph_id']) || (int) $_SESSION['ph_id'] < 1) {
    connect_ajax_fail('Not logged in. Open the app again from the login page.');
}

$user_id = $_SESSION['ph_id'];
$login_id = isset($_SESSION['login_id']) ? $_SESSION['login_id'] : '';
$login_expire_at = isset($_SESSION['login_expire_at']) ? $_SESSION['login_expire_at'] : '';
$user_name = isset($_SESSION['ph_name']) ? $_SESSION['ph_name'] : '';
$branch_id = isset($_SESSION['branch_id']) ? $_SESSION['branch_id'] : '';
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : '';
$is_incharge = isset($_SESSION['is_incharge']) ? $_SESSION['is_incharge'] : '';
$branch_name = isset($_SESSION['branch_name']) ? $_SESSION['branch_name'] : '';
$branch_address = isset($_SESSION['branch_address']) ? $_SESSION['branch_address'] : '';
$branch_phone = isset($_SESSION['branch_phone']) ? $_SESSION['branch_phone'] : '';

if ($login_expire_at !== '' && substr($current_date, 0, 10) !== substr($login_expire_at, 0, 10)) {
    connect_ajax_fail('Your login is not valid for today. Log out and log in again.');
}

@include_once __DIR__ . '/company_info.php';

$db_host = getenv('DB_HOST');
if ($db_host === false || $db_host === '') {
    $db_host = file_exists('/.dockerenv') ? 'srv-captain--mysql-db' : 'localhost';
}
if ($db_host === 'localhost' && PHP_OS_FAMILY !== 'Windows') {
    $db_host = '127.0.0.1';
}
$con = mysqli_connect($db_host, getenv('DB_USER') ?: 'ycdoeh1', getenv('DB_PASS') ?: 'ycdoeh1', getenv('DB_NAME') ?: 'ycdomlt');

if (!$con) {
    connect_ajax_fail('Database connection failed.');
}
