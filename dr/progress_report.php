<?php
include 'includes/connect.php';
$progress_page_title = 'DOCTOR PROGRESS REPORT';
$progress_bootstrap_opts = array(
    'print' => 'print_progess_report.php',
    'window_title' => 'PROGRESS REPORT',
);
include 'includes/progress_report_form.php';
