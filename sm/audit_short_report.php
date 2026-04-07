<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
if(!isset($_SESSION['sm_id']))
{
    header('location: logout.php');
}
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
                $select = "SELECT *, items.name AS item_name, categories.name AS category_name FROM `audit_branch_detail` INNER JOIN item_register_to_branches ON audit_branch_detail.branch_item_id = item_register_to_branches.id AND item_register_to_branches.branch_id = 1 INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN categories ON items.category_id = categories.id WHERE audit_branch_detail.audit_id = '$audit_id' AND computer_quantity > manual_quantity ";
                $run = mysqli_query($con, $select);
                if(mysqli_num_rows($run) > 0)
                {
                    while($row = mysqli_fetch_array($run))
                    {
                        $s = $s + 1;
                        $branch_item_id = $row['branch_item_id'];
                        $item_poor_price = $row['item_poor_price'];
                        $computer_quantity = $row['computer_quantity'];
                        $manual_quantity = $row['manual_quantity'];
                        $tries = $row['tries'];
                        $difference = $computer_quantity - $manual_quantity; 
                        $total_short = $total_short + abs($item_poor_price*$difference);
                        echo '
                        <tr>
                            <td>'.$s.'</td>
                            <td>'.$row['item_name'].' - '.$row['category_name'].'</td>
                            <td>'.$item_poor_price.'</td>
                            <td>'.$difference.'</td>
                            <td>'.abs($item_poor_price*$difference).'</td>
                        </tr>
                        '; 
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
                <?php
                $select_audit = "SELECT * FROM `audit_branch_form` WHERE `id` = $audit_id ";
                $run_audit = mysqli_query($con, $select_audit);
                if(mysqli_num_rows($run_audit) == 1)
                {
                    while($row_audit = mysqli_fetch_array($run_audit))
                    {
                        $audit_id = $row_audit['id'];
                        $audit_created = $row_audit['created'];
                    }
                } ?>
                <h3><?php echo $audit_branch_name; ?></h3>
                <h3><?php echo date_format(date_create($audit_created), "d-F-Y"); ?></h3>
            </caption>
        </table>
    </div>
</div>

</body>
</html>
<?php mysqli_close($con); ?>