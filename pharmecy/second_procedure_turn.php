<?php
ob_start();
@ini_set('memory_limit', '512M');
@set_time_limit(300);

include 'includes/connect.php';

if (isset($_GET['ajax']) && $_GET['ajax'] === 'procedure_options') {
    header('Content-Type: text/html; charset=UTF-8');
    echo pharmecy_branch_procedures_options_html($con, (int) $branch_id);
    exit;
}

if (isset($_GET['save']) && $_GET['save'] != '') {
    $token_pre = (int) ($_REQUEST['previous_tokan_no'] ?? 0);
    if ($token_pre < 1) {
        header('Location: second_procedure_turn.php?save_error=missing_token');
        exit;
    }

    $procedure_opts = array(
        'fix_dose' => (int) ($_REQUEST['fix_dose'] ?? 0),
        'dose' => (int) ($_REQUEST['dose'] ?? 1),
        'feed' => (int) ($_REQUEST['feed'] ?? 1),
        'days' => (int) ($_REQUEST['days'] ?? 1),
    );

    $reg_item_id = pharmecy_validate_branch_reg_item_id(
        $con,
        $branch_id,
        (int) ($_REQUEST['reg_item_id'] ?? 0)
    );
    if ($reg_item_id < 1) {
        $reg_item_id = pharmecy_procedure_turn_first_cart_reg_item_id($con, $user_id, $branch_id);
    }

    if (pharmecy_procedure_turn_cart_count($con, $user_id, $branch_id) < 1 && $reg_item_id > 0) {
        pharmecy_procedure_turn_add_cart_item($con, $reg_item_id, $user_id, $branch_id, $current_date, $procedure_opts);
    }

    $count_item = pharmecy_procedure_turn_cart_count($con, $user_id, $branch_id);
    $has_procedure = ($count_item >= 1 || $reg_item_id > 0);
    if ($has_procedure) {
        $patient_id = (int) ($_REQUEST['patient_id'] ?? 0);
        $doctor_id = (int) ($_REQUEST['doctor_id'] ?? 0);
        $tokan_type = (int) ($_REQUEST['tokan_payment'] ?? 104);
        if ($tokan_type < 100) {
            $tokan_type = 104;
        }
        $cash_received = (float) ($_REQUEST['cash_received'] ?? 0);

        $cash = 0.0;
        if ($reg_item_id > 0) {
            $cash = pharmecy_procedure_reg_item_sale_amount($con, $reg_item_id, $branch_id, $tokan_type, $procedure_opts);
        }
        if ($cash <= 0) {
            $cash = pharmecy_cart_amount_by_tokan_type($con, $user_id, $branch_id, $tokan_type);
        }

        $insert = "INSERT INTO `tokans`
        (`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`,`cash_received`, `user_id`, `previous_tokan_no`, `status`, `created`, `branch_id`)
        VALUES
        (NULL, '$patient_id','$doctor_id', '$tokan_type', '$cash', '$cash_received', '$user_id', '$token_pre', '1', '$current_date', '$branch_id')";
        if (mysqli_query($con, $insert)) {
            $tokan_no = mysqli_insert_id($con);
            if ($count_item >= 1) {
                pharmecy_finalize_procedure_cart_items($con, $tokan_no, $user_id, $branch_id, $doctor_id, $tokan_type);
            } elseif ($reg_item_id > 0) {
                pharmecy_insert_procedure_token_item_line(
                    $con,
                    $tokan_no,
                    $reg_item_id,
                    $user_id,
                    $branch_id,
                    $doctor_id,
                    $tokan_type,
                    $current_date,
                    $procedure_opts
                );
            }

            $final_cash = pharmecy_token_bill_amount($con, $tokan_no);
            if ($final_cash <= 0 && $cash > 0) {
                $final_cash = (float) $cash;
            }
            if ($final_cash > 0) {
                $final_cash_sql = mysqli_real_escape_string($con, (string) $final_cash);
                mysqli_query(
                    $con,
                    "UPDATE tokans SET cash = '$final_cash_sql', tokan_type_id = '$tokan_type'
                    WHERE id = '$tokan_no'"
                );
            } else {
                mysqli_query($con, "UPDATE tokans SET tokan_type_id = '$tokan_type' WHERE id = '$tokan_no'");
            }

            pharmecy_insert_branch_pending_details($con, $tokan_no, $current_date, $branch_id, '1', array(
                'amount' => $final_cash,
                'user_id' => $user_id,
                'tokan_type_id' => $tokan_type,
            ));
            header('Location: branch_procedure_pendings.php?saved=1&tokan_no=' . (int) $tokan_no);
            exit;
        }
        header('Location: second_procedure_turn.php?search_tokan_no=' . urlencode((string) $token_pre) . '&save_error=1');
        exit;
    }
    header('Location: second_procedure_turn.php?search_tokan_no=' . urlencode((string) $token_pre) . '&save_error=no_procedure');
    exit;
}

if (isset($_GET['del_medicine']) && $_GET['del_medicine'] != '') {
    $del_id = (int) $_GET['del_medicine'];
    $search_tokan_no = (int) ($_GET['search_tokan_no'] ?? 0);
    $delete = "DELETE FROM item_by_doctor WHERE id = '$del_id' AND user_id = '$user_id' AND branch_id = '$branch_id' AND `tokan_no` IS NULL ";
    $reg_item_id = get_branch_item_id_from_select_by_doctor_id($del_id);
    $quantity = get_item_quantity_from_item_by_docotr_by_id($del_id);
    $get_available_quantity = get_register_item_quantity_from_item_id($reg_item_id);
    $new_quantity = $get_available_quantity + $quantity;
    $update = "UPDATE `item_register_to_branches` SET `quantity`= '$new_quantity' WHERE id = '$reg_item_id' ";
    if (mysqli_query($con, $delete)) {
        mysqli_query($con, $update);
        header('Location: second_procedure_turn.php?search_tokan_no=' . $search_tokan_no);
        exit;
    }
}

if (isset($_GET['save_test'])) {
    $search_tokan_no = (int) ($_GET['search_tokan_no'] ?? 0);
    $reg_item_id = (int) ($_GET['reg_item_id'] ?? 0);
    $fix_dose = (int) ($_GET['fix_dose'] ?? 0);
    $dose = (int) ($_GET['dose'] ?? 1);
    $feed = (int) ($_GET['feed'] ?? 1);
    $days = (int) ($_GET['days'] ?? 1);

    if ($reg_item_id > 0 && pharmecy_procedure_turn_add_cart_item($con, $reg_item_id, $user_id, $branch_id, $current_date, array(
        'fix_dose' => $fix_dose,
        'dose' => $dose,
        'feed' => $feed,
        'days' => $days,
    ))) {
        header('Location: second_procedure_turn.php?search_tokan_no=' . $search_tokan_no);
        exit;
    }
    header('Location: second_procedure_turn.php?search_tokan_no=' . $search_tokan_no . '&cart_dup=1');
    exit;
}

$search_tokan_no = '';
$patient_id = 0;
$doctor_id = 0;
$name = '';
$age = '';
$gender = 0;
$tokan_no = 0;
$has_registration_token = false;

if (isset($_GET['search_tokan_no']) && $_GET['search_tokan_no'] !== '') {
    $search_tokan_no = (int) $_GET['search_tokan_no'];
    $tok = pharmecy_load_procedure_turn_token($con, $search_tokan_no);
    if ($tok) {
        $has_registration_token = true;
        $tokan_no = (int) $tok['token_no'];
        $patient_id = (int) $tok['patient_id'];
        $doctor_id = (int) $tok['doctor_id'];
        $name = (string) $tok['name'];
        $age = (string) $tok['age'];
        $gender = (int) $tok['gender'];
    }
}

$initial_cart_amount = (int) pharmecy_cart_amount_by_tokan_type($con, $user_id, $branch_id, 104);
$cart_item_count = pharmecy_procedure_turn_cart_count($con, $user_id, $branch_id);
$next_token_display = pharmecy_next_tokan_no_fast($con);
$cart_options_html = pharmecy_medicine_selected_cart_options_html($con, (int) $branch_id, $user_id);

$doctors_options_html = '';
$get_doctor = mysqli_query(
    $con,
    "SELECT id, u_name FROM users WHERE role_id = '3' AND branch_id = '$branch_id' AND status = 1 ORDER BY u_name"
);
if ($get_doctor && mysqli_num_rows($get_doctor) > 0) {
    while ($row_doctor = mysqli_fetch_assoc($get_doctor)) {
        $opt_id = (int) $row_doctor['id'];
        $opt_name = htmlspecialchars((string) $row_doctor['u_name'], ENT_QUOTES, 'UTF-8');
        $selected = ($has_registration_token && $doctor_id === $opt_id) ? ' selected' : '';
        $doctors_options_html .= '<option' . $selected . ' value="' . $opt_id . '">' . $opt_name . '</option>';
    }
}

include 'includes/head.php';
?>
	<title>SECOND TURN - <?php echo $company_trademark; ?></title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
</head>

<body class="background_image_ycdo" onkeydown="return (event.keyCode != 116)">
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
<?php if (isset($_GET['save_error']) && $_GET['save_error'] === 'no_procedure') { ?>
        <div class="col-md-12"><div class="alert alert-danger">Please select a procedure from the dropdown before saving.</div></div>
<?php } elseif (isset($_GET['save_error']) && $_GET['save_error'] !== '') { ?>
        <div class="col-md-12"><div class="alert alert-danger">Could not save procedure token. Please try again.</div></div>
<?php } elseif (isset($_GET['cart_dup']) && $_GET['cart_dup'] !== '') { ?>
        <div class="col-md-12"><div class="alert alert-warning">Procedure not added — select a procedure from the list first, or it is already in the cart.</div></div>
<?php } ?>
        <div class="col-md-12">
        	<form name="search" method="get">
        		<div class="row">
        			<div class="col-md-1"></div>
        			<div class="col-md-4 btn btn-outline-primary">
        				<label>NEXT TOKEN NO:<?php echo (int) $next_token_display . ' / ' . date('y'); ?></label>
        			</div>
        		<div class="col-md-1"></div>
        		<div class="col-md-5 btn btn-sm btn-outline-info">
        			<label class="">SELECTED TOKEN NO : <span><?php echo (int) $search_tokan_no; ?></span></label>
        		</div>
        		</div>
        	</form>
        </div>
    </div>
<form method="get" id="addProcedureForm" onsubmit="return validateAddProcedure(this);">
<div class="row">
<div class="col-md-12">
	<fieldset class="border p-2">
	<legend style="font-size: 14px;" class="w-auto">SELECT TEST OR MEDICINE OR PROCEDURE</legend>
	<div class="alert alert-info" style="margin-bottom: 10px; font-size: 14px;">
		<strong>Step 1:</strong> Select a procedure from the dropdown below.<br>
		<strong>Step 2:</strong> Click <strong>ADD</strong> to add it to the cart (recommended), <em>or</em> go straight to <strong>SAVE</strong> — the selected procedure will be added automatically.
	</div>
	<div class="row">

	<div class="col-md-6">
	<input type="hidden" name="search_tokan_no" value="<?php echo (int) $search_tokan_no; ?>" />
		<select required name="reg_item_id" id="select_item" placeholder="Pick Procedure" class="form-control bg-info">
			<option value="">Loading procedures…</option>
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
		<input class="form-control" id="fix_dose" type="number" name="fix_dose" value="0" min="0">
    </div>
  </div>
	</div>
</div>
<div class="col-md-12" style="text-align: right;" >
	<input type="submit" onclick="myDisplayGoneAdd()" id="add" name="save_test" value="ADD PROCEDURE" class="btn btn-lg btn-success" style="font-weight: bold; min-width: 180px;">
	<input type="submit" name="clear" value="CLEAR" class="btn btn-sm btn-warning">
</div>


   	</div>
   	<div class="col-md-6">
   		<input type="hidden" id="tokan_no" name="tokan_no" value="<?php echo (int) $search_tokan_no; ?>">
   		<select id="mySelect" ondblclick="del_medicine();" class="form-control" size="6" title="Cart — double-click to remove">
   			<?php echo $cart_options_html; ?>
   		</select>
   		<small class="text-muted"><?php echo (int) $cart_item_count; ?> item(s) in cart</small>
   	</div>

   </div>
</fieldset>

</div>

</div>

</form>

<form method="get" onsubmit="syncProcedureToSaveForm(); return checknumber(this);">
<input type="hidden" name="search_tokan_no" value="<?php echo (int) $search_tokan_no; ?>">
<input type="hidden" name="tokan_payment" value="104">
<input type="hidden" name="reg_item_id" id="save_reg_item_id" value="">
<input type="hidden" name="fix_dose" id="save_fix_dose" value="0">
<input type="hidden" name="dose" value="1">
<input type="hidden" name="feed" value="1">
<input type="hidden" name="days" value="1">
<div class="row">
<?php if ($has_registration_token) { ?>
	<div class="col-md-3">
		<label>Patient Name</label>
		<input type="hidden" name="patient_id" value="<?php echo (int) $patient_id; ?>">
		<input type="hidden" name="previous_tokan_no" value="<?php echo (int) $tokan_no; ?>">
		<input readonly type="text" class="form-control" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
	</div>
	<div class="col-md-2">
		<label> Age</label>
		<input readonly type="number" value="<?php echo htmlspecialchars($age, ENT_QUOTES, 'UTF-8'); ?>" min="0" class="form-control">
	</div>
	<div class="col-md-2">
		<label> Gender</label>
		<select readonly required class="form-control">
<?php
if ($gender == 1) {
    echo '<option value="1"> Female</option>';
} elseif ($gender == 2) {
    echo '<option value="2"> Male</option>';
} else {
    echo '<option value="3"> Other</option>';
}
?>
		</select>
	</div>
	<div class="col-md-3">
		<label>Operation By</label>
		<select name="doctor_id" required class="form-control">
		<?php echo $doctors_options_html; ?>
		</select>
	</div>
   	<div class="col-md-2">
   		<label>Cash</label>
   		<textarea readonly required rows="1" style="resize: none;" id="cash" name="cash" class="form-control"><?php echo (int) $initial_cart_amount; ?></textarea>
   	</div>
<?php } else { ?>
	<div class="col-md-12">
		<div class="alert alert-warning">Enter a valid registration token from Procedure Token page first.</div>
	</div>
<?php } ?>


   	<div class="col-md-7" style="font-size: 15px;">
   		<label>Amount Token Type</label><br>
   		<input type="radio" id="general" name="tokan_payment" value="104" checked>
   		<label for="general">General</label>
   	</div>


   	<div class="col-md-3">
   		<label>Cash Received</label>
   		<input type="number" min="0" name="cash_received" class="form-control" required>
   	</div>

	<div class="col-md-2">
		<br>
<?php if ($has_registration_token) { ?>
        <input type="submit" id="save" onclick="myDisplayGoneSave()" value="SAVE" name="save" class="btn btn-lg btn-primary" style="font-weight: bold; min-width: 120px;">
        <small class="text-muted d-block">Select procedure above, then SAVE</small>
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
var procedureSelectize;
var initialCartAmount = <?php echo (int) $initial_cart_amount; ?>;
var cartItemCount = <?php echo (int) $cart_item_count; ?>;

function syncProcedureSelectValue() {
  if (!procedureSelectize) {
    return '';
  }
  var val = procedureSelectize.getValue() || '';
  $('#select_item').val(val);
  return val;
}

function initProcedureSelect() {
  if (procedureSelectize) {
    procedureSelectize.destroy();
  }
  procedureSelectize = $('#select_item').selectize({
    sortField: 'text',
    onChange: function () {
      syncProcedureToSaveForm();
    }
  })[0].selectize;
}

$(document).ready(function () {
  $.get('second_procedure_turn.php', { ajax: 'procedure_options' })
    .done(function (html) {
      $('#select_item').html('<option value="">Select Procedure</option>' + html);
      initProcedureSelect();
      syncProcedureToSaveForm();
    })
    .fail(function () {
      $('#select_item').html('<option value="">Could not load procedures</option>');
      initProcedureSelect();
    });
  $(".alert").alert();
  setCashAmount(initialCartAmount);
  $('#fix_dose').on('change input', syncProcedureToSaveForm);
});

function syncProcedureToSaveForm() {
  var regItemId = syncProcedureSelectValue();
  if (!regItemId) {
    var sel = document.getElementById('select_item');
    if (sel) {
      regItemId = sel.value;
    }
  }
  var saveReg = document.getElementById('save_reg_item_id');
  var saveFix = document.getElementById('save_fix_dose');
  var fixEl = document.getElementById('fix_dose');
  if (saveReg) {
    saveReg.value = regItemId;
  }
  if (saveFix && fixEl) {
    saveFix.value = fixEl.value;
  }
}

function validateAddProcedure(form) {
  var regItemId = syncProcedureSelectValue();
  if (!regItemId) {
    var sel = form.reg_item_id || document.getElementById('select_item');
    if (sel) {
      regItemId = sel.value;
    }
  }
  if (!regItemId) {
    alert('Please select a procedure from the dropdown first.');
    return false;
  }
  syncProcedureToSaveForm();
  return true;
}
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
</script>

<script type = "text/javascript" >
    function preventBack() { window.history.forward(); }
    setTimeout("preventBack()", 0);
    window.onunload = function () { null };
</script>
<script>
function myDisplayGoneAdd() {
  var el = document.getElementById("add");
  if (el) { el.style.display = "none"; }
}
function myDisplayGoneSave() {
  syncProcedureToSaveForm();
  var el = document.getElementById("save");
  if (el) { el.style.display = "none"; }
}
</script>
<script type="text/javascript">
function checknumber(theForm) {
  syncProcedureToSaveForm();
  var regId = document.getElementById('save_reg_item_id');
  var hasProcedure = regId && regId.value !== '';
  if (cartItemCount < 1 && !hasProcedure) {
    alert('Please select a procedure from the dropdown. You can click ADD first, or SAVE will add it automatically.');
    return false;
  }
  if (parseInt(theForm.cash.value, 10) > parseInt(theForm.cash_received.value, 10)) {
    alert('enter the correct amount');
    return false;
  }
  return true;
}
</script>
<?php
if (ob_get_level() > 0) {
    ob_end_flush();
}
