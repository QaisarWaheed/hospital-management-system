<?php
include 'includes/connect.php';
include 'includes/head.php';

$role_title = '';
$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if ($run_roles && mysqli_num_rows($run_roles) == 1) {
    while ($row_role = mysqli_fetch_array($run_roles)) {
        $role_title = $row_role['title'];
    }
}

$part_files = [
    '1' => 'print_progress_report_monthly_half.php',
    '2' => 'print_progress_report_monthly_half2.php',
    '3' => 'print_progress_report_monthly_half3.php',
    '4' => 'print_progress_report_monthly_half4.php',
    '5' => 'print_progress_report_monthly_half5.php',
    '6' => 'print_progress_report_monthly_half6.php',
];

if (isset($_POST['progress'], $_POST['date'], $_POST['br_id'], $_POST['report_part'])) {
    $date = $_POST['date'];
    $br_id = (int) $_POST['br_id'];
    $part = $_POST['report_part'];
    if (isset($part_files[$part])) {
        $target = $part_files[$part];
        echo '<script>window.open("' . $target . '?date=' . urlencode($date) . '&br_id=' . $br_id . '", "MONTHLY PROGRESS REPORT", "width=3000,height=3000");</script>';
    }
}
?>
	<title>Progress Monthly (TIME) - <?php echo $company_trademark; ?></title>
</head>
<body class="background_image">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?></h1></label></div>
	<div class="col-md-3 background_whitesmoke"><?php include 'left_navigation.php'; ?>
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo htmlspecialchars($bk_name); if ($bk_is_incharge == 2) { echo ' Incharge '; } ?>(<?php echo htmlspecialchars($role_title); ?>)</h3>
    </div>
    <div class="col-md-9">
        <form method="POST" class="container">
        <div class="row">
            <div class="col">
                <label>BRANCH</label>
                <select name="br_id" class="form-control" required>
                    <option value="<?php echo (int) $bk_branch_id; ?>"><?php echo htmlspecialchars($bk_branch_address); ?></option>
                </select>
            </div>
            <div class="col">
                <label>MONTH</label>
                <input required type="month" value="<?php echo date('Y-m'); ?>" name="date" class="form-control" />
            </div>
            <div class="col">
                <label>REPORT PART (20 doctors per part)</label>
                <select name="report_part" class="form-control" required>
                    <option value="1">Part 1 (doctors 1-20)</option>
                    <option value="2">Part 2 (doctors 21-40)</option>
                    <option value="3">Part 3 (doctors 41-60)</option>
                    <option value="4">Part 4 (doctors 61-80)</option>
                    <option value="5">Part 5 (doctors 81-100)</option>
                    <option value="6">Part 6 (doctors 101+)</option>
                </select>
                <input type="submit" name="progress" value="PROGRESS" class="btn btn-sm btn-info" />
                <input type="reset" value="CLEAR" class="btn btn-sm btn-danger" />
            </div>
        </div>
        </form>
    </div>
</div>
</body>
</html>
<?php mysqli_close($con); ?>
