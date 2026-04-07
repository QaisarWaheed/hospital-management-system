<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) 
{
	$password = md5($_POST['password']);
	$branch_idd = $_POST['branch_idd'];
	$is_admin = $_POST['is_admin'];
	$phone = $_POST['phone'];
	$consultant_status = $_POST['consultant_status'];
	$is_incharge = $_POST['is_incharge'];
	$department_id = $_POST['department_id'];
	$in_time = $_POST['in_time'];
	$out_time = $_POST['out_time'];
	$qualification = $_POST['qualification'];
	$user_id_name = $_POST['user_id_name'];
	$role_idd = $_POST['role_idd'];

	$insert = "INSERT INTO `users`
	(`emp_id`, `branch_id`, `u_name`, `password`, `is_admin`, `user_id`, `role_id`, `is_incharge`, `created`, `in_time`, `out_time`, `qualification`, `department_id`, `phone`, `consultant_status`) 
	VALUES 
	('0', '$branch_idd', '$user_id_name', '$password', '$is_admin','$user_id', '$role_idd', '$is_incharge', '$current_date', '$in_time', '$out_time', '$qualification', '$department_id', '$phone', '$consultant_status')";
	$run = mysqli_query($con, $insert);
	if ($run) 
	{	?>
	<script>
		alert('DATA SAVE SUCCESSFULLY');
			location.replace("add_user.php");
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
	<title>Add User - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
	    <div class = "row">
    	    <div class = "col"><a class = "btn btn-info" href = "dashboard.php">Dashboard</a></div>
    	    <div class = "col">YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</div>
	    </div>
	</div>
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

				<form method = "POST" autocomplete="off">

					<div class="row">

						<div class="col-md-12">
							<label>SELECT BRANCH</label>
							<select name="branch_idd" class="form-control" required>
								<option value = "">Select Branch</option>
								<option value = "0">No One</option>
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

						<div class="col-md-12">
							<label>SELECT ROLE</label>
							<select name="role_idd" class="form-control" required>
								<option>Select Role</option>
								<?php
								$roles = mysqli_query($con, "SELECT * FROM `roles` WHERE status = '1' AND id != '1	' ORDER BY title");
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
							<label for = "in_time">In-Time</label>
							<input type = "time" name = "in_time" class = "form-control" required />
						</div>
						<div class="col-md-6">
							<label for = "out_time">Out-Time</label>
							<input type = "time" name = "out_time" class = "form-control" required />
						</div>
						<div class="col-md-6">
							<label for = "qualification">Qualification</label>
							<input type="text" name="qualification" class="form-control" />
						</div>
						<div class="col-md-6">
							<label>Department</label>
							<select name = "department_id" class = "form-control" required autofocus>
							    <option value = "0">NO DEPARTMENT</option>
								<?php
								$roles = mysqli_query($con, "SELECT * FROM `departments` WHERE `department_status` = '1' ORDER BY `departments`.`department_title` ASC ");
								if (mysqli_num_rows($roles) > 0) {
									while ($row_role = mysqli_fetch_array($roles)) {
										$department_id = $row_role['department_id'];
										$department_title = $row_role['department_title'];
										echo '<option value="'.$department_id.'">'.$department_title.'</option>';
									}
								}
								?>							
								</select>
						</div>
						<div class="col-md-6">
							<label for="user_id_name"> Enter User Id</label>
							<input type="text" id="user_id_name" name="user_id_name" required class="form-control">
						</div>
						<div class="col-md-6">
							<label for="pasword"> Enter Password</label>
							<input type="password" id="password" name="password" autocomplete="off" class="form-control" required>
						</div>
						<div class="col-md-6">
							<label for="phone"> Phone No</label>
							<input type="text" pattern="[0-9]{11}" id="phone" name="phone" required class="form-control">
						</div>
						<div class="col-md-6">
							<label for="consultant_status"> CONSULTANT STATUS</label>
							<select name = "consultant_status" class = "form-control" required>
							    <option value = "1">ACTIVE</option>
							    <option value = "2">BLOCK</option>
							</select>
						</div>
						<div class="col-md-6" style="font-size: 25px">
							<label for="pasword"> IS ADMIN:</label>						
							<input type="radio" value="2" id="is_admin_true" class="btn " name="is_admin">
							<label for="is_admin_true">YES</label>
							<input checked type="radio" value="1" id="is_admin_false" name="is_admin">
							<label for="is_admin_false">NO</label>
							</br>
							<label for="is_incharge"> IS INCHARGE:</label>						
							<input type="radio" value="2" id="is_incharge_true" class="btn " name="is_incharge">
							<label for="is_incharge_true">YES</label>
							<input checked type="radio" value="1" id="is_incharge_false" name="is_incharge">
							<label for="is_incharge_false">NO</label>
						</div>
						<div class="col-md-12" style="margin: 20px 0px;">

							<input type="submit" name="save" value="SAVE USER ID IN BRANCH" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
							<a href="show_user.php" class="btn btn-info btn-sm"> Show Users List</a>
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
<?php mysqli_close($con); ?>