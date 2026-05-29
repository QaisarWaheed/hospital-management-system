<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
include 'includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';

$req = progress_report_resolve_request($con);
$date = $req['date'];
$br_id = $req['br_id'];
$like = $req['like'];
?>
<html>
<head>
    <title>PRINT PHARMACY PROGRESS REPORT <?php echo get_branch_tag_by($br_id); echo date_format(date_create($date), " F Y"); ?></title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PHARMACY PROGRESS <?php echo date_format(date_create($date), " F Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>ID</th>
            <th>NAME</th>
            <th>PURCHASE</th>
            <th>SALE </th>
            <th>PROFIT </th>
            <th>INCENTIVE</th>
        </tr>
    </thead>
<?php
$s = 0; 
$total_sale = 0;
$total_incentive = 0;
$total_purchase = 0;
$ibd_date_clause = progress_sql_date_clause($con, $like, 'item_by_doctor.created');
$select = "SELECT DISTINCT item_by_doctor.user_id, users.u_name, SUM(item_by_doctor.sale_quantity*item_by_doctor.sale_price_poor)AS sale_poor, SUM(item_by_doctor.sale_quantity*item_by_doctor.purchase_price)AS purchase_price FROM `item_by_doctor` INNER JOIN users ON item_by_doctor.user_id = users.id INNER JOIN categories ON item_by_doctor.category_id = categories.id AND categories.is_medicine = '1' WHERE item_by_doctor.branch_id = '$br_id' AND item_by_doctor.status = '2' AND $ibd_date_clause GROUP BY item_by_doctor.user_id ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    echo '<tbody>';
    while($row = mysqli_fetch_array($run))
    {
        $incentive = 0;
        $s = $s + 1;
        $sale = $row['sale_poor'];
        $total_sale = $total_sale + $sale;
        $purchase = $row['purchase_price'];
        $total_purchase = $total_purchase + $purchase;
        if($sale > 100000)
        {
            $incentive = $incentive + 1000;
            if($sale > 200000)
            {
                $incentive = $incentive + 2000;
                if($sale >= 300000)
                {
                    $incentive = $incentive +  (($sale-200000)*0.03);
                }
            }
            else
            {
                $incentive = $incentive +  (($sale-100000)*0.02);
            }
        }
        else
        {
            $incentive = $incentive + $sale*0.01;
        }
        
        $total_incentive = $total_incentive + $incentive;
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td>'.$row['user_id'].'</td>
                <td style = "text-align: left;">'.$row['u_name'].'</td>';
                echo '<td>'.number_format((float)(intval($purchase ?? 0) ?? 0)).'</td>';
                echo '<td>'.number_format((float)(intval($sale ?? 0) ?? 0)).'</td>';
                echo '<td>'.number_format((float)(intval($sale-$purchase) ?? 0)).'</td>';
                echo '<td>'.number_format((float)(intval($incentive ?? 0) ?? 0)).'</td>';
                echo '
            </tr>';
    }
    echo '</tbody>';
    echo '<tfoot>
            <tr style = "text-align: right;">
                <th colspan = "3"></th>';
                echo '<th>'.number_format((float)(intval($total_purchase ?? 0) ?? 0)).'</th>';
                echo '<th>'.number_format((float)(intval($total_sale ?? 0) ?? 0)).'</th>';
                echo '<th>'.number_format((float)(intval($total_sale-$total_purchase) ?? 0)).'</th>';
                echo '<th>'.number_format((float)(intval($total_incentive ?? 0) ?? 0)).'</th>';
                echo '
            </tr>
        </tfoor>';
}
?>
</table>
<small>
    <span style = "color: red;">NOTE:</span>1% Till One Lac, 2% More then One Lac and Less then Two Lac & 3% More Then Two Lac
</small>
</body>
</html>
<?php mysqli_close($con); ?>