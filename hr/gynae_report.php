<?php
include 'includes/connect.php';
$progress_page_title = 'GYNAE REPORT';
$progress_bootstrap_opts = array(
    'print' => '../bk/print_gynae_report.php',
    'window_title' => 'PROGRESS REPORT',
    'needs_br_id' => false,
);
$progress_date_input = 'date';
$progress_hide_branch = true;
include 'includes/progress_report_form.php';
