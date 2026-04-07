<?php 
include 'includes/connect.php'; 
if(isset($_GET['date']))
{
    $date = $_GET['date'];
    $br_id = $_GET['br_id'];
}
elseif(isset($_POST['date']))
{
    $date = $_POST['date'];
    $br_id = $_POST['br_id'];
}
else
{
    exit(0);
}
?>
<html>
<head>
    <title><?php echo get_branch_name_by($br_id); ?> <?php echo date_format(date_create($date), " d F Y"); ?> PROCEDURE PROGRESS REPORT</title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3> OPD & PROCEDURE PROGRESS DATE <?php echo date_format(date_create($date), " d F Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>DOCTOR NAME</th>
            <th>TOTAL PATIENT</th>
            <th>CONSULTANT</th>
            <th>SVD & DNC</th>
            <th>PROCEDURE</th>
            <!--<th>TOTAL LAB AMOUNT</th>-->
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
$select = "SELECT DISTINCT `doctor_id` FROM `tokans` WHERE (`tokan_type_id` < 9 OR doctor_id IN (SELECT DISTINCT `doctor_id` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$date%' AND status = '1') AND branch_id = '$br_id' AND `status` = '2' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (489, 849, 850, 1415, 1327, 1139, 1141, 1477, 1154) OR `item_id` IN (SELECT id FROM items WHERE category_id = '3'))) ) AND doctor_id IN (SELECT `id` FROM `users` WHERE `branch_id` = '$br_id') AND created like '$date%' AND `branch_id` = '$br_id' ORDER BY `doctor_id` ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    echo '<tbody>';
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $doctor = $row['doctor_id'];

        $opds = mysqli_num_rows(mysqli_query($con, "SELECT id FROM tokans WHERE `tokan_type_id` < 9 AND status = 1 and doctor_id = '$doctor' AND created like '$date%' AND `branch_id` = '$br_id' "));
        $total_opds = $total_opds + $opds;

        $cons_opds = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (489, 849, 850, 1415, 1327, 1139, 1141, 1477, 1154) )"));
        $total_cons_opds = $total_cons_opds + $cons_opds;

        // $admissions = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301, 1579, 1580) )"));
        // $total_admission = $total_admission + $admissions;

        $svds = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (472, 1118, 1313, 473, 1119, 1314, 1577, 1578) )"));
        $total_svd = $total_svd + $svds;

        // $reffered = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `referral_patients` WHERE referral_patient_created LIKE '$date%' AND from_user_id = '$doctor' "));
        // $total_reffered = $total_reffered + $reffered;

        // $dncs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (473, 1119, 1314) )"));
        // $total_dnc = $total_dnc + $dncs;

        // $usgs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435))"));
        // $total_usg = $total_usg + $usgs;

        // $gynae = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (483, 1159, 1321, 1414))"));
        // $total_gynae = $total_gynae + $gynae;

        // $gynae_system = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `gynae_register` WHERE doctor_id = '$doctor' AND created like '$date%' AND branch_id = '$br_id'"));
        // $total_gynae_system = $total_gynae_system + $gynae_system;

        // $count_lab = mysqli_num_rows(mysqli_query($con, "SELECT `cash_received` FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1 AND branch_id = '$br_id' AND `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT id FROM `item_register_to_branches` WHERE item_id IN (SELECT id FROM items WHERE category_id = 2)))"));
        // $total_count_lab = $total_count_lab + $count_lab;
        
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

        // $procedures = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE id NOT IN (473, 1119, 1314, 472, 1118, 1313, 1577, 1578) AND category_id = 3))"));
        $procedures = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = '3'))"));
        $total_procedure = $total_procedure + $procedures;
        
        $doctor_name = get_uname_by_id($doctor);
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td style = "text-align: left;">'.$doctor_name.'</td>
                <td>'.$opds.'</td>';
                echo '<td>'.$cons_opds.'</td>';
                echo '<td>'.$svds.'</td>';
                echo '<td>'.$procedures.'</td>';
                echo '
            </tr>';
    }
    echo '</tbody>';
    echo '<tfoot>
            <tr style = "text-align: right;">
                <th></th>
                <th></th>
                <th>'.$total_opds.'</th></th>';
                echo '<th>'.$total_cons_opds.'</th>';
                echo '<th>'.$total_svd.'</th>';
                echo '<th>'.$total_procedure.'</th>';
                echo '
            </tr>
        </tfoor>';
}
?>
</table>

</body>
</html>
<?php mysqli_close($con); ?>