<?php include 'includes/connect.php';

function days_in_month($month_days, $year)
{
    return $month_days == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month_days - 1) % 7 % 2 ? 30 : 31);
}

if (isset($_GET['br_id'])) {
    $br_id = (int) $_GET['br_id'];
} else {
    $br_id = (int) $hr_branch_id;
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
$month_filter = (isset($_GET['month']) && $_GET['month'] !== '') ? $_GET['month'] : $month;
$month_sql = mysqli_real_escape_string($con, $month);
$branch_filter_sql = " AND attendance_records.branch_id = '" . (int) $br_id . "' ";

$staff_rows = array();
$staff_sql = "SELECT DISTINCT staff.staff_id, staff.staff_name, designations.designation_title
    FROM attendance_records
    INNER JOIN staff ON attendance_records.employee_id = staff.staff_id
    INNER JOIN designations ON staff.designation_id = designations.designation_id
    WHERE attendance_records.attendance_record_month = '$month_sql' $branch_filter_sql
    ORDER BY staff.staff_name ASC";
$run_staff = mysqli_query($con, $staff_sql);
if ($run_staff) {
    while ($row = mysqli_fetch_assoc($run_staff)) {
        $staff_rows[] = $row;
    }
}

$attendance_by_staff = array();
$attendance_sql = "SELECT employee_id, attendance_record_date,
        CASE
            WHEN attendance_record_title = '1' THEN 'P'
            WHEN attendance_record_title = '2' THEN 'L'
            WHEN attendance_record_title = '3' THEN 'A'
            WHEN attendance_record_title = '4' THEN 'D'
            ELSE ' '
        END AS att_status
    FROM attendance_records
    WHERE attendance_record_month = '$month_sql' $branch_filter_sql
    ORDER BY employee_id, attendance_record_date";
$run_attendance_all = mysqli_query($con, $attendance_sql);
if ($run_attendance_all) {
    while ($row = mysqli_fetch_assoc($run_attendance_all)) {
        $sid = (int) $row['employee_id'];
        $day = (int) $row['attendance_record_date'];
        if (!isset($attendance_by_staff[$sid])) {
            $attendance_by_staff[$sid] = array();
        }
        if (!isset($attendance_by_staff[$sid][$day])) {
            $attendance_by_staff[$sid][$day] = array();
        }
        $attendance_by_staff[$sid][$day][] = $row['att_status'];
    }
}

$extra_duty_map = get_extra_staff_duty_map($month, $br_id);
?>
<?php include 'includes/head.php'; ?>
	<title>ATTENDANCE REGISTER <?php echo get_branch_name_by($br_id); ?> FEB-2025 ORDER BY STAFF - <?php echo $company_trademark; ?></title>
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
	                        <input onchange = "this.form.submit()" type = "month" name = "month" value = "<?php echo htmlspecialchars($month_filter, ENT_QUOTES, 'UTF-8'); ?>" class = "form-control" />
	                    </form>
	                </td>
	                <td colspan = "4">
	                    <form>
	                        <input type = "hidden" name = "month" value = "<?php echo htmlspecialchars($month_filter, ENT_QUOTES, 'UTF-8'); ?>" />
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
	        if (count($staff_rows) > 0) {
	            $s = 0;
	            foreach ($staff_rows as $row_attendance) {
	                $s++;
                    $p = 0;
                    $l = 0;
                    $a = 0;
                    $d = 0;
	                $staff_id = (int) $row_attendance['staff_id'];
	                $by_day = $attendance_by_staff[$staff_id] ?? array();
	                $extra = $extra_duty_map[$staff_id] ?? 0;
	            ?>
	           <tr>
	               <td><?php echo $s; ?></td>
	               <td><?php echo (int) $row_attendance['staff_id']; ?></td>
	               <td><?php echo htmlspecialchars($row_attendance['staff_name'], ENT_QUOTES, 'UTF-8'); ?></td>
	               <td><?php echo htmlspecialchars($row_attendance['designation_title'], ENT_QUOTES, 'UTF-8'); ?></td>
	               <td><?php echo $days_in_month; ?></td>
	               <?php
                    for ($day = 1; $day <= $days_in_month; $day++) {
                        echo '<td>';
                        if (!empty($by_day[$day])) {
                            $loc = 1;
                            $attendacne_msg = '';
                            foreach ($by_day[$day] as $attendacne_status) {
                                if ($loc == 1 || $attendacne_msg != $attendacne_status) {
                                    if ($attendacne_status == 'P') {
                                        $p++;
                                    } elseif ($attendacne_status == 'L') {
                                        $l++;
                                    } elseif ($attendacne_status == 'A') {
                                        $a++;
                                    } elseif ($attendacne_status == 'D') {
                                        $d++;
                                    }
                                    $loc = 0;
                                    $attendacne_msg = $attendacne_status;
                                }
                                echo $attendacne_status;
                            }
                        } else {
                            echo ' ';
                        }
                        echo '</td>';
                    } ?>
                    <td><?php echo $p; ?></td>
                    <td><?php echo $l; ?></td>
                    <td><?php echo $a; ?></td>
                    <td><?php echo $d + $extra; ?></td>
	           </tr>
	   <?php    }
	        } else {
	            echo '<tr><th colspan = "8">NO DATA FOUND</th></tr>';
	        }
	        ?>
	        </tbody>
</table>
	</div>
</div>
</body>
</html>
<?php mysqli_close($con); ?>