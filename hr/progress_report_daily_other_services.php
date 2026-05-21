<?php
include 'includes/connect.php';
$progress_page_title = 'DAILY PROGRESS (OTHER SERVICES)';
$progress_bootstrap_opts = array(
    'print' => '../bk/print_progess_report_daily_other_services.php',
    'window_title' => 'OTHER SERVICES PROGRESS REPORT',
);
$progress_date_input = 'date';
$progress_branch_mode = 'hr_extra';
include 'includes/progress_report_form.php';
