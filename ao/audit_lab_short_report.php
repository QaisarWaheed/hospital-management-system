<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php';
if(isset($_GET['audit_id']) && $_GET['audit_id'] != '')
{
    $audit_id = $_GET['audit_id'];
    $br_id = $_GET['br_id'];
    $audit_branch_name = get_branch_name_by($br_id);
}
else
{
    // header('location: logout.php');
}
?>
	<title>AUDIT DIFERENCE REPORT - <?php echo $company_trademark; ?></title>
</head>

<body>

<div class = "row">
    		    
			<div class="col-md-12" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>
    <div class = "col-md-12">
        <table  class = "table table-bordered table-hover">
            <thead>
                <tr>
                    <th>S #</th>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Short Quantity</th>
                    <th>Short Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $s = 0;
                $total_extra = 0;
                $total_short = 0;
                $select = "SELECT * FROM `audit_branch_detail` WHERE audit_id = '$audit_id' ";
                $run = mysqli_query($con, $select);
                if(mysqli_num_rows($run) > 0)
                {
                    while($row = mysqli_fetch_array($run))
                    {
                        $audit_created = $row['created'];
                        $branch_item_id = $row['branch_item_id'];
                        $item_name = get_item_name_by_register_item_id($branch_item_id);
                        $category_name = show_category_name_by_register_branch_id($branch_item_id);
                        $item_poor_price = $row['item_poor_price'];
                        $computer_quantity = $row['computer_quantity'];
                        $manual_quantity = $row['manual_quantity'];
                        $tries = $row['tries'];
                        $difference = $computer_quantity - $manual_quantity; 
                        if($computer_quantity > $manual_quantity)
                        {
                            $s = $s + 1;
                            $total_short = $total_short + abs($item_poor_price*$difference);
                        echo '
                        <tr>
                            <td>'.$s.'</td>
                            <td>'.$item_name.' - '.$category_name.'</td>
                            <td>'.$item_poor_price.'</td>
                            <td>'.$difference.'</td>
                            <td>'.abs($item_poor_price*$difference).'</td>
                        </tr>
                        ';    
                        }
                    }
                }
                echo '
                    <tr>
                        <th colspan = "4"></th>
                        <th>'.$total_short.'</th>
                    </tr>
                ';
                ?>
            </tbody>
            <caption style = "caption-side: top;color: black;text-align: center;"> 
                <h2>AUDIT SHORT REPORT - YCDO</h2>
                <h3><?php echo $audit_branch_name; ?></h3>
                <h3><?php echo date_format(date_create($audit_created), "d-F-Y"); ?></h3>
            </caption>
        </table>
    </div>
</div>

</body>
</html>
<?php mysqli_close($con); ?>