<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
require_once __DIR__ . '/includes/connect_report.php';
require_once __DIR__ . '/includes/progress_report_params.php';

@set_time_limit(300);
if (function_exists('ini_set')) {
    @ini_set('max_execution_time', '300');
}

$date = null;
if (isset($_GET['date']) && $_GET['date'] !== '') {
    $date = (string) $_GET['date'];
} elseif (isset($_POST['date']) && $_POST['date'] !== '') {
    $date = (string) $_POST['date'];
}

$dateObj = $date !== null ? date_create($date) : false;
if ($dateObj === false) {
    header('Content-Type: text/html; charset=utf-8');
    http_response_code(400);
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Invalid date</title></head><body>';
    echo '<p>Invalid report date. Close this window and try again.</p></body></html>';
    exit;
}

$date_esc = mysqli_real_escape_string($con, (string) $date);
$day_like = $date_esc . '%';
$date_from_start = $dateObj->format('Y-m');
$month_like = mysqli_real_escape_string($con, $date_from_start) . '%';
$date_label = $dateObj->format(' d F Y');

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>PRINT GYNAE REPORT' . htmlspecialchars($date_label, ENT_QUOTES, 'UTF-8') . '</title></head><body>';

try {
    $dataset = progress_gynae_organization_report_dataset($con, $day_like, $month_like);
    progress_render_gynae_organization_report($dataset, $company_name, $date_label);
} catch (Throwable $e) {
    error_log('print_gynae_report.php: ' . $e->getMessage());
}

echo '</body></html>';
if ($con instanceof mysqli) {
    mysqli_close($con);
}
