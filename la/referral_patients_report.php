<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
include 'includes/head.php'; 
?>
	<title>Lab Dashboard - <?php echo $company_trademark; ?></title>
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
	            <h2>REFERAL PATIENTS OF <?php echo date('M-Y'); ?></h2>
	        </caption>
	        <thead>
	            <tr>
	                <th>S#</th>
	                <th>Doctor Id</th>
	                <th>Doctor Name</th>
	                <th>Doctor Phone</th>
	                <th>Referal Patients</th>
	                <th>BRANCH</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 0;
$select_month = date('Y-m');
$select = "SELECT DISTINCT referral_test_report_doctor_id AS doctor_id, users.u_name AS doctor_name, users.phone AS doctor_phone, referral_test_reports.branch_id AS branch_id, COUNT(referral_test_report_token_no) AS total_refral_patient FROM `referral_test_reports` INNER JOIN users ON referral_test_report_doctor_id = users.id
WHERE referral_test_report_date LIKE '$select_month%' GROUP BY referral_test_report_doctor_id ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s++;
?>
                <tr>
                    <td><?php echo $s; ?></td>
                    <td><?php echo $row['doctor_id']; ?></td>
                    <td><?php echo $row['doctor_name']; ?></td>
                    <td><?php echo $row['doctor_phone']; ?></td>
                    <td><?php echo $row['total_refral_patient']; ?></td>
                    <td><?php echo get_branch_tag_by($row['branch_id']); ?></td>
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