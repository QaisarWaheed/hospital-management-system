<?php
if (defined('YCDO_DB_CONFIG_LOADED')) {
    return;
}
define('YCDO_DB_CONFIG_LOADED', true);

/**
 * DB settings. In Docker/CapRover, MySQL is not on "localhost" — use the internal service host.
 * Override with DB_HOST, DB_NAME, DB_USER, DB_PASS on the app (CapRover → App Configs → Environmental Variables).
 */
$DB_HOST = getenv('DB_HOST');
if ($DB_HOST === false || $DB_HOST === '') {
    $DB_HOST = file_exists('/.dockerenv') ? 'srv-captain--mysql-db' : 'localhost';
}

$DB_NAME = getenv('DB_NAME') ?: 'ycdomlt';
if (file_exists('/.dockerenv')) {
    $DB_USER = getenv('DB_USER') ?: 'root';
    $DB_PASS = getenv('DB_PASS') ?: 'AppPass123';
} else {
    $DB_USER = getenv('DB_USER') ?: 'ycdoeh1';
    $DB_PASS = getenv('DB_PASS') ?: 'ycdoeh1';
}

// On Linux, mysqli "localhost" uses a Unix socket; use TCP to the same machine instead.
if ($DB_HOST === 'localhost' && PHP_OS_FAMILY !== 'Windows') {
    $DB_HOST = '127.0.0.1';
}

$con = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($con) {
    mysqli_set_charset($con, 'utf8mb4');
}
