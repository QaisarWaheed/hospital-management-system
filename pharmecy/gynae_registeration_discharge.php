<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) 
{
    $registeration_id = $_POST['registeration_id'];
    $token_no = $_POST['token_no'];
    $procedure_token_no = $_POST['procedure_token_no'];
    $patient_name = $_POST['patient_name'];
    $phone = $_POST['phone'];
    $consultant = $_POST['consultant'];
    $ota = $_POST['ota'];
    $anesthetic = $_POST['anesthetic'];
    $sergeon = $_POST['sergeon'];
    $department = $_POST['department'];
    $postal_address = $_POST['postal_address'];
    $diagnosis = $_POST['diagnosis'];
    $doa = $_POST['doa'];
    $dos = $_POST['dos'];
    $dod = $_POST['dod'];
    $presenting_complaints = $_POST['presenting_complaints'];
    $brief_history = $_POST['brief_history'];
    $efap = $_POST['efap'];
    $investigations = $_POST['investigations'];
    $final_diagnosis = $_POST['final_diagnosis'];
    $treatment_given = $_POST['treatment_given'];
    $cattod = $_POST['cattod'];
    $follow_up = $_POST['follow_up']; 
    $insert = "INSERT INTO `gynae_register_discharge` (`gynae_discharge_id`, `registeration_id`, `token_no`, `procedure_token_no`, `phone`, `consultant`, `ota`, `anesthetic`, `sergeon`, `department`, `postal_address`, `diagnosis`, `doa`, `dos`, `dod`, `presenting_complaints`, `brief_history`, `efap`, `investigations`, `final_diagnosis`, `treatment_given`, `cattod`, `follow_up`, `gynae_discharge_status`, `gynae_discharge_created`) VALUES (NULL,'$registeration_id','$token_no','$procedure_token_no','$phone','$consultant','$ota','$anesthetic','$sergeon','$department','$postal_address','$diagnosis','$doa','$dos','$dod','$presenting_complaints','$brief_history','$efap','$investigations','$final_diagnosis','$treatment_given','$cattod','$follow_up','1','$current_date')";
    if(mysqli_query($con, $insert))
    {
        $data = "UPDATE `gynae_register` SET `status` = '2' WHERE status = '1' AND `id` = '$registeration_id' ";
        mysqli_query($con, $data);
    ?>
     <script>
         window.location.replace("gynae_registeration_discharge_print.php?update=<?php echo $registeration_id; ?>");
     </script>   
<?php
    }
    else
    {
    exit(0);
    }
}
?>
<?php include 'includes/head.php'; ?>
	<title>Patient Registeration - <?php echo $company_trademark; ?></title>
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
                <?php
                if(isset($_GET['update']) && $_GET['update'] != '')
                {
                    $up_id = $_GET['update'];
                $select = "SELECT * FROM `gynae_register` WHERE id = '$up_id' ";
                $run = mysqli_query($con, $select);
                if(mysqli_num_rows($run) > 0)
                {
                while($row = mysqli_fetch_array($run))
                {
                $id = $row['id'];
                $token_no = $row['token_no'];
                $patient_name = get_patient_name_by_token_no($row['token_no']);
                $doctor_id = $row['doctor_id'];
                $update_by = $row['update_by'];
                $phone = $row['phone'];
                $start_date = ycdo_safe_date_format($row['weeks'], 'd/m/Y H:i:s', '');
                $next_visit_date = $row['next_visit_date'];
                $to_date = date('d/m/Y H:i:s');
                $weeks = weeks_between($start_date, $to_date);
                ?>
                <form METHOD = "POST" autocomplete = "off">
                <div class = "row">
                    <div class = "col-md-4">
                        <label>REGISTERATION ID</label>
                        <input type = "text" readonly required name = "registeration_id" value = "<?php echo $id; ?>" class = "form-control"/>
                    </div>
                    <div class = "col-md-4">
                        <label>TOKEN NO</label>
                        <input type = "text" readonly required name = "token_no" value = "<?php echo $token_no; ?>" class = "form-control"/>
                    </div>
                    <div class = "col-md-4">
                        <label for = "procedure_token_no">PROCEDURE TOKEN NO</label>
                        <input type = "number" required name = "procedure_token_no" class = "form-control"/>
                    </div>
                        
                    <div class = "col-md-4">
                        <label>PATIENT NAME</label>
                        <input readonly type = "text" name = "patient_name" value = "<?php echo $patient_name; ?>" required class = "form-control" />
                    </div>
                    <div class = "col-md-4">
                        <label>PHONE</label>
                        <input type = "text" name = "phone" pattern="[0-9]{11}" value = "<?php echo $phone; ?>" required class = "form-control" />
                    </div>
                    <div class = "col-md-4">
	                    <label>CONSULTANT</label>
	                    <select name = 'consultant' class = "form-control" required>
	                    <?php
	                    $select_dr = "SELECT * FROM users WHERE role_id = 3 AND branch_id = '$branch_id' AND status = '1' ";
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
	                    <label>OTA</label>
	                    <select name = 'ota' class = "form-control" required>
	                    <?php
	                    $select_dr = "SELECT * FROM users WHERE role_id = 13 AND status = '1' ";
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
	                    <label>ANESTHETIC</label>
	                    <select name = 'anesthetic' class = "form-control" required>
	                    <?php
	                    $select_dr = "SELECT * FROM users WHERE role_id = 14 AND status = '1' ";
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
	                    <label>SURGEON</label>
	                    <select name = 'sergeon' class = "form-control" required>
	                    <?php
	                    $select_dr = "SELECT * FROM users WHERE role_id = 15 AND status = '1' ";
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
                        <label for = "department">DEPARTMENT</label>
                        <input type = "text" required name = "department" class = "form-control"/>
                    </div>
                    <div class = "col-md-4">
                        <label for = "postal_address">POSTAL ADDRESS</label>
                        <input type = "text" required name = "postal_address" class = "form-control"/>
                    </div>
                    <div class = "col-md-4">
                        <label for = "diagnosis">DIAGNOSIS</label>
                        <input type = "text" required name = "diagnosis" class = "form-control"/>
                    </div>
                    <div class = "col-md-4">
                        <label for = "doa">DOA</label>
                        <input type = "date" required name = "doa" class = "form-control"/>
                    </div>
                    <div class = "col-md-4">
                        <label for = "dos">DOS</label>
                        <input type = "date" required name = "dos" class = "form-control"/>
                    </div>
                    <div class = "col-md-4">
                        <label for = "dod">DOD</label>
                        <input type = "date" required name = "dod" class = "form-control"/>
                    </div>
                    
                    <div class = "col-md-12">
                        <label for = "presenting_complaints">PRESENTING COMPLAINTS</label>
                        <input type = "text" required name = "presenting_complaints" class = "form-control"/>
                    </div>
                    <div class = "col-md-12">
                        <label for = "brief_history">BRIEF HISTORY</label>
                        <input type = "text" required name = "brief_history" class = "form-control"/>
                    </div>
                    <div class = "col-md-12">
                        <label for = "efap">EXAMINATION FINDINGS AT PRESENTATION</label>
                        <input type = "text" required name = "efap" class = "form-control"/>
                    </div>
                    <div class = "col-md-12">
                        <label for = "investigations">INVESTIGATIONS</label>
                        <input type = "text" required name = "investigations" class = "form-control"/>
                    </div>
                    <div class = "col-md-12">
                        <label for = "final_diagnosis"> FINAL DIAGNOSIS</label>
                        <input type = "text" required name = "final_diagnosis" class = "form-control"/>
                    </div>
                    <div class = "col-md-12">
                        <label for = "treatment_given">TREATMENT GIVEN</label>
                        <input type = "text" required name = "treatment_given" class = "form-control"/>
                    </div>
                    <div class = "col-md-12">
                        <label for = "cattod">CONDITION AT THE TIME OF DISCHARGE</label>
                        <input type = "text" required name = "cattod" class = "form-control"/>
                    </div>
                    <div class = "col-md-12">
                        <label for = "follow_up">FOLLOW UP</label>
                        <input type = "text" required name = "follow_up" class = "form-control"/>
                    </div>
                    <div class = "col-md-12 p-4">
                        <input type = "submit" name = "save" value = "DISCHARGE PATIENT" required class = "btn btn-info" />
                    </div>
                </div>
                </form>
                <?php
                }
                }
     }?>
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