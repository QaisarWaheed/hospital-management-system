<?php include 'includes/connect.php';
if (!isset($_GET['s'], $_GET['e'])) {
    header('Location: logout.php');
    exit;
}
$first_month = $_GET['s'];
$second_month = $_GET['e'];
include 'includes/head.php';
?>
	<title>COMPARISION ALL BRANCHES - <?php echo $company_trademark; ?></title>
</head>
<body style = "text-transform: uppercase;">
    <div class = "row">
        <div class = "col-md-12" style = "text-transform: uppercase;text-align: center;">
            <label><h1>COMPARISION ALL BRANCHES <?php echo date_format(date_create($first_month), "F Y"); ?> & <?php echo date_format(date_create($second_month), "F Y"); ?></h1></label>
        </div>
<?php
$select_branch = "SELECT * FROM `branchs` WHERE `status` = 1 ";
$run_branch = mysqli_query($con, $select_branch);
if(mysqli_num_rows($run_branch) > 0)
{
    while($row_branch = mysqli_fetch_array($run_branch))
    {
        $comparision_branch_id = $row_branch['id'];
        $comparision_branch_name = $row_branch['name'];
        $comparision_branch_address = $row_branch['address'];
        
        // PATIENT FIRST MONTH
        $patient_first_month = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `tokans` WHERE `status` = 1 AND `branch_id` = '$comparision_branch_id' AND `created` LIKE '$first_month%' AND `tokan_type_id` <= 10 " ));
        $cons_first_month = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE  created like '$first_month%' AND status = 1) AND branch_id = '$comparision_branch_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 29) )"));

        // COLLECTION FIRST MONTH
        $select_collection = "SELECT SUM(`cash_received`) FROM `tokans` WHERE `status` = 1 AND `created` LIKE '$first_month%' AND `branch_id` = '$comparision_branch_id' ";
        $run_collection = mysqli_query($con, $select_collection);
        if(mysqli_num_rows($run_collection) == 1)
        {
            while($row_collection = mysqli_fetch_array($run_collection))
            {
                $collection_first_month = $row_collection['0'];
            }
        }
        else
        {
            $collection_first_month = 0;
        }
        
        // PROCEDURE FIRST MONTH
        $select_procedure = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `tokans` WHERE `status` = 1 AND `branch_id` = '$comparision_branch_id' AND created LIKE '$first_month%' AND id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE branch_id = $comparision_branch_id AND `created` LIKE '$first_month%' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 3)))"));

        // LAB FIRST MONTH
        $select_lab = "SELECT SUM(`cash_received`) FROM `tokans` WHERE `status` = 1 AND `branch_id` = '$comparision_branch_id' AND created LIKE '$first_month%' AND id IN(SELECT `tokan_no` FROM `item_by_doctor` WHERE branch_id = '$comparision_branch_id' AND `created` LIKE '$first_month%' AND `item_id` IN(SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN(SELECT id FROM items WHERE category_id = 2)))";
        $run_lab = mysqli_query($con, $select_lab);
        if(mysqli_num_rows($run_lab) == 1)
        {
            while($row_lab = mysqli_fetch_array($run_lab))
            {
                $lab_first_month = $row_lab['0'];
            }
        }
        else
        {
            $lab_first_month = 0;
        }
        
        // PATIENT SECOND MONTH
        $patient_second_month = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `tokans` WHERE `status` = 1 AND `branch_id` = '$comparision_branch_id' AND `created` LIKE '$second_month%' AND `tokan_type_id` <= 10 " ));
        $cons_second_month = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE  created like '$second_month%' AND status = 1) AND branch_id = '$comparision_branch_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 29) )"));
        
        // COLLECTION SECOND MONTH
        $select_collection_2 = "SELECT SUM(`cash_received`) FROM `tokans` WHERE `status` = 1 AND `created` LIKE '$second_month%' AND `branch_id` = '$comparision_branch_id' ";
        $run_collection_2 = mysqli_query($con, $select_collection_2);
        if(mysqli_num_rows($run_collection_2) == 1)
        {
            while($row_collection_2 = mysqli_fetch_array($run_collection_2))
            {
                $collection_second_month = $row_collection_2['0'];
            }
        }
        else
        {
            $collection_second_month = 0;
        }
        
        // PROCEDURE SECOND MONTH
        $select_procedure_2 = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `tokans` WHERE `status` = 1 AND `branch_id` = '$comparision_branch_id' AND created LIKE '$second_month%' AND id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE branch_id = $comparision_branch_id AND `created` LIKE '$second_month%' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 3)))"));

        // LAB SECOND MONTH
        $select_lab_2 = "SELECT SUM(`cash_received`) FROM `tokans` WHERE `status` = 1 AND `branch_id` = '$comparision_branch_id' AND created LIKE '$second_month%' AND id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE branch_id = $comparision_branch_id AND `created` LIKE '$second_month%' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 2)))";
        $run_lab_2 = mysqli_query($con, $select_lab_2);
        if(mysqli_num_rows($run_lab_2) == 1)
        {
            while($row_lab_2 = mysqli_fetch_array($run_lab_2))
            {
                $lab_second_month = $row_lab_2['0'];
            }
        }
        else
        {
            $lab_second_month = 0;
        }
        

?>
        <div class = "col-md-12">
            <div class = "panel panel-info">
                <div class = "panel-heading" style = "text-align: center;">
                    <h2><?php echo $comparision_branch_address; ?></h2>
                </div>
                <div class = "panel-body">
                    <table class = "table table-bordered">
                        <tr>
                            <td></td>
                            <th>PATIENT</th>
                            <th>LAB INCOME</th>
                            <th>PROCEDURE</th>
                            <th>COLLECTION</th>
                        </tr>
                        <tr>
                            <th><?php echo date_format(date_create($first_month), "M-y"); ?></th>
                            <th><?php echo $patient_first_month; ?> + <?php echo $cons_first_month; ?> => <?php echo intval($patient_first_month+$cons_first_month); ?></th>
                            <th><?php echo $lab_first_month; ?></th>
                            <th><?php echo $select_procedure; ?></th>
                            <th><?php echo $collection_first_month; ?></th>
                        </tr>
                        <tr>
                            <th><?php echo date_format(date_create($second_month), "M-y"); ?></th>
                            <th><?php echo $patient_second_month; ?> + <?php echo $cons_second_month; ?> => <?php echo intval($patient_second_month+$cons_second_month); ?></th>
                            <th><?php echo $lab_second_month; ?></th>
                            <th><?php echo $select_procedure_2; ?></th>
                            <th><?php echo $collection_second_month; ?></th>
                        </tr>
                        <tr>
                            <th></th>
                            <th><?php echo $patient_second_month-$patient_first_month; ?> + <?php echo $cons_second_month-$cons_first_month; ?> => <?php echo intval(($patient_second_month-$patient_first_month)+($cons_second_month-$cons_first_month)); ?></th>
                            <th><?php echo $lab_second_month-$lab_first_month; ?></th>
                            <th><?php echo $select_procedure_2-$select_procedure; ?></th>
                            <th><?php echo $collection_second_month-$collection_first_month; ?></th>
                        </tr>
</table>
                </div>
            </div>
        </div>
<?php }
}
else
{ ?>
        <div class = "col-md-12">
            <label style = "text-transform: uppercase;">PLEASE ADD BRANCH FIRST</label>
        </div>
<?php }
?>
    </div>
</body>
</html>