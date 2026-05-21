<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php';
if(isset($_GET['audit_id']) && $_GET['audit_id'] != '')
{
    $audit_id = $_GET['audit_id'];
    $br_id = $_GET['br_id'];
}
else
{
    // header('location: logout.php');
}
?>
	<title>AUDIT DIFERENCE REPORT - <?php echo $company_trademark; ?></title>
<style>
@page 
{
  size: A4;
  margin: 10px 0px 10px 0px;
}
@media print 
{
html, body 
{
    width: 210mm;
    height: 297mm;
    font-size: 9px;
}
.noprint
{
    display: none;
}
}    
</style>
</head>
<body>

<div class = "row">
    <div class = "col-md-12 noprint">
        <?php include 'top_row.php'; ?>
    </div>
    <div class = "col-md-12">
        <table  class = "table table-bordered table-hover">
            <caption style = "caption-side: top;text-align: center;color: black;">
                <h3><?php echo get_branch_name_by($br_id); ?></h3>
                
            </caption>
            <thead>
                <tr>
                    <th>S #</th>
                    <th>Item Name</th>
                    <th>Price</th>
                    <th>Computer Quantity</th>
                    <th>Manual Quantity</th>
                    <th>Short</th>
                    <th>Extra</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $s = 0;
                $total_extra = 0;
                $total_short = 0;
                $total_difference_amount = 0;
                $select = "SELECT * FROM `audit_branch_detail` WHERE audit_id = '$audit_id' ";
                $run = mysqli_query($con, $select);
                if(mysqli_num_rows($run) > 0)
                {
                    while($row = mysqli_fetch_array($run))
                    {
                        $branch_item_id = $row['branch_item_id'];
                        $item_name = get_item_name_by_register_item_id($branch_item_id);
                        $category_name = show_category_name_by_register_branch_id($branch_item_id);
                        $item_poor_price = $row['item_poor_price'];
                        $computer_quantity = $row['computer_quantity'];
                        $manual_quantity = $row['manual_quantity'];
                        $tries = $row['tries'];
                        $difference = $manual_quantity - $computer_quantity; 
                        $s = $s + 1;
                            if($difference <= 0)
                            {
                                $total_short = $total_short + abs($difference*$item_poor_price);
                            echo '
                            <tr>
                                <td>'.$s.'</td>
                                <td>'.$item_name.' - '.$category_name.'</td>
                                <td>'.$item_poor_price.'</td>
                                <td>'.$computer_quantity.'</td>
                                <td>'.$manual_quantity.'</td>
                                <td>'.$difference.'('.abs($difference*$item_poor_price).')</td>
                                <td></td>
                            </tr>
                            '; 
                            }
                            elseif($difference > 0)
                            {
                                $total_extra = $total_extra + ($difference*$item_poor_price);
                            echo '
                            <tr>
                                <td>'.$s.'</td>
                                <td>'.$item_name.' - '.$category_name.'</td>
                                <td>'.$item_poor_price.'</td>
                                <td>'.$computer_quantity.'</td>
                                <td>'.$manual_quantity.'</td>
                                <td></td>
                                <td>'.$difference.'('.$difference*$item_poor_price.')</td>
                            </tr>
                            '; 
                            }
                    }
                }
                echo '
                    <tr>
                        <th colspan = "4"></th>
                        <th></th>
                        <th>'.$total_short.'</th>
                        <th>'.$total_extra.'</th>
                    </tr>
                ';
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
<?php mysqli_close($con); ?>