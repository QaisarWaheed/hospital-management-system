<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
?>
	<title>User Update - <?php echo $company_trademark; ?></title>
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
			<?php
if(isset($_POST['update']) && $_POST['update'] != '')
{
	$up_id = $_POST['up_id'];
	$is_admin = $_POST['is_admin'];
	$new_role_id = $_POST['role_id'];
	$consultant_status = $_POST['consultant_status'];
	$phone = $_POST['phone'];
	$is_incharge = $_POST['is_incharge'];
	$department_id = $_POST['department_id'];
	$branch_id = $_POST['branch_id'];
	$in_time = $_POST['in_time'];
	$out_time = $_POST['out_time'];
	$qualification = $_POST['qualification'];
	$status = $_POST['status'];

	$update = "UPDATE users SET
    is_admin = '$is_admin',
    is_incharge = '$is_incharge',
    department_id = '$department_id',
    branch_id = '$branch_id',
    in_time = '$in_time',
    out_time = '$out_time',
    consultant_status = '$consultant_status',
    phone = '$phone',
    qualification = '$qualification',
    status = '$status'
	WHERE id = '$up_id' ";

    if(mysqli_query($con, $update))
    {
    echo '<script>alert("Update Item")</script>';
    echo '<script>location.replace("show_user.php")</script>';
    }
    exit(0);
}
elseif(isset($_GET['up']) && $_GET['up'] != '')
{
    $up_id = $_GET['up'];
    $select = mysqli_query($con,"SELECT * FROM users WHERE id = '$up_id' ");
    if (mysqli_num_rows($select) == 1) 
    {
    	while ($row = mysqli_fetch_array($select)) 
    	{
    		$u_name = $row['u_name'];
    		$is_admin = $row['is_admin'];
    		$department_id = $row['department_id'];
    		$branch_id = $row['branch_id'];
    		$in_time = $row['in_time'];
    		$out_time = $row['out_time'];
    		$consultant_status = $row['consultant_status'];
    		$phone = $row['phone'];
    		$is_incharge = $row['is_incharge'];
    		$qualification = $row['qualification'];
    		$status = $row['status'];
    		$old_role_id = $row['role_id'];
    	}
    }

?>
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Update Item Form</h1></label>
			</div>
			<div class="col-md-12">

				<form name="test" method = "POST" autocomplete="off">

					<div class="row">

						<div class="col-md-2">
							<label>Id</label>
							<input type = "text" name = "up_id" value = "<?php echo $up_id; ?>" class = "form-control" readonly />
						</div>
						<div class="col-md-4">
							<label>Role</label>
							<select name = "role_id" class = "form-control" required>
							    <?php
                                    $run_role = mysqli_query($con, "SELECT * FROM `roles` WHERE id != '1' OR status = '1' ");
                                    if (mysqli_num_rows($run_role) > 0) 
                                    {
                                        while ($row_role = mysqli_fetch_array($run_role)) 
                                        {
                                            $update_branch_id = $row_role['id'];
                                            
                                            if($old_role_id == $update_branch_id)
                                            {
                                                echo '<option SELECTED value = "'.$update_branch_id.'">'.$row_role['title'].'</option>';
                                            }
                                        }    
                                    } 							    
							    ?>
							</select>						
							</div>
						<div class="col-md-6">
							<label>User Name</label>
							<input type="text" name="name" class="form-control" required value = "<?php echo $u_name; ?>" readonly />
						</div>

						<div class="col-md-2">
							<label for = "in_time">In-Time</label>
							<input type = "time" name = "in_time" value = "<?php echo $in_time; ?>" class = "form-control" required />
						</div>
						<div class="col-md-2">
							<label for = "out_time">Out-Time</label>
							<input type = "time" name = "out_time" value = "<?php echo $out_time; ?>" class = "form-control" required />
						</div>
						<div class="col-md-2">
							<label for = "branch_id">Branch</label>
							<select name = "branch_id" class = "form-control" required>
							    <option value = "">SELECT BRANCH</option>
							    <?php
                                    $run = mysqli_query($con, "SELECT id, address FROM `branchs` WHERE `status` = '1' ");
                                    if (mysqli_num_rows($run) > 0) 
                                    {
                                        while ($row = mysqli_fetch_array($run)) 
                                        {
                                            $update_branch_id = $row['id'];
                                            
                                            if($branch_id == $update_branch_id)
                                            {
                                                echo '<option SELECTED value = "'.$update_branch_id.'">'.$row['address'].'</option>';
                                            }
                                            else
                                            {
                                                echo '<option value = "'.$row['id'].'">'.$row['address'].'</option>';
                                            }
                                        }    
                                    } 							    
							    ?>
							</select>
						</div>
						<div class="col-md-2">
							<label for = "is_admin">Is Admin</label>
							<select name = "is_admin" class = "form-control" required>
							    <option value = "">SELECT ADMIN STATUS</option>
							    <option <?php if($is_admin == 1){echo 'SELECTED';}?> value = "1">NO</option>
							    <option <?php if($is_admin == 2){echo 'SELECTED';}?> value = "2">YES</option>
							</select>
						</div>
						<div class="col-md-4">
							<label for = "qualification">Qualification</label>
							<input type="text" name="qualification" class="form-control" required value = "<?php echo $qualification; ?>" />
						</div>
						<div class="col-md-2">
							<label for = "is_incharge">Is Incharge</label>
							<select name = "is_incharge" class = "form-control" required>
							    <option value = "">SELECT INCHARGE STATUS</option>
							    <option <?php if($is_incharge == 1){echo 'SELECTED';}?> value = "1">NO</option>
							    <option <?php if($is_incharge == 2){echo 'SELECTED';}?> value = "2">YES</option>
							</select>
						</div>
						<div class="col-md-2">
							<label>User Status</label>
							<select name = "status" class = "form-control" required>
							    <option value = "">SELECT STATUS</option>
							    <option <?php if($status == 1){echo 'SELECTED';}?> value = "1">ACTIVE</option>
							    <option <?php if($status == 2){echo 'SELECTED';}?> value = "2">CLOSED</option>
							</select>
						</div>
						<div class="col-md-2">
							<label>Consultant Status</label>
							<select name = "consultant_status" class = "form-control" required>
							    <option value = "">SELECT STATUS</option>
							    <option <?php if($consultant_status == 1){echo 'SELECTED';}?> value = "1">ACTIVE</option>
							    <option <?php if($consultant_status == 2){echo 'SELECTED';}?> value = "2">CLOSED</option>
							</select>
						</div>
						<div class="col-md-2">
							<label for="phone"> Phone No</label>
							<input type="text" pattern="[0-9]{11}" id="phone" name="phone" required class="form-control" value = "<?php echo $phone; ?>">
						</div>
						<div class="col-md-4">
							<label>Department</label>
							<select name = "department_id" class = "form-control" requiredautofocus>
								<?php
								if($department_id == 0)
								{
    								echo '<option value="0">NO DEPARTMENT SELECTED</option>';
								}
								$roles = mysqli_query($con, "SELECT * FROM `departments` WHERE `department_status` = '1' ORDER BY `departments`.`department_title` ASC ");
								if (mysqli_num_rows($roles) > 0) {
									while ($row_role = mysqli_fetch_array($roles)) {
										$department_idd = $row_role['department_id'];
										$department_title = $row_role['department_title'];
										if($department_idd == $department_id)
										{
    										echo '<option SELECTED value="'.$department_idd.'">'.$department_title.'</option>';
										}
										else
										{
    										echo '<option value="'.$department_idd.'">'.$department_title.'</option>';
										}
									}
								}
								?>							</select>
						</div>

						<div class="col-md-12" style="margin: 20px 0px;">
						
							<input type="submit" name="update" value="UPDATE USER" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
							<a href="show_user.php" class="btn btn-info">SHOW USERS</a>
						</div>
					</div>

				</form>
			</div>    
<?php    
exit(0);
}
else
{
    header('location: show_user.php');
}
?>
	</div>
</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<?php mysqli_close($con); ?>