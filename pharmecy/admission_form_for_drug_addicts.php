<?php include 'includes/connect.php'; 
$msg_token = '';
$show_data = '0';
if(isset($_GET['token_no']) && $_GET['token_no'] != '')
{
    $token_no = $_GET['token_no'];
}
if(isset($_POST['token_no']) && $_POST['token_no'] != '')
{
    $token_no = $_POST['token_no'];
}
if($token_no > 0)
{
    $select = "SELECT * FROM `tokans` INNER JOIN patients ON tokans.patient_id = patients.id WHERE tokans.`id` = '$token_no' ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run) == 1)
    {
        while($row = mysqli_fetch_array($run))
        {
            $cnic = $row['cnic'];
            $dob = $row['dob'];
            if(is_null($cnic))
            {
                $show_data = '0';
                $msg_token =  '<div class="alert alert-danger" role="alert"><strong>WARNING:</strong> this is not a complete registeration token.</div>';
            }
            else
            {
                $show_data = '1';
            }
                $name = $row['name'];
                $last_name = $row['last_name'];
                $phone = $row['phone'];
                $age = $row['age'];
                $address = $row['address'];
                $gender = $row['gender'];
                $created = $row['created'];
                $ref_name = $row['ref_name'];
                $ref_phone = $row['ref_phone'];
                $dob = $row['dob'];
        }
    }
}

if (isset($_POST['save'])) 
{
	$token_no = $_POST['token_no'];
	$insert = "INSERT INTO `drug_addict_patient_admisssions`
	(`drug_addict_patient_admisssion_date`, `token_no`, `expected_date_discharge`, `date_of_birth`, `next_checkup_date`, `height`, `weight`, `drug_period`, `blood_group`, `category_of_drug_patient_id`, `type_of_drug_id`, `brought_patient_in_ospital_via_id`, `doctor_id`, `past_treatment_history`, `past_medical_history`, `per_day_fee`, `drug_addict_patient_admisssion_created_by`, `drug_addict_patient_admisssion_created_at`, `drug_addict_patient_admisssion_status`, `branch_id`, `ip_address`) 
	VALUES 
	('".$_POST['admission_date']."', '".$_POST['token_no']."', '".$_POST['edd']."', '".$_POST['dob']."', '".$_POST['ncd']."', '".$_POST['height']."', '".$_POST['weight']."', '".$_POST['drug_period']."', '".$_POST['blood_group']."', '".$_POST['category_of_drug_patient']."', '".$_POST['type_of_drug_id']."', '".$_POST['brought_patient_in_ospital_via']."', '".$_POST['doctor_id']."', '".$_POST['past_treatment_history']."', '".$_POST['past_medical_history']."', '".$_POST['per_day_fee']."', '$user_id', '$current_date', '1', '$branch_id', '$ip_address') ";
	if (mysqli_query($con, $insert)) 
	{
    	header('location: open_drug_addict_register.php');
    	exit(0);
	}
	else
	{
    	header('location: open_drug_addict_register.php');
	    exit(0);
	}
	header('location: open_drug_addict_register.php?msg=error');
	exit(0);
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
        	<div class="col-md-12 background_whitesmoke d-print-none">
        		<?php 
        		include 'navigation_top.php'; 
        		?>
        	</div>
			<div class="col-md-12 d-print-none">
			    <?php include 'includes/navigation_drug_patient_register.php'; ?>
		    </div>
			<div class="col-md-12">
					<div class="row">
					    <div class="col-md-12" style="text-align: center;">
            				<label><h1>ADMISSION FORM (DRUG REHABILITITATION PATIENTS)</h1></label>
        				</div>
						<div class="col-md-12">
					    <form method = "GET" autocomplete="off">
							<label>Token No:</label>
							<div><?php echo $msg_token; ?></div>
						    <div class = "row">
						        <div class = "col-md-9">
        							<input type="number" min = "1" value = "<?php if($token_no > 0)echo $token_no; ?>" name="token_no" class="form-control" required>
						        </div>
						        <div class = "col-md-3">
        							<input type = "submit" value = "SEARCH" class = "btn-info" />
						        </div>
						    </div>
					    </form>
						</div>
					</div>
    				<form action = "admission_form_for_drug_addicts.php" method = "POST" autocomplete="off" onsubmit="if(submitted) return false; submitted = true; return true">
					<div class="row">
						<div class="col-md-3">
							<label>Patient Name</label>
							<input value = "<?php echo $name; ?>" type="text" name="last_name" class="form-control" readonly>
						</div>
						<div class="col-md-3">
							<label>Patient Father</label>
							<input value = "<?php echo $last_name; ?>" pattern="" type="text" name="last_name" class="form-control" readonly>
						</div>
						<div class="col-md-3">
							<label>Patient Address</label>
							<input value = "<?php echo $address; ?>" type="text" readonly name="address" class="form-control">
						</div>
						
						<div class="col-md-3">
							<label>Patient CNIC</label>
							<input pattern="[0-9]{13}" value = "<?php echo $cnic; ?>" type="text" name="cnic" class="form-control" readonly>
						</div>
						<div class="col-md-3">
							<label>Patient Phone</label>
							<input pattern="[0-9]{11}" value = "<?php echo $phone; ?>" type="text" name="phone" readonly class="form-control">
						</div>
						<div class="col-md-3">
							<label> Age</label>
							<input type="text" readonly value = "<?php echo $age; ?>" id = "age" name="age" class="form-control">
						</div>

						<div class="col-md-3">
							<label>Guardian Name</label>
							<input type="text" pattern="[a-z A-Z].{2,}" value = "<?php echo $ref_name; ?>" name="ref_name" class="form-control" readonly>
						</div>

						<div class="col-md-3">
							<label>Guardian Phone</label>
							<input type="text" pattern="[0-9]{11}" value = "<?php echo $ref_phone; ?>" name="ref_phone" class="form-control" readonly>
						</div>
					<?php if($show_data == 1){ ?>
						<div class="col-md-3">
							<label> Admission Date</label>
							<input value = "<?php echo $token_no; ?>" type="hidden" name="token_no">
							<input type="date" min = "<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" name="admission_date" class="form-control">
						</div>

						<div class="col-md-3">
							<label title = "Expected Date of Discharge"> E.D.D</label>
							<input type="date" min = "<?php echo date('Y-m-d'); ?>" value="<?php echo date('Y-m-d'); ?>" name="edd" class="form-control">
						</div>
						<div class="col-md-3">
							<label title = "Date of Birth"> DOB</label>
							<input type="date" required id = "dob" name="dob" class="form-control">
						</div>
						<div class="col-md-3">
							<label title = ""> Next Chheckup Date</label>
							<input type="date" min = "<?php echo date('Y-m-d'); ?>" required id = "ncd" name="ncd" class="form-control">
						</div>     
						<div class="col-md-3">
							<label>Drug Period</label>
							<input type="text" maxlength = "30" min = "1" name="drug_period" placeholder = "in years" class="form-control" required>
						</div>
						<div class="col-md-3">
							<label>Height</label>
							<input type="number" step = "0.001" min = "1" max = "200" name="height" placeholder = "height in inches" class="form-control" required>
						</div>
						<div class="col-md-3">
							<label>Weight</label>
							<input type="number" step = "0.001" min = "1" max = "200" placeholder = "weight in (KG's)" name="weight" class="form-control" required>
						</div>
						<div class="col-md-3">
							<label>Blood Group</label>
							<select name="blood_group" required class="form-control">
								<option value="">Select Blood Group</option>
								<option value="A+">A+</option>
								<option value="AB+">AB+</option>
								<option value="B+">B+</option>
								<option value="O+">O+</option>
								<option value="A-">A-</option>
								<option value="AB-">AB-</option>
								<option value="B-">B-</option>
								<option value="O+">O+</option>
							</select>
						</div>
						<div class="col-md-3">
							<label>Category of Drug Patient</label>
							<select name="category_of_drug_patient" required class="form-control">
								<option value="">Select Patient Type</option>
								<option value="1">DRUG ADDICT</option>
								<option value="2">PSYCHO</option>
								<option value="3">DRUG ADDICT & PSYCHO</option>
								<option value="4">Other</option>
							</select>
						</div>
						<div class="col-md-3">
							<label>Type of Drug </label>
							<select name="type_of_drug_id" required class="form-control">
								<option value="">Select Drug Type</option>
								<?php
								$select = "SELECT * FROM `type_of_druges` WHERE type_of_drug_status = '1' ";
								$run = mysqli_query($con, $select);
								if(mysqli_num_rows($run) > 0)
								{
								    while($row = mysqli_fetch_array($run))
								    {
								        echo '<option value="'.$row['type_of_drug_id'].'">'.$row['main_type_of_drug'].' ('.$row['sub_type_of_drug'].')</option>';
								    }
								}
								?>
							</select>
						</div>
						<div class="col-md-3">
							<label>Brought  Patient In Hospital via</label>
							<select name="brought_patient_in_ospital_via" required class="form-control">
								<option value="1">Self</option>
								<option value="2">Guardian</option>
								<option value="3">Police</option>
								<option value="4">Other</option>
							</select>
						</div>                  
						<div class="col-md-3">
                            <label>DOCTOR</label>
                            <select name="doctor_id" required class="form-control">
                            <option value="">Select doctor</option>
                            <?php
                            $get_doctor = mysqli_query($con, "SELECT * FROM users WHERE role_id = '3' AND branch_id = '$branch_id' AND status = 1 ORDER BY u_name ");
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
						<div class="col-md-3">
							<label>Past Treatment History</label>
							<textarea name="past_treatment_history" class = "form-control" rows = "3" required></textarea>
						</div>
						<div class="col-md-3">
							<label>Past & Present Medical History</label>
							<textarea name="past_medical_history" class = "form-control" rows = "3" required></textarea>
						</div>
						<div class="col-md-6">
							<label>Per Day Fee/ Services Charges </label>
							<select size = "3" name="per_day_fee" required class="form-control">
								<option value="">Select Category</option>
								<option value="0">0 - zakat</option>
								<option value="100">100</option>
								<option value="200">200</option>
								<option value="300">300</option>
								<option value="500">500</option>
								<option value="600">600</option>
								<option value="700">700</option>
								<option value="800">800</option>
								<option value="900">900</option>
								<option value="1000">1000</option>
								<option value="1100">1100</option>
								<option value="1200">1200</option>
								<option value="1300">1300</option>
								<option value="1500">1500</option>
								<option value="1600">1600</option>
								<option value="1700">1700</option>
								<option value="1800">1800</option>
								<option value="1900">1900</option>
								<option value="2000">2000</option>
								<option value="2500">2500</option>
								<option value="3000">3000</option>
								<option value="4000">4000</option>
								<option value="5000">5000</option>
								<option value="6000">6000</option>
								<option value="8000">8000</option>
								<option value="10000">10000</option>
								<option value="12000">12000</option>
								<option value="15000">15000</option>
							</select>
						</div>
						<div class="col-md-12" style="margin: 20px 0px;">
							<input type="submit" id="add" name="save" value="SAVE ADMISSION" class="btn btn-success">
							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
						</div>
					<?php } ?>
					</div>

				</form>
			</div>		

	</div>
	
</div>

</div>

</body>
</html>
<script>
// document.getElementById('myForm').addEventListener('submit', 
// function() 
// {
//     document.getElementById('submitButton').disabled = true;
// });
</script>