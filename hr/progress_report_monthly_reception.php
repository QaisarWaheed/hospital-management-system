<?php
include 'includes/connect.php';
$progress_page_title = 'MONTHLY PROGRESS (RECEPTION)';
$progress_bootstrap_opts = array(
    'print' => '../bk/print_progess_report_monthly_reception.php',
    'window_title' => 'PROGRESS RECEPTION REPORT',
);
$progress_date_input = 'month';
$progress_branch_mode = 'hr_extra';
include 'includes/progress_report_form.php';
