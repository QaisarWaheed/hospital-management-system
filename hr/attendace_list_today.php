<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
$br_id = $hr_branch_id;
if(!isset($_SESSION['hr_id']))
{
    header('location: logout.php');
}
?>
<title>ATTENDANCE REPORT TODAY - <?php echo $company_trademark; ?></title>
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
	<div class="col-md-12">
	    <table class = "table table-bordered table-hover">
	        <caption>
	            
	        </caption>
	        <thead>
	            <tr>
	                <th>SR</th>
	                <th>SHIFT START</th>
	                <th>SHIFT END</th>
	                <th>SHIFT HOURS</th>
	                <th>TOTAL STAFF</th>
	                <th>ACTION</th>
	            </tr>
	        </thead>
	        <tbody>
	        <?php
	        $s = 0;
	        $total_staff = 0;
	        $attendance = "SELECT DISTINCT `staff_time_in`, staff_time_out FROM `staff` WHERE `staff_status` = '1'  ORDER BY `staff`.`staff_time_in` ASC ";
	        $run_attendance = mysqli_query($con, $attendance);
	        if(mysqli_num_rows($run_attendance) > 0)
	        {
	            while($row_attendance = mysqli_fetch_array($run_attendance))
	            {
	                $s++;
	                $staff_in = $row_attendance['0'];
	                $staff_out = $row_attendance['1'];	
	                $shift_hours = abs($staff_in-$staff_out);
	                if($shift_hours == 0)
	                {
	                    $shift_hours = 24;
	                }
	                $attendance_run = mysqli_query($con, "SELECT * FROM `staff` LEFT JOIN branchs ON staff.branch_id = branchs.id LEFT JOIN designations ON staff.designation_id = designations.designation_id WHERE `staff_status` = '1' AND staff_time_in = '$staff_in' AND staff_time_out = '$staff_out' ");
	                $attendance_count = mysqli_num_rows($attendance_run);
	                $total_staff = $total_staff + $attendance_count;
	                ?>
	           <tr>
	               <td><?php echo $s; ?></td>
	               <td><?php echo ($row_attendance['0'] && $row_attendance['0'] != '0000-00-00' && $row_attendance['0'] != '0000-00-00 00:00:00' ? date_format(date_create($row_attendance['0']), "h:i A") : ''; ?></td>
	               <td><?php echo date_format(date_create($row_attendance['1']), "h:i A"); ?></td>
	               <td><?php echo $shift_hours; ?></td>
	               <td><a href = "attendace_list_today.php?start=<?php echo $staff_in; ?>&end=<?php echo $staff_out; ?>"><?php echo $attendance_count; ?></a></td>
	               <td></td>
	           </tr>
	           <?php
	           if(isset($_GET['start']) && $_GET['start'] != '' && isset($_GET['end']) && $_GET['end'] != '' && $_GET['start'] == $staff_in && $_GET['end'] == $staff_out)
	           { ?>
	            <tr>
	                <td colspan = "6">
                        <div class = "row">
                        <div class = "col-md-1 border border-primary h3">SR</div>
                        <div class = "col-md-5 border border-primary h3">NAME</div>
                        <div class = "col-md-2 border border-primary h3">PHONE</div>
                        <div class = "col-md-2 border border-primary h3">DESIGNATION</div>
                        <div class = "col-md-2 border border-primary h3">ATT_STATUS</div>
                        <?php
                        $sr = 0;
                        while($attendance_row = mysqli_fetch_array($attendance_run))
                        {
                           $sr++;
                           $staff_id = $attendance_row['staff_id'];
                           $attendance_record_in_time = '0000-00-00';
                           $attendance_record_out_time = '0000-00-00';
                           $month = date('Y-m');
                           $day = date('d');
            	                $attendance_record_run = mysqli_query($con, "SELECT * FROM `attendance_records` WHERE `employee_id` = '$staff_id' AND `attendance_record_month` = '$month' AND `attendance_record_date` = '$day' ");
            	                $attendance_record_count = mysqli_num_rows($attendance_record_run);
            	                if(mysqli_num_rows($attendance_record_run) == 1)
            	                {
            	                    while($attendance_record_row = mysqli_fetch_array($attendance_record_run))
            	                    {
            	                        $attendance_record_in_time = $attendance_record_row['attendance_record_start_time'];
            	                        $attendance_record_out_time = $attendance_record_row['attendance_record_end_time'];
            	                    }
            	                }
                           echo '
                                <div class = "col-md-1 border border-primary">'.$sr.'</div>
                                <div class = "col-md-5 border border-primary">'.$attendance_row['staff_name'].' ('.$attendance_row['tag_name'].')($row_attendance['1'] && $row_attendance['1'] != '0000-00-00' && $row_attendance['($row_attendance['1'] && $row_attendance['1'] != '0000-00-00' && $row_attendance['1'] != '0000-00-00 00:00:00' ? date_format(date_create($row_attendance['1']), "h:i A") : ''; ?></td>
	               <td><?php echo $shift_hours; ?></td>
	               <td><a href = "attendace_list_today.php?start=<?php echo $staff_in; ?>&end=<?php echo $staff_out; ?>"><?php echo $attendance_count; ?></a></td>
	               <td></td>
	           </tr>
	           <?php
	           if(isset($_GET['start']) && $_GET['start'] != '' && isset($_GET['end']) && $_GET['end'] != '' && $_GET['start'] == $staff_in && $_GET['end'] == $staff_out)
	           { ?>
	            <tr>
	                <td colspan = "6">
                        <div class = "row">
                        <div class = "col-md-1 border border-primary h3">SR</div>
                        <div class = "col-md-5 border border-primary h3">NAME</div>
                        <div class = "col-md-2 border border-primary h3">PHONE</div>
                        <div class = "col-md-2 border border-primary h3">DESIGNATION</div>
                        <div class = "col-md-2 border border-primary h3">ATT_STATUS</div>
                        <?php
                        $sr = 0;
                        while($attendance_row = mysqli_fetch_array($attendance_run))
                        {
                           $sr++;
                           $staff_id = $attendance_row['staff_id'];
                           $attendance_record_in_time = '0000-00-00';
                           $attendance_record_out_time = '0000-00-00';
                           $month = date('Y-m');
                           $day = date('d');
            	                $attendance_record_run = mysqli_query($con, "SELECT * FROM `attendance_records` WHERE `employee_id` = '$staff_id' AND `attendance_record_month` = '$month' AND `attendance_record_date` = '$day' ");
            	                $attendance_record_count = mysqli_num_rows($attendance_record_run);
            	                if(mysqli_num_rows($attendance_record_run) == 1)
            	                {
            	                    while($attendance_record_row = mysqli_fetch_array($attendance_record_run))
            	                    {
            	                        $attendance_record_in_time = $attendance_record_row['attendance_record_start_time'];
            	                        $attendance_record_out_time = $attendance_record_row['attendance_record_end_time'];
            	                    }
            	                }
                           echo '
                                <div class = "col-md-1 border border-primary">'.$sr.'</div>
                                <div class = "col-md-5 border border-primary">'.$attendance_row['staff_name'].' ('.$attendance_row['tag_name'].')($row_attendance['1'] && $row_attendance['1'] != '0000-00-00' && $row_attendance['1'] != '0000-00-00 00:00:00' ? date_format(date_create($row_attendance['1']), "h:i A") : ''; ?></td>
	               <td><?php echo $shift_hours; ?></td>
	               <td><a href = "attendace_list_today.php?start=<?php echo $staff_in; ?>&end=<?php echo $staff_out; ?>"><?php echo $attendance_count; ?></a></td>
	               <td></td>
	           </tr>
	           <?php
	           if(isset($_GET['start']) && $_GET['start'] != '' && isset($_GET['end']) && $_GET['end'] != '' && $_GET['start'] == $staff_in && $_GET['end'] == $staff_out)
	           { ?>
	            <tr>
	                <td colspan = "6">
                        <div class = "row">
                        <div class = "col-md-1 border border-primary h3">SR</div>
                        <div class = "col-md-5 border border-primary h3">NAME</div>
                        <div class = "col-md-2 border border-primary h3">PHONE</div>
                        <div class = "col-md-2 border border-primary h3">DESIGNATION</div>
                        <div class = "col-md-2 border border-primary h3">ATT_STATUS</div>
                        <?php
                        $sr = 0;
                        while($attendance_row = mysqli_fetch_array($attendance_run))
                        {
                           $sr++;
                           $staff_id = $attendance_row['staff_id'];
                           $attendance_record_in_time = '0000-00-00';
                           $attendance_record_out_time = '0000-00-00';
                           $month = date('Y-m');
                           $day = date('d');
            	                $attendance_record_run = mysqli_query($con, "SELECT * FROM `attendance_records` WHERE `employee_id` = '$staff_id' AND `attendance_record_month` = '$month' AND `attendance_record_date` = '$day' ");
            	                $attendance_record_count = mysqli_num_rows($attendance_record_run);
            	                if(mysqli_num_rows($attendance_record_run) == 1)
            	                {
            	                    while($attendance_record_row = mysqli_fetch_array($attendance_record_run))
            	                    {
            	                        $attendance_record_in_time = $attendance_record_row['attendance_record_start_time'];
            	                        $attendance_record_out_time = $attendance_record_row['attendance_record_end_time'];
            	                    }
            	                }
                           echo '
                                <div class = "col-md-1 border border-primary">'.$sr.'</div>
                                <div class = "col-md-5 border border-primary">'.$attendance_row['staff_name'].' ('.$attendance_row['tag_name'].')</div>
                                <div class = "col-md-2 border border-primary">'.$attendance_row['staff_phone'].'</div>
                                <div class = "col-md-2 border border-primary">'.$attendance_row['designati$row_attendance['1']), "h:i A") : ''; ?></td>
	               <td><?php echo $shift_hours; ?></td>
	               <td><a href = "attendace_list_today.php?start=<?php echo $staff_in; ?>&end=<?php echo $staff_out; ?>"><?php echo $attendance_count; ?></a></td>
	               <td></td>
	           </tr>
	           <?php
	           if(isset($_GET['start']) && $_GET['start'] != '' && isset($_GET['end']) && $_GET['end'] != '' && $_GET['start'] == $staff_in && $_GET['end'] == $staff_out)
	           { ?>
	            <tr>
	                <td colspan = "6">
                        <div class = "row">
                        <div class = "col-md-1 border border-primary h3">SR</div>
                        <div class = "col-md-5 border border-primary h3">NAME</div>
                        <div class = "col-md-2 border border-primary h3">PHONE</div>
                        <div class = "col-md-2 border border-primary h3">DESIGNATION</div>
                        <div class = "col-md-2 border border-primary h3">ATT_STATUS</div>
                        <?php
                        $sr = 0;
                        while($attendance_row = mysqli_fetch_array($attendance_run))
                        {
                           $sr++;
                           $staff_id = $attendance_row['staff_id'];
                           $attendance_record_in_time = '0000-00-00';
                           $attendance_record_out_time = '0000-00-00';
                           $month = date('Y-m');
                           $day = date('d');
            	                $attendance_record_run = mysqli_query($con, "SELECT * FROM `attendance_records` WHERE `employee_id` = '$staff_id' AND `attendance_record_month` = '$month' AND `attendance_record_date` = '$day' ");
            	                $attendance_record_count = mysqli_num_rows($attendance_record_run);
            	                if(mysqli_num_rows($attendance_record_run) == 1)
            	                {
            	                    while($attendance_record_row = mysqli_fetch_array($attendance_record_run))
            	                    {
            	                        $attendance_record_in_time = $attendance_record_row['attendance_record_start_time'];
            	                        $attendance_record_out_time = $attendance_record_row['attendance_record_end_time'];
            	                    }
            	                }
                           echo '
                                <div class = "col-md-1 border border-primary">'.$sr.'</div>
                                <div class = "col-md-5 border border-primary">'.$attendance_row['staff_name'].' ('.$attendance_row['tag_name'].')</div>
                                <div class = "col-md-2 border border-primary">'.$attendance_row['staff_phone'].'</div>
                                <div class = "col-md-2 border border-primary">'.$attendance_row['designation_title'].'</div>';
                                if($attendance_record_in_time != '0000-00-00')
                                {
                                    echo '<div class = "col-md-1 border border-primary">'.$attendance_record_in_time.'</div>';
                                }
                                else
                                {
                                    echo '<div class = "col-md-1 border border-primary">NO RECORD</div>';
                                }
                                if($attendance_record_in_time != '0000-00-00')
                                {
                                    echo '<div class = "col-md-1 border border-primary">'.$attendance_record_out_time.'</div>';
                                }
                                else
                                {
                                    echo '<div class = "col-md-1 border border-primary">NO RECORD</div>';
                                }
                        } ?>
    	               </div>
	               </td>
	            </tr>
	           <?php }
	           ?>
	   <?php    }
	        }
	        ?>
	        </tbody>
	        <caption style = "caption-side: top; color: black;text-align: center;">
	            <h2><?php echo $br_address; ?></h2>
	            <h3>ATTENDANCE REPORT DATED: <?php echo $report_date; ?></h3>
	        </caption>
	    </table>
	</div>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php mysqli_close($con); ?>