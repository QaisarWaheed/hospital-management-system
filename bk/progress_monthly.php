<?php 
include 'includes/connect.php'; 
if(isset($_GET['date']))
{
    $date = $_GET['date'];
    $br_id = $_GET['br_id'];
}
else
{
    $date = '2025-01';
    $br_id = 18;
}
?>
<html>
<head>
    <title><?php echo get_branch_tag_by($br_id)." ";echo date_format(date_create($date), "m-Y"); ?> MONTHLY PROGRESS REPORT </title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS MONTH <?php echo date_format(date_create($date), " F Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>CONS</th>
            <th>LAB</th>
            <th>USG</th>
            <th>SVD & DNC</th>
            <th>PROCEDURE</th>
            <th>ADMISSION</th>
            <th>COLLECTION</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$count_opd = 0;
$count_consultant_opd = 0;
$select_dr = "SELECT id, u_name FROM users WHERE status = '1' AND role_id = '3' AND id IN (SELECT `doctor_id` FROM `tokans` WHERE `branch_id` = '$br_id' AND created LIKE '$date%') ORDER BY `u_name` ";
// $select_dr = "SELECT * FROM users WHERE `branch_id` = '$br_id' AND status = '1' AND role_id = '3' AND id IN (SELECT `doctor_id` FROM `tokans` WHERE `branch_id` = '$br_id' AND created LIKE '$date%') ORDER BY `u_name` ";
$run_dr = mysqli_query($con, $select_dr);
if(mysqli_num_rows($run_dr) > 0)
{
    while($row_dr = mysqli_fetch_array($run_dr))
    {
        $dr_id = $row_dr['id'];
        $dr_name = $row_dr['u_name'];

        $select_opd = "SELECT COUNT(CASE WHEN tokans.tokan_type_id < 10 THEN 1 END) AS opd, SUM(`cash`) AS cash FROM `tokans` WHERE tokans.doctor_id = '$dr_id' AND tokans.created LIKE '$date%' AND tokans.branch_id = '$br_id' AND tokans.status = '1' ";
        $run_opd = mysqli_query($con, $select_opd);
        $opds = mysqli_num_rows($run_opd);
        if($opds == 1)
        {
            while($row_opd = mysqli_fetch_array($run_opd))
            {
                $opd = $row_opd['opd'];
                $count_opd = $count_opd + $opd;
                $total = $row_opd['cash'];
                $count_total = $count_total + $total;
            }
        }
        
        $select_admissoin = "SELECT COUNT(CASE WHEN items.category_id = 29 THEN 1 END) AS cons, COUNT(CASE WHEN items.category_id = 2 THEN 1 END) AS labs, COUNT(CASE WHEN items.id IN (476, 477, 478,1318, 1184, 1317, 1163, 1161, 1162, 1435, 1411) THEN 1 END) AS usgs, COUNT(CASE WHEN items.id IN (472, 473, 1118, 1119, 1313, 1314, 1575, 1577, 1578) THEN 1 END) AS svds, COUNT(CASE WHEN items.category_id = 3 THEN 1 END) AS procedures, COUNT(CASE WHEN items.id IN (444, 448, 452, 456, 460, 945, 1124, 1125, 1128, 1145, 1285, 1289, 1293, 1297, 1301, 1579, 1580, 1741, 1742, 1743, 1744) THEN 1 END) AS addmissions FROM `tokans` INNER JOIN item_by_doctor ON tokans.id = item_by_doctor.tokan_no INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id WHERE tokans.doctor_id = '$dr_id' AND tokans.created LIKE '$date%' AND tokans.branch_id = '$br_id' AND tokans.status = '1' ";
        $run_admissoin = mysqli_query($con, $select_admissoin);
        $admissoins = mysqli_num_rows($run_admissoin);
        if($admissoins > 0)
        {
            while($row_admissoin = mysqli_fetch_array($run_admissoin))
            {
                $consultant_opd = $row_admissoin['cons'];
                $count_consultant_opd = $count_consultant_opd + $consultant_opd;
                $total_labs = $row_admissoin['labs'];
                $count_total_lab = $count_total_lab + $total_labs;
                $usg_count = $row_admissoin['usgs'];
                $count_usg = $count_usg + $usg_count;
                $svd_dnc_count = $row_admissoin['svds'];
                $count_svd_dnc = $count_svd_dnc + $svd_dnc_count;
                $major_count = $row_admissoin['procedures'];
                $count_major = $count_major + $major_count;
                $admissoin_count = $row_admissoin['addmissions'];
                $count_admissoin = $count_admissoin + $admissoin_count;
            }
        }
  
        $s++;
        echo '
        <tr style = "text-align: center;">
            <td>'.$s.'</td>
            <td style = "text-align: left;">'.$dr_name.'</td>
            <td>'.$opd.'</td>
            <td>'.$consultant_opd.'</td>
            <td>'.$total_labs.'</td>
            <td>'.$usg_count.'</td>
            <td>'.$svd_dnc_count.'</td>
            <td>'.$major_count.'</td>
            <td>'.$admissoin_count.'</td>
            <td style = "text-align: right;">'.number_format((float)($total ?? 0)).'</td>
        </tr>
        ';
    }
}
?>
    </tbody>
    <tfoot>
            <th colspan = "2">TOTAL</th>
            <th><?php echo $count_opd; ?></th>
            <th><?php echo $count_consultant_opd; ?></th>
            <th><?php echo $count_total_lab; ?></th>
            <th><?php echo $count_usg; ?></th>
            <th><?php echo $count_svd_dnc; ?></th>
            <th><?php echo $count_major; ?></th>
            <th><?php echo $count_admissoin; ?></th>
            <th style = "text-align: right;"><?php echo number_format((float)($count_total ?? 0)); ?></th>
        </tr>
        
    </tfoot>
</table>
</body>
</html>
<?php mysqli_close($con); ?>