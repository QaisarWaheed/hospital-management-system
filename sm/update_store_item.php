<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
?>
	<title>Update Store Item- <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">
<?php
if(isset($_POST['update']) && $_POST['update'] != '')
{
	$up_id = $_POST['up_id'];
	$poor = $_POST['poor'];
	$member = $_POST['member'];
	$general = $_POST['general'];
	
	$update = "UPDATE items SET
	`poor` = '$poor',
	`member` = '$member',
	`general` = '$general',
	`rate_updated_by` = '$user_id',
	`rate_updated_at` = '$current_date'
	WHERE id = '$up_id' AND status = '1' ";
    if(mysqli_query($con, $update))
    {
    echo '<script>location.replace("show_all_item_with_rate.php")</script>';
    }
    exit(0);
}
elseif(isset($_GET['id']) && $_GET['id'] != '')
{
    $up_id = $_GET['id'];
    $select = mysqli_query($con,"SELECT * FROM items WHERE id = '$up_id' ");
    if (mysqli_num_rows($select) == 1) 
    {
    	while ($row = mysqli_fetch_array($select)) 
    	{
    		$item_status = $row['status'];
    		$quantity = $row['quantity'];
    		$item_name = $row['name'];
    		$category_name = show_category_name($row['category_id']);
    		$poor = $row['poor'];
    		$member = $row['member'];
    		$general = $row['general'];
    		$min_limit = $row['min_limit'];
    		$max_limit = $row['max_limit'];
    		$purchase = $row['purchase'];
    	}
    }
?>
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Update Item Form</h1></label>
			</div>
			<div class="col-md-12">

				<form name="test" method = "POST" autocomplete="off">

					<div class="row">

						<div class="col-md-3">
							<label>Id</label>
							<input type = "text" name = "up_id" value = "<?php echo $up_id; ?>" class = "form-control" readonly />
						</div>
						<div class="col-md-6">
							<label>Item Name</label>
							<input type="text" name="item_name" class="form-control" required value = "<?php echo $item_name; ?>" readonly />
						</div>
						<div class="col-md-3">
							<label for="quantity">Quantity</label>
							<input type="number" readonly id="quantity" name="quantity" class="form-control" value = "<?php echo $quantity; ?>" />
						</div>
						<div class="col-md-3">
							<label for="min_limit">Min Limit</label>
							<input type="number" readonly id="min_limit" name="min_limit" class="form-control" value = "<?php echo $min_limit; ?>" />
						</div>
						<div class="col-md-3">
							<label for="max_limit">Max Limit</label>
							<input type="number" readonly id="max_limit" name="max_limit" class="form-control" value = "<?php echo $max_limit; ?>" />
						</div>						
						<div class="col-md-3">
							<label for="purchase">Price</label>
							<input step = "0.001" type="number" id="purchase" name="purchase" class="form-control" value = "<?php echo $purchase; ?>" readonly />
						</div>					
						<div class="col-md-3">
							<label for="poor">Poor</label>
							<input step = "0.001" type="number" id="poor" name="poor" class="form-control" value = "<?php echo $poor; ?>" />
						</div>					
						<div class="col-md-3">
							<label for="member">Member</label>
							<input step = "0.001" type="number" id="member" name="member" class="form-control" value = "<?php echo $member; ?>" />
						</div>					
						<div class="col-md-3">
							<label for="general">General</label>
							<input step = "0.001" type="number" id="general" name="general" class="form-control" value = "<?php echo $general; ?>" />
						</div>					
						<div class="col-md-12" style="margin: 20px 0px;">
						
							<input type="submit" name="update" value="UPDATE ITEM" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
							<a target="_blank" href="dashboard.php" class="btn btn-info">DASHBOARD</a>
						</div>
					</div>

				</form>
			</div>    
<?php    exit(0);
}
?>
	</div>
</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<!-- 
 -->