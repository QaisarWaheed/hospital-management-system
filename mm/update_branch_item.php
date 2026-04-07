<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
if (isset($_GET['category_idds']) && $_GET['category_idds'] != ''&& $_GET['category_idds'] != '0') 
{
$select_value = $_GET['category_idds'];
$select = mysqli_query($con, "SELECT * FROM `item_register_to_branches` WHERE branch_id = '$branch_id' AND item_id IN (SELECT id FROM items WHERE category_id = '$select_value')");
}
else
{
$select_value = 0;
$select = mysqli_query($con, "SELECT * FROM `item_register_to_branches` WHERE branch_id = '$branch_id' ");
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
			
<?php
if(isset($_POST['update']) && $_POST['update'] != '')
{
	$up_id = $_POST['up_id'];
	$select_value = $_POST['select_value'];
	$status = $_POST['status'];
	$min_limit = $_POST['min_limit'];
	$max_limit = $_POST['max_limit'];
	$update = "UPDATE item_register_to_branches SET
	`status` = '$status',
	`min_limit` = '$min_limit',
	`max_limit` = '$max_limit'
	WHERE id = '$up_id' ";

    if(mysqli_query($con, $update))
    {
    // echo '<script>alert("Update Item")</script>';
    // echo $update;
    echo '<script>location.replace("update_branch_item.php?category_idds='.$select_value.'")</script>';
    }
    // header('location: show_items.php');
    exit(0);
}
elseif(isset($_GET['up']) && $_GET['up'] != '')
{
    $up_id = $_GET['up'];
    $select_value = $_GET['select_value'];
    $select = mysqli_query($con,"SELECT * FROM `item_register_to_branches` WHERE id = '$up_id' ");
    if (mysqli_num_rows($select) == 1) 
    {
    	while ($row = mysqli_fetch_array($select)) 
    	{
    		$item_status = $row['status'];
    		$br_id = $row['branch_id'];
    		$item_id = $row['item_id'];
    		$item_name = get_item_name_by_register_item_id($up_id);
    		$quantity = $row['quantity'];
    		$min_limit = $row['min_limit'];
    		$max_limit = $row['max_limit'];
    		if($quantity > $max_limit){$status = '<span style="color:green;">Over Stock</span>';}
    		elseif($quantity <= $min_limit){$status = '<span style="color:red;">'.intval($max_limit-$quantity).'</span>';}
    		else{$status = "OK";}
    	}
    }
?>
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Update Branch Item Form</h1></label>
			</div>
			<div class="col-md-12">

				<form name="test" method = "POST" autocomplete="off">

					<div class="row">

						<div class="col-md-4">
							<label>Id</label>
							<input type = "hidden" name = "select_value" value = "<?php echo $select_value; ?>" class = "form-control" readonly />
							<input type = "text" name = "up_id" value = "<?php echo $up_id; ?>" class = "form-control" readonly />
						</div>
						<div class="col-md-8">
							<label>Item Name</label>
							<input readonly type="text" name="name" class="form-control" required value = "<?php echo $item_name; ?>" readonly />
						</div>
						<div class="col-md-4">
							<label>STATUS</label>
							<select name = "status" class = "form-control" required>
							    <option <?php if($item_status == 1){echo "SELECTED";} ?> value = "1">Active</option>
							    <option <?php if($item_status != 1){echo "SELECTED";} ?> value = "2">DELETE</option>
							</select>
						</div>
						<div class="col-md-3">
							<label for="min_limit">Branch Min Limit</label>
							<input type="number" min="0" id="min_limit" name="min_limit" class="form-control" value = "<?php echo $min_limit; ?>" />
						</div>
						<div class="col-md-3">
							<label for="max_limit">Branch Max Limit</label>
							<input type="number" min="0" id="max_limit" name="max_limit" class="form-control" value = "<?php echo $max_limit; ?>" />
						</div>
						<div class="col-md-2">
							<label for="item_from_branch">Item From Branch</label>
							<input type="text" readonly  id="item_from_branch" name="item_from_branch" class="form-control" value = "<?php echo get_branch_name_by($br_id); ?>" />
						</div>
						<div class="col-md-12" style="margin: 20px 0px;">
						
							<input type="submit" name="update" value="UPDATE ITEM" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
							<a href="update_branch_item.php" class="btn btn-info">SHOW BRANCH ITEMS</a>
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
					<h3>SHOW ITEMS(<?php echo $branch_address; ?>)</h3>
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
					<th>Quantity</th>
					<th>Min Limit</th>
					<th>Max Limit</th>
					<th>Status</th>
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
		$quantity = $row['quantity'];
		$item_id = $row['item_id'];
		$item_name = get_item_name_by_register_item_id($id);
		$category_name = show_category_name_by_register_branch_id($id);
		$quantity = $row['quantity'];
		$min_limit = $row['min_limit'];
		$max_limit = $row['max_limit'];
		if($quantity > $max_limit){$status = '<span style="color:green;">Over Stock</span>';}
		elseif($quantity <= $min_limit){$status = '<span style="color:red;">'.intval($max_limit-$quantity).'</span>';}
		else{$status = "OK";}

echo '
<tr>
	<td>'.$s.'</td>
	<td>'.$item_name.'</td>
	<td>'.$category_name.'</td>
	<td>'.$quantity.'</td>
	<td>'.$min_limit.'</td>
	<td>'.$max_limit.'</td>
	<td>'.$status.'</td>
	<td><a href="?up='.$id.'&select_value='.$select_value.'" class="btn btn-success btn-sm">Update</a>
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