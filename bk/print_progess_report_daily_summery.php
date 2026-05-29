<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
include 'includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';

$req = progress_report_resolve_request($con);
$date = $req['date'];
$br_id = $req['br_id'];
$like = $req['like'];
$br_name = get_branch_tag_by($br_id);
?>
<html>
<head>
    <title>PRINT PROGRESS SUMMERY REPORT <?php echo $br_name; ?> DATE <?php echo date_format(date_create($date), " d F Y"); ?></title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS SUMMERY <?php echo $br_name; ?> DATE <?php echo date_format(date_create($date), " d F Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>CONS</th>
            <th>% Bill</th>
            <th>LAB</th>
            <th>USG</th>
            <th>SVD</th>
            <th>D&C</th>
            <th>PROCEDURE</th>
            <th>ADMISSION</th>
            <th>GYNAE SYSTEM</th>
            <th>REFERRED</th>
        </tr>
    </thead>
<?php
$s = 0;
$cash_received = 0;
$labs = 0;
$medicines = 0;
$total_admission = 0;
$total_procedure = 0;
$total_dnc = 0;
$total_svd = 0;
$total_referred = 0;
$total_usg = 0;
$total_lab = 0;
$total_medicine = 0;
$total_opds = 0;
$total_cons_opds = 0;
$total_gynae = 0;
// $select = "SELECT DISTINCT `doctor_id` FROM `tokans` WHERE doctor_id IN (SELECT `id` FROM `users` WHERE `branch_id` = '$br_id') AND created like '$date%' AND `branch_id` = '$br_id' ORDER BY `doctor_id` ";
// $run = mysqli_query($con, $select);
{
    echo '<tbody>';
    {
        $s = $s + 1;
        $opds = 0;
        $cons_opds = 0;
        $labs = 0;
        $usgs = 0;
        $svds = 0;
        $dncs = 0;
        $procedures = 0;
        $admissions = 0;
        $gynae_system = 0;
        $reffered = 0;
        $day_stats = progress_single_branch_day_summary($con, $br_id, $like);
        $collections = (float) $day_stats['collection'];
        $cash_received = $collections;
        $opds = (int) $day_stats['opd'];
        $total_opds = $opds;
        $cons_opds = (int) $day_stats['cons_opd'];
        $total_cons_opds = $cons_opds;
        $denom = $total_opds + $total_cons_opds;
        $per_patient = $denom > 0 ? $cash_received / $denom : 0;
        $svds = (int) $day_stats['svd'];
        $total_svd = $svds;
        $dncs = (int) $day_stats['dnc'];
        $total_dnc = $dncs;
        $procedures = (int) $day_stats['procedure'];
        $total_procedure = $procedures;
        $admissions = (int) $day_stats['admission'];
        $total_admission = $admissions;
        $reffered = (int) $day_stats['referred'];
        $total_reffered = $reffered;
        $usgs = (int) $day_stats['usg'];
        $total_usg = $usgs;
        $gynae_system = (int) $day_stats['gynae_system'];
        $total_gynae_system = $gynae_system;
        $labs = $day_stats['lab_cash'];

        // $procedures = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE id NOT IN (473, 1119, 1314, 472, 1118, 1313) AND category_id = 3))"));
        // $total_procedure = $total_procedure + $procedures;
        // $svds = mysqli_num_rows(mysqli_query($con, "SELECT `token_no` FROM `branch_pending_details` WHERE branch_id = '$br_id' AND status = '1' AND token_no IN (SELECT `tokan_no` FROM item_by_doctor WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 2 AND `item_id` IN (SELECT `id` FROM item_register_to_branches WHERE item_id IN (472, 1118, 1313) ) )"));
        // $total_svd = $total_svd + $svds;
        // $dncs = mysqli_num_rows(mysqli_query($con, "SELECT `token_no` FROM `branch_pending_details` WHERE branch_id = '$br_id' AND status = '1' AND token_no IN (SELECT `tokan_no` FROM item_by_doctor WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 2 AND `item_id` IN (SELECT `id` FROM item_register_to_branches WHERE item_id IN (473, 1119, 1314) ) )"));
        // $total_dnc = $total_dnc + $dncs;
        // $procedures = mysqli_num_rows(mysqli_query($con, "SELECT `token_no` FROM `branch_pending_details` WHERE branch_id = '$br_id' AND status = '1' AND token_no IN (SELECT `tokan_no` FROM item_by_doctor WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 2 AND `item_id` IN (SELECT `id` FROM item_register_to_branches WHERE item_id IN (SELECT id FROM items WHERE category_id = '3') ) )"));
        // $total_procedure = $total_procedure + $procedures;
                
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td style = "text-align: left;">'.$br_name.'</td>
                <td>'.$opds.'</td>
                <td>'.$cons_opds.'</td>';
                echo '<td>'.intval($per_patient ?? 0).'</td>';
                echo '<td>'.$labs.'</td>';
                echo '<td>'.$usgs.'</td>
                <td>'.$svds.'</td>
                <td>'.$dncs.'</td>
                <td>'.$procedures.'</td>
                <td>'.$admissions.'</td>
                <td>'.$gynae_system.'</td>
                <td>'.$reffered.'</td>
            </tr>';
    }
    echo '</tbody>';
    // echo '<tfoot>
    //         <tr style = "text-align: right;">
    //             <th></th>
    //             <th></th>
    //             <th>'.$total_opds.'</th>
    //             <th>'.$total_cons_opds.'</th>';
    //             // echo '<th>'.$total_lab.'</th>';
    //             echo '<th>'.$total_usg.'</th>
    //             <th>'.$total_svd.'</th>
    //             <th>'.$total_dnc.'</th>
    //             <th>'.$total_procedure.'</th>
    //             <th>'.$total_admission.'</th>
    //             <th>'.$total_gynae_system.'</th>
    //             <th>'.$total_reffered.'</th>
    //         </tr>
    //     </tfoor>';
}
?>
</table>

</body>
</html>
<?php mysqli_close($con); ?>