<?php include 'includes/connect.php';
include_once 'includes/rehab_fingerprint.php';
$rehab_next_patient_display = next_patient_id();
if (isset($_POST['save'])) 
{
    $name = $_POST['name'];
    $age = $_POST['age'];
    $phone = $_POST['phone'];
    $patient_reg_date = $_POST['registeration_date'];
    $cash = $_POST['cash'];
    $tokan_type = $_POST['tokan_type'];
    $doctor_id = $_POST['doctor_id'];
    $gender = $_POST['gender'];

    if (is_rehabilitation_branch($branch_id) && !empty($_POST['rehab_existing_patient_id'])) {
        $patient_id = (int) $_POST['rehab_existing_patient_id'];
        $chk = mysqli_query($con, "SELECT id FROM patients WHERE id = '$patient_id' LIMIT 1");
        if (!$chk || mysqli_num_rows($chk) !== 1) {
            echo '<script>alert("Invalid returning patient. Please identify again."); history.back();</script>';
            exit(0);
        }
        $name_e = mysqli_real_escape_string($con, $name);
        $phone_e = mysqli_real_escape_string($con, $phone);
        mysqli_query($con, "UPDATE patients SET name='$name_e', age='$age', phone='$phone_e', gender='$gender' WHERE id='$patient_id'");
        $tokan_no = next_tokan_no();
        $run2 = mysqli_query($con, "INSERT INTO `tokans`
	(`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`, `cash_received`, `user_id`, `created`, `branch_id`) 
	VALUES 
	('$tokan_no', '$patient_id','$doctor_id', '$tokan_type', '$cash', '$cash', '$user_id', '$current_date', '$branch_id')");
        if (!$run2) {
            echo '<script>alert("TOKEN SAVE FAILED"); history.back();</script>';
            exit(0);
        }
        header('location: print_tokan.php?tokan_no='.$tokan_no);
        exit(0);
    }

    if (is_rehabilitation_branch($branch_id)) {
        $fp_left = trim($_POST['fp_thumb_left'] ?? '');
        $fp_right = trim($_POST['fp_thumb_right'] ?? '');
        if (strlen($fp_left ?? '') < REHAB_FP_MIN_TEMPLATE_LEN || strlen($fp_right ?? '') < REHAB_FP_MIN_TEMPLATE_LEN) {
            echo '<script>alert("Rehabilitation branch (new patient): both thumb templates are required. Use identification first for returning patients, or complete both thumb fields."); history.back();</script>';
            exit(0);
        }
    }
    $last_token = last_tokan_no();
    $last_patient_name = get_patient_name_by_token_no($last_token);
    $last_patient_age = get_patient_age_by_token_no($last_token);
    $last_token_user_id = get_token_user_id($last_token);
	$tokan_no = next_tokan_no();
	$patient_id = next_patient_id();
	if($last_patient_name != $name && $last_patient_age != $age && $age != 0)
	{
	$run = mysqli_query($con, "INSERT INTO `patients`
	(`id`, `name` ,`age` , `gender`, `created`, `phone`) 
	VALUES 
	('$patient_id','$name','$age','$gender', '$current_date', '$phone')");

	$run2 = mysqli_query($con, "INSERT INTO `tokans`
	(`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`, `cash_received`, `user_id`, `created`, `branch_id`) 
	VALUES 
	('$tokan_no', '$patient_id','$doctor_id', '$tokan_type', '$cash', '$cash', '$user_id', '$current_date', '$branch_id')");
	if (is_rehabilitation_branch($branch_id)) {
		save_patient_fingerprints($con, $patient_id, $fp_left, $fp_right);
	}
	header('Location: print_tokan.php?tokan_no=' . (int) $tokan_no);
	exit;
    }
    else
    {
        echo '<script>alert("SOMETHING WENT WRONG...CHECK INTERNET CONNECTION...");location.replace("dashboard.php");</script>';
        exit;
    }
}
?>
<?php include 'includes/head.php'; ?>
	<title>Patient Registeration - <?php echo $company_trademark; ?></title>
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

				<form method = "POST" autocomplete="off" data-default-reg-id="<?php echo (int) $rehab_next_patient_display; ?>" onreset="if (typeof rehabRegistrationOnReset === 'function') { setTimeout(rehabRegistrationOnReset, 0); }">

					<div class="row">
            			<div class="col-md-12" style="text-align: center;">
            				<label><h1>Patient Registeration Form</h1></label>
            			</div>
						<?php if (is_rehabilitation_branch($branch_id)) {
						    rehab_fingerprint_identify_block($rehab_next_patient_display);
						} ?>
						<div class="col-md-3">
							<label> Registeration #</label>
							<input type="text" value="<?php echo (int) $rehab_next_patient_display; ?>" name="registeration_no" id="reg_display_id" readonly class="form-control">
						</div>
						<div class="col-md-3">
							<label> Registeration Date</label>
							<input type="date" readonly value="<?php echo date('Y-m-d'); ?>" name="registeration_date" class="form-control">
						</div>

						<div class="col-md-3">
							<label>Patient Name</label>
							<input pattern="[a-z A-Z].{2,}" type="text" name="name" id="reg_name" class="form-control" required>
						</div>
						<div class="col-md-3">
							<label>Patient Age</label>
							<input type="number" name="age" id="reg_age" class="form-control" required>
						</div>
						<div class="col-md-3">
							<label>Patient Phone</label>
							<input pattern="[0-9]{11}" type="text" name="phone" id="reg_phone" required class="form-control">
						</div>
						<div class="col-md-3">
							<label>Patient Gender</label>
							<select name="gender" id="reg_gender" required class="form-control">
								<option value="">Select Gender</option>
								<option value="1">Female</option>
								<option value="2">Male</option>
								<option value="3">Other</option>
							</select>
						</div>				
						<div class="col-md-3">
							<label>DOCTOR</label>
					<select name="doctor_id" id="reg_doctor_id" required class="form-control">
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

						<div class="col-md-9">
							<fieldset>
								<legend>
									<label><strong>OPD. Checkup Type</strong></label>
								</legend>
							
							<?php if($branch_id == 9 || $branch_id == 23){ ?>
							<div class="row">
								<div class="col-md-1">
									<input onclick="myFunction1()"  id="poor" type="radio" name="tokan_type" value="1">
								</div>
								<div class="col-md-2">
									<label for="poor" style="cursor: pointer;">Poor</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction3()" checked id="general" type="radio" name="tokan_type" value="2">
								</div>
								<div class="col-md-2">
									<label for="general" style="cursor: pointer;">General</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction2()" id="member" type="radio" name="tokan_type" value="3">
								</div>
								<div class="col-md-2">
									<label for="member" style="cursor: pointer;">Private</label>
								</div>
								<?php if($branch_id == 23){ ?>
								<div class="col-md-1">
									<input onclick="myFunction4()"  id="cons_poor" type="radio" name="tokan_type" value="7">
								</div>
								<div class="col-md-2">
									<label for="cons_poor" style="cursor: pointer;">CONS-POOR</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction5()" id="urgent" type="radio" name="tokan_type" value="4">
								</div>
								<div class="col-md-2">
									<label for="urgent" style="cursor: pointer;">CONS-GENERAL</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction9()" id="urgent" type="radio" name="tokan_type" value="9">
								</div>
								<div class="col-md-2">
									<label for="cons_poor" style="cursor: pointer;">CONS-PRIVATE</label>
								</div>
								<?php }if($branch_id == 9){ ?>
								<div class="col-md-1">
									<input onclick="myFunction6()"  id="cons_poor" type="radio" name="tokan_type" value="7">
								</div>
								<div class="col-md-2">
									<label for="cons_general" style="cursor: pointer;">CONS-POOR</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction7()" id="cons_member" type="radio" name="tokan_type" value="6">
								</div>
								<div class="col-md-2">
									<label for="cons_member" style="cursor: pointer;">CONS-PRIVATE</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction8()" id="cons_general" type="radio" name="tokan_type" value="5">
								</div>
								<div class="col-md-2">
									<label for="cons_general" style="cursor: pointer;">CONS-GENERAL</label>
								</div>
								<?php } ?>
							</div>
							<?php }
							else if($branch_id == 10)
							{ ?>
							<div class="row">
								<div class="col-md-1">
									<input onclick="myFunction1()"  id="poor" type="radio" name="tokan_type" value="1">
								</div>
								<div class="col-md-2">
									<label for="poor" style="cursor: pointer;">Poor</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction3()" id="member" type="radio" name="tokan_type" value="3">
								</div>
								<div class="col-md-2">
									<label for="member" style="cursor: pointer;">Private</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction2()" checked id="general" type="radio" name="tokan_type" value="2">
								</div>
								<div class="col-md-2">
									<label for="general" style="cursor: pointer;">General</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction4()" id="urgent" type="radio" name="tokan_type" value="4">
								</div>
								<div class="col-md-2">
									<label for="urgent" style="cursor: pointer;">Emergency</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction7()" id="consultant1" type="radio" name="tokan_type" value="7">
								</div>
								<div class="col-md-3">
									<label for="consultant1" style="cursor: pointer;">Cons.-POOR</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction5()" id="consultant3" type="radio" name="tokan_type" value="5">
								</div>
								<div class="col-md-3">
									<label for="consultant3" style="cursor: pointer;">Cons - General</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction6()" id="consultant2" type="radio" name="tokan_type" value="6">
								</div>
								<div class="col-md-3">
									<label for="consultant2" style="cursor: pointer;">Cons - Member</label>
								</div>
							</div>
							<?php }else if($branch_id == 15)
							{ ?>
							<div class="row">
								<div class="col-md-1">
									<input onclick="myFunction1()"  id="poor" type="radio" name="tokan_type" value="1">
								</div>
								<div class="col-md-2">
									<label for="poor" style="cursor: pointer;">Poor</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction2()" checked id="general" type="radio" name="tokan_type" value="2">
								</div>
								<div class="col-md-2">
									<label for="general" style="cursor: pointer;">General</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction3()" id="member" type="radio" name="tokan_type" value="3">
								</div>
								<div class="col-md-2">
									<label for="member" style="cursor: pointer;">Private</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction4()" id="urgent" type="radio" name="tokan_type" value="4">
								</div>
								<div class="col-md-2">
									<label for="urgent" style="cursor: pointer;">Private</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction11()"  id="cons_poor" type="radio" name="tokan_type" value="7">
								</div>
								<div class="col-md-2">
									<label for="cons_poor" style="cursor: pointer;">Cons-P</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction12()" id="cons_member" type="radio" name="tokan_type" value="6">
								</div>
								<div class="col-md-2">
									<label for="cons_member" style="cursor: pointer;">Cons-M</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction13()" id="cons_general" type="radio" name="tokan_type" value="5">
								</div>
								<div class="col-md-2">
									<label for="cons_general" style="cursor: pointer;">Cons-G</label>
								</div>
							</div>
							<?php }
							else
							{ ?>
							<div class="row">
								<div class="col-md-1">
									<input onclick="myFunction1()"  id="poor" type="radio" name="tokan_type" value="1">
								</div>
								<div class="col-md-2">
									<label for="poor" style="cursor: pointer;">Poor</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction3()" id="member" type="radio" name="tokan_type" value="3">
								</div>
								<div class="col-md-2">
									<label for="member" style="cursor: pointer;">Private</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction2()" checked id="general" type="radio" name="tokan_type" value="2">
								</div>
								<div class="col-md-2">
									<label for="general" style="cursor: pointer;">General</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction4()" id="urgent" type="radio" name="tokan_type" value="4">
								</div>
								<div class="col-md-2">
									<label for="urgent" style="cursor: pointer;">Emergency</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction11()"  id="cons_poor" type="radio" name="tokan_type" value="7">
								</div>
								<div class="col-md-2">
									<label for="cons_poor" style="cursor: pointer;">Cons-P</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction12()" id="cons_member" type="radio" name="tokan_type" value="6">
								</div>
								<div class="col-md-2">
									<label for="cons_member" style="cursor: pointer;">Cons-M</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction13()" id="cons_general" type="radio" name="tokan_type" value="5">
								</div>
								<div class="col-md-2">
									<label for="cons_general" style="cursor: pointer;">Cons-G</label>
								</div>
								<div class="col-md-1">
									<input onclick="myFunction14()" id="cons_general_ground" type="radio" name="tokan_type" value="5">
								</div>
								<div class="col-md-2">
									<label for="cons_general_ground" style="cursor: pointer;">Cons-G2</label>
								</div>
							</div>
							<?php } ?>
							</fieldset>
						</div>
						<div class="col-md-3">
							<label>Cash Received</label>
							<textarea name="cash" class="form-control" rows="1" style="resize: none;" readonly id="cash"><?php if($branch_id == 9 || $branch_id == 23){echo 70;}elseif($branch_id == 10){echo 200;}elseif($branch_id == 15){echo 50;}else{echo 30;}?>
							</textarea>
						</div>
						<?php if (is_rehabilitation_branch($branch_id)) { ?>
						<div class="col-md-12" id="rehab_fp_enrollment_wrap" style="display:none;margin-top:12px;">
							<?php rehab_fingerprint_new_patient_enrollment_only(); ?>
						</div>
						<?php } ?>
						<div class="col-md-12" style="margin: 0px 0px;">
							<input type="submit" onclick="myDisplayGoneAdd()" id="add" name="save" value="SAVE TOKEN" class="btn btn-success">
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
<?php
if($branch_id == 9 || $branch_id == 23)
{
?>
<script>
function myFunction1() 
{
  document.getElementById("cash").innerHTML = 50;
}
// MEMBER
function myFunction2() 
{
  document.getElementById("cash").innerHTML = 100;
}
// GENERAL
function myFunction3() 
{
  document.getElementById("cash").innerHTML = 70;
}
// con. P
function myFunction4() 
{
  document.getElementById("cash").innerHTML = 200;
}
// EMERGENCY
function myFunction5() 
{
  document.getElementById("cash").innerHTML = 300;
}
// PRIVATE
function myFunction9() 
{
  document.getElementById("cash").innerHTML = 400;
}
// con. P
function myFunction6() 
{
  document.getElementById("cash").innerHTML = 250;
}
// con. M
function myFunction7() 
{
  document.getElementById("cash").innerHTML = 350;
}
// Consult G
function myFunction8() 
{
  document.getElementById("cash").innerHTML = 450;
}
<?php
}
elseif($branch_id == 10)
{
?>
<script>
function myFunction1() 
{
  document.getElementById("cash").innerHTML = 100;
}
// GENERAL
function myFunction2() 
{
  document.getElementById("cash").innerHTML = 200;
}
// MEMBER
function myFunction3() 
{
  document.getElementById("cash").innerHTML = 150;
}
// EMERGENCY
function myFunction4() 
{
  document.getElementById("cash").innerHTML = 250;
}
function myFunction7() 
{
  document.getElementById("cash").innerHTML = 300;
}
// con. P
function myFunction8() 
{
  document.getElementById("cash").innerHTML = 300;
}
// con. M
function myFunction6() 
{
  document.getElementById("cash").innerHTML = 400;
}
// Consult G
function myFunction5() 
{
  document.getElementById("cash").innerHTML = 500;
}
<?php
}
elseif($branch_id == 15)
{
?>
<script>
function myFunction1() 
{
  document.getElementById("cash").innerHTML = 10;
}
// GENERAL
function myFunction2() 
{
  document.getElementById("cash").innerHTML = 70;
}
// MEMBER
function myFunction3() 
{
  document.getElementById("cash").innerHTML = 50;
}
// EMERGENCY
function myFunction4() 
{
  document.getElementById("cash").innerHTML = 100;
}
function myFunction7() 
{
  document.getElementById("cash").innerHTML = 300;
}
// con. P
function myFunction11() 
{
  document.getElementById("cash").innerHTML = 300;
}
// con. M
function myFunction12() 
{
  document.getElementById("cash").innerHTML = 400;
}
// Consult G
function myFunction13() 
{
  document.getElementById("cash").innerHTML = 500;
}
<?php
}
else
{
?>
<script>
function myFunction1() 
{
  document.getElementById("cash").innerHTML = 10;
}
// GENERAL
function myFunction2() 
{
  document.getElementById("cash").innerHTML = 30;
}
// MEMBER
function myFunction3() 
{
  document.getElementById("cash").innerHTML = 50;
}
// EMERGENCY
function myFunction4() 
{
  document.getElementById("cash").innerHTML = 100;
}
function myFunction7() 
{
  document.getElementById("cash").innerHTML = 100;
}
// con. M
function myFunction6() 
{
  document.getElementById("cash").innerHTML = 700;
}
// Consult G
function myFunction5() 
{
  document.getElementById("cash").innerHTML = 1000;
}
// con. P
function myFunction11() 
{
  document.getElementById("cash").innerHTML = 100;
}
// con. M
function myFunction12() 
{
  document.getElementById("cash").innerHTML = 150;
}
// Consult G
function myFunction13() 
{
  document.getElementById("cash").innerHTML = 200;
}
// Consult General
function myFunction14() 
{
  document.getElementById("cash").innerHTML = 300;
}
<?php
}
?>
</script>
<script type = "text/javascript" >  
    function preventBack() { window.history.forward(); }  
    setTimeout("preventBack()", 0);  
    window.onunload = function () { null };  
</script> 
<script type="text/javascript">
        // setTimeout(function () { window.close(); }, 120000);
</script>
<script>
function myDisplayGone() {
  document.getElementById("clear").style.display = "none";
}
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