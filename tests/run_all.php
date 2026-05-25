<?php

/**
 * Lightweight test runner (no PHPUnit required).
 * Usage: php tests/run_all.php
 */

declare(strict_types=1);

$root = dirname(__DIR__);
require_once $root . '/includes/report_helpers.php';
require_once $root . '/bk/includes/progress_report_params.php';

$passed = 0;
$failed = 0;

function assert_true(bool $condition, string $message): void
{
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  OK  $message\n";
        return;
    }
    $failed++;
    echo " FAIL $message\n";
}

function assert_same($expected, $actual, string $message): void
{
    assert_true($expected === $actual, $message . ' (expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . ')');
}

echo "Report helper tests\n";
assert_same(15, summary_resolve_branch_id(array('b_id' => '15'), array(), 0), 'branch id from b_id');
assert_same(15, summary_login_branch_id(array('u' => '15'), array(), 0), 'login branch id legacy u param');
assert_same(9, summary_resolve_branch_id(array(), array(), 9), 'branch id session default');
assert_same('F', summary_gender_code(1), 'gender female');
assert_same('M', summary_gender_code(2), 'gender male');
assert_same(50, summary_lab_conversion_percent(10, 5), 'lab percent half');
assert_same(100, summary_lab_conversion_percent(5, 10), 'lab percent cap');
assert_same('NULL', summary_previous_tokan_display('NULL'), 'previous tokan null string');

$params = summary_token_report_params(
    array('s' => '2026-04-01', 'e' => '2026-04-30', 'u' => '0', 'un' => 'ALL', 'br_id' => '15'),
    array()
);
assert_true($params !== null, 'token report params not null');
assert_same('2026-04-01', $params['from'] ?? '', 'token from date');
assert_same(15, $params['branch_id'] ?? -1, 'token branch id');

$login = summary_login_report_params(array('s' => '2026-04-22', 'e' => '2026-04-22', 'u' => '15'), array(), 9);
assert_true($login !== null, 'login params from legacy u');
assert_same(15, $login['branch_id'] ?? -1, 'login branch from u');

echo "Progress report tests\n";
$sql = progress_tokans_subquery(null, 9, '2026-04-23%');
assert_true(strpos($sql, "branch_id = '9'") !== false, 'tokans subquery branch');
assert_same(array(), progress_map_int(null, 'SELECT 1', 'id', 'cnt'), 'map int empty on bad connection');

echo "PHP file quality checks\n";
$critical = array(
    'fr/print_summary.php',
    'fr/print_summary_login.php',
    'fr/user_summary.php',
    'bk/print_progress_report_daily_branch.php',
    'includes/report_helpers.php',
    'includes/ycdo_bootstrap.php',
    'bk/includes/progress_report_params.php',
    'hr/includes/progress_report_helper.php',
    'lab/print_progress_report_monthly.php',
    'lab/print_progess_report_daily.php',
);

foreach ($critical as $rel) {
    $path = $root . '/' . $rel;
    assert_true(is_file($path), "file exists: $rel");
    $raw = file_get_contents($path);
    assert_true(is_string($raw), "readable: $rel");
    $bomless = ltrim($raw, "\xEF\xBB\xBF");
    assert_true(strpos($bomless, '<?php') === 0, "starts with php tag: $rel");
    assert_true(strpos($raw, '<<<<<<<') === false, "no conflict markers: $rel");
    assert_true(strpos($raw, 'strpos($dr_name') === false, "no dr_name strpos bug: $rel");
}

$branchReport = file_get_contents($root . '/bk/print_progress_report_daily_branch.php');
assert_true(
    strpos($branchReport, 'progress_dia_patient_stats_by_doctor') !== false,
    'branch report uses batch dia stats'
);
assert_true(
    strpos($branchReport, 'progress_item_row_counts_by_doctor') !== false,
    'branch report uses batch category counts'
);

$summaryPages = array(
    'fr/user_summary.php',
    'fr/user_summary_time.php',
    'fr/user_complete_summary.php',
    'fr/comparision_all_branches.php',
);
foreach ($summaryPages as $page) {
    $contents = file_get_contents($root . '/' . $page);
    assert_true(strpos($contents, 'fr_summary_form_actions') !== false, "$page has action buttons");
    assert_true(strpos($contents, 'target="_blank"') === false, "$page no blank target");
}

$userSummary = file_get_contents($root . '/fr/user_summary.php');
assert_true(strpos($userSummary, 'http_build_query') !== false, 'user summary build query');
assert_true(strpos($userSummary, 'target="_blank"') === false, 'user summary no blank target');

$labMonthly = file_get_contents($root . '/lab/print_progress_report_monthly.php');
assert_true(strpos($labMonthly, 'lab_map') !== false, 'lab monthly uses lab_map batch');
assert_true(strpos($labMonthly, "created LIKE '\$date%'") === false, 'lab monthly no LIKE date');
assert_true(strpos($labMonthly, 'GROUP BY ibd.doctor_id') !== false, 'lab monthly batch GROUP BY doctor');

echo "\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
exit($failed > 0 ? 1 : 0);
