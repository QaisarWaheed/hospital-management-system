<?php
set_time_limit(120);

include 'includes/connect.php';

if (isset($_GET['save']) && $_GET['save'] != '') 
{
$select_item = "SELECT * FROM `item_by_doctor` WHERE user_id = ".$user_id." AND status ='1' ";
$count_item = mysqli_num_rows(mysqli_query($con, $select_item));
if($count_item >= 1)
{    
	if (isset($_GET['previous_tokan_no'])) 
	{
		$token_pre = $_GET['previous_tokan_no'];
		$patient_id = $_GET['patient_id'];
		$doctor_id = $_GET['doctor_id'];
		$tokan_type = (int) $_GET['tokan_payment'];
		$cash_received = (float) ($_GET['cash_received'] ?? 0);
		$cash = pharmecy_cart_amount_by_tokan_type($con, $user_id, $branch_id, $tokan_type);
		if ($cash <= 0) {
			$cash = (float) ($_GET['cash'] ?? 0);
		}
		$insert = "INSERT INTO `tokans`
		(`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`,`cash_received`, `user_id`, `previous_tokan_no`, `status`, `created`, `branch_id`) 
		VALUES 
		(NULL, '$patient_id','$doctor_id', '$tokan_type', '$cash', '$cash_received', '$user_id', '$token_pre', '1', '$current_date', '$branch_id')";
	}
		if (mysqli_query($con, $insert)) 
		{
		    $tokan_no = mysqli_insert_id($con);
			pharmecy_finalize_procedure_cart_items($con, $tokan_no, $user_id, $branch_id, $doctor_id, $tokan_type);
			pharmecy_insert_branch_pending_details($con, $tokan_no, $current_date, $branch_id, '1', array(
				'amount' => $cash,
				'user_id' => $user_id,
				'tokan_type_id' => $tokan_type,
			));
			header(
				'Location: branch_procedure_pendings.php?saved=1&tokan_no=' . (int) $tokan_no
			);
			exit;
		}
		header('Location: second_procedure_turn.php?search_tokan_no=' . urlencode((string) ($_GET['previous_tokan_no'] ?? '')) . '&save_error=1');
		exit;
}
else
{
    echo "INTERNET ERROR";
    exit(0);
}
}

if (isset($_GET['del_medicine']) && $_GET['del_medicine'] != '') 
{
	$del_id = $_GET['del_medicine'];
	$search_tokan_no = $_GET['search_tokan_no'];
	$delete = "DELETE FROM item_by_doctor WHERE id = '$del_id' AND user_id = '$user_id' AND branch_id = '$branch_id' AND `tokan_no` IS NULL ";
	$delete_branch_data = "DELETE FROM `item_register_to_branches` WHERE id = '$del_id' AND user_id = '$user_id' AND branch_id = '$branch_id' AND `tokan_no` IS NULL ";
		$reg_item_id = get_branch_item_id_from_select_by_doctor_id($del_id);
		$quantity = get_item_quantity_from_item_by_docotr_by_id($del_id);
		$get_available_quantity = get_register_item_quantity_from_item_id($reg_item_id);
		$new_quantity = $get_available_quantity + $quantity;
		$update = "UPDATE `item_register_to_branches` SET `quantity`= '$new_quantity' WHERE id = '$reg_item_id' ";
	if (mysqli_query($con, $delete)) 
	{
		mysqli_query($con, $update);

		echo '<script type="text/javascript">
		alert("Data Deleted Successfully...");
  location.replace("second_procedure_turn.php?search_tokan_no='.$search_tokan_no.'");
		</script>';
	}
}

if (isset($_GET['save_test'])) 
{
	$search_tokan_no = $_GET['search_tokan_no'];
	$reg_item_id = $_GET['reg_item_id'];
	$fix_dose = $_GET['fix_dose'];
	$dose = $_GET['dose'];
	$feed = $_GET['feed'];
	$days = $_GET['days'];
	if ($fix_dose == 0) 
	{
	$quantity = $dose * $days * $feed;
	}
	else
	{
			$quantity = $fix_dose;
	}

	$select_items = "SELECT id, purchase, poor, member, general, deserving, category_id FROM `items` WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE item_register_to_branches.branch_id = '$branch_id' AND id = '$reg_item_id')";
	$run_items = mysqli_query($con, $select_items);
	if(mysqli_num_rows($run_items) == 1)
	{
	    while($row_item = mysqli_fetch_array($run_items))
	    {
    	    $items_id = $row_item['id'];
    	    $purchase = $row_item['purchase'];
    	    $poor = $row_item['poor'];
    	    $member = $row_item['member'];
    	    $general = $row_item['general'];
    	    $category_id = $row_item['category_id'];
	    }
	}
	else
	{
	    $id = 0;
	    $purchase = 0;
	    $poor = 0;
	    $member = 0;
	    $general = 0;
	    $category_id = 0;
	}
	$check_item = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `item_by_doctor` WHERE item_id = '$reg_item_id' AND user_id = '$user_id' AND status = '1' "));
	$insert = "INSERT INTO `item_by_doctor`
	(`item_id`,      `dose`,  `feed`,  `days`,  `user_id`,  `branch_id`, `fix_dose`, `created`, `purchase_price`, `sale_price_general`, `sale_price_member`, `sale_price_poor`, `category_id`, `sale_quantity`) VALUES 
	('$reg_item_id', '$dose', '$feed', '$days', '$user_id','$branch_id', '$fix_dose', '$current_date', '$purchase', '$general', '$member', '$poor', '$category_id', '$quantity')";
	if($check_item == 0)
	{	
		$get_available_quantity = get_register_item_quantity_from_item_id($reg_item_id);
		$new_quantity = $get_available_quantity - $quantity;
		mysqli_query($con, "UPDATE `item_register_to_branches` SET `quantity`= '$new_quantity' WHERE id = '$reg_item_id' ");
		if (mysqli_query($con, $insert))		
		{ 
			?>
	<script type="text/javascript">
			  location.replace("second_procedure_turn.php?search_tokan_no=<?php echo $search_tokan_no; ?>");
			</script>
	<?php	}
	}
	else
	{ ?>
	<script type="text/javascript">
		alert("INFO: Data already addad");
	  location.replace("second_procedure_turn.php");	
	</script>
<?php }
}
include 'includes/head.php'; ?>
	<title>SECOND TURN - <?php echo $company_trademark; ?></title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
</head>

<body class="background_image_ycdo" onkeydown="return (event.keyCode != 116)">
<?php if(isset($_GET['search_tokan_no']) && $_GET['search_tokan_no'] != '')
{
	$search_tokan_no = $_GET['search_tokan_no'];
}
?>
<div>
	<div class="">
		<div class="row">
        	<div class="col-md-12" style="text-align: center;background: lightgreen;">
        		<label><h1><?php echo $company_name?> </h1></label>
        	</div>
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
        		<div class="col-md-1"></div>
        		<div class="col-md-5 btn btn-sm btn-outline-info">
        			<label class="">SELECTED TOKEN NO : <span><?php echo $search_tokan_no; ?></span></label>
        		</div>
        		<div>
        			
        		</div>
        		</div>			
        	</form>
        </div>
    </div>	
<form>
<div class="row">
<div class="col-md-12">
	<fieldset class="border p-2">
	<legend style="font-size: 14px;" class="w-auto">SELECT TEST OR MEDICINE OR PROCEDURE</legend>
	<div class="row">

	<div class="col-md-6">
	<input type="hidden" name="search_tokan_no" value="<?php echo $search_tokan_no; ?>" />
		<select required name="reg_item_id" id="select_item" placeholder="Pick Procedure" class="form-control bg-info">
			<option value="">Select  Procedure</option>
		    <?php echo branch_procedures_by_name(); ?>
		</select>

  <input type="hidden" name="dose" value="1" id="od">
  <input type="hidden" name="feed" value="1">
  <input type="hidden" name="days" value="1">
<br>
<div class="row">
	<div class="col-md-12">

  <div class="form-group row">
    <label for="fix_dose" class="col-sm-3 col-form-label">Fix / Not:</label>
    <div class="col-sm-9">
		<input class="form-control" id="fix_dose" type="number" name="fix_dose" value="0" min="1">
    </div>
  </div>		
	</div>
</div>
<div class="col-md-12" style="text-align: right;" >
	<input type="submit" onclick="myDisplayGoneAdd()" id = "add" name="save_test" value="ADD" class="btn btn-sm btn-primary">
	<input type="submit" name="clear" value="CLEAR" class="btn btn-sm btn-warning">
</div>


   	</div>
   	<div class="col-md-6">
   		<input type="hidden" id="tokan_no" name="tokan_no" value="<?php echo $search_tokan_no; ?>">
   		<select id="mySelect" ondblclick="del_medicine();" class="form-control" size="6">
   			<?php echo medicine_selected(); ?>
   		</select>
   	</div>

   </div>
</fieldset>

</div>

</div>

</form>

<form onsubmit="return checknumber(this);">
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
		<label>Operation By</label>
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

   		<textarea readonly required rows="1" style="resize: none;" readonly id="cash" name="cash" class="form-control">0</textarea>
   	</div>
<?php } ?>


   	<div class="col-md-7" style="font-size: 15px;">
   		<label>Amount Token Type</label><br>

   		<!--<input disabled="disabled" onclick="myFunction101()" type="radio" id="deserving"  name="tokan_payment" value="101">-->
   		<!--<label for="deserving">Deserving</label>-->
   		
   		<!--<input onclick="myFunction102()" type="radio" id="poor" required name="tokan_payment" value="102">-->
   		<!--<label for="poor">Poor</label>-->
   		
   		<!--<input onclick="myFunction103()" type="radio" id="member"  name="tokan_payment" value="103">-->
   		<!--<label for="member">YCDO / Member</label>-->
   		
   		<input onclick="myFunction104()" type="radio" id="general"  name="tokan_payment" value="104">
   		<label for="general">General</label>
   		
   	</div>


   	<div class="col-md-3">
   		<label>Cash Received</label>
   		<input type="number" min="0" name="cash_received" class="form-control" required>
   	</div>

	<div class="col-md-2">
		<br>
<?php
$select_item = "SELECT * FROM `item_by_doctor` WHERE user_id = ".$user_id." AND status = '1' ";
$count_item = mysqli_num_rows(mysqli_query($con, $select_item));
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

</body>

</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type="text/javascript">
      $(document).ready(function () {
  $('#select_item').selectize({
      sortField: 'text'
  });
  $(".alert").alert();
});
</script>
<script type="text/javascript">
function del_medicine() 
{
	var x = document.getElementById("mySelect").value;
	var y = document.getElementById("tokan_no").value;
	window.open('second_procedure_turn.php?del_medicine=' + x + '&search_tokan_no=' + y, '_self');
}	
</script>

<script>
function setCashAmount(value) {
  var cashEl = document.getElementById("cash");
  if (cashEl) {
    cashEl.value = value;
  }
}
function myFunction101() {
	//DESERVING
  setCashAmount(<?php echo (int) get_amount(101); ?>);
}
function myFunction102() {
	//POOR
  setCashAmount(<?php echo (int) get_amount(102); ?>);
}
function myFunction103() {
	//MEMBER
  setCashAmount(<?php echo (int) get_amount(103); ?>);
}
function myFunction104() {
	//GENERAL
  setCashAmount(<?php echo (int) get_amount(104); ?>);
}
document.addEventListener('DOMContentLoaded', function () {
  if (document.getElementById('general')) {
    document.getElementById('general').checked = true;
    myFunction104();
  }
});
</script>

<script type = "text/javascript" >  
    function preventBack() { window.history.forward(); }  
    setTimeout("preventBack()", 0);  
    window.onunload = function () { null };  
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