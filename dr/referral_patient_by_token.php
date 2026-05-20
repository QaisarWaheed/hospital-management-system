<?php 
include 'includes/connect.php'; 
?>
<?php include 'includes/head.php'; 

$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if(mysqli_num_rows($run_roles) == 1)
{
    while($row_role = mysqli_fetch_array($run_roles))
    {
        $role_title = $row_role['title'];
    }
}
else
{
    $role_title = '';
}

if (isset($_POST['save_referral_patient'])) 
{
    $token_id = $_POST['token_id'];
    $department_id = $_POST['department_id'];
    $consultant_id = $_POST['consultant_id'];
    $required_opinion = $_POST['required_opinion'];
    $insert = "INSERT INTO `referral_patients`
    (`referral_patient_id`, `opd_token_id`, `department_id`, `from_user_id`, `to_user_id`, `required_opinion`, `referral_patient_status`, `referral_patient_created`, `branch_id`) 
    VALUES
    (NULL, '$token_id', '$department_id', '$user_id', '$consultant_id', '$required_opinion', '1', '$current_date', '$branch_id')";
    if(mysqli_query($con, $insert))
    {
    ?>
     <script>
        alert("DATA SEND TO RECEPTION SUCCESSFULLY....");
        window.location.replace("patient_by_token.php?token_id=<?php echo $token_id; ?>");
     </script>   
<?php 
    exit(0);
    }
    else
    {
        echo $con->error;
        exit(0);
    }
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
<script src="js/jquery.min.js"></script>
<script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke">	<?php include 'left_navigation.php'; ?>	
			<h3 style="margin-top: 350px;text-align: center;"><?php echo $_SESSION['dr_name'];if($_SESSION['is_incharge'] == 2){ echo " Incharge ";} ?>(<?php echo $role_title; ?>)</h3>
    </div>
<?php
if(isset($_POST['token_id']) && $_POST['token_id'] != '')
{ ?>
	<div class="col-md-9">
	    <div class="row">
        	<div class="col-md-12">
        	    <h2 align="center"><label>Token Detail</label></h2>
            	    <?php
            	    if(isset($_POST['token_id']) && $_POST['token_id'] != '')
            	    {
            	        $department_id = $_POST['department_id'];
            	        $token_id = $_POST['token_id'];
            	        $select_token = "SELECT * FROM tokans WHERE id = '$token_id' ";
            	        $run_token = mysqli_query($con, $select_token);
            	        if(mysqli_num_rows($run_token) == 1)
            	        {
            	            while($row_token = mysqli_fetch_array($run_token))
            	            {
            	                $token_date = date_format(date_create($row_token['created']), 'd-m-Y');
            	                $docotr_id = $row_token['doctor_id'];
            	                $docotr_name = get_uname_by_id($docotr_id);
            	                $patient_id = $row_token['patient_id'];
            	                    $get_patient = mysqli_query($con, "SELECT * FROM patients WHERE id = '$patient_id' ");
                                    if (mysqli_num_rows($get_patient) == 1) 
                                    {
                                        while ($row_patient = mysqli_fetch_array($get_patient)) 
                                        {
                                            $name = $row_patient['name'];
                                            $age = $row_patient['age'];
                                            $cnic = $row_patient['cnic'];
                                            if($cnic == ''){$cnic = 'N/A';}
                                            $phone = $row_patient['phone'];
                                            if($phone == ''){$phone = 'N/A';}
                                            $gender = $row_patient['gender'];
                                            if($gender == '1'){$gender = 'Female';}elseif($gender == '2'){$gender = 'Male';}else{$gender = 'Transgender';}
                                        }
                                    }
            	   ?>  
                        <div class="form-group row">
                            <label for="token_no" class="col-sm-2 col-form-label">Token No</label>
                            <div class="col-sm-2">
                                <input type="text" readonly class="form-control-plaintext" id="token_no" name="token_no" value="<?php echo $token_id; ?>">
                            </div>
                            <label for="token_no" class="col-sm-2 col-form-label">Token Date</label>
                            <div class="col-sm-2">
                                <input type="text" readonly class="form-control-plaintext" id="token_no" name="token_no" value="<?php echo $token_date; ?>">
                            </div>
                            <div class="col-sm-4">
                                <?php if($docotr_id != $user_id)
                                { ?>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group">
<textarea style="resize: none;" readonly class="form-control" id="detail" rows="3">
Name: <?php echo $name; ?>

Gender : <?php echo $gender; ?>, Age : <?php echo $age; ?>, Phone : <?php echo $phone; ?>, CNIC: <?php echo $cnic; ?>

Dr Name: <?php echo $docotr_name; ?>
</textarea>
                        </div>
            	   <?php
            	            }
            	        }
            	    }
            	    ?>
<div class="row">
    <div class="col-md-12">
        <input type="hidden" name="token_id" value="<?php echo $_POST['token_id']; ?>" />
    	<fieldset class="border p-2">
    	<legend style="font-size: 14px;" class="w-auto">
    	    REFFRRAL PATIENT'S
    	</legend>
    	
    	<div class="row">
    	<div class="col-md-12">
    		<select required name="department_id" id="select_department" class = "bg-success" placeholder="Pick Select Department" autofocus>
    			<?php echo show_department_by_id($department_id); ?>
    		</select>
    	</div>
      </div>
    </div>
    <div class="col-md-12">
    <form method = "POST" >
        <input type="hidden" name="token_id" value="<?php echo $_POST['token_id']; ?>" />
        <input type="hidden" name="department_id" value="<?php echo $_POST['department_id']; ?>" />
    	<fieldset class="border p-2">
    	<legend style="font-size: 14px;" class="w-auto">
    	    Select Consultant
    	</legend>
    	
    	<div class="row">
    	<div class="col-md-12">
    		<select required name="consultant_id" id="consultant_id" class = "bg-success" placeholder="Select Doctor" autofocus>
    			<?php echo show_doctors_by_department_id($department_id); ?>
    		</select>
    	</div>
    	<div class="col-md-12">
    	    <label for = "required_opinion">Required Opinion From Consultant</label>
    	    <input type = "text" name = "required_opinion" class = "form-control" required />
    	</div>
    	<div class="col-md-12 g-2 p-2">
    	    <input type = "submit" value = "SEND DATA TO RECEPTION" name = "save_referral_patient" class = "btn btn-success" />
    	</div>
      </div>
      </fieldset>
    </form>
    </div>
</div>
</div>

			
	</div>
</div>
<?php }
else
{ ?>
<?php
    echo "</div>";
}
?>
</body>
</html>

<script type="text/javascript">
      $(document).ready(function () {
  $('#select_department').selectize({
      sortField: 'text'
  });
  $(".alert").alert();
});
</script>

<script type="text/javascript">
      $(document).ready(function () {
  $('#consultant_id').selectize({
      sortField: 'text'
  });
  $(".alert").alert();
});
</script>

<script type="text/javascript" src="js/bootstrap.min.js"></script>

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
  document.getElementById("save").style.display = "none";
}
</script>
<?php mysqli_close($con); ?>