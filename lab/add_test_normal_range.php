<?php 
include 'includes/connect.php'; 
include 'includes/config.php'; 
include 'includes/head.php'; 
if(isset($_POST['update_test_details']) && $_POST['update_test_details'] != '')
{
    $lab_test_id = $_POST['lab_test_id'];
    $test_id = $_POST['test_id'];
    $lab_reporting_test_id = $_POST['lab_reporting_test_id'];
    $test_category_id = $_POST['test_category_id'];
    $lab_test_unit_id = $_POST['lab_test_unit_id'];
    $lab_reporting_test_normal_value = $_POST['lab_reporting_test_normal_value'];
    $lab_reporting_test_normal_male = $_POST['lab_reporting_test_normal_male'];
    $lab_reporting_test_normal_female = $_POST['lab_reporting_test_normal_female'];
    $lab_reporting_test_normal_childern = $_POST['lab_reporting_test_normal_childern'];
    $lab_reporting_test_time_minutes = $_POST['lab_reporting_test_time_minutes'];
    $lab_reporting_test_msg_if_normal = $_POST['lab_reporting_test_msg_if_normal'];
    $lab_reporting_test_msg_if_low = $_POST['lab_reporting_test_msg_if_low'];
    $lab_reporting_test_msg_if_high = $_POST['lab_reporting_test_msg_if_high'];
    echo $update = "UPDATE `lab_reporting_tests` SET `lab_reporting_test_unit`= '$lab_test_unit_id', `lab_test_unit_id`= '$lab_test_unit_id', `lab_reporting_test_normal_value`= '$lab_reporting_test_normal_value',`lab_reporting_test_normal_male`= '$lab_reporting_test_normal_male',`lab_reporting_test_normal_female`= '$lab_reporting_test_normal_female',`lab_reporting_test_normal_childern`= '$lab_reporting_test_normal_childern',`lab_reporting_test_type`='$test_category_id',`lab_reporting_test_time_minutes` = '$lab_reporting_test_time_minutes', `lab_reporting_test_msg_if_normal` = '$lab_reporting_test_msg_if_normal', `lab_reporting_test_msg_if_low` = '$lab_reporting_test_msg_if_low', `lab_reporting_test_msg_if_high` = '$lab_reporting_test_msg_if_high' WHERE  `lab_reporting_test_id` = '$lab_reporting_test_id' AND `item_id` = '$test_id' ";
    mysqli_query($con, $update);
    header('location: lab_test_type_report.php?lab_test_id='.$lab_test_id);
    exit(0);
}

if(isset($_GET['lab_test_id']) && $_GET['lab_test_id'] != '')
{
    $lab_test_id = $_GET['lab_test_id'];
    $test_id = $_GET['test_id'];
    $received_samples = "SELECT lab_reporting_test_id, lab_test_unit_id, lab_reporting_test_normal_value, lab_reporting_test_unit, lab_reporting_test_time_minutes, lab_reporting_test_normal_childern, lab_reporting_test_normal_female, lab_reporting_test_normal_male, test_categories.test_category_id, test_categories.test_category_title, items.name AS item_name, items.id AS item_id FROM `lab_reporting_tests` INNER JOIN items ON lab_reporting_tests.item_id = items.id INNER JOIN test_categories ON lab_reporting_tests.lab_reporting_test_type = test_categories.test_category_id WHERE items.id = '$test_id' ";
    $run_sample = mysqli_query($con, $received_samples);
    if(mysqli_num_rows($run_sample) > 0)
    {
        while($row_sample = mysqli_fetch_array($run_sample))
        {
            // Lab Test Detail
            $token_no = $row_sample['token_no'];
            $lab_reporting_test_id = $row_sample['lab_reporting_test_id'];
            $test_name = $row_sample['item_name'];
            $test_category_id = $row_sample['test_category_id'];
            $test_category_title = $row_sample['test_category_title'];
            $lab_test_status_id = $row_sample['lab_test_status_id'];
            $lab_reporting_test_msg_if_normal = $row_sample['lab_reporting_test_msg_if_normal'];
            $lab_reporting_test_msg_if_low = $row_sample['lab_reporting_test_msg_if_low'];
            $lab_reporting_test_msg_if_high = $row_sample['lab_reporting_test_msg_if_high'];
            // Lab Test Reporting Detail
            $lab_reporting_test_time_minutes = $row_sample['lab_reporting_test_time_minutes'];
            $lab_test_unit_id = $row_sample['lab_test_unit_id'];
            $lab_reporting_test_unit = $row_sample['lab_reporting_test_unit'];
            $lab_reporting_test_normal_value = $row_sample['lab_reporting_test_normal_value'];
            $lab_reporting_test_normal_male = $row_sample['lab_reporting_test_normal_male'];
            $lab_reporting_test_normal_female = $row_sample['lab_reporting_test_normal_female'];
            $lab_reporting_test_normal_childern = $row_sample['lab_reporting_test_normal_childern'];
        }
    }
}
else
{
    header('location: logout.php');
}
?>
	<title>UPDATE TEST RECORD (<?php echo date('d-m-Y'); ?>)- <?php echo $lab_login_branch_name; ?> - LAB - <?php echo $company_trademark; ?></title>
</head>

<body class = "p-2">
    <div class = "row">
        <div class = "col">
            <h1 align = "center">LAB TEST STATUS </h1>
        </div>
    </div>
    <form method = "POST" action = "">
    <div class = "row">
        <div class = "col-md-3">
            <label>LAB TEST ID</label>
            <input type = "text" readonly name = "lab_test_id" value = "<?php echo $lab_test_id; ?>" class = "form-control" />
        </div>
        <div class = "col-md-3">
            <label>REPORT TEST ID</label>
            <input type = "text" readonly name = "lab_reporting_test_id" value = "<?php echo $lab_reporting_test_id; ?>" class = "form-control" />
        </div>
        <div class = "col-md-3">
            <label>TEST ID</label>
            <input type = "text" readonly name = "test_id" value = "<?php echo $test_id; ?>" class = "form-control" />
        </div>
        <div class = "col-md-3">
            <label>TEST NAME</label>
            <input type = "text" readonly value = "<?php echo $test_name; ?>" class = "form-control" />
        </div>
    </div>
    <div class = "row">
        <div class = "col-md-6">
            <label>TEST CATEGORY</label>
            <select name = "test_category_id" class = "form-control" required>
                
                <?php
                $select_test_categories = "SELECT * FROM `test_categories` WHERE `test_category_status` = '1' ";
                $run_test_categories = mysqli_query($con, $select_test_categories);
                if(mysqli_num_rows($run_test_categories) > 0)
                {
                    while($row_test_categories = mysqli_fetch_array($run_test_categories))
                    {
                        if($test_category_id == $row_test_categories['test_category_id'])
                        {
                            echo '<option selected value = "'.$row_test_categories['test_category_id'].'">'.$row_test_categories['test_category_title'].'</option>';
                        }
                        else
                        {
                            echo '<option value = "'.$row_test_categories['test_category_id'].'">'.$row_test_categories['test_category_title'].'</option>';
                        }
                    }
                }
                ?>
            </select>
        </div>
        <div class = "col-md-3">
            <label>LAB TEST UNITS(<?php echo $lab_test_unit_id; ?>)</label>
            <select name = "lab_test_unit_id" class = "form-control" required>
                
                <?php
                $select_test_categories = "SELECT * FROM `lab_test_units` WHERE `lab_test_unit_status` = '1' ";
                $run_test_categories = mysqli_query($con, $select_test_categories);
                if(mysqli_num_rows($run_test_categories) > 0)
                {
                    while($row_test_categories = mysqli_fetch_array($run_test_categories))
                    {
                        if($lab_test_unit_id == $row_test_categories['lab_test_unit_id'])
                        {
                            echo '<option selected value = "'.$row_test_categories['lab_test_unit_id'].'">'.$row_test_categories['lab_test_unit_value'].'</option>';
                        }
                        else
                        {
                            echo '<option value = "'.$row_test_categories['lab_test_unit_id'].'">'.$row_test_categories['lab_test_unit_value'].'</option>';
                        }
                    }
                }
                ?>
            </select>
        </div>
        <div class = "col-md-3">
            <label>REPORTING TIME (MINTUES)</label>
            <input type = "number" name = "lab_reporting_test_time_minutes" value = "<?php echo $lab_reporting_test_time_minutes; ?>" class = "form-control" />
        </div>
    </div>
    <div class = "row">
        <div class = "col-md-3">
            <label>NORMAL RANGE(DEFUALT)</label>
            <input type = "text" name = "lab_reporting_test_normal_value" value = "<?php echo $lab_reporting_test_normal_value; ?>" class = "form-control" />
        </div>
        <div class = "col-md-3">
            <label>NORMAL RANGE(MALE)</label>
            <input type = "text" name = "lab_reporting_test_normal_male" value = "<?php echo $lab_reporting_test_normal_male; ?>" class = "form-control" />
        </div>
        <div class = "col-md-3">
            <label>NORMAL RANGE(FEMALE)</label>
            <input type = "text" name = "lab_reporting_test_normal_female" value = "<?php echo $lab_reporting_test_normal_female; ?>" class = "form-control" />
        </div>
        <div class = "col-md-3">
            <label>NORMAL RANGE(CHILDERN)</label>
            <input type = "text" name = "lab_reporting_test_normal_childern" value = "<?php echo $lab_reporting_test_normal_childern; ?>" class = "form-control" />
        </div>
    </div>
    
    <div class = "row">
        <div class = "col">
            <label>TEST MSG IF NORMAL</label>
            <input type = "text" name = "lab_reporting_test_msg_if_normal" value = "<?php echo $lab_reporting_test_msg_if_normal; ?>" class = "form-control" />
        </div>
    </div>
    <div class = "row">
        <div class = "col">
            <label>TEST MSG IF LOW</label>
            <input type = "text" name = "lab_reporting_test_msg_if_low" value = "<?php echo $lab_reporting_test_msg_if_low; ?>" class = "form-control" />
        </div>
    </div>
    <div class = "row">
        <div class = "col">
            <label>TEST MSG IF HIGH</label>
            <input type = "text" name = "lab_reporting_test_msg_if_high" value = "<?php echo $lab_reporting_test_msg_if_high; ?>" class = "form-control" />
        </div>
    </div>
    
    <div class = "row">
        <div class = "col">
            <input type = "submit" value = "UPDATE TEST REPORT" name = "update_test_details" class = "btn btn-info" />
            <input type = "reset" name = "reset" class = "btn btn-danger" />
        </div>
    </div>

    </form>
</body>
</html>