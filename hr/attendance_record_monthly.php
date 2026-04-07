<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
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
    $br_id = $hr_branch_id;
}
?>
	<title>ATTENDANCE <?php echo get_branch_name_by($br_id); ?> FEB-2025 ORDER BY STAFF - <?php echo $company_trademark; ?></title>
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
    	    <div class = "col noprint"><a class = "btn btn-info" href = "dashboard.php">Dashboard</a></div>
    	    <div class = "col">YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</div>
	    </div>
	</div>
	<div class="col-md-12">
	    <table class = "table table-bordered table-hover">
	        <caption style = "color: black;caption-side: top;text-align: center;">
	            <h2><?php echo get_branch_name_by($br_id); ?></h2>
	        </caption>
	        <thead>
	            <tr class = "noprint">
	                <td></td>
	                <td></td>
	                <td>
	                    <form>
	                        <input type = "hidden" name = "br_id" value = "<?php echo $br_id; ?>" />
	                        <input onchange = "this.form.submit()" type = "month" name = "attendance_record_month" value = "<?php echo (isset($_GET['attendance_record_month']) == '') ? date('Y-m') : $_GET['attendance_record_month']; ?>" class = "form-control" />
	                    </form>
	                </td>
	                <td>
	                    <form>
	                        <input type = "hidden" name = "attendance_record_month" value = "<?php echo (isset($_GET['attendance_record_month']) == '') ? date('Y-m') : $_GET['attendance_record_month']; ?>" />
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
	                <td></td>
	                <td></td>
	                <td></td>
	                <td></td>
	            </tr>
	            <tr>
	                <th>SR</th>
	                <th>DATE</th>
	                <th>MONTH</th>
	                <th>EMPLOYEE</th>
	                <th>DESIGNATION</th>
	                <th>STATUS</th>
	                <th>START</th>
	                <th>END</th>
	                <th>BIO-START</th>
	                <th>BIO-END</th>
	                <th>REMARKS</th>
	            </tr>
	        </thead>
	        <tbody>
	        <?php
	        $s = 0;
	        if(isset($_GET['attendance_record_month']) && $_GET['attendance_record_month'] != '')
	        {
    	        $month = $_GET['attendance_record_month'];
	        }
	        else
	        {
    	        $month = date('Y-m');
	        }
	        $attendance = "SELECT attendance_records.attendance_record_date,attendance_records.attendance_record_remarks, attendance_record_bio_end_time, attendance_record_bio_start_time,staff.staff_name, designations.designation_title, attendance_records.attendance_record_month, CASE WHEN attendance_records.attendance_record_title = '1' THEN 'DUTY' WHEN attendance_records.attendance_record_title = '2' THEN 'LEAVE' WHEN attendance_records.attendance_record_title = '3' THEN 'ABSENT' WHEN attendance_records.attendance_record_title = '4' THEN 'DOUBLE' ELSE 'NO DATA' END AS ATT_STATUS, attendance_records.attendance_record_start_time, attendance_records.attendance_record_end_time FROM `attendance_records` INNER JOIN staff ON attendance_records.employee_id = staff.staff_id INNER JOIN designations ON staff.designation_id = designations.designation_id WHERE attendance_records.branch_id = '$br_id' AND `attendance_record_month` = '$month' ORDER BY `staff`.`staff_name` ASC ";
	        $run_attendance = mysqli_query($con, $attendance);
	        if(mysqli_num_rows($run_attendance) > 0)
	        {
	            while($row_attendance = mysqli_fetch_array($run_attendance))
	            {
	                $s++;
	            ?>
	           <tr>
	               <td><?php echo $s; ?></td>
	               <td><?php echo $row_attendance['attendance_record_date']; ?></td>
	               <td><?php echo $row_attendance['attendance_record_month']; ?></td>
	               <td><?php echo $row_attendance['staff_name']; ?></td>
	               <td><?php echo $row_attendance['designation_title']; ?></td>
	               <td><?php echo $row_attendance['ATT_STATUS']; ?></td>
	               <td><?php echo $row_attendance['attendance_record_start_time']; ?></td>
	               <td><?php echo $row_attendance['attendance_record_end_time']; ?></td>
	               <td><?php echo $row_attendance['attendance_record_bio_start_time']; ?></td>
	               <td><?php echo $row_attendance['attendance_record_bio_end_time']; ?></td>
	               <td><?php echo $row_attendance['attendance_record_remarks']; ?></td>
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
<script type="text/javascript">
      $(document).ready(function () {
  $('#select_item').selectize({
      sortField: 'text'
  });
  $(".alert").alert();
});
</script>
</body>
</html>
<?php mysqli_close($con); ?>