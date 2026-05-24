<?php
include 'includes/connect.php';
require_once __DIR__ . '/../../../includes/doctor_monthly_profile_helpers.php';

$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
$role_title = '';
if ($run_roles && mysqli_num_rows($run_roles) == 1) {
    while ($row_role = mysqli_fetch_array($run_roles)) {
        $role_title = $row_role['title'];
    }
}

$date = date('Y-m');
$doctor_id = 0;
$showReport = false;
$summary = null;
$opdBreakdown = array();
$procedureRows = array();
$referralReceived = array('count' => 0, 'sum' => 0.0);
$doctor_name = '';

if (isset($_POST['progress'], $_POST['date'], $_POST['doctor_id']) && $_POST['date'] !== '' && $_POST['doctor_id'] !== '') {
    @set_time_limit(300);
    if (function_exists('ini_set')) {
        @ini_set('max_execution_time', '300');
    }
    $reportCon = ycdo_db_connect_report();
    if ($reportCon) {
        $con = $reportCon;
        $GLOBALS['con'] = $con;
    }

    $date = substr((string) $_POST['date'], 0, 7);
    $doctor_id = (int) $_POST['doctor_id'];
    $parsed = doctor_monthly_profile_parse_month($date);
    if ($parsed !== null && $doctor_id > 0) {
        $showReport = true;
        $doctor_name = get_uname_by_id($doctor_id);
        $summary = doctor_monthly_profile_summary($con, $doctor_id, (int) $branch_id, $parsed['year'], $parsed['month']);
        $opdBreakdown = doctor_monthly_profile_opd_breakdown($con, $doctor_id, (int) $branch_id, $parsed['year'], $parsed['month']);
        $procedureRows = doctor_monthly_profile_procedure_rows($con, $doctor_id, (int) $branch_id, $parsed['year'], $parsed['month']);
        $referralReceived = doctor_monthly_profile_referral_received($con, $doctor_id, $parsed['year'], $parsed['month']);
    }
} elseif (isset($_POST['date']) && $_POST['date'] !== '') {
    $date = substr((string) $_POST['date'], 0, 7);
}

include 'includes/head.php';
?>
	<title>DOCTOR MONTHLY PROFILE - <?php echo htmlspecialchars($date); ?> <?php echo htmlspecialchars($company_trademark); ?></title>
<style>
@media print {
    .no-print, .no-print * { display: none !important; }
}
</style>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo htmlspecialchars($company_name); ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke no-print">	<?php include 'left_navigation.php'; ?>	
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo htmlspecialchars($user_name); if ($is_incharge == 2) { echo ' Incharge '; } ?>(<?php echo htmlspecialchars($role_title); ?>)</h3>
    </div>
    <div class="col-md-9">
        <form method="POST">
        <div class="row no-print">
            <div class="col-md-12">
                <h2 align="center"><?php echo htmlspecialchars($branch_name); ?></h2>
            </div>
            <div class="col-md-12">
                <label>DOCTOR</label>
                <select name="doctor_id" class="form-control" required>
                    <?php
                    if (isset($_POST['doctor_id']) && $_POST['doctor_id'] !== '') {
                        echo '<option value="' . (int) $_POST['doctor_id'] . '">' . htmlspecialchars(get_uname_by_id($_POST['doctor_id'])) . '</option>';
                    }
                    echo get_doctor_option($branch_id);
                    ?>
                </select>
            </div>
            <div class="col-md-12">
                <label>MONTH</label>
                <input required type="month" value="<?php echo htmlspecialchars($date); ?>" name="date" id="date" class="form-control" />
                <input type="submit" name="progress" value="GENERATE REPORT" class="btn btn-sm btn-info" />
                <input type="reset" name="reset" value="CLEAR" class="btn btn-sm btn-danger" />
            </div>
        </div>
        </form>
<?php
if ($showReport && $summary !== null) {
    $s = $summary;
    $monthTitle = ycdo_safe_date_format($date . '-01', 'F Y', $date);
    ?>
    <table class="table" border="solid">
    <caption style="caption-side: top; text-align: center;color: black;">
        <h3>SUMMERY REPORT OF <?php echo htmlspecialchars($monthTitle); ?></h3>
    </caption>
    <thead>
        <tr>
            <th>NAME</th>
            <th>OPD</th>
            <th>CONS</th>
            <th>LAB</th>
            <th>USG</th>
            <th>SVD</th>
            <th>D&amp;C</th>
            <th>PROCEDURE</th>
            <th>ADMISSION</th>
            <th>GYNAE SYSTEM</th>
            <th>REFERRED BY</th>
            <th>REFERRED OPD</th>
            <th>COLLECTION</th>
        </tr>
    </thead>
    <tbody>
        <tr style="text-align: right;">
            <td style="text-align: left;"><?php echo htmlspecialchars($doctor_name); ?></td>
            <td><?php echo (int) $s['opds']; ?></td>
            <td><?php echo (int) $s['cons_opds'] . '(' . number_format($s['cons_opds_cash']) . ')'; ?></td>
            <td><?php echo (int) $s['labs']; ?></td>
            <td><?php echo (int) $s['usgs']; ?></td>
            <td><?php echo (int) $s['svds']; ?></td>
            <td><?php echo (int) $s['dncs']; ?></td>
            <td><?php echo (int) $s['procedures']; ?></td>
            <td><?php echo (int) $s['admissions']; ?></td>
            <td><?php echo (int) $s['gynae_system']; ?></td>
            <td><?php echo (int) $s['referred']; ?></td>
            <td><?php echo (int) $s['referred_opd']; ?></td>
            <td><?php echo number_format($s['collections']); ?></td>
        </tr>
    </tbody>
    </table>

    <div class="col-md-12">
        <table class="table">
            <tr>
                <th colspan="5"><h3 align="center">OPD TOKENS DETAIL</h3></th>
            </tr>
            <tr>
                <th>SR</th>
                <th>TOKEN TYPE</th>
                <th>RATE</th>
                <th>COUNT</th>
                <th>TOTAL</th>
            </tr>
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
            <td>' . number_format($row['total']) . '</td>
        </tr>';
    }
    echo '<tr>
        <td>' . $sr++ . '</td>
        <td>REFERRAL CHECKUP</td>
        <td></td>
        <td>' . (int) $referralReceived['count'] . '</td>
        <td>' . number_format($referralReceived['sum']) . '</td>
    </tr>
    <tr>
        <td>' . $sr . '</td>
        <td>CONS CHECKUP</td>
        <td></td>
        <td>' . (int) $s['cons_opds'] . '</td>
        <td>' . number_format($s['cons_opds_cash']) . '</td>
    </tr>
    <tr>
        <th></th><th></th><th></th>
        <th>' . ($opd_count + (int) $referralReceived['count'] + (int) $s['cons_opds']) . '</th>
        <th>' . number_format($opd_sum + $referralReceived['sum'] + $s['cons_opds_cash']) . '</th>
    </tr>';

    if ($procedureRows !== array()) {
        echo '<tr><th colspan="5"><h3 align="center">PROCEDURE TOKENS DETAIL</h3></th></tr>
        <tr>
            <th>SR</th><th>DATE</th><th>TOKEN NO</th><th>TOKEN TYPE</th><th>AMOUNT</th>
        </tr>';
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
                <td>' . number_format($row['cash']) . '</td>
            </tr>';
        }
        echo '<tr><td colspan="4"></td><td>' . number_format($procedure_sum) . '</td></tr>';
    }
?>
        </table>
    </div>
<?php
}
?>
    </div>
</div>
</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
