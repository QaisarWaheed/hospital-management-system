<?php
/**
 * Load first on every request (via includes/connect.php or *_login.php).
 * Set CapRover env YCDO_DEBUG=1 to show PHP errors on screen (disable after fixing).
 */
if (defined('YCDO_BOOTSTRAP_LOADED')) {
    return;
}
define('YCDO_BOOTSTRAP_LOADED', true);

if (getenv('YCDO_DEBUG') === '1' || getenv('APP_DEBUG') === '1') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

mysqli_report(MYSQLI_REPORT_OFF);

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

/**
 * @return mysqli|false
 */
function ycdo_db_connect()
{
    require_once __DIR__ . '/ycdo_mysqli_vars.php';
    global $ycdo_db_host, $ycdo_db_user, $ycdo_db_pass, $ycdo_db_name;

    if (empty($ycdo_db_host) || empty($ycdo_db_name)) {
        $ycdo_db_host = $GLOBALS['ycdo_db_host'] ?? null;
        $ycdo_db_user = $GLOBALS['ycdo_db_user'] ?? null;
        $ycdo_db_pass = $GLOBALS['ycdo_db_pass'] ?? null;
        $ycdo_db_name = $GLOBALS['ycdo_db_name'] ?? null;
    }

    $con = mysqli_connect($ycdo_db_host, $ycdo_db_user, $ycdo_db_pass, $ycdo_db_name);
    if (!$con) {
        if (getenv('YCDO_DEBUG') === '1') {
            die('Database connection failed: ' . mysqli_connect_error());
        }
        http_response_code(503);
        exit('Database connection failed.');
    }
    mysqli_set_charset($con, 'utf8mb4');
    return $con;
}

/**
 * Longer read timeout for heavy report pages (still subject to nginx proxy limit).
 *
 * @return mysqli|false
 */
function ycdo_db_connect_report()
{
    require_once __DIR__ . '/ycdo_mysqli_vars.php';
    global $ycdo_db_host, $ycdo_db_user, $ycdo_db_pass, $ycdo_db_name;

    if (empty($ycdo_db_host) || empty($ycdo_db_name)) {
        $ycdo_db_host = $GLOBALS['ycdo_db_host'] ?? null;
        $ycdo_db_user = $GLOBALS['ycdo_db_user'] ?? null;
        $ycdo_db_pass = $GLOBALS['ycdo_db_pass'] ?? null;
        $ycdo_db_name = $GLOBALS['ycdo_db_name'] ?? null;
    }

    $mysqli = mysqli_init();
    if (!$mysqli) {
        return false;
    }
    mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 15);
    if (defined('MYSQLI_OPT_READ_TIMEOUT')) {
        mysqli_options($mysqli, MYSQLI_OPT_READ_TIMEOUT, 300);
    }
    if (!mysqli_real_connect($mysqli, $ycdo_db_host, $ycdo_db_user, $ycdo_db_pass, $ycdo_db_name)) {
        return false;
    }
    mysqli_set_charset($mysqli, 'utf8mb4');
    return $mysqli;
}

/**
 * @return array{start: string, end: string}
 */
function ycdo_sql_day_range($date)
{
    $date = substr((string) $date, 0, 10);
    $start = $date . ' 00:00:00';
    $end = date('Y-m-d H:i:s', strtotime($date . ' +1 day'));

    return array('start' => $start, 'end' => $end);
}

/**
 * Value safe for HTML &lt;input type="date"&gt; (Y-m-d).
 */
function ycdo_date_input_value($value, $fallback = null)
{
    if ($fallback === null) {
        $fallback = date('Y-m-d');
    }
    if ($value === null || $value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
        return $fallback;
    }
    if (preg_match('/^(\d{4}-\d{2}-\d{2})/', (string) $value, $m)) {
        return $m[1];
    }

    return $fallback;
}

function ycdo_safe_date_format($value, $format = 'd-M-Y', $default = '')
{
    if ($value === null || $value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
        return $default;
    }
    $dt = date_create((string) $value);
    if (!$dt && preg_match('/^\d{4}-\d{2}-\d{2}/', (string) $value, $m)) {
        $dt = date_create($m[0]);
    }
    return $dt ? $dt->format($format) : $default;
}

/**
 * Weeks between EDD/LMP date (gynae_register.weeks column) and today — for row highlighting.
 */
function ycdo_gynae_weeks_offset($weeksValue)
{
    $start = ycdo_safe_date_format($weeksValue, 'd/m/Y H:i:s', '');
    if ($start === '') {
        return 0;
    }
    $datefrom = DateTime::createFromFormat('d/m/Y H:i:s', $start);
    $dateto = DateTime::createFromFormat('d/m/Y H:i:s', date('d/m/Y H:i:s'));
    if (!$datefrom || !$dateto) {
        return 0;
    }
    $interval = $dateto->diff($datefrom);
    return (int) floor($interval->format('%R%a') / 7);
}

function ycdo_gynae_row_style($weeksValue)
{
    $weeks = ycdo_gynae_weeks_offset($weeksValue);
    if ($weeks < 2 && $weeks > -2) {
        return 'bg-info text-light';
    }
    if ($weeks <= -2) {
        return 'bg-danger text-light';
    }
    return '';
}
