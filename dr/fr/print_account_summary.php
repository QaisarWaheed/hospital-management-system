<?php
require_once __DIR__ . '/../../fr/includes/connect_report.php';
require_once __DIR__ . '/../../includes/report_helpers.php';
require_once __DIR__ . '/../../includes/account_summary_helpers.php';

@set_time_limit(300);
if (function_exists('ini_set')) {
    @ini_set('max_execution_time', '300');
}

if (!isset($_GET['month']) || $_GET['month'] === '') {
    http_response_code(400);
    exit('Month is required.');
}

$br_id = (int) ($_GET['br_id'] ?? 0);
$month = substr((string) $_GET['month'], 0, 10);
$ym = ycdo_parse_year_month($month);
$year = $ym['year'];
$monthNum = $ym['month'];
$total_days_of_month = $ym['days'];

header('Content-Type: text/html; charset=utf-8');
echo '<html><head><meta charset="utf-8"><title>Account Summary</title></head><body><p>Loading account summary…</p>';
if (function_exists('ob_flush')) {
    @ob_flush();
}
@flush();

$byDay = account_summary_month_by_day($con, $br_id, $year, $monthNum);
$branchHeader = summary_branch_header($con, $br_id, $company_name);
$monthTitle = ycdo_safe_date_format($month, 'F Y', $month);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Print Account Summary - <?php echo htmlspecialchars($company_trademark); ?></title>
<style>
* { font-size: 16px; }
</style>
</head>
<body onload="window.print()">

<table class="table" style="font-size: 8px">
	<thead>
	<tr style="caption-side: top;text-align: center;">
	    <td colspan="9">
	    <?php echo htmlspecialchars($branchHeader['name']); ?>
    	<h6><?php echo htmlspecialchars($branchHeader['address']); ?></h6>
    	<h5>Account Summary - <span style="text-align: left;font-size: 25px;"><?php echo htmlspecialchars($monthTitle); ?></span></h5>
         <div style="float:left">Print Time: <?php echo date('h:i:s A'); ?></div>
         <div style="float:right">Print Date: <?php echo date('d-m-Y'); ?></div>
         <br>
         <div style="float:left">Print By: <?php echo htmlspecialchars($user_name); ?></div>
         </td>
	</tr>
		<tr>
			<th>Date</th>
			<th>Total Cash</th>
			<th>Pending</th>
			<th>Pending Received</th>
			<th colspan="5">Received Amount</th>
		</tr>
	</thead>
	<tbody>
<?php
$total_cash = 0.0;
$total_cash_received = 0.0;
$total_pending = 0.0;
$total_pending_receive = 0.0;
for ($day = 1; $day <= $total_days_of_month; $day++) {
    $row = isset($byDay[$day]) ? $byDay[$day] : account_summary_empty_day();
    $cash = (float) $row['total_cash'];
    $cash_received = (float) $row['total_cash_received'];
    $pending = (float) $row['pending'];
    $pending_receive = (float) $row['pending_receive'];

    $total_pending += $pending;
    $total_pending_receive += $pending_receive;

    if ($cash == 0.0 || $cash_received == 0.0) {
        continue;
    }

    $total_cash += $cash;
    $total_cash_received += $cash_received;
    $dayPad = $day < 10 ? '0' . $day : (string) $day;
    $select_date = $year . '-' . $monthNum . '-' . $dayPad;
    $display_date = ycdo_safe_date_format($select_date, 'd-m-Y', $select_date);
?>
        <tr>
            <td><?php echo htmlspecialchars($display_date); ?></td>
            <td><?php echo number_format((float)($cash ?? 0)); ?></td>
            <td><?php echo number_format((float)($pending ?? 0)); ?></td>
            <td><?php echo number_format((float)($pending_receive ?? 0)); ?></td>
            <td><?php echo number_format((float)($cash_received ?? 0)); ?></td>
        </tr>
<?php } ?>
        <tr>
            <th></th>
            <th><?php echo number_format((float)($total_cash ?? 0)); ?></th>
            <th><?php echo number_format((float)($total_pending ?? 0)); ?></th>
            <th><?php echo number_format((float)($total_pending_receive ?? 0)); ?></th>
            <th><?php echo number_format((float)($total_cash_received ?? 0)); ?></th>
        </tr>
    </tbody>
</table>

</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
