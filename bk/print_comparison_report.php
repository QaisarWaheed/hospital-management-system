<?php
include 'includes/connect.php';
if (!isset($_GET['s'], $_GET['e'])) {
    header('Location: logout.php');
    exit;
}
$first_month = $_GET['s'];
$second_month = $_GET['e'];
?>
<html>
<head>
	<title>Comparison Report - <?php echo htmlspecialchars($company_trademark); ?></title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
</head>
<body style="text-transform: uppercase;">
    <div class="row">
        <div class="col-md-12" style="text-align: center;">
            <h1>Comparison All Branches — <?php echo ycdo_safe_date_format($first_month . '-01', 'F Y', $first_month); ?> &amp; <?php echo ycdo_safe_date_format($second_month . '-01', 'F Y', $second_month); ?></h1>
        </div>
<?php
$select_branch = "SELECT * FROM `branchs` WHERE `status` = 1 ";
$run_branch = mysqli_query($con, $select_branch);
if ($run_branch && mysqli_num_rows($run_branch) > 0) {
    while ($row_branch = mysqli_fetch_array($run_branch)) {
        $comparision_branch_id = $row_branch['id'];
        $comparision_branch_address = $row_branch['address'];

        $patient_first_month = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `tokans` WHERE `status` = 1 AND `branch_id` = '$comparision_branch_id' AND `created` LIKE '$first_month%' AND `tokan_type_id` <= 10 "));
        $cons_first_month = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$first_month%' AND status = 1) AND branch_id = '$comparision_branch_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 29) )"));

        $collection_first_month = 0;
        $run_collection = mysqli_query($con, "SELECT SUM(`cash_received`) FROM `tokans` WHERE `status` = 1 AND `created` LIKE '$first_month%' AND `branch_id` = '$comparision_branch_id' ");
        if ($run_collection && mysqli_num_rows($run_collection) == 1) {
            $row_collection = mysqli_fetch_array($run_collection);
            $collection_first_month = $row_collection[0];
        }

        $select_procedure = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `tokans` WHERE `status` = 1 AND `branch_id` = '$comparision_branch_id' AND created LIKE '$first_month%' AND id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE branch_id = $comparision_branch_id AND `created` LIKE '$first_month%' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 3)))"));

        $lab_first_month = 0;
        $run_lab = mysqli_query($con, "SELECT SUM(`cash_received`) FROM `tokans` WHERE `status` = 1 AND `branch_id` = '$comparision_branch_id' AND created LIKE '$first_month%' AND id IN(SELECT `tokan_no` FROM `item_by_doctor` WHERE branch_id = '$comparision_branch_id' AND `created` LIKE '$first_month%' AND `item_id` IN(SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN(SELECT id FROM items WHERE category_id = 2)))");
        if ($run_lab && mysqli_num_rows($run_lab) == 1) {
            $row_lab = mysqli_fetch_array($run_lab);
            $lab_first_month = $row_lab[0];
        }

        $patient_second_month = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `tokans` WHERE `status` = 1 AND `branch_id` = '$comparision_branch_id' AND `created` LIKE '$second_month%' AND `tokan_type_id` <= 10 "));
        $cons_second_month = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$second_month%' AND status = 1) AND branch_id = '$comparision_branch_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 29) )"));

        $collection_second_month = 0;
        $run_collection_2 = mysqli_query($con, "SELECT SUM(`cash_received`) FROM `tokans` WHERE `status` = 1 AND `created` LIKE '$second_month%' AND `branch_id` = '$comparision_branch_id' ");
        if ($run_collection_2 && mysqli_num_rows($run_collection_2) == 1) {
            $row_collection_2 = mysqli_fetch_array($run_collection_2);
            $collection_second_month = $row_collection_2[0];
        }

        $select_procedure_2 = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `tokans` WHERE `status` = 1 AND `branch_id` = '$comparision_branch_id' AND created LIKE '$second_month%' AND id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE branch_id = $comparision_branch_id AND `created` LIKE '$second_month%' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 3)))"));

        $lab_second_month = 0;
        $run_lab_2 = mysqli_query($con, "SELECT SUM(`cash_received`) FROM `tokans` WHERE `status` = 1 AND `branch_id` = '$comparision_branch_id' AND created LIKE '$second_month%' AND id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE branch_id = $comparision_branch_id AND `created` LIKE '$second_month%' AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 2)))");
        if ($run_lab_2 && mysqli_num_rows($run_lab_2) == 1) {
            $row_lab_2 = mysqli_fetch_array($run_lab_2);
            $lab_second_month = $row_lab_2[0];
        }
?>
        <div class="col-md-12">
            <div class="panel panel-info">
                <div class="panel-heading" style="text-align: center;">
                    <h2><?php echo htmlspecialchars($comparision_branch_address); ?></h2>
                </div>
                <div class="panel-body">
                    <table class="table table-bordered">
                        <tr>
                            <td></td>
                            <th>PATIENT</th>
                            <th>LAB INCOME</th>
                            <th>PROCEDURE</th>
                            <th>COLLECTION</th>
                        </tr>
                        <tr>
                            <th><?php echo ycdo_safe_date_format($first_month . '-01', 'M-y', $first_month); ?></th>
                            <th><?php echo $patient_first_month; ?> + <?php echo $cons_first_month; ?> => <?php echo (int) ($patient_first_month + $cons_first_month); ?></th>
                            <th><?php echo $lab_first_month; ?></th>
                            <th><?php echo $select_procedure; ?></th>
                            <th><?php echo $collection_first_month; ?></th>
                        </tr>
                        <tr>
                            <th><?php echo ycdo_safe_date_format($second_month . '-01', 'M-y', $second_month); ?></th>
                            <th><?php echo $patient_second_month; ?> + <?php echo $cons_second_month; ?> => <?php echo (int) ($patient_second_month + $cons_second_month); ?></th>
                            <th><?php echo $lab_second_month; ?></th>
                            <th><?php echo $select_procedure_2; ?></th>
                            <th><?php echo $collection_second_month; ?></th>
                        </tr>
                        <tr>
                            <th>DIFFERENCE</th>
                            <th><?php echo $patient_second_month - $patient_first_month; ?> + <?php echo $cons_second_month - $cons_first_month; ?> => <?php echo (int) (($patient_second_month - $patient_first_month) + ($cons_second_month - $cons_first_month)); ?></th>
                            <th><?php echo $lab_second_month - $lab_first_month; ?></th>
                            <th><?php echo $select_procedure_2 - $select_procedure; ?></th>
                            <th><?php echo $collection_second_month - $collection_first_month; ?></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
<?php
    }
} else {
?>
        <div class="col-md-12">
            <label>PLEASE ADD BRANCH FIRST</label>
        </div>
<?php } ?>
    </div>
</body>
</html>
<?php mysqli_close($con); ?>
