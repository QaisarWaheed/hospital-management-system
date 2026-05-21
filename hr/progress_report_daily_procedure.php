<?php
include 'includes/connect.php';
$progress_page_title = 'DAILY PROGRESS (OPD & PROCEDURE)';
$progress_bootstrap_opts = array(
    'print' => '../bk/print_progess_report_daily_procedure.php',
    'window_title' => 'PROCEDURE PROGRESS REPORT',
);
$progress_date_input = 'date';
$progress_branch_mode = 'hr_extra';
include 'includes/progress_report_form.php';
