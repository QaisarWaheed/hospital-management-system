<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php';
if(isset($_GET['audit_store_form_id']) && $_GET['audit_store_form_id'] != '')
{
    $audit_store_form_id = $_GET['audit_store_form_id'];
}
else
{
    // header('location: logout.php');
}
?>
<style>
@media print 
{
.noprint
{
    display: none;
}
}
</style>
	<title>AUDIT EXTRA MEDICINE REPORT - <?php echo $company_trademark; ?></title>
</head>

<body>

<div class = "row">
    
    <div class = "col-md-12 noprint">
        <?php include 'top_row.php'; ?>
    </div>
    <div class = "col-md-12">
        <table  class = "table table-bordered table-hover">
            <thead>
                <tr>
                    <th>S #</th>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>MANUAL</th>
                    <th>COMPUTER</th>
                    <th>Extra Quantity</th>
                    <th>Extra Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $s = 0;
                $total_extra = 0;
                $total_short = 0;
                $select = "SELECT categories.name AS cat_name,items.name AS name, audit_store_detail.audit_store_detail_created AS created, audit_store_detail.poor AS poor, audit_store_detail.computer_stock AS computer_stock, audit_store_detail.manual_stock AS manual_stock, audit_store_detail.manual_tries AS manual_tries FROM `audit_store_detail` INNER JOIN items ON audit_store_detail.item_id = items.id INNER JOIN categories ON items.category_id = categories.id WHERE audit_store_form_id = '$audit_store_form_id' AND computer_stock < manual_stock ";
                $run = mysqli_query($con, $select);
                if(mysqli_num_rows($run) > 0)
                {
                    while($row = mysqli_fetch_array($run))
                    {
                        $audit_created = $row['created'];
                        $name = $row['name'];
                        $cat_name = $row['cat_name'];
                        $poor = $row['poor'];
                        $computer_stock = $row['computer_stock'];
                        $manual_stock = $row['manual_stock'];
                        $manual_tries = $row['manual_tries'];
                        $difference = $manual_stock-$computer_stock; 
                        $s = $s + 1;
                        $total_extra = $total_extra + abs($poor*$difference);
                        echo '
                        <tr>
                            <td>'.$s.'</td>
                            <td>'.$name.' - '.$cat_name.'</td>
                            <td>'.$poor.'</td>
                            <td>'.$manual_stock.'</td>
                            <td>'.$computer_stock.'</td>
                            <td>'.$difference.'</td>
                            <td>'.number_format((float)(abs($poor*$difference) ?? 0)).'</td>
                        </tr>
                        ';    
                    }
                }
                echo '
                    <tr>
                        <th colspan = "6"></th>
                        <th style = "font-size:23px;">'.number_format((float)($total_extra ?? 0)).'</th>
                    </tr>
                ';
                ?>
            </tbody>
            <caption style = "caption-side: top;color: black;text-align: center;"> 
                <h2>AUDIT EXTRA MEDICINE REPORT - YCDO</h2>
                <h3><?php echo $audit_branch_name; ?></h3>
                <h3><?php echo date_format(date_create($audit_created), "d-F-Y"); ?></h3>
            </caption>
        </table>
        <div style = "font-size: 24px;text-align: center;color: black;">
            <p>EXTRA MEDICINE AMOUNT: <strong> <?php echo number_format((float)($total_extra ?? 0)); ?></strong></p>
        </div>
    </div>
</div>

</body>
</html>
<?php mysqli_close($con); ?>