<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
$br_id = $hr_branch_id;
$sr = 0;
if(!isset($_SESSION['hr_id']))
{
    header('location: logout.php');
}

if(isset($_GET['br_id']))
{
    if($_GET['br_id'] > 0)
    {
        $br_id = $_GET['br_id'];
        $br_address = get_branch_name_by($br_id);
        $report_date = $_GET['report_date'];
        $attendance = "SELECT * FROM attendance_records INNER JOIN staff ON attendance_records.employee_id = staff.staff_id LEFT JOIN designations ON staff.designation_id = designations.designation_id LEFT JOIN branchs ON attendance_records.branch_id = branchs.id WHERE `attendance_record_created` LIKE '$report_date%' AND attendance_records.branch_id = '$br_id' AND staff.staff_status = '1' AND attendance_records.user_id > 0 ORDER BY attendance_records.branch_id, `staff_time_in` ";
        $attendance_absent = "SELECT * FROM `staff` INNER JOIN designations ON staff.designation_id = designations.designation_id LEFT JOIN branchs ON staff.branch_id = branchs.id WHERE `branch_id` = '$br_id' AND staff_id NOT IN (SELECT employee_id FROM attendance_records WHERE `attendance_record_created` LIKE '$report_date%' AND attendance_records.branch_id = '$br_id') AND staff.staff_status = '1' ORDER BY staff.branch_id, `staff_time_in` ";
    }
    elseif($_GET['br_id'] == 0)
    {
        $br_id = $_GET['br_id'];
        $br_address = "ORGANIZATION";
        $report_date = $_GET['report_date'];
        $attendance = "SELECT * FROM attendance_records INNER JOIN staff ON attendance_records.employee_id = staff.staff_id LEFT JOIN designations ON staff.designation_id = designations.designation_id LEFT JOIN branchs ON attendance_records.branch_id = branchs.id WHERE `attendance_record_created` LIKE '$report_date%' AND attendance_records.branch_id = '$br_id' AND staff.branch_id = '$br_id' AND staff.staff_status = '1' AND attendance_records.user_id > 0 ORDER BY attendance_records.branch_id, `staff_time_in` ";
        $attendance_absent = "SELECT * FROM `staff` INNER JOIN designations ON staff.designation_id = designations.designation_id LEFT JOIN branchs ON staff.branch_id = branchs.id WHERE `branch_id` = '$br_id' AND staff_id NOT IN (SELECT employee_id FROM attendance_records WHERE `attendance_record_created` LIKE '$report_date%' AND attendance_records.branch_id = '$br_id') AND staff.staff_status = '1' ORDER BY staff.branch_id, `staff_time_in` ";
    }
    elseif($_GET['br_id'] == -1)
    {
        $br_id = $_GET['br_id'];
        $br_address = "ALL BRANCHES";
        $report_date = $_GET['report_date'];
        $attendance = "SELECT * FROM attendance_records INNER JOIN staff ON attendance_records.employee_id = staff.staff_id LEFT JOIN designations ON staff.designation_id = designations.designation_id LEFT JOIN branchs ON attendance_records.branch_id = branchs.id WHERE `attendance_record_created` LIKE '$report_date%' AND staff.branch_id > 0 AND staff.staff_status = '1' AND attendance_records.user_id > 0 ORDER BY attendance_records.branch_id, `staff_time_in` ";
        $attendance_absent = "SELECT * FROM `staff` INNER JOIN designations ON staff.designation_id = designations.designation_id LEFT JOIN branchs ON staff.branch_id = branchs.id WHERE staff.branch_id > 0 AND staff_id NOT IN (SELECT employee_id FROM attendance_records WHERE `attendance_record_created` LIKE '$report_date%') AND staff.staff_status = '1' ORDER BY `staff_time_in`, staff.branch_id ";
    }
}


if(isset($_GET['attendance_record_bio_start_time']) && $_GET['attendance_record_bio_start_time'] != '')
{
    $br_id = $_GET['br_id'];
    $report_date = $_GET['report_date'];
    $attendance_record_id = $_GET['attendance_record_id'];
    $attendance_record_bio_start_time = $_GET['attendance_record_bio_start_time'];
    $update = "UPDATE `attendance_records` SET `attendance_record_bio_start_time` = '$attendance_record_bio_start_time' WHERE `attendance_record_id` = '$attendance_record_id' ";
    if(mysqli_query($con, $update))
    {
        header('location: print_attendance_daily.php?br_id='.$br_id.'&report_date='.$report_date);
    }
}
elseif(isset($_GET['attendance_record_bio_end_time']) && $_GET['attendance_record_bio_end_time'] != '')
{
    $br_id = $_GET['br_id'];
    $report_date = $_GET['report_date'];
    $attendance_record_id = $_GET['attendance_record_id'];
    $attendance_record_bio_end_time = $_GET['attendance_record_bio_end_time'];
    $update = "UPDATE `attendance_records` SET `attendance_record_bio_end_time` = '$attendance_record_bio_end_time' WHERE `attendance_record_id` = '$attendance_record_id' ";
    if(mysqli_query($con, $update))
    {
        header('location: print_attendance_daily.php?br_id='.$br_id.'&report_date='.$report_date);
    }
}
elseif(isset($_GET['attendance_record_remarks']) && $_GET['attendance_record_remarks'] != '')
{
    $br_id = $_GET['br_id'];
    $report_date = $_GET['report_date'];
    $attendance_record_id = $_GET['attendance_record_id'];
    $attendance_record_remarks = $_GET['attendance_record_remarks'];
    $update = "UPDATE `attendance_records` SET `attendance_record_remarks` = '$attendance_record_remarks' WHERE `attendance_record_id` = '$attendance_record_id' ";
    if(mysqli_query($con, $update))
    {
        header('location: print_attendance_daily.php?br_id='.$br_id.'&report_date='.$report_date);
    }
}
?>
	<title>ATTENDANCE REPORT <?php echo $report_date.' - '.$br_address; ?> - <?php echo $company_trademark; ?></title>
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
	    <table class = "table table-bordered table-hover" id="myTable">
	        <thead>
	            <tr>
	                <th colspan = "2">
	                    <form>
	                        <input type = "hidden" value = "<?php echo $br_id; ?>" name = "br_id" />
	                        <input type = "hidden" value = "OPEN" name = "generate_report" />
	                        <input class = "form-control" onchange = "this.form.submit()" name = "report_date" type = "date" value = "<?php echo $report_date; ?>" />
	                    </form>
	                </th>
	                <th colspan = "8">
	                    <input class = "form-control" type="text" id="myInput" onkeyup="filterTable()" placeholder="Search for names..">
	                </th>
	            </tr>
	            <tr id="tableHeader">>
	                <th>SR</th>
	                <th>BR</th>
	                <th>EMPLOYEE</th>
	                <th>DESIGNATION</th>
	                <th>DUTY TIMING(HOURS)</th>
	                <th>START</th>
	                <th>END</th>
	                <th>BIO-START</th>
	                <th>BIO-END</th>
	                <th>REMARKS</th>
	                <th>ACTION</th>
	            </tr>
	        </thead>
	        <tbody>
	        <?php
	        $s = 0;
	        $run_attendance = mysqli_query($con, $attendance);
	        if(mysqli_num_rows($run_attendance) > 0)
	        {
	            while($row_attendance = mysqli_fetch_array($run_attendance))
	            {
	                $s++;
	                $attendance_record_id = $row_attendance['attendance_record_id'];
	                $staff_duty_in = $row_attendance['staff_duty_in'];
	                $staff_duty_out = $row_attendance['staff_duty_out'];
	                
	                $select_releaver = "SELECT staff.staff_name AS releaver_staff_name FROM `attendance_releaver_records` INNER JOIN staff ON attendance_releaver_records.releaver_staff_id = staff.staff_id WHERE `attendance_record_id` = '$attendance_record_id' ";
	                $run_releaver = mysqli_query($con, $select_releaver);
	                if(mysqli_num_rows($run_releaver) == 1)
	                {
	                    while($row_releaver = mysqli_fetch_array($run_releaver))
	                    {
	                        $releaver_staff_name = $row_releaver['releaver_staff_name'];
	                    }
	                }
	                else
	                {
	                    $releaver_staff_name = 0;
	                }
	            ?>
	           <tr>
	               <td><?php echo $s; ?></td>
	               <td><?php echo $row_attendance['tag_name']; ?></td>
	               <td><?php echo $row_attendance['staff_name']; if($releaver_staff_name != 0){echo '('.$releaver_staff_name.')';}?></td>
	               <td><?php echo $row_attendance['designation_title']; ?></td>
	               <td><?php echo date_format(date_create($staff_duty_in), "h:i:s A").' TO '.date_format(date_create($staff_duty_out), "h:i:s A"); ?>(<?php echo $row_attendance['staff_duty_hours']; ?>)</td>
               <?php 
               if($row_attendance['attendance_record_title'] == '1' || $row_attendance['attendance_record_title'] == '4')
               { ?>
	               <td><?php echo date_format(date_create($row_attendance['attendance_record_created']), "h:i:s A"); ?></td>
	               <td>
	                   <?php 
    	               if($row_attendance['attendance_record_end_time'] == '00:00:00')
    	               {
    	                   echo 'NOT END DUTY';
    	               }
    	               else
    	               {
    	               echo date_format(date_create($row_attendance['attendance_record_end_time']), "h:i:s A");
    	               } ?>
	               </td>
	               <td>
	                   <?php 
	                   //if($row_attendance['attendance_record_bio_start_time'] == '00:00:00')
	                   { ?>
	                   <form method = "GET">
	                        <input type = "hidden" name = "br_id" value = "<?php echo $_GET['br_id']; ?>" />
	                        <input type = "hidden" name = "report_date" value = "<?php echo $_GET['report_date']; ?>" />
	                        <input type = "hidden" name = "attendance_record_id" value = "<?php echo $attendance_record_id; ?>" />
	                        <input type = "time" name = "attendance_record_bio_start_time" value = "<?php echo $row_attendance['attendance_record_bio_start_time']; ?>" />
	                        <input type = "submit" value = "+" class = " btn-sm btn-success" />
	                   </form>
	                   <?php }
	                   //else
	                   //{
	                   //    echo $row_attendance['attendance_record_bio_start_time'];
	                   //} 
	                   ?>
	               </td>
	               <td>
	                   <?php 
	                   //if($row_attendance['attendance_record_bio_end_time'] == '00:00:00')
	                   { ?>
	                   <form method = "GET">
	                        <input type = "hidden" name = "br_id" value = "<?php echo $_GET['br_id']; ?>" />
	                        <input type = "hidden" name = "report_date" value = "<?php echo $_GET['report_date']; ?>" />
	                        <input type = "hidden" name = "attendance_record_id" value = "<?php echo $attendance_record_id; ?>" />
	                        <input type = "time" name = "attendance_record_bio_end_time" value = "<?php echo $row_attendance['attendance_record_bio_end_time']; ?>" />
	                        <input type = "submit" value = "+" class = "btn-sm btn-success" />
	                   </form>
	                   <?php }
	                   //else
	                   //{
	                   //    echo $row_attendance['attendance_record_bio_end_time'];
	                   //} 
	                   ?>
	               </td>
	               <td>
	                   <form method = "GET">
	                        <input type = "hidden" name = "br_id" value = "<?php echo $_GET['br_id']; ?>" />
	                        <input type = "hidden" name = "report_date" value = "<?php echo $_GET['report_date']; ?>" />
	                        <input type = "hidden" name = "attendance_record_id" value = "<?php echo $attendance_record_id; ?>" />
	                        <input type = "text" name = "attendance_record_remarks" value = "<?php echo $row_attendance['attendance_record_remarks']; ?>" />
	                        <input type = "submit" value = "SAVE" class = "btn-sm btn-success" />
	                   </form>
	               </td>
	               <td>
	                   <?php
	                   if(date_format(date_create($row_attendance['attendance_record_created']), "H:i:s") > date('H:i:s',strtotime($row_attendance['staff_duty_in'] . ' +16 minutes')) )
	                   {
	                       echo 'STAFF LATE';
	                   }
	                   ?>
	               </td>	               
               <?php }
               elseif($row_attendance['attendance_record_title'] == '3')
               { ?>
	               <td colspan = "4"></td>
	               <td>
	                   <form method = "GET">
	                        <input type = "hidden" name = "br_id" value = "<?php echo $_GET['br_id']; ?>" />
	                        <input type = "hidden" name = "report_date" value = "<?php echo $_GET['report_date']; ?>" />
	                        <input type = "hidden" name = "attendance_record_id" value = "<?php echo $attendance_record_id; ?>" />
	                        <input type = "text" name = "attendance_record_remarks" value = "<?php echo $row_attendance['attendance_record_remarks']; ?>" />
	                        <input type = "submit" value = "SAVE" class = "btn-sm btn-success" />
	                   </form>
	               </td>
	               <td>ABSENT</td>
               <?php }
               elseif($row_attendance['attendance_record_title'] == '2')
               { ?>
	               <td colspan = "4"></td>
	               <td>
	                   <form method = "GET">
	                        <input type = "hidden" name = "br_id" value = "<?php echo $_GET['br_id']; ?>" />
	                        <input type = "hidden" name = "report_date" value = "<?php echo $_GET['report_date']; ?>" />
	                        <input type = "hidden" name = "attendance_record_id" value = "<?php echo $attendance_record_id; ?>" />
	                        <input type = "text" name = "attendance_record_remarks" value = "<?php echo $row_attendance['attendance_record_remarks']; ?>" />
	                        <input type = "submit" value = "SAVE" class = "btn-sm btn-success" />
	                   </form>
	               </td>
	               <td>LEAVE</td>
               <?php } ?>
	           </tr>
	   <?php    }
	        }
	        $run_attendance_absent = mysqli_query($con, $attendance_absent);
	        if(mysqli_num_rows($run_attendance_absent) > 0)
	        {
	            while($row_attendance_absent = mysqli_fetch_array($run_attendance_absent))
	            {
	                $sr++; ?>
	           <tr>
	               <td></td>
	               <td><?php echo $row_attendance_absent['tag_name']; ?></td>
	               <td><?php echo $row_attendance_absent['staff_name']; ?></td>
	               <td><?php echo $row_attendance_absent['designation_title']; ?></td>
	               <td><?php echo date_format(date_create($row_attendance_absent['staff_time_in']), "h:i:s A").' TO '.date_format(date_create($row_attendance_absent['staff_time_out']), "h:i:s A").'('.$row_attendance_absent['staff_duty_hours'].')'; ?></td>
	               <td colspan = "5"></td>
               </tr>
            <?php
	            }
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("#myInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#myTable tbody tr").filter(function() {
      // Toggle the display of rows based on whether they contain the input value
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
  });
});    
</script>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
<script>
function filterTable() {
  // Declare variables
  var input, filter, table, tbody, tr, td, i, j, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tbody = table.getElementsByTagName("tbody")[0]; // Target the tbody
  tr = tbody.getElementsByTagName("tr");

  // Loop through all table rows, starting from index 0 of tbody
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0]; // Get the first cell (Name column)
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = ""; // Show row
      } else {
        tr[i].style.display = "none"; // Hide row
      }
    }
  }
}
    
</script>
<?php mysqli_close($con); ?>