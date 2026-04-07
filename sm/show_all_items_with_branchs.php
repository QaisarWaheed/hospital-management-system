<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
if(isset($_GET['update_store']) && $_GET['id'] != '')
{
    $id = $_GET['id'];
    $update = "UPDATE `items` SET `min_limit` = '".$_GET['store_min_limit']."', `max_limit` = '".$_GET['store_max_limit']."', `updated_at` = '$current_date', `updated_by` = '$user_id' WHERE `status` = '1' AND `id` = '".$_GET['id']."' ";
    if(mysqli_query($con, $update))
    {
        mysqli_query($con, "INSERT INTO `update_quires`(`update_query_id`, `update_query`, `user_id`, `created`) VALUES(NULL, '$update', '$user_id', '$current_date')" );
        header('location: show_all_items_with_branchs.php?id='.$id.'&msg=update_limits');
        exit(0);
    }
}

?>
	<title>Show Branch Stock - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>			
			<div class="col-md-12" style="text-align: center;">
			</div>
		<table class="table table-bordered table-hover">
			<thead>
				<caption style="caption-side: top;text-align: center;">
					<h3>SHOW ITEMS</h3>
				</caption>
				<tr class="select_category">
					<th colspan="2">
					    <a class = "btn btn-success" href = "dashboard.php">Dashboard</a>
					    <a class = "btn btn-info active" href = "show_all_items.php">Show All Item</a>
				    </th>
				</tr>
                <tr>
                    <th colspan = "7">
                        <h2 align = "center">STORE STOCK DETAIL</h2>
                    </th>
                <tr>
				<tr>
					<th colspan = "2">Item Name</th>
					<th>Category</th>
					<th>Quantity</th>
					<th>Min Limit</th>
					<th>Max Limit</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
<?php
if(isset($_GET['id']) && $_GET['id'] != '')
{
    $id = $_GET['id'];
}
else
{
    header('location: dashboard.php');
    exit(0);
}
$s = 0;
$select = mysqli_query($con, "SELECT * FROM items WHERE id = '$id' ");
if (mysqli_num_rows($select) > 0) 
{
	while ($row = mysqli_fetch_array($select)) 
	{
		$id = $row['id'];
		$item_name = $row['name'];
		$category_name = show_category_name($row['category_id']);
		$quantity = $row['quantity'];
		$min_limit = $row['min_limit'];
		$max_limit = $row['max_limit'];
		if($quantity > $max_limit){$status = '<span style="color:green;">Over Stock</span>';}
		elseif($quantity <= $min_limit){$status = '<span style="color:red;">'.intval($max_limit-$quantity).'</span>';}
		else{$status = "OK";}
echo '
<tr>
	<td colspan = "2">'.$item_name.'</td>
	<td>'.$category_name.'</td>
	<td>'.$quantity.'</td>
	<td>
	    '.$min_limit.'
	</td>
	<td>
	    '.$max_limit.'
	</td>
	<td>
	    '.$status.' <br>
	</td>
</tr>
<form>
<tr>
	<td colspan = "4"></td>
	<td>
	    <input type = "hidden" value = "'.$id.'" name = "id"  />
	    <input type = "number" value = "'.$min_limit.'" name = "store_min_limit" class = "form-control" />
	</td>
	<td>
	    <input type = "number" value = "'.$max_limit.'" name = "store_max_limit" class = "form-control" />
	</td>
	<td>
	    <input type = "submit" value = "UPDATE" name = "update_store" class = "btn btn-sm btn-success" />
	</td>
</tr>
</form>
';
	}
}
?>
			</tbody>
<?php
$select_br = "SELECT * FROM `item_register_to_branches` WHERE item_id = '$id' AND status = '1' ";
$run_br = mysqli_query($con, $select_br);
if(mysqli_num_rows($run_br) > 0)
{?>
                <tr>
                    <th colspan = "7">
                        <h2 align = "center">BRANCHS STOCK DETAIL</h2>
                    </th>
                <tr>
				<tr>
					<th>S #</th>
					<th>Branch Name</th>
					<th>Select Quantity</th>
					<th>Quantity</th>
					<th>Min Limit</th>
					<th>Max Limit</th>
					<th>Status</th>
				</tr>
<?php 
    while($row_br = mysqli_fetch_array($run_br))
    {
        $s++;
		$branch_register_id = $row_br['id'];
		$br_id = $row_br['branch_id'];
		$br_name = get_branch_name_by($br_id);
		$br_quantity = $row_br['quantity'];
		$br_min_limit = $row_br['min_limit'];
		$br_max_limit = $row_br['max_limit'];
		if($br_quantity > $br_max_limit){$br_status = '<span style="color:green;">Over Stock</span>';}
		elseif($br_quantity <= $br_min_limit){$br_status = '<span style="color:red;">'.intval($br_max_limit-$br_quantity).'</span>';}
		else{$br_status = "OK";}
		echo '
				<tr>
					<td>'.$s.'</td>
					<td>'.$br_name.'</td>
					<td>
					    <form>
					        <input type = "hidden" name = "item_id" value = "'.$id.'" />
					        <input type = "number" name = "quantity" />
					    </form>
					</td>
					<td>'.$br_quantity.'</td>
					<td>'.$br_min_limit.'</td>
					<td>'.$br_max_limit.'</td>
					<td>'.$br_status.'</td>
				</tr>
		';
   }
}
?>
		</table>

	</div>
</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<!-- 
 -->