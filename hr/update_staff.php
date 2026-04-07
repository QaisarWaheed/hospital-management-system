<?php include 'includes/connect.php'; 
if(isset($_GET['staff_id']))
{
    $staff_id = $_GET['staff_id'];
    $select = "SELECT * FROM staff WHERE staff_id = '$staff_id' AND staff_status > '0' AND staff_status < '6' ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run) == 1)
    {
        while($_row = mysqli_fetch_array($run))
        {
            $other_person_name = $_row['other_person_name'];
            $other_person_address = $_row['other_person_address'];
            $other_person_phone = $_row['other_person_phone'];
            $staff_relationship_id = $_row['relationship_id'];
            $hostel_name = $_row['hostel_name'];
            $hostel_warden_name = $_row['hostel_warden_name'];
            $hostel_warden_phone = $_row['hostel_warden_phone'];
            $hostel_address = $_row['hostel_address'];
            $staff_id = $_row['staff_id'];
            $staff_status = $_row['staff_status'];
            $staff_name = $_row['staff_name'];
            $staff_spouse = $_row['staff_spouse'];
            $staff_phone = $_row['staff_phone'];
            $staff_cnic = $_row['staff_cnic'];
            $staff_address = $_row['staff_address'];
            $staff_branch_id = $_row['branch_id'];
            $staff_duty_hours = $_row['staff_duty_hours'];
            $staff_joining_date = $_row['staff_joining_date'];
            $staff_time_in = $_row['staff_time_in'];
            $staff_time_out = $_row['staff_time_out'];
            $staff_bacis_salary = $_row['staff_bacis_salary'];
            $staff_allowed_leaves = $_row['staff_allowed_leaves'];
            $staff_qualification = $_row['staff_qualification'];
            $staff_designation_id = $_row['designation_id'];            
        }
    }
    else
    {
        header('location: show_staff.php?msg=err');
    }
}
if(isset($_POST['update_staff'])) 
{
    $other_person_name = $_POST['other_person_name'];
    $other_person_address = $_POST['other_person_address'];
    $other_person_phone = $_POST['other_person_phone'];
    $relationship_id = $_POST['relationship_id'];
    $hostel_name = $_POST['hostel_name'];
    $hostel_warden_name = $_POST['hostel_warden_name'];
    $hostel_warden_phone = $_POST['hostel_warden_phone'];
    $hostel_address = $_POST['hostel_address'];
	$staff_id = $_POST['staff_id'];
	$staff_status = $_POST['staff_status'];
	$staff_name = $_POST['staff_name'];
	$staff_spouse = $_POST['staff_spouse'];
	$staff_phone = $_POST['staff_phone'];
	$staff_cnic = $_POST['staff_cnic'];
	$staff_address = $_POST['staff_address'];
	$branch_idd = $_POST['branch_idd'];
	$staff_duty_hours = $_POST['staff_duty_hours'];
	$staff_joining_date = $_POST['staff_joining_date'];
	$staff_time_in = $_POST['staff_time_in'];
	$staff_time_out = $_POST['staff_time_out'];
	$staff_bacis_salary = $_POST['staff_bacis_salary'];
	$staff_allowed_leaves = $_POST['staff_allowed_leaves'];
	$staff_qualification = $_POST['staff_qualification'];
	$designation_id = $_POST['designation_id'];
	$staff_updated_by = $_POST['staff_updated_by'];
    $insert = 
    "UPDATE
        `staff`
    SET
        `staff_name` = '$staff_name',
        `staff_status` = '$staff_status',
        `staff_spouse` = '$staff_spouse',
        `staff_phone` = '$staff_phone',
        `staff_cnic` = '$staff_cnic',
        `staff_address` = '$staff_address',
        `branch_id` = '$branch_idd',
        `staff_duty_hours` = '$staff_duty_hours',
        `staff_joining_date` = '$staff_joining_date',
        `staff_time_in` = '$staff_time_in',
        `staff_time_out` = '$staff_time_out',
        `staff_bacis_salary` = '$staff_bacis_salary',
        `staff_allowed_leaves` = '$staff_allowed_leaves',
        `staff_qualification` = '$staff_qualification',
        `designation_id` = '$designation_id',
        `other_person_name` = '$other_person_name',
        `other_person_phone` = '$other_person_phone',
        `relationship_id` = '$relationship_id',
        `other_person_address` = '$other_person_address',
        `hostel_name` = '$hostel_name',
        `hostel_warden_name` = '$hostel_warden_name',
        `hostel_warden_phone` = '$hostel_warden_phone',
        `hostel_address` = '$hostel_address',
        `staff_updated_at` = '$current_date',
        `staff_updated_by` = '$staff_updated_by'
    WHERE `staff_id` = '$staff_id' ";
	$run = mysqli_query($con, $insert);
	if ($run) 
	{	
        $activity_logs = "INSERT INTO `activity_logs`
        (`activity_log_id`, `user_id`, `activity_log_title`, `table_name`, `record_id`, `parameter_names`, `activity_log_new_value`, `activity_log_status`, `activity_logs_created_at`, `activity_log_location`, `ip_address`) 
        VALUES
        (NULL, '$hr_id', 'UPDATE STAFF DETAILS', 'staff', '$staff_id', 'ALL RECORD', 'MULTIPLE VALUES CHANGES', '1', '$current_date', '', '$ip_address')";
        mysqli_query($con, $activity_logs);
	    header('location:  show_staff.php?msg=success');
	}
	else
	{	
		 echo  $con->error;
		 echo '
			 <script> 
				location.replace("show_staff.php");
			 </script>';
	}
	exit(0);
}
?>
<?php include 'includes/head.php'; ?>
	<title>Update Staff - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo" onafterprint="window.location.href = 'dashboard.php'">
<div>

	<div class="col-md-12" style="text-align: center;background: lightgreen;">
	    <div class = "row">
    	    <div class = "col"><a class = "btn btn-info" href = "dashboard.php">Dashboard</a></div>
    	    <div class = "col">YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</div>
	    </div>
	</div>
	<div class="" style="margin: 10px 15px;">

		<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>
			
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Update Staff Data Form</h1></label>
			</div>
			<div class="col-md-12">

				<form method = "POST" action = "update_staff.php" autocomplete="off">

					<div class="row">
						<div class="col-md-12">
							<label for="staff_name"> Staff Id</label>
							<input readonly type="text" id="staff" name="staff" value = "<?php echo $staff_id; ?>" required class="form-control">
							<input type="hidden" id="staff_id" name="staff_id" value = "<?php echo $staff_id; ?>" required class="form-control">
						</div>
						<div class="col-md-3">
							<label for="staff_name"> Staff Name</label>
							<input type="text" id="staff_name" name="staff_name" value = "<?php echo $staff_name; ?>" required class="form-control">
						</div>
						<div class="col-md-3">
							<label for="staff_spouse"> S/O, D/O, W/O</label>
							<input type="text" id="staff_spouse" name="staff_spouse" value = "<?php echo $staff_spouse; ?>" required class="form-control">
						</div>			
						<div class="col-md-3">
							<label for="staff_phone"> Phone No</label>
							<input type="text" pattern="[0-9]{11}" id="staff_phone" name="staff_phone" value = "<?php echo $staff_phone; ?>" required class="form-control">
						</div>
						<div class="col-md-3">
							<label for="staff_cnic"> CNIC</label>
							<input type="text" pattern="[0-9]{13}" id="staff_cnic" name="staff_cnic" value = "<?php echo $staff_cnic; ?>" required class="form-control">
						</div>
						<div class="col-md-3">
							<label for = "staff_duty_hours">Duty Hours</label>
							<input type = "number" max = "24" min = "1" name = "staff_duty_hours" value = "<?php echo $staff_duty_hours; ?>" class = "form-control" required />
						</div>
						<div class="col-md-3">
							<label for = "staff_joining_date">Joining Date</label>
							<input type = "date" name = "staff_joining_date" value = "<?php echo $staff_joining_date; ?>" class = "form-control" required />
						</div>
						<div class="col-md-3">
							<label for = "staff_time_in">In-Time</label>
							<input type = "time" name = "staff_time_in" value = "<?php echo $staff_time_in; ?>" class = "form-control" required />
						</div>
						<div class="col-md-3">
							<label for = "staff_time_out">Out-Time</label>
							<input type = "time" name = "staff_time_out" value = "<?php echo $staff_time_out; ?>" class = "form-control" required />
						</div>
						<div class="col-md-3">
							<label for = "staff_qualification">Qualification</label>
							<input type="text" name="staff_qualification" value = "<?php echo $staff_qualification; ?>" class="form-control" />
						</div>
						<div class="col-md-3">
							<label>SELECT BRANCH</label>
							<select name="branch_idd" class="form-control" required>
							    <?php
							    $select = "SELECT * FROM branchs WHERE status = '1' ";
							    $run = mysqli_query($con, $select);
							    if(mysqli_num_rows($run) > 0)
							    {
							        echo '<option value = "">SELECT BRANCH</option>';
							        echo '<option SELECTED value = "0">ORGANIZATION STAFF</option>';
							        while($row = mysqli_fetch_array($run))
							        {
							            if($staff_branch_id == $row['id'])
							            {
        							        echo '<option SELECTED value = "'.$row['id'].'">'.$row['address'].'</option>';
							            }
							            else
							            {
        							        echo '<option value = "'.$row['id'].'">'.$row['address'].'</option>';
							            }
							        }
							    }
							    else
							    {
							        echo '<option value = "">PLEASE ADD BRANCH DATA</option>';
							    }
							    ?>
							</select>
						</div>

						<div class="col-md-3">
							<label>SELECT DESIGNATION</label>
							<select name="designation_id" class="form-control" required>
								<option>Select Designation</option>
								<?php
								$roles = mysqli_query($con, "SELECT * FROM `designations` WHERE designation_status = '1' ORDER BY designation_title");
								if (mysqli_num_rows($roles) > 0) {
									while ($row_role = mysqli_fetch_array($roles)) {
										$role_idd = $row_role['designation_id'];
										$role_namee = $row_role['designation_title'];
										if($staff_designation_id == $role_idd)
										{
    										echo '<option SELECTED value="'.$role_idd.'">'.$role_namee.'</option>';
										}
										else{
    										echo '<option value="'.$role_idd.'">'.$role_namee.'</option>';
										}
									}
								}
								?>
							</select>
						</div>
						<div class="col-md-3">
							<label for = "staff_address">Staff Address</label>
							<textarea name = "staff_address" class = "form-control" rows = "1"><?php echo $staff_address; ?></textarea>
						</div>
						<div class="col-md-12">
							<h2 align = "left"> OTHER CONTACT DETAIL</h2>
						</div>
						<div class="col-md-3">
							<label for="other_person_name"> PERSON NAME</label>
							<input type="text" name="other_person_name" value = "<?php echo $other_person_name; ?>" class="form-control" />
						</div>
						<div class="col-md-3">
							<label for="other_person_phone"> PERSON PHONE</label>
							<input type="text" pattern="[0-9]{11}" id="other_person_phone" value = "<?php echo $other_person_phone; ?>" name="other_person_phone" required class="form-control">
						</div>
						<div class="col-md-3">
							<label for="relationship_id"> RELASHINSHIP</label>
							<select name = "relationship_id" class = "form-control" required>
							    <?php
							    $select = "SELECT * FROM `relationships` WHERE `relationship_status` = '1' ORDER BY `relationships`.`relationship_title` ASC ";
							    $run = mysqli_query($con, $select);
							    if(mysqli_num_rows($run) > 0)
							    {
							        echo '<option value = "">SELECT ANY ONE</option>';
							        while($row = mysqli_fetch_array($run))
							        {
							            if($staff_relationship_id = $row['0'])
							            {
    							            echo '<option SELECTED value = "'.$row['0'].'">'.$row['1'].'</option>';
							            }
							            else{
    							            echo '<option value = "'.$row['0'].'">'.$row['1'].'</option>';
							            }
							        }
							        
							    }
							    else
							    {
							        echo '<option value = "">ADD RELATIONSHIP RECORD</option>';
							    }
							    ?>
							</select>
						</div>
						<div class="col-md-3">
							<label for="other_person_address"> ADDRESS</label>
							<textarea name = "other_person_address" class = "form-control" rows = "1"><?php echo $other_person_address; ?></textarea>
						</div>
						<div class="col-md-12">
							<h2 align = "left"> RESIDENCE DETAIL</h2>
						</div>
						<div class="col-md-3">
							<label for="hostel_name"> HOUSE/ HOSTEL NAME</label>
							<input type="text" name="hostel_name" value = "<?php echo $hostel_name; ?>" class="form-control" />
						</div>
						<div class="col-md-3">
							<label for="hostel_warden_name"> PERSON/ WARDEN NAME</label>
							<input type="text" name="hostel_warden_name" value = "<?php echo $hostel_warden_name; ?>" class="form-control" />
						</div>
						<div class="col-md-3">
							<label for="hostel_warden_phone">  PERSON/ WARDEN PHONE</label>
							<input type="text" pattern="[0-9]{11}" id="hostel_warden_phone" value = "<?php echo $hostel_warden_phone; ?>" name="hostel_warden_phone" required class="form-control">
						</div>
						<div class="col-md-3">
							<label for="hostel_address">  PERSON/ WARDEN ADDRESS</label>
							<textarea name = "hostel_address" class = "form-control" rows = "1"><?php echo $hostel_address; ?></textarea>
						</div>
						<div class="col-md-12">
							<h2 align = "left"> SALARY & LEAVES DETAIL</h2>
						</div>
						<div class="col-md-3">
							<label for="staff_bacis_salary"> BASIC SALARY</label>
							<input type="number" name="staff_bacis_salary" value = "<?php echo $staff_bacis_salary; ?>" class="form-control" />
						</div>
						<div class="col-md-3">
							<label for="staff_allowed_leaves"> ALLOWED LEAVES</label>
							<input type="number" name="staff_allowed_leaves" value = "<?php echo $staff_allowed_leaves; ?>" class="form-control" />
						</div>
						<div class = "col-md-3">
						    <label for = "staff_status">STAFF STATUS</label>
						    <select required name = "staff_status" class = "form-control">
						<?php
        			    $staff_query = "SELECT * FROM `statuses` ";
        			    $staff_run = mysqli_query($con, $staff_query);
        			    if(mysqli_num_rows($staff_run) > 0)
        			    {
        			        while($staff_row = mysqli_fetch_array($staff_run))
        			        {
        			            $staff_status_id = $staff_row['staff_status_id'];
        			            $staff_status_title = $staff_row['staff_status_title'];
        			            if($staff_status_id == $staff_status)
        			            {
            			            echo '<option SELECTED value = "'.$staff_status_id.'">'.$staff_status_title.'</option>';
        			            }
        			            else
        			            {
            			            echo '<option value = "'.$staff_status_id.'">'.$staff_status_title.'</option>';
        			            }
        			        }
        			    }?>
        			        </select>
        			    </div>
						<div class="col-md-3">
							<label for="staff_updated_by"> UPDATED BY</label>
							<input type="hidden" name="staff_updated_by" value = "<?php echo $hr_id; ?>" class="form-control" />
							<input type="text" readonly name="hr_name" value = "<?php echo $hr_name; ?>" class="form-control" />
						</div>
						<div class="col-md-12" style="margin: 20px 0px;">

							<input type="submit" name="update_staff" value="UPDATE STAFF" class="btn btn-success">

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
<?php mysqli_close($con); ?>