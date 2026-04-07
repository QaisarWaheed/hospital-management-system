<?php 
include 'includes/connect_second_turn.php'; 
$select_item = "SELECT * FROM `item_by_doctor` WHERE user_id = ".$user_id." AND status = '1' ";
$count_item = mysqli_num_rows(mysqli_query($con, $select_item));

include 'includes/head.php'; ?>
	<title>SECOND TURN - <?php echo $company_trademark; ?></title>
<!--<script src="js/jquery.min.js"></script>-->
<!--<script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>-->
<!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css.map" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />-->
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
            <div class = "row">
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Patient Medicine</h1></label>
			</div>
<div class="col-md-12">
	<form name="search" method="get">
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-4 btn btn-outline-primary">
				<label>NEXT TOKEN NO:<?php echo next_tokan_no().' / '.date('y'); ?></label>
			</div>
		<div class="col-md-4">
			<input formaction="search" type="number" placeholder="Token" name="search_tokan_no" max="<?php echo intval(next_tokan_no())-1; ?>" required min="1" class="form-control">
		</div>	
		<div class="col-md-2">
			<input type="submit" name="" value="SEARCH">
		</div>
		<div>
			
		</div>
		</div>			
	</form>
</div>
	<div class="col-md-12">			
<form method = "POST" action = "action_save_test.php">
	
<div class="row">
<div class="col-md-12">
	<fieldset class="border p-2">
	<legend style="font-size: 14px;" class="w-auto">SELECT TEST OR MEDICINE OR PROCEDURE</legend>
	<div class="row">

	<div class="col-md-6">
		<select class = "form-control bg-primary text-dark" required name="reg_item_id">
			<option value="">Select Test, Medicine, Procedure...</option>
		    <?php echo branch_medicines_by_name(); ?>
		</select>

  <label>DOSE:</label>

  <input type="radio" checked name="dose" value="1" id="od">
  <label for="od" title="ONCE A DOSE">OD</label>

  <input type="radio" name="dose" value="2" id="bd">
  <label for="bd" title="TWO DOSES">BD</label>

  <input type="radio" name="dose" value="3" id="tds">
  <label for="tds" title="THREE DOSES">TDS</label>

<br>
<div class="row">
	<div class="col-md-12">
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-2 col-form-label">Feed:</label>
    <div class="col-sm-10">
		<select class="form-control" name="feed" required>
			<option value="0.5">Half</option>
			<option selected value="1">One</option>
			<option value="2">Two</option>
			<option value="3">Three</option>
			<option value="4">Four</option>
			<option value="5">Five</option>
			<option value="6">Six</option>
			<option value="7">Seven</option>
		</select>
    </div>
  </div>  
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-2 col-form-label">Days:</label>
    <div class="col-sm-4">
		<input class="form-control" type="number" name="days" value="1" min="1">
    </div>
    <label for="fix_dose" class="col-sm-3 col-form-label">Fix / Not:</label>
    <div class="col-sm-3">
		<input class="form-control" id="fix_dose" type="number" name="fix_dose" value="0" min="0">
    </div>
  </div>		
	</div>
</div>
<div class="col-md-12" style="text-align: right;" >
	<!--<input onclick="myDisplayGoneAdd()" id="add" type="submit" name="save_test" value="ADD" class="btn btn-sm btn-primary">-->
	<input type = "hidden" name = "token_data" value = "1" />
	<input id="add" type="submit" name="save_test" value="ADD" class="btn btn-sm btn-primary">
	<input id="clear" type="reset" name="clear" value="CLEAR" class="btn btn-sm btn-warning">
</div>


   	</div>
   	<div class="col-md-6">
   			<?php echo medicine_selected_list(); ?>
   	</div>

   </div>
</fieldset>

</div>

</div>

</form>

<form onsubmit="return checknumber(this);" action = "action_save.php">
<div class="row">
<?php
if (isset($_GET['search_tokan_no']) && $_GET['search_tokan_no'] != '') 
{ 
	$tokan_no = $_GET['search_tokan_no'];
	$select_tokan = "SELECT * FROM tokans WHERE id = '$tokan_no' ";
	$run_tokan = mysqli_query($con, $select_tokan);
	if (mysqli_num_rows($run_tokan) == 1) 
	{
		while ($row_tokan = mysqli_fetch_array($run_tokan)) 
		{
			$doctor_id = $row_tokan['doctor_id'];
			$patient_id = $row_tokan['patient_id'];
			$select_patient = "SELECT * FROM patients WHERE id = '$patient_id' ";
			$run_patient = mysqli_query($con, $select_patient);
			if (mysqli_num_rows($run_patient) == 1) 
			{
				while ($row_patient = mysqli_fetch_array($run_patient)) 
				{
					$name = $row_patient['name'];
					$age = $row_patient['age'];
					$gender = $row_patient['gender'];
				}
			}

		}
	}
	?>
	<div class="col-md-3">
		<label>Patient Name</label>
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="previous_tokan_no" value="<?php echo $tokan_no; ?>">
		<input readonly type="text" class="form-control" value="<?php echo $name; ?>">
	</div>
	<div class="col-md-2">
		<label> Age</label>
		<input readonly type="number" value="<?php echo $age; ?>" min="0" class="form-control">
	</div>
	<div class="col-md-2">
		<label> Gender</label>
		<select readonly required class="form-control">
<?php 
if ($gender == 1) {echo '<option value="1"> Female</option>';}
elseif ($gender == 2) {echo '<option value="2"> Male</option>';}
else {echo '<option value="3"> Other</option>';}
?>
		</select>
	</div>
	<div class="col-md-3">
		<label>Checked By</label>
		<select name="doctor_id" required class="form-control">
		<?php 	$get_doctor = mysqli_query($con, "SELECT * FROM users WHERE role_id = '3' AND branch_id = '$branch_id' AND status = 1 ORDER BY u_name  ");
		if (mysqli_num_rows($get_doctor) > 0) 
		{	
			while ($row_doctor = mysqli_fetch_array($get_doctor)) 
		    {
		    	$option_doctor_id = $row_doctor['id'];
		    	if ($doctor_id == $option_doctor_id) 
		    	{
		      echo '<option selected value="'.$row_doctor['id'].'">'.$row_doctor['u_name'].'</option>';
		    	}
		    	else
		    	{
		      echo '<option value="'.$row_doctor['id'].'">'.$row_doctor['u_name'].'</option>';
		    	}

		    }
		} ?>
		</select>
	</div>
   	<div class="col-md-2">
   		<label>Cash</label>

   		<textarea readonly required rows="1" style="resize: none;" readonly id="cash" name="cash" class="form-control">0</textarea>
   	</div>
<?php }
else
{ ?>
	<div class="col-md-3">
		<label>Patient Name</label>
		<input type="text" name="name" class="form-control">
	</div>
	<div class="col-md-2">
		<label> Age</label>
		<input type="number" min="0" name="age" class="form-control">
	</div>
	<div class="col-md-2">
		<label> Gender</label>
		<select name="gender" required class="form-control">
			<option value=""> Gender</option>
			<option value="1">Female</option>
			<option value="2">Male</option>
			<option value="3">Other</option>
		</select>
	</div>
	<div class="col-md-3">
		<label>Checked By</label>
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
		} ?>
		</select>
	</div>
   	<div class="col-md-2">
   		<label>Cash</label>

   		<textarea readonly required rows="1" style="resize: none;" readonly id="cash" name="cash" class="form-control"><?php echo get_selected_amount(); ?></textarea>
   	</div>
<?php } ?>


   	<div class="col-md-7" style="font-size: 15px;">
   		<label>Amonut Tokan Type</label><br>

   		<!--<input disabled="disabled" onclick="myFunction101()" type="radio" id="deserving"  name="tokan_payment" value="101">-->
   		<!--<label for="deserving">Deserving</label>-->
   		
   		<input onclick="myFunction102()" type="radio" id="poor" required name="tokan_payment" value="102">
   		<label for="poor">Poor</label>
   		
   		<input onclick="myFunction103()" type="radio" id="member"  name="tokan_payment" value="103">
   		<label for="member">YCDO / Member</label>
   		
   		<input onclick="myFunction104()" type="radio" id="general"  name="tokan_payment" value="104">
   		<label for="general">General</label>
   		
   	</div>


   	<div class="col-md-3">
   		<label>Cash Received</label>
   		<input type="number" min = "0" max = "<?php echo get_selected_amount()+20; ?>" name="cash_received" class="form-control" required>
   	</div>

	<div class="col-md-2">
		<br>
<?php
if($count_item >= 1)
{ ?>
        <input type="submit" id="save" onclick="myDisplayGoneSave()" value="SAVE" name="save" class="btn btn-sm btn-primary">
<?php } ?>
		
		<input type="reset" value="CLEAR" name="clear" class="btn btn-sm btn-warning">
	</div>

</div>

</form>

</div>

		</div>
		</div>
	</div>
</div>

</body>

</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>

<script>
function myFunction101() {
	//DESERVING
  document.getElementById("cash").innerHTML = <?php echo intval(get_amount(101)); ?>;
  // document.getElementById("tokan_get1").innerHTML = 8;
}
function myFunction102() {
	//POOR
  document.getElementById("cash").innerHTML = <?php echo intval(get_amount(102)); ?>;
  // document.getElementById("tokan_get1").innerHTML = 9;
}
function myFunction103() {
	//MEMBER
  document.getElementById("cash").innerHTML = <?php echo intval(get_amount(103)); ?>;
  // document.getElementById("tokan_get1").innerHTML = 1;
}
function myFunction104() {
	//GENERAL
  document.getElementById("cash").innerHTML = <?php echo intval(get_amount(104)); ?>;
  // document.getElementById("tokan_get1").innerHTML = 2;
}
</script>

<script type = "text/javascript" >  
    function preventBack() { window.history.forward(); }  
    setTimeout("preventBack()", 0);  
    window.onunload = function () { null };  
</script> 

<script type = "text/javascript" >
	function checknumber(theForm) {   
 if (parseInt(theForm.cash.value) > (parseInt(theForm.cash_received.value)) ) 
    { 
    alert('enter the correct amount');
            return false;
     } 
     return true;
            
}
</script> 
<script>
function myDisplayGoneSave() {
  document.getElementById("save").style.display = "none";
}
</script>