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
?>
	<title>Progress Monthly (Doctors) - <?php echo $company_trademark; ?></title>
</head>
<body class="background_image">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?></h1></label></div>
	<div class="col-md-3 background_whitesmoke"><?php include 'left_navigation.php'; ?>
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo htmlspecialchars($bk_name); if ($bk_is_incharge == 2) { echo ' Incharge '; } ?>(<?php echo htmlspecialchars($role_title); ?>)</h3>
    </div>
    <div class="col-md-9">
        <form action="print_progess_report_doctor.php" method="POST" class="container" target="_blank">
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
