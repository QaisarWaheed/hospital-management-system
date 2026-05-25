<?php
/**
 * Load first on every request (via includes/connect.php or *_login.php).
 * Set CapRover env YCDO_DEBUG=1 to show PHP errors on screen (disable after fixing).
 */
if (defined('YCDO_BOOTSTRAP_LOADED')) {
    return;
}
define('YCDO_BOOTSTRAP_LOADED', true);

ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

if (getenv('YCDO_DEBUG') === '1' || getenv('APP_DEBUG') === '1') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

mysqli_report(MYSQLI_REPORT_OFF);

/**
 * True when the incoming request is served over HTTPS (direct or reverse proxy).
 */
function ycdo_request_is_https()
{
    if (getenv('YCDO_FORCE_HTTPS') === '1') {
        return true;
    }
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if (!empty($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) {
        return true;
    }
    if (isset($_SERVER['REQUEST_SCHEME']) && strtolower((string) $_SERVER['REQUEST_SCHEME']) === 'https') {
        return true;
    }
    $forwarded = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? $_SERVER['HTTP_X_FORWARDED_PROTOCOL'] ?? '';
    if ($forwarded !== '' && stripos($forwarded, 'https') !== false) {
        return true;
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_SSL']) !== 'off') {
        return true;
    }
    $cfVisitor = $_SERVER['HTTP_CF_VISITOR'] ?? '';
    if ($cfVisitor !== '' && stripos($cfVisitor, 'https') !== false) {
        return true;
    }

    return false;
}

if (session_status() === PHP_SESSION_NONE) {
    $isHttps = ycdo_request_is_https();
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

/**
 * Scheme + host for absolute links (e.g. https://app.example.com).
 */
function ycdo_base_url()
{
    $scheme = ycdo_request_is_https() ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return $scheme . '://' . $host;
}

/**
 * Root-relative path for a script (e.g. /hr/print_gynae_report.php).
 *
 * @param string $relativeScript e.g. print_summary.php or ../bk/print_x.php
 */
function ycdo_resolve_app_path($relativeScript)
{
    $relativeScript = str_replace('\\', '/', (string) $relativeScript);
    if ($relativeScript !== '' && $relativeScript[0] === '/') {
        $path = $relativeScript;
    } else {
        $dir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
        if ($dir === '\\' || $dir === '.') {
            $dir = '';
        }
        $path = ($dir === '' || $dir === '/') ? '/' . ltrim($relativeScript, '/') : $dir . '/' . $relativeScript;
    }

    $segments = array();
    foreach (explode('/', $path) as $seg) {
        if ($seg === '' || $seg === '.') {
            continue;
        }
        if ($seg === '..') {
            array_pop($segments);
            continue;
        }
        $segments[] = $seg;
    }

    return '/' . implode('/', $segments);
}

/**
 * Same-origin URL for popups and redirects. Uses //host/path so the browser keeps
 * the current page scheme (fixes http popups from https HR pages behind a proxy).
 *
 * @param string $relativeScript e.g. print_summary.php or ../bk/print_x.php
 * @param string $queryString optional query without leading ? (key=value&...)
 */
function ycdo_absolute_url($relativeScript, $queryString = '')
{
    $path = ycdo_resolve_app_path($relativeScript);
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $url = '//' . $host . $path;
    if ($queryString !== '') {
        $url .= '?' . ltrim((string) $queryString, '?');
    }

    return $url;
}

/**
 * Absolute URL for HTML form actions. Uses explicit https:// when the request is
 * HTTPS (including behind X-Forwarded-Proto) so POST is not sent to http first.
 */
function ycdo_form_action_url($relativeScript, $queryString = '')
{
    $path = ycdo_resolve_app_path($relativeScript);
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scheme = ycdo_request_is_https() ? 'https' : 'http';
    $url = $scheme . '://' . $host . $path;
    if ($queryString !== '') {
        $url .= '?' . ltrim((string) $queryString, '?');
    }

    return $url;
}

/**
 * If $url is relative, resolve it with ycdo_absolute_url(); otherwise return unchanged.
 */
function ycdo_absolute_url_if_relative($url)
{
    $url = (string) $url;
    if ($url === '' || preg_match('#^(?:[a-z][a-z0-9+.-]*:|//)#i', $url)) {
        return $url;
    }
    if (strpos($url, '?') !== false) {
        list($script, $query) = explode('?', $url, 2);

        return ycdo_absolute_url($script, $query);
    }

    return ycdo_absolute_url($url);
}

/**
 * Echo a window.open() script tag with a fully qualified URL.
 *
 * @param string $target _blank or a named window
 * @param string $features optional window features string
 */
function ycdo_echo_window_open($relativeScript, $queryString = '', $target = '_blank', $features = '')
{
    $url = ycdo_absolute_url($relativeScript, $queryString);
    if ($features !== '') {
        echo '<script>window.open('
            . json_encode($url) . ','
            . json_encode($target) . ','
            . json_encode($features) . ');</script>';
        return;
    }
    if ($target === '_blank') {
        echo '<script>window.open(' . json_encode($url) . ', "_blank");</script>';
        return;
    }
    echo '<script>window.open(' . json_encode($url) . ',' . json_encode($target) . ');</script>';
}

/**
 * Styled empty state for report/print popups when no rows were output.
 */
/**
 * HTML shown in print popups when the user is not logged in (avoids blank redirect).
 */
function ycdo_print_auth_failed_page($message = '')
{
    if ($message === '') {
        $message = 'Your session has expired. Close this window, log in again from the main application, then open the report again.';
    }
    if (!headers_sent()) {
        header('Content-Type: text/html; charset=utf-8');
        http_response_code(401);
    }
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Unable to open report</title></head><body>';
    echo '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:60vh;font-family:Arial,sans-serif;color:#555;text-align:center;padding:24px;">';
    echo '<h2 style="color:#333;">Unable to open report</h2>';
    echo '<p style="max-width:480px;color:#888;">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '<button type="button" onclick="window.close()" style="margin-top:20px;padding:10px 24px;background:#007bff;color:#fff;border:none;border-radius:6px;font-size:16px;cursor:pointer;">Close</button>';
    echo '</div></body></html>';
    exit;
}

function ycdo_echo_report_no_data_found()
{
    echo '
    <div style="
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 60vh;
        font-family: Arial, sans-serif;
        color: #555;
    ">
        <div style="
            font-size: 64px;
            margin-bottom: 20px;
        ">📋</div>
        <h2 style="
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        ">No Data Found</h2>
        <p style="
            font-size: 16px;
            color: #888;
            margin-bottom: 30px;
        ">There are no records available for the selected date or branch.</p>
        <button onclick="window.close()" style="
            padding: 10px 24px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        ">Close</button>
    </div>';
}
