<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) 
{
	$fr_collection_id = $_POST['fr_collection_id'];
	$branch_idd = $_POST['branch_idd'];
	$collection_type_id = $_POST['collection_type_id'];
	$amount_mod_id = $_POST['amount_mod_id'];
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$cost = $_POST['cost'];
	$amount = $_POST['amount'];
// 	$slip_no = $_POST['slip_no'];

	$insert = "INSERT INTO `fr_collection`(`fr_collection_id`, `slip_no`, `name`, `collection_type_id`, `book_id`, `branch_id`, `phone`, `amount`, `cost`, `user_id`, `payment_method_id`, `fr_collection_created`) 
	VALUES 
	(NULL, '$slip_no', '$name', '$collection_type_id', '$book_no', '$branch_idd', '$phone', '$amount', '$cost', '$user_id', '$amount_mod_id', '$current_date')";
	$update = "
	UPDATE `fr_collection`
        SET
            `branch_id` = '$branch_idd',
            `collection_type_id` = '$collection_type_id',
            `name` = '$name',
            `phone` = '$phone',
            `amount` = '$amount',
            `cost` = '$cost',
            `payment_method_id` = '$payment_method_id',
            `fr_collection_updated_at` = '$current_date',
            `fr_collection_updated_by` = '$user_id',
            `fr_collection_status` = '2'
        WHERE
            `fr_collection_id` = '$fr_collection_id' AND fr_collection_status = '1' ";
	$run = mysqli_query($con, $update);
	if ($run) 
	{
	    mysqli_query($con, "UPDATE `receipt_books` SET `used_receipts` = `used_receipts` + 1 WHERE book_no IN (SELECT book_id FROM fr_collection WHERE fr_collection.fr_collection_id = '$fr_collection_id')");
	?>
	<script>
		alert('DATA SAVE SUCCESSFULLY');
			location.replace("fr_collection.php");
	</script><?php
	}
	else
	{	
		 echo  $con->error;
// 		 echo '
// 			 <script> 
// 				location.replace("add_user.php");
// 			 </script>';
	}
	exit(0);
}
?>
<?php include 'includes/head.php'; ?>
	<title>FR Collection - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>
<div>

	<div class="">

		<div class="row" style="margin: 0px;">

        	<div class="col-md-3 background_whitesmoke nodisplay_print">
        		<?php include 'left_navigation.php'; ?>
        	</div>
			<div class="col-md-9">
            <div class = "row">
			<div class="col-md-12" style="text-align: center;">
				<label><h1>FR COLLECION FORM </h1></label>
			</div>
		<div class="col-md-12">
				<form method = "POST" autocomplete="off">

					<div class="row">

						<div class="col-md-6">
							<label for="book_no"> Computer Record Id</label>
							<select required autofocus name = "fr_collection_id" class = "form-control">
							    <?php echo next_collection_id($branch_id)?>
							</select>
						</div>

						<!--<div class="col-md-6">-->
						<!--	<label for="book_no"> Enter Book No</label>-->
						<!--	<input type="number" min="1" id="book_no" name="book_no" required class="form-control">-->
						<!--</div>-->
						<!--<div class="col-md-6">-->
						<!--	<label for="slip_no"> Enter Slip / Reg. No</label>-->
						<!--	<input type="number" id="slip_no" name="slip_no" autocomplete="off" class="form-control" required>-->
						<!--</div>-->


						<div class="col-md-6">
							<label for="name"> Enter Name</label>
							<input type="text" id="name" name="name" required class="form-control">
						</div>
						<div class="col-md-6">
							<label for="phone"> Enter Phone No</label>
							<input type="text" value = "<?php echo $branch_phone; ?>" pattern="[0-9]{11}" id="phone" name="phone" autocomplete="off" class="form-control" required>
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
							<label>SELECT COLLECTION TYPE</label>
							<select name="collection_type_id" class="form-control" required>
								<option value = "">Select Collection Type</option>
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
								<option value = "">Select Amount Mod</option>
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
							<input type="number" min = "1" id="amount" name="amount" required class="form-control">
						</div>
						<div class="col-md-6">
							<label for="cost"> Enter Cost </label>
							<input type="number" value = "0" min = "0" id="cost" name="cost" required class="form-control">
						</div>

						<div class="col-md-12" style="margin: 20px 0px;">

							<input type="submit" id="save" onclick="myDisplayGoneSave()" name="save" value="SAVE COLLECTION" class="btn btn-success">
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