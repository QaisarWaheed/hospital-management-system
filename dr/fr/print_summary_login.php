<?php
require_once __DIR__ . '/includes/connect_report.php';
require_once __DIR__ . '/../../../includes/report_helpers.php';
require_once __DIR__ . '/../../../includes/fr_summary_report_helpers.php';

@set_time_limit(300);
if (function_exists('ini_set')) {
    @ini_set('max_execution_time', '300');
}

$loginParams = summary_login_report_params($_GET, $_POST, (int) $branch_id);
if ($loginParams === null) {
    http_response_code(400);
    exit('Date range is required.');
}

$from_date = $loginParams['from'];
$to_date = $loginParams['to'];
$b_id = $loginParams['branch_id'];
$branchHeader = summary_branch_header($con, $b_id, $company_name);
$fromLabel = ycdo_safe_date_format($from_date, 'd-m-Y', $from_date);
$toLabel = ycdo_safe_date_format($to_date, 'd-m-Y', $to_date);

$bounds = fr_summary_range_bounds($from_date, $to_date, false);
$start = mysqli_real_escape_string($con, $bounds['start']);
$end = mysqli_real_escape_string($con, $bounds['end']);
$b_id = (int) $b_id;

header('Content-Type: text/html; charset=utf-8');
echo '<html><head><meta charset="utf-8"><title>Login Summary</title></head><body><p>Loading login summary…</p>';
if (function_exists('ob_flush')) {
    @ob_flush();
}
@flush();

$sql = "SELECT sd.user_id, sd.computer_total, sd.received_amount, sd.short_amount, sd.extra_amount,
        ld.login_at, ld.logout_at, COALESCE(u.u_name, '') AS user_name
    FROM summary_details sd
    INNER JOIN logins_detail ld ON ld.id = sd.login_id
    LEFT JOIN users u ON u.id = sd.user_id
    WHERE ld.branch_id = $b_id AND ld.status = '2'
        AND ld.login_at >= '$start' AND ld.login_at < '$end'
    ORDER BY sd.created";
$run_users = mysqli_query($con, $sql);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Print Summary - <?php echo htmlspecialchars($company_trademark); ?></title>
<style>* { font-size: 16px; }</style>
</head>
<body onload="window.print()">
<table class="table" style="font-size: 10px">
	<thead>
	<tr style="caption-side: top;text-align: center;">
	    <td colspan="9">
	    <?php echo htmlspecialchars($branchHeader['name']); ?>
    	<h6><?php echo htmlspecialchars($branchHeader['address']); ?></h6>
    	<h5>Token Summary</h5>
         <div style="float:left"><strong>Date:</strong> <?php echo htmlspecialchars($fromLabel); ?> To <?php echo htmlspecialchars($toLabel); ?></div>
         <div style="float:right">Print Time: <?php echo date('h:i:s A'); ?></div><br>
         <div style="float:left"><strong>User Login:</strong> All Logins</div>
         <div style="float:right">Print Date: <?php echo date('d-m-Y'); ?></div>
         </td>
	</tr>
		<tr>
			<th>S #</th>
			<th>Name</th>
			<th>Login Time</th>
			<th>Logout Time</th>
			<th>Computer Amount</th>
			<th>Received Amount</th>
			<th>Extra</th>
			<th>Short</th>
			<th>Total</th>
		</tr>
	</thead>
<tbody>
<?php
$s = 0;
$total_cash = 0.0;
$total_extra = 0.0;
$total_short = 0.0;
$total_r_a = 0.0;
$total_cash_received = 0.0;
if ($run_users) {
    while ($row_users = mysqli_fetch_assoc($run_users)) {
        $s++;
        $computer_total = (float) $row_users['computer_total'];
        $received_amount = (float) $row_users['received_amount'];
        $short_amount = (float) $row_users['short_amount'];
        $extra_amount = (float) $row_users['extra_amount'];
        $total_cash += $computer_total;
        $total_cash_received += $received_amount;
        $total_short += $short_amount;
        $total_extra += $extra_amount;
        $total_receiveable = $received_amount + $extra_amount;
        $total_r_a += $total_receiveable;
        $userName = $row_users['user_name'] !== '' ? $row_users['user_name'] : 'Unknown';
        echo '<tr>
            <td>' . $s . '</td>
            <td>' . htmlspecialchars($userName) . '</td>
            <td>' . htmlspecialchars((string) $row_users['login_at']) . '</td>
            <td>' . htmlspecialchars((string) $row_users['logout_at']) . '</td>
            <td>' . number_format((float)($computer_total ?? 0)) . '</td>
            <td>' . number_format((float)($received_amount ?? 0)) . '</td>
            <td>' . number_format((float)($extra_amount ?? 0)) . '</td>
            <td>' . number_format((float)($short_amount ?? 0)) . '</td>
            <td>' . number_format((float)($total_receiveable ?? 0)) . '</td>
        </tr>';
    }
}
?>
		<tr>
			<th colspan="4"></th>
			<th><?php echo number_format((float)($total_cash ?? 0)); ?></th>
			<th><?php echo number_format((float)($total_cash_received ?? 0)); ?></th>
			<th><?php echo number_format((float)($total_extra ?? 0)); ?></th>
			<th><?php echo number_format((float)($total_short ?? 0)); ?></th>
			<th><?php echo number_format((float)($total_r_a ?? 0)); ?></th>
		</tr>
	</tbody>
</table>
<?php
foreach (fr_summary_tokan_type_breakdown($con, $from_date, $to_date, 0, $b_id, false) as $typeRow) {
    echo '<p style="text-align: center;"><strong>'
        . htmlspecialchars($typeRow['title']) . ' -> ' . (int) $typeRow['count']
        . ' Amount(' . number_format((float)($typeRow['amount'] ?? 0)) . ')</strong></p>';
}
?>
</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
