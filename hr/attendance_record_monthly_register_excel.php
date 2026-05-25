<?php include 'includes/connect.php'; 
$data = '';
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
    $br_id = $hr_branch_id;
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
$data .= '
<html>
<head>
    <title>ATTENDANCE REGISTER <?php echo get_branch_name_by($br_id); ?> FEB-2025 ORDER BY STAFF - <?php echo $company_trademark; ?></title>
</head>
<body>
	    <table class = "table table-bordered table-hover">
	        <caption style = "color: black;caption-side: top;text-align: center;">
	            <h2><?php echo get_branch_name_by($br_id); ?></h2>
	        </caption>
	        <thead>
	            <tr>
	                <th>SR</th>
	                <th>ID</th>
	                <th>EMPLOYEE</th>
	                <th>DESIGNATION</th>
	                <th>DAYS</th>';
                    for ($x = 1; $x <= $days_in_month; $x++) 
                    {
                        $data .= '<th>'.$x.'</th>';
                    }
	                $data .= '<th>DUTY</th>
	                <th>LEAVE</th>
	                <th>ABSENT</th>
	                <th>DOUBLE</th>
	            </tr>
	        </thead>
	        <tbody>';
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
	           $data .= '<tr>
	               <td>'.$s.'</td>
	               <td>'.$row_attendance['staff_id'].'</td>
	               <td>'.$row_attendance['staff_name'].'</td>
	               <td>'.$row_attendance['designation_title'].'</td>
	               <td>'.$days_in_month.'</td>';
                    for ($day = 1; $day <= $days_in_month; $day++) 
                    {
                        $select = "SELECT CASE WHEN attendance_records.attendance_record_title = '1' THEN 'P' WHEN attendance_records.attendance_record_title = '2' THEN 'L' WHEN attendance_records.attendance_record_title = '3' THEN 'A' WHEN attendance_records.attendance_record_title = '4' THEN 'D' ELSE ' ' END AS ATT_STATUS FROM `attendance_records` WHERE `employee_id` = '$staff_id' AND `attendance_record_month` = '$month' AND attendance_record_date = '$day' ";
            	        $run = mysqli_query($con, $select);
                        $data .= '<td>';
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
    	                            $data .= $attendacne_status;
            	            }
            	        }
            	        else
            	        {
	                            $data .= ' ';
            	        }      
                        $data .= '</td>';
                    }
                    $data .= '
                    <td>'.$p.'</td>
                    <td>'.$l.'</td>
                    <td>'.$a.'</td>
                    <td>'.$d.'</td>
	           </tr>';
	       }
	        }
	        else
	        {
	            $data .= '<tr><th colspan = "8">NO DATA FOUND</th></tr>';
	        }
	        $data .= '</tbody>
</table>
</body>
</html>';

$file_contents = $data;
header("Content-type: application/octet-stream");
header("Content-disposition: attachment; filename=my_file.xlsx");
echo $file_contents;
mysqli_close($con); 
?>