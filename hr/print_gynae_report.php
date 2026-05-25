<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
require_once __DIR__ . '/includes/connect_report.php';
require_once __DIR__ . '/../bk/includes/progress_report_params.php';

@set_time_limit(300);
if (function_exists('ini_set')) {
    @ini_set('max_execution_time', '300');
}

if (isset($_GET['date'])) {
    $date = $_GET['date'];
} elseif (isset($_POST['date'])) {
    $date = $_POST['date'];
} else {
    exit(0);
}

$date_esc = mysqli_real_escape_string($con, (string) $date);
$day_like = $date_esc . '%';
$date_from_start = date_format(date_create($date), 'Y-m');
$month_like = mysqli_real_escape_string($con, $date_from_start) . '%';
$date_label = date_format(date_create($date), ' d F Y');

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>PRINT GYNAE REPORT ' . $date_label . '</title></head><body>';
echo '<p>Generating gynae report…</p>';
if (function_exists('ob_flush')) {
    @ob_flush();
}
@flush();

$dataset = progress_gynae_organization_report_dataset($con, $day_like, $month_like);
progress_render_gynae_organization_report($dataset, $company_name, $date_label);
echo '</body></html>';
mysqli_close($con);
