<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
include 'includes/head.php'; 
if(isset($_GET['selected_date']) && $_GET['selected_date'] != '')
{
    $selected_date = $_GET['selected_date'];
}
else
{
    $selected_date = date('Y-m-d');
}
?>
	<title>REFERAL PATIENTS OF <?php echo date_format(date_create($selected_date), 'd-M-Y'); ?> - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	    <table class = "table" style = "color: black;">
	        <caption style = "caption-side: top; text-align: center;color: black;">
	            <h2>REFERAL PATIENTS OF <?php echo date_format(date_create($selected_date), 'd-M-Y'); ?></h2>
	        </caption>
	        <thead>
	            <tr class = "nodisplay_print">
	                <th></th>
	                <th>
	                    <form action = "referral_patients.php">
	                        <input onchange="this.form.submit()" class = "form-control" type = "date" name = "selected_date" value = "<?php echo $selected_date; ?>" />
	                    </form>
	                </th>
	                <th colspan = "4"></th>
	                <th>
	                    <a href = "referral_patients_report.php" class = "btn btn-primary"> REPORT</a>
	                </th>
	            </tr>
	            <tr>
	                <th>S#</th>
	                <th>TimeDate</th>
	                <th>Token No</th>
	                <th>Patient Name</th>
	                <th>Patient Phone</th>
	                <th>Referal By</th>
	                <th>Branch</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 0;
$select = "SELECT DISTINCT item_by_doctor.tokan_no FROM item_by_doctor WHERE item_by_doctor.created LIKE '$selected_date%' AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (SELECT item_id FROM referral_tests)) ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $token_no = $row['tokan_no'];
        $select_token = "SELECT tokans.created, tokans.doctor_id, tokans.branch_id, patients.name, patients.phone, users.u_name FROM tokans 
        INNER JOIN patients ON tokans.patient_id = patients.id 
        INNER JOIN users ON tokans.doctor_id = users.id
        WHERE tokans.id = '$token_no' ";
        $run_token = mysqli_query($con, $select_token);
        if(mysqli_num_rows($run_token))
        {
            while($row_token = mysqli_fetch_array($run_token))
            {
                $token_create = $row_token['created'];
                $patient_name = $row_token['name'];
                $patient_phone = $row_token['phone'];
                $doctor_name = $row_token['u_name'];
                $doctor_id = $row_token['doctor_id'];
                $referal_from_branch_name = get_branch_tag_by($row_token['branch_id']);
                $select_date = date('Y-m-d');
                $searech_date = date_format(date_create($token_create),'Y-m-d');
                if($select_date != $searech_date)
                {
                    $INSERT = "INSERT INTO `referral_test_reports`
                    (`referral_test_report_id`, `referral_test_report_date`, `referral_test_report_token_no`, `referral_test_report_doctor_id`, `referral_test_report_status`, `referral_test_report_created`, `user_id`, `branch_id`) 
                    VALUES
                    (NULL, '$token_create', '$token_no', '$doctor_id', '1', '$current_date', '$lab_user_id', '".$row_token['branch_id']."')";
                }
            }
        }
        $s++;
?>
                <tr>
                    <td><?php echo $s; ?></td>
                    <td><?php echo date_format(date_create($token_create), 'h:i:s A'); ?></td>
                    <td><?php echo $row['tokan_no']; ?></td>
                    <td><?php echo $patient_name; ?></td>
                    <td><?php echo $patient_phone; ?></td>
                    <td><?php echo $doctor_name; ?></td>
                    <td><?php echo $referal_from_branch_name; ?></td>
                </tr>
<?php
    }
}
?>
	        </tbody>
	    </table>
	</div>
</div>

</body>
</html>