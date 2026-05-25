<?php include 'includes/connect.php'; 

if (isset($_GET['save']) && $_GET['save'] != '') 
{
    if($branch_id > 0)
    {
    $token_no = $_GET['previous_tokan_no'];
    $patient_id = get_patient_id_by_token_no($token_no);
    $doctor_id = get_docotr_id_by_token_no($token_no);
    $received_cash = $_GET['cash'];
    $referral_patient_phone = $_GET['phone'];
    $tokan_payment = $_GET['tokan_payment'];
    $cash = $_GET['cash'];
    $insert = "INSERT INTO `tokans`
    (`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`, `cash_received`, `previous_tokan_no`, `user_id`, `status`, `created`, `branch_id`) VALUES 
    (NULL, '$patient_id', '$doctor_id', '$tokan_payment', '$cash', '$cash', '$token_no', '$user_id', '1', '$current_date', '$branch_id')";
    if(mysqli_query($con, $insert))
    {
        $token_id = mysqli_insert_id($con);
        $item_id = get_register_item_id_from_item_id(1639, $branch_id);
        mysqli_query($con, "INSERT INTO `item_by_doctor`
        (`id`, `tokan_no`, `item_id`, `dose`, `feed`, `days`, `fix_dose`, `doctor_id`, `branch_id`, `user_id`, `status`, `created`, `purchase_price`, `sale_price_general`, `sale_price_member`, `sale_price_poor`, `category_id`, `tokan_type_id`, `sale_price`, `sale_quantity`) 
        VALUES 
        (NULL, '$token_id', '$item_id', '1', '1', '1', '1', '$doctor_id', '$branch_id', '$user_id', '2', '$current_date', '0', '500', '400', '300', '29', '$tokan_payment', '$cash', '1')");
        mysqli_query($con, "UPDATE `item_register_to_branches` SET `quantity` = `quantity`-1 WHERE id = '$item_id' ");
        mysqli_query($con, "UPDATE `referral_patients` SET `token_id` = '$token_id', `received_cash` = '$received_cash', `referral_patient_phone` = '$referral_patient_phone', `user_id` = '$user_id', `referral_patient_status` = '2', `branch_id` = '$branch_id' WHERE `opd_token_id` = '$token_no' ");
    }
    else
    {
        echo '<script>alert("Save failed.");location.replace("dashboard.php");</script>';
        exit;
    }
?>
<style>
.greenText{ background-color:green; }
.blueText{ background-color:blue; }
.redText{ background-color:red; }
</style>

<script>
  window.open(<?php echo json_encode(ycdo_absolute_url('referral_turn_token_print.php', 'referral_tokan_no=' . rawurlencode((string) $token_id))); ?>, "_blank", "toolbar=no,scrollbars=no,resizable=no,top=10,left=10,width=1200,height=1600,status=no");
  location.replace("referral_turn.php");
</script>
<?php
    }
}

include 'includes/head.php'; ?>
	<title>DOCTOR TURN - <?php echo $company_trademark; ?></title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
<script>
  $(document).ready(function () {
      $('select').selectize({
          sortField: 'text'
      });
  });    
</script>
<style>
@media print
{    
    .noprint, .no-print *
    {
        display: none !important;
    }
}    
</style>
    <style>
        #loader {
            border: 12px solid #f3f3f3;
            border-radius: 50%;
            border-top: 12px solid #444444;
            width: 70px;
            height: 70px;
            animation: spin 1s linear infinite;
        }

        .center {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="background_image_ycdo" onkeydown="return (event.keyCode != 116)">
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
	<div class="col-md-12" style="text-align: center;background: lightgreen;" id = "submitBody">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>

	<div class="">

		<div class="row" style="margin: 0px;">

        	<div class="col-md-3 background_whitesmoke noprint">
        		<?php include 'left_navigation.php'; ?>
        	</div>
			<div class="col-md-9">
            <div class = "row">
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Patient Medicine</h1></label>
			</div>
<div class="col-md-12">

	<form name="search" method="get" onsubmit="showProgress(); return true;">
		<div class="row">
    		<div class="col-md-9">
    		    <select name="search_tokan_no" class="form-control bg-success" required>
    		        <option value = "">SELECT TOKEN</option>
    		        <?php
    		        $select_referral_token = "SELECT * FROM `referral_patients` WHERE `referral_patient_status` = '1' AND branch_id = '$branch_id' ORDER BY `referral_patients`.`referral_patient_id` DESC limit 0, 50 ";
    		        $run_referral_token = mysqli_query($con, $select_referral_token);
    		        if(mysqli_num_rows($run_referral_token) > 0)
    		        {
    		            while($row_referral_token = mysqli_fetch_array($run_referral_token))
    		            {
    		                $token_no = $row_referral_token['opd_token_id'];
    		                echo '<option value = "'.$token_no.'">'.$token_no.'</option>';
    		            }
    		        }
    		        ?>
    		    </select>
    		</div>	
    		<div class="col-md-3">
    			<input type="submit" name="" value="SEARCH">
    		</div>
			
		</div>
		</div>			
	</form>
</div>
	<div class="col-md-12">			
<form onsubmit="showProgress(); return true;">
	
<div class="row">
<div class="col-md-12">
	<fieldset class="border p-2">
	<legend style="font-size: 14px;text-align: center;" class="w-auto">SELECT REFERRAL PATIENT RECORDS</legend>
	<div class="row">

	<div class="col-md-12">
<?php
if(isset($_GET['search_tokan_no']) && $_GET['search_tokan_no'] != '')
{
    $token_id = $_GET['search_tokan_no'];
?>
        <div class="row">
            <div class="col-md-6">
                <label>FROM</label>
                <?php echo show_from_doctors_by_token_id($token_id); ?>
            </div>            
            <div class="col-md-6">
                <label>TO</label>
                <?php echo show_to_doctors_by_token_id($token_id); ?>
            </div>
        </div>
   	</div>
   </div>
</fieldset>

</div>

</div>

</form>

<form onsubmit="showProgress(); return true;">
<div class="row">
<?php
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
	<div class="col-md-6">
		<label>Patient Name</label>
		<input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
		<input type="hidden" name="previous_tokan_no" value="<?php echo $tokan_no; ?>">
		<input readonly type="text" class="form-control bg-info" value="<?php echo $name; ?>">
	</div>
	<div class="col-md-3">
		<label> Age</label>
		<input readonly type="number" value="<?php echo $age; ?>" min="0" class="form-control bg-info">
	</div>
	<div class="col-md-3">
		<label for = "phone"> Phone</label>
		<input type="text" name ="phone" id ="phone" pattern="[0-9]{11}" required class="form-control">
	</div>
	<div class="col-md-3">
		<label> Gender</label>
        <?php 
        if ($gender == 1) {echo '<input readonly type="gender" value="FEMALE" class="form-control bg-info">';}
        elseif ($gender == 2) {echo '<input readonly type="gender" value="MALE" class="form-control bg-info">';}
        else {echo '<input readonly type="gender" value="OTHER" class="form-control bg-info">';}
        ?>
	</div>
<?php } ?>


   		
<?php
if(isset($_GET['search_tokan_no']) && $_GET['search_tokan_no'] != '')
{?>   	
    <div class="col-md-6" style="font-size: 15px;">
   		<label>Amonut Tokan Type</label><br>
   		<input  onclick="myFunction102()" type="radio" id="poor" required name="tokan_payment" value="102">
   		<label for="poor">Poor</label>
   		
   		<input onclick="myFunction103()" type="radio" id="member"  name="tokan_payment" value="103">
   		<label for="member">YCDO / Member</label>
   		
   		<input checked onclick="myFunction104()" type="radio" id="general"  name="tokan_payment" value="104">
   		<label for="general">General</label>
   		
   	</div>
    <div class="col-md-3">
        <label>Cash</label>
        <textarea readonly required rows="1" style="resize: none;" readonly id="cash" name="cash" class="form-control bg-info">500</textarea>
    </div>
	<div class="col-md-2">
		<br>
        <input type="submit" id="save" onclick="myDisplayGoneSave()" value="SAVE" name="save" class="btn btn-sm btn-primary">
		<input type="reset" value="CLEAR" name="clear" class="btn btn-sm btn-warning">
	</div>
<?php
}
?>
</div>

</form>

<footer>
    <!--<img src = "images/ycdo_footer.png" width = "100%" />-->
</footer>
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
//   document.getElementById("cash").innerHTML = <?php echo intval(get_amount(101)); ?>;
   document.getElementById("cash").innerHTML = 100;
}
function myFunction102() {
	//POOR
//   document.getElementById("cash").innerHTML = <?php echo intval(get_amount(102)); ?>;
  // document.getElementById("tokan_get1").innerHTML = 9;
   document.getElementById("cash").innerHTML = 300;
}
function myFunction103() {
	//MEMBER
  document.getElementById("cash").innerHTML = <?php echo intval(get_amount(103)); ?>;
  // document.getElementById("tokan_get1").innerHTML = 1;
   document.getElementById("cash").innerHTML = 400;
}
function myFunction104() {
	//GENERAL
  document.getElementById("cash").innerHTML = <?php echo intval(get_amount(104)); ?>;
  // document.getElementById("tokan_get1").innerHTML = 2;
   document.getElementById("cash").innerHTML = 500;
}
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
<script type="text/javascript">
        setTimeout(function () { window.close(); }, 160000);
</script>
<script>
function showProgress() {
  document.getElementById('submitBody').style.display = 'none';
//   document.getElementById('submitButton').style.display = 'none';
  document.getElementById('loadingSpinner').style.display = 'block';
}    
</script>
<?php mysqli_close($con); ?>