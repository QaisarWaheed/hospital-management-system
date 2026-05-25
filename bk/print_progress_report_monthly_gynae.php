<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
include 'includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';

set_time_limit(120);

$req = progress_report_resolve_request($con);
$date = $req['date'];
$br_id = $req['br_id'];
$like = $req['like'];

$doctors = progress_gynae_progress_monthly_doctors($con, $br_id, $like);
$opd_map = progress_opd_count_by_doctor_lte10($con, $br_id, $like);
$cons_map = progress_tokan_count_by_item_category_doctor($con, $br_id, $like, 29);
$gynae_token_map = progress_item_count_by_doctor($con, $br_id, $like, '483, 1159, 1321, 1414');
$gynae_system_map = progress_gynae_register_count_by_doctor($con, $br_id, $like);
$svd_dnc_map = progress_item_count_by_doctor($con, $br_id, $like, '472, 473, 1118, 1119, 1313, 1314');
$procedure_map = progress_tokan_count_by_item_category_doctor($con, $br_id, $like, 3);
$refer_map = progress_referral_from_count_by_branch($con, $br_id, $like);
?>
<html>
<head>
    <title>GYNAE PROGRESS MONTH <?php echo ycdo_safe_date_format($date.'-01', 'F Y', $date); ?><?php echo get_branch_tag_name_by_id($br_id); ?></title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS MONTH <?php echo ycdo_safe_date_format($date.'-01', 'F Y', $date); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>CONS</th>
            <th>SVD & DNC</th>
            <th>PROCEDURES</th>
            <th>GYNAE TOKEN</th>
            <th>GYNAE FILES</th>
            <th>REFERED PATIENTS</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$count_opd = 0;
$count_consultant_opd = 0;
$count_gynae = 0;
$count_gynae_system = 0;
$count_svd_dnc = 0;
$count_procedure = 0;
$total_reffered = 0;
if (count($doctors) > 0) {
    foreach ($doctors as $dr_id => $row_dr) {
        $dr_id = (int) $dr_id;
        $dr_name = $row_dr['u_name'];
        $opd = $opd_map[$dr_id] ?? 0;
        $consultant_opd = $cons_map[$dr_id] ?? 0;
        $gynae_count = $gynae_token_map[$dr_id] ?? 0;
        $gynae_count_system = $gynae_system_map[$dr_id] ?? 0;
        $svd_dnc_count = $svd_dnc_map[$dr_id] ?? 0;
        $procedure = $procedure_map[$dr_id] ?? 0;
        $reffered = $refer_map[$dr_id] ?? 0;

        $count_opd += $opd;
        $count_consultant_opd += $consultant_opd;
        $count_gynae += $gynae_count;
        $count_gynae_system += $gynae_count_system;
        $count_svd_dnc += $svd_dnc_count;
        $count_procedure += $procedure;
        $total_reffered += $reffered;

        $s++;
        echo '
        <tr style = "text-align: center;">
            <td>'.$s.'</td>
            <td style = "text-align: left;">'.$dr_name.'</td>
            <td>'.$opd.'</td>
            <td>'.$consultant_opd.'</td>
            <td>'.$svd_dnc_count.'</td>
            <td>'.$procedure.'</td>
            <td>'.$gynae_count.'</td>
            <td>'.$gynae_count_system.'</td>
            <td>'.$reffered.'</td>
        </tr>
        ';
    }
}
?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan = "2">TOTAL</th>
            <th><?php echo $count_opd; ?></th>
            <th><?php echo $count_consultant_opd; ?></th>
            <th><?php echo $count_svd_dnc; ?></th>
            <th><?php echo $count_procedure; ?></th>
            <th><?php echo $count_gynae; ?></th>
            <th><?php echo $count_gynae_system; ?></th>
            <th><?php echo $total_reffered; ?></th>
        </tr>
    </tfoot>
</table>
</body>
</html>
<?php mysqli_close($con); ?>
