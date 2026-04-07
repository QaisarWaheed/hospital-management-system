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
            <th>ID</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>CONS</th>
            <th>LAB</th>
            <th>USG</th>
            <th>SVD / D&C</th>
            <th>PROCEDURE</th>
            <th>SKIN & EYE</th>
            <th>ADMISSION</th>
            <th>GYNAE TOKEN</th>
            <th>GYNAE SYSTEM</th>
            <th>REFERREL BY</th>
            <th>REFERREL TO</th>
        </tr>
    </thead>
<?php
$s = 0; 
$labs = 0;
$medicines = 0;
$total_admission = 0;
$total_procedure = 0;
$total_gynae_system = 0;
$total_dnc = 0;
$total_svd = 0;
$total_usg = 0;
$total_lab = 0;
$total_medicine = 0;
$total_opds = 0;
$total_cons_opds = 0;
$total_gynae = 0;
$select = "SELECT DISTINCT `doctor_id` FROM `tokans` WHERE doctor_id IN (SELECT `id` FROM `users` WHERE `branch_id` = '$br_id') AND created like '$date%' AND `branch_id` = '$br_id' ORDER BY `doctor_id` ";
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

        $cons_opds = mysqli_num_rows(mysqli_query($con, "SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = '1') AND branch_id = '$br_id' AND `status` = '2' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = '29'))"));
        $total_cons_opds = $total_cons_opds + $cons_opds;

        $usgs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435))"));
        $total_usg = $total_usg + $usgs;

        $svds = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (472, 1118, 1313, 473, 1119, 1314, 1577, 1578) )"));
        $total_svd = $total_svd + $svds;

        $procedures = mysqli_num_rows(mysqli_query($con, "SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = '1') AND branch_id = '$br_id' AND `status` = '2' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE id NOT IN (473, 1119, 1314, 472, 1118, 1313, 1577, 1578) AND category_id = '3'))"));
        $total_procedure = $total_procedure + $procedures;

        $skins = mysqli_num_rows(mysqli_query($con, "SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = '1') AND branch_id = '$br_id' AND `status` = '2' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id IN (32, 33)))"));
        $total_skin = $total_skin + $skins;

        $admissions = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301, 1579, 1580, 1741, 1742, 1743, 1744) )"));
        $total_admission = $total_admission + $admissions;

        $gynae = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (483, 1159, 1321, 1414))"));
        $total_gynae = $total_gynae + $gynae;

        $gynae_system = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `gynae_register` WHERE doctor_id = '$doctor' AND created like '$date%' AND branch_id = '$br_id'"));
        $total_gynae_system = $total_gynae_system + $gynae_system;
        
        $refered = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `referral_patients` WHERE `from_user_id` = '$doctor' AND `referral_patient_status` > '1' AND `referral_patient_created` like '$date%' "));
        $total_refered = $total_refered + $refered;
        
        $refered_to = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `referral_patients` WHERE `to_user_id` = '$doctor' AND `referral_patient_status` > '1' AND `referral_patient_created` like '$date%' "));
        $total_refered_to = $total_refered_to + $refered_to;
        
        // $labs = 0;
        // $select_lab = "SELECT SUM(`cash_received`) FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1 AND branch_id = '$br_id' AND `id` IN (SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT id FROM `item_register_to_branches` WHERE item_id IN (SELECT id FROM items WHERE category_id = 2)))";
        // $run_lab = mysqli_query($con, $select_lab);
        // if(mysqli_num_rows($run_lab) > 0)
        // {
        //     while($row_lab = mysqli_fetch_array($run_lab))
        //     {
        //         $labs = $row_lab[0];
        //     }
        // }
        // $total_lab = $total_lab + $labs;

        $doctor_name = get_uname_by_id($doctor);
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td>'.$doctor.'</td>
                <td style = "text-align: left;">'.$doctor_name.'</td>
                <td>'.$opds.'</td>
                <td>'.$cons_opds.'</td>
                <td>'.$labs.'</td>';
                echo '<td>'.$usgs.'</td>
                <td>'.$svds.'</td>';
                echo '<td>'.$procedures.'</td>
                <td>'.$skins.'</td>
                <td>'.$admissions.'</td>
                <td>'.$gynae.'</td>
                <td>'.$gynae_system.'</td>
                <td>'.$refered.'</td>
                <td>'.$refered_to.'</td>
            </tr>';
    }
    echo '</tbody>';
    echo '<tfoot>
            <tr style = "text-align: right;">
                <th colspan = "3"></th>
                <th>'.$total_opds.'</th>
                <th>'.$total_cons_opds.'</th>
                <th>'.$total_lab.'</th>';
                echo '<th>'.$total_usg.'</th>
                <th>'.$total_svd.'</th>';
                echo '<th>'.$total_procedure.'</th>
                <th>'.$total_skin.'</th>
                <th>'.$total_admission.'</th>
                <th>'.$total_gynae.'</th>
                <th>'.$total_gynae_system.'</th>
                <td>'.$total_refered.'</td>
                <td>'.$total_refered_to.'</td>
            </tr>
        </tfoor>';
}
?>
</table>

</body>
</html>