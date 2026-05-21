<?php
include 'includes/config.php';
include 'includes/connect.php';
require_once __DIR__ . '/../bk/includes/progress_report_params.php';

$req = progress_report_resolve_request($con);
$date = $req['date'];
$br_id = $req['br_id'];
$like = $req['like'];

$opds = array();
$opd_sql = "SELECT doctor_id, users.u_name, COUNT(tokans.id) AS opd
    FROM tokans
    INNER JOIN users ON tokans.doctor_id = users.id
    WHERE tokans.branch_id = '$br_id'
    AND tokans.created LIKE '$like'
    AND tokans.tokan_type_id < 100
    AND tokans.status = 1
    GROUP BY doctor_id, users.u_name
    ORDER BY users.u_name";
$run = mysqli_query($con, $opd_sql);
if ($run) {
    while ($row = mysqli_fetch_assoc($run)) {
        $opds[(int) $row['doctor_id']] = array(
            'name' => $row['u_name'],
            'opd' => (int) $row['opd'],
        );
    }
}

$lab_counts = array();
$lab_sql = "SELECT item_by_doctor.doctor_id, COUNT(DISTINCT item_by_doctor.tokan_no) AS lab_cnt
    FROM item_by_doctor
    INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id
    INNER JOIN items ON item_register_to_branches.item_id = items.id
    WHERE item_by_doctor.created LIKE '$like'
    AND item_by_doctor.branch_id = '$br_id'
    AND items.category_id = '2'
    AND item_by_doctor.status = '2'
    GROUP BY item_by_doctor.doctor_id";
$run_lab = mysqli_query($con, $lab_sql);
if ($run_lab) {
    while ($row = mysqli_fetch_assoc($run_lab)) {
        $lab_counts[(int) $row['doctor_id']] = (int) $row['lab_cnt'];
    }
}
?>
<html>
<head>
    <title><?php echo get_branch_tag_by($br_id) . ' ' . date_format(date_create($date), 'd-M-Y'); ?> DAILY PROGRESS REPORT</title>
</head>
<body>

<table border="solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS DAILY <?php echo date_format(date_create($date), 'd-M-Y'); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th><th>ID</th><th>NAME</th><th>OPD</th><th>LAB</th><th>%LAB</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $s = 0;
    if (count($opds) > 0) {
        foreach ($opds as $doctor_id => $info) {
            $s++;
            $lab = $lab_counts[$doctor_id] ?? 0;
            $opd = $info['opd'];
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
    } else {
        echo '<tr><td colspan="6">NO DATA FOUND</td></tr>';
    }
    ?>
    </tbody>
</table>
</body>
</html>
<?php mysqli_close($con); ?>
