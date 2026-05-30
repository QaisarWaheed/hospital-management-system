<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include 'includes/connect.php';
include 'includes/config.php';

function lab_test_type_report_esc($con, $value)
{
    return mysqli_real_escape_string($con, (string) ($value ?? ''));
}

/**
 * After saving conducted results: reload opener (if any) and land on conducted queue.
 */
function lab_test_type_report_finish_save($message = '')
{
    header('Content-Type: text/html; charset=utf-8');
    $safe_msg = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Lab test saved</title></head><body>';
    if ($message !== '') {
        echo '<p>' . $safe_msg . '</p>';
    }
    echo '<script type="text/javascript">';
    echo 'try { if (window.opener && !window.opener.closed) { window.opener.location.href = "lab_test_conducted.php"; } } catch (e) {}';
    echo 'window.location.replace("lab_test_conducted.php");';
    echo '</script>';
    echo '<p><a href="lab_test_conducted.php">Continue to conducted tests</a></p>';
    echo '</body></html>';
    exit;
}

if (isset($_GET['lab_test_conducted_save']) && $_GET['lab_test_conducted_save'] !== '') {
    $lab_test_id = (int) ($_GET['lab_test_id'] ?? 0);
    $test_id = (int) ($_GET['test_id'] ?? 0);
    $lab_test_conducted_comments = lab_test_type_report_esc($con, $_GET['lab_test_conducted_comments'] ?? '');

    if ($lab_test_id < 1 || $test_id < 1) {
        lab_test_type_report_finish_save('Invalid test. Please open the test again from the conducted list.');
    }

    $existing = mysqli_query($con, "SELECT 1 FROM `lab_test_reports` WHERE `lab_test_id` = '$lab_test_id' LIMIT 1");
    if ($existing && mysqli_num_rows($existing) > 0) {
        lab_test_type_report_finish_save('Results already saved for this test. Check the reports section.');
    }

    $reporting_ids = (isset($_GET['lab_reporting_test_id']) && is_array($_GET['lab_reporting_test_id']))
        ? $_GET['lab_reporting_test_id']
        : array();
    $results = (isset($_GET['lab_test_result']) && is_array($_GET['lab_test_result']))
        ? $_GET['lab_test_result']
        : array();
    $param_count = min(count($reporting_ids), count($results));

    for ($i = 0; $i < $param_count; $i++) {
        $lab_reporting_test_id = (int) $reporting_ids[$i];
        $lab_test_report_result = lab_test_type_report_esc($con, $results[$i] ?? '');
        if ($lab_reporting_test_id < 1) {
            continue;
        }
        $insert = "INSERT INTO `lab_test_reports`(`lab_test_report_id`, `lab_test_id`, `item_id`, `lab_reporting_test_id`, `lab_test_report_result`, `lab_test_report_status`, `lab_test_report_created_by`, `lab_test_report_created_at`)
            VALUES (NULL, '$lab_test_id', '$test_id', '$lab_reporting_test_id', '$lab_test_report_result', '1', '$lab_user_id', '$current_date')";
        if (!mysqli_query($con, $insert)) {
            lab_test_type_report_finish_save('Could not save results: ' . mysqli_error($con));
        }
    }

    $update = "UPDATE `lab_tests` SET `lab_test_conducted_comments` = '$lab_test_conducted_comments', `lab_test_conducted_created_by` = '$lab_user_id', `lab_test_conducted_created_at` = '$current_date', `lab_test_status_id` = '5' WHERE `lab_test_id` = '$lab_test_id' ";
    if (!mysqli_query($con, $update)) {
        lab_test_type_report_finish_save('Could not update test status: ' . mysqli_error($con));
    }

    lab_test_type_report_finish_save();
}

if (isset($_POST['lab_test_result']) && $_POST['lab_test_result'] !== '') {
    $lab_test_id = (int) ($_POST['lab_test_id'] ?? 0);
    $test_id = (int) ($_POST['test_id'] ?? 0);
    $lab_test_result = lab_test_type_report_esc($con, $_POST['lab_test_result']);
    $lab_test_conducted_comments = lab_test_type_report_esc($con, $_POST['lab_test_conducted_comments'] ?? '');

    if ($lab_test_id < 1 || $test_id < 1) {
        lab_test_type_report_finish_save('Invalid test. Please open the test again.');
    }

    $insert = "INSERT INTO `lab_test_reports`
        (`lab_test_report_id`, `lab_test_id`, `item_id`, `lab_test_report_result`, `lab_test_report_status`, `lab_test_report_created_by`, `lab_test_report_created_at`)
        VALUES (NULL, '$lab_test_id', '$test_id', '$lab_test_result', '1', '$lab_user_id', '$current_date')";
    if (mysqli_query($con, $insert)) {
        $update = "UPDATE `lab_tests` SET `lab_test_conducted_comments` = '$lab_test_conducted_comments', `lab_test_conducted_created_by` = '$lab_user_id', `lab_test_conducted_created_at` = '$current_date', `lab_test_status_id` = '5' WHERE `lab_test_id` = '$lab_test_id' ";
        mysqli_query($con, $update);
        lab_test_type_report_finish_save();
    }

    lab_test_type_report_finish_save('Could not save result: ' . mysqli_error($con));
}

include 'includes/head.php';

if (isset($_GET['lab_test_id']) && $_GET['lab_test_id'] != '') {
    $lab_test_id = (int) $_GET['lab_test_id'];
    $received_samples = "SELECT lab_tests.token_no, items.id AS test_id, items.name AS test_name, patients.name, patients.age, patients.phone, patients.cnic, lab_reporting_test_unit, lab_reporting_test_normal_male, lab_reporting_test_normal_female, lab_reporting_test_normal_childern, tokans.branch_id AS test_branch_id  FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN items ON lab_tests.item_id = items.id INNER JOIN lab_reporting_tests ON items.id = lab_reporting_tests.item_id WHERE lab_tests.lab_test_id = '$lab_test_id' ";
    $run_sample = mysqli_query($con, $received_samples);
    if (mysqli_num_rows($run_sample) > 0) {
        while ($row_sample = mysqli_fetch_array($run_sample)) {
            // Lab Test Detail
            $token_no = $row_sample['token_no'];
            $test_id = $row_sample['test_id'];
            $test_branch_id = $row_sample['test_branch_id'];
            $test_name = $row_sample['test_name'];

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
} else {
    header('location: logout.php');
    exit;
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
            <input type = "text" readonly value = "<?php echo $test_id .' - '.$test_name; ?>" class = "form-control" />
        </div>
        <div class = "col">
            <label>TEST BRANCH ID</label>
            <input type = "text" readonly value = "<?php echo $test_branch_id .' - '. get_branch_tag_by($test_branch_id); ?>" class = "form-control" />
        </div>
    </div>
    <form method = "GET" action = "">
        <input type = "hidden" name = "lab_test_id" value = "<?php echo $lab_test_id; ?>" class = "form-control" />
        <input type = "hidden" name = "test_id" value = "<?php echo $test_id; ?>" class = "form-control" />
    <table class = "table table-bordered table-hover" style = "color: black'">
        <caption style = "caption-side: top; color: black; text-align: center;">
            <?php echo $test_name; ?>
        </caption>
        <tr>
            <th>ID</th>
            <th>PARAMETER</th>
            <th>UNIT</th>
            <th>RESULT RANGE</th>
            <th>FINGINGS</th>
        </tr>
        <?php
        $select_parameter = "SELECT `lab_reporting_test_id`, `parameter_name`, `lab_reporting_test_unit`, `lab_reporting_test_normal_value`, `lab_reporting_test_normal_male`, `lab_reporting_test_normal_female`, `lab_reporting_test_normal_childern`, lab_test_units.lab_test_unit_value FROM `lab_reporting_tests` INNER JOIN lab_test_units ON lab_reporting_tests.lab_test_unit_id = lab_test_units.lab_test_unit_id WHERE lab_reporting_test_status = '1' AND `item_id` = '$test_id' ";
        $run_parameter = mysqli_query($con, $select_parameter);
        $count_parameters = mysqli_num_rows($run_parameter);
        if (mysqli_num_rows($run_parameter) > 0) {
            while ($row_parameter = mysqli_fetch_array($run_parameter)) {
                $lab_reporting_test_id = $row_parameter['lab_reporting_test_id'];
                $parameter_name = $row_parameter['parameter_name'];

                // Lab Test Reporting Detail
                $lab_test_unit_value = $row_parameter['lab_test_unit_value'];
                $lab_reporting_test_normal_value = $row_parameter['lab_reporting_test_normal_value'];
                $lab_reporting_test_normal_male = $row_parameter['lab_reporting_test_normal_male'];
                $lab_reporting_test_normal_female = $row_parameter['lab_reporting_test_normal_female'];
                $lab_reporting_test_normal_childern = $row_parameter['lab_reporting_test_normal_childern'];
                $lab_reporting_test_normal = '0';
                ?>
        <tr>
            <td><?php echo $lab_reporting_test_id; ?></td>
            <td><?php echo $parameter_name; ?></td>
            <td><?php echo $lab_test_unit_value; ?></td>
            <td>
                <?php
                if ($lab_reporting_test_normal_value != '') {
                    $lab_reporting_test_normal = '-1';
                    echo $lab_reporting_test_normal_value. '</br>';
                }
                if ($lab_reporting_test_normal_male != '') {
                    $lab_reporting_test_normal = '-1';
                    echo 'Male: '.$lab_reporting_test_normal_male. '</br>';
                }
                if ($lab_reporting_test_normal_female != '') {
                    $lab_reporting_test_normal = '-1';
                    echo 'Female: '. $lab_reporting_test_normal_female. '</br>';
                }
                if ($lab_reporting_test_normal_childern != '') {
                    $lab_reporting_test_normal = '-1';
                    echo 'Childern: '. $lab_reporting_test_normal_childern. '</br>';
                }
                if ($lab_reporting_test_normal == '0') {
                    echo '<a href = "add_test_normal_range.php?lab_test_id='.$lab_test_id.'&test_id='.$test_id.'">ADD TEST RANGE DATA</a>';
                }
                ?>
            </td>
            <td>
                <input type = "hidden" name = "lab_reporting_test_id[]" value = "<?php echo $lab_reporting_test_id; ?>" class = "form-control" />
                <?php
                if ($lab_reporting_test_id == 77 || $lab_reporting_test_id == 78 || $lab_reporting_test_id == 79) {
                    echo '<input required type = "number" step = "0.01" id = "'.$lab_reporting_test_id.'" name = "lab_test_result[]" value = "0" min = "0" onblur="findTotalAll()" class = "form-control" />';
                } elseif ($lab_reporting_test_id == 19 || $lab_reporting_test_id == 20 || $lab_reporting_test_id == 21 || $lab_reporting_test_id == 23 || $lab_reporting_test_id == 24) {
                    echo '<input required type = "number" step = "0.01" id = "'.$lab_reporting_test_id.'" name = "lab_test_result[]" value = "0" min = "0" onblur="findTotalE1()" class = "form-control" />';
                } else {
                    echo '<input required type = "text" id = "'.$lab_reporting_test_id.'" name = "lab_test_result[]" class = "form-control" />';
                } ?>
            </td>
        </tr>
        <?php }
        } ?>
        <tr>
            <td colspan = "5">
                <label>TEST REPROT COMMENT</label>
                <input type = "text" name = "lab_test_conducted_comments" class = "form-control" />
            </td>
        </tr>
        <tr>
            <td colspan = "5">
                <input type = "hidden" name = "count_parameters" value = "<?php echo $count_parameters; ?>" class = "form-control" />
                <?php
                if (str_contains((string) $test_name, 'CBC COMPLETE')) {
                    echo '<input type="text" name="total" id="total" class = "form-control"/>';
                    echo '<input id = "save_button" type = "submit" value = "SAVE TEST REPORT" name = "lab_test_conducted_save" class = "btn btn-info" />';
                } else {
                    echo '<input type = "submit" value = "SAVE TEST REPORT" name = "lab_test_conducted_save" class = "btn btn-info" />';
                } ?>

                <input type = "reset" name = "reset" class = "btn btn-danger" />
            </td>
        </tr>
    </table>
    </form>
</body>
</html>
 <script type="text/javascript">
function findTotalE1() {
    var LYM = parseFloat(document.getElementById('19').value);
    var MID = parseFloat(document.getElementById('20').value);
    var NEU = parseFloat(document.getElementById('21').value);
    var BAS = parseFloat(document.getElementById('23').value);
    var EOS = parseFloat(document.getElementById('24').value);
    var tot = parseInt(LYM+MID+NEU+BAS+EOS);
    if(tot === 100)
    {
        document.getElementById('total').value = tot;
        document.getElementById('save_button').disabled = false;
    }
    else
    {
        document.getElementById('total').value = tot;
        document.getElementById('save_button').disabled = true;
    }
}
function findTotalAll() {
    var LYM = parseFloat(document.getElementById('77').value);
    var MID = parseFloat(document.getElementById('78').value);
    var GRAN = parseFloat(document.getElementById('79').value);
    var tot = parseInt(LYM+MID+GRAN);
    if(tot === 100)
    {
        document.getElementById('total').value = tot;
        document.getElementById('save_button').disabled = false;
    }
    else
    {
        document.getElementById('total').value = tot;
        document.getElementById('save_button').disabled = true;
    }
}
</script>
