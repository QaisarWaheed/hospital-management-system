<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php';
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO</h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	    <table class = "table table-hover table-boredered">
	        <thead>
	            <tr>
	                <th>S#</th>
	                <th>DATE</th>
	                <th>STAFF</th>
	                <th>STAFF DUTY-IN</th>
	                <th>NOTIFICATION</th>
	                <th>ATTENDANCE-IN</th>
	                <th>ACTION</th>
	            </tr>
	        </thead>
	        <tbody>
	            <?php
	            $s = 0;
	            $select = "SELECT notifications.notification_id, notifications.notification_date, staff.staff_name, staff.staff_phone, staff.staff_time_in, attendance_records.attendance_record_start_time, notification_actions.notification_action_title, letters.letter_header, letters.letter_body, letters.letter_footer FROM `notifications` INNER JOIN staff ON notifications.staff_id = staff.staff_id INNER JOIN attendance_records ON staff.staff_id = attendance_records.employee_id INNER JOIN notification_actions ON notifications.notification_action_id = notification_actions.notification_action_id INNER JOIN letters ON notifications.letter_id = letters.letter_id WHERE `notification_status` = '1' AND `notification_date` = '2025-10-06' AND attendance_records.attendance_record_date = '6' AND attendance_record_month = '2025-10' ";
	            $run = mysqli_query($con, $select);
	            if(mysqli_num_rows($run) > 0)
	            {
	                while($row = mysqli_fetch_array($run))
	                {
	                    $notification_date = $row['notification_date'];
	                    $staff_name = $row['staff_name'];
	                    $staff_time_in = $row['staff_time_in'];
	                    $attendance_record_start_time = $row['attendance_record_start_time'];
	                    $letter_header = $row['letter_header'];
	                    $letter_body = $row['letter_body'];
	                    $letter_footer = $row['letter_footer'];
	                    $s++;
                echo '
                <tr>
                    <td>'.$s.'</td>
                    <td>'.$row['notification_date'].'</td>
                    <td>'.$row['staff_name'].'</td>
                    <td>'.$row['staff_time_in'].'</td>
                    <td>'.$row['notification_action_title'].'</td>
                    <td>'.$row['attendance_record_start_time'].'</td>
                    <td><a target = "_blank" href = "https://api.whatsapp.com/send/?phone=+92'.ltrim($row['staff_phone'], '0').'&text=*SUBJECT: '.$row['notification_action_title'].'* %0A%0A '.$letter_header.' '.$staff_name.' %0A%0A '.$letter_body.' %0A%0A '.$letter_footer.'"/>Whatsapp</td>
                </tr>
                ';
	                }
	            }
	            ?>
	        </tbody>
	    </table>
	</div>
</div>

</body>
</html>