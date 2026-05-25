<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
include 'includes/connect.php';
$progress_page_title = 'GYNAE REPORT';
$progress_bootstrap_opts = array(
    'print' => 'print_gynae_report.php',
    'window_title' => 'GYNAE REPORT',
    'needs_br_id' => false,
);
$progress_date_input = 'date';
$progress_hide_branch = true;
include 'includes/progress_report_form.php';
