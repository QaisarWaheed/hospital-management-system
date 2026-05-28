<?php 
include 'includes/connect.php'; 
$amount_array = get_select_amount_array();
$select_item = "SELECT * FROM `items_by_doctor` WHERE user_id = ".$user_id." AND status = '1' ";
$run_select_item = mysqli_query($con, $select_item);
$count_item = mysqli_num_rows($run_select_item);

if (isset($_GET['save']) && $_GET['save'] != '') 
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
		(NULL, '$patient_id','$doctor_id', '$tokan_type', '$cash', '$cash_received', '$user_id', '$previous_tokan_no', '$current_date', '$branch_id')";
	}
	else
	{
		$name = $_GET['name'];
		$age = $_GET['age'];
		$gender = $_GET['gender'];
		$tokan_type = $_GET['tokan_payment'];
		$cash = $_GET['cash'];
		$cash_received = $_GET['cash_received'];
		$doctor_id = $_GET['doctor_id'];
			mysqli_query($con, "INSERT INTO `patients`
			(`id`, `name` ,`age` , `gender`, `created`) 
			VALUES 
			(NULL,'$name','$age','$gender', '$current_date')");
			    $patient_id = mysqli_insert_id($con);
		$insert = "INSERT INTO `tokans`
		(`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`,`cash_received`, `user_id`, `previous_tokan_no`, `created`, `branch_id`) 
		VALUES 
		(NULL, '$patient_id','$doctor_id', '$tokan_type', '$cash', '$cash_received', '$user_id', NULL, '$current_date', '$branch_id')";

	}
			if (mysqli_query($con, $insert)) 
			{
			    $tokan_no = mysqli_insert_id($con);
				if ($cash > $cash_received) 
				{
					pharmecy_insert_branch_pending_details($con, $tokan_no, $current_date, $branch_id, '2');
				}
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
				mysqli_query($con, "DELETE FROM `items_by_doctor` WHERE branch_id = '$branch_id' AND user_id = '$user_id' ");
				header('Location: print_medicine_slip.php?tokan_no=' . (int) $tokan_no);
				exit;
			}
			echo '<script>alert("Token could not be saved. Please try again.");history.back();</script>';
			exit;
}

if (isset($_GET['save_test'])) 
{
    $search_tokan_no = $_GET['search_tokan_no'];
    $select_medicine_by_doctor_id = $_GET['save_test'];
    $select_data = "SELECT * FROM `select_by_doctor` WHERE `id` = '$select_medicine_by_doctor_id' ";
    $run_data = mysqli_query($con, $select_data);
    if (mysqli_num_rows($run_data) == 1) 
    {
        while ($row_data = mysqli_fetch_array($run_data)) 
        {
            $reg_item_id = $row_data['item_id'];
            $br_id = $row_data['branch_id'];
        	$select_items = "SELECT id, purchase, poor, member, general, deserving, category_id FROM `items` WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE item_register_to_branches.branch_id = '$br_id' AND id = '$reg_item_id')";
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
	        $fix_dose = $row_data['fix_dose'];
            $dose = $row_data['dose'];
            $feed = $row_data['feed'];
            $days = $row_data['days'];
            if ($fix_dose == 0) 
            {
            $quantity = $dose * $days * $feed;
            }
            else
            {
                    $quantity = $fix_dose;
            }
        }
    }
    else
    {
        header('location: second_turn_by_doctor.php?search_tokan_no='.$search_tokan_no.'&error='.$select_medicine_by_doctor_id);
        exit();
    }
    $check_item = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `items_by_doctor` WHERE item_id = '$reg_item_id' AND user_id = '$user_id' AND status = '1' "));
    $insert = "INSERT INTO `items_by_doctor`
	(`item_id`,      `dose`,  `feed`,  `days`,  `user_id`,  `branch_id`, `fix_dose`, `created`, `purchase_price`, `sale_price_general`, `sale_price_member`, `sale_price_poor`, `category_id`) VALUES 
	('$reg_item_id', '$dose', '$feed', '$days', '$user_id','$branch_id', '$fix_dose', '$current_date', '$purchase', '$general', '$member', '$poor', '$category_id')";
    if($check_item == 0)
    {   
        mysqli_query($con, "UPDATE `item_register_to_branches` SET `quantity`= quantity-$quantity WHERE id = '$reg_item_id' ");
        if (mysqli_query($con, $insert))        
        { 
            ?>
    <script type="text/javascript">
              location.replace("second_turn_by_doctor.php?search_tokan_no=<?php echo $search_tokan_no; ?>");
            </script>
    <?php   }
    }
    else
    { ?>
    <script type="text/javascript">
        alert("INFO: Data already addad");
      location.replace("second_turn_by_doctor.php?search_tokan_no=<?php echo $search_tokan_no; ?>");    
    </script>
<?php }
}
include 'includes/head.php'; ?>
	<title>DOCTOR TURN - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo" onkeydown="return (event.keyCode != 116)">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>

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
			<div class="col-md-4 btn btn-sm btn-outline-primary">
				<label>NEXT TOKEN NO:<?php echo next_tokan_no().' / '.date('y'); ?></label>
			</div>
		<div class="col-md-4">
			<input formaction="search" type="number" placeholder="Token" name="search_tokan_no" max="<?php echo intval(next_tokan_no())-1; ?>" required min="1" class="form-control">
		</div>	
		<div class="col-md-3">
			<input type="submit" name="" value="SEARCH">
		</div>
		<div>
			
		</div>
		</div>			
	</form>
</div>
	<div class="col-md-12">			
<form>
<div class="row">
<?php
if(isset($_GET['search_tokan_no']) && $_GET['search_tokan_no'] != '')
{
    $token_id = $_GET['search_tokan_no'];
?>
<div class="col-md-12">
	<fieldset class="border p-2">
	<legend style="font-size: 14px;" class="w-auto">SELECT TEST OR MEDICINE OR PROCEDURE</legend>
	<div class="row">
	<div class="col-md-8">
        <div class="row">
            <div class="col-md-6">
                <label>Medicines</label>
           		<select id="mySelect_1" ondblclick="del_medicine_1();" class="form-control" size="8">
           			<?php echo medicine_selected_by_doctor($token_id); ?>
           		</select>
            </div>            
            <div class="col-md-6">
                <label>Tests</label>
           		<select id="mySelect_2" ondblclick="del_medicine_2();" class="form-control" size="8">
           			<?php echo test_selected_by_doctor($token_id); ?>
           		</select>
            </div>
        </div>
   	</div>
   	<div class="col-md-4">
   			<?php echo medicine_select_list_by_doctor_turn($token_id); ?>
   	</div>
   </div>
</fieldset>
</div>
<?php 
}
?>
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

   		<textarea readonly required rows="1" style="resize: none;" readonly id="cash" name="cash" class="form-control"><?php echo $amount_array['2']; ?></textarea>
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
		$get_doctor = mysqli_query($con, "SELECT * FROM users WHERE role_id = '3' AND branch_id = '$branch_id' ");
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

   		<textarea readonly required rows="1" style="resize: none;" readonly id="cash" name="cash" class="form-control"><?php echo $amount_array['2']; ?></textarea>
   	</div>
<?php } ?>
   	<div class="col-md-7" style="font-size: 15px;">
   		<label>Amonut Tokan Type</label><br>
   		<div class="row">
   			<div class="col-md-4">
		   		<input onclick="myFunction102()" type="radio" id="poor" required name="tokan_payment" value="102">
		   		<label for="poor">Poor</label>   
		   		<input required id = "get_poor" type="hidden" value = "<?php echo $amount_array['0']; ?>" name="">				
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
   		<input type="number" min = "0" max = "<?php echo $amount_array['2']+20; ?>" name="cash_received" class="form-control" required>
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
<!--paste-->
		</div>
	</div>
</div>

</body>

</html>
<script type="text/javascript">
function del_medicine() 
{
	var x = document.getElementById("mySelect").value;
	window.open('second_turn_by_doctor.php?search_tokan_no=<?php echo $token_id; ?>&del_medicine='+x,'_self');
}	
function del_medicine_1() 
{
	var x = document.getElementById("mySelect_1").value;
	window.open('second_turn_by_doctor.php?search_tokan_no=<?php echo $token_id; ?>&save_test='+x,'_self');
}	
function del_medicine_2() 
{
	var x = document.getElementById("mySelect_2").value;
	window.open('second_turn_by_doctor.php?search_tokan_no=<?php echo $token_id; ?>&save_test='+x,'_self');
}	
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
<script type="text/javascript">
        setTimeout(function () { window.close(); }, 160000);
</script>