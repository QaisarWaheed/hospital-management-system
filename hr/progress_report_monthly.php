<?php
include 'includes/connect.php';
$progress_page_title = 'MONTHLY PROGRESS';
$progress_bootstrap_opts = array(
    'print' => 'print_progress_report_monthly.php',
    'window_title' => 'MONTHLY PROGRESS REPORT',
);
$progress_date_input = 'month';
$progress_branch_mode = 'all';
include 'includes/progress_report_form.php';
