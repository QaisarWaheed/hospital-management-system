<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
function days_in_month($month_days, $year)
{
    return $month_days == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month_days - 1) % 7 % 2 ? 30 : 31);
}

$br_id = $hr_branch_id;
if(!isset($_SESSION['hr_id']))
{
    header('location: logout.php');
}

if(isset($_GET['br_id']))
{
    $br_id = $_GET['br_id'];
}
else
{
    $br_id = 0;
}

if(isset($_GET['month']) && $_GET['month'] != '')
{
    $month = $_GET['month'];
    $year = substr($month,0,4);
    $month_days = substr($month,5,2);
}
else
{
    $year = date('Y');
    $month = date('Y-m');
    $month_days = date('m');
}
$days_in_month = days_in_month($month_days, $year);
?>
	<title>ATTENDANCE REGISTER <?php echo get_branch_name_by($br_id); ?> FEB-2025 ORDER BY STAFF - <?php echo $company_trademark; ?></title>
<script src="js/jquery.min.js"></script>
<script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
<style>
    @media print {
       .noprint {
          visibility: hidden;
       }
    }
</style>
</head>

<body class="">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
	    <div class = "row">
    	    <div class = "col">
    	        <a class = "btn btn-info noprint" href = "dashboard.php">Dashboard</a>
    	        <!--<a href="attendance_record_monthly_register_excel.php" download="my_file.xlsx">Download File</a>-->
    	    </div>
    	    <div class = "col">YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</div>
	    </div>
	</div>
	<div class="col-md-12 bg-light">
	    <table class = "table table-bordered table-hover">
	        <caption style = "color: black;caption-side: top;text-align: center;">
	            <h2><?php echo get_branch_name_by($br_id); ?></h2>
	        </caption>
	        <thead>
	            <tr>
	                <td colspan = "3">
	                    <form>
	                        <input type = "hidden" name = "br_id" value = "<?php echo $br_id; ?>" />
	                        <input onchange = "this.form.submit()" type = "month" name = "month" value = "<?php echo (isset($_GET['month']) == '') ? date('Y-m') : $_GET['month']; ?>" class = "form-control" />
	                    </form>
	                </td>
	                <td colspan = "4">
	                    <form>
	                        <input type = "hidden" name = "month" value = "<?php echo (isset($_GET['month']) == '') ? date('Y-m') : $_GET['month']; ?>" />
	                        <select name = "br_id" class = "form-control" onchange = "this.form.submit()">
	                            <option value = "0">ORGANIZATION</option>
	                            <?php
	                            $select_branch_data = "SELECT * FROM `branchs` WHERE `status` = '1' ";
	                            $run_branch_data = mysqli_query($con, $select_branch_data);
	                            if(mysqli_num_rows($run_branch_data) > 0)
	                            {
	                                while($row_branch_data = mysqli_fetch_array($run_branch_data))
	                                {
	                                    if($row_branch_data['id'] == $br_id)
	                                    {
	                                        echo '<option SELECTED value = "'.$row_branch_data['id'].'">'.$row_branch_data['address'].' ('.$row_branch_data['tag_name'].')</option>';
	                                    }
	                                    else
	                                    {
	                                        echo '<option value = "'.$row_branch_data['id'].'">'.$row_branch_data['address'].' ('.$row_branch_data['tag_name'].')</option>';
	                                    }
	                                }
	                            }
	                            ?>
	                        </select>
	                    </form>
	                </td>
	            </tr>
	            <tr>
	                <th>SR</th>
	                <th>ID</th>
	                <th>EMPLOYEE</th>
	                <th>DESIGNATION</th>
	                <th>DAYS</th>
	               <?php
                    for ($x = 1; $x <= $days_in_month; $x++) {
                    echo '<th>'.$x.'</th>';
                    }?>
	                <th>DUTY</th>
	                <th>LEAVE</th>
	                <th>ABSENT</th>
	                <th>EXTRA</th>
	            </tr>
	        </thead>
	        <tbody>
	        <?php
	        $s = 0;
	        $attendance = "SELECT distinct staff.staff_id, staff.staff_name, designations.designation_title FROM `attendance_records` INNER JOIN staff ON attendance_records.employee_id = staff.staff_id INNER JOIN designations ON staff.designation_id = designations.designation_id WHERE attendance_records.branch_id = '$br_id' AND `attendance_record_month` = '$month' ORDER BY `staff`.`staff_name` ASC ";
	        $run_attendance = mysqli_query($con, $attendance);
	        if(mysqli_num_rows($run_attendance) > 0)
	        {
	            while($row_attendance = mysqli_fetch_array($run_attendance))
	            {
	                $s++;
                    $p = 0;
                    $l = 0;
                    $a = 0;
                    $d = 0;
	                $staff_id = $row_attendance['staff_id'];
	            ?>
	           <tr>
	               <td><?php echo $s; ?></td>
	               <td><?php echo $row_attendance['staff_id']; ?></td>
	               <td><?php echo $row_attendance['staff_name']; ?></td>
	               <td><?php echo $row_attendance['designation_title']; ?></td>
	               <td><?php echo $days_in_month; ?></td>	               
	               <?php
                    for ($day = 1; $day <= $days_in_month; $day++) 
                    {
                        $select = "SELECT CASE WHEN attendance_records.attendance_record_title = '1' THEN 'P' WHEN attendance_records.attendance_record_title = '2' THEN 'L' WHEN attendance_records.attendance_record_title = '3' THEN 'A' WHEN attendance_records.attendance_record_title = '4' THEN 'D' ELSE ' ' END AS ATT_STATUS FROM `attendance_records` WHERE `employee_id` = '$staff_id' AND `attendance_record_month` = '$month' AND attendance_record_date = '$day' ";
            	        $run = mysqli_query($con, $select);
                        echo '<td>';
                        $loc = 1;
            	        if(mysqli_num_rows($run) > 0)
            	        {
            	            while($row = mysqli_fetch_array($run))
            	            {
                    	            $attendacne_status = $row['0'];
            	                if($loc == 1 || $attendacne_msg != $attendacne_status)
            	                {
                    	            if($attendacne_status == 'P'){$p = $p +1;}
                    	            elseif($attendacne_status == 'L'){$l = $l +1;}
                    	            elseif($attendacne_status == 'A'){$a = $a +1;}
                    	            elseif($attendacne_status == 'D'){$d = $d +1;}
    	                            $loc = 0;
    	                            $attendacne_msg = $attendacne_status;
            	                }
    	                            echo $attendacne_status;
            	            }
            	        }
            	        else
            	        {
	                            echo ' ';
            	        }      
                        echo '</td>';
                    }?>
                    <td><?php echo $p; ?></td>
                    <td><?php echo $l; ?></td>
                    <td><?php echo $a; ?></td>
                    <td><?php echo $d+get_extra_staff_duty($staff_id, $month); ?></td>
	           </tr>
	   <?php    }
	        }
	        else
	        {
	            echo '<tr><th colspan = "8">NO DATA FOUND</th></tr>';
	        }
	        ?>
	        </tbody>
	    </table>
	</div>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/bootstrap.js"></script>
</body>
</html>
<?php mysqli_close($con); ?>