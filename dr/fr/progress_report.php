<?php
include 'includes/connect.php';
$progress_page_title = 'PROGRESS REPORT';
$progress_bootstrap_opts = array(
    'print' => 'print_progess_report.php',
    'window_title' => 'PROGRESS REPORT',
);
$progress_left_nav_file = __DIR__ . '/left_navigation.php';
$progress_logout_href = 'logout.php';
require_once __DIR__ . '/../includes/progress_report_form.php';
