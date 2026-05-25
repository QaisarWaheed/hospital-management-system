<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
include 'includes/config.php';
include 'includes/connect.php';

if (isset($_GET['date'])) {
    $date = (string) $_GET['date'];
    $br_id = (int) $_GET['br_id'];
} elseif (isset($_POST['date'])) {
    $date = (string) $_POST['date'];
    $br_id = (int) $_POST['br_id'];
} else {
    exit(0);
}

$br_id = (int) $br_id;
$month_start = date('Y-m-01 00:00:00', strtotime($date));
$month_end = date('Y-m-01 00:00:00', strtotime($date . ' +1 month'));
$month_start_esc = mysqli_real_escape_string($con, $month_start);
$month_end_esc = mysqli_real_escape_string($con, $month_end);

$opd_rows = array();
$opd_sql = "SELECT t.doctor_id, u.u_name, COUNT(t.id) AS opd
    FROM tokans t
    INNER JOIN users u ON t.doctor_id = u.id
    WHERE t.branch_id = '$br_id'
    AND t.created >= '$month_start_esc' AND t.created < '$month_end_esc'
    AND t.tokan_type_id < 100
    AND t.status = 1
    GROUP BY t.doctor_id, u.u_name
    ORDER BY u.u_name";
$run_opd = mysqli_query($con, $opd_sql);
if ($run_opd) {
    while ($row = mysqli_fetch_assoc($run_opd)) {
        $opd_rows[(int) $row['doctor_id']] = array(
            'u_name' => (string) $row['u_name'],
            'opd' => (int) $row['opd'],
        );
    }
}

$lab_counts = array();
$lab_sql = "SELECT ibd.doctor_id, COUNT(DISTINCT ibd.tokan_no) AS lab_count
    FROM item_by_doctor ibd
    INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
    INNER JOIN items i ON ir.item_id = i.id
    WHERE ibd.branch_id = '$br_id'
    AND ibd.created >= '$month_start_esc' AND ibd.created < '$month_end_esc'
    AND i.category_id = 2
    AND ibd.status = '2'
    GROUP BY ibd.doctor_id";
$run_lab = mysqli_query($con, $lab_sql);
if ($run_lab) {
    while ($row = mysqli_fetch_assoc($run_lab)) {
        $lab_counts[(int) $row['doctor_id']] = (int) $row['lab_count'];
    }
}

$date_obj = date_create($date);
$month_label = $date_obj ? $date_obj->format('F Y') : $date;
$has_data = false;
$s = 0;
?>
<html>
<head>
    <title><?php echo htmlspecialchars(get_branch_tag_by($br_id), ENT_QUOTES, 'UTF-8'); ?> <?php echo $date_obj ? $date_obj->format('m-Y') : ''; ?> MONTHLY PROGRESS REPORT</title>
</head>
<body>

<table border="solid">
<caption>
    <h2><?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?></h2>
    <h2><?php echo htmlspecialchars(get_branch_name_by($br_id), ENT_QUOTES, 'UTF-8'); ?></h2>
    <h3>PROGRESS MONTH <?php echo htmlspecialchars($month_label, ENT_QUOTES, 'UTF-8'); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>ID</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>LAB</th>
            <th>%LAB</th>
        </tr>
    </thead>
    <tbody>
<?php
if (count($opd_rows) > 0) {
    foreach ($opd_rows as $doctor_id => $row) {
        $has_data = true;
        $s++;
        $opd = (int) $row['opd'];
        $lab = $lab_counts[$doctor_id] ?? 0;
        $pct = ($opd > 0 && $lab > 0) ? (int) (($lab / $opd) * 100) : 0;
        if ($opd > 0 && $lab >= $opd) {
            $pct = 100;
        }
        ?>
        <tr>
            <td><?php echo $s; ?></td>
            <td><?php echo $doctor_id; ?></td>
            <td><?php echo htmlspecialchars($row['u_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo $opd; ?></td>
            <td><?php echo $lab; ?></td>
            <td><?php echo $pct; ?>%</td>
        </tr>
        <?php
    }
}
if (!$has_data) {
    echo '<tr><td colspan="6">NO DATA FOUND</td></tr>';
}
?>
    </tbody>
</table>
</body>
</html>
<?php
mysqli_close($con);
