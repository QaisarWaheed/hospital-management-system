<?php
require_once __DIR__ . '/includes/connect_report.php';
require_once __DIR__ . '/../../../includes/report_helpers.php';
require_once __DIR__ . '/../../../includes/doctor_monthly_profile_helpers.php';

@set_time_limit(300);
if (function_exists('ini_set')) {
    @ini_set('max_execution_time', '300');
}

if (!isset($_GET['doctor_id'], $_GET['date']) || $_GET['date'] === '') {
    http_response_code(400);
    exit('Doctor and month are required.');
}

$doctor_id = (int) $_GET['doctor_id'];
$br_id = (int) ($_GET['br_id'] ?? $branch_id);
$date = substr((string) $_GET['date'], 0, 7);
$parsed = doctor_monthly_profile_parse_month($date);
if ($parsed === null || $doctor_id < 1) {
    http_response_code(400);
    exit('Invalid doctor or month.');
}

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Doctor Monthly Profile</title></head><body><p>Generating report…</p>';
if (function_exists('ob_flush')) {
    @ob_flush();
}
@flush();

$doctor_name = doctor_monthly_doctor_name($con, $doctor_id);
$summary = doctor_monthly_profile_summary($con, $doctor_id, $br_id, $parsed['year'], $parsed['month']);
$opdBreakdown = doctor_monthly_profile_opd_breakdown($con, $doctor_id, $br_id, $parsed['year'], $parsed['month']);
$referralReceived = doctor_monthly_profile_referral_received($con, $doctor_id, $parsed['year'], $parsed['month']);
$procedureRows = doctor_monthly_profile_procedure_rows($con, $doctor_id, $br_id, $parsed['year'], $parsed['month']);

$branchHeader = summary_branch_header($con, $br_id, $company_name);
$monthTitle = ycdo_safe_date_format($date . '-01', 'F Y', $date);
$s = $summary;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Doctor Monthly Profile - <?php echo htmlspecialchars($doctor_name); ?></title>
    <style>
    * { font-size: 14px; }
    @media print { .no-print { display: none !important; } }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #333; padding: 4px; }
    </style>
</head>
<body onload="window.print()">
<p class="no-print"><a href="doctor_monthly_profile.php">← Back</a></p>
<h2 style="text-align:center;"><?php echo htmlspecialchars($branchHeader['name']); ?></h2>
<h3 style="text-align:center;">SUMMERY REPORT OF <?php echo htmlspecialchars($monthTitle); ?></h3>
<h4 style="text-align:center;"><?php echo htmlspecialchars($doctor_name); ?></h4>

<table>
    <thead>
        <tr>
            <th>NAME</th><th>OPD</th><th>CONS</th><th>LAB</th><th>USG</th><th>SVD</th><th>D&amp;C</th>
            <th>PROCEDURE</th><th>ADMISSION</th><th>GYNAE SYSTEM</th><th>REFERRED BY</th><th>REFERRED OPD</th><th>COLLECTION</th>
        </tr>
    </thead>
    <tbody>
        <tr style="text-align:right;">
            <td style="text-align:left;"><?php echo htmlspecialchars($doctor_name); ?></td>
            <td><?php echo (int) $s['opds']; ?></td>
            <td><?php echo (int) $s['cons_opds'] . ' (' . number_format((float)($s['cons_opds_cash'] ?? 0)) . ')'; ?></td>
            <td><?php echo (int) $s['labs']; ?></td>
            <td><?php echo (int) $s['usgs']; ?></td>
            <td><?php echo (int) $s['svds']; ?></td>
            <td><?php echo (int) $s['dncs']; ?></td>
            <td><?php echo (int) $s['procedures']; ?></td>
            <td><?php echo (int) $s['admissions']; ?></td>
            <td><?php echo (int) $s['gynae_system']; ?></td>
            <td><?php echo (int) $s['referred']; ?></td>
            <td><?php echo (int) $s['referred_opd']; ?></td>
            <td><?php echo number_format((float)($s['collections'] ?? 0)); ?></td>
        </tr>
    </tbody>
</table>

<table style="margin-top:1em;">
    <tr><th colspan="5"><h3 align="center">OPD TOKENS DETAIL</h3></th></tr>
    <tr><th>SR</th><th>TOKEN TYPE</th><th>RATE</th><th>COUNT</th><th>TOTAL</th></tr>
<?php
$sr = 1;
$opd_count = 0;
$opd_sum = 0.0;
foreach ($opdBreakdown as $row) {
    $opd_count += $row['count'];
    $opd_sum += $row['total'];
    echo '<tr>
        <td>' . $sr++ . '</td>
        <td>' . htmlspecialchars($row['title']) . '</td>
        <td>' . (int) $row['rate'] . '</td>
        <td>' . (int) $row['count'] . '</td>
        <td>' . number_format((float)($row['total'] ?? 0)) . '</td>
    </tr>';
}
echo '<tr>
    <td>' . $sr++ . '</td><td>REFERRAL CHECKUP</td><td></td>
    <td>' . (int) $referralReceived['count'] . '</td>
    <td>' . number_format((float)($referralReceived['sum'] ?? 0)) . '</td>
</tr>
<tr>
    <td>' . $sr . '</td><td>CONS CHECKUP</td><td></td>
    <td>' . (int) $s['cons_opds'] . '</td>
    <td>' . number_format((float)($s['cons_opds_cash'] ?? 0)) . '</td>
</tr>
<tr>
    <th colspan="3"></th>
    <th>' . ($opd_count + (int) $referralReceived['count'] + (int) $s['cons_opds']) . '</th>
    <th>' . number_format((float)($opd_sum + $referralReceived['sum'] + $s['cons_opds_cash'] ?? 0)) . '</th>
</tr>';

if ($procedureRows !== array()) {
    echo '<tr><th colspan="5"><h3 align="center">PROCEDURE TOKENS DETAIL</h3></th></tr>
    <tr><th>SR</th><th>DATE</th><th>TOKEN NO</th><th>TOKEN TYPE</th><th>AMOUNT</th></tr>';
    $sr_procedure = 0;
    $procedure_sum = 0.0;
    foreach ($procedureRows as $row) {
        $sr_procedure++;
        $procedure_sum += $row['cash'];
        echo '<tr>
            <td>' . $sr_procedure . '</td>
            <td>' . htmlspecialchars(ycdo_safe_date_format($row['created'], 'd-m-Y', '')) . '</td>
            <td>' . (int) $row['token_no'] . '</td>
            <td>' . htmlspecialchars($row['title']) . '</td>
            <td>' . number_format((float)($row['cash'] ?? 0)) . '</td>
        </tr>';
    }
    echo '<tr><td colspan="4"></td><td>' . number_format((float)($procedure_sum ?? 0)) . '</td></tr>';
}
?>
</table>
</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
