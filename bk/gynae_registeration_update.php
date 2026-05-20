<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) 
{
    $registeration_id = $_POST['registeration_id'];
    $last_visit_date = $_POST['last_visit_date'];
    $previous_remarks = $_POST['previous_remarks'];
    $previous_gravide = $_POST['previous_gravide'];
    $previous_update_by = $_POST['previous_update_by'];
    $weeks_visit_time = $_POST['weeks_visit_time'];
    $old_doctor_id = $_POST['old_doctor_id'];
    $token_no = $_POST['token_no'];
    $weeks = $_POST['weeks'];
    $remarks = $_POST['remarks'];
    $phone = $_POST['phone'];
    $gravida = $_POST['gravida'];
    $next_visit_date = $_POST['next_visit_date'];
    $doctor_id = $_POST['doctor_id'];
    $insert = "INSERT INTO `gynae_register_history`
    (`id`, `gynae_register_id`, `last_visit_date`, `previous_remarks`, `previous_gravide`, `previous_update_by`, `weeks_visit_time`, `user_id`, `status`, `created`, `branch_id`, `doctor_id`)
    VALUE
    (NULL, '$registeration_id', '$last_visit_date', '$previous_remarks', '$previous_gravide', '$previous_update_by', '$weeks_visit_time', '$user_id', '1', '$current_date', '$branch_id', '$old_doctor_id')";
    if(mysqli_query($con, $insert))
    {
         $update = "
            UPDATE `gynae_register`
            SET
                `phone` = '$phone',
                `gravide` = '$gravida',
                `next_visit_date` = '$next_visit_date',
                `update_by` = '$user_id',
                `status` = '1',
                `remarks` = '$remarks',
                `branch_id` = '$branch_id',
                `doctor_id` = '$doctor_id'
            WHERE
                `id` = '$registeration_id' ";
            if(!mysqli_query($con, $update))
            {
                echo $con->error;
                exit();
            }
    ?>
     <script>
         alert("FILE DATA SAVE SUCCESSFULLY...");
         window.location.replace("gynae_registeration.php");
     </script>   
<?php 
    }
    else
    {
        echo $con->error;
    }
    exit;
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
                if ($run && mysqli_num_rows($run) > 0)
                {
                while($row = mysqli_fetch_array($run))
                {
                $id = $row['id'];
                $token_no = $row['token_no'];
                $patient_name = get_patient_name_by_token_no($row['token_no']);
                $doctor_id = $row['doctor_id'];
                $update_by = $row['update_by'];
                $phone = $row['phone'];
                $remarks = $row['remarks'];
                $gravide = $row['gravide'];
                $weeks = ycdo_gynae_weeks_offset($row['weeks']);
                $next_visit_date = $row['next_visit_date'];
                $weeks_start_ymd = ycdo_safe_date_format($row['weeks'], 'Y-m-d', '');
                $next_visit_ymd = ycdo_safe_date_format($next_visit_date, 'Y-m-d', date('Y-m-d'));
                ?>
                <form METHOD = "POST" autocomplete = "off">
                <div class = "row">
                    <div class = "col-md-6">
                        <label>REGISTERATION ID</label>
                        <input type = "text" readonly required name = "registeration_id" value = "<?php echo $id; ?>" class = "form-control"/>
                    </div>
                    <div class = "col-md-6">
                        <label>TOKEN NO</label>
                        <input type = "text" readonly required name = "token_no" value = "<?php echo $token_no; ?>" class = "form-control"/>
                    </div>
                    <div class = "col-md-6">
                        <label>PATIENT NAME</label>
                        <input readonly type = "text" name = "patient_name" value = "<?php echo $patient_name; ?>" required class = "form-control" />
                    </div>
                    <div class = "col-md-6">
                        <label>PHONE</label>
                        <input type = "text" name = "phone" pattern="[0-9]{11}" value = "<?php echo $phone; ?>" required class = "form-control" />
                    </div>
                    <div class = "col-md-6">
                        <label>START DATE</label>
                        <input type = "date" name = "start_date" readonly value = "<?php echo $weeks_start_ymd; ?>" required class = "form-control" />
                    </div>
                    <div class = "col-md-6">
                        <label>TOTAL WEEKS</label>
                        <input type = "text" name = "weeks_visit_time" readonly value = "<?php echo $weeks; ?>" required class = "form-control" />
                    </div>
                    <div class = "col-md-6">
                        <label>GRAVIDA(OLD)</label>
                        <input readonly type = "TEXT" name = "previous_gravide" required value = "<?php echo $gravide; ?>" class = "form-control" />
                    </div>
                    <div class = "col-md-6">
                        <label>GRAVIDA(NEW)</label>
                        <input type = "TEXT" name = "gravida" required class = "form-control" />
                    </div>
                    <div class = "col-md-6">
                        <label>LAST VISIT DATE</label>
                        <input readonly type = "date" name = "last_visit_date" value = "<?php echo $next_visit_ymd; ?>" required class = "form-control bg-info" />
                    </div>
                    <div class = "col-md-6">
                        <label>NEXT VISIT DATE</label>
                        <input type = "date" name = "next_visit_date" required min = "<?php echo $next_visit_ymd; ?>" class = "form-control" />
                    </div>
                    <div class = "col-md-6">
                        <label>LAST TIME CHECK BY DOCTOR</label>
                        <?php
                        $select_dr = "SELECT * FROM users WHERE id = '$doctor_id' ";
                        $run_dr = mysqli_query($con, $select_dr);
                        if ($run_dr && mysqli_num_rows($run_dr) == 1)
                        {
                        while($row_dr = mysqli_fetch_array($run_dr))
                        {
                        $doctor_id = $row_dr['id'];
                        $doctor_name = $row_dr['u_name']; 
                        }
                        }
                        ?>
                        <input type = "hidden" name = "old_doctor_id" value = "<?php echo $doctor_id; ?>" class = "form-control" />
                        <input readonly type = "text" name = "doctor_name" value = "<?php echo $doctor_name; ?>" class = "form-control" />
                    </div>
                    <div class = "col-md-6">
	                    <label>DOCTOR</label>
	                    <select name = 'doctor_id' class = "form-control" required>
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
                    <div class = "col-md-6">
                        <label>REMARKS(OLD)</label>
                        <input readonly type = "TEXT" name = "previous_remarks" value = "<?php echo $remarks; ?>" required class = "form-control" />
                    </div>
                    <div class = "col-md-6">
                        <label>REMARKS(NEW)</label>
                        <input type = "TEXT" name = "remarks" required class = "form-control" />
                    </div>
                    <div class = "col-md-6">
                        <label>UPDATE(OLD)</label>
                        <input readonly type = "TEXT" name = "previous_update_by" value = "<?php echo $update_by; ?>" required class = "form-control" />
                    </div>
                    <div class = "col-md-6">
                        <label>UPDATE(NEW)</label>
                        <input type = "TEXT" name = "update_by" value = "<?php echo $user_name; ?>" required class = "form-control" />
                    </div>
                    <div class = "col-md-12 p-4">
                        <input type = "submit" name = "save" value = "UPDATE FILE" required class = "btn btn-info" />
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
<?php mysqli_close($con); ?>