<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
include 'includes/head.php'; 
if(isset($_POST['save']))
{
    $item_id = $_POST['item_id'];
    $lab_reporting_test_unit = $_POST['lab_reporting_test_unit'];
    $lab_reporting_test_normal_value = $_POST['lab_reporting_test_normal_value'];
    $lab_reporting_test_time_minutes = $_POST['lab_reporting_test_time_minutes'];
    $lab_reporting_test_type = $_POST['lab_reporting_test_type'];
    $insert = "INSERT INTO `lab_reporting_tests`
    (`lab_reporting_test_id`, `item_id`, `lab_reporting_test_unit`, `lab_reporting_test_normal_value`, `lab_reporting_test_type`, `lab_reporting_test_time_minutes`, `lab_reporting_test_status`, `lab_reporting_test_created`, `user_id`) 
    VALUES
    (NULL, '$item_id', '$lab_reporting_test_unit', '$lab_reporting_test_normal_value', '$lab_reporting_test_type', '$lab_reporting_test_time_minutes', '1','$current_date', '$lab_user_id')";
    if(mysqli_query($con, $insert))
    {
        header('location: lab_tests.php');
    }
    exit(0);
}
?>
	<title>ALL LAB TESTS - <?php echo $company_trademark; ?></title>
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
	    <table class = "table table-bordered table-hover" style = "color: black;">
	        <caption style = "caption-side: top;color: black;text-align: center;"><h2>ALL LAB TESTS</h2></caption>
	        <thead>
                <form action = "lab_tests.php" method = "POST">
                <tr>
	                <th colspan = "2">
                        <input list="items" name="item_id" id="item_id" class = "form-control text-danger" required>
                        <datalist id="items">
    	                <?php
    	                $select = "SELECT id, name FROM items WHERE category_id = '2' AND status = '1' AND id NOT IN (SELECT item_id FROM lab_reporting_tests) ";
    	                $run = mysqli_query($con, $select);
    	                if(mysqli_num_rows($run) > 0)
    	                {
    	                    while($row = mysqli_fetch_array($run))
    	                    {
        	                    echo '<option value = "'.$row['id'].'">'.$row['name'].'</option>';
    	                    }
    	                }
    	                else
    	                    echo '<option value = ""> NO DATA FOUND</option>';
    	                ?>
	                    </datalist>
	                </th>
                    <td>
                        <select name = "lab_reporting_test_type" class = "form-control">
                        <?php
    	                $select = "SELECT * FROM `test_categories` WHERE `test_category_status` = '1' ";
    	                $run = mysqli_query($con, $select);
    	                if(mysqli_num_rows($run) > 0)
    	                {
    	                    while($row = mysqli_fetch_array($run))
    	                    {
        	                    echo '<option value = "'.$row['test_category_id'].'">'.$row['test_category_title'].'</option>';
    	                    }
    	                }
                        ?>
                        </select>
                    </td>
                    <td>
                        <select name = "lab_reporting_test_unit" class = "form-control">
                            <option value = ""></option>
                            <option value = "%">%</option>
                            <option value = "nmol/l">NMOL/L</option>
                            <option value = "pmol/l">PMOL/L</option>
                            <option value = "mlu/ml">mLU/ML</option>
                            <option value = "sec">SEC</option>
                            <option value = "pg/ml">PG/ML</option>
                            <option value = "ng/ml">NG/ML</option>
                            <option value = "ng/dl">NG/DL</option>
                            <option value = "mg/ml">MG/ML</option>
                            <option value = "iu/ml">IU/ML</option>
                            <option value = "g/dl">G/DL</option>
                            <option value = "mg/dl">MG/DL</option>
                            <option value = "u/l">U/L</option>
                            <option value = "u/ml">U/ML</option>
                            <option value = "&micro;iu/ml">&micro;IU/ML</option>
                            <option value = "min">MIN</option>
                            <option value = "sec">SEC</option>
                            <option value = "mm/hr">MM/HR</option>
                        </select>
                    </td>
                    <td>
                        <textarea rows = "1" required name = "lab_reporting_test_normal_value" class = "form-control" id = "lab_reporting_test_normal_value"></textarea>
                    </td>
                    <td>
                        <input type = "number" placeholder = "ENTER TEST TIME IN MINUTIES" min = "0" class = "form-control" name = "lab_reporting_test_time_minutes" id = "lab_reporting_test_time_minutes" />
                    </td>
                    <td colspan = "3" style = "min-width: 100%; ">
                        <input style = "min-width: 100%; " type = "submit" name = "save" value = "SAVE DATA" class = "btn btn-sm btn-primary" />
                    </td>
                </tr>
                </form>
	            <tr>
	                <th>S#</th>
	                <th>NAME</th>
	                <th>TEST CATEGORY</th>
	                <th>UNITS</th>
	                <th>NORMAL VALUES</th>
	                <th>TEST TIME </th>
	                <th>POOR</th>
	                <th>MEMBER</th>
	                <th>GENERAL</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 0;
$select = "SELECT items.id, items.name, items.poor, items.member, items.general, lab_reporting_tests.lab_reporting_test_normal_value, lab_reporting_tests.lab_reporting_test_unit, lab_reporting_tests.lab_reporting_test_time_minutes, lab_reporting_tests.lab_reporting_test_type, `test_categories`.`test_category_title` FROM `items` INNER JOIN lab_reporting_tests ON items.id = lab_reporting_tests.item_id  INNER JOIN test_categories ON lab_reporting_tests.lab_reporting_test_type = test_categories.test_category_id WHERE items.category_id = '2' AND items.status = '1' AND items.id IN (SELECT item_id FROM lab_reporting_tests)  ";
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
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['test_category_title']; ?></td>
                    <td><?php echo $row['lab_reporting_test_unit']; ?></td>
                    <td><?php echo $row['lab_reporting_test_normal_value']; ?></td>
                    <td><?php echo $row['lab_reporting_test_time_minutes']; ?></td>
                    <td><?php echo $row['poor']; ?></td>
                    <td><?php echo $row['member']; ?></td>
                    <td><?php echo $row['general']; ?></td>
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