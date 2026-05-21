<?php
include 'includes/connect.php';
$progress_page_title = 'MONTHLY PROGRESS (PHARMACY)';
$progress_bootstrap_opts = array(
    'print' => '../bk/print_progess_report_monthly_pharmacy.php',
    'window_title' => 'PROGRESS PHARMACY REPORT',
);
$progress_date_input = 'month';
$progress_branch_mode = 'hr_extra';
include 'includes/progress_report_form.php';
