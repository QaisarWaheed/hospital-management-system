<?php include 'includes/connect.php';
include_once 'includes/rehab_fingerprint.php';
if (isset($_POST['save'])) {
	if (is_rehabilitation_branch($branch_id)) {
		$fp_left = trim($_POST['fp_thumb_left'] ?? '');
		$fp_right = trim($_POST['fp_thumb_right'] ?? '');
		if (strlen($fp_left) < REHAB_FP_MIN_TEMPLATE_LEN || strlen($fp_right) < REHAB_FP_MIN_TEMPLATE_LEN) {
			echo '<script>alert("Rehabilitation branch: both thumb templates are required. Capture left and right thumb on the reader, then save."); history.back();</script>';
			exit(0);
		}
	}
	$tokan_no = next_tokan_no();
	$id = next_patient_id();
	$name = $_POST['name'];
	$last_name = $_POST['last_name'];
	$cnic = $_POST['cnic'];
	$phone = $_POST['phone'];
	$age = $_POST['age'];
	$dob = $_POST['dob'];
	$address = $_POST['address'];
	$doctor_id = $_POST['doctor_id'];
	$gender = $_POST['gender'];
	$ref_name = $_POST['ref_name'];
	$ref_phone = $_POST['ref_phone'];
	$tokan_type = $_POST['tokan_type'];
	$cash = $_POST['cash'];
	$run = mysqli_query($con, "INSERT INTO `patients`
	(`id`, `name`, `last_name`, `cnic`, `phone`, `age`, `address`, `gender`, `ref_name`, `ref_phone`, `created`, `dob`) 
	VALUES 
	('$id', '$name', '$last_name', '$cnic', '$phone', '$age', '$address', '$gender', '$ref_name', '$ref_phone', '$current_date', '$dob')");

	$run4 = mysqli_query($con, "INSERT INTO `tokans`
	(`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`, `cash_received`, `user_id`, `created`, `branch_id`) 
	VALUES 
	('$tokan_no', '$id','$doctor_id', '$tokan_type', '$cash', '$cash', '$user_id', '$current_date', '$branch_id')");	
	if (!$run4) 
	{
		echo 'ERROR: '.$con->error;
	}
	else
	{
		if (is_rehabilitation_branch($branch_id)) {
			save_patient_fingerprints($con, $id, $fp_left, $fp_right);
		}
    	header('Location: print_tokan.php?tokan_no=' . (int) $tokan_no);
		exit;
	}
}
?>
<?php include 'includes/head.php'; ?>
	<title>Patient Complete Registeration - <?php echo $company_trademark; ?></title>
    <script type="text/javascript" src="js/jquery-3.6.0.min.js"></script> 
</head>

<body class="background_image_ycdo" onkeydown="return (event.keyCode != 116)">
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
			    

				<form method = "POST" autocomplete="off">

					<div class="row">
					    <div class="col-md-12" style="text-align: center;">
            				<label><h1>Patient Registeration (COMPLETE)</h1></label>
        				</div>
					    <div class="col-md-12" style="text-align: left;">
            				    <div class="alert alert-danger"><label>THIS TURN TOKEN USED ONLY FOR PROCEDURE</label></div>
        				</div>

						<div class="col-md-3">
							<label> Registeration #</label>
							<input type="text" value="<?php echo next_patient_id(); ?>" name="registeration_no" readonly class="form-control">
						</div>
						<div class="col-md-3">
							<label> Registeration Date</label>
							<input type="date" readonly value="<?php echo date('Y-m-d'); ?>" name="registeration_date" class="form-control">
						</div>

						<div class="col-md-3">
							<label>Patient Name</label>
							<input pattern="[a-z A-Z].{2,}" type="text" name="name" class="form-control" required>
						</div>
						<div class="col-md-3">
							<label>Patient Father</label>
							<input pattern="[a-z A-Z].{2,}" type="text" name="last_name" class="form-control" required>
						</div>

						<div class="col-md-6">
							<label>Patient Address</label>
							<input type="text" required name="address" class="form-control">
						</div>
						
						<div class="col-md-3">
							<label>Patient CNIC</label>
							<input pattern="[0-9]{13}" type="text" name="cnic" class="form-control" required>
						</div>
						<div class="col-md-3">
							<label>Patient Phone</label>
							<input pattern="[0-9]{11}" type="text" name="phone" required class="form-control">
						</div>

						<div class="col-md-2">
							<label>Patient Gender</label>
							<select name="gender" required class="form-control">
								<option value="">Select Gender</option>
								<option value="1">Female</option>
								<option value="2">Male</option>
								<option value="3">Other</option>
							</select>
						</div>
						<div class="col-md-2">
							<label> DOB</label>
							<input type="date" required id = "dob" name="dob" class="form-control">
						</div>
						<div class="col-md-2">
							<label> Age</label>
							<input type="text" required id = "age" name="age" class="form-control">
						</div>

						<div class="col-md-3">
							<label>Patient Ref Name</label>
							<input type="text" pattern="[a-z A-Z].{2,}" name="ref_name" class="form-control" required>
						</div>

						<div class="col-md-3">
							<label>Patient Ref Phone</label>
							<input type="text" pattern="[0-9]{11}" name="ref_phone" class="form-control" required>
						</div>
						<div class="col-md-2">
							<label>Cash Received</label>
							<textarea name="cash" class="form-control" rows="1" style="resize: none;" readonly id="cash">10</textarea>
						</div>
                        <div class="col-md-2">
                            <label>DOCTOR</label>
                            <select name="doctor_id" required class="form-control">
                            <option value="">Select doctor</option>
                            <?php
                            $get_doctor = mysqli_query($con, "SELECT * FROM users WHERE role_id = '3' AND branch_id = '$branch_id' AND status = 1 ORDER BY u_name  ");
                            if (mysqli_num_rows($get_doctor) > 0) 
                            {
                            while ($row_doctor = mysqli_fetch_array($get_doctor)) 
                            {
                            echo '<option value="'.$row_doctor['id'].'">'.$row_doctor['u_name'].'</option>';
                            }
                            }
                            ?>
                            </select>
                        </div>
						<div class="col-md-8" id="detail">
							<label>TOKEN TYPE</label>
							<div class="row">
								<div class="col-md-1">
									<input checked onclick="myFunction1()"  id="poor" type="radio" name="tokan_type" value="1">
								</div>
								<div class="col-md-2">
									<label for="poor" style="cursor: pointer;">Poor</label>
								</div>
							</div>
						</div>
						<?php if (is_rehabilitation_branch($branch_id)) { rehab_fingerprint_enrollment_block(); } ?>
						<div class="col-md-12" style="margin: 20px 0px;">

							<input type="submit" onclick="myDisplayGoneAdd()" id="add" name="save" value="SAVE TOKEN" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">

							<a href="patient_registeration.php" class="btn btn-primary">PATIENT REGISTERATION</a>
						</div>

					</div>

				</form>
			</div>		

	</div>
	
</div>

</div>

</body>
</html>
<?php
if($branch_id == 10)
{
?>
<script>
// Poor
function myFunction1() 
{
  document.getElementById("cash").innerHTML = 100;
}
// General
function myFunction2() 
{
  document.getElementById("cash").innerHTML = 200;
}
MEMBER
function myFunction3() 
{
  document.getElementById("cash").innerHTML = 300;
}
// EMERGENDY
function myFunction4() 
{
  document.getElementById("cash").innerHTML = 500;
}
// CONS P
function myFunction7() 
{
  document.getElementById("cash").innerHTML = 700;
}
// CONS G
function myFunction5() 
{
  document.getElementById("cash").innerHTML = 1000;
}
// Cons Member
function myFunction6() 
{
  document.getElementById("cash").innerHTML = 700;
}
</script>
<?php
}
else
{
?>
<script>
// Poor
function myFunction1() 
{
  document.getElementById("cash").innerHTML = 10;
}
// General
function myFunction2() 
{
  document.getElementById("cash").innerHTML = 30;
}
MEMBER
function myFunction3() 
{
  document.getElementById("cash").innerHTML = 50;
}
// EMERGENDY
function myFunction4() 
{
  document.getElementById("cash").innerHTML = 100;
}
// CONS P
function myFunction7() 
{
  document.getElementById("cash").innerHTML = 100;
}
// CONS G
function myFunction5() 
{
  document.getElementById("cash").innerHTML = 300;
}
// Cons Member
function myFunction6() 
{
  document.getElementById("cash").innerHTML = 200;
}
</script>
<?php
}
?>
<script type="text/javascript">
        // setTimeout(function () { window.close(); }, 500000);
</script>
<script>
function myDisplayGoneAdd() {
  document.getElementById("add").style.display = "none";
}
</script> 

<script>
function myDisplayGoneSave() {
  document.getElementById("save").style.display = "none";
}
</script>