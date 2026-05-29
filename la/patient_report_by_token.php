<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
?>
<?php include 'includes/head.php'; 
if(isset($_POST['save_sample']) )
{
    
    $token_id = $_POST['token_id'];
    $reporting_date_time = $_POST['reporting_date_time'];
    $run = mysqli_query($con, "SELECT item_by_doctor.id AS record_id, items.id AS item_id, items.name AS item_name FROM `item_by_doctor` INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id WHERE `tokan_no` = '$token_id' AND items.category_id = 2");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $ser = $ser + 1;
            $item_id = $row['item_id'];
            $reporting_date_time = $_POST['reporting_date_time'];
            $insert = "INSERT INTO `lab_tests`
            (`lab_test_id`, `token_no`, `item_id`, `lab_test_status`, `user_id`, `sample_date_time`, `reporting_date_time`) 
            VALUES
            (NULL, '$token_id', '$item_id', '1', '$lab_user_id', '$current_date', '$reporting_date_time')";
            if(!mysqli_query($con, $insert))
            {
                goto out;
            }
        }
        header('location: print_test_report_slip.php?token_no='.$token_id);
        exit(0);    
    }
    else
    {
        out:
        echo '<script>alert("DATA ALREADY SAVED...");</script>';
    }
}
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
?>
	<title>2nd Turn - Lab - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
</div>
<div class="row" style="margin: 0px;min-height:600px;">
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
		<h3 style="margin-top: 200px;text-align: center;">USER: <?php echo $_SESSION['lab_user_name'];if($_SESSION['lab_login_is_incharge'] == 2){ echo " Incharge ";} ?>(<?php echo $role_title; ?>)</h3>
	</div>
	<div class="col-md-9">
        <form method = "POST">
            <div class="col-md-12">
            <fieldset class="border p-2">
            <legend style="font-size: 14px;" class="w-auto">SEARCH PATIENT TOKEN </legend>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group row">   
                        <div class="col-sm-10">
                            <input autocomplete="off" autofocus  type="number" id="token_id" required name="token_id" value = "<?php echo $_POST['token_id']; ?>" class = "form-control" pattern="[0-9]{1,}" title="ENTER PATIENT TOKEN NO" placeholder = "ENTER PATIENT TOKEN NO">
                        </div> 
                        <div class="col-sm-2">
                            <input type="submit" class="btn btn-outline-success" value="SEARCH" />
                        </div>
                    </div>  
                </div>  
            </div>
            </fieldset> 
            </div>
        </form>
<?php
if(isset($_POST['token_id']) && $_POST['token_id'] != '')
{ ?>
	    <div class="row">
        	<div class="col-md-10">
            	    <?php
            	    if(isset($_POST['token_id']) && $_POST['token_id'] != '')
            	    {
            	        $token_id = $_POST['token_id'];
            	        $select_token = "SELECT * FROM tokans WHERE id = '$token_id' ";
            	        $run_token = mysqli_query($con, $select_token);
            	        if(mysqli_num_rows($run_token) == 1)
            	        {
            	            while($row_token = mysqli_fetch_array($run_token))
            	            {
            	                $token_date = date_format(date_create($row_token['created']), 'd-F-Y');
            	                $token_time = date_format(date_create($row_token['created']), 'h:i:s A');
            	                $token_branch_tag_name = get_branch_tag_by($row_token['branch_id']);
            	                $docotr_id = $row_token['doctor_id'];
            	                $cash = $row_token['cash'];
            	                $cash_received = $row_token['cash_received'];
            	                $token_type_id = $row_token['tokan_type_id'];
                	                $token_type = "SELECT title FROM `tokan_types` WHERE id = '$token_type_id' ";
                	                $run_token_type = mysqli_query($con, $token_type);
                                    if (mysqli_num_rows($run_token_type) == 1) 
                                    {
                                        while ($row_token_type = mysqli_fetch_array($run_token_type)) 
                                        {
                                            $token_type_title = $row_token_type['title'];
                                        }
                                    }
            	                $token_by = get_uname_by_id($row_token['user_id']);
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
                    </div>
                </div>
            	   <?php
            	            }
            	        }
            	    }
?>
        <form method = "POST">
            <input type="hidden" name="token_id" value="<?php echo $_POST['token_id']; ?>" />
            <div class="col-md-12">
            <fieldset class="border p-2">
            <legend style="font-size: 14px;" class="w-auto">SELECTED TEST FOR TOKEN <strong><?php echo $token_id; ?></strong></legend>
            <div class="row">
                <div class="col-md-12">
                    <table class = "table table-hover">
                        <caption style = "caption-side: top;color: black;">
                            <table class = "table">
                                <tr>
                                    <td>Name</td>
                                    <th><u><?php echo $name; ?></u> / <u><?php echo $age; ?></u></th>
                                    <td>Gender</td>
                                    <th><u><?php echo $gender; ?></u></th>
                                    <td>Phone</td>
                                    <th><u><?php echo $phone; ?></u></th>
                                    <td>Token Date & Time</td>
                                    <th colspan = "2"><u><?php echo $token_time; ?></u> <u><?php echo $token_date; ?></u></th>
                                </tr>
                                <tr>
                                    <td>Doctor</td>
                                    <th><u><?php echo $docotr_name; ?></u> / <u><?php echo $token_branch_tag_name; ?></u></th>
                                    <td>Cash</td>
                                    <th><u><?php echo $cash; ?></u></th>
                                    <td>Sample By</td>
                                    <th><u><?php echo $token_by; ?></u></th>
                                    <td>Reporting Time</td>
                                    <td colspan = "2">
                                        <input type = "datetime-local" name = "reporting_date_time" required class = "form-control" />
                                    </td>
                                </tr>
                                <tr>

                                </tr>
                            </table>
                        </caption>
                        <thead>
                            <tr>
                                <th>S #</th>
                                <th>Test Name</th>
                                <th>Normal Values</th>
                                <th>Test Result</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $run = mysqli_query($con, "SELECT * FROM `lab_tests` INNER JOIN items ON lab_tests.item_id = items.id WHERE token_no = '$token_id' ");
                        if (mysqli_num_rows($run) > 0) 
                        {
                            while ($row = mysqli_fetch_array($run)) 
                            {
                                $ser = $ser + 1;
                                echo '<tr>
                                        <td>'.$ser.'</td><td>' .$row['name'] . '</td>
                                        <td><textarea rows = "1" class = "form-control" name = "test_normal_value">'.$row['test_normal_value'].'</textarea></td>
                                        <td><textarea rows = "1" class = "form-control" name = "test_result">'.$row['test_result'].'</textarea></td>
                                    </tr>';
                            }    
                        }  
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            </fieldset>
            </div>
            </div>
        </form>
<?php } ?>
	</div>
</div>

</body>
</html>