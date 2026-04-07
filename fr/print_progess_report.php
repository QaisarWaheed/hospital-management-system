<?php 
include 'includes/connect.php'; 
if(isset($_GET['date']))
{
    $date = $_GET['date'];
    $br_id = $_GET['br_id'];
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
            <th>LAB</th>
        </tr>
    </thead>
<?php
$s = 0; 
$total_admission = 0;
$total_procedure = 0;
$total_dnc = 0;
$total_svd = 0;
$total_usg = 0;
$total_lab = 0;
$total_opds = 0;
$total_cons_opds = 0;
$select = "SELECT DISTINCT `doctor_id` FROM `tokans` WHERE created like '$date%' AND `branch_id` = '$br_id' ORDER BY `doctor_id` ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    echo '<tbody>';
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $doctor = $row['doctor_id'];

        $opds = mysqli_num_rows(mysqli_query($con, "SELECT id FROM tokans WHERE `tokan_type_id` < 9 AND status = 1 and doctor_id = '$doctor' AND created like '$date%' "));
        $total_opds = $total_opds + $opds;

        $cons_opds = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 29) )"));
        $total_cons_opds = $total_cons_opds + $cons_opds;

        // $admissions = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (444, 448, 452, 456, 460, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301) )"));
        // $total_admission = $total_admission + $admissions;

        // $svds = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (472, 1118, 1313) )"));
        // $total_svd = $total_svd + $svds;

        // $dncs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (473, 1119, 1314) )"));
        // $total_dnc = $total_dnc + $dncs;

        // $usgs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (1138, 1185, 476, 477, 478, 1161, 1162, 1163, 1184, 1317, 1318))"));
        // $total_usg = $total_usg + $usgs;

        $select_lab = "SELECT SUM(`cash_received`) FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1 AND branch_id = '$br_id' AND `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT id FROM `item_register_to_branches` WHERE item_id IN (SELECT id FROM items WHERE category_id = 2)))";
        $run_lab = mysqli_query($con, $select_lab);
        if(mysqli_num_rows($run_lab) > 0)
        {
            while($row_lab = mysqli_fetch_array($run_lab))
            {
                $labs = $row_lab[0];
                if($labs == 0)
                {
                    $labs = 'N/A';
                }
                else
                {
                    $total_lab = $total_lab + $labs;
                }
            }
        }
        else
        {
            $labs = 'N/A';
        }
        
        // $procedures = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE id NOT IN (473, 1119, 1314, 472, 1118, 1313) AND category_id = 3))"));
        // $total_procedure = $total_procedure + $procedures;
        
        $doctor_name = get_uname_by_id($doctor);
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td style = "text-align: left;">'.$doctor_name.'</td>
                <td>'.$opds.'</td>
                <td>'.$cons_opds.'</td>
                <td>'.$labs.'</td>
            </tr>';
        // echo ' <tr style = "text-align: right;">
        //         <td>'.$s.'</td>
        //         <td style = "text-align: left;">'.$doctor_name.'</td>
        //         <td>'.$opds.'</td>
        //         <td>'.$cons_opds.'</td>
        //         <td>'.$labs.'</td>
        //         <td>'.$usgs.'</td>
        //         <td>'.$svds.'</td>
        //         <td>'.$dncs.'</td>
        //         <td>'.$procedures.'</td>
        //         <td>'.$admissions.'</td>
        //     </tr>';
    }
    echo '</tbody>';
    echo '<tfoot>
            <tr style = "text-align: right;">
                <th></th>
                <th></th>
                <th>'.$total_opds.'</th>
                <th>'.$total_cons_opds.'</th>
                <th>'.$total_lab.'</th>
            </tr>
        </tfoor>';
    // echo '<tfoot>
    //         <tr style = "text-align: right;">
    //             <th></th>
    //             <th></th>
    //             <th>'.$total_opds.'</th>
    //             <th>'.$total_cons_opds.'</th>
    //             <th>'.$total_lab.'</th>
    //             <th>'.$total_usg.'</th>
    //             <th>'.$total_svd.'</th>
    //             <th>'.$total_dnc.'</th>
    //             <th>'.$total_procedure.'</th>
    //             <th>'.$total_admission.'</th>
    //         </tr>
    //     </tfoor>';
}
?>
</table>

</body>
</html>