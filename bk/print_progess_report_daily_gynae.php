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
    <title>PRINT PROGRESS GYNAE DATE <?php echo date_format(date_create($date), " d F Y"); ?></title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS DATE <?php echo date_format(date_create($date), "d F Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th rowspan = "2">S#</th>
            <th rowspan = "2">NAME</th>
            <th rowspan = "2">OPD</th>
            <th rowspan = "2">USG</th>
            <th colspan = "2">GYNAE REGISTRATION</th>
            <th colspan = "3">REFERRAL SYSTEM</th>
        </tr>
        <tr>
            <th>TOKEN</th>
            <th>SYSTEM</th>
            <th>PATIENT</th>
            <th>COMPLETE</th>
            <th>REJECT</th>
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
$select = "SELECT DISTINCT `doctor_id` FROM `tokans` WHERE doctor_id IN (SELECT `id` FROM `users` WHERE `branch_id` = '$br_id') AND created like '$date%' AND `branch_id` = '$br_id' AND ( doctor_id IN (SELECT DISTINCT `doctor_id` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (483, 1159, 1321, 1414, 476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435)) ) OR doctor_id IN (SELECT DISTINCT doctor_id FROM `gynae_register` WHERE created like '$date%' AND branch_id = '$br_id') OR (doctor_id IN (SELECT DISTINCT from_user_id FROM `referral_patients` WHERE referral_patient_created LIKE '$date%' AND branch_id = '$br_id') ) ) ORDER BY `doctor_id` ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    echo '<tbody>';
    while($row = mysqli_fetch_array($run))
    {
        $gynae_file_tokens = '';
        $gynae_file_token = '';
        $s = $s + 1;
        $doctor = $row['doctor_id'];

        $opds = mysqli_num_rows(mysqli_query($con, "SELECT id FROM tokans WHERE `tokan_type_id` < 9 AND status = 1 and doctor_id = '$doctor' AND created like '$date%' AND `branch_id` = '$br_id' "));
        $total_opds = $total_opds + $opds;

        $gynae = mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (483, 1159, 1321, 1414, 1576)) ORDER BY `tokan_no` ");
        $gynae_count = mysqli_num_rows($gynae);
        if(mysqli_num_rows($gynae) > 0)
        {
            while($row_gynae_token = mysqli_fetch_array($gynae))
            {
                $gynae_file_token .= $row_gynae_token['tokan_no']."</br>";
            }
        }
        $total_gynae = $total_gynae + $gynae_count;

        $gynae_system = mysqli_query($con, "SELECT * FROM `gynae_register` WHERE doctor_id = '$doctor' AND created like '$date%' AND branch_id = '$br_id' ORDER BY `token_no`");
        $gynae_system_count = mysqli_num_rows($gynae_system);
        if(mysqli_num_rows($gynae_system) > 0)
        {
            while($row_gynae_file = mysqli_fetch_array($gynae_system))
            {
                $gynae_file_tokens .= $row_gynae_file['1']."</br>";
            }
        }
        $total_gynae_system = $total_gynae_system + $gynae_system_count;
        
        $reffered = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `referral_patients` WHERE referral_patient_created LIKE '$date%' AND from_user_id = '$doctor' "));
        $total_reffered = $total_reffered + $reffered;
        
        $reffered_successfull = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `referral_patients` WHERE referral_patient_created LIKE '$date%' AND from_user_id = '$doctor' AND referral_patient_status > '1' "));
        $total_reffered_successfull = $total_reffered_successfull + $reffered_successfull;

        $rejected = $reffered - $reffered_successfull;
        $rejected_total = $rejected + $rejected_total;

        $usgs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435))"));
        $total_usg = $total_usg + $usgs;

        // $svds = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (472, 1118, 1313) )"));
        // $total_svd = $total_svd + $svds;

        // $dncs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (473, 1119, 1314) )"));
        // $total_dnc = $total_dnc + $dncs;

        // $procedures = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE id NOT IN (473, 1119, 1314, 472, 1118, 1313) AND category_id = 3))"));
        // $total_procedure = $total_procedure + $procedures;
        
        $doctor_name = get_uname_by_id($doctor);
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td style = "text-align: left;">'.$doctor_name.'</td>
                <td>'.$opds.'</td>';
                echo '<td>'.$usgs.'</td>';
                echo '<td>'.$gynae_count.' - '.$gynae_file_token.'</td>';
                echo '<td>'.$gynae_system_count.' - '.$gynae_file_tokens.'</td>';
                echo '<td>'.$reffered.'</td>';
                echo '<td>'.$reffered_successfull.'</td>';
                echo '<td>'.$rejected.'</td>';
                echo '
            </tr>';
    }
    echo '</tbody>';
    echo '<tfoot>
            <tr style = "text-align: right;">
                <th></th>
                <th></th>
                <th>'.$total_opds.'</th>';
                echo '<th>'.$total_usg.'</th>';
                echo '<th>'.$total_gynae.'</th>';
                echo '<th>'.$total_gynae_system.'</th>';
                echo '<th>'.$total_reffered.'</th>';
                echo '<th>'.$total_reffered_successfull.'</th>';
                echo '<th>'.$rejected_total.'</th>';
                echo '
            </tr>
        </tfoor>';
}
?>
</table>

</body>
</html>
<?php mysqli_close($con); ?>