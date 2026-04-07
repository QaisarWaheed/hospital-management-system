<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 

$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if(mysqli_num_rows($run_roles) == 1)
{
    while($row_role = mysqli_fetch_array($run_roles))
    {
        $role_title = $row_role['title'];
    }
}
else
{
    $role_title = '';
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image" oncontextmenu="return false;">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke" style = "text-transform: uppercase;">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	    <div>
	        <table class = "table table-hover">
	            <thead>
	                <tr>
	                    <th>S#</th>
	                    <th>Id</th>
	                    <th>Dcotor Name</th>
	                    <th>Phone No</th>
	                    <th>Today Total</th>
	                    <th>Current Month Total</th>
	                </tr>
	            </thead>
	            <tbody>
<?php
$doctor_used = '';
$doctor_used .= '(';
$current_month_count_patients_total = 0;
$today_count_patients_total = 0;
$current_day = date('Y-m-d');
$current_month = date('Y-m');
$to_user_id = $_GET['dr_id'];
$select_referral_token = "SELECT distinct from_user_id, count(from_user_id) FROM `referral_patients` WHERE to_user_id = '$to_user_id' AND user_id != 0 AND referral_patient_created LIKE '$current_month%' GROUP BY from_user_id ORDER BY `referral_patients`.`referral_patient_id` DESC";
$run_referral_token = mysqli_query($con, $select_referral_token);
if(mysqli_num_rows($run_referral_token) > 0)
{
    while($row_referral_token = mysqli_fetch_array($run_referral_token))
    {
        $s++;
        $from_user = $row_referral_token['from_user_id'];
        $doctor_used .= $from_user.', ';
        $from_user_id = get_uname_by_id($row_referral_token['from_user_id']);
        $today_count_patients = mysqli_num_rows(mysqli_query($con, "SELECT from_user_id FROM `referral_patients` WHERE from_user_id = '$from_user' AND to_user_id = '$to_user_id' AND user_id != 0 AND referral_patient_created LIKE '$current_day%' GROUP BY from_user_id ORDER BY `referral_patients`.`referral_patient_id` DESC"));
        $today_count_patients_total = $today_count_patients_total + $today_count_patients;
        $current_month_count_patients = $row_referral_token['1'];
        $current_month_count_patients_total = $current_month_count_patients_total + $current_month_count_patients;
        $from_user_phone = get_user_phone_by_id($row_referral_token['from_user_id']);
        echo '<tr>
                    <td>'.$s.'</td>
                    <td>'.$from_user.'</td>
                    <td>'.$from_user_id.'</td>
                    <td>'.$from_user_phone.'</td>
                    <td>'.$today_count_patients.'</td>
                    <td>'.$current_month_count_patients.'</td>
              </tr>';
    }
    $doctor_used .= '-1)';
}
?>
	            </tbody>
	            <tbody>
	                <tr>
	                    <th></th>
	                    <th></th>
	                    <th></th>
	                    <th></th>
	                    <th><?php echo $today_count_patients_total; ?></th>
	                    <th><?php echo $current_month_count_patients_total; ?></th>
	                </tr>
	            </tbody>
	        </table>
	    </div>
	    <div>
	        <table class = "table table-hover table-bordered">
	            <thead>
	                <tr>
	                    <th>S#</th>
	                    <th>Id</th>
	                    <th>Doctor Name</th>
	                    <th>Phone</th>
	                    <th>Doctor Duty Time</th>
	                    <th>TOTAL PATIENTS CURRENT MONTH</th>
	                </tr>
	            </thead>
	            <tbody>
<?php
$s = 0;
$select_doctors = "SELECT * FROM users WHERE u_name NOT LIKE '%SELF' AND status = '1' AND role_id = '3' AND id NOT IN $doctor_used ";
$run_doctor = mysqli_query($con, $select_doctors);
if(mysqli_num_rows($run_doctor))
{
    while($row_doctor = mysqli_fetch_array($run_doctor))
    {
        $s = $s + 1;
        $doctor_id = $row_doctor['id'];
        $doctor_name = $row_doctor['u_name'];
        $doctor_phone = $row_doctor['phone'];
        $in_time = $row_doctor['in_time'];
        $out_time = $row_doctor['out_time']; ?>
        <tr>
            <td><?php echo $s; ?></td>
            <td><?php echo $doctor_id; ?></td>
            <td><?php echo $doctor_name; ?></td>
            <td><?php echo $doctor_phone; ?></td>
            <td><?php echo $in_time . ' TO ' .$out_time; ?></td>
            <td>NULL</td>
        </tr>
<?php    }
}
?>
	            </tbody>
	        </table>
	    </div>
	</div>
</div>

</body>
</html>
<?php mysqli_close($con); ?>