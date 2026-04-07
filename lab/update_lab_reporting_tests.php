<?php 
include 'includes/connect.php'; 
include 'includes/config.php'; 
include 'includes/head.php'; 
if(isset($_POST['update_lab_reporting_test']) && $_POST['update_lab_reporting_test'] != '')
{
    // echo '<pre>';
    // print_r($_POST);
    // echo '</pre>';
    $lab_reporting_test_id = $_POST['lab_reporting_test_id'];
    $lab_reporting_test_normal_value = $_POST['lab_reporting_test_normal_value'];
    $lab_reporting_test_normal_male = $_POST['lab_reporting_test_normal_male'];
    $lab_reporting_test_normal_female = $_POST['lab_reporting_test_normal_female'];
    $lab_reporting_test_normal_childern = $_POST['lab_reporting_test_normal_childern'];
    $lab_reporting_test_type = $_POST['lab_reporting_test_type'];
    $lab_test_unit_id = $_POST['lab_test_unit_id'];
    $parameter_detail = $_POST['parameter_detail'];
    $update = "UPDATE `lab_reporting_tests` SET `lab_reporting_test_unit` = '$lab_test_unit_id', `lab_test_unit_id` = '$lab_test_unit_id', `lab_reporting_test_normal_value` = '$lab_reporting_test_normal_value', `lab_reporting_test_normal_male` = '$lab_reporting_test_normal_male', `lab_reporting_test_normal_female` = '$lab_reporting_test_normal_female', `lab_reporting_test_normal_childern` = '$lab_reporting_test_normal_childern', `lab_reporting_test_type` = '$lab_reporting_test_type', `parameter_detail` = '$parameter_detail', `lab_reporting_test_updated_by` = '$lab_user_id', `lab_reporting_test_updated_at` = '$current_date' WHERE `lab_reporting_test_id` = '$lab_reporting_test_id' ";
    if(mysqli_query($con, $update))
    {
        echo "<script type='text/javascript'>alert('NOTE:DATA UPDATED SUCCESSFULLY...');</script>";
        echo "<script type='text/javascript'>sleep(2);</script>";
        echo '<script type="text/javascript">
        
        window.opener.location.reload(true);
        window.close();</script>';        
    }
    else
    {
        $error = $con->error;
        echo "<script type='text/javascript'>alert('$error');</script>";
        echo '<script type="text/javascript">
        window.opener.location.reload(true);
        window.close();</script>';  
    }
    exit(0);
}
elseif(isset($_GET['lab_reporting_test_id']) && $_GET['lab_reporting_test_id'] != '')
{
    $lab_reporting_test_id = $_GET['lab_reporting_test_id'];
    $received_samples = "SELECT `lab_reporting_test_id`, items.name AS test_name, categories.name AS cat_name, `lab_reporting_test_normal_value`, `lab_reporting_test_normal_male`, `lab_reporting_test_normal_female`, `lab_reporting_test_normal_childern`, `lab_reporting_test_time_minutes`, `parameter_detail`, `lab_reporting_test_type`, `lab_test_unit_id` FROM `lab_reporting_tests` INNER JOIN items ON lab_reporting_tests.item_id = items.id INNER JOIN categories ON items.category_id = categories.id WHERE `lab_reporting_test_id` = '$lab_reporting_test_id' ";
    $run_sample = mysqli_query($con, $received_samples);
    if(mysqli_num_rows($run_sample) > 0)
    {
        while($row_sample = mysqli_fetch_array($run_sample))
        {
            $lab_reporting_test_id = $row_sample['lab_reporting_test_id'];
            $test_name = $row_sample['test_name'];
            $cat_name = $row_sample['cat_name'];
            $lab_reporting_test_type = $row_sample['lab_reporting_test_type'];
            $lab_test_unit_id = $row_sample['lab_test_unit_id'];
            $lab_reporting_test_normal_value = $row_sample['lab_reporting_test_normal_value'];
            $lab_reporting_test_normal_male = $row_sample['lab_reporting_test_normal_male'];
            $lab_reporting_test_normal_female = $row_sample['lab_reporting_test_normal_female'];
            $lab_reporting_test_normal_childern = $row_sample['lab_reporting_test_normal_childern'];
            $lab_reporting_test_time_minutes = $row_sample['lab_reporting_test_time_minutes'];
            $parameter_detail = $row_sample['parameter_detail'];
        }
    }
}
else
{
    header('location: logout.php');
    exit(0);
}
?>
	<title>UPDATE LAB REPORTING TEST (<?php echo date('d-m-Y'); ?>)- <?php echo $lab_login_branch_name; ?> - LAB - <?php echo $company_trademark; ?></title>
</head>

<body class = "p-2">
    <form method = "POST" action = "update_lab_reporting_tests.php">
        <div class = "row">
            <div class = "col">
                <h1 align = "center">UPDATE LAB REPORTING TEST </h1>
            </div>
        </div>
        <div class = "row">
            <div class = "col-md-4">
                <label>REPORTING TEST ID</label>
                <input type = "text" readonly value = "<?php echo $lab_reporting_test_id; ?>" class = "form-control" />
                <input type = "hidden" name = "lab_reporting_test_id" value = "<?php echo $lab_reporting_test_id; ?>" class = "form-control" />
            </div>
            <div class = "col-md-2">
                <label>CATEGORY</label>
                <input type = "text" readonly value = "<?php echo $cat_name; ?>" class = "form-control" />
            </div>
            <div class = "col-md-6">
                <label>TEST NAME</label>
                <input type = "text" readonly value = "<?php echo $test_name; ?>" class = "form-control" />
            </div>
            
            <div class = "col-md-6">
                <label>RESULT NORMAL VALUE(GENRAL)</label>
                <input type = "text" value = "<?php echo $lab_reporting_test_normal_value; ?>" name = "lab_reporting_test_normal_value" class = "form-control" />
            </div>
            <div class = "col-md-6">
                <label>RESULT NORMAL VALUE(MALE)</label>
                <input type = "text" value = "<?php echo $lab_reporting_test_normal_male; ?>" name = "lab_reporting_test_normal_male" class = "form-control" />
            </div>
            <div class = "col-md-6">
                <label>RESULT NORMAL VALUE(FEMALE)</label>
                <input type = "text" value = "<?php echo $lab_reporting_test_normal_female; ?>" name = "lab_reporting_test_normal_female" class = "form-control" />
            </div>
            <div class = "col-md-6">
                <label>RESULT NORMAL VALUE(CHILD)</label>
                <input type = "text" value = "<?php echo $lab_reporting_test_normal_childern; ?>" name = "lab_reporting_test_normal_childern" class = "form-control" />
            </div>
            
            <div class = "col-md-6">
                <label>TEST CATEGORY</label>
                <select name = "lab_reporting_test_type" class = "form-control">
                    <?php
                    $categories = "SELECT * FROM `test_categories` WHERE `test_category_status` = '1'";
                    $run_categories = mysqli_query($con, $categories);
                    if(mysqli_num_rows($run_categories) > 0)
                    {
                        while($row_categories = mysqli_fetch_array($run_categories))
                        {
                            if($row_categories['test_category_id'] == $lab_reporting_test_type)
                            {
                                echo '<option SELECTED value = "'.$row_categories['test_category_id'].'">'.$row_categories['test_category_title'].'</option>';
                            }
                            else
                            {
                                echo '<option value = "'.$row_categories['test_category_id'].'">'.$row_categories['test_category_title'].'</option>';
                            }
                        }
                    }
                    else
                    {
                        echo '<option value = "">ADD CATEGORIES</option>';
                    } ?>
                </select>
            </div>
            <div class = "col-md-6">
                <label>TEST UNIT</label>
                <select name = "lab_test_unit_id" class = "form-control">
                    <?php
                    $categories = "SELECT `lab_test_unit_id`, `lab_test_unit_value`, `lab_test_unit_tag_value` FROM `lab_test_units` WHERE `lab_test_unit_status` = '1' ";
                    $run_categories = mysqli_query($con, $categories);
                    if(mysqli_num_rows($run_categories) > 0)
                    {
                        while($row_categories = mysqli_fetch_array($run_categories))
                        {
                            if($row_categories['lab_test_unit_id'] == $lab_test_unit_id)
                            {
                                echo '<option SELECTED value = "'.$row_categories['lab_test_unit_id'].'">'.$row_categories['lab_test_unit_value'].' - '.$row_categories['lab_test_unit_tag_value'].'</option>';
                            }
                            else
                            {
                                echo '<option value = "'.$row_categories['lab_test_unit_id'].'">'.$row_categories['lab_test_unit_value'].' - '.$row_categories['lab_test_unit_tag_value'].'</option>';
                            }
                        }
                    }
                    else
                    {
                        echo '<option value = "">ADD CATEGORIES</option>';
                    } ?>
                </select>
            </div>
            <div class = "col-md-12">
                <label>PARAMETER DETAILS </label>
                <textarea name = "parameter_detail" class = "form-control" rows = "1"><?php echo $parameter_detail; ?></textarea>
            </div>
            <div class = "col-md-12">
                <input type = "submit" value = "UPDATE" name = "update_lab_reporting_test" class = "btn-success btn-sm" />
            </div>
        </div>
    </form>
</body>
</html>