<?php
include 'includes/connect.php';
include 'includes/config.php';
include 'includes/head.php';

$lab_report_page_title = 'APPROVED REPORT TEST';
$lab_report_status_id = 5;
$lab_report_action_script = 'lab_test_report_view.php';
include 'includes/lab_test_report_list_page.php';
