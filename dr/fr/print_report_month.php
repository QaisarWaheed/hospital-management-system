<?php
require_once __DIR__ . '/../../fr/includes/connect_report.php';
require_once __DIR__ . '/../../includes/report_helpers.php';
require_once __DIR__ . '/../../includes/month_report_helpers.php';

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
echo '<html><head><meta charset="utf-8"><title>Month Report</title></head><body><p>Loading month report…</p>';
if (function_exists('ob_flush')) {
    @ob_flush();
}
@flush();

$byDay = month_report_month_by_day($con, $br_id, $year, $month);
$monthTitle = ycdo_safe_date_format($date, 'F Y', $date);
$branchHeader = summary_branch_header($con, $br_id, $company_name);
$branch_label = $branchHeader['address'] !== '' ? $branchHeader['address'] : $branchHeader['name'];
?>
<table border="solid">
<caption>
    <h2><?php echo htmlspecialchars($company_name); ?></h2>
    <h2><?php echo htmlspecialchars($branch_label); ?></h2>
    <h4>Progress For The Month Of <?php echo htmlspecialchars($monthTitle); ?></h4>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>DATE</th>
            <th>TOKEN CASH</th>
            <th>RETURN TOKEN</th>
            <th>COLLECTION</th>
            <th>LOGIN RECEIVED</th>
            <th>LOGIN EXTRA</th>
            <th>LOGIN SHORT</th>
            <th>LOGIN TOTAl</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$totals = month_report_empty_day();

for ($day = 1; $day <= $days; $day++) {
    $row = isset($byDay[$day]) ? $byDay[$day] : month_report_empty_day();
    $s++;
    $dayPad = $day < 10 ? '0' . $day : (string) $day;
    $select_date = $dayPad . '-' . $month . '-' . $year;
    $cash_amount = (float) $row['cash'];
    $collection_amount = (float) $row['collection'];
    $return_token_amount = (float) $row['return_token'];
    $received_amount = (float) $row['received_amount'];
    $extra_amount = (float) $row['extra_amount'];
    $short_amount = (float) $row['short_amount'];

    $totals['cash'] += $cash_amount;
    $totals['collection'] += $collection_amount;
    $totals['return_token'] += $return_token_amount;
    $totals['received_amount'] += $received_amount;
    $totals['extra_amount'] += $extra_amount;
    $totals['short_amount'] += $short_amount;
?>
        <tr style="text-align: right;">
            <td><?php echo $s; ?></td>
            <td><?php echo htmlspecialchars($select_date); ?></td>
            <td><?php echo report_safe_number_format((float)($cash_amount ?? 0)); ?></td>
            <td><?php echo report_safe_number_format((float)($return_token_amount ?? 0)); ?></td>
            <td><?php echo report_safe_number_format((float)($collection_amount ?? 0)); ?></td>
            <td><?php echo report_safe_number_format((float)($received_amount ?? 0)); ?></td>
            <td><?php echo report_safe_number_format((float)($extra_amount ?? 0)); ?></td>
            <td><?php echo report_safe_number_format((float)($short_amount ?? 0)); ?></td>
            <td><?php echo report_safe_number_format((float)($received_amount + $extra_amount ?? 0)); ?></td>
        </tr>
<?php } ?>
    </tbody>
    <tfoot>
        <tr style="text-align: right;">
            <th colspan="2">TOTAL</th>
            <th><?php echo report_safe_number_format((float)($totals['cash'] ?? 0)); ?></th>
            <th><?php echo report_safe_number_format((float)($totals['return_token'] ?? 0)); ?></th>
            <th><?php echo report_safe_number_format((float)($totals['collection'] ?? 0)); ?></th>
            <th><?php echo report_safe_number_format((float)($totals['received_amount'] ?? 0)); ?></th>
            <th><?php echo report_safe_number_format((float)($totals['extra_amount'] ?? 0)); ?></th>
            <th><?php echo report_safe_number_format((float)($totals['short_amount'] ?? 0)); ?></th>
            <th><?php echo report_safe_number_format((float)($totals['received_amount'] + $totals['extra_amount'] ?? 0)); ?></th>
        </tr>
    </tfoot>
</table>
</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
