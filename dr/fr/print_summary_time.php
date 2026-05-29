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
$br_id = $params['branch_id'];
$u_name = $params['user_name'];
if ($br_id < 1 && !empty($branch_id)) {
    $br_id = (int) $branch_id;
}

$branchHeader = summary_branch_header($con, $br_id, $company_name);
$fromLabel = ycdo_safe_date_format($from_date, 'd-m-Y H:i', $from_date);
$toLabel = ycdo_safe_date_format($to_date, 'd-m-Y H:i', $to_date);

header('Content-Type: text/html; charset=utf-8');
echo '<html><head><meta charset="utf-8"><title>Token Summary (Time)</title></head><body><p>Loading token summary…</p>';
if (function_exists('ob_flush')) {
    @ob_flush();
}
@flush();

$totals = fr_summary_tokans_totals($con, $from_date, $to_date, $u_id, $br_id, true);
$detailRun = fr_summary_tokans_detail_result($con, $from_date, $to_date, $u_id, $br_id, true);
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
	    <td colspan="11">
	    <?php echo htmlspecialchars($branchHeader['name']); ?>
    	<h6><?php echo htmlspecialchars($branchHeader['address']); ?></h6>
    	<h5>Token Summary (Date &amp; Time)</h5>
         <div style="float:left"><strong>From:</strong> <?php echo htmlspecialchars($fromLabel); ?></div>
         <div style="float:right"><strong>To:</strong> <?php echo htmlspecialchars($toLabel); ?></div><br>
         <div style="float:left"><strong>User Name:</strong> <?php echo htmlspecialchars($u_name); ?></div>
         <div style="float:right">Print: <?php echo date('d-m-Y h:i:s A'); ?></div>
         </td>
	</tr>
		<tr>
			<th>S #</th>
			<th>Time</th>
			<th>Date</th>
			<th>Tokan</th>
			<th>Patient</th>
			<th>Age</th>
			<th>Pre</th>
			<th>Dr Id</th>
			<th>Total Amount</th>
			<th>Type</th>
			<th>Received Amount</th>
		</tr>
	</thead>
	<tbody>
<?php
$s = 0;
if ($detailRun) {
    while ($row = mysqli_fetch_assoc($detailRun)) {
        $s++;
        $pre = ($row['previous_tokan_no'] ?? '') !== '' && ($row['previous_tokan_no'] ?? null) !== 'NULL'
            ? $row['previous_tokan_no'] : 'NULL';
        $genders = fr_summary_gender_letter($row['patient_gender'] ?? 0);
        $name = $row['patient_name'] !== null && $row['patient_name'] !== '' ? $row['patient_name'] : 'No Name';
        $age = $row['patient_age'] ?? 0;
        $token_date = $row['created'];
        echo '<tr>
            <td>' . $s . '</td>
            <td>' . htmlspecialchars(ycdo_safe_date_format($token_date, 'h:i A', '')) . '</td>
            <td>' . htmlspecialchars(ycdo_safe_date_format($token_date, 'd M Y', '')) . '</td>
            <td style="text-align: right;">' . (int) $row['id'] . '</td>
            <td>' . htmlspecialchars($name) . '(' . $genders . ')</td>
            <td style="text-align: right;">' . htmlspecialchars((string) $age) . '</td>
            <td>' . htmlspecialchars((string) $pre) . '</td>
            <td>' . (int) $row['doctor_id'] . '</td>
            <td style="text-align: right;">' . htmlspecialchars((string) $row['cash']) . '</td>
            <td>' . htmlspecialchars($row['type_title']) . '</td>
            <td style="text-align: right;">' . htmlspecialchars((string) $row['cash_received']) . '</td>
        </tr>';
    }
}
?>
<tr style="text-align: right;">
	<th colspan="7"></th>
	<th colspan="2"><?php echo number_format((float)($totals['cash'] ?? 0)); ?></th>
	<th colspan="2"><?php echo number_format((float)($totals['cash_received'] ?? 0)); ?></th>
</tr>
<?php
fr_render_summary_type_breakdown_rows($con, $from_date, $to_date, $u_id, $br_id, true);
if ($u_id === 0) {
    fr_render_summary_branch_extras($con, $from_date, $to_date, $br_id, true);
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
