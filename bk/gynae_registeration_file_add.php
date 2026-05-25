<?php
include 'includes/connect.php';
require_once __DIR__ . '/../includes/gynae_helpers.php';

if (isset($_POST['save'])) {
    $token_no = $_POST['token_no'];
    $weeks = $_POST['weeks'];
    $remarks = $_POST['remarks'];
    $phone = $_POST['phone'];
    $lmp = $_POST['lmp'];
    $years_marriage = $_POST['years_marriage'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $blood_group = $_POST['blood_group'];
    $husband_blood_group = $_POST['husband_blood_group'];
    $menstrual_cycle = $_POST['menstrual_cycle'];
    $psh = $_POST['psh'];
    $pmh = $_POST['pmh'];
    $husband_name = $_POST['husband_name'];
    $husband_phone = $_POST['husband_phone'];
    $gravida = $_POST['gravida'];
    $next_visit_date = $_POST['next_visit_date'];
    $doctor_id = $_POST['doctor_id'];
    $insert = "INSERT INTO `gynae_register`
    (`id`, `token_no`, `phone`, `weeks`, `gravide`, `next_visit_date`, `update_by`, `status`, `remarks`, `created`, `branch_id`, `doctor_id`, `user_id`, `husband_name`, `husband_phone`, `lmp`, `years_marriage`, `height`, `weight`, `blood_group`, `husband_blood_group`, `menstrual_cycle`, `psh`, `pmh`, `register_by_doctor`)
    VALUES
    (NULL, '$token_no', '$phone', '$weeks', '$gravida', '$next_visit_date', '$user_id', '1', '$remarks', '$current_date', '$bk_branch_id', '$doctor_id', '$user_id', '$husband_name', '$husband_phone', '$lmp', '$years_marriage', '$height', '$weight', '$blood_group', '$husband_blood_group', '$menstrual_cycle', '$psh', '$pmh', '$doctor_id')";
    if (mysqli_query($con, $insert)) {
        $last_id = (int) mysqli_insert_id($con);
        $print_url = 'gynae_registeration_file_print.php?reg_id=' . $last_id;
        ?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Saving…</title></head>
<body>
<script>
window.open(<?php echo json_encode(ycdo_absolute_url_if_relative($print_url)); ?>, '_blank', 'toolbar=no,scrollbars=no,resizable=no,width=400,height=400');
window.location.replace('gynae_registeration.php');
</script>
</body>
</html>
<?php
        exit;
    }
    header('Location: gynae_registeration_file_add.php?msg=ERROR-SAVE');
    exit;
}

include 'includes/head.php';
?>
	<title>Patient Registeration - <?php echo $company_trademark; ?></title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
<script>
  $(document).ready(function () {
      $('select').selectize({
          sortField: 'text'
      });
  });    
</script>
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
			    <table class = "table">
			            <tr>
		                <form METHOD = "POST">
			                <th colspan = "13">
			                    <div class = "row">
			                        <div class = "col-md-4">
			                            <label>TOKEN NO</label>
        			                    <select name = 'token_no' class = "form-control" required>
        			                    <?php
        			                    $eligible_tokens = ycdo_gynae_eligible_tokens_list($con, $bk_branch_id);
        			                    if (count($eligible_tokens) === 0) {
        			                        echo '<option value="">No gynae tokens available</option>';
        			                    }
        			                    foreach ($eligible_tokens as $row) {
        			                        $token_no = (int) $row['token_no'];
        			                        $patinet_name = htmlspecialchars($row['patient_name'], ENT_QUOTES, 'UTF-8');
        			                        echo '<option value="' . $token_no . '">' . $token_no . ' - ' . $patinet_name . '</option>';
        			                    }
        			                    ?>
        			                    </select>
			                        </div>
			                        <div class = "col-md-4">
        			                    <label title = "Last Menstrual Period">L.M.P</label>
        			                    <input type = "date" name = "lmp" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label title = "ESTIMATE DELIVERY DATE">E.D.D</label>
        			                    <input type = "date" name = "weeks" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label for = "years_marriage">YEARS MARRIAGE</label>
        			                    <input type = "number" name = "years_marriage" id = "years_marriage" min="1" max = "999" value = "0" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label for = "height">HEIGHT</label>
        			                    <input type = "number" name = "height" id = "height" min="1" max = "999" value = "0" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label for = "weight">WEIGHT</label>
        			                    <input type = "number" name = "weight" id = "weight" min="1" max = "999" value = "0" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label>GRAVIDA</label>
        			                    <input type = "TEXT" name = "gravida" maxlength="10" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label for = "blood_group">BLOOD GROUP</label>
                                        <select name = "blood_group" id="select-state"  placeholder="Pick a Blood Group...">
                                            <option value="">Select a Blood Group...</option>
                                            <option value="A+">A+</option>
                                            <option value="AB+">AB+</option>
                                            <option value="B+">B+</option>
                                            <option value="O+">O+</option>
                                            <option value="A-">A-</option>
                                            <option value="AB-">AB-</option>
                                            <option value="B-">B-</option>
                                            <option value="O-">O-</option>
                                        </select>
			                        </div>
			                        <div class = "col-md-4">
        			                    <label>PHONE</label>
        			                    <input type = "text" name = "phone" pattern="[0-9]{10}" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label>HUSBAND NAME</label>
        			                    <input type = "TEXT" name = "husband_name" maxlength="30" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label for = "husband_blood_group">HUSBAND BLOOD GROUP</label>
                                        <select name = "husband_blood_group" id="select-state" placeholder="Pick a Blood Group...">
                                            <option value="">Select a Blood Group...</option>
                                            <option value="A+">A+</option>
                                            <option value="AB+">AB+</option>
                                            <option value="B+">B+</option>
                                            <option value="O+">O+</option>
                                            <option value="A-">A-</option>
                                            <option value="AB-">AB-</option>
                                            <option value="B-">B-</option>
                                            <option value="O-">O-</option>
                                        </select>
			                        </div>
			                        <div class = "col-md-4">
        			                    <label>HUSBAND PHONE</label>
        			                    <input type = "text" name = "husband_phone" pattern="[0-9]{10}" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label>NEXT VISIT DATE</label>
        			                    <input type = "date" name = "next_visit_date" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label>DOCTOR</label>
        			                    <select name = 'doctor_id' class = "form-control" required>
        			                    <?php
        			                    $select_dr = "SELECT * FROM users WHERE role_id = 3 AND branch_id = '$bk_branch_id' AND status = '1' ";
        			                    $run_dr = mysqli_query($con, $select_dr);
        			                    if(mysqli_num_rows($run_dr) > 0)
        			                    {
        			                        while($row_dr = mysqli_fetch_array($run_dr))
        			                        {
        			                            $doctor_id = $row_dr['id'];
        			                            $doctor_name = $row_dr['u_name'];
        			                            echo '<option value = "'.$doctor_id.'">'.$doctor_name.'</option>';
        			                        }
        			                    }
        			                    ?>
        			                    </select>
			                        </div>
			                        <div class = "col-md-4">
        			                    <label for = "menstrual_cycle">MENSTRUAL CYCLE</label>
        			                    <input type = "TEXT" name = "menstrual_cycle" id = "menstrual_cycle" maxlength="50" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label for = "psh">PAST SURGICAL HISTORY</label>
        			                    <input type = "TEXT" name = "psh" id = "psh" maxlength="50" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label for = "pmh">PAST MEDICAL HISTORY</label>
        			                    <input type = "TEXT" name = "pmh" id = "pmh" maxlength="50" required class = "form-control" />
			                        </div>
			                        <div class = "col-md-4">
        			                    <label>REMARKS</label>
        			                    <input type = "TEXT" name = "remarks" required class = "form-control" />
			                        </div>
			                        <div class = "col p-4">
        			                    <input type = "submit" name = "save" value = "SUBMIT FILE" required class = "btn btn-info" />
			                        </div>
			                    </div>
			                </th>
		                </form>
			            </tr>
			    </table>
			</div>
		</div>

	</div>

</div>


</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
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
<?php mysqli_close($con); ?>