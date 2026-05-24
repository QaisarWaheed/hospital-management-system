<?php
require_once __DIR__ . '/includes/connect.php';
require_once __DIR__ . '/includes/gynae_report_queries.php';

$br_id = isset($_GET['br_id']) ? (int) $_GET['br_id'] : (int) $bk_branch_id;
$report_date = date('Y-m-d');
$title = ycdo_gynae_gestational_report_title('gt8');
$select_dr = ycdo_gynae_gestational_report_sql('gt8', $br_id);
$run_dr = mysqli_query($con, $select_dr);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <title><?php echo htmlspecialchars($title); ?> - <?php echo htmlspecialchars($company_trademark); ?></title>
<style>
@media print {
    @page { size: 210mm 297mm; }
    body { font-size: xx-small; }
    .no-print { display: none !important; }
}
</style>
</head>
<body>
<div class="row">
	<div class="col-md-12 text-center bg-success py-2"><h1>YCDO</h1></div>
	<div class="col-md-12 background_whitesmoke no-print"><?php include 'navigation_top.php'; ?></div>
<table border="solid" class="table table-bordered">
<caption class="text-center">
    <h2><?php echo htmlspecialchars($company_name); ?></h2>
    <h2><?php echo $br_id > 0 ? htmlspecialchars(get_branch_name_by($br_id)) : 'ALL BRANCHES'; ?></h2>
    <h3><?php echo htmlspecialchars($title); ?></h3>
    <p>8 months gestation completed — please contact patients as needed.</p>
    <p>Report date: <?php echo ycdo_safe_date_format($report_date, 'd-m-Y', $report_date); ?></p>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>ID</th>
            <th>TOKEN</th>
            <th>BRANCH</th>
            <th>REGISTERED</th>
            <th>PATIENT</th>
            <th>PHONE</th>
            <th>DOCTOR</th>
            <th>LMP / EDD</th>
            <th>MONTHS</th>
            <th>NEXT VISIT</th>
            <th>VISITS</th>
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
        echo '<td>' . htmlspecialchars($row_dr['tag_name']) . '</td>';
        echo '<td>' . ycdo_safe_date_format($row_dr['created'], 'd-m-y', '') . '</td>';
        echo '<td>' . htmlspecialchars($row_dr['name']) . '</td>';
        echo '<td>' . htmlspecialchars($row_dr['phone']) . '</td>';
        echo '<td>' . htmlspecialchars($row_dr['u_name']) . '</td>';
        echo '<td>' . ycdo_safe_date_format($row_dr['weeks'], 'd-m-y', '') . '</td>';
        echo '<td>' . (int) $row_dr['gestational_months'] . '</td>';
        echo '<td>' . ycdo_safe_date_format($row_dr['next_visit_date'], 'd-m-y', '') . '</td>';
        echo '<td>' . (int) $row_dr['total_visits'] . '</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="12">No records found for this gestational range.</td></tr>';
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
