<?php 
include 'includes/connect.php'; 
include 'includes/config.php'; 
include 'includes/head.php'; 
if(isset($_GET['lab_test_appoved_report_save']) && $_GET['lab_test_appoved_report_save'] != '')
{
    $lab_test_reports = $_GET['lab_test_report_id'];
    $lab_test_report_result = $_GET['lab_test_report_result'];
    foreach ($lab_test_reports as $key => $value) 
    {
        mysqli_query($con, "UPDATE `lab_test_reports` SET `lab_test_report_result` = '$lab_test_report_result[$key]' WHERE `lab_test_report_id` = '$value' ");
    }
    
    $lab_test_id = $_GET['lab_test_id'];
    $lab_test_approved_comments = $_GET['lab_test_approved_comments'];
    $update = "UPDATE `lab_tests` SET `lab_test_approved_comments` = '$lab_test_approved_comments', `lab_test_approved_created_by` = '$lab_user_id', `lab_test_approved_created_at` = '$current_date', `lab_test_status_id` = '6' WHERE `lab_test_id` = '$lab_test_id' AND  `lab_test_status_id` = '5' ";
    mysqli_query($con, $update);
    echo '<script type="text/javascript">
    window.opener.location.reload(true);
    window.close();</script>';
    exit(0);
}

if(isset($_GET['lab_test_id']) && $_GET['lab_test_id'] != '')
{
    $lab_test_id = $_GET['lab_test_id'];
    $received_samples = "SELECT lab_tests.token_no, items.id AS test_id, items.name AS test_name, patients.name, patients.age, patients.phone, patients.cnic, lab_reporting_test_unit, lab_reporting_test_normal_male, lab_reporting_test_normal_female, lab_reporting_test_normal_childern FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN items ON lab_tests.item_id = items.id INNER JOIN lab_reporting_tests ON items.id = lab_reporting_tests.item_id WHERE lab_tests.lab_test_id = '$lab_test_id' ";
    $run_sample = mysqli_query($con, $received_samples);
    if(mysqli_num_rows($run_sample) > 0)
    {
        while($row_sample = mysqli_fetch_array($run_sample))
        {
            // Lab Test Detail
            $token_no = $row_sample['token_no'];
            $test_id = $row_sample['test_id'];
            $test_name = $row_sample['test_name'];
            $lab_test_status_id = $row_sample['lab_test_status_id'];
            
            
            // Lab Test Reporting Detail
            $lab_reporting_test_unit = $row_sample['lab_reporting_test_unit'];
            $lab_reporting_test_normal_male = $row_sample['lab_reporting_test_normal_male'];
            $lab_reporting_test_normal_female = $row_sample['lab_reporting_test_normal_female'];
            $lab_reporting_test_normal_childern = $row_sample['lab_reporting_test_normal_childern'];
            
            // Patient Details
            $patient_name = $row_sample['name'];
            $patient_age = $row_sample['age'];
            $patient_phone = $row_sample['phone'];
            $patient_cnic = $row_sample['cnic'];
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
    <form method = "GET" action = "">
        <input type = "hidden" name = "lab_test_id" value = "<?php echo $lab_test_id; ?>" class = "form-control" />
        <input type = "hidden" name = "test_id" value = "<?php echo $test_id; ?>" class = "form-control" />
    <table class = "table table-bordered table-hover" style = "color: black'">
        <tr>
            <th>ID</th>
            <th>PARAMETER</th>
            <th>UNIT</th>
            <th>RESULT RANGE</th>
            <th>FINGINGS</th>
        </tr>
        <?php 
        $select_parameter = "SELECT lab_test_reports.lab_test_report_result, lab_test_report_id, lab_test_reports.lab_reporting_test_id, `parameter_name`, `lab_reporting_test_unit`, `lab_reporting_test_normal_value`, `lab_reporting_test_normal_male`, `lab_reporting_test_normal_female`, `lab_reporting_test_normal_childern`, lab_test_units.lab_test_unit_value FROM lab_test_reports INNER JOIN lab_tests ON lab_test_reports.lab_test_id = lab_tests.lab_test_id INNER JOIN lab_reporting_tests ON lab_test_reports.lab_reporting_test_id = lab_reporting_tests.lab_reporting_test_id  INNER JOIN lab_test_units ON lab_reporting_tests.lab_test_unit_id = lab_test_units.lab_test_unit_id WHERE lab_test_reports.lab_test_id = '$lab_test_id' AND lab_test_reports.item_id = '$test_id' ";
        $run_parameter = mysqli_query($con, $select_parameter);
        $count_parameters = mysqli_num_rows($run_parameter);
        if(mysqli_num_rows($run_parameter) > 0)
        {
            while($row_parameter = mysqli_fetch_array($run_parameter))
            {
                $lab_reporting_test_id = $row_parameter['lab_reporting_test_id'];
                $lab_test_report_id = $row_parameter['lab_test_report_id'];
                $parameter_name = $row_parameter['parameter_name'];

                // Lab Test Reporting Detail
                $lab_test_report_result = $row_parameter['lab_test_report_result'];
                $lab_test_unit_value = $row_parameter['lab_test_unit_value'];
                $lab_reporting_test_normal_value = $row_parameter['lab_reporting_test_normal_value'];
                $lab_reporting_test_normal_male = $row_parameter['lab_reporting_test_normal_male'];
                $lab_reporting_test_normal_female = $row_parameter['lab_reporting_test_normal_female'];
                $lab_reporting_test_normal_childern = $row_parameter['lab_reporting_test_normal_childern'];            
                ?>
        <tr>
            <td><?php echo $lab_reporting_test_id; ?></td>
            <td><?php echo $parameter_name; ?></td>
            <td><?php echo $lab_test_unit_value; ?></td>
            <td>
                <?php 
                if ($lab_reporting_test_normal_value != '')
                {
                    $lab_reporting_test_normal = '-1';
                    echo $lab_reporting_test_normal_value. '</br>';
                }
                if ($lab_reporting_test_normal_male != '')
                {
                    $lab_reporting_test_normal = '-1';
                    echo 'Male: '.$lab_reporting_test_normal_male. '</br>';
                }
                if($lab_reporting_test_normal_female != '')
                {
                    $lab_reporting_test_normal = '-1';
                    echo 'Female: '. $lab_reporting_test_normal_female. '</br>';
                }
                if($lab_reporting_test_normal_childern != '')
                {
                    $lab_reporting_test_normal = '-1';
                    echo 'Childern: '. $lab_reporting_test_normal_female. '</br>';
                }
                if($lab_reporting_test_normal == '0')
                {
                    echo '<a href = "add_test_normal_range.php?lab_test_id='.$lab_test_id.'&test_id='.$test_id.'">ADD TEST RANGE DATA</a>';
                }
                ?>
            </td>
            <td>
                <input type = "hidden" value = "<?php echo $lab_test_report_id; ?>" name = "lab_test_report_id[]" />
                <input type = "text" value = "<?php echo $lab_test_report_result; ?>" name = "lab_test_report_result[]" />
            </td>
        </tr>
        <?php } } ?>
        <tr>
            <td colspan = "5">
                <label>TEST APPROVED REPORT COMMENT</label>
                <input type = "text" name = "lab_test_approved_comments" class = "form-control" />
            </td>
        </tr>
        <tr>
            <td colspan = "5">
                <input type = "submit" value = "SAVE TEST APPROVED REPORT" name = "lab_test_appoved_report_save" class = "btn btn-info" />
                <input type = "reset" name = "reset" class = "btn btn-danger" />
            </td>
        </tr>
    </table>
    </form>
</body>
</html>