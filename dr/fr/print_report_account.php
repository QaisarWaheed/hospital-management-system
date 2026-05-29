<?php
require_once __DIR__ . '/../../fr/includes/connect_report.php';
require_once __DIR__ . '/../../includes/report_helpers.php';
require_once __DIR__ . '/../../includes/account_report_helpers.php';

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
echo '<html><head><meta charset="utf-8"><title>Accounts Report</title></head><body><p>Loading accounts report…</p>';
if (function_exists('ob_flush')) {
    @ob_flush();
}
@flush();

$byDay = account_report_month_by_day($con, $br_id, $year, $month);
$monthTitle = ycdo_safe_date_format($date, 'F Y', $date);
$branchHeader = summary_branch_header($con, $br_id, $company_name);
$branch_label = $branchHeader['address'] !== '' ? $branchHeader['address'] : $branchHeader['name'];
?>
<link rel="stylesheet" type="text/css" href="../../fr/css/bootstrap.min.css">
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
            <th>POOR</th>
            <th>GENERAL</th>
            <th>PRIVATE</th>
            <th>URGENT</th>
            <th>TOTAL</th>
            <th>CONSULTANT</th>
            <th>MINOR</th>
            <th>PROCEDURE</th>
            <th>USG</th>
            <th>GYNAE</th>
            <th>ADDMISSIOM</th>
            <th>COLLECTION</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$totals = account_report_empty_day();

for ($day = 1; $day <= $days; $day++) {
    $row = isset($byDay[$day]) ? $byDay[$day] : account_report_empty_day();
    $s++;
    $dayPad = $day < 10 ? '0' . $day : (string) $day;
    $select_date = $dayPad . '-' . $month . '-' . $year;
    $count_poor = (int) $row['poor'];
    $count_general = (int) $row['general'];
    $count_private = (int) $row['private'];
    $count_urgent = (int) $row['urgent'];
    $total = $count_poor + $count_general + $count_private + $count_urgent;
    $collection_amount = (float) $row['collection'];

    $totals['poor'] += $count_poor;
    $totals['general'] += $count_general;
    $totals['private'] += $count_private;
    $totals['urgent'] += $count_urgent;
    $totals['consultant'] += (int) $row['consultant'];
    $totals['minor_procedure'] += (int) $row['minor_procedure'];
    $totals['procedure'] += (int) $row['procedure'];
    $totals['usg'] += (int) $row['usg'];
    $totals['gynae'] += (int) $row['gynae'];
    $totals['admission'] += (int) $row['admission'];
    $totals['collection'] += $collection_amount;
?>
        <tr style="text-align: right;">
            <td><?php echo $s; ?></td>
            <td><?php echo htmlspecialchars($select_date); ?></td>
            <td><?php echo $count_poor; ?></td>
            <td><?php echo $count_general; ?></td>
            <td><?php echo $count_private; ?></td>
            <td><?php echo $count_urgent; ?></td>
            <td><?php echo $total; ?></td>
            <td><?php echo (int) $row['consultant']; ?></td>
            <td><?php echo (int) $row['minor_procedure']; ?></td>
            <td><?php echo (int) $row['procedure']; ?></td>
            <td><?php echo (int) $row['usg']; ?></td>
            <td><?php echo (int) $row['gynae']; ?></td>
            <td><?php echo (int) $row['admission']; ?></td>
            <td><?php echo number_format((float)($collection_amount ?? 0)); ?></td>
        </tr>
<?php } ?>
    </tbody>
    <tfoot>
        <tr style="text-align: right;">
            <th colspan="2">TOTAL</th>
            <th><?php echo (int) $totals['poor']; ?></th>
            <th><?php echo (int) $totals['general']; ?></th>
            <th><?php echo (int) $totals['private']; ?></th>
            <th><?php echo (int) $totals['urgent']; ?></th>
            <th><?php echo (int) ($totals['poor'] + $totals['general'] + $totals['private'] + $totals['urgent']); ?></th>
            <th><?php echo (int) $totals['consultant']; ?></th>
            <th><?php echo (int) $totals['minor_procedure']; ?></th>
            <th><?php echo (int) $totals['procedure']; ?></th>
            <th><?php echo (int) $totals['usg']; ?></th>
            <th><?php echo (int) $totals['gynae']; ?></th>
            <th><?php echo (int) $totals['admission']; ?></th>
            <th><?php echo number_format((float)($totals['collection'] ?? 0)); ?></th>
        </tr>
    </tfoot>
</table>
</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
