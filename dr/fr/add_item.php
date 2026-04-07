<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) {
	$name = $_POST['name'];
	$category_id = $_POST['category_id'];
	$barcode = $_POST['barcode'];
	$retail = $_POST['retail'];
	$deserving = $_POST['deserving'];
	$poor = $_POST['poor'];
	$member = $_POST['member'];
	$general = $_POST['general'];
	$min_limit = $_POST['min_limit'];
	$max_limit = $_POST['max_limit'];

	$run2 = mysqli_query($con, "INSERT INTO `items`
	(`category_id`, `name`, `barcode`, `retail`, `deserving`, `poor`, `member`, `general`, `min_limit`, `max_limit`) 
	VALUES 
	('$category_id', '$name', '$barcode', '$retail', '$deserving', '$poor', '$member', '$general', '$min_limit', '$max_limit')");
?>
<!-- <script>
	alert('DATA SAVE SUCCESSFULLY');
</script> -->
<?php
}
?>
<?php include 'includes/head.php'; ?>
	<title>Add Item - <?php echo $company_trademark; ?></title>
<!-- <script>
    document.onkeydown=function(evt){
        var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
        if(keyCode == 18)
        {
            document.test.submit();
        }
    }
</script> -->
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

		<div class="row">

			<div class="col-md-12" style="text-align: center;">
				<label><h1>Add Item Form</h1></label>
			</div>
			<div class="col-md-12">

				<form name="test" method = "POST" autocomplete="off">

					<div class="row">

						<div class="col-md-12">
							<label>Category</label>
							<select name="category_id" class="form-control" required>
								<option>Select Category</option>
								<?php
								$categories = mysqli_query($con, "SELECT * FROM `categories` WHERE status = '1' ORDER BY name");
								if (mysqli_num_rows($categories) > 0) {
									while ($row_category = mysqli_fetch_array($categories)) {
										$cat_id = $row_category['id'];
										$cat_name = $row_category['name'];
										echo '<option value="'.$cat_id.'">'.$cat_name.'</option>';
									}
								}
								?>
							</select>
						</div>
						<div class="col-md-12">
							<label>Item Name</label>
							<input type="text" name="name" class="form-control" required>
						</div>
						<div class="col-md-6">
							<label>Barcode</label>
							<input type="text" name="barcode" class="form-control">
						</div>
						<div class="col-md-6">
							<label for="retail">Retail</label>
							<input type="number" step="0.01" min="0.0" id="retail" name="retail" class="form-control">
						</div>
						<div class="col-md-6">
							<label for="deserving">Deserving</label>
							<input type="number" step="0.01" min="0.0" id="deserving" name="deserving" class="form-control">
						</div>
						<div class="col-md-6">
							<label for="poor">Poor</label>
							<input type="number" step="0.01" min="0.0" id="poor" name="poor" class="form-control">
						</div>
						<div class="col-md-6">
							<label for="member">Member</label>
							<input type="number" step="0.01" min="0.0" id="member" name="member" class="form-control">
						</div>
						<div class="col-md-6">
							<label for="general">General</label>
							<input type="number" step="0.01" min="0.0" id="general" name="general" class="form-control">
						</div>
						<div class="col-md-6">
							<label for="min_limit">Store Min Limit</label>
							<input type="number" min="0" id="min_limit" name="min_limit" class="form-control">
						</div>
						<div class="col-md-6">
							<label for="max_limit">Store Max Limit</label>
							<input type="number" min="0" id="max_limit" name="max_limit" class="form-control">
						</div>
						<div class="col-md-12" style="margin: 20px 0px;">
						
							<input type="submit" name="save" value="SAVE ITEM" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
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