<?php include 'includes/connect.php'; 
if (isset($_GET['up_id'])) 
{
    $up_id = $_GET['up_id'];
    $bill_no = $_GET['bill_no'];
    $quantity = $_GET['quantity'];
    $old = $_GET['old'];
    $quantity_old = get_register_item_quantity_from_item_id($old); 
    $update_quantity_old = $quantity_old - $quantity;
    $update_old = "UPDATE item_register_to_branches SET quantity = $update_quantity_old WHERE id = '$old' ";
    mysqli_query($con, $update_old);
    
    $new = $_GET['new'];
    $quantity_new = get_register_item_quantity_from_item_id($new);
    $update_quantity_new = $quantity_new + $quantity;
    $update_new = "UPDATE item_register_to_branches SET quantity = $update_quantity_new WHERE id = '$new' ";
    mysqli_query($con, $update_new);
    
    $update_item_id = "UPDATE `item_register_branchs_by_sm` SET `branch_item_id` = '$new' WHERE id = '$up_id' ";
    mysqli_query($con, $update_item_id);
    
    echo $update_old;
    echo "</br>";
    echo $update_new;
    echo "</br>";
    echo $update_item_id;
    header('location: item_register_branch_update.php?bill_no='.$bill_no);
exit(0);
}
$current_issue_no = mysqli_num_rows(mysqli_query($con, "SELECT DISTINCT issue_id FROM `item_register_branchs_by_sm`"));
?>
<?php include 'includes/head.php'; ?>
	<title>Add Item - <?php echo $company_trademark; ?></title>
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
				<label><h1 style = "text-decoration: underline;">Item Issue LIST</h1></label>
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
				                <th>Sr #</th>
				                <th></th>
				                <th>Br Item Id</th>
				                <th>Br Id</th>
				                <th>Item Id</th>
				                <th>Id</th>
				                <th>Action</th>
				                <th>Item Name </th>
				                <th>Category </th>
				                <th>QTY</th>
				                <th>Status</th>
				                <th class = "noprint">Action</th>
				            </tr>
				        </thead>
				        <tboby>
<?php
$s = 0;
$total_amount = 0;
if(isset($_GET['bill_no']) && $_GET['bill_no'] != '')
{
    $bill_no = $_GET['bill_no'];
    $select = "SELECT * FROM `item_register_branchs_by_sm` WHERE issue_id = '$bill_no' ";
}
else
{
    $select = "SELECT * FROM `item_register_branchs_by_sm` WHERE issue_id = '$current_issue_no' ";
    
}
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $id = $row['id'];
        $status = $row['status'];
        if($status == 1){$status_msg = "Issued";}else{$status_msg = "RECEIVED";}
        $quantity = $row['quantity'];
        $difference = $row['difference'];
        $branch_item_id = $row['branch_item_id'];
        $get_br_id = get_branch_id_by_register_item_id($branch_item_id);
        
        $get_item_id = get_items_id_store_by_register_item($branch_item_id);
        
        $get_br_item_id = get_branch_item_id_by_item($get_item_id, $branch_id);
        
        $bran_id = $row['branch_id'];
        $created = $row['created'];
        $item_name = get_item_name_by_register_item_id($branch_item_id);
        $category_name = show_category_name_by_register_branch_id($branch_item_id);
        echo '				            
        <tr  style = "font-size: 15px;">
            <td style = "text-align: center;">'.$s.'</td>
            <td style = "text-align: center;">'.$id.'</td>
            <td style = "text-align: center;">'.$branch_item_id.'</td>
            <td style = "text-align: center;">'.$get_br_id.'</td>
            <td style = "text-align: center;">'.$get_item_id.'</td>
            <td style = "text-align: center;">'.$get_br_item_id.'</td>';
        if($branch_item_id != $get_br_item_id)
        {
        echo '<td><a href="item_register_branch_update.php?up_id='.$id.'&old='.$branch_item_id.'&new='.$get_br_item_id.'&quantity='.$quantity.'&bill_no='.$bill_no.'" class="btn btn-success btn-sm">'.$branch_item_id.' = '.$get_br_item_id.'</a></td>';
        }
        else
        {
        echo '<td>OK</td>';
        }       
        
            echo '<td style = "text-align: left">'.$item_name.'</td>
            <td style = "text-align: center">'.$category_name.'</td>
            <td style = "text-align: right;">'.$quantity.'</td>
            <td>
                '.$status_msg.'
            </td>';
        if($status == 1)
        {
        echo '<td class = "noprint"><a href="update_item_register_branch.php?up='.$id.'" class="btn btn-success btn-sm">Update</a></td>';
        }
        else
        {
        echo '<td class = "noprint">OK</td>';
        }
        echo '</tr>
';

                // <a href="add_party.php?u_id='.$id.'" class = "btn btn-sm btn-primary">update</a>
    }
}
    $tag_name = get_branch_name_by($bran_id);
    $receiver_name = get_uname_by_issue_id($bill_no);
?>
				        </tboby>
    <caption style = "caption-side: top;">
        <div style = "font-size:15px;">BRANCH NAME: <?php echo $tag_name; ?></div>
        <div style = "font-size:15px;">Issue No: <?php echo $bill_no; ?></div>
        <div style = "font-size:15px;">Issue Date: <?php echo date_format(date_create($created), "d F Y"); ?></div>
        <div style = "font-size:15px;">RECEIVED BY: <?php if($receiver_name != ''){echo $receiver_name;}else{echo 'NOT RECEIVED YEET';}  ?></div>
    </caption>    
				    </table>
				</div>
			</div>


		</div>

	</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>