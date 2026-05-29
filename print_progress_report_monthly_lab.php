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
    <title>PRINT MONTHLY LAB PROGRESS REPORT</title>
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
            <th rowspan = "2">S#</th>
            <th rowspan = "2">NAME</th>
            <th rowspan = "2">OPD</th>
            <th rowspan = "2">CONS</th>
            <th colspan = "3">LAB</th>
            <th rowspan = "2">USG</th>
            <th rowspan = "2">COLLECTION</th>
        </tr>
        <tr>
            <th>Diag. Pt.</th>
            <th>%</th>
            <th>AMOUNT</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$count_opd = 0;
$count_consultant_opd = 0;
$select_dr = "SELECT * FROM users WHERE role_id = '3' AND id IN (SELECT `doctor_id` FROM `tokans` WHERE `branch_id` = '$br_id' AND created LIKE '$date%') ORDER BY `u_name` ";
$run_dr = mysqli_query($con, $select_dr);
if(mysqli_num_rows($run_dr) > 0)
{
    while($row_dr = mysqli_fetch_array($run_dr))
    {
        $dr_id = $row_dr['id'];
        $dr_name = $row_dr['u_name'];

        $select_total = "SELECT SUM(cash_received) AS cash FROM tokans WHERE doctor_id = '$dr_id' AND created LIKE '$date%' AND branch_id = '$br_id' AND status = 1 ";
        $run_total = mysqli_query($con, $select_total);
        $totals = mysqli_num_rows($run_total);
        if($totals == 1)
        {
            while($row_total = mysqli_fetch_array($run_total))
            {
                $total = $row_total['cash'];
                $count_total = $count_total + $total;
            }
        }

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
        
        $select_lab = "SELECT SUM(cash) AS cash, COUNT(cash) AS total_labs FROM tokans WHERE status = 1 AND doctor_id = '$dr_id' AND created LIKE '$date%' AND branch_id = '$br_id' AND id IN (SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE item_id IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 2)) AND `doctor_id` = '$dr_id' AND `branch_id` = '$br_id' AND `created` LIKE '$date%')";
        $run_lab = mysqli_query($con, $select_lab);
        $labs = mysqli_num_rows($run_lab);
        if($labs > 0)
        {
            while($row_lab = mysqli_fetch_array($run_lab))
            {
                $lab_cash = $row_lab['cash'];
                $total_labs = $row_lab['total_labs'];
                $count_lab = $count_lab + $lab_cash;
                $count_total_lab = $count_total_lab + $total_labs;
                if(empty($total_labs))
                {
                    $total_labs = 0;
                }
                $total_ops = $opd + $consultant_opd;
                if($total_labs == 0 || $total_ops == 0)
                {
                    $per_lab = 0;
                }
                else
                {
                    $per_lab = number_format((float)(($total_labs/$total_ops)*100 ?? 0),2);
                }
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
            <td>'.$per_lab.'%</td>
            <td style = "text-align: right;">'.number_format((float)($lab_cash ?? 0)).'</td>
            <td style = "text-align: right;">'.number_format((float)($total ?? 0)).'</td>
        </tr>
        ';
        // echo '
        // <tr style = "text-align: center;">
        //     <td>'.$s.'</td>
        //     <td style = "text-align: left;">'.$dr_name.'</td>
        //     <td>'.$opd.'</td>
        //     <td>'.$consultant_opd.'</td>
        //     <td>'.$total_labs.'</td>
        //     <td>'.$per_lab.'%</td>
        //     <td>'.$usg_count.'</td>
        //     <td>'.$svd_dnc_count.'</td>
        //     <td>'.$minor_count.'</td>
        //     <td>'.$major_count.'</td>
        //     <td>'.$admissoin_count.'</td>
        // </tr>
        // ';
    }
}
?>
    </tbody>
    <tfoot>
            <th colspan = "2">TOTAL</th>
            <th><?php echo $count_opd; ?></th>
            <th><?php echo $count_consultant_opd; ?></th>
            <th><?php echo $count_total_lab; ?></th>
            <th></th>
            <th style = "text-align: right;"><?php echo number_format((float)($count_lab ?? 0)); ?></th>
            <th style = "text-align: right;"><?php echo number_format((float)($count_total ?? 0)); ?></th>
        </tr>
        
    </tfoot>
</table>
</body>
</html>



