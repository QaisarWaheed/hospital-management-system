<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
include 'includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';

set_time_limit(120);

$req = progress_report_resolve_request($con);
$date = $req['date'];
$br_id = $req['br_id'];
$like = $req['like'];

$doctors = progress_lab_monthly_doctors($con, $br_id, $like);
$collection_map = progress_cash_received_sum_by_doctor($con, $br_id, $like);
$opd_map = progress_opd_count_by_doctor_lte10($con, $br_id, $like);
$cons_map = progress_tokan_count_by_item_category_doctor($con, $br_id, $like, 29);
$lab_map = progress_lab_token_cash_by_doctor($con, $br_id, $like);

$count_opd = 0;
$count_consultant_opd = 0;
$count_lab = 0;
$count_total = 0;
$count_total_lab = 0;
?>
<html>
<head>
    <title>PRINT MONTHLY LAB PROGRESS REPORT</title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS MONTH <?php echo date_format(date_create($date), " F Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th rowspan = "2">S#</th>
            <th rowspan = "2">NAME</th>
            <th rowspan = "2">OPD</th>
            <th rowspan = "2">CONS</th>
            <th colspan = "3">LAB</th>
            <th rowspan = "2">USG</th>
            <th rowspan = "2">COLLECTION</th>
        </tr>
        <tr>
            <th>Diag. Pt.</th>
            <th>%</th>
            <th>AMOUNT</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$has_data = false;

if (count($doctors) > 0) {
    foreach ($doctors as $dr_id => $row_dr) {
        $has_data = true;
        $dr_id = (int) $dr_id;
        $dr_name = $row_dr['u_name'];
        $total = $collection_map[$dr_id] ?? 0;
        $opd = $opd_map[$dr_id] ?? 0;
        $consultant_opd = $cons_map[$dr_id] ?? 0;
        $lab_row = $lab_map[$dr_id] ?? array('lab_cash' => 0.0, 'lab_count' => 0);
        $lab_cash = $lab_row['lab_cash'];
        $total_labs = $lab_row['lab_count'];

        $count_total += $total;
        $count_opd += $opd;
        $count_consultant_opd += $consultant_opd;
        $count_lab += $lab_cash;
        $count_total_lab += $total_labs;

        if (empty($total_labs)) {
            $total_labs = 0;
        }
        $total_ops = $opd + $consultant_opd;
        if ($total_labs == 0 || $total_ops == 0) {
            $per_lab = 0;
        } else {
            $per_lab = number_format(($total_labs / $total_ops) * 100, 2);
        }

        $s++;
        echo '
        <tr style = "text-align: center;">
            <td>'.$s.'</td>
            <td style = "text-align: left;">'.$dr_name.'</td>
            <td>'.$opd.'</td>
            <td>'.$consultant_opd.'</td>
            <td>'.$total_labs.'</td>
            <td>'.$per_lab.'%</td>
            <td style = "text-align: right;">'.number_format($lab_cash).'</td>
            <td style = "text-align: right;">'.number_format($total).'</td>
        </tr>
        ';
    }
}
?>
    </tbody>
    <tfoot>
            <th colspan = "2">TOTAL</th>
            <th><?php echo $count_opd; ?></th>
            <th><?php echo $count_consultant_opd; ?></th>
            <th><?php echo $count_total_lab; ?></th>
            <th></th>
            <th style = "text-align: right;"><?php echo number_format($count_lab); ?></th>
            <th style = "text-align: right;"><?php echo number_format($count_total); ?></th>
        </tr>
        
    </tfoot>
<?php if (!$has_data) { ycdo_echo_report_no_data_found(); } ?>

</table>
</body>
</html>
<?php mysqli_close($con); ?>
