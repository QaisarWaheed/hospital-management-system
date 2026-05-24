<?php
require_once __DIR__ . '/includes/connect.php';

$br_id = isset($_GET['br_id']) ? (int) $_GET['br_id'] : 0;
$report_date = date('Y-m-d');
$start_date = '2025-03-31';
$end_date = date('Y-m-d', strtotime('-1 month'));

$branch_filter = $br_id > 0 ? " AND gynae_register.branch_id = '{$br_id}' " : '';

$select_dr = "SELECT gynae_register.id, gynae_register.token_no, gynae_register.next_visit_date, gynae_register.weeks, patients.name, gynae_register.phone, gynae_register.created, branchs.tag_name, users.u_name, COUNT(gynae_register_history.id) AS total_visits
FROM gynae_register
INNER JOIN users ON users.id = gynae_register.doctor_id
INNER JOIN branchs ON gynae_register.branch_id = branchs.id
LEFT JOIN gynae_register_history ON gynae_register.id = gynae_register_history.gynae_register_id
INNER JOIN tokans ON gynae_register.token_no = tokans.id
INNER JOIN patients ON tokans.patient_id = patients.id
WHERE gynae_register.created > '$start_date'
AND gynae_register.created <= '$end_date'
AND gynae_register.status = '1'
AND gynae_register.id NOT IN (SELECT gynae_register_history.gynae_register_id FROM gynae_register_history)
{$branch_filter}
GROUP BY gynae_register.id
ORDER BY gynae_register.weeks";

$run_dr = mysqli_query($con, $select_dr);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <title>DISCONTINUED GYNAE - <?php echo htmlspecialchars($company_trademark); ?></title>
<style>
@media print {
    @page { size: 210mm 297mm; }
    body { font-size: xx-small; }
}
</style>
</head>
<body>
<div class="row">
	<div class="col-md-12 text-center bg-success py-2"><h1>YCDO</h1></div>
	<div class="col-md-12 background_whitesmoke"><?php include 'navigation_top.php'; ?></div>
<table border="solid" class="table table-bordered py-3">
<caption class="text-center">
    <h2><?php echo htmlspecialchars($company_name); ?></h2>
    <h2><?php echo $br_id > 0 ? htmlspecialchars(get_branch_name_by($br_id)) : 'ALL BRANCHES'; ?></h2>
    <h3>DISCONTINUED (no follow-up visit recorded)</h3>
    <p>Report date: <?php echo ycdo_safe_date_format($report_date, 'd-m-Y', $report_date); ?></p>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>ID</th>
            <th>TOKEN</th>
            <th>DATE</th>
            <th>PATIENT</th>
            <th>PHONE</th>
            <th>BRANCH</th>
            <th>DOCTOR</th>
            <th>LMP / EDD</th>
            <th>VISIT DATE</th>
            <th>TOTAL VISITS</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
if ($run_dr && mysqli_num_rows($run_dr) > 0) {
    while ($row_dr = mysqli_fetch_array($run_dr)) {
        $s++;
        echo '<tr class="text-center">';
        echo '<td>' . $s . '</td>';
        echo '<td>' . (int) $row_dr['id'] . '</td>';
        echo '<td>' . htmlspecialchars($row_dr['token_no']) . '</td>';
        echo '<td>' . ycdo_safe_date_format($row_dr['created'], 'd-m-y', '') . '</td>';
        echo '<td>' . htmlspecialchars($row_dr['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row_dr['phone']) . '</td>';
        echo '<td>' . htmlspecialchars($row_dr['tag_name']) . '</td>';
        echo '<td>' . htmlspecialchars($row_dr['u_name']) . '</td>';
        echo '<td>' . ycdo_safe_date_format($row_dr['weeks'], 'd-m-y', '') . '</td>';
        echo '<td>' . ycdo_safe_date_format($row_dr['next_visit_date'], 'd-m-y', '') . '</td>';
        echo '<td>' . (int) $row_dr['total_visits'] . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="11">No discontinued records found.</td></tr>';
}
?>
    </tbody>
</table>
</div>
</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
