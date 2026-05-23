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
    <title>GYNAE PROGRESS MONTH <?php echo ycdo_safe_date_format($date.'-01', 'F Y', $date); ?><?php echo get_branch_tag_name_by_id($br_id); ?></title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS MONTH <?php echo ycdo_safe_date_format($date.'-01', 'F Y', $date); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>CONS</th>
            <th>SVD & DNC</th>
            <th>PROCEDURES</th>
            <th>GYNAE TOKEN</th>
            <th>GYNAE FILES</th>
            <th>REFERED PATIENTS</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$count_opd = 0;
$count_consultant_opd = 0;
$count_gynae = 0;
$count_gynae_system = 0;
$count_svd_dnc = 0;
$count_procedure = 0;
$total_reffered = 0;
$select_dr = "SELECT * FROM users WHERE ( role_id = '3' AND id IN (SELECT `doctor_id` FROM `tokans` WHERE `branch_id` = '$br_id' AND created LIKE '$date%') AND id IN (SELECT DISTINCT `doctor_id` FROM `item_by_doctor` WHERE `branch_id` = '$br_id' AND created LIKE '$date%' AND item_id IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (483, 1159, 1321, 1414) )) ) OR (id IN (SELECT from_user_id FROM `referral_patients` WHERE referral_patient_created LIKE '$date%' AND branch_id = '$br_id' and referral_patient_status > 1 ) ) ORDER BY `u_name` ";
$run_dr = mysqli_query($con, $select_dr);
if ($run_dr && mysqli_num_rows($run_dr) > 0)
{
    while($row_dr = mysqli_fetch_array($run_dr))
    {
        $dr_id = $row_dr['id'];
        $dr_name = $row_dr['u_name'];
        $opd = 0;
        $consultant_opd = 0;
        $gynae_count = 0;
        $gynae_count_system = 0;
        $svd_dnc_count = 0;
        $procedure = 0;
        $reffered = 0;

        $select_opd = "SELECT COUNT(id) FROM tokans WHERE doctor_id = '$dr_id' AND created LIKE '$date%' AND branch_id = '$br_id' AND tokan_type_id <= 10 AND status = 1 ";
        $run_opd = mysqli_query($con, $select_opd);
        $opds = mysqli_num_rows($run_opd);
        if($opds == 1)
        {
            while($row_opd = mysqli_fetch_array($run_opd))
            {
                $opd = $row_opd[0];
                $count_opd = $count_opd + $opd;
            }
        }
        $select_consultant_opd = "SELECT COUNT(id) FROM `tokans` WHERE `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$date%' AND status = 1 AND doctor_id = '$dr_id') AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = '29') ))";
        // $select_consultant_opd = "SELECT COUNT(`tokan_no`) FROM `item_by_doctor` WHERE `item_id` IN (SELECT `id` FROM item_register_to_branches WHERE item_id IN (489, 849, 850, 1139, 1140, 1141, 1412, 1415)) AND branch_id = '$br_id' AND `tokan_no` IN (SELECT id FROM tokans WHERE created LIKE '$date%' AND branch_id = '$br_id' AND doctor_id = '$dr_id')";
        $run_consultant_opd = mysqli_query($con, $select_consultant_opd);
        $consultant_opds = mysqli_num_rows($run_consultant_opd);
        if($consultant_opds == 1)
        {
            while($row_consultant_opd = mysqli_fetch_array($run_consultant_opd))
            {
                $consultant_opd = $row_consultant_opd[0];
                $count_consultant_opd = $count_consultant_opd + $consultant_opd;
            }
        }
        
        $select_gynae = "SELECT COUNT(id) FROM tokans WHERE status = 1 AND doctor_id = '$dr_id' AND created LIKE '$date%' AND branch_id = '$br_id' AND id IN (SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE item_id IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (483, 1159, 1321, 1414) ) AND `doctor_id` = '$dr_id' AND `branch_id` = '$br_id' AND `created` LIKE '$date%')";
        $run_gynae = mysqli_query($con, $select_gynae);
        $gynaes = mysqli_num_rows($run_gynae);
        if($gynaes > 0)
        {
            while($row_gynae = mysqli_fetch_array($run_gynae))
            {
                $gynae_count = $row_gynae[0];
                $count_gynae = $count_gynae + $gynae_count;
            }
        }
        
        $gynae_system = "SELECT count(*) FROM `gynae_register` WHERE doctor_id = '$dr_id' AND created like '$date%' AND branch_id = '$br_id'";
        $run_gynae_system = mysqli_query($con, $gynae_system);
        $gynaes_system = mysqli_num_rows($run_gynae_system);
        if($gynaes_system > 0)
        {
            while($row_gynae_system = mysqli_fetch_array($run_gynae_system))
            {
                $gynae_count_system = $row_gynae_system[0];
                $count_gynae_system = $count_gynae_system + $gynae_count_system;
            }
        }

        $select_svd_dnc = "SELECT COUNT(id) FROM tokans WHERE status = 1 AND doctor_id = '$dr_id' AND created LIKE '$date%' AND branch_id = '$br_id' AND id IN (SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE item_id IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (472, 473, 1118, 1119, 1313, 1314) ) AND `doctor_id` = '$dr_id' AND `branch_id` = '$br_id' AND `created` LIKE '$date%')";
        $run_svd_dnc = mysqli_query($con, $select_svd_dnc);
        $svd_dncs = mysqli_num_rows($run_svd_dnc);
        if($svd_dncs > 0)
        {
            while($row_svd_dnc = mysqli_fetch_array($run_svd_dnc))
            {
                $svd_dnc_count = $row_svd_dnc[0];
                $count_svd_dnc = $count_svd_dnc + $svd_dnc_count;
            }
        }
        
        $select_procedure = "SELECT COUNT(id) FROM `tokans` WHERE `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$date%' AND status = 1 AND doctor_id = '$dr_id') AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = '3') ))";
        $run_procedure = mysqli_query($con, $select_procedure);
        $procedures = mysqli_num_rows($run_procedure);
        if($procedures == 1)
        {
            while($row_procedure = mysqli_fetch_array($run_procedure))
            {
                $procedure = $row_procedure[0];
                $count_procedure = $count_procedure + $procedure;
            }
        }

        $reffered = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `referral_patients` WHERE referral_patient_created LIKE '$date%' AND from_user_id = '$dr_id' and referral_patient_status > 1 "));
        $total_reffered = $total_reffered + $reffered;

        
        $s++;
        echo '
        <tr style = "text-align: center;">
            <td>'.$s.'</td>
            <td style = "text-align: left;">'.$dr_name.'</td>
            <td>'.$opd.'</td>
            <td>'.$consultant_opd.'</td>
            <td>'.$svd_dnc_count.'</td>
            <td>'.$procedure.'</td>
            <td>'.$gynae_count.'</td>
            <td>'.$gynae_count_system.'</td>
            <td>'.$reffered.'</td>
        </tr>
        ';
    }
}
?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan = "2">TOTAL</th>
            <th><?php echo $count_opd; ?></th>
            <th><?php echo $count_consultant_opd; ?></th>
            <th><?php echo $count_svd_dnc; ?></th>
            <th><?php echo $count_procedure; ?></th>
            <th><?php echo $count_gynae; ?></th>
            <th><?php echo $count_gynae_system; ?></th>
            <th><?php echo $total_reffered; ?></th>
        </tr>
    </tfoot>
</table>
</body>
</html>
<?php mysqli_close($con); ?>