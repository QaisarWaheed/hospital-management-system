<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 

$role_title = '';
$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if(mysqli_num_rows($run_roles) == 1)
{
    while($row_role = mysqli_fetch_array($run_roles))
    {
        $role_title = $row_role['title'];
    }
}

if (isset($_GET['del_medicine']) && $_GET['del_medicine'] != '') 
{
	$token_id = $_GET['token_id'];
	$del_id = $_GET['del_medicine'];
// 	$delete = "DELETE FROM select_by_doctor WHERE id = '$del_id' AND `tokan_no` = '$token_id' ";
	$update = "UPDATE `select_by_doctor` SET `status` = '2' WHERE `id` = '$del_id' AND `tokan_no` = '$token_id' ";
	if (mysqli_query($con, $update)) 
	{
		echo '<script type="text/javascript">
            // alert("Data Deleted Successfully...");
            location.replace("patient_by_token.php?token_id="'.$token_id.');
		</script>';
	}
}
if (isset($_GET['save_test'])) 
{
    $token_id = $_GET['token_id'];
	$reg_item_id = ycdo_resolve_register_item_id($branch_id, $_GET['reg_item_id']);
	if ($reg_item_id < 1) {
		header('location: stop_patient_by_token.php?token_id='.$token_id.'&msg=ERROR-INVALID-ITEM');
		exit;
	}
	$item_id = get_item_id_by_register_item_id($reg_item_id);
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
	$insert = "INSERT INTO `select_by_doctor`
	(`tokan_no`, `item_id`, `dose`,  `feed`,  `days`,  `user_id`,  `branch_id`, `fix_dose`, `created`, `items_table_id`) VALUES 
	('$token_id', '$reg_item_id', '$dose', '$feed', '$days', '$user_id','$branch_id', '$fix_dose', '$current_date', '$item_id')";
	
 		if (mysqli_query($con, $insert))		
 		{ 
 		    $token_doctor_id = get_doctor_id_by_token_no($token_id);
 		    if(mysqli_query($con, "INSERT INTO `doctor_tokens`(`doctor_token`, `token_no`, `doctor_id`, `user_id`, `status`, `created`) VALUES (NULL, '$token_id', '$token_doctor_id', '$user_id', '1', '$current_date')"))
 		    {
     		    mysqli_query($con, "UPDATE tokans SET doctor_id = '$user_id'  WHERE id = '$token_id' ");
 		    }
			?>
	<script type="text/javascript">
			  location.replace("patient_by_token.php?token_id="<?php echo $token_id; ?>);
			</script>
	<?php
    // exit();
 		}
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke">	<?php include 'left_navigation.php'; ?>	
			<h3 style="margin-top: 350px;text-align: center;"><?php echo $_SESSION['dr_name'];if($_SESSION['is_incharge'] == 2){ echo " Incharge ";} ?>(<?php echo $role_title; ?>)</h3>
    </div>
<?php
if(isset($_GET['token_id']) && $_GET['token_id'] != '')
{ ?>
	<div class="col-md-9">
	    <div class="row">
        	<div class="col-md-10">
        	    <h2 align="center"><label>Token Detail</label></h2>
            	    <?php
            	    if(isset($_GET['token_id']) && $_GET['token_id'] != '')
            	    {
            	        $token_id = $_GET['token_id'];
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
                            <!--<form METHOD = "GET">-->
                            <!--<input type="hidden" class="form-control-plaintext" id="token_id" name="token_id" value="<?php echo $token_id; ?>">-->
                            <!--<input type="hidden" class="form-control-plaintext" id="new_dr_id" name="new_dr_id" value="<?php echo $user_id; ?>">-->
                            <!--    <input type="submit"  class="btn btn-xs btn-info" name="update_dr" value="<?php echo $user_name; ?>">-->
                            <!--</form>-->
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
<form action = "referral_patient_by_token.php" method = "POST">
    <input type="hidden" name="token_id" value="<?php echo $_GET['token_id']; ?>" />
	<fieldset class="border p-2">
	<legend style="font-size: 14px;" class="w-auto">
	    REFFRRAL PATIENT'S
	    <!--<span style = "color: white;font-size: 12px;" class = "bg-danger">WORKING PLEASE DO NOT USE THAT SECTION OF REFERRAL</span>-->
	</legend>
	
	<div class="row">
	<div class="col-md-9">
		<select required name="department_id" id="select_department" class = "bg-success" placeholder="Pick Select Department" autofocus>
			<option value="">Select Department...</option>
			<?php echo show_departments_option(); ?>
		</select>
	</div>
	<?php
	if(mysqli_num_rows(mysqli_query($con, "SELECT * FROM `referral_patients` WHERE `opd_token_id` = '$token_id' ")) == 0)
	{
    echo '<div class="col-md-3 w-auto" style="text-align: right;" >
    	<input onclick="myDisplayGoneAddDr()" id="add_dr" type="submit" name="save_depatment" value="REFERRAL PATIENT" class="btn w-auto btn-sm btn-primary">
    </div>';
	} ?>
  </div>
</form>
</div>
<form>
    <input type="hidden" name="token_id" value="<?php echo $_GET['token_id']; ?>" />
<div class="col-md-12">
	<fieldset class="border p-2">
	<legend style="font-size: 14px;" class="w-auto">SELECT TEST OR MEDICINE OR PROCEDURE</legend>
	<div class="row">
	<div class="col-md-8">
		<select required name="reg_item_id" id="select_item" class = "bg-info" placeholder="Pick Test, Medicine Or Procedure" autofocus>
			<option value="">Select Test, Medicine, Procedure...</option><?php echo branch_medicines_by_name(); ?>
		</select>
	</div>
	<div class="col-md-4">
      <label>DOSE:</label>
      <input type="radio" checked name="dose" value="1" id="od"><label for="od" title="ONCE A DOSE">OD</label><input type="radio" name="dose" value="2" id="bd"><label for="bd" title="TWO DOSES">BD</label><input type="radio" name="dose" value="3" id="tds"><label for="tds" title="THREE DOSES">TDS</label>
    </div> 

	<div class="col-md-12">
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-1 col-form-label">Feed:</label>
    <div class="col-sm-2">
		<select class="form-control" name="feed" required>
			<option value="0.5">Half</option><option selected value="1">One</option><option value="2">Two</option><option value="3">Three</option><option value="4">Four</option><option value="5">Five</option><option value="6">Six</option><option value="7">Seven</option>
		</select>
    </div>
    <label for="inputPassword" class="col-sm-1 col-form-label">Days:</label>
    <div class="col-sm-2">
		<input class="form-control" type="number" name="days" value="1" min="1">
    </div>
    <label for="fix_dose" class="col-sm-2 col-form-label">Fix/Not:</label>
    <div class="col-sm-2">
		<input class="form-control" id="fix_dose" type="number" name="fix_dose" value="0" min="0">
    </div>
    <div class="col-md-2" style="text-align: right;" >
    	<input onclick="myDisplayGoneAdd()" id="add" type="submit" name="save_test" value="ADD" class="btn btn-sm btn-primary"><input id="clear" type="reset" name="clear" value="CLEAR" class="btn btn-sm btn-warning">
    </div>
  </div>
	</div>

   	</div>
</fieldset>
</div>
</div>
</form>
<form>
    <fieldset>
        <legend style="font-size: 14px;" class="w-auto">SELECTED TEST AND MEDICINE</legend>
        <div class="row">
            <div class="col-md-6">
           		<select id="mySelect" ondblclick="del_medicine();" class="form-control" size="8">
           			<?php echo medicine_selected_by_doctor($token_id); ?>
           		</select>
            </div>            <div class="col-md-6">
           		<select id="mySelect_2" ondblclick="del_medicine_2();" class="form-control" size="8">
           			<?php echo test_selected_by_doctor($token_id); ?>
           		</select>
            </div>

        </div>
    </fieldset>
</form>
        	</div>
        	<div class="col-md-2">
                <div style="text-align: right;float: right;">
                    <div style="background-color: white;overflow:auto; max-height:550px;">
                                <form>
                                    <input type="text" id="token_id" required name="token_id" maxlength="8" size="8" pattern="[0-9]{1,}" title="Eight or more characters"><br>
                                    <input type="submit" class="btn btn-outline-success" value="SEARCH" />
                                </form>
                                <p>Token & Date</p>
                            <?php
                            // $date_select = date("Y-m-d");
                            // $select_token = "SELECT id, created FROM tokans WHERE status = '1' AND branch_id = '$branch_id' AND `created` LIKE '$date_select%'  ORDER BY `id` DESC ";
                            // $run_token = mysqli_query($con, $select_token);
                            // if(mysqli_num_rows($run_token) > 0)
                            // {
                            //     while($row_token = mysqli_fetch_array($run_token))
                            //     {
                            //         $token_no = $row_token['id'];
                            //         $token_created = date_format(date_create($row_token['created']), 'd-m'); 
                                    ?>
                                    <!--<a href="?token_id=<?php echo $token_no; ?>" class="btn btn-outline-danger btn-sm">-->
                                    <!--    <span><?php echo $token_created; ?> - <?php echo $token_no; ?></span>-->
                                    <!--</a></br>-->
                                    <?php    
                            //     } 
                            // }
                            ?>
                    </div>
                </div>        	    
        	</div>
	    </div>

			
	</div>
</div>
<?php }
else
{ ?>
	<div class="col-md-9">
        <div style="text-align: right;float: right;">
            <div style="background-color: white;overflow:auto; max-height:550px;">
                        <form>
                            <input type="text" id="token_id" required name="token_id" maxlength="8" size="8" pattern="[0-9]{1,}" title="Eight or more characters"><br>
                            <input type="submit" class="btn btn-outline-success" value="SEARCH" />
                        </form>
                        <p>Token & Date</p>
                    <?php
                    $date_select = date("Y-m-d");
                    $select_token = "SELECT id, created FROM tokans WHERE branch_id = '$branch_id' AND status = '1' AND `created` LIKE '$date_select%' ORDER BY `id` DESC ";
                    $run_token = mysqli_query($con, $select_token);
                    if(mysqli_num_rows($run_token) > 0)
                    {
                        while($row_token = mysqli_fetch_array($run_token))
                        {
                            $token_no = $row_token['id'];
                            $token_created = date_format(date_create($row_token['created']), 'd-m'); ?>
                            <a href="?token_id=<?php echo $token_no; ?>" class="btn btn-outline-danger btn-sm">
                                <span><?php echo $token_created; ?> - <?php echo $token_no; ?></span>
                            </a></br>
                            <?php    
                        } 
                    }
                    ?>
            </div>
        </div>        	    
	</div>
<?php
    echo "</div>";
}
?>
</body>
</html>
<script type="text/javascript">
      $(document).ready(function () {
  $('#select_item').selectize({
      sortField: 'text'
  });
  $(".alert").alert();
});
</script>

<script type="text/javascript">
      $(document).ready(function () {
  $('#select_department').selectize({
      sortField: 'text'
  });
  $(".alert").alert();
});
</script>

<script type="text/javascript">
function del_medicine() 
{
	var x = document.getElementById("mySelect").value;
	window.open('patient_by_token.php?token_id=<?php echo $token_id; ?>&del_medicine='+x,'_self');
}	
function del_medicine_2() 
{
	var x = document.getElementById("mySelect_2").value;
	window.open('patient_by_token.php?token_id=<?php echo $token_id; ?>&del_medicine='+x,'_self');
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
function myDisplayGoneAddDr() {
  document.getElementById("add_dr").style.display = "none";
}
</script> 

<script>
function myDisplayGoneSave() {
  document.getElementById("save").style.display = "none";
}
</script>
<?php mysqli_close($con); ?>