<?php include 'includes/connect.php'; 
if (isset($_POST['id'])) 
{
    $id = $_POST['id'];
    $receive_quantity = get_receive_quantity_from_branch($id);
    $quantity = $_POST['quantity'];
    $branch_item_id = $_POST['branch_item_id'];
    
    $item_id = $_POST['item_id'];
    $quantity_store = get_item_quantity_by_item_id($item_id);
    
    $update_branch = "UPDATE item_register_to_branches SET quantity = quantity-$quantity WHERE id = '$branch_item_id' ";
    $update_store = "UPDATE items SET quantity = quantity+$quantity WHERE id = '$item_id' ";
    $update_database = "UPDATE `return_item_by_branch` SET `mm`= '$user_id', status = 2 WHERE `id` = '$id' AND status = '1' ";
    if ((float) $receive_quantity === (float) $quantity && $quantity > 0) {
        mysqli_query($con, $update_database);
        if (mysqli_affected_rows($con) >= 1) {
            mysqli_query($con, $update_branch);
            mysqli_query($con, $update_store);
        } else {
            echo '<script>alert("Return row already processed or not found.");location.replace("item_receive_from_branch.php");</script>';
            exit;
        }
    ?>
	<script>
 		location.replace("item_receive_from_branch.php");
 	</script>
<?php
    }
    else
    {
        ?>
	<script>
 		alert('ITEM NOT RECEIVED...');
 		location.replace("item_receive_from_branch.php");
 	</script>
<?php    }
exit(0);
}
$distinct_returns = mysqli_query($con, "SELECT COUNT(DISTINCT return_no) AS cnt FROM `return_item_by_branch`");
$current_issue_no = 1;
if ($distinct_returns && ($cnt_row = mysqli_fetch_assoc($distinct_returns))) {
    $current_issue_no = max(1, (int) $cnt_row['cnt']);
}
$current_purchase_no = $current_issue_no;
?>
<?php include 'includes/head.php'; ?>
	<title>Receive Item From Branch - <?php echo $company_trademark; ?></title>
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

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

		<div class="row">

			<div class="col-md-12" style="text-align: center;">
				<label><h1 style = "text-decoration: underline;">Item Return From Branch LIST</h1></label>
				<div class = "table">
				    <table class = "table" border = "1">
				        <thead>
				            <tr class = "noprint">
				                <form method = "GET">
				                    <td colspan = "4"><input required type = "number" min = "1" value = "<?php echo ($_GET['bill_no']>0 ? $_GET['bill_no'] : $current_purchase_no); ?>" max = "<?php echo $current_purchase_no; ?>" name = "bill_no" class = "form-control" /></td>
				                    <td><input type = "submit" value = "SEARCH" class = "btn btn-sm btn-info" /></td>
				                </form>
				            </tr>
				            <tr style = "font-size: 16px;">
				                <th>S #</th>
				                <th>Return No</th>
				                <th>Return Date</th>
				                <th>Branch Name</th>
				                <th>Item Name</th>
				                <th>Expiry Date</th>
				                <th>Quantity</th>
				                <th>Status</th>
				            </tr>
				        </thead>
				        <tboby>
<?php
$s = 0;
$total_amount = 0;
if(isset($_GET['bill_no']) && $_GET['bill_no'] != '')
{
    $bill_no = $_GET['bill_no'];
    $select = "SELECT * FROM `return_item_by_branch` WHERE return_no = '$bill_no' ";
}
else
{
    $select = "SELECT * FROM `return_item_by_branch` WHERE status = 1 ";
    
}
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $id = $row['id'];
        $return_no = $row['return_no'];
        $return_quantity = $row['quantity'];
        $expire_date = $row['expire_date'];
        $status = $row['status'];
        $mm = $row['mm'];
        if($status == 1){$status_msg = "NOT RECEIVED";}else{$status_msg = "RECEIVED";}
        $branch_item_id = $row['branch_item_id'];
        $get_br_id = get_branch_id_by_register_item_id($branch_item_id);
        $get_item_id = get_items_id_store_by_register_item($branch_item_id);
        $br_name = get_branch_name_by($get_br_id);
        $get_br_item_id = get_branch_item_id_by_item($get_item_id, $branch_id);
        $created = $row['created'];
        $item_name = get_item_name_by_register_item_id($branch_item_id);
        $category_title = show_category_name_by_register_branch_id($branch_item_id);
        echo '				            
        <tr  style = "font-size: 15px;">
            <td style = "text-align: center;">'.$s.'</td>
            <td style = "text-align: center;">'.$id.'</td>
            <td style = "text-align: center;">'.date_format(date_create($created), "d-m-Y").'</td>
            <td style = "text-align: center;">'.$br_name.'</td>
            <td style = "text-align: center;">'.$item_name.'('.$category_title.')</td>';
        if($expire_date != '0000-00-00')
        {
        echo '<td style = "text-align: center;">'.date_format(date_create($expire_date), "d-M-Y").'</td>';
        }      
        else{
        echo '<td style = "text-align: center;">'.$return_quantity.'</td>';
        }
        if ($status == 1 && ($mm === null || $mm === '' || $mm === '0' || (int) $mm === 0))
        {
        echo '
            <td>
                <form method = "POST">
                    <input type = "hidden" value = "'.$id.'"  name = "id" class = "form-control" required />
                    <input type = "hidden" value = "'.$get_item_id.'"  name = "item_id" class = "form-control" required />
                    <input type = "hidden" value = "'.$branch_item_id.'" name = "branch_item_id" class = "form-control" required />
                    <input type = "number" value = "'.$return_quantity.'" name = "quantity" class = "form-control" required onchange="this.form.submit()" />
                    <input style = "min-width: 100%;" type = "submit" value = "RECEIVE" class = "btn btn-sm" />
                </form>
            </td>';
        }
        else
        {
        echo '<td style = "text-align: center;">'.$return_quantity.'</td>';
        echo '<td style = "text-align: center;">'.$status_msg.'</td>';
        }
        echo '</tr>';
    }
}
?>
				        </tboby>
				    </table>
				</div>
			</div>


		</div>

	</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>