<?php
include 'includes/connect.php';
require_once __DIR__ . '/includes/comparison_report_helpers.php';

set_time_limit(120);

if (!isset($_GET['s'], $_GET['e'])) {
    header('Location: logout.php');
    exit;
}
$first_month = (string) $_GET['s'];
$second_month = (string) $_GET['e'];

$first_stats = comparison_branch_month_stats($con, $first_month);
$second_stats = comparison_branch_month_stats($con, $second_month);
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
$select_branch = "SELECT * FROM `branchs` WHERE `status` = 1 ORDER BY id";
$run_branch = mysqli_query($con, $select_branch);
if ($run_branch && mysqli_num_rows($run_branch) > 0) {
    while ($row_branch = mysqli_fetch_array($run_branch)) {
        $comparision_branch_id = (int) $row_branch['id'];
        $comparision_branch_address = $row_branch['address'];

        $patient_first_month = comparison_branch_stat($first_stats, $comparision_branch_id, 'patients');
        $cons_first_month = comparison_branch_stat($first_stats, $comparision_branch_id, 'cons');
        $collection_first_month = comparison_branch_stat($first_stats, $comparision_branch_id, 'collection');
        $select_procedure = comparison_branch_stat($first_stats, $comparision_branch_id, 'procedures');
        $lab_first_month = comparison_branch_stat($first_stats, $comparision_branch_id, 'lab');

        $patient_second_month = comparison_branch_stat($second_stats, $comparision_branch_id, 'patients');
        $cons_second_month = comparison_branch_stat($second_stats, $comparision_branch_id, 'cons');
        $collection_second_month = comparison_branch_stat($second_stats, $comparision_branch_id, 'collection');
        $select_procedure_2 = comparison_branch_stat($second_stats, $comparision_branch_id, 'procedures');
        $lab_second_month = comparison_branch_stat($second_stats, $comparision_branch_id, 'lab');
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
