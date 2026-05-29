<?php include 'includes/connect.php';

if(isset($_GET['audit_lab_store_form_id']) && $_GET['audit_lab_store_form_id'] != '')
{
    $audit_lab_store_form_id = $_GET['audit_lab_store_form_id'];
}

if(isset($_GET['clear_id']))
{
    $quantity = $_GET['quantity'];
    $audit_lab_store_form_id = $_GET['audit_lab_store_form_id'];
    $item_id = $_GET['item_id'];
    $clear_id = $_GET['clear_id'];
    $update = "UPDATE `audit_lab_store_detail` SET `audit_lab_store_detail_clear_by` = '$user_id', `audit_lab_store_detail_clear_at` = '$current_date' WHERE `audit_lab_store_detail_id` = '$clear_id' AND `audit_lab_store_detail_clear_by` = '0' AND `audit_lab_store_form_id` = '$audit_lab_store_form_id' ";
    mysqli_query($con, $update);
    if(mysqli_affected_rows($con) == '1')
    {
        $update_item = "UPDATE `items` SET `quantity` = `quantity`+$quantity WHERE `id` = '$item_id' ";
        mysqli_query($con, $update_item);
    }
    header('Location: audit_lab_store_clear.php?audit_lab_store_form_id='.$audit_lab_store_form_id);
    exit;
}

include 'includes/head.php';
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
	<title>AUDIT LAB STORE CLEARANCE - <?php echo $company_trademark; ?></title>
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
                    <th>Audit Id</th>
                    <th>Item id</th>
                    <th>Item Name</th>
                    <th>MANUAL</th>
                    <th>COMPUTER</th>
                    <th>Extra Quantity</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $s = 0;
                $total_short = 0;
                $select = "SELECT audit_lab_store_detail.audit_lab_store_detail_id AS audit_lab_store_detail_id,categories.name AS cat_name,items.name AS name, items.id AS item_id, audit_lab_store_detail.audit_lab_store_detail_created_at AS created, audit_lab_store_detail.poor AS poor, audit_lab_store_detail.computer_stock AS computer_stock, audit_lab_store_detail.manual_stock AS manual_stock, audit_lab_store_detail.manual_tries AS manual_tries, (audit_lab_store_detail.computer_stock-audit_lab_store_detail.manual_stock) AS differance_quantity FROM `audit_lab_store_detail` INNER JOIN items ON audit_lab_store_detail.item_id = items.id INNER JOIN categories ON items.category_id = categories.id WHERE audit_lab_store_form_id = '$audit_lab_store_form_id' AND computer_stock != manual_stock AND audit_lab_store_detail.audit_lab_store_detail_clear_by = '0' ";
                $run = mysqli_query($con, $select);
                if(mysqli_num_rows($run) > 0)
                {
                    while($row = mysqli_fetch_array($run))
                    {
                        $audit_created = $row['created'];
                        $audit_lab_store_detail_id = $row['audit_lab_store_detail_id'];
                        $item_id = $row['item_id'];
                        $name = $row['name'];
                        $cat_name = $row['cat_name'];
                        $poor = $row['poor'];
                        $computer_stock = $row['computer_stock'];
                        $manual_stock = $row['manual_stock'];
                        $manual_tries = $row['manual_tries'];
                        $difference = $manual_stock-$computer_stock; 
                        $s = $s + 1;
                        echo '
                        <tr>
                            <td>'.$s.'</td>
                            <td>'.$audit_lab_store_detail_id.'</td>
                            <td>'.$item_id.'</td>
                            <td>'.$name.' - '.$cat_name.'</td>
                            <td>'.$manual_stock.'</td>
                            <td>'.$computer_stock.'</td>
                            <td>'.$difference.'</td>
                            <td>
                                <a href = "audit_lab_store_clear.php?clear_id='.$audit_lab_store_detail_id.'&quantity='.$difference.'&item_id='.$item_id.'&audit_lab_store_form_id='.$audit_lab_store_form_id.'">CLEAR</a>
                            </td>
                        </tr>
                        ';    
                    }
                }
                ?>
            </tbody>
            <caption style = "caption-side: top;color: black;text-align: center;"> 
                <h2>AUDIT LAB DIFFERANCE REPORT - YCDO</h2>
                <h3><?php echo $audit_branch_name; ?></h3>
                <h3><?php echo date_format(date_create($audit_created), "d-F-Y"); ?></h3>
            </caption>
        </table>
    </div>
</div>

</body>
</html>
<?php mysqli_close($con); ?>