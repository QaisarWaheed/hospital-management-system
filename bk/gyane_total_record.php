<?php
require_once __DIR__ . '/includes/connect.php';

$br_id = 0;
$date = date('Y-m-d');

if (isset($_POST['date']) && $_POST['date'] !== '') {
    $date = $_POST['date'];
    $br_id = isset($_POST['br_id']) ? (int) $_POST['br_id'] : 0;
} elseif (isset($_GET['date']) && $_GET['date'] !== '') {
    $date = $_GET['date'];
    $br_id = isset($_GET['br_id']) ? (int) $_GET['br_id'] : 0;
}

$date_sql = mysqli_real_escape_string($con, $date);

if ($br_id > 0) {
    $select_dr = "SELECT gynae_register.id, gynae_register.token_no, gynae_register.next_visit_date, gynae_register.weeks, patients.name, gynae_register.phone, gynae_register.created, branchs.tag_name, users.u_name, COUNT(gynae_register_history.id) AS total_visits
    FROM gynae_register
    INNER JOIN users ON users.id = gynae_register.doctor_id
    INNER JOIN branchs ON gynae_register.branch_id = branchs.id
    LEFT JOIN gynae_register_history ON gynae_register.id = gynae_register_history.gynae_register_id
    INNER JOIN tokans ON gynae_register.token_no = tokans.id
    INNER JOIN patients ON tokans.patient_id = patients.id
    WHERE gynae_register.created LIKE '{$date_sql}%'
    AND gynae_register.branch_id = '{$br_id}'
    AND gynae_register.status = '1'
    GROUP BY gynae_register.id
    ORDER BY total_visits ASC";
} else {
    $select_dr = "SELECT gynae_register.id, gynae_register.token_no, gynae_register.next_visit_date, gynae_register.weeks, patients.name, gynae_register.phone, gynae_register.created, branchs.tag_name, users.u_name, COUNT(gynae_register_history.id) AS total_visits
    FROM gynae_register
    INNER JOIN users ON users.id = gynae_register.doctor_id
    INNER JOIN branchs ON gynae_register.branch_id = branchs.id
    LEFT JOIN gynae_register_history ON gynae_register.id = gynae_register_history.gynae_register_id
    INNER JOIN tokans ON gynae_register.token_no = tokans.id
    INNER JOIN patients ON tokans.patient_id = patients.id
    WHERE gynae_register.created > '2025-12-31'
    AND gynae_register.status = '1'
    GROUP BY gynae_register.id
    ORDER BY total_visits DESC";
}

$report_date_label = ycdo_safe_date_format($date, 'd-m-Y', date('d-m-Y'));
$branch_label = $br_id > 0 ? get_branch_name_by($br_id) : 'ALL BRANCHES';
$branch_tag = $br_id > 0 ? get_branch_tag_name_by_id($br_id) : '';
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/nav_style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <title>GYNAE PROGRESS <?php echo htmlspecialchars($report_date_label); ?><?php echo htmlspecialchars($branch_tag); ?></title>
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
    <h2><?php echo htmlspecialchars($branch_label); ?></h2>
    <h3>GYNAE PROGRESS <?php echo htmlspecialchars($report_date_label); ?></h3>
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
            <th>E.E.D</th>
            <th>VISIT DATE</th>
            <th>TOTAL VISITS</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$run_dr = mysqli_query($con, $select_dr);
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
    echo '<tr><td colspan="11">No records found.</td></tr>';
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
