<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) 
{
    // start image
// 	$file=$_FILES['file'];
// 	$fileName=$_FILES['file']['name'];	
// 	$fileTmpName=$_FILES['file']['tmp_name'];
// 	$fileExt = explode('.', $fileName);	//
// 	$fileActualExt = strtolower(end($fileExt));	
// 	$fileNameNew = uniqid('' , true).".".$fileActualExt;
// 	$staff_image = '../images/staff/'.$fileNameNew;
// 	$staff_image_href = $fileNameNew;
    //end image  
    
    $other_person_name = $_POST['other_person_name'];
    $other_person_address = $_POST['other_person_address'];
    $other_person_phone = $_POST['other_person_phone'];
    $relationship_id = $_POST['relationship_id'];
    $hostel_name = $_POST['hostel_name'];
    $hostel_warden_name = $_POST['hostel_warden_name'];
    $hostel_warden_phone = $_POST['hostel_warden_phone'];
    $hostel_address = $_POST['hostel_address'];
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
	$staff_bacis_salary = 0;
	$staff_allowed_leaves = 0;
	$staff_qualification = $_POST['staff_qualification'];
	$designation_id = $_POST['designation_id'];
	$staff_status = 1;

    $insert = "INSERT INTO `staff`(`staff_id`, `staff_name`, `staff_spouse`, `staff_phone`, `staff_cnic`, `staff_address`, `branch_id`, `staff_duty_hours`, `staff_joining_date`, `staff_time_in`, `staff_time_out`, `staff_bacis_salary`, `staff_allowed_leaves`, `staff_qualification`, `designation_id`, `staff_status`, `staff_created`,other_person_name, other_person_address, other_person_phone, relationship_id, hostel_name, hostel_warden_name, hostel_warden_phone, hostel_address, `staff_image_href`) 
	VALUES 
	( NULL,'$staff_name','$staff_spouse','$staff_phone','$staff_cnic','$staff_address','$branch_idd','$staff_duty_hours','$staff_joining_date','$staff_time_in','$staff_time_out','$staff_bacis_salary','$staff_allowed_leaves','$staff_qualification','$designation_id','$staff_status','$current_date','$other_person_name','$other_person_address','$other_person_phone','$relationship_id','$hostel_name','$hostel_warden_name','$hostel_warden_phone','$hostel_address', '$staff_image_href')";
	$run = mysqli_query($con, $insert);
	if ($run) 
	{
// 		move_uploaded_file($fileTmpName, $staff_image);		
	    $staff_id = mysqli_insert_id($con);
	    header('location: print_staff.php?staff_id='.$staff_id);
	}
	else
	{	
		 echo  $con->error;
// 		 echo '
// 			 <script> 
// 				location.replace("add_staff.php");
// 			 </script>';
	}
	exit(0);
}
?>
<?php include 'includes/head.php'; ?>
	<title>Add Staff - <?php echo $company_trademark; ?></title>
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
			
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Add Staff Data Form</h1></label>
			</div>
			<div class="col-md-12">

				<form method = "POST" action = "add_staff.php" autocomplete="off">

					<div class="row">
						<div class="col-md-3">
							<label for="staff_name"> Enter Staff Name</label>
							<input type="text" id="staff_name" name="staff_name" required class="form-control">
						</div>
						<div class="col-md-3">
							<label for="staff_spouse"> Enter S/O, D/O, W/O</label>
							<input type="text" id="staff_spouse" name="staff_spouse" required class="form-control">
						</div>			
						<div class="col-md-3">
							<label for="staff_phone"> Phone No</label>
							<input type="text" pattern="[0-9]{11}" id="staff_phone" name="staff_phone" required class="form-control">
						</div>
						<div class="col-md-3">
							<label for="staff_cnic"> CNIC</label>
							<input type="text" pattern="[0-9]{13}" id="staff_cnic" name="staff_cnic" required class="form-control">
						</div>			
						<!--<div class="col-md-3">-->
						<!--	<label for = "staff_image_href" class="mb-2 text-muted" for="email">Image</label>-->
						<!--	<input type="file" capture name="file" accept="image/*" id = "staff_image_href" class="form-control" required />-->
						<!--</div>-->
						<div class="col-md-3">
							<label for = "staff_duty_hours">Duty Hours</label>
							<input type = "number" max = "24" min = "1" name = "staff_duty_hours" class = "form-control" required />
						</div>
						<div class="col-md-3">
							<label for = "staff_joining_date">Joining Date</label>
							<input type = "date" name = "staff_joining_date" class = "form-control" required />
						</div>
						<div class="col-md-3">
							<label for = "staff_time_in">In-Time</label>
							<input type = "time" name = "staff_time_in" class = "form-control" required />
						</div>
						<div class="col-md-3">
							<label for = "staff_time_out">Out-Time</label>
							<input type = "time" name = "staff_time_out" class = "form-control" required />
						</div>
						<div class="col-md-3">
							<label for = "staff_qualification">Qualification</label>
							<input type="text" name="staff_qualification" class="form-control" />
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
							        echo '<option value = "0">ORGANIZATION STAFF</option>';
							        while($row = mysqli_fetch_array($run))
							        {
    							        echo '<option value = "'.$row['id'].'">'.$row['address'].'</option>';
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
										echo '<option value="'.$role_idd.'">'.$role_namee.'</option>';
									}
								}
								?>
							</select>
						</div>
						<div class="col-md-3">
							<label for = "staff_address">Staff Address</label>
							<textarea name = "staff_address" class = "form-control" rows = "1"></textarea>
						</div>
						<div class="col-md-12">
							<h2 align = "left"> OTHER CONTACT DETAIL</h2>
						</div>
						<div class="col-md-3">
							<label for="other_person_name"> PERSON NAME</label>
							<input type="text" name="other_person_name" class="form-control" />
						</div>
						<div class="col-md-3">
							<label for="other_person_phone"> PERSON PHONE</label>
							<input type="text" pattern="[0-9]{11}" id="other_person_phone" name="other_person_phone" required class="form-control">
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
    							        echo '<option value = "'.$row['0'].'">'.$row['1'].'</option>';
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
							<textarea name = "other_person_address" class = "form-control" rows = "1"></textarea>
						</div>
						<div class="col-md-12">
							<h2 align = "left"> RESIDENCE DETAIL</h2>
						</div>
						<div class="col-md-3">
							<label for="hostel_name"> HOUSE/ HOSTEL NAME</label>
							<input type="text" name="hostel_name" class="form-control" />
						</div>
						<div class="col-md-3">
							<label for="hostel_warden_name"> PERSON/ WARDEN NAME</label>
							<input type="text" name="hostel_warden_name" class="form-control" />
						</div>
						<div class="col-md-3">
							<label for="hostel_warden_phone">  PERSON/ WARDEN PHONE</label>
							<input type="text" pattern="[0-9]{11}" id="hostel_warden_phone" name="hostel_warden_phone" required class="form-control">
						</div>
						<div class="col-md-3">
							<label for="hostel_address">  PERSON/ WARDEN ADDRESS</label>
							<textarea name = "hostel_address" class = "form-control" rows = "1"></textarea>
						</div>
						<div class="col-md-12" style="margin: 20px 0px;">

							<input type="submit" name="save" value="SAVE NEW STAFF" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
							<a href="show_staff.php" class="btn btn-info btn-sm"> Show Staff List</a>
							<a href="attendance_record_monthly.php" class="btn btn-success btn-sm"> Show Staff Attendance</a>
							<a href="attendance_record_monthly_register.php" class="btn btn-primary btn-sm"> Attendance Register</a>
							<a href="attendance_record_monthly_account.php" class="btn btn-warning btn-sm"> Attendance For Account</a>
						</div>
					</div>

				</form>
			</div>

		</div>

	</div>

</div>


</body>
</html>
<?php mysqli_close($con); ?>