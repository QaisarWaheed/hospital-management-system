<?php 
include 'includes/connect.php'; 
include 'includes/config.php'; 
include 'includes/head.php'; 
if(isset($_GET['lab_test_id']) && $_GET['lab_test_id'] != '')
{
    $lab_test_id = $_GET['lab_test_id'];
    $received_samples = "SELECT lab_tests.token_no, lab_tests.lab_test_status_id, lab_test_collected_comments, items.name AS test_name, patients.name, lab_test_received_sample_comments, lab_test_processed_comments, lab_test_conducted_comments, lab_test_approved_comments, lab_test_print_comments, patients.age, patients.phone, patients.cnic, tokans.created AS register_at, register.u_name AS register_by, lab_tests.sample_date_time AS collected_at, collected.u_name AS collected_by, lab_tests.lab_test_processed_created_at As processed_at, processed.u_name AS processed_by, lab_tests.lab_test_conducted_created_at AS conducted_at, conducted.u_name AS conducted_by, lab_tests.lab_test_print_created_at AS printed_at, printed.u_name AS printed_by FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN users register ON tokans.user_id = register.id LEFT JOIN users collected ON lab_tests.user_id = collected.id LEFT JOIN users processed ON lab_tests.lab_test_processed_created_by = processed.id LEFT JOIN users conducted ON lab_tests.lab_test_conducted_created_by = conducted.id LEFT JOIN users approved ON lab_tests.lab_test_approved_created_by = approved.id LEFT JOIN users printed ON lab_tests.lab_test_print_created_by = printed.id INNER JOIN items ON lab_tests.item_id = items.id WHERE lab_tests.lab_test_id = '$lab_test_id' ";
    $run_sample = mysqli_query($con, $received_samples);
    if(mysqli_num_rows($run_sample) > 0)
    {
        while($row_sample = mysqli_fetch_array($run_sample))
        {
            // Lab Test Detail
            $token_no = $row_sample['token_no'];
            $test_name = $row_sample['test_name'];
            $lab_test_status_id = $row_sample['lab_test_status_id'];
            
            // Patient Details
            $patient_name = $row_sample['name'];
            $patient_age = $row_sample['age'];
            $patient_phone = $row_sample['phone'];
            $patient_cnic = $row_sample['cnic'];
            
            // Register By
            $register_by = $row_sample['register_by'];
            $register_at = $row_sample['register_at'];
            
            // Collected By
            $collected_by = $row_sample['collected_by'];
            $collected_at = $row_sample['collected_at'];
            
            // Processed By
            $processed_by = $row_sample['processed_by'];
            $processed_at = $row_sample['processed_at'];
            
            // Conducted By
            $conducted_by = $row_sample['conducted_by'];
            $conducted_at = $row_sample['conducted_at'];
            
            // Approved By
            $approved_by = $row_sample['approved_by'];
            $approved_at = $row_sample['approved_at'];
            
            // Approved By
            $printer_by = $row_sample['printer_by'];
            $printer_at = $row_sample['printer_at'];
            
            // Comments
            $lab_test_collected_comments = $row_sample['lab_test_collected_comments'];
            $lab_test_received_sample_comments = $row_sample['lab_test_received_sample_comments'];
            $lab_test_processed_comments = $row_sample['lab_test_processed_comments'];
            $lab_test_conducted_comments = $row_sample['lab_test_conducted_comments'];
            $lab_test_approved_comments = $row_sample['lab_test_approved_comments'];
            $lab_test_print_comments = $row_sample['lab_test_print_comments'];
        }
    }
}
else
{
    header('location: logout.php');
}
?>
	<title>SAMPLES IN LAB FOR TEST (<?php echo date('d-m-Y'); ?>)- <?php echo $lab_login_branch_name; ?> - LAB - <?php echo $company_trademark; ?></title>
</head>

<body class = "p-2">
    <div class = "row">
        <div class = "col">
            <h1 align = "center">LAB TEST STATUS </h1>
        </div>
    </div>
    <div class = "row">
        <div class = "col">
            <label>TOKEN #</label>
            <input type = "text" readonly value = "<?php echo $token_no; ?>" class = "form-control" />
        </div>
        <div class = "col">
            <label>TEST ID</label>
            <input type = "text" readonly value = "<?php echo $lab_test_id; ?>" class = "form-control" />
        </div>
    </div>
    <div class = "row">
        <div class = "col">
            <label>NAME</label>
            <input type = "text" readonly value = "<?php echo $patient_name; ?>" class = "form-control" />
        </div>
        <div class = "col">
            <label>AGE / SEX</label>
            <input type = "text" readonly value = "<?php echo $patient_age; ?>" class = "form-control" />
        </div>
    </div>
    <div class = "row">
        <div class = "col">
            <label>PHONE</label>
            <input type = "text" readonly value = "<?php echo $patient_phone; ?>" class = "form-control" />
        </div>
        <div class = "col">
            <label>CNIC</label>
            <input type = "text" readonly value = "<?php echo $patient_cnic; ?>" class = "form-control" />
        </div>
    </div>
    <div class = "row">
        <div class = "col">
            <label>TEST NAME</label>
            <input type = "text" readonly value = "<?php echo $test_name; ?>" class = "form-control" />
        </div>
    </div>
    <table class = "table table-bordered table-hover" style = "color: black'">
        <tr>
            <th>DATE</th>
            <th>ACTION</th>
            <th>COMMENTS</th>
            <th>USER</th>
        </tr>
        <?php
        if($lab_test_status_id >= '1')
        {
            echo '<tr><td>'.date_format(date_create($register_at), 'h:i:s A d-m-Y').'</td><td>Register Test</td><td></td><td>'.$register_by.'</td></tr>';
        }
        if($lab_test_status_id >= '2')
        {
            echo '<tr><td>'.date_format(date_create($collected_at), 'h:i:s A d-m-Y').'</td><td>Specimen Collected</td><td>'.$lab_test_collected_comments.'</td><td>'.$collected_by.'</td></tr>';
        }
        if($lab_test_status_id >= '3')
        {
            echo '<tr><td>'.date_format(date_create($processed_at), 'h:i:s A d-m-Y').'</td><td>Test Process</td><td>'.$lab_test_processed_comments.'</td><td>'.$processed_by.'</td></tr>';
        }
        if($lab_test_status_id >= '4')
        {
            echo '<tr><td>'.date_format(date_create($conducted_at), 'h:i:s A d-m-Y').'</td><td>Conducted</td><td>'.$lab_test_conducted_comments.'</td><td>'.$conducted_by.'</td></tr>';
        }
        if($lab_test_status_id >= '5')
        {
            echo '<tr><td>'.date_format(date_create($approved_at), 'h:i:s A d-m-Y').'</td><td>Approved Report</td><td>'.$lab_test_approved_comments.'</td><td>'.$approved_by.'</td></tr>';
        }
        if($lab_test_status_id >= '6')
        {
            echo '<tr><td>'.date_format(date_create($printed_at), 'h:i:s A d-m-Y').'</td><td>Print</td><td>'.$lab_test_print_comments.'</td><td>'.$printed_by.'</td></tr>';
        }
        ?>
    </table>
</body>
</html>