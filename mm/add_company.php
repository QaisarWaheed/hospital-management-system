<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) {
	$name = $_POST['name'];

	$run2 = mysqli_query($con, "INSERT INTO `item_companies`
	(`name`, `created`) 
	VALUES 
	( '$name' , '$current_date')");
?>
<script>
	alert('DATA SAVE SUCCESSFULLY');
</script>
<?php
}
?>
<?php include 'includes/head.php'; ?>
	<title>Add Company - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

		<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>
			
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Add Company Form</h1></label>
			</div>
			<div class="col-md-12">

				<form method = "POST">

					<div class="row">

						<div class="col-md-12">
							<label>Company Name</label>
							<input type="text" name="name" class="form-control" required>
						</div>
						<div class="col-md-12" style="margin: 20px 0px;">

							<input type="submit" name="save" value="SAVE COMPANY" class="btn btn-success">

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