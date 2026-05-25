<?php 
include 'includes/connect_second_turn.php'; 
$amount_array = get_select_amount_array();
$select_item = "SELECT * FROM `items_by_doctor` WHERE `branch_id` = '$branch_id' AND `user_id` = '$user_id' AND `status` = '1' ";
$run_select_item = mysqli_query($con, $select_item);
$count_item = mysqli_num_rows($run_select_item);

if (isset($_GET['save']) && $_GET['save'] != '') 
{
if($count_item >= 1)
{
	if (isset($_GET['previous_tokan_no'])) 
	{
		$previous_tokan_no = $_GET['previous_tokan_no'];
		$patient_id = $_GET['patient_id'];
		$doctor_id = $_GET['doctor_id'];
		$tokan_type = $_GET['tokan_payment'];
		$cash = $_GET['cash'];
		$cash_received = $_GET['cash_received'];
		$insert = "INSERT INTO `tokans`
		(`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`,`cash_received`, `user_id`, `previous_tokan_no`, `created`, `branch_id`) 
		VALUES 
		( NULL, '$patient_id','$doctor_id', '$tokan_type', '$cash', '$cash_received', '$user_id', '$previous_tokan_no', '$current_date', '$branch_id')";
	}
	else
	{
		$patient_id = next_patient_id();
		$name = $_GET['name'];
		$age = $_GET['age'];
		$gender = $_GET['gender'];
		$tokan_type = $_GET['tokan_payment'];
		$cash = $_GET['cash'];
		$cash_received = $_GET['cash_received'];
		$doctor_id = $_GET['doctor_id'];
			$run2 = mysqli_query($con, "INSERT INTO `patients`
			(`id`, `name` ,`age` , `gender`, `created`) 
			VALUES 
			('$patient_id','$name','$age','$gender', '$current_date')");

		$insert = "INSERT INTO `tokans`
		(`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`,`cash_received`, `user_id`, `previous_tokan_no`, `created`, `branch_id`) 
		VALUES 
		(NULL , '$patient_id','$doctor_id', '$tokan_type', '$cash', '$cash_received', '$user_id', NULL, '$current_date', '$branch_id')";

	}
			if (mysqli_query($con, $insert)) 
			{
            	    $tokan_no = mysqli_insert_id($con);
					mysqli_query($con, "INSERT INTO `branch_pending_details`(`token_no`, `created`, `branch_id`) VALUES ('$tokan_no', '$current_date', '$branch_id')");
				    $ref_name = $_GET['ref_name'];
				    $ref_phone = $_GET['ref_phone'];
				    $recommended_by = $_GET['recommended_by'];
				    $return_date = $_GET['return_date'];
				    $pending_amount = $cash - $cash_received;
					mysqli_query($con, "INSERT INTO `branch_daily_pending_details`
					(`token_no`,  `ref_name`, `ref_phone`, `recommended_by`, `amount`, `return_date`, `created`) 
					VALUES 
					('$tokan_no', '$ref_name', '$ref_phone', '$recommended_by', '$pending_amount', '$return_date', '$current_date')");

				while ($row_select_item = mysqli_fetch_array($run_select_item)) 
				{
            	    $del_record_id = $row_select_item['id'];
                	    $purchase = $row_select_item['purchase_price'];
                	    $poor = $row_select_item['sale_price_poor'];
                	    $member = $row_select_item['sale_price_member'];
                	    $general = $row_select_item['sale_price_general'];
                	    $category_id = $row_select_item['category_id'];					
    	            $reg_item_id = $row_select_item['item_id'];
					$dose = $row_select_item['dose'];
					$feed = $row_select_item['feed'];
					$days = $row_select_item['days'];
					$fix_dose = $row_select_item['fix_dose'];
                	if ($fix_dose == 0) 
                	{
                	    $quantity = $dose * $days * $feed;
                	}
                	else
                	{
            			$quantity = $fix_dose;
                	}	
                	$sale_price = 0;
                	$sale_quantity = $quantity;
                	if($tokan_type == 102)
                	{
                	    $sale_price = $poor*$sale_quantity;
                	}
                	elseif($tokan_type == 103)
                	{
                	    $sale_price = $member*$sale_quantity;
                	}
                	else
                	{
                	    $sale_price = $general*$sale_quantity;
                	}
					mysqli_query($con, "INSERT INTO `item_by_doctor`
					(`tokan_no`,`item_id`, `dose`,  `feed`,  `days`,  `user_id`,  `branch_id`, `fix_dose`, `created`, `doctor_id`, `status`, `purchase_price`, `sale_price_general`, `sale_price_member`, `sale_price_poor`, `category_id`, `tokan_type_id`, `sale_price`, `sale_quantity`) 
					VALUES 
					('$tokan_no','$reg_item_id', '$dose', '$feed', '$days', '$user_id','$branch_id', '$fix_dose', '$current_date', '$doctor_id', '2', '$purchase', '$general', '$member', '$poor', '$category_id', '$tokan_type', '$sale_price', '$sale_quantity')");
    				mysqli_query($con, "DELETE FROM `items_by_doctor` WHERE id = '$del_record_id' AND user_id = '$user_id' ");
				}
				// mysqli_query($con, "DELETE FROM `items_by_doctor` WHERE branch_id = '$branch_id' AND user_id = '$user_id' AND tokan_no IS NULL ");
			}
?>
<script>
  window.open(<?php echo json_encode(ycdo_absolute_url('print_medicine_slip.php', 'tokan_no=' . rawurlencode((string) $tokan_no))); ?>, "_blank", "toolbar=no,scrollbars=no,resizable=no,top=500,left=500,width=400,height=400,status=no");
  location.replace("dashboard.php");
</script>
<?php
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
	$delete = "DELETE FROM items_by_doctor WHERE id = '$del_id' AND user_id = '$user_id' AND branch_id = '$branch_id' AND `tokan_no` IS NULL ";
		$reg_item_id = get_branch_item_id_from_items_by_doctor_id($del_id);
		$quantity = get_item_quantity_from_item_by_docotor_id($del_id);
		$get_available_quantity = get_register_item_quantity_from_item_id($reg_item_id);
		$new_quantity = $get_available_quantity + $quantity;
		$update = "UPDATE `item_register_to_branches` SET `quantity`= quantity+$quantity WHERE id = '$reg_item_id' ";
	if (mysqli_query($con, $delete)) 
	{
		mysqli_query($con, $update);
        header('location: second_turn_pending.php');
        exit(0);
	}	
}

if (isset($_GET['save_test'])) 
{
	$reg_item_id = $_GET['reg_item_id'];
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
	$check_item = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `items_by_doctor` WHERE item_id = '$reg_item_id' AND user_id = '$user_id' AND status = '1' "));
	$check_test = mysqli_num_rows(mysqli_query($con, "SELECT id FROM `items` WHERE category_id = '2' AND `id` IN (SELECT item_id FROM `item_register_to_branches` WHERE id = '$reg_item_id') "));
	$insert = "INSERT INTO `items_by_doctor`
	(`item_id`,      `dose`,  `feed`,  `days`,  `user_id`,  `branch_id`, `fix_dose`, `created`, `purchase_price`, `sale_price_general`, `sale_price_member`, `sale_price_poor`, `category_id`) VALUES 
	('$reg_item_id', '$dose', '$feed', '$days', '$user_id','$branch_id', '$fix_dose', '$current_date', '$purchase', '$general', '$member', '$poor', '$category_id')";
	if($check_item == 0)
	{	
		$get_available_quantity = get_register_item_quantity_from_item_id($reg_item_id);
		$new_quantity = $get_available_quantity - $quantity;
		mysqli_query($con, "UPDATE `item_register_to_branches` SET `quantity`= '$new_quantity' WHERE id = '$reg_item_id' ");
		if (mysqli_query($con, $insert))		{ 
			if ($check_test == 1) {
				mysqli_query($con, "INSERT INTO `tests_by_doctor`(`test_id`, `user_id`, `created`) VALUES('$reg_item_id', '$user_id', '$current_date')");
			}
			?>
	<script type="text/javascript">
			  location.replace("second_turn_pending.php");
			</script>
	<?php	}
	}
	else
	{ ?>
	<script type="text/javascript">
		alert("INFO: Data already addad");
	  location.replace("second_turn_pending.php");	
	</script>
<?php }
}
include 'includes/head.php'; ?>
	<title>SECOND TURN PENDING - <?php echo $company_trademark; ?></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js" integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.bootstrap3.min.css" integrity="sha512-cNefX8/Vd+UJbeYHzwdRZYEHI1K5Wj+gCdaK4R767/8SFhqMaHHg881hZONXpq4ainln9e330TalryDPKysm2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body class="background_image_ycdo" onkeydown="return (event.keyCode != 116)">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>
<div id="loadingSpinner" style="display: none;">
    <div class = "container">
        <div class = "row p-5 g-5">
            <div class = "col text-center">
                <div aria-busy="true" aria-describedby="progress-bar">
                    <h2>LOADING...</h2>
                    <p>Please Wait Untill Processing Completed.</p>
                    <p>Data Processing...</p>
                </div>
                <progress id="progress-bar" aria-label="Content loading…"></progress>    
                
            </div>
        </div>        
    </div>
</div>
<div>

	<div class="">

		<div class="row" style="margin: 0px;" id = "submitBody">

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
<form onsubmit="showProgress(); return true;">
	
<div class="row">
<div class="col-md-12">
	<fieldset class="border p-2">
	<legend style="font-size: 14px;" class="w-auto">SELECT TEST OR MEDICINE OR PROCEDURE</legend>
	<div class="row">

	<div class="col-md-6">
		<select required name="reg_item_id" id="select_item" placeholder="Pick Test, Medicine Or Procedure" class="form-control">
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
			<option value="8">Eight</option>
			<option value="9">Nine</option>
			<option value="10">Ten</option>
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
	<input type="submit" onclick="myDisplayGoneAdd()" id="add" name="save_test" value="ADD" class="btn btn-sm btn-primary">
	<input type="submit" name="clear" value="CLEAR" class="btn btn-sm btn-warning">
</div>


   	</div>
   	<div class="col-md-6">
   			<?php echo medicine_select_list_pending(); ?>
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
					$cnic = $row_patient['cnic'];
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
		<label>Patient CNIC</label>
		<input required readonly  type="text" name="cnic" class="form-control" value="<?php echo $cnic; ?>">
	</div>
	<div class="col-md-3">
		<label> Refference Name</label>
		<input required type="text" name="ref_name" class="form-control">
	</div>
	<div class="col-md-2">
		<label> Refference Phone</label>
		<input required type="text" name="ref_phone" class="form-control">
	</div>
	<div class="col-md-2">
		<label>Recommended By</label>
		<input required type="text" name="recommended_by" class="form-control">
	</div>
	<div class="col-md-3">
		<label>Return Date</label>
		<input required type="date" name="return_date" class="form-control">
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
		<?php 	$get_doctor = mysqli_query($con, "SELECT * FROM users WHERE role_id = '3' AND branch_id = '$branch_id' ");
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
		<input required readonly type="text" name="name" class="form-control">
	</div>
	<div class="col-md-2">
		<label>Patient CNIC</label>
		<input required readonly  type="text" name="cnic" class="form-control">
	</div>
	<div class="col-md-3">
		<label>Refference Name</label>
		<input required readonly type="text" name="ref_name" class="form-control">
	</div>
	<div class="col-md-2">
		<label>Refference Phone</label>
		<input required readonly type="text" name="ref_phone" class="form-control">
	</div>
	<div class="col-md-2">
		<label>Recommended By</label>
		<input required readonly type="text" name="recommended_by" class="form-control">
	</div>
	<div class="col-md-3">
		<label>Return Date</label>
		<input required readonly type="date" name="return_date" class="form-control">
	</div>
	<div class="col-md-2">
		<label> Age</label>
		<input required readonly type="number" min="0" name="age" class="form-control">
	</div>
	<div class="col-md-2">
		<label> Gender</label>
		<select name="gender" required readonly class="form-control">
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
		$b_id = $_SESSION['branch_id'];
		$dr = "SELECT * FROM users WHERE role_id = '3' AND branch_id = '$b_id' ";
		$get_doctor = mysqli_query($con, $dr);
		if (mysqli_num_rows($get_doctor) > 0) 
		{	
			while ($row_doctor = mysqli_fetch_array($get_doctor)) 
		    {
		      echo '<option value="'.$row_doctor['id'].'">'.$row_doctor['u_name'].'</option>';
		    }
		}
		else
		    {
		      echo '<option value="">'.$dr.'</option>';
		    }
?>
		</select>
	</div>
   	<div class="col-md-2">
   		<label>Cash</label>

   		<textarea readonly required rows="1" style="resize: none;" readonly id="cash" name="cash" class="form-control">0</textarea>
   	</div>
<?php } ?>


   	<div class="col-md-7" style="font-size: 15px;">
   		<label>Amonut Tokan Type</label><br>
   		<div class = "row">
   			<div class="col-md-4">
		   		<input onclick="myFunction102()" type="radio" id="poor" required name="tokan_payment" value="102">
		   		<label for="poor">Poor</label>   
		   		<input id = "get_poor" type="hidden" value = "<?php echo $amount_array['0']; ?>" name="">				
   			</div>
   			<div class="col-md-4">
		   		<input onclick="myFunction103()" type="radio" id="member"  name="tokan_payment" value="103">
		   		<label for="member">YCDO / Member</label> 				
		   		<input id = "get_member" type="hidden" value = "<?php echo $amount_array['1']; ?>" name="">	
   			</div>
   			<div class="col-md-4">
		   		<input onclick="myFunction104()" type="radio" id="general"  name="tokan_payment" value="104">
		   		<label for="general">General</label>   				
		   		<input id = "get_general" type="hidden" value = "<?php echo $amount_array['2']; ?>" name="">	
   			</div>   			
   		</div>
   	</div>


   	<div class="col-md-3">
   		<label>Cash Received</label>
   		<input type="number" name="cash_received" class="form-control" required>
   	</div>

	<div class="col-md-2">
		<br>
<?php
$select_item = "SELECT * FROM `items_by_doctor` WHERE user_id = ".$user_id." AND status = '1' ";
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
<script>
function showProgress() {
  document.getElementById('submitBody').style.display = 'none';
//   document.getElementById('submitButton').style.display = 'none';
  document.getElementById('loadingSpinner').style.display = 'block';
}    
</script>
<script type="text/javascript">
      $(document).ready(function () {
  $('#select_item').selectize({
      sortField: 'text'
  });
  $(".alert").alert();
});
</script>

<script>
function myFunction101() {
	//DESERVING
	var get_poor = document.getElementById('get_poor').value;
  document.getElementById("cash").innerHTML = get_poor;
}
function myFunction102() {
	//POOR
	var get_poor = document.getElementById("get_poor").value;
  document.getElementById("cash").innerHTML = get_poor;
}
function myFunction103() {
	//MEMBER
	var get_member = document.getElementById("get_member").value;
  document.getElementById("cash").innerHTML = get_member;
}
function myFunction104() {
	//GENERAL
	var get_general = document.getElementById("get_general").value;
  document.getElementById("cash").innerHTML = get_general;
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

<script type = "text/javascript" >  
    function preventBack() { window.history.forward(); }  
    setTimeout("preventBack()", 0);  
    window.onunload = function () { null };  
</script> 

<script type = "text/javascript" >
	function checknumber(theForm) {   
 if (parseInt(theForm.cash.value) == 0 ) 
    { 
    alert('Please add Medicines');
            return false;
     } 
     return true;
            
}
</script> 

<?php mysqli_close($con); ?>