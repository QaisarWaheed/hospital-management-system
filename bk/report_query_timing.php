<?php
/**
 * Diagnose slow BK reports (logged-in BK users only).
 * Open: /bk/report_query_timing.php?date=2026-04-24
 */
require_once __DIR__ . '/includes/connect_report.php';
require_once __DIR__ . '/includes/progress_report_params.php';
require_once __DIR__ . '/includes/comparison_report_helpers.php';

header('Content-Type: text/plain; charset=utf-8');

$date = isset($_GET['date']) ? substr((string) $_GET['date'], 0, 10) : date('Y-m-d');
$month = isset($_GET['month']) ? substr((string) $_GET['month'], 0, 7) : date('Y-m');

function report_time_query($con, $label, callable $fn)
{
    $t0 = microtime(true);
    $fn($con);
    $ms = round((microtime(true) - $t0) * 1000 ?? 0);
    echo $label . ': ' . $ms . " ms\n";
}

echo "Report query timing\n";
echo "Date: $date\n";
echo "Month: $month\n\n";

report_time_query($con, 'progress_opd', static function ($con) use ($date) {
    progress_opd_count_by_branch_day($con, $date);
});
report_time_query($con, 'progress_items', static function ($con) use ($date) {
    progress_item_metrics_by_branch_day($con, $date);
});
report_time_query($con, 'progress_gynae', static function ($con) use ($date) {
    progress_gynae_register_count_by_branch_day($con, $date);
});
report_time_query($con, 'progress_full', static function ($con) use ($date) {
    progress_organization_daily_branch_summary($con, $date);
});

$m2 = isset($_GET['month2']) ? substr((string) $_GET['month2'], 0, 7) : date('Y-m', strtotime($month . '-01 +1 month'));
$m1b = comparison_month_bounds($month);
$m2b = comparison_month_bounds($m2);
$rs = min($m1b[0], $m2b[0]);
$re = max($m1b[1], $m2b[1]);

report_time_query($con, 'comparison_tokans', static function ($con) use ($m1b, $m2b, $rs, $re) {
    $first = array();
    $second = array();
    comparison_load_tokans_both_months($con, $m1b, $m2b, $rs, $re, $first, $second);
});
report_time_query($con, 'comparison_cons_m1', static function ($con) use ($m1b) {
    $b = array();
    comparison_load_cons_month($con, $m1b[0], $m1b[1], $b);
});
report_time_query($con, 'comparison_cons_m2', static function ($con) use ($m2b) {
    $b = array();
    comparison_load_cons_month($con, $m2b[0], $m2b[1], $b);
});
report_time_query($con, 'comparison_procedures_m1', static function ($con) use ($m1b) {
    $b = array();
    comparison_load_procedures_month($con, $m1b[0], $m1b[1], $b);
});
report_time_query($con, 'comparison_procedures_m2', static function ($con) use ($m2b) {
    $b = array();
    comparison_load_procedures_month($con, $m2b[0], $m2b[1], $b);
});
report_time_query($con, 'comparison_lab_m1', static function ($con) use ($m1b) {
    $b = array();
    comparison_load_lab_month($con, $m1b[0], $m1b[1], $b);
});
report_time_query($con, 'comparison_lab_m2', static function ($con) use ($m2b) {
    $b = array();
    comparison_load_lab_month($con, $m2b[0], $m2b[1], $b);
});
report_time_query($con, 'comparison_total', static function ($con) use ($month, $m2) {
    comparison_two_month_stats($con, $month, $m2);
});

echo "\nIndexes (show if missing):\n";
$tables = array('tokans', 'item_by_doctor', 'gynae_register');
foreach ($tables as $table) {
    echo "\n-- $table --\n";
    $run = mysqli_query($con, "SHOW INDEX FROM `$table`");
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            echo $row['Key_name'] . ' (' . $row['Column_name'] . ")\n";
        }
    }
}

mysqli_close($con);
