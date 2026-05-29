<?php
include 'includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';

$req = progress_report_resolve_request($con);
$date = $req['date'];
$br_id = $req['br_id'];
$like = $req['like'];

$opds = progress_opd_count_by_doctor($con, $br_id, $like);
$admissions = progress_item_count_by_doctor($con, $br_id, $like, '444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301, 1579, 1580');
$usgs = progress_item_count_by_doctor($con, $br_id, $like, '476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435');
$gynae_system = progress_gynae_register_count_by_doctor($con, $br_id, $like);
$referred = progress_referral_from_count_by_doctor($con, $like);

$doctor_ids = array_unique(array_merge(
    array_keys($opds),
    array_keys($admissions),
    array_keys($usgs),
    array_keys($gynae_system),
    array_keys($referred)
));
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
    <title><?php echo get_branch_name_by($br_id); ?> <?php echo date_format(date_create($date), ' d F Y'); ?> OTHER SERIVES PROGRESS REPORT</title>
</head>
<body>

<table border="solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>OTHER SERIVES PROGRESS DATE <?php echo date_format(date_create($date), ' d F Y'); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>DOCTOR NAME</th>
            <th>TOTAL PATIENT</th>
            <th>ADMISSION</th>
            <th>USG</th>
            <th>GYANE SYSTEM</th>
            <th>REFERAL</th>
        </tr>
    </thead>
<?php
$s = 0;
$total_opds = 0;
$total_admission = 0;
$total_usg = 0;
$total_gynae_system = 0;
$total_referred = 0;

if (count($doctor_ids) > 0) {
    echo '<tbody>';
    foreach ($doctor_ids as $doctor) {
        $s++;
        $opd = $opds[$doctor] ?? 0;
        $adm = $admissions[$doctor] ?? 0;
        $usg = $usgs[$doctor] ?? 0;
        $gyn = $gynae_system[$doctor] ?? 0;
        $ref = $referred[$doctor] ?? 0;
        $total_opds += $opd;
        $total_admission += $adm;
        $total_usg += $usg;
        $total_gynae_system += $gyn;
        $total_referred += $ref;
        $doctor_name = $user_names[$doctor] ?? get_uname_by_id($doctor);
        echo '<tr style="text-align: right;">';
        echo '<td>' . $s . '</td>';
        echo '<td style="text-align: left;">' . htmlspecialchars($doctor_name, ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . $opd . '</td><td>' . $adm . '</td><td>' . $usg . '</td><td>' . $gyn . '</td><td>' . $ref . '</td>';
        echo '</tr>';
    }
    echo '</tbody><tfoot><tr style="text-align: right;"><th></th><th></th>';
    echo '<th>' . $total_opds . '</th><th>' . $total_admission . '</th><th>' . $total_usg . '</th>';
    echo '<th>' . $total_gynae_system . '</th><th>' . $total_referred . '</th></tr></tfoot>';
}
?>
</table>
</body>
</html>
<?php mysqli_close($con); ?>
