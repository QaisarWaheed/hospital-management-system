<?php
include 'includes/connect.php';
$progress_page_title = 'DAILY PROGRESS';
$progress_bootstrap_opts = array(
    'print' => '../bk/print_progess_report_daily.php',
    'window_title' => 'PROGRESS REPORT',
);
$progress_date_input = 'date';
$progress_branch_mode = 'exclude_first';
include 'includes/progress_report_form.php';
