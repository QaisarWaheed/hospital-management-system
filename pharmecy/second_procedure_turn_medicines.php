<?php
include 'includes/connect_second_turn.php';

$tokan_no = '';
$search_tokan_no = '';
if (isset($_GET['search_tokan_no']) && $_GET['search_tokan_no'] !== '') {
    $search_tokan_no = (int) $_GET['search_tokan_no'];
    $tokan_no = $search_tokan_no;
} elseif (isset($_GET['tokan_no']) && $_GET['tokan_no'] !== '') {
    $search_tokan_no = (int) $_GET['tokan_no'];
    $tokan_no = $search_tokan_no;
} elseif (isset($_GET['token_no']) && $_GET['token_no'] !== '') {
    $search_tokan_no = (int) $_GET['token_no'];
    $tokan_no = $search_tokan_no;
} elseif (isset($_POST['token_no']) && $_POST['token_no'] !== '') {
    $search_tokan_no = (int) $_POST['token_no'];
    $tokan_no = $search_tokan_no;
} elseif (isset($_POST['tokan_no']) && $_POST['tokan_no'] !== '') {
    $search_tokan_no = (int) $_POST['tokan_no'];
    $tokan_no = $search_tokan_no;
}

$GLOBALS['search_tokan_no'] = $search_tokan_no;

if (isset($_GET['ajax']) && $_GET['ajax'] === 'cart_total') {
    header('Content-Type: application/json; charset=UTF-8');
    $tokan_type = (int) ($_GET['tokan_type'] ?? 104);
    echo json_encode(array(
        'amount' => pharmecy_items_by_doctor_cart_amount($con, $user_id, $branch_id, $tokan_type),
    ));
    exit;
}

$limit = 0;
$issued_medicine = 0;
$patient_id = 0;
$doctor_id = 0;
$name = '';
$age = '';
$gender = 0;

if ($tokan_no > 0) {
    pharmecy_sync_branch_pending_amount_from_tokan($con, $tokan_no);
    $procedure_turn = pharmecy_load_procedure_medicine_turn($con, $tokan_no);
    if ($procedure_turn) {
        $patient_id = (int) $procedure_turn['patient_id'];
        $doctor_id = (int) $procedure_turn['doctor_id'];
        $name = (string) $procedure_turn['name'];
        $age = (string) $procedure_turn['age'];
        $gender = (int) $procedure_turn['gender'];
        $limit = (int) $procedure_turn['medicine_limit'];
        $issued_medicine = (int) $procedure_turn['issued_medicine'];
    }
}

$cart_totals = array(
    101 => pharmecy_items_by_doctor_cart_amount($con, $user_id, $branch_id, 101),
    102 => pharmecy_items_by_doctor_cart_amount($con, $user_id, $branch_id, 102),
    103 => pharmecy_items_by_doctor_cart_amount($con, $user_id, $branch_id, 103),
    104 => pharmecy_items_by_doctor_cart_amount($con, $user_id, $branch_id, 104),
);
$cart_cash = (int) ($cart_totals[104] ?? 0);
$select_item = "SELECT * FROM `items_by_doctor` WHERE `branch_id` = '$branch_id' AND `user_id` = '$user_id' AND `status` = '1' ";
$run_select_item = mysqli_query($con, $select_item);
$count_item = mysqli_num_rows($run_select_item);

$is_save_medicine = (isset($_GET['save_medicine']) && $_GET['save_medicine'] !== '')
    || (isset($_POST['save_medicine']) && $_POST['save_medicine'] !== '')
    || (isset($_GET['save']) && $_GET['save'] !== '');

if ($is_save_medicine) {
if ($count_item >= 1) {
		$previous_tokan_no = (int) ($_REQUEST['previous_tokan_no'] ?? 0);
		$patient_id = (int) ($_REQUEST['patient_id'] ?? 0);
		$doctor_id = (int) ($_REQUEST['doctor_id'] ?? 0);
		$tokan_type = (int) ($_REQUEST['tokan_payment'] ?? 104);
		$cash = (float) ($_REQUEST['cash'] ?? 0);
		if ($cash <= 0) {
			$cash = pharmecy_items_by_doctor_cart_amount($con, $user_id, $branch_id, $tokan_type);
		}
		$cash_received = (float) ($_REQUEST['cash_received'] ?? 0);
		$insert = "INSERT INTO `tokans`
		(`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`,`cash_received`, `user_id`, `previous_tokan_no`, `created`, `branch_id`, `status`) 
		VALUES 
		(NULL, '$patient_id','$doctor_id', '$tokan_type', '$cash', '$cash_received', '$user_id', '$previous_tokan_no', '$current_date', '$branch_id', '2')";
			if (mysqli_query($con, $insert)) 
			{
			    $tokan_no = mysqli_insert_id($con);
			    if ($cash_received > 0 && $previous_tokan_no > 0) {
			        $cash_received_sql = mysqli_real_escape_string($con, (string) $cash_received);
			        mysqli_query(
			            $con,
			            "UPDATE tokans SET cash_received = '$cash_received_sql'
			            WHERE id = '$previous_tokan_no'"
			        );
			    }
			    pharmecy_sync_branch_pending_amount_from_tokan($con, $previous_tokan_no);
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
	$delete = "DELETE FROM items_by_doctor WHERE id = '$del_id' AND user_id = '$user_id' AND branch_id = '$branch_id' AND `tokan_no` IS NULL ";
		$reg_item_id = get_branch_item_id_from_items_by_doctor_id($del_id);
		$quantity = get_item_quantity_from_item_by_docotor_id($del_id);
		$get_available_quantity = get_register_item_quantity_from_item_id($reg_item_id);
		$new_quantity = $get_available_quantity + $quantity;
		$update = "UPDATE `item_register_to_branches` SET `quantity`= quantity+$quantity WHERE id = '$reg_item_id' ";
	if (mysqli_query($con, $delete)) 
	{
		mysqli_query($con, $update);
        header('location: second_procedure_turn_medicines.php?search_tokan_no='.$search_tokan_no);
        exit(0);
	}	
}
if (isset($_GET['save_test'])) 
{
	$search_tokan_no = $_GET['search_tokan_no'];
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
	$insert = "INSERT INTO `items_by_doctor`
	(`item_id`,      `dose`,  `feed`,  `days`,  `user_id`,  `branch_id`, `fix_dose`, `created`, `purchase_price`, `sale_price_general`, `sale_price_member`, `sale_price_poor`, `category_id`) VALUES 
	('$reg_item_id', '$dose', '$feed', '$days', '$user_id','$branch_id', '$fix_dose', '$current_date', '$purchase', '$general', '$member', '$poor', '$category_id')";
	if($check_item == 0)
	{	
		mysqli_query($con, "UPDATE `item_register_to_branches` SET `quantity`= quantity-$quantity WHERE id = '$reg_item_id' ");
		if (mysqli_query($con, $insert)) {
			header('Location: second_procedure_turn_medicines.php?search_tokan_no=' . (int) $search_tokan_no);
			exit;
		}
	}
	else
	{
		header('Location: second_procedure_turn_medicines.php?search_tokan_no=' . (int) $search_tokan_no . '&cart_dup=1');
		exit;
	}
}
include 'includes/head.php'; ?>
	<title>SECOND TURN PROCEDURE MEDICINES - <?php echo $company_trademark; ?></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/js/selectize.min.js" integrity="sha512-IOebNkvA/HZjMM7MxL0NYeLYEalloZ8ckak+NDtOViP7oiYzG5vn6WVXyrJDiJPhl4yRdmNAG49iuLmhkUdVsQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.15.2/css/selectize.bootstrap3.min.css" integrity="sha512-cNefX8/Vd+UJbeYHzwdRZYEHI1K5Wj+gCdaK4R767/8SFhqMaHHg881hZONXpq4ainln9e330TalryDPKysm2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
			<div class="row">
                <div class="col-md-12" style = "text-align: center;">
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
	<div class="col-md-12">			
<form>
	
<div class="row">
<div class="col-md-12">
	<fieldset class="border p-2">
	<legend style="font-size: 14px;" class="w-auto">SELECT TEST OR MEDICINE OR PROCEDURE</legend>
	<div class="row">

	<div class="col-md-6">
	<input type="hidden" name="search_tokan_no" value="<?php echo $search_tokan_no; ?>" />
		<select required name="reg_item_id" id="select_item" placeholder="Pick Procedure" class="form-control bg-success">
			<option value="">Select  Procedure</option>
		    <?php echo procedure_medicines_by_name(); ?>
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
	<input onclick="myDisplayGoneAdd()" id="add" type="submit" name="save_test" value="ADD" class="btn btn-sm btn-primary">
	<input type="submit" name="clear" value="CLEAR" class="btn btn-sm btn-warning">
</div>


   	</div>
   	<div class="col-md-6">
   			<?php echo medicine_select_list_procedure(); ?>
   	</div>

   </div>
</fieldset>

</div>

</div>

</form>

<form method="get" id="medicine_save_form" onsubmit="return checknumber(this);">
<input type="hidden" name="search_tokan_no" value="<?php echo (int) $search_tokan_no; ?>" />
<?php if ($tokan_no > 0 && $patient_id > 0) { ?>
<div class="row">
	<div class="col-md-3">
		<label>Patient Name</label>
		<input type="hidden" name="patient_id" value="<?php echo (int) $patient_id; ?>">
		<input type="hidden" name="previous_tokan_no" value="<?php echo (int) $tokan_no; ?>">
		<input readonly type="text" class="form-control" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
	</div>
	<div class="col-md-2">
		<label>Age</label>
		<input readonly type="number" value="<?php echo htmlspecialchars($age, ENT_QUOTES, 'UTF-8'); ?>" min="0" class="form-control">
	</div>
	<div class="col-md-2">
		<label>Gender</label>
		<select readonly required class="form-control">
<?php
if ($gender == 1) {
    echo '<option value="1">Female</option>';
} elseif ($gender == 2) {
    echo '<option value="2">Male</option>';
} else {
    echo '<option value="3">Other</option>';
}
?>
		</select>
	</div>
	<div class="col-md-3">
		<label>Operation By</label>
		<select name="doctor_id" required class="form-control">
		<?php
		$get_doctor = mysqli_query($con, "SELECT id, u_name FROM users WHERE role_id = '3' AND branch_id = '$branch_id' AND status = 1 ORDER BY u_name");
		if ($get_doctor && mysqli_num_rows($get_doctor) > 0) {
		    while ($row_doctor = mysqli_fetch_assoc($get_doctor)) {
		        $opt_id = (int) $row_doctor['id'];
		        $sel = ($doctor_id === $opt_id) ? ' selected' : '';
		        echo '<option' . $sel . ' value="' . $opt_id . '">' . htmlspecialchars($row_doctor['u_name'], ENT_QUOTES, 'UTF-8') . '</option>';
		    }
		}
		?>
		</select>
	</div>
	<div class="col-md-2">
		<label>Cash</label>
		<input type="hidden" name="cash" id="cash" value="<?php echo (int) $cart_cash; ?>">
		<input type="text" readonly id="cash_display" class="form-control" value="<?php echo (int) $cart_cash; ?>">
	</div>
</div>
<div class="row" style="margin-top: 10px;">
   	<div class="col-md-3" style="font-size: 15px;">
   		<label>Amount Token Type</label><br>
   		<input type="radio" id="general" required name="tokan_payment" value="104" checked>
   		<label for="general">General</label>
   	</div>
   	<div class="col-md-2">
   		<label>Limit Medicine</label>
   		<input type="number" id="limit_medicine" value="<?php echo (int) $limit; ?>" readonly class="form-control">
   	</div>
   	<div class="col-md-2">
   		<label>Issued Medicine</label>
   		<input type="number" id="issued_medicine" value="<?php echo (int) $issued_medicine; ?>" readonly class="form-control">
   	</div>
   	<div class="col-md-3">
   		<label for="cash_received">Cash Received</label>
   		<input type="number" name="cash_received" id="cash_received" class="form-control" placeholder="Enter amount" style="width:150px" autocomplete="off">
   	</div>
	<div class="col-md-2">
		<br>
        <button type="submit" id="save_medicine" name="save_medicine" value="1" class="btn btn-success" onclick="myDisplayGoneSave()">SAVE</button>
		<input type="reset" value="CLEAR" class="btn btn-sm btn-warning">
	</div>
</div>
<?php } else { ?>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-warning">Open this page with a valid procedure token (search_tokan_no).</div>
	</div>
</div>
<?php } ?>

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
<script>
var cartTotalsByType = <?php echo json_encode($cart_totals); ?>;

function updateSessionCashDisplay() {
  var typeEl = document.querySelector('input[name="tokan_payment"]:checked');
  var typeId = typeEl ? parseInt(typeEl.value, 10) : 104;
  var amount = cartTotalsByType[typeId];
  if (typeof amount === 'undefined') {
    amount = cartTotalsByType[104] || 0;
  }
  var display = document.getElementById('cash_display');
  var hidden = document.getElementById('cash');
  if (display) {
    display.value = amount;
  }
  if (hidden) {
    hidden.value = amount;
  }
}

function refreshCartTotalFromServer() {
  var typeEl = document.querySelector('input[name="tokan_payment"]:checked');
  var typeId = typeEl ? parseInt(typeEl.value, 10) : 104;
  if (typeof jQuery === 'undefined') {
    updateSessionCashDisplay();
    return;
  }
  jQuery.getJSON('second_procedure_turn_medicines.php', {
    ajax: 'cart_total',
    search_tokan_no: <?php echo (int) $search_tokan_no; ?>,
    tokan_type: typeId
  }).done(function (data) {
    if (data && typeof data.amount !== 'undefined') {
      cartTotalsByType[typeId] = data.amount;
      updateSessionCashDisplay();
    }
  });
}

document.addEventListener('DOMContentLoaded', function () {
  var radios = document.querySelectorAll('input[name="tokan_payment"]');
  for (var i = 0; i < radios.length; i++) {
    radios[i].addEventListener('change', updateSessionCashDisplay);
  }
  updateSessionCashDisplay();
});

function checknumber(theForm) {
  var cashReceived = theForm.querySelector('#cash_received');
  if (cashReceived && cashReceived.value === '') {
    alert('Please enter cash received.');
    return false;
  }
  return true;
}
</script>
<script>
function myDisplayGoneAdd() {
  document.getElementById("add").style.display = "none";
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
function myDisplayGoneSave() {
  var el = document.getElementById("save_medicine");
  if (el) {
    el.style.display = "none";
  }
}
</script>