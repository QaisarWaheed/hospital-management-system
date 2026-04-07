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
    <title>PRINT PROGRESS REPORT</title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h3>PROGRESS DATE <?php echo date_format(date_create($date), " d F Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>BRANCH</th>
            <th>OPD</th>
            <th>CONS</th>
            <th>LAB</th>
            <th>USG</th>
            <th>SVD</th>
            <th>D&C</th>
            <th>PROCEDURE</th>
            <th>ADMISSION</th>
            <th>GYNAE TOKEN</th>
            <th>GYNAE SYSTEM</th>
        </tr>
    </thead>
<?php
$s = 0; 
$select = "SELECT DISTINCT `branch_id` FROM `tokans` WHERE created like '$date%' ORDER BY `branch_id` ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    echo '<tbody>';
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $select_branch_id = $row['branch_id'];
        $select_branch_tag_name = get_branch_tag_name_by_id($select_branch_id);
        
        $opds = mysqli_num_rows(mysqli_query($con, "SELECT id FROM tokans WHERE `tokan_type_id` < 9 AND status = 1 AND created like '$date%' AND `branch_id` = '$select_branch_id' "));
        $total_opds = $total_opds + $opds;

        $cons_opds = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$date%' AND status = 1 AND branch_id = '$select_branch_id') AND branch_id = '$select_branch_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE branch_id = '$select_branch_id' AND `item_id` IN (489, 849, 850, 1415, 1327, 1139, 1141, 1477, 1154) )"));
        $total_cons_opds = $total_cons_opds + $cons_opds;

        $admissions = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$date%' AND status = 1 AND branch_id = '$select_branch_id') AND branch_id = '$select_branch_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE branch_id = '$select_branch_id' AND `item_id` IN (444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301) )"));
        $total_admission = $total_admission + $admissions;

        $procedures = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$date%' AND status = 1 AND branch_id = '$select_branch_id') AND branch_id = '$select_branch_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE branch_id = '$select_branch_id' AND `item_id` IN (SELECT id FROM items WHERE category_id = '3') )"));
        $total_procedure = $total_procedure + $procedures;

        $svds = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$date%' AND status = 1 AND branch_id = '$select_branch_id') AND branch_id = '$select_branch_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE branch_id = '$select_branch_id' AND `item_id` IN (472, 1118, 1313) )"));
        $total_svd = $total_svd + $svds;

        $dncs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$date%' AND status = 1 AND branch_id = '$select_branch_id') AND branch_id = '$select_branch_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE branch_id = '$select_branch_id' AND `item_id` IN (473, 1119, 1314) )"));
        $total_dnc = $total_dnc + $dncs;

        $usgs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$date%' AND status = 1 AND branch_id = '$select_branch_id') AND branch_id = '$select_branch_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE branch_id = '$select_branch_id' AND `item_id` IN (476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435) )"));
        $total_usg = $total_usg + $usgs;

        $gynae = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$date%' AND status = 1 AND branch_id = '$select_branch_id') AND branch_id = '$select_branch_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE branch_id = '$select_branch_id' AND `item_id` IN (483, 1159, 1321, 1414, 1576) )"));
        $total_gynae = $total_gynae + $gynae;

        // $gynae_system = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `gynae_register` WHERE AND created like '$date%' AND branch_id = '$select_branch_id'"));
        // $total_gynae_system = $total_gynae_system + $gynae_system;
        
        // $select_lab = "SELECT SUM(`cash_received`) FROM tokans WHERE AND created like '$date%' AND status = 1 AND branch_id = '$select_branch_id' AND `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE branch_id = '$select_branch_id' AND `item_id` IN (SELECT id FROM `item_register_to_branches` WHERE branch_id = '$select_branch_id' AND item_id IN (SELECT id FROM items WHERE category_id = 2)))";
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

        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td style = "text-align: left;">'.$select_branch_tag_name.'</td>
                <td>'.$opds.'</td>
                <td>'.$cons_opds.'</td>
                <td>'.$labs.'</td>';
                if($br_id == 10){ echo '<td>'.$medicines.'</td>'; }
                echo '<td>'.$usgs.'</td>
                <td>'.$svds.'</td>
                <td>'.$dncs.'</td>
                <td>'.$procedures.'</td>
                <td>'.$admissions.'</td>
                <td>'.$gynae.'</td>
                <td>'.$gynae_system.'</td>
            </tr>';
    }
    echo '</tbody>';
    echo '<tfoot>
            <tr style = "text-align: right;">
                <th></th>
                <th></th>
                <th>'.$total_opds.'</th>
                <th>'.$total_cons_opds.'</th>
                <th>'.$total_lab.'</th>';
                if($br_id == 10){ echo '<th>'.$total_medicines.'</th>'; }
                echo '<th>'.$total_usg.'</th>
                <th>'.$total_svd.'</th>
                <th>'.$total_dnc.'</th>
                <th>'.$total_procedure.'</th>
                <th>'.$total_admission.'</th>
                <th>'.$total_gynae.'</th>
                <th>'.$total_gynae_system.'</th>
            </tr>
        </tfoor>';
}
?>
</table>

</body>
</html>
<?php mysqli_close($con); ?>