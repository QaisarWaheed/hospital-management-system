<?php
/**
 * Standard HR progress report form shell.
 *
 * Required before include:
 *   $progress_page_title (string)
 *   $progress_bootstrap_opts (array for hr_progress_bootstrap)
 *   $progress_date_input ('date'|'month')
 * Optional:
 *   $progress_branch_mode ('all'|'hr_extra'|'hr_only'|'exclude_first') default hr_extra
 *   $progress_hide_branch (bool) hide branch selector
 */
require_once __DIR__ . '/progress_report_helper.php';

if (!isset($progress_branch_mode)) {
    $progress_branch_mode = 'hr_extra';
}
if (!isset($progress_date_input)) {
    $progress_date_input = 'date';
}

$pb = hr_progress_bootstrap($con, $hr_id, $progress_bootstrap_opts);
$role_title = $pb['role_title'];
$date_value = ($progress_date_input === 'month') ? date('Y-m') : date('Y-m-d');
?>
<?php include __DIR__ . '/head.php'; ?>
<?php echo $pb['popup_script']; ?>
	<title><?php echo htmlspecialchars($progress_page_title, ENT_QUOTES, 'UTF-8'); ?> - <?php echo $company_trademark; ?></title>
</head>
<body class="background_image">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke"><?php include __DIR__ . '/../left_navigation.php'; ?>
    	<?php hr_progress_sidebar_user_line($hr_name, $role_title); ?>
    </div>
    <div class="col-md-9">
        <form method="POST" class="container">
        <div class="row">
<?php if (empty($progress_hide_branch)) { ?>
            <div class="col">
                <label>BRANCH</label>
                <select name="br_id" class="form-control" required>
<?php
if ($progress_branch_mode === 'all') {
    hr_progress_all_branch_options($con);
} elseif ($progress_branch_mode === 'exclude_first') {
    hr_progress_branch_options_exclude_first($con);
} elseif ($progress_branch_mode === 'hr_only') {
    echo '<option value="' . (int) $hr_branch_id . '">' . htmlspecialchars($hr_branch_address, ENT_QUOTES, 'UTF-8') . '</option>';
} else {
    hr_progress_branch_options($con, $hr_branch_id, $hr_branch_address, "id != '0'");
}
?>
                </select>
            </div>
<?php } ?>
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
