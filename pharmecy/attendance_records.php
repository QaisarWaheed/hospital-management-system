<?php include 'includes/connect.php';

$month = date('Y-m');
$day = date('d');
$br_id = $branch_id;
$attendance_record_month = substr(date('Y-m-d H:i:s'), 0, 7);

if (isset($_POST['save_break_records'])) {
    $attendance_record_id = (int) $_POST['attendance_record_id'];
    $insert = "INSERT INTO `attendance_break_records`
    (`attendance_break_record_id`, `attendance_record_id`, `attendance_break_record_status`, `attendance_break_record_created_at`, `attendance_break_record_created_by`)
    VALUES (NULL, '$attendance_record_id', '1', '$current_date', '$user_id')";
    if (mysqli_query($con, $insert)) {
        header('Location: attendance_records.php?msg=break_start');
        exit;
    }
    exit;
}

if (isset($_POST['save_records'])) {
    $employee_id = $_POST['employee_id'] ?? '';
    $attendance_record_title = $_POST['attendance_record_title'] ?? '1';
    $attendance_record_remarks = $_POST['attendance_record_remarks'] ?? '';
    if ($employee_id === '') {
        header('Location: attendance_records.php?msg=error');
        exit;
    }
    $staff_time_in = get_staff_time_in($employee_id);
    $staff_time_out = get_staff_time_out($employee_id);
    $insert = "INSERT INTO `attendance_records`
    (`attendance_record_id`, `attendance_record_month`, `attendance_record_date`, `employee_id`, `attendance_record_title`, `attendance_record_remarks`, `attendance_record_start_time`, `attendance_record_status`, `attendance_record_created`, `user_id`, `branch_id`, `staff_duty_in`, `staff_duty_out`)
    VALUES
    (NULL, '".substr(date('Y-m-d H:i:s'), 0, 7)."', '".substr(date('Y-m-d H:i:s'), 8, 2)."', '$employee_id', '$attendance_record_title', '$attendance_record_remarks', '".substr(date('Y-m-d H:i:s'), 11)."', '1', '$current_date', '$user_id', '$branch_id', '$staff_time_in', '$staff_time_out')";
    if (mysqli_query($con, $insert)) {
        $attendance_record_id = mysqli_insert_id($con);
        if ($attendance_record_title == 2) {
            $releaver_staff_id = $_POST['releaver_staff_id'] ?? 0;
            mysqli_query($con, "INSERT INTO `attendance_releaver_records`
            (`attendance_releaver_record_id`, `staff_id`, `releaver_staff_id`, `attendance_record_id`, `attendance_releaver_record_created_by`, `attendance_releaver_record_created_at`, `branch_id`)
            VALUES
            (NULL, '$employee_id', '$releaver_staff_id', '$attendance_record_id', '$user_id', '$current_date', '$br_id')");
        }
        header('Location: attendance_records.php?msg=in');
        exit;
    }
    header('Location: attendance_records.php?msg=error');
    exit;
}

if (isset($_POST['update_record'])) {
    $attendance_record_id = (int) $_POST['attendance_record_id'];
    $update = "UPDATE `attendance_records` SET `attendance_record_end_time` = '".substr(date('Y-m-d H:i:s'), 11)."', attendance_record_updated_at = '$current_date', attendance_record_updated_by = '$user_id' WHERE `attendance_record_id` = '$attendance_record_id' ";
    if (mysqli_query($con, $update)) {
        header('Location: attendance_records.php?msg=out');
        exit;
    }
    exit;
}

if (isset($_POST['update_break_record'])) {
    $attendance_record_id = (int) $_POST['attendance_record_id'];
    $update = "UPDATE `attendance_break_records` SET `attendance_break_record_status` = '2', attendance_break_record_updated_at = '$current_date', attendance_break_record_updated_by = '$user_id' WHERE `attendance_record_id` = '$attendance_record_id' AND attendance_break_record_status = '1' ";
    if (mysqli_query($con, $update)) {
        header('Location: attendance_records.php?msg=break_end');
        exit;
    }
    exit;
}

if (isset($_POST['br_id'])) {
    $br_id = $_POST['br_id'];
}

include 'includes/head.php';
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
<script src="js/jquery.min.js"></script>
<script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
</head>

<body class="background_image">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
	    <div class = "row">
    	    <div class = "col"><a class = "btn btn-info" href = "dashboard.php">Dashboard</a></div>
    	    <div class = "col">YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</div>
	    </div>
	</div>
	<div class="col-md-12 bg-primary">
	    <table class = "table table-bordered table-hover">
	        <caption>
	            
	        </caption>
	        <thead>
	            <form method = "POST" action = "attendance_records.php">
	            <tr>
	                <td>
	                    <a class = "btn btn-sm" style = "background-color: black;color: white;" href = "add_staff.php" >ADD STAFF</a>
	                </td>
	                <td>
	                    <select readonly name = "br_id" id = "br_id" class = "form-control">
                            <?php echo '<option SELECTED value = "'.$branch_id.'">'.$branch_address.'</option>';?>
                        </select>
	                </td>
	                <td>
                		<select class = "bg-primary text-white" required name = "employee_id" id="select_item" placeholder="Pick Staff...">
                			<option value="">Select Staff...</option>
                            <?php
                            echo $user = "SELECT * FROM `staff` WHERE `branch_id` = '$br_id' AND `staff_status` = '1' AND staff_id NOT IN (SELECT `employee_id` FROM `attendance_records` WHERE `attendance_record_month` = '$month' AND `attendance_record_date` = '$day' AND branch_id = '$br_id') ";
                            $run_user = mysqli_query($con, $user);
                            if(mysqli_num_rows($run_user) > 0)
                            {
                                while($row_user = mysqli_fetch_array($run_user))
                                {
                                    $employee_id = $row_user['0'];
                                    $employee_name = $row_user['staff_name'];
                                    echo '<option value = "'.$employee_id.'">'.$employee_name.'</option>';
                                }
                            }
                            else
                            {
                                echo '<option value = "">NO EMPLOYEE DATA FOUND<option>';
                            }
                            ?>
                	    </select>
	                </td>
	                <td>
	                    <select required name = "attendance_record_title" id = "attendance_record_title" class = "form-control" onchange="myFunction()">
	                        <option value = "1">START DUTY</option>
	                        <!--<option value = "2">LEAVE</option>-->
	                        <!--<option value = "3">ABSENT</option>-->
	                        <!--<option value = "4">DOUBLE</option>-->
	                    </select>
                            <div style = "display: none;" id = "releaver">
                                <label for = "releaver_staff_id"> SELECT RELEAVER STAFF</label>
                                <select name = "releaver_staff_id" class = "form-control bg-primary text-white" id="releaver_staff_id" placeholder="Pick Leaver Staff...">
                                    <option SELECTED value = "0">NO STAFF</option>
                                <?php
                                $select_staff = "SELECT staff.staff_id, staff.staff_name, designations.designation_title, branchs.tag_name FROM `staff` INNER JOIN branchs ON staff.branch_id = branchs.id INNER JOIN designations ON staff.designation_id = designations.designation_id WHERE staff.staff_status = 1 AND `branch_id` > 0";
                                $run_staff = mysqli_query($con, $select_staff);
                                if($row_staff = mysqli_num_rows($run_staff) > 0)
                                {
                                    while($row_staff = mysqli_fetch_array($run_staff))
                                    {
                                        $releaver_staff_id = $row_staff['staff_id'];
                                        $releaver_staff_name = $row_staff['staff_name'];
                                        $releaver_designation_title = $row_staff['designation_title'];
                                        $releaver_tag_name = $row_staff['tag_name'];
                                        echo '<option value = "'.$releaver_staff_id.'">'.$releaver_staff_name.' - '.$releaver_designation_title.' ('.$releaver_tag_name.')</option>';
                                    }
                                }
                                ?>
                                </select>
                            </div>
                    </td>
	                <td>
	                    <input style = "min-width: 100%;" type = "submit" name = "save_records" id = "save_records" value = "PONCH IN" title = "DUTY START" class = "btn btn-success" />
	                </td>
	            </tr>
	            </form>
	            <tr>
	                <th>SR</th>
	                <th>BRANCH</th>
	                <th>EMPLOYEE</th>
	                <th>DATE</th>
	                <th>ACTION</th>
	            </tr>
	        </thead>
	        <tbody>
	        <?php
	        $s = 0;
	        $month = date('Y-m');
	        $attendance = "SELECT * FROM attendance_records INNER JOIN branchs ON attendance_records.branch_id = branchs.id INNER JOIN staff ON attendance_records.employee_id = staff.staff_id WHERE attendance_records.`attendance_record_month` = '$month' AND (`attendance_record_end_time` = '0000-00-00' OR `attendance_record_month` LIKE '$attendance_record_month') AND attendance_records.branch_id = '$branch_id'ORDER BY `attendance_records`.`attendance_record_created` DESC ";
	        $run_attendance = mysqli_query($con, $attendance);
	        if(mysqli_num_rows($run_attendance) > 0)
	        {
	            while($row_attendance = mysqli_fetch_array($run_attendance))
	            {
	                $s++;
	            ?>
	           <tr>
	               <td><?php echo $s; ?></td>
	               <td><?php echo $row_attendance['tag_name']; ?></td>
	               <td><?php echo $row_attendance['staff_name']; ?></td>
	               <td><?php echo $row_attendance['attendance_record_created']; ?></td>
	               <td><?php if($row_attendance['attendance_record_end_time'] == '00:00:00'){ ?> 
	                <form method = "POST">
	                    <input type = "hidden" name = "attendance_record_id" value = "<?php echo $row_attendance['attendance_record_id']; ?>" />
	                    <input type = "submit" value = "PONCH OUT" title = "DUTY OFF" name = "update_record" class = "btn btn-light btn-sm" />
	                    <!--<input type = "submit" value = "BREAK START" title = "DUTY BREAK" name = "save_break_records" class = "btn btn-light btn-info" />-->
	                    <!--<input type = "submit" value = "BREAK END" title = "DUTY BREAK" name = "update_break_record" class = "btn btn-light btn-success" />-->
	                    
	                </form>
	               <?php }else{echo $row_attendance['attendance_record_end_time'];} ?></td>
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
});
</script>
<script type="text/javascript">
      $(document).ready(function () {
  $('#releaver_staff_id').selectize({
      sortField: 'text'
  });
});
</script>
</body>
</html>
<script>
function myFunction() {
  var x = document.getElementById("attendance_record_title").value;
  if(x == 2)
  {
      document.getElementById("releaver").style.display = "inline";
  }
  else if(x == 3)
  {
      document.getElementById("releaver").style.display = "inline";
  }
  else
  {
      document.getElementById("releaver").style.display = "none";
  }
}
</script>
<?php mysqli_close($con); ?>