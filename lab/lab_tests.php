<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
if(isset($_GET['delete_lab_reporting_test_id']) && $_GET['delete_lab_reporting_test_id'] != '')
{
    $delete_lab_reporting_test_id = $_GET['delete_lab_reporting_test_id'];
    echo $delete = "UPDATE `lab_reporting_tests` SET `lab_reporting_test_status` = '2', `lab_reporting_test_deleted_by` = '$lab_user_id', `lab_reporting_test_deleted_at` = '$current_date' WHERE `lab_reporting_test_id` = '$delete_lab_reporting_test_id' ";
    if(mysqli_query($con, $delete))
    {
        // header('location: lab_tests.php?msg=success');
    }
    exit(0);
}
if(isset($_POST['save_form_data']))
{
    $item_id = $_POST['item_id'];
    $lab_reporting_test_unit = $_POST['lab_reporting_test_unit'];
    $parameter_name = $_POST['parameter_name'];
    $lab_reporting_test_normal_value = $_POST['lab_reporting_test_normal_value'];
    $lab_reporting_test_normal_male = $_POST['lab_reporting_test_normal_male'];
    $lab_reporting_test_normal_female = $_POST['lab_reporting_test_normal_female'];
    $lab_reporting_test_normal_childern = $_POST['lab_reporting_test_normal_childern'];
    $lab_reporting_test_time_minutes = $_POST['lab_reporting_test_time_minutes'];
    $lab_reporting_test_type = $_POST['lab_reporting_test_type'];
    $lab_reporting_test_msg_if_normal = $_POST['lab_reporting_test_msg_if_normal'];
    $lab_reporting_test_msg_if_low = $_POST['lab_reporting_test_msg_if_low'];
    $lab_reporting_test_msg_if_high = $_POST['lab_reporting_test_msg_if_high'];
    $insert = "INSERT INTO `lab_reporting_tests`
    (`lab_reporting_test_id`, `item_id`, `lab_reporting_test_unit`, `lab_reporting_test_normal_value`, `lab_reporting_test_type`, `lab_reporting_test_time_minutes`, `lab_reporting_test_status`, `lab_reporting_test_created`, `user_id`, `lab_reporting_test_msg_if_normal`, `lab_reporting_test_msg_if_low`, `lab_reporting_test_msg_if_high`, `lab_reporting_test_normal_male`, `lab_reporting_test_normal_female`, `lab_reporting_test_normal_childern`, `parameter_name`, `lab_test_unit_id`) 
    VALUES
    (NULL, '$item_id', '$lab_reporting_test_unit', '$lab_reporting_test_normal_value', '$lab_reporting_test_type', '$lab_reporting_test_time_minutes', '1','$current_date', '$lab_user_id', '$lab_reporting_test_msg_if_normal', '$lab_reporting_test_msg_if_low', '$lab_reporting_test_msg_if_high', '$lab_reporting_test_normal_male', '$lab_reporting_test_normal_female', '$lab_reporting_test_normal_childern', '$parameter_name', '$lab_reporting_test_unit')";
    if(mysqli_query($con, $insert))
    {
        header('location: lab_tests.php?msg=success');
    }
    exit(0);
}
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
	<!--<link rel="stylesheet" type="text/css" href="../css/nav_style.css">-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>LAB TESTS</title>
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
<body class = "background_image">
<?php include 'top_navigation.php'; ?>
<div class = "">
	<div class="">
	    <?php include 'form/add_lab_test.php'; ?>
    </div>
	<div class="">
	    <div class = "table table-responsive">
	    <table class = "table-bordered table-hover" style = "color: black;">
	        <caption style = "caption-side: top;color: black;text-align: center;"><h2>ALL LAB TESTS</h2></caption>
	        <thead>
	            <tr>
	                <th>S#</th>
	                <th>ID</th>
	                <th>TEST</th>
	                <th>PARAMETER NAME</th>
	                <th>TEST CATEGORY</th>
	                <th>UNITS</th>
	                <th>NORMAL VALUES</th>
	                <th>NORMAL MALE</th>
	                <th>NORMAL FEMALE</th>
	                <th>NORMAL CHILDERN</th>
	                <th>TEST TIME </th>
	                <!--<th>NORMAL</th>-->
	                <!--<th>LOW</th>-->
	                <!--<th>HIGH</th>-->
	                <th colspan = "2">Action</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 0;
$select = "SELECT items.id, lab_reporting_test_id, items.name, parameter_name,lab_reporting_test_msg_if_normal, lab_reporting_test_msg_if_low, lab_reporting_test_msg_if_high, lab_reporting_test_normal_male, lab_reporting_test_normal_female, lab_reporting_test_normal_childern, items.poor, items.member, items.general, lab_reporting_tests.lab_reporting_test_normal_value, lab_reporting_tests.lab_reporting_test_unit, lab_reporting_tests.lab_reporting_test_time_minutes, lab_reporting_tests.lab_reporting_test_type, `test_categories`.`test_category_title` FROM `items` INNER JOIN lab_reporting_tests ON items.id = lab_reporting_tests.item_id  INNER JOIN test_categories ON lab_reporting_tests.lab_reporting_test_type = test_categories.test_category_id WHERE lab_reporting_tests.lab_reporting_test_status = '1' AND items.category_id = '2' AND items.status = '1' AND items.id IN (SELECT item_id FROM lab_reporting_tests) ORDER BY items.name ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s++;
        $test_id = $row['id'];
?>
                <tr>
                    <td><?php echo $s; ?></td>
                    <td><?php echo $row['lab_reporting_test_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['parameter_name']; ?></td>
                    <td><?php echo $row['test_category_title']; ?></td>
                    <td><?php echo $row['lab_reporting_test_unit']; ?></td>
                    <td><?php echo $row['lab_reporting_test_normal_value']; ?></td>
                    <td><?php echo $row['lab_reporting_test_normal_male']; ?></td>
                    <td><?php echo $row['lab_reporting_test_normal_female']; ?></td>
                    <td><?php echo $row['lab_reporting_test_normal_childern']; ?></td>
                    <td><?php echo $row['lab_reporting_test_time_minutes']; ?></td>
                    <!--<td><?php echo $row['lab_reporting_test_msg_if_normal']; ?></td>-->
                    <!--<td><?php echo $row['lab_reporting_test_msg_if_low']; ?></td>-->
                    <!--<td><?php echo $row['lab_reporting_test_msg_if_high']; ?></td>-->
                    <td>
	                    <a href = "#" class = "btn btn-sm btn-success" onClick="MyWindow=window.open('update_lab_reporting_tests.php?lab_reporting_test_id=<?php echo $row['lab_reporting_test_id']; ?>','MyWindow','width=900,height=1200'); return false;">update</a>
	                    <!--<a href = "lab_tests.php?delete=<?php echo $row['lab_reporting_test_id']; ?>" class = "btn-sm btn-danger">X</a>-->
                    </td>
                    <td>
	                    <!--<a href = "#" class = "btn btn-sm btn-success" onClick="MyWindow=window.open('update_lab_reporting_tests.php?lab_reporting_test_id=<?php echo $row['lab_reporting_test_id']; ?>','MyWindow','width=900,height=1200'); return false;">update</a>-->
	                    <a onclick="if(confirm('Delete selected item?')){return true;}else{event.stopPropagation(); event.preventDefault();};" href = "lab_tests.php?delete_lab_reporting_test_id=<?php echo $row['lab_reporting_test_id']; ?>" class = "btn-sm btn-danger">X</a>
                    </td>
                </tr>
<?php
    }
}
?>
	        </tbody>
	    </table>
	    </div>
	</div>    
</div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>