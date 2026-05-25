<?php
include 'includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';

$req = progress_report_resolve_request($con);
$date = $req['date'];
$br_id = $req['br_id'];
$like = $req['like'];

$opds = progress_opd_count_by_doctor($con, $br_id, $like);
$usgs = progress_item_count_by_doctor($con, $br_id, $like, '476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435');
$gynae_tokens = progress_item_count_by_doctor($con, $br_id, $like, '483, 1159, 1321, 1414, 1576');
$gynae_system = progress_gynae_register_count_by_doctor($con, $br_id, $like);
$refer_all = progress_referral_from_count_by_doctor($con, $like, false);
$refer_ok = progress_referral_from_count_by_doctor($con, $like, true);

$doctor_ids = array_unique(array_merge(
    array_keys($opds),
    array_keys($usgs),
    array_keys($gynae_tokens),
    array_keys($gynae_system),
    array_keys($refer_all)
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
    <title>PRINT PROGRESS GYNAE DATE <?php echo date_format(date_create($date), ' d F Y'); ?></title>
</head>
<body>

<table border="solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS DATE <?php echo date_format(date_create($date), 'd F Y'); ?></h3>
</caption>
    <thead>
        <tr>
            <th rowspan="2">S#</th>
            <th rowspan="2">NAME</th>
            <th rowspan="2">OPD</th>
            <th rowspan="2">USG</th>
            <th colspan="2">GYNAE REGISTRATION</th>
            <th colspan="3">REFERRAL SYSTEM</th>
        </tr>
        <tr>
            <th>TOKEN</th><th>SYSTEM</th>
            <th>PATIENT</th><th>COMPLETE</th><th>REJECT</th>
        </tr>
    </thead>
<?php
$s = 0;
$total_opds = 0;
$total_usg = 0;
$total_gynae = 0;
$total_gynae_system = 0;
$total_reffered = 0;
$total_reffered_successfull = 0;
$rejected_total = 0;

if (count($doctor_ids) > 0) {
    echo '<tbody>';
    foreach ($doctor_ids as $doctor) {
        $s++;
        $opd = $opds[$doctor] ?? 0;
        $usg = $usgs[$doctor] ?? 0;
        $gyn_cnt = $gynae_tokens[$doctor] ?? 0;
        $gyn_sys = $gynae_system[$doctor] ?? 0;
        $ref = $refer_all[$doctor] ?? 0;
        $ref_ok = $refer_ok[$doctor] ?? 0;
        $rejected = $ref - $ref_ok;

        $total_opds += $opd;
        $total_usg += $usg;
        $total_gynae += $gyn_cnt;
        $total_gynae_system += $gyn_sys;
        $total_reffered += $ref;
        $total_reffered_successfull += $ref_ok;
        $rejected_total += $rejected;

        $doctor_name = $user_names[$doctor] ?? get_uname_by_id($doctor);
        echo '<tr style="text-align: right;">';
        echo '<td>' . $s . '</td>';
        echo '<td style="text-align: left;">' . htmlspecialchars($doctor_name, ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . $opd . '</td><td>' . $usg . '</td>';
        echo '<td>' . $gyn_cnt . '</td><td>' . $gyn_sys . '</td>';
        echo '<td>' . $ref . '</td><td>' . $ref_ok . '</td><td>' . $rejected . '</td>';
        echo '</tr>';
    }
    echo '</tbody><tfoot><tr style="text-align: right;"><th></th><th></th>';
    echo '<th>' . $total_opds . '</th><th>' . $total_usg . '</th><th>' . $total_gynae . '</th><th>' . $total_gynae_system . '</th>';
    echo '<th>' . $total_reffered . '</th><th>' . $total_reffered_successfull . '</th><th>' . $rejected_total . '</th>';
    echo '</tr></tfoot>';
}
?>
</table>
</body>
</html>
<?php mysqli_close($con); ?>
