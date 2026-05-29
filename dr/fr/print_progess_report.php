<?php
require_once __DIR__ . '/includes/connect_report.php';
require_once __DIR__ . '/../../../includes/report_helpers.php';
require_once __DIR__ . '/../../../includes/fr_summary_report_helpers.php';

@set_time_limit(300);
if (function_exists('ini_set')) {
    @ini_set('max_execution_time', '300');
}

if (!isset($_GET['date'], $_GET['br_id']) || $_GET['date'] === '') {
    http_response_code(400);
    exit('Date and branch are required.');
}

$date = substr((string) $_GET['date'], 0, 10);
$br_id = (int) $_GET['br_id'];
$branchHeader = summary_branch_header($con, $br_id, $company_name);
$dateTitle = ycdo_safe_date_format($date, 'd F Y', $date);
$doctors = fr_progress_doctors_day($con, $br_id, $date);

header('Content-Type: text/html; charset=utf-8');
echo '<html><head><meta charset="utf-8"><title>Progress Report</title></head><body><p>Loading progress report…</p>';
if (function_exists('ob_flush')) {
    @ob_flush();
}
@flush();

$s = 0;
$total_opds = 0;
$total_cons_opds = 0;
$total_lab = 0.0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PRINT PROGRESS REPORT</title>
</head>
<body onload="window.print()">
<table border="solid">
<caption>
    <h2><?php echo htmlspecialchars($company_name); ?></h2>
    <h2><?php echo htmlspecialchars($branchHeader['name']); ?></h2>
    <h3>PROGRESS DATE <?php echo htmlspecialchars($dateTitle); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>CONS</th>
            <th>LAB</th>
        </tr>
    </thead>
<tbody>
<?php
foreach ($doctors as $doc) {
    $s++;
    $opds = (int) $doc['opd'];
    $cons_opds = (int) $doc['cons'];
    $labs = (float) $doc['lab'];
    $labsDisplay = $labs > 0 ? number_format((float)($labs ?? 0)) : 'N/A';
    if ($labs > 0) {
        $total_lab += $labs;
    }
    $total_opds += $opds;
    $total_cons_opds += $cons_opds;
    echo '<tr style="text-align: right;">
        <td>' . $s . '</td>
        <td style="text-align: left;">' . htmlspecialchars($doc['name']) . '</td>
        <td>' . $opds . '</td>
        <td>' . $cons_opds . '</td>
        <td>' . $labsDisplay . '</td>
    </tr>';
}
?>
    </tbody>
    <tfoot>
        <tr style="text-align: right;">
            <th colspan="2">TOTAL</th>
            <th><?php echo $total_opds; ?></th>
            <th><?php echo $total_cons_opds; ?></th>
            <th><?php echo $total_lab > 0 ? number_format((float)($total_lab ?? 0)) : 'N/A'; ?></th>
        </tr>
    </tfoot>
</table>
</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
