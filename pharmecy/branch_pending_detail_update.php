<?php include 'includes/connect.php'; 
if (isset($_GET['u_id']) && $_GET['u_id'] != '') {
	$branch_pending_id = $_GET['u_id'];
	$select_branch_pending_query = "SELECT * FROM `branch_pending_details` WHERE id = '$branch_pending_id' ";
	$select_branch_pending = mysqli_query($con, $select_branch_pending_query);
	if (mysqli_num_rows($select_branch_pending) > 0) 
	{
	while ($row_branch_data = mysqli_fetch_array($select_branch_pending) ) 
	{
		$token_no = $row_branch_data['token_no'];
		$gardian_name = $row_branch_data['gardian_name'];
		$gardian_phone = $row_branch_data['gardian_phone'];
		$recommended_by = $row_branch_data['recommended_by'];
	}
	}
}

if (isset($_GET['save'])) {
	$branch_pending_id = $_GET['branch_pending_id'];
	$token_no = $_GET['token_no'];
	$gardian_name = $_GET['gardian_name'];
	$gardian_phone = $_GET['gardian_phone'];
	$recommended_by = $_GET['recommended_by'];

	$update = "UPDATE `branch_pending_details` SET
		`gardian_name` = '$gardian_name',
		`gardian_phone` = '$gardian_phone',
		`recommended_by` = '$recommended_by'
		WHERE id = '$branch_pending_id'";
	$run = mysqli_query($con, $update);
	if ($run) 
	{
	echo '<script>alert("DATA UPDATED...");
		location.replace("branch_procedure_pending_token.php");</script>';
	}
	else
	{
		echo $con->error;
	}
}
?>
<?php include 'includes/head.php'; ?>
	<title>Patient Registeration - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo" oncontextmenu="return false;">

<div>

	<div class="" style="margin: 10px 15px;">
		<form method="GET">
			<div class="row" style="margin-top: 20px">

				<div class="col-md-6">
					<label>Pending Id</label>
					<input required type="number" value="<?php echo $branch_pending_id;?>" class="form-control" name="branch_pending_id" readonly>
				</div>

				<div class="col-md-6">
					<label>TOKAN NO</label>
					<input required type="number" value="<?php echo $token_no;?>" class="form-control" name="token_no" readonly>
				</div>

				<div class="col-md-12">
					<label>Gardian / Ref. Name</label>
					<input type="text" value="<?php echo $gardian_name;?>" class="form-control" name="gardian_name">
				</div>

				<div class="col-md-12">
					<label>Gardian / Ref. Phone</label>
					<input type="text" pattern="[0-9]{11}" value="<?php echo $gardian_phone;?>" class="form-control" name="gardian_phone" />
				</div>

				<div class="col-md-12">
					<label>Recommended By</label>
					<input type="text" value="<?php echo $recommended_by;?>" class="form-control" name="recommended_by" />
				</div>

				<div class="col-md-12" style="margin-top: 20px">
					<input type="submit" value="UPDATE" name="save" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>

</div>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>