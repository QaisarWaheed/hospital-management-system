<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) {
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$address = $_POST['address'];

	$run2 = mysqli_query($con, "INSERT INTO `parties`
	(`name`, `phone`, `address`) 
	VALUES 
	( '$name', '$phone', '$address')");
?>
<script>
	alert('DATA SAVE SUCCESSFULLY');
</script>
<?php
}
?>
<?php include 'includes/head.php'; ?>
	<title>Add Party - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

		<div class="row">

			<div class="col-md-12" style="text-align: center;">
				<label><h1>Add Party Form</h1></label>
			</div>
			<div class="col-md-12">

				<form method = "POST">

					<div class="row">

						<div class="col-md-12">
							<label>Party Name</label>
							<input type="text" name="name" class="form-control" required>
						</div>
						<div class="col-md-6">
							<label>Phone No</label>
							<input type="text" name="phone" maxlength="11" required class="form-control">
						</div>
						<div class="col-md-6">
							<label>Address</label>
							<textarea name="address" class="form-control" rows="2"></textarea>
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