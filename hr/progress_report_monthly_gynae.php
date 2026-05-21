<?php
include 'includes/connect.php';
$progress_page_title = 'MONTHLY PROGRESS (GYNAE)';
$progress_bootstrap_opts = array(
    'print' => '../bk/print_progess_report_monthly_gynae.php',
    'window_title' => 'PROGRESS REPORT',
);
$progress_date_input = 'month';
$progress_branch_mode = 'hr_extra';
include 'includes/progress_report_form.php';
