<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) {
	$item_id = $_POST['item_id'];
	$branch_idd = $_POST['branch_idd'];
	$min_limit = $_POST['min_limit'];
	$max_limit = $_POST['max_limit'];

	$insert = "INSERT INTO `item_register_to_branches`
	(`item_id`, `branch_id`, `quantity`, `min_limit`, `max_limit`, `user_id`, `created`) 
	VALUES 
	('$item_id', '$branch_idd', '0', '$min_limit', '$max_limit','$user_id', '$current_date')";
	try 
	{
		if (mysqli_query($con, $insert)) 
		{	?>
		<script>
			alert('DATA SAVE SUCCESSFULLY');
				location.replace("add_item_to_branch.php");
		</script><?php
		}
		
	}
	catch (Exception $e) 
	{
		 $error = $e;
		 if (strpos($error, 'Duplicate entry') !== false) 
		 {
		 	$get_branch_item_id = get_branch_item_id($item_id, $branch_idd);
		 	$get_branch_item_quantity = get_branch_item_quantity($get_branch_item_id, $branch_idd);
		 	$new_quantity = $get_branch_item_quantity + $quantity;
		
		 	mysqli_query($con, 
		 		"UPDATE `item_register_to_branches` SET 
		 		`min_limit` = '$min_limit',
		 		`max_limit` = '$max_limit'
		 		WHERE `id` = '$get_branch_item_id' ");
		
		 echo '<script> alert("Update Item: min_limit is '.$min_limit.' And max_limit is '.$max_limit.' ")</script>';
		 }
		?>
	<script>
			location.replace("add_item_to_branch.php");
	</script><?php
		
	}
}
?>
<?php include 'includes/head.php'; ?>
	<title>Add Item - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

		<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>
			
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Add Item To Branch Form</h1></label>
			</div>
			<div class="col-md-12">

				<form method = "POST">

					<div class="row">

					<div class="col-md-12">
                    <label>Item</label>
                    <input required list="browsers" name="item_id" id="browser" class = "form-control">
                    <datalist id="browsers">
                    <?php
                    $items = mysqli_query($con, "SELECT * FROM `items` WHERE status = '1' ORDER BY name");
                    if (mysqli_num_rows($items) > 0) {
                    	while ($row_item = mysqli_fetch_array($items)) {
                    		$item_id = $row_item['id'];
                    		$item_name = $row_item['name'];
                    		$category_id = $row_item['category_id'];
                    $categories = mysqli_query($con, "SELECT name FROM `categories` WHERE id = '$category_id' ");
                    if (mysqli_num_rows($categories) == 1) 
                    {
                    	while ($row_category = mysqli_fetch_array($categories)) 
                    	{
                    		$cat_name = $row_category['name'];
                    	}
                    }
                    		echo '<option value="'.$item_id.'">'.$item_name.' - '.$cat_name.'</option>';
                    	}
                    }
                    ?>
                    </datalist>
                    					</div>

						<div class="col-md-12">
							<label>Branchs</label>
							<select name="branch_idd" class="form-control" required>
								<option>Select Item</option>
								<?php
								$branches = mysqli_query($con, "SELECT * FROM `branchs` WHERE status = '1' ORDER BY name");
								if (mysqli_num_rows($branches) > 0) {
									while ($row_branch = mysqli_fetch_array($branches)) {
										$branch_idd = $row_branch['id'];
										$branch_namee = $row_branch['name'];
										$branch_addresss = $row_branch['address'];
										echo '<option value="'.$branch_idd.'">'.$branch_addresss.' ( '.$branch_namee.' )</option>';
									}
								}
								?>
							</select>
						</div>
						<div class="col-md-6">
							<label for="min_limit"> Min Limit</label>
							<input type="number" min="0" id="min_limit" name="min_limit" class="form-control">
						</div>
						<div class="col-md-6">
							<label for="max_limit"> Max Limit</label>
							<input type="number" min="0" id="max_limit" name="max_limit" class="form-control">
						</div>
						<div class="col-md-12" style="margin: 20px 0px;">

							<input type="submit" name="save" value="SAVE ITEM IN BRANCH" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
							<a href = "update_branch_item.php" class = "btn btn-info">UPDATE BRANCH ITEM</a>
						</div>
					</div>

				</form>
			</div>

		</div>

	</div>

</div>


</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>