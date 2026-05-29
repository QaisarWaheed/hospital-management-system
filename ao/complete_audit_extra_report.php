<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
if(!isset($_SESSION['ao_id']) && $_SESSION['ao_id'] != 1)
{
    header('location: logout.php');
}
?>
	<title>COMPLETE AUDIT EXTRA MEDICINE REPORT - <?php echo $company_trademark; ?></title>
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
                    <th>Audit</th>
                    <th>Branch</th>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Computer Quantity</th>
                    <th>Manual Quantity</th>
                    <th>Extra Quantity</th>
                    <th>Total Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $s = 0;
                $total_extra = 0;
                $select = "SELECT `audit_id`,branchs.tag_name,items.name,categories.name,`item_poor_price`,`computer_quantity`,`manual_quantity`, `manual_quantity`-`computer_quantity` AS differnce_quantity, (item_poor_price*(`manual_quantity`-`computer_quantity`)) AS differnce_amount FROM `audit_branch_detail` INNER JOIN item_register_to_branches ON audit_branch_detail.branch_item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN branchs ON item_register_to_branches.branch_id = branchs.id INNER JOIN categories ON items.category_id = categories.id WHERE `computer_quantity` < `manual_quantity` ORDER BY `items`.`name` ASC ";
                $run = mysqli_query($con, $select);
                if(mysqli_num_rows($run) > 0)
                {
                    while($row = mysqli_fetch_array($run))
                    {
                        $s = $s + 1;
                        $total_extra = $total_extra + $row['8'];
                        echo '
                        <tr>
                            <td>'.$s.'</td>
                            <td>'.$row['0'].'</td>
                            <td>'.$row['1'].'</td>
                            <td>'.$row['2'].' - '.$row['3'].'</td>
                            <td>'.$row['4'].'</td>
                            <td>'.$row['5'].'</td>
                            <td>'.$row['6'].'</td>
                            <td>'.$row['7'].'</td>
                            <td>'.number_format((float)($row['8'] ?? 0)).'</td>
                        </tr>
                        ';  
                    }
                }
                echo '
                    <tr>
                        <th colspan = "8"></th>
                        <th>'.number_format((float)($total_extra ?? 0)).'</th>
                    </tr>';
                ?>
            </tbody>
            <caption style = "caption-side: top;color: black;text-align: center;"> 
                <h2>COMPLETE AUDIT EXTRA MEDICINE REPORT - <?php echo $company_trademark; ?></h2>
                <h3><?php echo date("d-F-Y"); ?></h3>
            </caption>
        </table>
    </div>
</div>

</body>
</html>
<?php mysqli_close($con); ?>