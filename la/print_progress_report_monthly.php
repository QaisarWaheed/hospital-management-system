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

$lab_map = array();
$lab_sql = "SELECT ibd.doctor_id, COUNT(DISTINCT ibd.tokan_no) AS lab_count
    FROM item_by_doctor ibd
    INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id
    INNER JOIN items i ON ir.item_id = i.id
    INNER JOIN tokans t ON ibd.tokan_no = t.id
    WHERE ibd.branch_id = '$br_id'
    AND ibd.created >= '$month_start_esc' AND ibd.created < '$month_end_esc'
    AND i.category_id = '2'
    AND t.status = '1'
    GROUP BY ibd.doctor_id";
$run_lab = mysqli_query($con, $lab_sql);
if ($run_lab) {
    while ($row = mysqli_fetch_assoc($run_lab)) {
        $lab_map[(int) $row['doctor_id']] = (int) $row['lab_count'];
    }
}

$s = 0;
$total_lab = 0;
$total_opd = 0;
$select = "SELECT t.doctor_id, u.u_name, b.tag_name,
    COUNT(CASE WHEN t.tokan_type_id <= 100 THEN t.tokan_type_id END) AS opd
    FROM tokans t
    INNER JOIN users u ON t.doctor_id = u.id
    INNER JOIN branchs b ON u.branch_id = b.id
    WHERE t.created >= '$month_start_esc' AND t.created < '$month_end_esc'
    AND t.branch_id = '$br_id'
    AND t.status = '1'
    GROUP BY t.doctor_id, u.u_name, b.tag_name
    ORDER BY t.doctor_id";
$run = mysqli_query($con, $select);
?>
<html>
<head>
    <title><?php echo htmlspecialchars(get_branch_tag_by($br_id), ENT_QUOTES, 'UTF-8'); ?> <?php echo date_format(date_create($date), 'm-Y'); ?> MONTHLY PROGRESS REPORT</title>
</head>
<body>

<table border="solid">
<caption>
    <h2><?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?></h2>
    <h2><?php echo htmlspecialchars(get_branch_name_by($br_id), ENT_QUOTES, 'UTF-8'); ?></h2>
    <h3>PROGRESS MONTH <?php echo date_format(date_create($date), ' F Y'); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>ID</th>
            <th>BRANCH</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>LAB</th>
            <th>%LAB</th>
        </tr>
    </thead>
    <tbody>
<?php
if ($run && mysqli_num_rows($run) > 0) {
    while ($row = mysqli_fetch_array($run)) {
        $doctor_id = (int) $row['doctor_id'];
        $opd = (int) $row['opd'];
        $labs = $lab_map[$doctor_id] ?? 0;
        $total_opd += $opd;
        $total_lab += $labs;
        $labs_percentage = 0;
        if ($opd > 0 && $labs > 0 && $labs < $opd) {
            $labs_percentage = (int) (($labs / $opd) * 100);
        } elseif ($opd > 0 && $labs >= $opd) {
            $labs_percentage = 100;
        }
        $s++;
        ?>
        <tr>
            <td><?php echo $s; ?></td>
            <td><?php echo $doctor_id; ?></td>
            <td><?php echo htmlspecialchars($row['tag_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($row['u_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo $opd; ?></td>
            <td><?php echo $labs; ?></td>
            <td><?php echo $labs_percentage; ?>%</td>
        </tr>
        <?php
    }
    ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <th><?php echo $total_opd; ?></th>
            <th><?php echo $total_lab; ?></th>
            <td></td>
        </tr>
    <?php
}
?>
    </tbody>
</table>
</body>
</html>
<?php
mysqli_close($con);
