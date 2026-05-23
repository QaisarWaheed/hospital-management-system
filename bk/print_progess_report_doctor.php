<?php 
include 'includes/connect.php';
require_once __DIR__ . '/../includes/report_helpers.php';
require_once __DIR__ . '/includes/branch_select_options.php';

if(isset($_GET['date']))
{
    $date = $_GET['date'];
    $br_id = (int) $_GET['br_id'];
}
elseif(isset($_POST['date']))
{
    $date = $_POST['date'];
    $br_id = (int) $_POST['br_id'];
}
else
{
    exit(0);
}
?>
<html>
<head>
    <title>PRINT PROGRESS REPORT</title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS DATE <?php echo date_format(date_create($date), " d F Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>CONS</th>
            <!--<th>LAB</th>-->
            <th>USG</th>
            <th>COLLECTION</th>
            <!--<th>D&C</th>-->
            <!--<th>PROCEDURE</th>-->
            <!--<th>ADMISSION</th>-->
            <!--<th>GYNAE SYSTEM</th>-->
            <!--<th>REFERRED</th>-->
        </tr>
    </thead>
<?php
$s = 0; 
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
$select = "SELECT DISTINCT `doctor_id` FROM `tokans` WHERE branch_id = '$br_id' AND created like '$date%' ORDER BY `doctor_id` ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    echo '<tbody>';
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $doctor = $row['doctor_id'];

        $opd = mysqli_query($con, "SELECT COUNT(id) FROM tokans WHERE `tokan_type_id` < 9 AND status = 1 and doctor_id = '$doctor' AND created like '$date%' ");
        while($row_opd = mysqli_fetch_array($opd))
        {
            $opds = $row_opd['0'];
        }

        $cons_opd = mysqli_query($con, "SELECT COUNT(`tokan_no`) FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (489, 849, 850, 1415, 1327, 1139, 1141, 1477, 1154) )");
        while($row_cons_opd = mysqli_fetch_array($cons_opd))
        {
            $cons_opds = $row_cons_opd['0'];
        }        

        $collection = mysqli_query($con, "SELECT SUM(cash) FROM tokans WHERE `tokan_type_id` < 9 AND status = 1 and doctor_id = '$doctor' AND created like '$date%' OR (id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (489, 849, 850, 1415, 1327, 1139, 1141, 1477, 1154,476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435) )) )");
        while($row_collection = mysqli_fetch_array($collection))
        {
            $collections = $row_collection['0'];
        }        

        // $svd = mysqli_query($con, "SELECT COUNT(`tokan_no`) FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (472, 1118, 1313, 1577) )");
        // while($row_svd = mysqli_fetch_array($svd))
        // {
        //     $svds = $row_svd['0'];
        //     $total_svd = $total_svd + $svds;
        // }        

        // $dnc = mysqli_query($con, "SELECT COUNT(`tokan_no`) FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (473, 1119, 1314, 1578) )");
        // while($row_dnc = mysqli_fetch_array($dnc))
        // {
        //     $dncs = $row_dnc['0'];
        //     $total_dnc = $total_dnc + $dncs;
        // }        

        // $procedures = mysqli_num_rows(mysqli_query($con, "SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE id NOT IN (473, 1119, 1314, 472, 1118, 1313) AND category_id = 3))"));
        // $total_procedure = $total_procedure + $procedures;
        
        // $procedure = mysqli_query($con, "SELECT COUNT(`tokan_no`) FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE id NOT IN (473, 1119, 1314, 472, 1118, 1313) AND category_id = '3' )");
        // while($row_procedure = mysqli_fetch_array($procedure))
        // {
        //     $procedures = $row_procedure['0'];
        //     $total_procedure = $total_procedure + $procedures;
        // }        

        // $admission = mysqli_query($con, "SELECT COUNT(`tokan_no`) FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301, 1579, 1580, 1741, 1742, 1743, 1744) )");
        // while($row_admissions = mysqli_fetch_array($admission))
        // {
        //     $admissions = $row_admissions['0'];
        //     $total_admission = $total_admission + $admissions;
        // }        

        // $reffered = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `referral_patients` WHERE referral_patient_created LIKE '$date%' AND from_user_id = '$doctor' AND referral_patient_status > '1' "));
        // $total_reffered = $total_reffered + $reffered;

        $usgs = mysqli_query($con, "SELECT COUNT(`tokan_no`) FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435))");
        while($row_usg = mysqli_fetch_array($usgs))
        {
            $usgs = $row_usg['0'];
        }

        // $gynae = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (483, 1159, 1321, 1414))"));
        // $total_gynae = $total_gynae + $gynae;

        // $gynae_system = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `gynae_register` WHERE doctor_id = '$doctor' AND created like '$date%' AND branch_id = '$br_id'"));
        // $total_gynae_system = $total_gynae_system + $gynae_system;

        // $select_lab = "SELECT SUM(`cash_received`) FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1 AND branch_id = '$br_id' AND `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT id FROM `item_register_to_branches` WHERE item_id IN (SELECT id FROM items WHERE category_id = 2)))";
        // $run_lab = mysqli_query($con, $select_lab);
        // if(mysqli_num_rows($run_lab) > 0)
        // {
        //     while($row_lab = mysqli_fetch_array($run_lab))
        //     {
        //         $labs = $row_lab[0];
        //         if($labs == 0)
        //         {
        //             $labs = 'N/A';
        //         }
        //         else
        //         {
        //             $total_lab = $total_lab + $labs;
        //         }
        //     }
        // }
        // else
        // {
        //     $labs = 'N/A';
        // }
// if($br_id == 10)
// {
//         $select_medicine = "SELECT SUM(`cash_received`) FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1 AND branch_id = '$br_id' AND `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT id FROM `item_register_to_branches` WHERE item_id IN (SELECT id FROM items WHERE category_id IN (1, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 21, 22, 23, 24, 25, 26, 27) )))";
//         $run_medicine = mysqli_query($con, $select_medicine);
//         if(mysqli_num_rows($run_medicine) > 0)
//         {
//             while($row_medicine = mysqli_fetch_array($run_medicine))
//             {
//                 $medicines = $row_medicine[0];
//                 if($medicines == 0)
//                 {
//                     $medicines = 'N/A';
//                 }
//                 else
//                 {
//                     $total_medicine = $total_medicine + $medicines;
//                 }
//             }
//         }
//         else
//         {
//             $medicines = 'N/A';
//         }
// }        
        // $procedures = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE id NOT IN (473, 1119, 1314, 472, 1118, 1313) AND category_id = 3))"));
        // $total_procedure = $total_procedure + $procedures;
        // $svds = mysqli_num_rows(mysqli_query($con, "SELECT `token_no` FROM `branch_pending_details` WHERE branch_id = '$br_id' AND status = '1' AND token_no IN (SELECT `tokan_no` FROM item_by_doctor WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 2 AND `item_id` IN (SELECT `id` FROM item_register_to_branches WHERE item_id IN (472, 1118, 1313) ) )"));
        // $total_svd = $total_svd + $svds;
        // $dncs = mysqli_num_rows(mysqli_query($con, "SELECT `token_no` FROM `branch_pending_details` WHERE branch_id = '$br_id' AND status = '1' AND token_no IN (SELECT `tokan_no` FROM item_by_doctor WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 2 AND `item_id` IN (SELECT `id` FROM item_register_to_branches WHERE item_id IN (473, 1119, 1314) ) )"));
        // $total_dnc = $total_dnc + $dncs;
        // $procedures = mysqli_num_rows(mysqli_query($con, "SELECT `token_no` FROM `branch_pending_details` WHERE branch_id = '$br_id' AND status = '1' AND token_no IN (SELECT `tokan_no` FROM item_by_doctor WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 2 AND `item_id` IN (SELECT `id` FROM item_register_to_branches WHERE item_id IN (SELECT id FROM items WHERE category_id = '3') ) )"));
        // $total_procedure = $total_procedure + $procedures;
                
        $doctor_name = get_uname_by_id($doctor);
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td style = "text-align: left;">'.$doctor_name.'</td>
                <td>'.$opds.'</td>
                <td>'.$cons_opds.'</td>';
                echo '<td>'.$usgs.'</td>
                <td>'.$collections.'</td>
            </tr>';
    }
    echo '</tbody>';
}
?>
</table>

</body>
</html>