<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
?>
	<title>Lab Dashboard - <?php echo $company_trademark; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
    .background_image{
        background-image: url('../images/background.png');
        background-size: cover;
    }
    </style>    
    <style>
        @media print {
            body {
                /* Reduce the base font size for the entire page to 12px */
                font-size: 12px; 
            }

            table {
                font-size: 0.8em; 
            }
        }
    </style>
</head>

<body class="background_image">
<?php include 'top_navigation.php'; ?>
<div class="row">
	<div class="col-md-12">
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
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>