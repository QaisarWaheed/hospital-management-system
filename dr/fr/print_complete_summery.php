<?php
require_once __DIR__ . '/includes/connect_report.php';
require_once __DIR__ . '/../../../includes/report_helpers.php';
require_once __DIR__ . '/../../../includes/fr_summary_report_helpers.php';

@set_time_limit(300);
if (function_exists('ini_set')) {
    @ini_set('max_execution_time', '300');
}

$params = summary_token_report_params($_GET, $_POST);
if ($params === null) {
    http_response_code(400);
    exit('Date range is required.');
}

$from_date = $params['from'];
$to_date = $params['to'];
$u_id = $params['user_id'];
$u_name = $params['user_name'];
$br_id = $params['branch_id'];
if ($br_id < 1 && !empty($branch_id)) {
    $br_id = (int) $branch_id;
}

header('Content-Type: text/html; charset=utf-8');
echo '<html><head><meta charset="utf-8"><title>Complete Summary</title></head><body><p>Loading complete summary…</p>';
if (function_exists('ob_flush')) {
    @ob_flush();
}
@flush();

$totals = fr_summary_tokans_totals($con, $from_date, $to_date, $u_id, $br_id, false);
$branchHeader = summary_branch_header($con, $br_id, $company_name);
$fromLabel = ycdo_safe_date_format($from_date, 'd-m-Y', $from_date);
$toLabel = ycdo_safe_date_format($to_date, 'd-m-Y', $to_date);
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
         <div style="float:left"><strong>User Name:</strong> <?php echo htmlspecialchars($u_name); ?></div>
         <div style="float:right">Print Date: <?php echo date('d-m-Y'); ?></div>
         </td>
	</tr>
		<tr>
			<th style="text-align: right;" colspan="5">Total Amount</th>
			<th></th>
			<th colspan="5">Received Amount</th>
		</tr>
	</thead>
	<tbody>
<tr>
	<th style="text-align: right;" colspan="5"><?php echo number_format((float)($totals['cash'] ?? 0)); ?></th>
	<th></th>
	<th colspan="5"><?php echo number_format((float)($totals['cash_received'] ?? 0)); ?></th>
</tr>
<?php
foreach (fr_summary_tokan_type_breakdown($con, $from_date, $to_date, $u_id, $br_id, false) as $typeRow) {
    echo '<tr>
        <th style="text-align: right;" colspan="4">' . htmlspecialchars($typeRow['title']) . '</th>
        <th style="text-align: center;" colspan="3">' . (int) $typeRow['count'] . '</th>
        <th style="text-align: left;" colspan="4">' . number_format((float)($typeRow['amount'] ?? 0)) . '</th>
    </tr>';
}
$lab = fr_summary_ibd_category_totals($con, $from_date, $to_date, true, false);
echo '<tr>
    <th style="text-align: right;" colspan="4">LAB AMOUNT</th>
    <th style="text-align: center;" colspan="3">' . (int) $lab['count'] . '</th>
    <th style="text-align: left;" colspan="4">' . number_format((float)($lab['amount'] ?? 0)) . '</th>
</tr>';
$medicine = fr_summary_ibd_category_totals($con, $from_date, $to_date, false, false);
echo '<tr>
    <th style="text-align: right;" colspan="4">MEDICINE AMOUNT</th>
    <th style="text-align: center;" colspan="3">' . (int) $medicine['count'] . '</th>
    <th style="text-align: left;" colspan="4">' . number_format((float)($medicine['amount'] ?? 0)) . ' Approx</th>
</tr>';
$returns = fr_summary_return_tokens($con, $from_date, $to_date, $br_id, false);
if ($returns['amount'] > 0 || $returns['token_list'] !== '') {
    echo '<tr><th style="text-align: left;" colspan="11">RETURN TOKEN: Amount -> <u>' . number_format((float)($returns['amount'] ?? 0)) . '</u> --- Token Nos -> <u>' . htmlspecialchars($returns['token_list']) . '</u></th></tr>';
}
$pending = fr_summary_pending_amount($con, $from_date, $to_date, false);
if ($pending > 0) {
    echo '<tr><td colspan="11"><h3 style="text-align: center;">PENDING TOKEN Amount -> ' . number_format((float)($pending ?? 0)) . '</h3></td></tr>';
}
$pendingReceive = fr_summary_pending_receive_amount($con, $from_date, $to_date, false);
if ($pendingReceive > 0) {
    echo '<tr><td colspan="11"><h3 style="text-align: center;">PENDING RECEIVED AMOUNT -> ' . number_format((float)($pendingReceive ?? 0)) . '</h3></td></tr>';
}
?>
	</tbody>
</table>
</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
