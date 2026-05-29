<?php 
include 'includes/connect.php';

if(isset($_GET['save_staff_salary_details']) && $_GET['save_staff_salary_details'] != '')
{
    //OTHER DATA
    $staff_id = $_GET['staff_id'];
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
    (`staff_salary_id`, `staff_id`, `payroll_month`, `staff_salary`, `staff_extra_days`, `reward_on_progess`, `rewards`, `rashan_allowance`, `petrol`, `mobile_load`, `previous_arrears`, `other_allownaces`, `absence`, `less_hours`, `advance`, `pending_medicines`, `kitchen_expense`, `fine`, `health`, `rashan_deduction`, `other_deductions`, `staff_salary_created_by`, `staff_salary_created_at`, `staff_salary_status`) 
    VALUES
    (NULL, '$staff_id', '$payroll_month', '$staff_salary', '$staff_extra_days', '$reward_on_progess', '$rewards', '$rashan_allowance', '$petrol', '$mobile_load', '$previous_arrears', '$other_allownaces', '$absence', '$less_hours', '$advance', '$pending_medicines', '$kitchen_expense', '$fine', '$health', '$rashan_deduction', '$other_deductions', '$fr_id', '$current_date', '1')";
    if(mysqli_query($con, $insert))
    {
        header('Location: generate_salary_slip.php?msg=success');
        exit;
    }
    exit;
}

include 'includes/head.php';
    
if(isset($_GET['generate_salary_payroll_month']))
{
    if($_GET['payroll_month'] != '')
    {
        $payroll_month = $_GET['payroll_month'];
    }
    else
    {
        $payroll_month = date('Y-m');
    }
}
    
if(isset($_GET['br_id']) && $_GET['br_id'] != '')
{
    $br_id = $_GET['br_id'];   
}
else
{
    $br_id = $branch_id;    
}
?>
	<title>Generate Salary Staff - <?php echo $company_trademark; ?></title>
<style>
@media print{    
    .print_size
    {
        background-color: #FFFFFF;
        font-size: 0.5em;
    }
}    
</style>
</head>

<body class="background_image_ycdo print_size">
<div>
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
	    <div class = "row">
    	    <div class = "col"><a class = "btn btn-info" href = "dashboard.php">Dashboard</a></div>
    	    <div class = "col">YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</div>
	    </div>
	</div>
	<div class="" style="margin: 10px 15px;">
		<div class="row">
			<div class="col-md-12" style="text-align: center;">
			    <table class = "table table-bordered table-sm">
			        <thead>
    			        <tr class  = "d-print-none">
    			            <th colspan = "6"></th>
    			            <th colspan = "3">
    		                    <form>
                                    <select onchange="this.form.submit();" name = "br_id" class = "form-control" required>
                                        <?php
                                        if(!isset($_GET['br_id']))
                                        {
                                            echo '<option value = "'.$branch_id.'">'.$branch_address.'</option>';
                                        }
                            			    $query = "SELECT * FROM `branchs` WHERE `status` = '1' ";
                            			    $run = mysqli_query($con, $query);
                            			    if(mysqli_num_rows($run) > 0)
                            			    {
                    			                echo '<option value = "-1">ALL STAFF</option>';
                    			                echo '<option value = "0">ORGANIZATION STAFF</option>';
                            			        while($row = mysqli_fetch_array($run))
                            			        {
                            			            $id = $row['id'];
                            			            $address = $row['address'];
                            			            $tag_name = $row['tag_name'];
                            			            if($id == $_GET['br_id'])
                            			            {
                            			                echo '<option SELECTED value = "'.$id.'">'.$tag_name.'</option>';
                            			            }
                            			            else
                            			            {
                            			                echo '<option value = "'.$id.'">'.$tag_name.'</option>';
                            			            }
                            			        }
                            			    }
                                        ?>
                                    </select>
                                </form>
    			            </th>
    			            <th>
    			            </th>
    			        </tr>
			            <tr>
			                <th>S #</th>
			                <th>ID</th>
			                <th>NAME</th>
			                <th>S/O, D/O, W/O</th>
			                <th>PHONE</th>
			                <th>DESIGNATION</th>
			                <th>
                                <label>BRANCH</label>
			                </th>
			                <th>IN TIME</th>
			                <th>OUT TIME</th>
			                <th>DUTY HOURS</th>
			            </tr>
			        </thead>
			        <tbody>
			        <?php 
			        $s = 0;
			        if(isset($_GET['br_id']) && $_GET['br_id'] == '-1')
			        {
			            $select = "SELECT * FROM `staff` LEFT JOIN branchs ON staff.branch_id = branchs.id INNER JOIN designations ON staff.designation_id = designations.designation_id INNER JOIN statuses ON staff.staff_status = statuses.staff_status_id WHERE staff_status = '1' ORDER BY staff.branch_id, staff.designation_id ";
			        }
			        elseif(isset($_GET['br_id']) && $_GET['br_id'] != '')
			        {
			            $select = "SELECT * FROM `staff` LEFT JOIN branchs ON staff.branch_id = branchs.id INNER JOIN designations ON staff.designation_id = designations.designation_id INNER JOIN statuses ON staff.staff_status = statuses.staff_status_id WHERE staff_status = '1' AND staff.branch_id = '".$_GET['br_id']."' ORDER BY staff.branch_id, staff.designation_id ";
			        }
			        else
			        {
			            $select = "SELECT * FROM `staff` LEFT JOIN branchs ON staff.branch_id = branchs.id INNER JOIN designations ON staff.designation_id = designations.designation_id INNER JOIN statuses ON staff.staff_status = statuses.staff_status_id WHERE staff_status = '1' AND staff.branch_id = '$branch_id' ORDER BY staff.branch_id, staff.designation_id ";
			        }
			        $run = mysqli_query($con, $select);
			        if(mysqli_num_rows($run) > 0)
			        {
			            while($row = mysqli_fetch_array($run))
			            {
			                $s++;
			                $staff_status = $row['staff_status'];
			                $staff_image_href = $row['staff_image_href'];
			                echo '
			             <tr>
			                <td>'.$s.'</td>
			                <td style = "text-align: left;">'.$row['staff_id'].'</td>
			                <td style = "text-align: left;">'.$row['staff_name'].'</td>
			                <td style = "text-align: left;">'.$row['staff_spouse'].'</td>
			                <td>'.$row['staff_phone'].'</td>
			                <td>'.$row['designation_title'].'</td>
			                <td>'.$row['tag_name'].'</td>
			                <td>'.date_format(date_create($row['staff_time_in']), "h:i:s A").'</td>
			                <td>'.date_format(date_create($row['staff_time_out']), "h:i:s A").'</td>
			                <td>'.$row['staff_duty_hours'].'</td>
			             </tr>'; ?>
			             <tr>
			                 <td colspan = "10" class = "text-left">
			                     <form method = "GET">
    			                 <div class = "row">
    			                     <div class = "col-sm-12">
    			                         <h4 style = "text-align: left;">Pay & Allowances</h4>
    			                     </div>
    			                     <div class = "col">
    			                         <label>Stipend </label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "staff_salary" />
    			                     </div>
    			                         
    			                     <div class = "col">
    			                         <label>Extra Days </label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "staff_extra_days" />
    			                     </div>
    			                         
    			                     <div class = "col">
    			                         <label>Reward On Progess</label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "reward_on_progess" />
    			                     </div>
    			                         
    			                     <div class = "col">
    			                         <label>Rewards</label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "rewards" />
    			                     </div>
    			                         
    			                     <div class = "col">
    			                         <label>Rashan</label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "rashan_allowance" />
    			                     </div>
    			                         
    			                     <div class = "col">
    			                         <label>Petrol</label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "petrol" />
    			                     </div>
    			                         
    			                     <div class = "col">
    			                         <label>Mobile Load</label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "mobile_load" />
    			                     </div>
    			                         
    			                     <div class = "col">
    			                         <label>Previous Arrears</label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "previous_arrears" />
    			                     </div>
    			                         
    			                     <div class = "col">
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
    			                     <div class = "col">
    			                         <label>Less Hours</label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "less_hours" />
			                         </div>
    			                     <div class = "col">
    			                         <label>Advance </label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "advance" />
			                         </div>
    			                     <div class = "col">
    			                         <label>Pending Medicines </label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "pending_medicines" />
			                         </div>
    			                     <div class = "col">
    			                         <label>Kitchen Expense</label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "kitchen_expense" />
			                         </div>
    			                     <div class = "col">
    			                         <label>Fine</label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "fine" />
			                         </div>
    			                     <div class = "col">
    			                         <label>Health</label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "health" />
			                         </div>
    			                     <div class = "col">
    			                         <label>Rashan</label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "rashan_deduction" />
			                         </div>
    			                     <div class = "col">
    			                         <label>Other Deductions</label>
    			                         <input class = "form-control" value = "0" required type = "number" name = "other_deductions" />
			                         </div>
			                     </div>
    			                     <div class = "col-sm-12 text-right d-print-none">
    			                         <div style = "min-width:100%">
        			                         <input type = "hidden" name = "staff_id" value = "<?php echo $row['staff_id']; ?>" />
        			                         <input type = "hidden" name = "payroll_month" value = "<?php echo $_GET['payroll_month']; ?>" />
        			                         <input type = "submit" name = "save_staff_salary_details" value = "SAVE SALARY DETAILS" class = "btn btn-primary btn-sm" />
    			                         </div>
    			                     </div>
    			                 </div>
    			                 </form>
			                 </td>
			             </tr>
			            <?php }
			        }
			        else
			        {
			            echo '<tr><td colspan = "9">NO RECORDS FOUND</td></tr>';
			        }
			        ?>
			        </tbody>
			    </table>
			</div>
		</div>

	</div>
</div>


</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<?php mysqli_close($con); ?>