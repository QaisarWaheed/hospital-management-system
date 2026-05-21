<?php
/**
 * Doctor / front-desk progress report form shell.
 * Requires: $progress_page_title, $progress_bootstrap_opts
 * Optional: $progress_date_input ('date'|'month'), $progress_left_nav_file (absolute path to nav include)
 */
require_once __DIR__ . '/progress_report_helper.php';

$pb = dr_progress_bootstrap($con, $progress_bootstrap_opts);
$role_title = $pb['role_title'];
$session = $pb['session'];
if (!$session) {
    $logout = isset($progress_logout_href) ? $progress_logout_href : 'logout.php';
    header('Location: ' . $logout);
    exit;
}

if (!isset($progress_date_input)) {
    $progress_date_input = 'date';
}
if (!isset($progress_left_nav_file)) {
    $progress_left_nav_file = __DIR__ . '/../left_navigation.php';
}

$date_value = ($progress_date_input === 'month') ? date('Y-m') : date('Y-m-d');
?>
<?php include __DIR__ . '/head.php'; ?>
<?php echo $pb['popup_script']; ?>
	<title><?php echo htmlspecialchars($progress_page_title, ENT_QUOTES, 'UTF-8'); ?> - <?php echo $company_trademark; ?></title>
</head>
<body class="background_image">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke"><?php include $progress_left_nav_file; ?>
    	<?php dr_progress_sidebar_user_line($session, $role_title); ?>
    </div>
    <div class="col-md-9">
        <form method="POST" class="container">
        <div class="row">
            <div class="col">
                <label>BRANCH</label>
                <select name="br_id" class="form-control" required>
                    <option value="<?php echo (int) $session['branch_id']; ?>"><?php echo htmlspecialchars($session['branch_address'], ENT_QUOTES, 'UTF-8'); ?></option>
                </select>
            </div>
            <div class="col">
                <label>DATE</label>
                <input required type="<?php echo $progress_date_input === 'month' ? 'month' : 'date'; ?>" value="<?php echo $date_value; ?>" name="date" class="form-control" />
                <input type="submit" name="progress" value="PROGRESS" class="btn btn-sm btn-info" />
                <input type="reset" name="reset" value="CLEAR" class="btn btn-sm btn-danger" />
            </div>
        </div>
        </form>
    </div>
</div>
</body>
</html>
<?php mysqli_close($con); ?>
