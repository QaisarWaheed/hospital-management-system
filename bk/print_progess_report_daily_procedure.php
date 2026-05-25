<?php
include 'includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';

$req = progress_report_resolve_request($con);
$date = $req['date'];
$br_id = $req['br_id'];
$like = $req['like'];

$opds = progress_opd_count_by_doctor($con, $br_id, $like);
$cons_opds = progress_item_count_by_doctor($con, $br_id, $like, '489, 849, 850, 1415, 1327, 1139, 1141, 1477, 1154');
$svds = progress_item_count_by_doctor($con, $br_id, $like, '472, 1118, 1313, 473, 1119, 1314, 1577, 1578');
$procedures = progress_item_count_by_doctor($con, $br_id, $like, '(SELECT id FROM items WHERE category_id = 3)');

$doctor_ids = array_unique(array_merge(array_keys($opds), array_keys($cons_opds), array_keys($svds), array_keys($procedures)));
sort($doctor_ids, SORT_NUMERIC);

$user_names = array();
if (count($doctor_ids) > 0) {
    $ids = implode(',', $doctor_ids);
    $run_names = mysqli_query($con, "SELECT id, u_name FROM users WHERE id IN ($ids)");
    if ($run_names) {
        while ($row = mysqli_fetch_assoc($run_names)) {
            $user_names[(int) $row['id']] = $row['u_name'];
        }
    }
}
?>
<html>
<head>
    <title><?php echo get_branch_name_by($br_id); ?> <?php echo date_format(date_create($date), ' d F Y'); ?> PROCEDURE PROGRESS REPORT</title>
</head>
<body>

<table border="solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>OPD &amp; PROCEDURE PROGRESS DATE <?php echo date_format(date_create($date), ' d F Y'); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>DOCTOR NAME</th>
            <th>TOTAL PATIENT</th>
            <th>CONSULTANT</th>
            <th>SVD &amp; DNC</th>
            <th>PROCEDURE</th>
        </tr>
    </thead>
<?php
$s = 0;
$total_opds = 0;
$total_cons_opds = 0;
$total_svd = 0;
$total_procedure = 0;

if (count($doctor_ids) > 0) {
    echo '<tbody>';
    foreach ($doctor_ids as $doctor) {
        $s++;
        $opd = $opds[$doctor] ?? 0;
        $cons = $cons_opds[$doctor] ?? 0;
        $svd = $svds[$doctor] ?? 0;
        $proc = $procedures[$doctor] ?? 0;
        $total_opds += $opd;
        $total_cons_opds += $cons;
        $total_svd += $svd;
        $total_procedure += $proc;
        $doctor_name = $user_names[$doctor] ?? get_uname_by_id($doctor);
        echo '<tr style="text-align: right;">';
        echo '<td>' . $s . '</td>';
        echo '<td style="text-align: left;">' . htmlspecialchars($doctor_name, ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . $opd . '</td><td>' . $cons . '</td><td>' . $svd . '</td><td>' . $proc . '</td>';
        echo '</tr>';
    }
    echo '</tbody><tfoot><tr style="text-align: right;"><th></th><th></th>';
    echo '<th>' . $total_opds . '</th><th>' . $total_cons_opds . '</th><th>' . $total_svd . '</th><th>' . $total_procedure . '</th>';
    echo '</tr></tfoot>';
}
?>
</table>
</body>
</html>
<?php mysqli_close($con); ?>
