<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
if (isset($_GET['category_idds']) && $_GET['category_idds'] != ''&& $_GET['category_idds'] != '0') 
{
$select_value = $_GET['category_idds'];
$select = mysqli_query($con, "SELECT * FROM items WHERE category_id = '$select_value' ");
}
else
{
$select_value = 0;
$select = mysqli_query($con, "SELECT * FROM items ORDER BY `name` ");
}
?>
	<title>Show Store Stock - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>
			
<?php
if(isset($_POST['update']) && $_POST['update'] != '')
{
	$up_id = $_POST['up_id'];
	$category_id = $_POST['category_id'];
	$status = $_POST['status'];
	if($status == 2)
	{
	    $update_branch_item = "UPDATE `item_register_to_branches` SET status = 2 WHERE item_id = '$up_id' ";
	    mysqli_query($con, $update_branch_item);
	   // echo $update_branch_item;
	}
	$barcode = $_POST['barcode'];
	$purchase = $_POST['purchase'];
	$retail = $_POST['retail'];
	$deserving = $_POST['deserving'];
	$poor = $_POST['poor'];
	$member = $_POST['member'];
	$general = $_POST['general'];
	$min_limit = $_POST['min_limit'];
	$max_limit = $_POST['max_limit'];
	
	$update = "UPDATE items SET
	`barcode` = '$barcode',
	`category_id` = '$category_id',
	`status` = '$status',
	`retail` = '$retail',
	`purchase` = '$purchase',
	`deserving` = '$deserving',
	`poor` = '$poor',
	`member` = '$member',
	`general` = '$general',
	`min_limit` = '$min_limit',
	`max_limit` = '$max_limit'
	WHERE id = '$up_id' ";

    if(mysqli_query($con, $update))
    {
    // echo '<script>alert("Update Item")</script>';
    echo '<script>location.replace("show_items_with_rate.php")</script>';
    }
    // header('location: show_items.php');
    exit(0);
}
elseif(isset($_GET['up']) && $_GET['up'] != '')
{
    $up_id = $_GET['up'];
    $select = mysqli_query($con,"SELECT * FROM items WHERE id = '$up_id' ");
    if (mysqli_num_rows($select) == 1) 
    {
    	while ($row = mysqli_fetch_array($select)) 
    	{
    		$item_status = $row['status'];
    		$name = $row['name'];
    		$barcode = $row['barcode'];
    		$retail = $row['retail'];
    		$purchase = $row['purchase'];
    		$quantity = $row['quantity'];
    		$deserving = $row['deserving'];
    		$poor = $row['poor'];
    		$member = $row['member'];
    		$general = $row['general'];
    		$category_id = $row['category_id'];
    		$min_limit = $row['min_limit'];
    		$max_limit = $row['max_limit'];
    		if($quantity > $max_limit){$status = '<span style="color:green;">Over Stock</span>';}
    		elseif($quantity <= $min_limit){$status = '<span style="color:red;">'.intval($max_limit-$quantity).'</span>';}
    		else{$status = "OK";}
    	}
    }
?>
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Update Item Form</h1></label>
			</div>
			<div class="col-md-12">

				<form name="test" method = "POST" autocomplete="off">

					<div class="row">

						<div class="col-md-2">
							<label>Id</label>
							<input type = "text" name = "up_id" value = "<?php echo $up_id; ?>" class = "form-control" readonly />
						</div>
						<div class="col-md-2">
							<label>Category</label>
							<select name = "category_id" class = "form-control" required>
<?php
    		$categories = mysqli_query($con, "SELECT * FROM categories WHERE status = '1' ");
    		while ($row_category = mysqli_fetch_array($categories)) 
    		{
    		    $cat_id = $row_category['id'];
    		    $cat_name = $row_category['name'];
    		    ?>
                <option <?php if($category_id == $cat_id){echo "SELECTED";} ?> value = "<?php echo $cat_id; ?>"><?php echo $cat_name; ?></option>
<?php       } ?>
							</select>
						</div>
						<div class="col-md-6">
							<label>Item Name</label>
							<input type="text" name="name" class="form-control" required value = "<?php echo $name; ?>" readonly />
						</div>
						<div class="col-md-2">
							<label>STATUS</label>
							<select name = "status" class = "form-control" required>
							    <option <?php if($item_status == 1){echo "SELECTED";} ?> value = "1">Active</option>
							    <option <?php if($item_status != 1){echo "SELECTED";} ?> value = "2">DELETE</option>
							</select>
						</div>
						<div class="col-md-3">
							<label>Barcode</label>
							<input type="text" name="barcode" class="form-control" value = "<?php echo $barcode; ?>" />
						</div>
						<div class="col-md-3">
							<label for="purchase">Purchase</label>
							<input type="number" step="0.01" min="0.0" id="purchase" name="purchase" class="form-control" value = "<?php echo $purchase; ?>" />
						</div>
						<div class="col-md-3">
							<label for="retail">Retail</label>
							<input type="number" step="0.01" min="0.0" id="retail" name="retail" class="form-control" value = "<?php echo $retail; ?>" />
						</div>
						<div class="col-md-3">
							<label for="min_limit">Store Min Limit</label>
							<input type="number" min="0" id="min_limit" name="min_limit" class="form-control" value = "<?php echo $min_limit; ?>" />
						</div>
						<div class="col-md-3">
							<label for="max_limit">Store Max Limit</label>
							<input type="number" min="0" id="max_limit" name="max_limit" class="form-control" value = "<?php echo $max_limit; ?>" />
						</div>
						<div class="col-md-3">
							<label for="deserving">Deserving</label>
							<input type="number" step="0.01" min="0.0" id="deserving" name="deserving" class="form-control" value = "<?php echo $deserving; ?>" />
						</div>
						<div class="col-md-3">
							<label for="poor">Poor</label>
							<input type="number" step="0.01" min="0.0" id="poor" name="poor" class="form-control" value = "<?php echo $poor; ?>" />
						</div>
						<div class="col-md-3">
							<label for="member">Member</label>
							<input type="number" step="0.01" min="0.0" id="member" name="member" class="form-control" value = "<?php echo $member; ?>" />
						</div>
						<div class="col-md-3">
							<label for="general">General</label>
							<input type="number" step="0.01" min="0.0" id="general" name="general" class="form-control" value = "<?php echo $general; ?>" />
						</div>
						<div class="col-md-12" style="margin: 20px 0px;">
						
							<input type="submit" name="update" value="UPDATE ITEM" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
							<a target="_blank" href="show_items.php" class="btn btn-info">SHOW ITEMS</a>
							<a target="_blank" href="show_item_quantity.php" class="btn btn-primary">SHOW ITEMS(QUANTITY)</a>
						</div>
					</div>

				</form>
			</div>    
<?php    exit(0);
}
?>

		<table class="table table-bordered table-hover">
			<thead>
				<caption style="caption-side: top;text-align: center;">
					<h3>SHOW ITEMS</h3>
				</caption>
				<tr class="select_category">
					<th colspan="5"></th>
					<th colspan="2">
						<form method="GET">
							<select onchange="this.form.submit()" class="form-control" name="category_idds">
								<option value="0">All</option>
								<?php 
								echo show_category_options($select_value);
								?>
							</select>
						</form>
					</th>
				</tr>
				<tr>
					<th>S #</th>
					<th>Item Name</th>
					<th>Category</th>
					<th>Poor</th>
					<th>Member</th>
					<TH>GENERAL</TH>
					<th>Action</th>
			</thead>
			<tbody>
<?php
$s = 0;
if (mysqli_num_rows($select) > 0) 
{
	while ($row = mysqli_fetch_array($select)) 
	{
		$s = $s + 1;
		$id = $row['id'];
		$name = $row['name'];
		$quantity = $row['quantity'];
		$category_id = $row['category_id'];
		$categories = mysqli_query($con, "SELECT name FROM categories WHERE id = '$category_id' ");
		while ($row_category = mysqli_fetch_array($categories)) 
		{
			$cat_name = $row_category['name'];
		}
		$min_limit = $row['min_limit'];
		$max_limit = $row['max_limit'];
		if($quantity > $max_limit){$status = '<span style="color:green;">Over Stock</span>';}
		elseif($quantity <= $min_limit){$status = '<span style="color:red;">'.intval($max_limit-$quantity).'</span>';}
		else{$status = "OK";}

echo '
<tr>
	<td>'.$s.'</td>
	<td>'.$name.'</td>
	<td>'.$cat_name.'</td>
	<td>'.$row['poor'].'</td>
	<td>'.$row['member'].'</td>
	<td>'.$row['general'].'</td>
	<td><a href="?up='.$id.'" class="btn btn-success btn-sm">Update</a>
	</td>
</tr>
';
	}
}
?>
			</tbody>
		</table>

	</div>
</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<!-- 
 -->