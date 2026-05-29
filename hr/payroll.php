<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 

$payroll_month = date('Y-m');
$day = date('d');
$br_id = $hr_branch_id;
if(!isset($_SESSION['hr_id']))
{
    header('location: logout.php');
}

if(isset($_GET['save_staff_salary_details']) && $_GET['save_staff_salary_details'] != '')
{
    echo '<pre>';
    print_r($_GET);
    echo '</pre>';
    //OTHER DATA
    $staff_id = $_GET['staff_id'];
    $branch_id = $_GET['branch_id'];
    $payroll_month = $_GET['payroll_month'];
    
    // ALLOWANCES
    $staff_salary = $_GET['staff_salary'];
    $staff_extra_days = $_GET['staff_extra_days'];
    $reward_on_progess = $_GET['reward_on_progess'];
    $rewards = $_GET['rewards'];
    $rashan_allowance = $_GET['rashan_allowance'];
    $petrol = $_GET['petrol'];
    $mobile_load = $_GET['mobile_load'];
    $previous_arrears = $_GET['previous_arrears'];
    $other_allownaces = $_GET['other_allownaces'];
    
    // DEDUCTIONS
    $absence = $_GET['absence'];
    $less_hours = $_GET['less_hours'];
    $advance = $_GET['advance'];
    $pending_medicines = $_GET['pending_medicines'];
    $kitchen_expense = $_GET['kitchen_expense'];
    $fine = $_GET['fine'];
    $health = $_GET['health'];
    $rashan_deduction = $_GET['rashan_deduction'];
    $other_deductions = $_GET['other_deductions'];
    
    $insert = "INSERT INTO `staff_salaries`
    (`staff_salary_id`, `staff_id`, `payroll_month`, `staff_salary`, `staff_extra_days`, `reward_on_progess`, `rewards`, `rashan_allowance`, `petrol`, `mobile_load`, `previous_arrears`, `other_allownaces`, `absence`, `less_hours`, `advance`, `pending_medicines`, `kitchen_expense`, `fine`, `health`, `rashan_deduction`, `other_deductions`, `staff_salary_created_by`, `staff_salary_created_at`, `staff_salary_status`, `branch_id`) 
    VALUES
    (NULL, '$staff_id', '$payroll_month', '$staff_salary', '$staff_extra_days', '$reward_on_progess', '$rewards', '$rashan_allowance', '$petrol', '$mobile_load', '$previous_arrears', '$other_allownaces', '$absence', '$less_hours', '$advance', '$pending_medicines', '$kitchen_expense', '$fine', '$health', '$rashan_deduction', '$other_deductions', '$fr_id', '$current_date', '1', '$branch_id')";    
    if(mysqli_query($con, $insert))
    {
        header('location: payroll.php?msg=success&payroll_month='.$payroll_month.'&br_id='.$br_id);
    }
    else
    {
        echo $con-error;
    }
}

if(isset($_GET['attendance_record_bio_start_time']) && $_GET['attendance_record_bio_start_time'] != '')
{
    $payroll_month = $_GET['payroll_month'];
    $br_id = $_GET['br_id'];
    $staff_id = $_GET['staff_id'];
    $attendance_record_id = $_GET['attendance_record_id'];
    $attendance_record_bio_start_time = $_GET['attendance_record_bio_start_time'];
    $update = "UPDATE `attendance_records` SET `attendance_record_bio_start_time` = '$attendance_record_bio_start_time' WHERE `attendance_record_id` = '$attendance_record_id' ";
    if(mysqli_query($con, $update))
    {
        header('location: payroll.php?br_id='.$br_id.'&payroll_month='.$payroll_month.'&staff_id='.$staff_id.'&attendance_record_id='.$attendance_record_id);
    }
    
}
if(isset($_GET['attendance_record_bio_end_time']) && $_GET['attendance_record_bio_end_time'] != '')
{
    $payroll_month = $_GET['payroll_month'];
    $br_id = $_GET['br_id'];
    $staff_id = $_GET['staff_id'];
    $attendance_record_id = $_GET['attendance_record_id'];
    $attendance_record_bio_end_time = $_GET['attendance_record_bio_end_time'];
    $update = "UPDATE `attendance_records` SET `attendance_record_bio_end_time` = '$attendance_record_bio_end_time' WHERE `attendance_record_id` = '$attendance_record_id' ";
    if(mysqli_query($con, $update))
    {
        header('location: payroll.php?br_id='.$br_id.'&payroll_month='.$payroll_month.'&staff_id='.$staff_id.'&attendance_record_id='.$attendance_record_id);
    }
    
}

if(isset($_GET['br_id']) && $_GET['br_id'] != '')
{
    $br_id = $_GET['br_id'];
}

if(isset($_GET['payroll_month']) && $_GET['payroll_month'] != '')
{
    $payroll_month = $_GET['payroll_month'];
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
<script src="js/jquery.min.js"></script>
<script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
</head>

<body class="">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
	    <div class = "row">
    	    <div class = "col"><a class = "btn btn-info" href = "dashboard.php">Dashboard</a></div>
    	    <div class = "col">YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</div>
	    </div>
	</div>
	<div class="col-md-12 ">
	    <div class = "row">
	        <div class = "col">
        	    <form>
	            <input type = "hidden" name = "br_id" value = "<?php echo $br_id; ?>" />
	            <label>MONTH</label>
	            <input required onchange="this.form.submit();" value = "<?php echo $payroll_month; ?>" class = "form-control" type = "month" name = "payroll_month" />
        	    </form>
	        </div>
	        <div class = "col">
        	    <form>
	            <label>BRANCH</label>
	            <input type = "hidden" name = "payroll_month" value = "<?php echo $payroll_month; ?>" />
                <select required onchange="this.form.submit();" name = "br_id" id = "br_id" class = "form-control">
                    <option value = "0">ORGANIZATION</option>
                    <?php
                    $user = "SELECT * FROM `branchs` WHERE `branchs`.`status` = '1' ";
                    $run_user = mysqli_query($con, $user);
                    if(mysqli_num_rows($run_user) > 0)
                    {
                        while($row_user = mysqli_fetch_array($run_user))
                        {
                            $select_branch_id = $row_user['id'];
                            $branch_tag = $row_user['tag_name'];
                            if($select_branch_id != $br_id)
                            {
                                echo '<option value = "'.$select_branch_id.'">'.$branch_tag.'</option>';
                            }
                            else
                            {
                                echo '<option SELECTED value = "'.$select_branch_id.'">'.$branch_tag.'</option>';
                            }
                        }
                    }
                    else
                    {
                        echo '<option value = "">NO DATA FOUND<option>';
                    }
                    ?>
                </select>
	            </form>
	        </div>
	        <div class = "col">
        	    <form>
	            <label>SELECT STAFF</label>
	            <input type = "hidden" name = "payroll_month" value = "<?php echo $payroll_month; ?>" />
	            <input type = "hidden" name = "br_id" value = "<?php echo $br_id; ?>" />
                <select required onchange="this.form.submit();" name = "staff_id" id = "staff_id" class = "form-control">
                    <option>SELECT EMPLOYEE</option>
                    <?php
                    $staff = "SELECT * FROM `staff` INNER JOIN designations ON staff.designation_id = designations.designation_id LEFT JOIN branchs ON staff.branch_id = branchs.id WHERE `branch_id` = '$br_id' AND `staff_status` = '1' ";
                    $run_staff = mysqli_query($con, $staff);
                    if(mysqli_num_rows($run_staff) > 0)
                    {
                        while($row_staff = mysqli_fetch_array($run_staff))
                        {
                            if($_GET['staff_id'] == $row_staff['staff_id'])
                            {
                                echo '<option SELECTED value = "'.$row_staff['staff_id'].'">'.$row_staff['staff_name'].'</option>';
                            }
                            else
                            {
                                echo '<option value = "'.$row_staff['staff_id'].'">'.$row_staff['staff_name'].'</option>';
                            }
                        }
                    }
                    else
                    {
                        echo '<option value = "">NO DATA FOUND<option>';
                    }
                    ?>
                </select>
                </form>
	        </div>
	    </div>
<?php
if(isset($_GET['staff_id']) && $_GET['staff_id'] != '')
{
    $staff_id = $_GET['staff_id'];
?>
	    <div class = "col-md-12">
        <table class = "table">
	        <thead>
	            <tr>
	                <th>S#</th>
	                <th>DATE</th>
	                <th>DUTY HOUR</th>
	                <th>IN-TIME</th>
	                <th>OUT-TIME</th>
	                <th>EXTRA-TIME</th>
	                <th>SHORT-TIME</th>
	                <th>REMARKS</th>
	            </tr>
	        </thead>
	        <tbody>
            <?php 
                $staff = "SELECT * FROM `staff` INNER JOIN designations ON staff.designation_id = designations.designation_id LEFT JOIN branchs ON staff.branch_id = branchs.id WHERE `branch_id` = '$br_id' AND `staff_id` = '$staff_id' ";
                $run_staff = mysqli_query($con, $staff);
                if(mysqli_num_rows($run_staff) == 1)
                {
                    while($row_staff = mysqli_fetch_array($run_staff))
                    {
                    }
                } 
                $s = 0;
                $total_extra = 0;
                $total_short = 0;
                $staff_attendence = "SELECT * FROM `attendance_records` WHERE `attendance_record_month` = '$payroll_month' AND `employee_id` = '$staff_id' ORDER BY `attendance_records`.`attendance_record_date` ASC ";
                $run_staff_attendence = mysqli_query($con, $staff_attendence);
                if(mysqli_num_rows($run_staff_attendence) > 0)
                {
                    while($row_staff_attendence = mysqli_fetch_array($run_staff_attendence))
                    {
                        $s++;
                        $attendance_record_id = $row_staff_attendence['attendance_record_id'];
                        if($row_staff_attendence['attendance_record_date'] < 10)
                        {
                            $row_staff_attendence['attendance_record_date'] = '0'.$row_staff_attendence['attendance_record_date'];
                        }
                        $bia_start_time = strtotime($payroll_month.'-'.$row_staff_attendence['attendance_record_date'].' '.$row_staff_attendence['attendance_record_bio_start_time']);
                        $bia_end_time = strtotime($payroll_month.'-'.$row_staff_attendence['attendance_record_date'].' '.$row_staff_attendence['attendance_record_bio_end_time']);
                        $minutes = round(abs($bia_start_time - $bia_end_time) / 60 ?? 0,2);
            
                        $staff_duty_in = strtotime($payroll_month.'-'.$row_staff_attendence['attendance_record_date'].' '.$row_staff_attendence['staff_duty_in']);
                        $staff_duty_out = strtotime($payroll_month.'-'.$row_staff_attendence['attendance_record_date'].' '.$row_staff_attendence['staff_duty_out']);
                        $hours2 = round(abs($staff_duty_in - $staff_duty_out) / 3600 ?? 0,2);
                        
                        $minutes2 = round(abs($staff_duty_in - $staff_duty_out) / 60 ?? 0,2);

                        echo '<tr>';
                            echo '<td>'.$s.'</td>';
                            echo '<td>'.$row_staff_attendence['attendance_record_date'].'</td>';
                            echo '<td>'.$hours2.'</td>';
                            echo '<td>'.$row_staff_attendence['attendance_record_bio_start_time'].'</br>'; ?>
                           <form method = "GET">
                	            <input type = "hidden" name = "staff_id" value = "<?php echo $staff_id; ?>" />
                	            <input type = "hidden" name = "payroll_month" value = "<?php echo $payroll_month; ?>" />
                	            <input type = "hidden" name = "br_id" value = "<?php echo $br_id; ?>" />
                                <input type = "hidden" name = "attendance_record_id" value = "<?php echo $attendance_record_id; ?>" />
                                <input type = "time" name = "attendance_record_bio_start_time" value = "<?php echo $row_staff_attendence['attendance_record_bio_start_time']; ?>" />
                                <input type = "submit" value = "+" class = "btn-sm btn-success" />
                           </form> <?php echo '</td>';
    	                   echo '<td>'.$row_staff_attendence['attendance_record_bio_end_time'].'</br>'; ?>
    	                   <form method = "GET">
                	            <input type = "hidden" name = "staff_id" value = "<?php echo $staff_id; ?>" />
                	            <input type = "hidden" name = "payroll_month" value = "<?php echo $payroll_month; ?>" />
                	            <input type = "hidden" name = "br_id" value = "<?php echo $br_id; ?>" />
    	                        <input type = "hidden" name = "attendance_record_id" value = "<?php echo $attendance_record_id; ?>" />
    	                        <input type = "time" name = "attendance_record_bio_end_time" value = "<?php echo $row_staff_attendence['attendance_record_bio_end_time']; ?>" />
    	                        <input type = "submit" value = "+" class = "btn-sm btn-success" />
    	                   </form> <?php echo '</td>';
                            if($row_staff_attendence['attendance_record_bio_start_time'] != '00:00:00' && $row_staff_attendence['attendance_record_bio_end_time'] != '00:00:00')
                            {
                                if($minutes2 < $minutes)
                                {
                                    $total_extra  = $total_extra + ($minutes-$minutes2);
                                    echo '<td>'.$minutes-$minutes2.'</td><td></td>'; 
                                }
                                elseif($minutes2 > $minutes)
                                {
                                    $total_short  = $total_short + ($minutes2-$minutes);
                                    echo '<td></td><td>'.$minutes2-$minutes.'</td>'; 
                                }
                                else
                                {
                                    echo '<td></td><td></td>';
                                }
                            }
                            else
                            {
                                echo '<td></td><td></td><td></td>';
                            }
                            echo '<td>'.$row_staff_attendence['attendance_record_remarks'].'</td>';
                        echo '</tr>';
                    }
                }
            ?>
            </tbody>
            <tfoot>
                <th>GRAND TOTAL</th>
                <th><?php echo $total_extra; ?></th>
                <th><?php echo $total_short; ?></th>
            </tfoot>
<?php
$staff_data = "SELECT * FROM `staff` INNER JOIN designations ON staff.designation_id = designations.designation_id LEFT JOIN branchs ON staff.branch_id = branchs.id WHERE `staff_id` = '$staff_id' ";
$run_staff_data = mysqli_query($con, $staff_data);
if(mysqli_num_rows($run_staff_data) > 0)
{
    while($row_staff_data = mysqli_fetch_array($run_staff_data))
    { ?>
    <caption style = "caption-side: top; color: black;">
        <form>
        <div>
            <table class = "table table-bordered table-hover">
                <tr>
                    <td>ID</td>
                    <th><?php echo $row_staff_data['staff_id']; ?></th>
                    <td>FIRST NAME</td>
                    <th><?php echo $row_staff_data['staff_name']; ?></th>
                    <td>LAST NAME</td>
                    <th><?php echo $row_staff_data['staff_spouse']; ?></th>
                </tr>
                <tr>
                    <td>PHONE</td>
                    <th><?php echo $row_staff_data['staff_phone']; ?></th>
                    <td>ADDRESS</td>
                    <th><?php echo $row_staff_data['staff_address']; ?></th>
                    <td>SALARY</td>
                    <th><?php echo $row_staff_data['staff_bacis_salary']; ?></th>
                </tr>
                <tr>
                    <td>DESIGNATION</td>
                    <th><?php echo $row_staff_data['designation_title']; ?></th>
                    <td>BRANCH</td>
                    <th><?php echo $row_staff_data['tag_name']; ?></th>
                    <td>ALOWED LEAVES</td>
                    <th><?php echo $row_staff_data['staff_allowed_leaves']; ?></th>
                </tr>
                <tr>
                    <td>SALARY MONTH</td>
                    <th><?php echo $payroll_month; ?></th>
                    <td>TOTAL DAYS</td>
                    <th><?php echo $s; ?></th>
                    <td>STIPENDS</td>
                    <th><?php echo ($row_staff_data['staff_bacis_salary']/30)*$s; ?></th>
                </tr>
            </table>
             <div class = "row">
                 <div class = "col-sm-12">
                     <h4 style = "text-align: left;">Pay & Allowances</h4>
                 </div>
                 <div class = "col-sm-2">
                     <label>Stipend </label>
                     <input class = "form-control" value = "<?php echo $row_staff_data['staff_bacis_salary']; ?>" required type = "number" name = "staff_salary" />
                 </div>
                     
                 <div class = "col-sm-2">
                     <label>Extra Days </label>
                     <input class = "form-control" value = "0" required type = "number" name = "staff_extra_days" />
                 </div>
                     
                 <div class = "col-sm-2">
                     <label>Reward On Progess</label>
                     <input class = "form-control" value = "0" required type = "number" name = "reward_on_progess" />
                 </div>
                     
                 <div class = "col-sm-2">
                     <label>Rewards</label>
                     <input class = "form-control" value = "0" required type = "number" name = "rewards" />
                 </div>
                     
                 <div class = "col-sm-2">
                     <label>Rashan</label>
                     <input class = "form-control" value = "0" required type = "number" name = "rashan_allowance" />
                 </div>
                     
                 <div class = "col-sm-2">
                     <label>Petrol</label>
                     <input class = "form-control" value = "0" required type = "number" name = "petrol" />
                 </div>
                     
                 <div class = "col-sm-2">
                     <label>Mobile Load</label>
                     <input class = "form-control" value = "0" required type = "number" name = "mobile_load" />
                 </div>
                     
                 <div class = "col-sm-2">
                     <label>Previous Arrears</label>
                     <input class = "form-control" value = "0" required type = "number" name = "previous_arrears" />
                 </div>
                     
                 <div class = "col-sm-8">
                     <label>Other Allowances</label>
                     <input class = "form-control" value = "0" required type = "number" name = "other_allownaces" />
                 </div>
            </div>
            <div class = "row">
                <div class = "col-sm-12">
                    <h4 style = "text-align: left;">Deduction</h4>
                </div>
                <div class = "col">
                    <label>Absence</label>
                    <input class = "form-control" value = "0" required type = "number" name = "absence" />
                </div>
                <div class = "col-sm-2">
                    <label>Less Hours</label>
                    <input class = "form-control" value = "0" required type = "number" name = "less_hours" />
                </div>
                <div class = "col-sm-2">
                    <label>Advance </label>
                    <input class = "form-control" value = "0" required type = "number" name = "advance" />
                </div>
                <div class = "col-sm-2">
                    <label>Pending Medicines </label>
                    <input class = "form-control" value = "0" required type = "number" name = "pending_medicines" />
                </div>
                <div class = "col-sm-2">
                    <label>Kitchen Expense</label>
                    <input class = "form-control" value = "0" required type = "number" name = "kitchen_expense" />
                </div>
                <div class = "col-sm-2">
                    <label>Fine</label>
                    <input class = "form-control" value = "0" required type = "number" name = "fine" />
                </div>
                <div class = "col-sm-2">
                    <label>Health</label>
                    <input class = "form-control" value = "100" required type = "number" name = "health" />
                </div>
                <div class = "col-sm-2">
                    <label>Rashan</label>
                    <input class = "form-control" value = "0" required type = "number" name = "rashan_deduction" />
                </div>
                <div class = "col-sm-8">
                    <label>Other Deductions</label>
                    <input class = "form-control" value = "0" required type = "number" name = "other_deductions" />
                </div>
            </div>
            <div>
                <div class = "col-sm-12 text-right d-print-none">
                    <div style = "min-width:100%">
                        <input type = "hidden" name = "staff_id" value = "<?php echo $row_staff_data['staff_id']; ?>" />
                        <input type = "hidden" name = "payroll_month" value = "<?php echo $payroll_month; ?>" />
                        <input type = "hidden" name = "br_id" value = "<?php echo $br_id; ?>" />
                        <input type = "submit" name = "save_staff_salary_details" value = "SAVE SALARY DETAILS" class = "btn btn-primary btn-sm" />
                    </div>
                </div>
            </div>
        </div>
        </form>
    </caption>
    <?php }
} ?>
        </table>
	    </div>
<?php }
else
{ ?>
    <div class = "row">
        <div class = "col-md-12">
            <h2 align = "center">ALL STAFF SALARY REPORT</h2>
        </div>
        <?php
        $select = "SELECT * FROM `staff_salaries` WHERE `payroll_month` = '$payroll_month' ";
        $run = mysqli_query($con, $select);
        if(mysqli_num_rows($run) > 0)
        {
            while($row = mysqli_fetch_array($run))
            {
                // ALLOWANCES
                $staff_salary = $row['staff_salary'];
                $staff_extra_days = $row['staff_extra_days'];
                $reward_on_progess = $row['reward_on_progess'];
                $rewards = $row['rewards'];
                $rashan_allowance = $row['rashan_allowance'];
                $petrol = $row['petrol'];
                $mobile_load = $row['mobile_load'];
                $previous_arrears = $row['previous_arrears'];
                $other_allownaces = $row['other_allownaces'];
                
                // DEDUCTIONS
                $absence = $row['absence'];
                $less_hours = $row['less_hours'];
                $advance = $row['advance'];
                $pending_medicines = $row['pending_medicines'];
                $kitchen_expense = $row['kitchen_expense'];
                $fine = $row['fine'];
                $health = $row['health'];
                $rashan_deduction = $row['rashan_deduction'];
                $other_deductions = $row['other_deductions'];
                
                $staff_id = $row['staff_id'];
                $staff_data = "SELECT * FROM `staff` INNER JOIN designations ON staff.designation_id = designations.designation_id LEFT JOIN branchs ON staff.branch_id = branchs.id WHERE `staff_id` = '$staff_id' ";
                $run_staff_data = mysqli_query($con, $staff_data);
                if(mysqli_num_rows($run_staff_data) > 0)
                {
                    while($row_staff_data = mysqli_fetch_array($run_staff_data))
                    {
                        $duty_hours = $row_staff_data['staff_duty_hours'];
                        $total_allownaces = intval($other_allownaces+$previous_arrears+$mobile_load+$petrol+$rashan_allowance+$rewards+$reward_on_progess+$staff_salary)+(intval($staff_salary/30)*$staff_extra_days);
                        $total_deductions = intval($other_deductions+$rashan_deduction+$health+$fine+$kitchen_expense+$pending_medicines+$advance)+(intval($staff_salary/30)*$absence)+(intval(($staff_salary/30)/$duty_hours)*$less_hours);

                    ?>
                <table class = "table table-bordered table-hover">
                    <tr>
                        <td>FIRST NAME</td>
                        <th><?php echo $row_staff_data['staff_id']; ?> - <?php echo $row_staff_data['staff_name']; ?></th>
                        <td>LAST NAME</td>
                        <th><?php echo $row_staff_data['staff_spouse']; ?></th>
                        <td>DESIGNATION</td>
                        <th><?php echo $row_staff_data['designation_title']; ?></th>
                    </tr>
                    <tr>
                        <td>SALARY MONTH</td>
                        <th><?php echo $payroll_month; ?></th>
                        <td>BRANCH</td>
                        <th><?php echo $row_staff_data['tag_name']; ?></th>
                        <td>SALARY</td>
                        <th><?php echo $row_staff_data['staff_bacis_salary']; ?></th>
                    </tr>
                    <tr>
                        <td>TOTAL DAYS</td>
                        <th><?php echo $s; ?></th>
                        <td>STIPENDS</td>
                        <th><?php echo ($row_staff_data['staff_bacis_salary']/30)*$s; ?></th>
                        <td>ALOWED LEAVES</td>
                        <th><?php echo $row_staff_data['staff_allowed_leaves']; ?></th>
                    </tr>
                    <tr>
                        <th colspan = "2" style = "text-align: center;"><h3>PAY & ALLOWANCES</h3>
                            <table class = "table table-sm table-boredred">
                                <tr>
                                    <td>STIPENDS</td>
                                    <th><?php echo $staff_salary; ?></th>
                                </tr>
                                <tr>
                                    <td>EXTRA DAYS</td>
                                    <th><?php echo $staff_extra_days.' X '.intval($staff_salary/30); ?></th>
                                </tr>
                                <tr>
                                    <td>REWARD ON PROGRESS</td>
                                    <th><?php echo $reward_on_progess; ?></th>
                                </tr>
                                <tr>
                                    <td>REWARDS</td>
                                    <th><?php echo $rewards; ?></th>
                                </tr>
                                <tr>
                                    <td>RASHAN</td>
                                    <th><?php echo $rashan_allowance; ?></th>
                                </tr>
                                <tr>
                                    <td>PETROL</td>
                                    <th><?php echo $petrol; ?></th>
                                </tr>
                                <tr>
                                    <td>MOBILE LOAD</td>
                                    <th><?php echo $mobile_load; ?></th>
                                </tr>
                                <tr>
                                    <td>PREVIOUS ARREARS</td>
                                    <th><?php echo $previous_arrears; ?></th>
                                </tr>
                                <tr>
                                    <td>OTHER ALLOWNACES</td>
                                    <th><?php echo $other_allownaces; ?></th>
                                </tr>
                                <tr>
                                    <td>TOTAL ALLOWNACES</td>
                                    <th><?php echo $total_allownaces; ?></th>
                                </tr>
                            </table>
                        </th>
                        <th colspan = "2" style = "text-align: center;"><h3>DEDUCTIONS</h3>
                            <table class = "table table-sm table-boredred">
                                <tr>
                                    <td>ABSENCE</td>
                                    <th><?php echo $absence .' X '.intval($staff_salary/30); ?></th>
                                <tr>
                                    <td>LESS HOURS</td>
                                    <th><?php echo $less_hours .' X '. intval(($staff_salary/30)/$duty_hours); ?></th>
                                <tr>
                                    <td>ADVANCE</td>
                                    <th><?php echo $advance; ?></th>
                                <tr>
                                    <td>PENDIND MEDICINES</td>
                                    <th><?php echo $pending_medicines; ?></th>
                                <tr>
                                    <td>KITCHEN EXPENSE</td>
                                    <th><?php echo $kitchen_expense; ?></th>
                                <tr>
                                    <td>FINE</td>
                                    <th><?php echo $fine; ?></th>
                                <tr>
                                    <td>HEALTH</td>
                                    <th><?php echo $health; ?></th>
                                <tr>
                                    <td>RASHAN DEDUCTION</td>
                                    <th><?php echo $rashan_deduction; ?></th>
                                <tr>
                                    <td>OTHER DEDUCTION</td>
                                    <th><?php echo $other_deductions; ?></th>
                                </tr>
                                    <td>TOTAL DEDUCTIONS</td>
                                    <th><?php echo $total_deductions; ?></th>
                                </tr>
                            </table>
                        </th>
                        <th colspan = "2" style = "text-align: center;"><h3>CURRENT DETAILS</h3>
                            <table class = "table table-sm table-boredred">
                                <tr>
                                    <th>TOTAL ALLOWNACES</th>
                                    <th><?php echo $total_allownaces; ?></th>
                                </tr>
                                <tr>
                                    <th>TOTAL DEDUCTIONS</th>
                                    <th><?php echo $total_deductions; ?></th>
                                </tr>
                                <tr>
                                    <th>PAYABLE AMOUNT</th>
                                    <th><?php echo $total_allownaces-$total_deductions; ?></th>
                                </tr>
                            </table>
                        </th>
                    </tr>
                </table>
                <?php
                    }
                }
            }
        }
        ?>
    </div>
<?php } ?>
	</div>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/bootstrap.js"></script>
</body>
</html>
<?php mysqli_close($con); ?>