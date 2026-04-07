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
    <title><?php echo get_branch_name_by($br_id); ?> <?php echo date_format(date_create($date), " d F Y"); ?> OTHER SERIVES PROGRESS REPORT</title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3> OTHER SERIVES PROGRESS DATE <?php echo date_format(date_create($date), " d F Y"); ?></h3>
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
$select = "SELECT DISTINCT `doctor_id` FROM `tokans` WHERE (`tokan_type_id` < 9 OR doctor_id IN (SELECT DISTINCT `doctor_id` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$date%' AND status = '1') AND branch_id = '$br_id' AND `status` = '2' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (1639, 483, 1159, 1321, 1576, 476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435, 444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301, 1579, 1580) )) ) AND doctor_id IN (SELECT `id` FROM `users` WHERE `branch_id` = '$br_id') AND created like '$date%' AND `branch_id` = '$br_id' ORDER BY `doctor_id` ";
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

        $admissions = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301, 1579, 1580) )"));
        $total_admission = $total_admission + $admissions;

        $reffered = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `referral_patients` WHERE referral_patient_created LIKE '$date%' AND from_user_id = '$doctor' AND referral_patient_status > '1' "));
        $total_reffered = $total_reffered + $reffered;

        $usgs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435))"));
        $total_usg = $total_usg + $usgs;

        $gynae_system = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `gynae_register` WHERE doctor_id = '$doctor' AND created like '$date%' AND branch_id = '$br_id'"));
        $total_gynae_system = $total_gynae_system + $gynae_system;

        $doctor_name = get_uname_by_id($doctor);
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td style = "text-align: left;">'.$doctor_name.'</td>
                <td>'.$opds.'</td>
                <td>'.$admissions.'</td>';
                echo '<td>'.$usgs.'</td>';
                echo '<td>'.$gynae_system.'</td>';
                echo '<td>'.$reffered.'</td>';
                echo '
            </tr>';
    }
    echo '</tbody>';
    echo '<tfoot>
            <tr style = "text-align: right;">
                <th></th>
                <th></th>
                <th>'.$total_opds.'</th>
                <th>'.$total_admission.'</th>';
                echo '<th>'.$total_usg.'</th>';
                echo '<th>'.$total_gynae_system.'</th>';
                echo '<th>'.$total_reffered.'</th>';
                echo '
            </tr>
        </tfoor>';
}
?>
</table>

</body>
</html>
<?php mysqli_close($con); ?>