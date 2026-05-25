<?php
require_once __DIR__ . '/includes/connect_report.php';
require_once __DIR__ . '/includes/comparison_report_helpers.php';

@set_time_limit(300);
if (function_exists('ini_set')) {
    @ini_set('max_execution_time', '300');
}

if (!isset($_GET['s'], $_GET['e'])) {
    http_response_code(400);
    exit('Month range required.');
}

$first_month = substr((string) $_GET['s'], 0, 7);
$second_month = substr((string) $_GET['e'], 0, 7);

header('Content-Type: text/html; charset=utf-8');
echo '<html><head><meta charset="utf-8"><title>Comparison Report</title></head><body><p>Loading comparison…</p>';
if (function_exists('ob_flush')) {
    @ob_flush();
}
@flush();

$pair = comparison_two_month_stats($con, $first_month, $second_month);
$first_stats = $pair['first'];
$second_stats = $pair['second'];

$branch_ids = array_unique(array_merge(array_keys($first_stats), array_keys($second_stats)));
sort($branch_ids, SORT_NUMERIC);

$branches = array();
if (count($branch_ids) > 0) {
    $id_list = implode(',', array_map('intval', $branch_ids));
    $run_branch = mysqli_query($con, "SELECT id, address FROM branchs WHERE id IN ($id_list) ORDER BY id");
    if ($run_branch) {
        while ($row = mysqli_fetch_array($run_branch)) {
            $branches[] = $row;
        }
    }
}

$month1_label = ycdo_safe_date_format($first_month . '-01', 'M-y', $first_month);
$month2_label = ycdo_safe_date_format($second_month . '-01', 'M-y', $second_month);
?>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
<div style="text-transform: uppercase;">
    <div class="row">
        <div class="col-md-12" style="text-align: center;">
            <h1>Comparison All Branches — <?php echo htmlspecialchars($month1_label); ?> &amp; <?php echo htmlspecialchars($month2_label); ?></h1>
        </div>
<?php
if (count($branches) > 0) {
    foreach ($branches as $row_branch) {
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
                            <th><?php echo htmlspecialchars($month1_label); ?></th>
                            <th><?php echo $patient_first_month; ?> + <?php echo $cons_first_month; ?> => <?php echo (int) ($patient_first_month + $cons_first_month); ?></th>
                            <th><?php echo $lab_first_month; ?></th>
                            <th><?php echo $select_procedure; ?></th>
                            <th><?php echo $collection_first_month; ?></th>
                        </tr>
                        <tr>
                            <th><?php echo htmlspecialchars($month2_label); ?></th>
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
            <label>No activity for the selected months.</label>
        </div>
<?php } ?>
    </div>
</div>
</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
