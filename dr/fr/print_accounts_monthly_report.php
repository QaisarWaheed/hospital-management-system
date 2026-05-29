<?php
require_once __DIR__ . '/includes/connect_report.php';
require_once __DIR__ . '/../../../includes/report_helpers.php';
require_once __DIR__ . '/../../../includes/account_report_helpers.php';

@set_time_limit(300);
if (function_exists('ini_set')) {
    @ini_set('max_execution_time', '300');
}

if (!isset($_GET['date'], $_GET['br_id']) || $_GET['date'] === '') {
    http_response_code(400);
    exit('Date and branch are required.');
}

$date = substr((string) $_GET['date'], 0, 10);
$br_id = (int) $_GET['br_id'];
$ym = ycdo_parse_year_month($date);
$year = $ym['year'];
$month = $ym['month'];
$days = $ym['days'];

header('Content-Type: text/html; charset=utf-8');
echo '<html><head><meta charset="utf-8"><title>Monthly Accounts</title></head><body><p>Loading monthly report…</p>';
if (function_exists('ob_flush')) {
    @ob_flush();
}
@flush();

$byDay = accounts_monthly_report_month_by_day($con, $br_id, $year, $month);
$branchHeader = summary_branch_header($con, $br_id, $company_name);
$monthTitle = ycdo_safe_date_format($date, 'F Y', $date);
$empty = accounts_monthly_empty_day();
$totals = $empty;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PRINT MONTHLY ACCOUNTS REPORT</title>
</head>
<body onload="window.print()">
<table border="solid">
<caption>
    <h2><?php echo htmlspecialchars($company_name); ?></h2>
    <h2><?php echo htmlspecialchars($branchHeader['name']); ?></h2>
    <h4>Progress For The Month Of <?php echo htmlspecialchars($monthTitle); ?></h4>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>DATE</th>
            <th>POOR</th>
            <th>GENERAL</th>
            <th>PRIVATE</th>
            <th>URGENT</th>
            <th>TOTAL</th>
            <th>CONSULTANT</th>
            <th>PROCEDURE</th>
            <th>MEDICINE</th>
            <th>LAB</th>
            <th>COLLECTION</th>
        </tr>
    </thead>
    <tbody>
<?php
for ($day = 1; $day <= $days; $day++) {
    $row = isset($byDay[$day]) ? $byDay[$day] : $empty;
    if (!isset($row['medicine'])) {
        $row['medicine'] = 0.0;
    }
    if (!isset($row['lab'])) {
        $row['lab'] = 0.0;
    }
    $dayPad = $day < 10 ? '0' . $day : (string) $day;
    $select_date = $dayPad . '-' . $month . '-' . $year;
    $count_poor = (int) $row['poor'];
    $count_general = (int) $row['general'];
    $count_private = (int) $row['private'];
    $count_urgent = (int) $row['urgent'];
    $total = $count_poor + $count_general + $count_private + $count_urgent;
    $collection_amount = (float) $row['collection'];
    $cash_received_procedure = (float) ($row['procedure'] ?? 0);
    $cash_received_medicine = (float) $row['medicine'];
    $cash_received_lab = (float) $row['lab'];
    $count_consultent = (int) $row['consultant'];

    $totals['poor'] += $count_poor;
    $totals['general'] += $count_general;
    $totals['private'] += $count_private;
    $totals['urgent'] += $count_urgent;
    $totals['consultant'] += $count_consultent;
    $totals['procedure'] += $cash_received_procedure;
    $totals['medicine'] += $cash_received_medicine;
    $totals['lab'] += $cash_received_lab;
    $totals['collection'] += $collection_amount;

    echo '<tr style="text-align: right;">
        <td>' . $day . '</td>
        <td>' . htmlspecialchars($select_date) . '</td>
        <td>' . $count_poor . '</td>
        <td>' . $count_general . '</td>
        <td>' . $count_private . '</td>
        <td>' . $count_urgent . '</td>
        <td>' . $total . '</td>
        <td>' . $count_consultent . '</td>
        <td>' . number_format((float)($cash_received_procedure ?? 0)) . '</td>
        <td>' . number_format((float)($cash_received_medicine ?? 0)) . '</td>
        <td>' . number_format((float)($cash_received_lab ?? 0)) . '</td>
        <td>' . number_format((float)($collection_amount ?? 0)) . '</td>
    </tr>';
}
$grandTotal = (int) $totals['poor'] + (int) $totals['general'] + (int) $totals['private'] + (int) $totals['urgent'];
?>
    </tbody>
    <tfoot>
        <tr style="text-align: right;">
            <th colspan="2">TOTAL</th>
            <th><?php echo (int) $totals['poor']; ?></th>
            <th><?php echo (int) $totals['general']; ?></th>
            <th><?php echo (int) $totals['private']; ?></th>
            <th><?php echo (int) $totals['urgent']; ?></th>
            <th><?php echo $grandTotal; ?></th>
            <th><?php echo (int) $totals['consultant']; ?></th>
            <th><?php echo number_format((float)((float) $totals['procedure'] ?? 0)); ?></th>
            <th><?php echo number_format((float)((float) $totals['medicine'] ?? 0)); ?></th>
            <th><?php echo number_format((float)((float) $totals['lab'] ?? 0)); ?></th>
            <th><?php echo number_format((float)((float) $totals['collection'] ?? 0)); ?></th>
        </tr>
    </tfoot>
</table>
</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
