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
$day_start = date('Y-m-d', strtotime($date)) . ' 00:00:00';
$day_end = date('Y-m-d 00:00:00', strtotime($date . ' +1 day'));
$day_start_esc = mysqli_real_escape_string($con, $day_start);
$day_end_esc = mysqli_real_escape_string($con, $day_end);

$opds = array();
$opd_sql = "SELECT t.doctor_id, u.u_name, COUNT(t.id) AS opd
    FROM tokans t
    INNER JOIN users u ON t.doctor_id = u.id
    WHERE t.branch_id = '$br_id'
    AND t.created >= '$day_start_esc' AND t.created < '$day_end_esc'
    AND t.tokan_type_id < 100
    AND t.status = 1
    GROUP BY t.doctor_id, u.u_name
    ORDER BY u.u_name";
$run = mysqli_query($con, $opd_sql);
if ($run) {
    while ($row = mysqli_fetch_assoc($run)) {
        $opds[(int) $row['doctor_id']] = array(
            'name' => (string) $row['u_name'],
            'opd' => (int) $row['opd'],
        );
    }
}

$lab_counts = array();
$lab_sql = "SELECT ibd.doctor_id, COUNT(DISTINCT ibd.tokan_no) AS lab_cnt
    FROM item_by_doctor ibd
    INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
    INNER JOIN items i ON ir.item_id = i.id
    WHERE ibd.branch_id = '$br_id'
    AND ibd.created >= '$day_start_esc' AND ibd.created < '$day_end_esc'
    AND i.category_id = 2
    AND ibd.status = '2'
    GROUP BY ibd.doctor_id";
$run_lab = mysqli_query($con, $lab_sql);
if ($run_lab) {
    while ($row = mysqli_fetch_assoc($run_lab)) {
        $lab_counts[(int) $row['doctor_id']] = (int) $row['lab_cnt'];
    }
}

$date_obj = date_create($date);
$day_label = $date_obj ? $date_obj->format('d-M-Y') : $date;
$s = 0;
?>
<html>
<head>
    <title><?php echo htmlspecialchars(get_branch_tag_by($br_id), ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($day_label, ENT_QUOTES, 'UTF-8'); ?> DAILY PROGRESS REPORT</title>
</head>
<body>

<table border="solid">
<caption>
    <h2><?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?></h2>
    <h2><?php echo htmlspecialchars(get_branch_name_by($br_id), ENT_QUOTES, 'UTF-8'); ?></h2>
    <h3>PROGRESS DAILY <?php echo htmlspecialchars($day_label, ENT_QUOTES, 'UTF-8'); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th><th>ID</th><th>NAME</th><th>OPD</th><th>LAB</th><th>%LAB</th>
        </tr>
    </thead>
    <tbody>
<?php
if (count($opds) > 0) {
    foreach ($opds as $doctor_id => $info) {
        $s++;
        $lab = $lab_counts[$doctor_id] ?? 0;
        $opd = (int) $info['opd'];
        $pct = $opd > 0 ? (int) (($lab / $opd) * 100) : 0;
        echo '<tr>';
        echo '<td>' . $s . '</td>';
        echo '<td>' . $doctor_id . '</td>';
        echo '<td>' . htmlspecialchars($info['name'], ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . $opd . '</td>';
        echo '<td>' . $lab . '</td>';
        echo '<td>' . $pct . '%</td>';
        echo '</tr>';
    }
}
?>
    </tbody>
</table>
</body>
</html>
<?php
mysqli_close($con);
