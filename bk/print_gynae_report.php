<?php 
include 'includes/connect.php'; 
if(isset($_GET['date']))
{
    $date = $_GET['date'];
}
elseif(isset($_POST['date']))
{
    $date = $_POST['date'];
}
else
{
    exit(0);
}
?>
<html>
<head>
    <title>PRINT GYNAE REPORT <?php echo date_format(date_create($date), " d F Y"); ?></title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h3>GYNAE REPORT DATE <?php echo date_format(date_create($date), " d F Y"); ?></h3>
</caption>
<?php
$date_from_start = date_format(date_create($date), "Y-m");
$select_br = "SELECT id, address, tag_name FROM branchs WHERE status = '1' AND id IN (SELECT DISTINCT `branch_id` FROM `item_by_doctor` WHERE created like '$date_from_start%' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (483, 1159, 1321, 1414, 473, 1119, 1314, 472, 1118, 1313))) ";
$run_br = mysqli_query($con,  $select_br);
if (mysqli_num_rows($run_br) > 0) 
{
    while ( $row_br = mysqli_fetch_array($run_br) ) 
    {
        $br_id = $row_br['id'];
        $address = $row_br['address'];?>
        <tr>
            <th colspan = "12"><h2><?php echo $address; ?></h2></th>
        </tr>
        <tr>
            <th>S#</th>
            <th>NAME</th>
            <th>SVD</th>
            <th>TOTAL SVD</th>
            <th>DNC</th>
            <th>TOTAL DNC</th>
            <th>PROCEDURE</th>
            <th>TOTAL PROCEDURE</th>
            <th>GYNAE TOKEN</th>
            <th>TOTAL TOKEN</th>
            <th>GYNAE SYSTEM</th>
            <th>TOTAL SYSTEM</th>
        </tr>
<?php    
$s = 0;
$total_svd = 0;
$total_dnc = 0;
$total_procedure = 0;
$total_gynaes = 0;
$total_gynae_systems = 0;
$total_total_svd = 0;
$total_total_dnc = 0;
$total_total_procedure = 0;
$total_total_gynae = 0;
$total_total_gynae_system = 0;
$select = "SELECT DISTINCT `doctor_id` FROM `tokans` WHERE doctor_id IN (SELECT DISTINCT `doctor_id` FROM `item_by_doctor` WHERE created like '$date_from_start%' AND branch_id = '$br_id' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (483, 1159, 1321, 1414, 473, 1119, 1314, 472, 1118, 1313))) AND doctor_id IN (SELECT `id` FROM `users` WHERE `branch_id` = '$br_id')";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $doctor = $row['doctor_id'];

        $svds = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (472, 1118, 1313) )"));
        $total_svd = $total_svd + $svds;

        $total_svds = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date_from_start%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (472, 1118, 1313) )"));
        $total_total_svd = $total_total_svd + $total_svds;

        $total_dncs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date_from_start%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (473, 1119, 1314) )"));
        $total_total_dnc = $total_total_dnc + $total_dncs;

        $dncs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (473, 1119, 1314) )"));
        $total_dnc = $total_dnc + $dncs;

        $gynaes = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (483, 1159, 1321, 1414))"));
        $total_gynaes = $total_gynaes + $gynaes;

        $total_gynae = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date_from_start%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (483, 1159, 1321, 1414))"));
        $total_total_gynae = $total_total_gynae + $total_gynae;

        $gynae_systems = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `gynae_register` WHERE doctor_id = '$doctor' AND created like '$date%' AND branch_id = '$br_id'"));
        $total_gynae_systems = $total_gynae_systems + $gynae_systems;
            
        $total_gynae_system = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `gynae_register` WHERE doctor_id = '$doctor' AND created like '$date_from_start%' AND branch_id = '$br_id'"));
        $total_total_gynae_system = $total_total_gynae_system + $total_gynae_system;
            
        $procedures = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE id NOT IN (473, 1119, 1314, 472, 1118, 1313) AND category_id = 3))"));
        $total_procedure = $total_procedure + $procedures;
            
        $total_procedures = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date_from_start%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE id NOT IN (473, 1119, 1314, 472, 1118, 1313) AND category_id = 3))"));
        $total_total_procedure = $total_total_procedure + $total_procedures;
        
        $doctor_name = get_uname_by_id($doctor);
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td style = "text-align: left;">'.$doctor_name.'</td>
                <td>'.$svds.'</td>
                <td>'.$total_svds.'</td>
                <td>'.$dncs.'</td>
                <td>'.$total_dncs.'</td>
                <td>'.$procedures.'</td>
                <td>'.$total_procedures.'</td>
                <td>'.$gynaes.'</td>
                <td>'.$total_gynae.'</td>
                <td>'.$gynae_systems.'</td>
                <td>'.$total_gynae_system.'</td>
            </tr>';
    }
    echo '<tr style = "text-align: right;">
                <th></th>
                <th></th>
                <th>'.$total_svd.'</th>
                <th>'.$total_total_svd.'</th>
                <th>'.$total_dnc.'</th>
                <th>'.$total_total_dnc.'</th>
                <th>'.$total_procedure.'</th>
                <th>'.$total_total_procedure.'</th>
                <th>'.$total_gynaes.'</th>
                <th>'.$total_total_gynae.'</th>
                <th>'.$total_gynae_systems.'</th>
                <th>'.$total_total_gynae_system.'</th>
            </tr>';
}
        
    }
}
?>
</table>

</body>
</html>
<?php mysqli_close($con); ?>