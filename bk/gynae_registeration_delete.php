<?php include 'includes/connect.php'; 
if (isset($_POST['save_delete'])) 
{
    $registeration_id = $_POST['registeration_id'];
    $gynae_register_delete_detail = $_POST['gynae_register_delete_detail'];
    $insert = "INSERT INTO `gynae_register_delete`
    (`gynae_register_delete_id`, `gynae_register_id`, `gynae_register_delete_detail`, `gynae_register_delete_created`, `gynae_register_delete_by`, `branch_id`)
    VALUE
    (NULL, '$registeration_id', '$gynae_register_delete_detail', '$current_date', '$user_id', '$branch_id')";
    if(mysqli_query($con, $insert))
    {
         $update = "
            UPDATE `gynae_register`
            SET
                `status` = '3'
            WHERE
                `id` = '$registeration_id' ";
            if(!mysqli_query($con, $update))
            {
                echo $con->error;
                exit();
            }
        header('location: dashboard.php');
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
                if(isset($_GET['del']) && $_GET['del'] != '')
                {
                    $up_id = $_GET['del'];
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
                ?>
                <form METHOD = "POST" autocomplete = "off" action = "gynae_registeration_delete.php">
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
                        <input readonly type = "text" name = "phone" pattern="[0-9]{11}" value = "<?php echo $phone; ?>" required class = "form-control" />
                    </div>
                    <div class = "col-md-6">
                        <label>TOTAL WEEKS</label>
                        <input type = "text" name = "weeks_visit_time" readonly value = "<?php echo $weeks; ?>" required class = "form-control" />
                    </div>
                    <div class = "col-md-6">
                        <label>UPDATE(OLD)</label>
                        <input readonly type = "TEXT" name = "previous_update_by" value = "<?php echo $update_by; ?>" required class = "form-control" />
                    </div>
                    <div class = "col-md-12">
                        <label>WHY DELETE?</label>
                        <textarea name = "gynae_register_delete_detail" class = "form-control"></textarea>
                    </div>
                    <div class = "col-md-12 p-4">
                        <input type = "submit" name = "save_delete" value = "DELETE FILE" required class = "btn btn-info" />
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