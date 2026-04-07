<?php 
include 'includes/connect.php'; 
include 'includes/config.php';
if(isset($_POST['update_lab_test_collected']) && $_POST['update_lab_test_collected'] != '')
{
    $lab_test_id = $_POST['lab_test_id'];
    $lab_test_collected_comments = $_POST['lab_test_collected_comments'];
    $update = "UPDATE `lab_tests` SET `lab_test_collected_comments` = '$lab_test_collected_comments', `lab_test_collected_created_by` = '$lab_user_id', `lab_test_collected_created_at` = '$current_date', `lab_test_status_id` = '3' WHERE `lab_test_id` = '$lab_test_id' ";
    mysqli_query($con, $update);
    header('location: lab_test_received_samples.php?msg=updated#'.$lab_test_id);
    exit(0);
}
?>
	<title>SAMPLES TRANSFER FOR TEST (<?php echo date('d-m-Y'); ?>)- <?php echo $lab_login_branch_name; ?> - LAB - <?php echo $company_trademark; ?></title>
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
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body class="background_image">
<?php include 'top_navigation.php'; ?>
    <div class="row">
    	<div class="col-md-12">
    	    <table class = "table table-bordered table-hover" style = "color: black'">
    	        <caption style = "text-align: center; caption-side: top;color: black;">
    	            <h1>SAMPLES TRANSFER FOR TEST (<?php echo date('d-m-Y'); ?>)</h1>
    	        </caption>
    	        <thead>
    	            <tr>
    	                <th>S #</th>
    	                <th>Token #</th>
    	                <th>Test Name</th>
    	                <th>Patient Name</th>
    	                <th>Age</th>
    	                <th>Register At</th>
    	                <th>Register By</th>
    	                <th>Collected At</th>
    	                <th>Collected By</th>
    	                <th>Transfer Through(staff name)</th>
    	                <th>Transfer To(Location)</th>
    	            </tr>
    	        </thead>
    	        <tbody>
    	            <?php
    	            $s = 0;
    	            $received_samples = "SELECT lab_tests.token_no, lab_tests.lab_test_id, items.name AS test_name, patients.name, patients.age, patients.phone, patients.cnic, tokans.created AS register_at, register.u_name AS register_by, lab_tests.sample_date_time AS collected_at, collected.u_name AS collected_by FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN users register ON tokans.user_id = register.id INNER JOIN users collected ON lab_tests.user_id = collected.id INNER JOIN items ON lab_tests.item_id = items.id WHERE `lab_test_status_id` = '2' ORDER BY `lab_tests`.`token_no` DESC";
    	            $run_sample = mysqli_query($con, $received_samples);
    	            if(mysqli_num_rows($run_sample) > 0)
    	            {
    	                while($row_sample = mysqli_fetch_array($run_sample))
    	                {
    	                    $s++; ?>
    	            <form method = "POST">
    	            <tr>
    	                <td><?php echo $s; ?></td>
    	                <td><?php echo $row_sample['token_no']; ?><a href="#" class = "btn btn-sm btn-success" onClick="MyWindow=window.open('lab_test_current_status.php?lab_test_id=<?php echo $row_sample['lab_test_id']; ?>','MyWindow','width=900,height=1200'); return false;">+</a></td>
    	                <td><?php echo $row_sample['test_name']; ?></td>
    	                <td><?php echo $row_sample['name']; ?></td>
    	                <td><?php echo $row_sample['age']; ?></td>
    	                <td><?php echo date_format(date_create($row_sample['register_at']), 'h:i:s A d-m-Y'); ?></td>
    	                <td><?php echo $row_sample['register_by']; ?></td>
    	                <td><?php echo date_format(date_create($row_sample['collected_at']), 'h:i:s A d-m-Y'); ?></td>
    	                <td><?php echo $row_sample['collected_by']; ?></td>
    	                <td>
    	                    <input type = "hidden" name = "lab_test_id" value = "<?php echo $row_sample['lab_test_id']; ?>" />
    	                    <input title = "type comments of 30 latters only." type = "test" maxlength = "30" name = "lab_test_collected_comments" class = "form-control" />
    	                </td>
    	                <td>
    	                    <input type = "submit" value = "UPDATE" name = "update_lab_test_collected" class = "btn-success" />
    	                </td>
    	            </tr>
    	            </form>
    	                <?php }
    	            }
    	            else
    	            {
    	                echo '<tr><th colspan = "9">NO PENDING SAMPLES IN LAB</th></tr>';
    	            }
    	            ?>
    	        </tbody>
    	    </table>
    	</div>
    </div>
</body>
</html>