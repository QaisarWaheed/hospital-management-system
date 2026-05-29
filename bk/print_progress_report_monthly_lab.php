<?php
// OPTIMIZED: all doctors loaded via progress_lab_monthly_report_maps() — 5 batch queries total (no per-doctor loops).
require_once __DIR__ . '/includes/connect_report.php';
require_once __DIR__ . '/includes/progress_report_params.php';

@set_time_limit(300);
if (function_exists('ini_set')) {
    @ini_set('max_execution_time', '300');
}

$req = progress_report_resolve_request($con);
$date = $req['date'];
$br_id = $req['br_id'];
$like = $req['like'];

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Monthly Lab Progress</title></head><body>';
if (function_exists('ob_flush')) {
    @ob_flush();
}
@flush();

$maps = progress_lab_monthly_report_maps($con, $br_id, $like);
$doctors = $maps['doctors'];
$collection_map = $maps['collection_map'];
$opd_map = $maps['opd_map'];
$cons_map = $maps['cons_map'];
$lab_map = $maps['lab_map'];

$count_opd = 0;
$count_consultant_opd = 0;
$count_lab = 0;
$count_total = 0;
$count_total_lab = 0;
$s = 0;

$dateObj = date_create($date);
$monthLabel = $dateObj ? $dateObj->format('F Y') : $date;
?>
<table border="solid">
<caption>
    <h2><?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?></h2>
    <h2><?php echo htmlspecialchars(get_branch_name_by($br_id), ENT_QUOTES, 'UTF-8'); ?></h2>
    <h3>PROGRESS MONTH <?php echo htmlspecialchars($monthLabel, ENT_QUOTES, 'UTF-8'); ?></h3>
</caption>
    <thead>
        <tr>
            <th rowspan="2">S#</th>
            <th rowspan="2">NAME</th>
            <th rowspan="2">OPD</th>
            <th rowspan="2">CONS</th>
            <th colspan="3">LAB</th>
            <th rowspan="2">USG</th>
            <th rowspan="2">COLLECTION</th>
        </tr>
        <tr>
            <th>Diag. Pt.</th>
            <th>%</th>
            <th>AMOUNT</th>
        </tr>
    </thead>
    <tbody>
<?php
if (count($doctors) > 0) {
    foreach ($doctors as $dr_id => $row_dr) {
        $dr_id = (int) $dr_id;
        $dr_name = htmlspecialchars((string) $row_dr['u_name'], ENT_QUOTES, 'UTF-8');
        $total = $collection_map[$dr_id] ?? 0;
        $opd = $opd_map[$dr_id] ?? 0;
        $consultant_opd = $cons_map[$dr_id] ?? 0;
        $lab_row = $lab_map[$dr_id] ?? array('lab_cash' => 0.0, 'lab_count' => 0);
        $lab_cash = (float) $lab_row['lab_cash'];
        $total_labs = (int) $lab_row['lab_count'];

        $count_total += $total;
        $count_opd += $opd;
        $count_consultant_opd += $consultant_opd;
        $count_lab += $lab_cash;
        $count_total_lab += $total_labs;

        $total_ops = $opd + $consultant_opd;
        if ($total_labs === 0 || $total_ops === 0) {
            $per_lab = '0';
        } else {
            $per_lab = number_format((float)(($total_labs / $total_ops) * 100 ?? 0), 2);
        }

        $s++;
        echo '
        <tr style="text-align: center;">
            <td>' . $s . '</td>
            <td style="text-align: left;">' . $dr_name . '</td>
            <td>' . $opd . '</td>
            <td>' . $consultant_opd . '</td>
            <td>' . $total_labs . '</td>
            <td>' . $per_lab . '%</td>
            <td style="text-align: right;">' . number_format((float)($lab_cash ?? 0)) . '</td>
            <td></td>
            <td style="text-align: right;">' . number_format((float)($total ?? 0)) . '</td>
        </tr>
        ';
    }
}
?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2">TOTAL</th>
            <th><?php echo $count_opd; ?></th>
            <th><?php echo $count_consultant_opd; ?></th>
            <th><?php echo $count_total_lab; ?></th>
            <th></th>
            <th style="text-align: right;"><?php echo number_format((float)($count_lab ?? 0)); ?></th>
            <th></th>
            <th style="text-align: right;"><?php echo number_format((float)($count_total ?? 0)); ?></th>
        </tr>
    </tfoot>
</table>
</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
