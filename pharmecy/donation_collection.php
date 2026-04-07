<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) 
{
	$branch_idd = $_POST['branch_idd'];
	$type_donation_id = $_POST['type_donation_id'];
	$amount_mod_id = $_POST['amount_mod_id'];
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$cost = $_POST['cost'];
	$amount = $_POST['amount'];
	$book_no = $_POST['book_no'];
	$slip_no = $_POST['slip_no'];

	$insert = "INSERT INTO `fr_collection`(`fr_collection_id`, `slip_no`, `name`, `type_donation_id`, `book_no`, `branch_id`, `phone`, `amount`, `cost`, `user_id`, `payment_method_id `, `fr_collection_created`) 
	VALUES 
	(NULL, '$slip_no', '$name', '$type_donation_id', '$book_no', '$branch_idd', '$phone', '$amount', '$cost', '$user_id', '$amount_mod_id', '$current_date')";
	$run = mysqli_query($con, $insert);
	if ($run) 
	{	?>
	<script>
		alert('DATA SAVE SUCCESSFULLY');
			location.replace("donation_collection.php");
	</script><?php
	}
	else
	{	
		 echo  $con->error;
		 echo '
			 <script> 
				location.replace("add_user.php");
			 </script>';
	}
}
?>
<?php include 'includes/head.php'; ?>
	<title>Donation Collection - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>
<div>

	<div class="">

		<div class="row" style="margin: 0px;">

        	<div class="col-md-3 background_whitesmoke">
        		<?php include 'left_navigation.php'; ?>
        	</div>
			<div class="col-md-9">
            <div class = "row">
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Patient Medicine</h1></label>
			</div>
		<div class="col-md-12">
				<form method = "POST" autocomplete="off">

					<div class="row">

						<div class="col-md-6">
							<label for="book_no"> Computer Record Id</label>
							<input type="number" min="1" id="book_no" readonly name="id" value="<?php echo next_donation_id()?>" required class="form-control">
						</div>

						<div class="col-md-6">
							<label for="book_no"> Enter Book No</label>
							<input type="number" min="1" id="book_no" name="book_no" required class="form-control">
						</div>
						<div class="col-md-6">
							<label for="slip_no"> Enter Slip / Reg. No</label>
							<input type="number" id="slip_no" name="slip_no" autocomplete="off" class="form-control" required>
						</div>


						<div class="col-md-6">
							<label for="name"> Enter Name</label>
							<input type="text" id="name" name="name" required class="form-control">
						</div>
						<div class="col-md-6">
							<label for="phone"> Enter Phone No</label>
							<input type="text" pattern="[0-9]{11}" id="phone" name="phone" autocomplete="off" class="form-control" required>
						</div>

						<div class="col-md-6">
							<label>SELECT BRANCH</label>
							<select name="branch_idd" class="form-control" required>
								<?php
								$branches = mysqli_query($con, "SELECT * FROM `branchs` WHERE id = '$branch_id' ORDER BY name");
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
							<label>SELECT DONATION TYPE</label>
							<select name="type_donation_id" class="form-control" required>
								<option>Select Donation Type</option>
								<?php
								$roles = mysqli_query($con, "SELECT * FROM `donation_types` ORDER BY title");
								if (mysqli_num_rows($roles) > 0) {
									while ($row_role = mysqli_fetch_array($roles)) {
										$role_idd = $row_role['id'];
										$role_namee = $row_role['title'];
										echo '<option value="'.$role_idd.'">'.$role_namee.'</option>';
									}
								}
								?>
							</select>
						</div>

						<div class="col-md-6">
							<label>SELECT AMOUNT MOD</label>
							<select name="amount_mod_id" class="form-control" required>
								<option>Select Amount Mod</option>
								<?php
								$roles = mysqli_query($con, "SELECT * FROM `amount_modes` ORDER BY title");
								if (mysqli_num_rows($roles) > 0) {
									while ($row_role = mysqli_fetch_array($roles)) {
										$role_idd = $row_role['id'];
										$role_namee = $row_role['title'];
										echo '<option value="'.$role_idd.'">'.$role_namee.'</option>';
									}
								}
								?>
							</select>
						</div>

						<div class="col-md-6">
							<label for="amount"> Enter Amount </label>
							<input type="number" id="amount" name="amount" required class="form-control">
						</div>
						<div class="col-md-6">
							<label for="cost"> Enter Cost </label>
							<input type="number" id="cost" name="cost" required class="form-control">
						</div>

						<div class="col-md-12" style="margin: 20px 0px;">

							<input type="submit" id="save" onclick="myDisplayGoneSave()" name="save" value="SAVE DONATION" class="btn btn-success">

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

<script>
function myDisplayGoneSave() {
  document.getElementById("save").style.display = "none";
}
</script>