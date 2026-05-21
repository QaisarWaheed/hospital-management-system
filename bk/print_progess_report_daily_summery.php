<?php
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
        $collections = 0;

        $collection = mysqli_query($con, "SELECT SUM(cash_received) AS total FROM tokans WHERE status = 1 AND created LIKE '$like' AND branch_id = '$br_id' ");
        if ($collection && ($row_collection = mysqli_fetch_assoc($collection))) {
            $collections = (float) ($row_collection['total'] ?? 0);
            $cash_received = $collections;
        }

        $opd = mysqli_query($con, "SELECT COUNT(id) AS total FROM tokans WHERE tokan_type_id < 9 AND status = 1 AND created LIKE '$like' AND branch_id = '$br_id' ");
        if ($opd && ($row_opd = mysqli_fetch_assoc($opd))) {
            $opds = (int) $row_opd['total'];
            $total_opds = $opds;
        }

        $cons_opd = mysqli_query($con, "SELECT COUNT(tokan_no) AS total FROM item_by_doctor WHERE tokan_no IN (SELECT id FROM tokans WHERE created LIKE '$like' AND status = 1) AND branch_id = '$br_id' AND status = '2' AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (489, 849, 850, 1415, 1327, 1139, 1141, 1477, 1154))");
        if ($cons_opd && ($row_cons_opd = mysqli_fetch_assoc($cons_opd))) {
            $cons_opds = (int) $row_cons_opd['total'];
            $total_cons_opds = $cons_opds;
        }

        $denom = $total_opds + $total_cons_opds;
        $per_patient = $denom > 0 ? $cash_received / $denom : 0;
        
        $svd = mysqli_query($con, "SELECT COUNT(tokan_no) AS total FROM item_by_doctor WHERE tokan_no IN (SELECT id FROM tokans WHERE created LIKE '$like' AND status = 1) AND branch_id = '$br_id' AND status = '2' AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (472, 1118, 1313, 1577))");
        if ($svd && ($row_svd = mysqli_fetch_assoc($svd))) {
            $svds = (int) $row_svd['total'];
            $total_svd = $svds;
        }

        $dnc = mysqli_query($con, "SELECT COUNT(tokan_no) AS total FROM item_by_doctor WHERE tokan_no IN (SELECT id FROM tokans WHERE created LIKE '$like' AND status = 1) AND branch_id = '$br_id' AND status = '2' AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (473, 1119, 1314, 1578))");
        if ($dnc && ($row_dnc = mysqli_fetch_assoc($dnc))) {
            $dncs = (int) $row_dnc['total'];
            $total_dnc = $dncs;
        }

        $proc_run = mysqli_query($con, "SELECT COUNT(DISTINCT tokan_no) AS total FROM item_by_doctor WHERE tokan_no IN (SELECT id FROM tokans WHERE created LIKE '$like' AND status = 1) AND branch_id = '$br_id' AND status = '2' AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (SELECT id FROM items WHERE id NOT IN (473, 1119, 1314, 472, 1118, 1313) AND category_id = 3))");
        if ($proc_run && ($row_proc = mysqli_fetch_assoc($proc_run))) {
            $procedures = (int) $row_proc['total'];
            $total_procedure = $procedures;
        }

        $admission = mysqli_query($con, "SELECT COUNT(tokan_no) AS total FROM item_by_doctor WHERE tokan_no IN (SELECT id FROM tokans WHERE created LIKE '$like' AND status = 1) AND branch_id = '$br_id' AND status = '2' AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301, 1579, 1580, 1741, 1742, 1743, 1744))");
        if ($admission && ($row_admissions = mysqli_fetch_assoc($admission))) {
            $admissions = (int) $row_admissions['total'];
            $total_admission = $admissions;
        }

        $ref_run = mysqli_query($con, "SELECT COUNT(*) AS total FROM referral_patients WHERE referral_patient_created LIKE '$like' AND referral_patient_status > '1'");
        if ($ref_run && ($row_ref = mysqli_fetch_assoc($ref_run))) {
            $reffered = (int) $row_ref['total'];
            $total_reffered = $reffered;
        }

        $usg_run = mysqli_query($con, "SELECT COUNT(tokan_no) AS total FROM item_by_doctor WHERE tokan_no IN (SELECT id FROM tokans WHERE created LIKE '$like' AND status = 1) AND branch_id = '$br_id' AND status = '2' AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435))");
        if ($usg_run && ($row_usg = mysqli_fetch_assoc($usg_run))) {
            $usgs = (int) $row_usg['total'];
            $total_usg = $usgs;
        }

        $gyn_run = mysqli_query($con, "SELECT COUNT(*) AS total FROM gynae_register WHERE created LIKE '$like' AND branch_id = '$br_id'");
        if ($gyn_run && ($row_gyn = mysqli_fetch_assoc($gyn_run))) {
            $gynae_system = (int) $row_gyn['total'];
            $total_gynae_system = $gynae_system;
        }

        $run_lab = mysqli_query($con, "SELECT COALESCE(SUM(cash_received), 0) AS total FROM tokans WHERE created LIKE '$like' AND status = 1 AND branch_id = '$br_id' AND id IN (SELECT tokan_no FROM item_by_doctor WHERE item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (SELECT id FROM items WHERE category_id = 2)))");
        if ($run_lab && ($row_lab = mysqli_fetch_assoc($run_lab))) {
            $labs = $row_lab['total'];
        }

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
                echo '<td>'.intval($per_patient).'</td>';
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