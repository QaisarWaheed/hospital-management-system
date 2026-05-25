<?php include 'includes/connect.php'; 

if (isset($_GET['save']) && $_GET['save'] != '') 
{
	$tokan_no = next_tokan_no();
	if (isset($_GET['previous_tokan_no'])) 
	{
		$token_pre = $_GET['previous_tokan_no'];
		$patient_id = $_GET['patient_id'];
		$pending_id = $_GET['pending_id'];
		$doctor_id = $_GET['doctor_id'];
		$cash = 0;
		$cash_received = $_GET['cash_received'];
		$insert = "INSERT INTO `tokans`
		(`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`,`cash_received`, `user_id`, `previous_tokan_no`, `status`, `created`, `branch_id`) 
		VALUES 
		('$tokan_no', '$patient_id','$doctor_id', '201', '$cash', '$cash_received', '$user_id', '$token_pre', '1', '$current_date', '$branch_id')";
		mysqli_query($con, $insert);
		
		$insert_2 = "INSERT INTO `branch_pending_receive`
		( `token_no`, `pending_id`, `amount`, `user_id`, `branch_id`, `created`) 
		VALUES 
		('$token_pre', '$pending_id','$cash_received', '$user_id', '$branch_id', '$current_date')";
		mysqli_query($con, $insert_2);
	}

?>
<script>
  window.open(<?php echo json_encode(ycdo_absolute_url('print_medicine_slip.php', 'tokan_no=' . rawurlencode((string) $tokan_no))); ?>, "_blank", "toolbar=no,scrollbars=no,resizable=no,top=500,left=500,width=400,height=400,status=no");
  location.replace("branch_procedure_pending_token.php");
</script>
<?php
}

include 'includes/head.php'; ?>
	<title>Pending Amount Receiving - <?php echo $company_trademark; ?></title>
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

	<div class="container">
		
		<div class="row">
        <div class = "col-12 bg-light p-1">
            <?php include "navigation_dashboard.php"; ?>
        </div>
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Procedure Pending Amount Receiving</h1></label>
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
	<div class="col-md-12">			

<form onsubmit="return checknumber(this);">
<div class="row">
<?php
if (isset($_GET['search_tokan_no']) && $_GET['search_tokan_no'] != '') 
{ 
	$tokan_no = $_GET['search_tokan_no'];
	$select_pending = "SELECT * FROM branch_pending_details WHERE token_no = '$tokan_no' ";
	$run_select_pending = mysqli_query($con, $select_pending);
	if (mysqli_num_rows($run_select_pending) == 1) 
	{
		while ($row_pending = mysqli_fetch_array($run_select_pending)) 
		{
		    $pending_id = $row_pending['id'];
		    $g_name = $row_pending['gardian_name'];
		    $g_phone = $row_pending['gardian_phone'];
		    $recommended_by = $row_pending['recommended_by'];
		}
	}
	
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
		<input type="hidden" name="pending_id" value="<?php echo $pending_id; ?>">
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
		<select readonly name="doctor_id" required class="form-control">
		<?php 	$get_doctor = mysqli_query($con, "SELECT * FROM users WHERE id = '$doctor_id' ");
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
   		<label>Pending Cash</label>

   		<textarea readonly required rows="1" style="resize: none;" readonly id="cash" name="cash" class="form-control"><?php echo get_procedure_pending_amount($tokan_no); ?></textarea>
   	</div>
<?php } ?>

	<div class="col-md-3">
		<label>Reference Name</label>
		<input readonly type="text" class="form-control" value="<?php echo $g_name; ?>">
	</div>
	<div class="col-md-2">
		<label> Phone</label>
		<input readonly type="number" value="<?php echo $g_phone; ?>" min="0" class="form-control">
	</div>
	<div class="col-md-2">
		<label> Recommended</label>
		<input readonly type="text" class="form-control" value="<?php echo $recommended_by; ?>">
	</div>


   	<div class="col-md-3">
   		<label>Cash Received</label>
   		<input type="number" min="0" name="cash_received" class="form-control" required>
   	</div>

	<div class="col-md-2">
		<br>
   		<input  checked onclick="myFunction104()" type="hidden" id="general"  name="tokan_payment" value="104">
		<input type="submit" value="SAVE" name="save" class="btn btn-sm btn-primary">
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
<script type = "text/javascript" >  
    function preventBack() { window.history.forward(); }  
    setTimeout("preventBack()", 0);  
    window.onunload = function () { null };  
</script> 