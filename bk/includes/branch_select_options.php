<?php

/**
 * Shared branch dropdown options for BK report forms (all active branches).
 */
function bk_branch_select_options($con, int $selectedId = 0): string
{
    require_once __DIR__ . '/../../includes/report_helpers.php';
    return summary_branch_select_html($con, $selectedId, 0, true, 'br_id');
}
