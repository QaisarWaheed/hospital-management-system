<?php
require_once __DIR__ . '/includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';

@set_time_limit(120);

$req = progress_report_resolve_request($con);
$date = $req['date'];
$like = $req['like'];

$summary = progress_organization_daily_branch_summary($con, $like);
$branch_ids = $summary['branch_ids'];

$report_date_label = ycdo_safe_date_format($date, 'd F Y', $date);

$total_opds = 0;
$total_cons_opds = 0;
$total_usg = 0;
$total_svd = 0;
$total_dnc = 0;
$total_procedure = 0;
$total_admission = 0;
$total_gynae = 0;
$total_gynae_system = 0;
?>
<html>
<head>
    <meta charset="utf-8">
    <title>PRINT PROGRESS REPORT</title>
</head>
<body>

<table border="solid">
<caption>
    <h2><?php echo htmlspecialchars($company_name); ?></h2>
    <h3>PROGRESS DATE <?php echo htmlspecialchars($report_date_label); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>BRANCH</th>
            <th>OPD</th>
            <th>CONS</th>
            <th>LAB</th>
            <th>USG</th>
            <th>SVD</th>
            <th>D&amp;C</th>
            <th>PROCEDURE</th>
            <th>ADMISSION</th>
            <th>GYNAE TOKEN</th>
            <th>GYNAE SYSTEM</th>
        </tr>
    </thead>
<?php
if (count($branch_ids) > 0) {
    echo '<tbody>';
    $s = 0;
    foreach ($branch_ids as $branch_id) {
        $s++;
        $opds = $summary['opd'][$branch_id] ?? 0;
        $cons_opds = $summary['cons'][$branch_id] ?? 0;
        $admissions = $summary['admissions'][$branch_id] ?? 0;
        $procedures = $summary['procedures'][$branch_id] ?? 0;
        $svds = $summary['svds'][$branch_id] ?? 0;
        $dncs = $summary['dncs'][$branch_id] ?? 0;
        $usgs = $summary['usgs'][$branch_id] ?? 0;
        $gynae = $summary['gynae'][$branch_id] ?? 0;
        $gynae_system = $summary['gynae_system'][$branch_id] ?? 0;

        $total_opds += $opds;
        $total_cons_opds += $cons_opds;
        $total_admission += $admissions;
        $total_procedure += $procedures;
        $total_svd += $svds;
        $total_dnc += $dncs;
        $total_usg += $usgs;
        $total_gynae += $gynae;
        $total_gynae_system += $gynae_system;

        $branch_tag = htmlspecialchars(get_branch_tag_name_by_id($branch_id));
        echo '<tr style="text-align: right;">';
        echo '<td>' . $s . '</td>';
        echo '<td style="text-align: left;">' . $branch_tag . '</td>';
        echo '<td>' . $opds . '</td>';
        echo '<td>' . $cons_opds . '</td>';
        echo '<td>N/A</td>';
        echo '<td>' . $usgs . '</td>';
        echo '<td>' . $svds . '</td>';
        echo '<td>' . $dncs . '</td>';
        echo '<td>' . $procedures . '</td>';
        echo '<td>' . $admissions . '</td>';
        echo '<td>' . $gynae . '</td>';
        echo '<td>' . $gynae_system . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '<tfoot>
            <tr style="text-align: right;">
                <th></th>
                <th></th>
                <th>' . $total_opds . '</th>
                <th>' . $total_cons_opds . '</th>
                <th></th>
                <th>' . $total_usg . '</th>
                <th>' . $total_svd . '</th>
                <th>' . $total_dnc . '</th>
                <th>' . $total_procedure . '</th>
                <th>' . $total_admission . '</th>
                <th>' . $total_gynae . '</th>
                <th>' . $total_gynae_system . '</th>
            </tr>
        </tfoot>';
} else {
    echo '<tbody><tr><td colspan="12">No branch activity for this date.</td></tr></tbody>';
}
?>
</table>

</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
