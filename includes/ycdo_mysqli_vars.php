<?php
if (defined('YCDO_MYSQLI_VARS_LOADED')) {
    return;
}
define('YCDO_MYSQLI_VARS_LOADED', true);

$ycdo_db_host = getenv('DB_HOST');
if ($ycdo_db_host === false || $ycdo_db_host === '') {
    $ycdo_db_host = file_exists('/.dockerenv') ? 'srv-captain--mysql-db' : 'localhost';
}
if ($ycdo_db_host === 'localhost' && PHP_OS_FAMILY !== 'Windows') {
    $ycdo_db_host = '127.0.0.1';
}

if (file_exists('/.dockerenv')) {
    $ycdo_db_user = getenv('DB_USER') ?: 'root';
    $ycdo_db_pass = getenv('DB_PASS') ?: 'AppPass123';
    $ycdo_db_name = getenv('DB_NAME') ?: 'ycdomlt';
} else {
    $ycdo_db_user = getenv('DB_USER') ?: 'ycdoeh1';
    $ycdo_db_pass = getenv('DB_PASS') ?: 'ycdoeh1';
    $ycdo_db_name = getenv('DB_NAME') ?: 'ycdomlt';
}

$GLOBALS['ycdo_db_host'] = $ycdo_db_host;
$GLOBALS['ycdo_db_user'] = $ycdo_db_user;
$GLOBALS['ycdo_db_pass'] = $ycdo_db_pass;
$GLOBALS['ycdo_db_name'] = $ycdo_db_name;
