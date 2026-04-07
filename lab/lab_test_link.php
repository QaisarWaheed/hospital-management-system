<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
if(isset($_POST['new_item_id']) && $_POST['item_id'] != '' && $_POST['new_item_id'] != '')
{
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
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
<div class = "row">
	<div class="col-md-6">
	    <div class = "table table-responsive">
	    <table class = "table-bordered table-hover" style = "color: black;">
	        <caption style = "caption-side: top;color: black;text-align: center;"><h2>E1 TESTS</h2></caption>
	        <thead>
	            <tr>
	                <th>S#</th>
	                <th> ID</th>
	                <th> NAME</th>
	                <th> PRICE</th>
	                <th> TOTAL</th>
	                <th> E2</th>
	                <th> E3</th>
	                <th> ALL</th>
	                <th>ACTION</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 0;
$select = "SELECT distinct items.id, items.name, items.general, COUNT(lab_reporting_tests.lab_reporting_test_id) AS total_parameters FROM `items` INNER JOIN lab_reporting_tests ON items.id = lab_reporting_tests.item_id WHERE items.category_id = '2' AND items.name LIKE 'E1%' AND items.status = '1' GROUP BY items.id ORDER BY `items`.`name` ASC ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s++;
        $test_id = $row['id'];
?>
                <form method = "POST">
                <tr>
                    <td><?php echo $s; ?></td>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['general']; ?></td>
                    <td><?php echo $row['total_parameters']; ?></td>
                    <td>
                        <select name = "br_e2">
                            <option value = "0">NO NEED</option>
                            <?php
                            $select_br_e2 = "SELECT distinct items.id, items.name, items.general, COUNT(lab_reporting_tests.lab_reporting_test_id) AS total_parameters FROM `items` INNER JOIN lab_reporting_tests ON items.id = lab_reporting_tests.item_id WHERE items.category_id = '2' AND items.name LIKE 'E2%' AND items.status = '1' GROUP BY items.id ORDER BY `items`.`name` ASC ";
                            $run_br_e2 = mysqli_query($con, $select_br_e2);
                            if(mysqli_num_rows($run_br_e2) > 0)
                            {
                                while($row_br_e2 = mysqli_fetch_array($run_br_e2))
                                {
                                    echo '<option value = "'.$row_br_e2['id'].'">'.$row_br_e2['name'].'</option>';
                                }
                            }
                            
                            ?>
                        </select>
                    </td>                    
                    <td>
                        <select name = "br_e3">
                            <option value = "0">NO NEED</option>
                            <?php
                            $select_br_e3 = "SELECT distinct items.id, items.name, items.general, COUNT(lab_reporting_tests.lab_reporting_test_id) AS total_parameters FROM `items` INNER JOIN lab_reporting_tests ON items.id = lab_reporting_tests.item_id WHERE items.category_id = '2' AND items.name LIKE 'E3%' AND items.status = '1' GROUP BY items.id ORDER BY `items`.`name` ASC ";
                            $run_br_e3 = mysqli_query($con, $select_br_e3);
                            if(mysqli_num_rows($run_br_e3) > 0)
                            {
                                while($row_br_e3 = mysqli_fetch_array($run_br_e3))
                                {
                                    echo '<option value = "'.$row_br_e3['id'].'">'.$row_br_e3['name'].'</option>';
                                }
                            }
                            
                            ?>
                        </select>
                    </td>
                    <td>
                        <select name = "br_all">
                            <option value = "0">NO NEED</option>
                            <?php
                            $select_br_all = "SELECT distinct items.id, items.name, items.general, COUNT(lab_reporting_tests.lab_reporting_test_id) AS total_parameters FROM `items` INNER JOIN lab_reporting_tests ON items.id = lab_reporting_tests.item_id WHERE items.category_id = '2' AND items.name NOT LIKE 'E1%' AND items.name NOT LIKE 'E2%' AND items.name NOT LIKE 'E3%' AND items.status = '1' GROUP BY items.id ORDER BY `items`.`name` ASC ";
                            $run_br_all = mysqli_query($con, $select_br_all);
                            if(mysqli_num_rows($run_br_all) > 0)
                            {
                                while($row_br_all = mysqli_fetch_array($run_br_all))
                                {
                                    echo '<option value = "'.$row_br_all['id'].'">'.$row_br_all['name'].'</option>';
                                }
                            }
                            
                            ?>
                        </select>
                    </td>
                    <td>
                            <input type = "hidden" value = "<?php echo $row['id']; ?>" name = "item_id" />
                            <input type = "submit" value = "UPDATE/ ADD" class = "btn btn-success btn-sm" name = "new_item_id" />
                    </td>
                </tr>
                </form>
<?php
    }
}
?>
	        </tbody>
	    </table>
	    </div>
	</div>    
	<div class="col-md-2">
	    <div class = "table table-responsive">
	    <table class = "table-bordered table-hover" style = "color: black;">
	        <caption style = "caption-side: top;color: black;text-align: center;"><h2>E2 TESTS</h2></caption>
	        <thead>
	            <tr>
	                <th>S#</th>
	                <th>TEST ID</th>
	                <th>TEST NAME</th>
	                <th>TEST PRICE</th>
	                <th>TOTAL PARAMETRS</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 0;
$select = "SELECT distinct items.id, items.name, items.general, COUNT(lab_reporting_tests.lab_reporting_test_id) AS total_parameters FROM `items` INNER JOIN lab_reporting_tests ON items.id = lab_reporting_tests.item_id WHERE items.category_id = '2' AND items.name LIKE 'E2%' AND items.status = '1' GROUP BY items.id ORDER BY `items`.`name` ASC ";
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
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['general']; ?></td>
                    <td><?php echo $row['total_parameters']; ?></td>
                </tr>
<?php
    }
}
?>
	        </tbody>
	    </table>
	    </div>
	</div>    

	<div class="col-md-2">
	    <div class = "table table-responsive">
	    <table class = "table-bordered table-hover" style = "color: black;">
	        <caption style = "caption-side: top;color: black;text-align: center;"><h2>E3 TESTS</h2></caption>
	        <thead>
	            <tr>
	                <th>S#</th>
	                <th>TEST ID</th>
	                <th>TEST NAME</th>
	                <th>TEST PRICE</th>
	                <th>TOTAL PARAMETRS</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 0;
$select = "SELECT distinct items.id, items.name, items.general, COUNT(lab_reporting_tests.lab_reporting_test_id) AS total_parameters FROM `items` INNER JOIN lab_reporting_tests ON items.id = lab_reporting_tests.item_id WHERE items.category_id = '2' AND items.name LIKE 'E3%' AND items.status = '1' GROUP BY items.id ORDER BY `items`.`name` ASC ";
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
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['general']; ?></td>
                    <td><?php echo $row['total_parameters']; ?></td>
                </tr>
<?php
    }
}
?>
	        </tbody>
	    </table>
	    </div>
	</div>    

	<div class="col-md-2">
	    <div class = "table table-responsive">
	    <table class = "table-bordered table-hover" style = "color: black;">
	        <caption style = "caption-side: top;color: black;text-align: center;"><h2>ALL OTHER BRANCHES</h2></caption>
	        <thead>
	            <tr>
	                <th>S#</th>
	                <th>TEST ID</th>
	                <th>TEST NAME</th>
	                <th>TEST PRICE</th>
	                <th>TOTAL PARAMETRS</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 0;
$select = "SELECT distinct items.id, items.name, items.general, COUNT(lab_reporting_tests.lab_reporting_test_id) AS total_parameters FROM `items` INNER JOIN lab_reporting_tests ON items.id = lab_reporting_tests.item_id WHERE items.category_id = '2' AND items.name NOT LIKE 'E1%' AND items.name NOT LIKE 'E2%' AND items.name NOT LIKE 'E3%' AND items.status = '1' GROUP BY items.id ORDER BY `items`.`name` ASC ";
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
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['general']; ?></td>
                    <td><?php echo $row['total_parameters']; ?></td>
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