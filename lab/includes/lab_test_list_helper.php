<?php

/**
 * Build lab test listing SQL (approved / ready-to-print queues).
 * Avoids loading entire history — requires date range + optional branch.
 */
function lab_test_list_parse_filters($con, $default_branch_id)
{
    $branch_id = $default_branch_id;
    if (isset($_GET['selected_branch']) && $_GET['selected_branch'] !== '') {
        $branch_id = (int) $_GET['selected_branch'];
    }

    $date_to = isset($_GET['date_to']) && $_GET['date_to'] !== ''
        ? $_GET['date_to']
        : date('Y-m-d');
    $date_from = isset($_GET['date_from']) && $_GET['date_from'] !== ''
        ? $_GET['date_from']
        : date('Y-m-d', strtotime('-14 days'));

    if (strtotime($date_from) > strtotime($date_to)) {
        $tmp = $date_from;
        $date_from = $date_to;
        $date_to = $tmp;
    }

    $max_days = ($branch_id === 0) ? 7 : 62;
    $span_days = (strtotime($date_to) - strtotime($date_from)) / 86400;
    if ($span_days > $max_days) {
        $date_from = date('Y-m-d', strtotime($date_to . " -$max_days days"));
    }

    return array(
        'branch_id' => (int) $branch_id,
        'date_from' => $date_from,
        'date_to' => $date_to,
        'date_from_sql' => mysqli_real_escape_string($con, $date_from),
        'date_to_sql' => mysqli_real_escape_string($con, $date_to),
        // Queue pages should load with default dates without requiring ?search=1
        'should_run' => true,
    );
}

function lab_test_list_build_sql($filters, $status_id, $limit = 500)
{
    $status_id = (int) $status_id;
    $limit = (int) $limit;
    $branch_id = (int) $filters['branch_id'];
    $from = $filters['date_from_sql'] . ' 00:00:00';
    $to = $filters['date_to_sql'] . ' 23:59:59';

    $branch_sql = ($branch_id > 0) ? " AND tokans.branch_id = '$branch_id' " : '';

    return "SELECT
            lab_tests.lab_test_id,
            lab_tests.token_no,
            patients.name,
            items.name AS test_name,
            patients.age,
            patients.phone,
            patients.cnic,
            branchs.tag_name AS main_branch_name,
            tokans.created AS added_at,
            added.u_name AS added_by,
            lab_tests.lab_test_processed_created_at AS processed_at,
            processed.u_name AS processed_by,
            lab_tests.sample_date_time AS collected_at,
            collected.u_name AS collected_by,
            lab_tests.lab_test_conducted_created_at AS conducted_at,
            conducted.u_name AS conducted_by
        FROM lab_tests
        INNER JOIN tokans ON lab_tests.token_no = tokans.id
        INNER JOIN patients ON tokans.patient_id = patients.id
        LEFT JOIN users added ON tokans.user_id = added.id
        LEFT JOIN users collected ON lab_tests.user_id = collected.id
        LEFT JOIN users processed ON lab_tests.lab_test_processed_created_by = processed.id
        LEFT JOIN users conducted ON lab_tests.lab_test_conducted_created_by = conducted.id
        INNER JOIN items ON lab_tests.item_id = items.id
        INNER JOIN branchs ON tokans.branch_id = branchs.id
        WHERE lab_tests.lab_test_status_id = '$status_id'
        $branch_sql
        AND tokans.created >= '$from'
        AND tokans.created <= '$to'
        ORDER BY lab_tests.lab_test_id DESC
        LIMIT $limit";
}

/** Queue pages: received / in-process (status 2, 3, 4). */
function lab_test_list_build_sql_simple($filters, $status_id, $limit = 500)
{
    $status_id = (int) $status_id;
    $limit = (int) $limit;
    $branch_id = (int) $filters['branch_id'];
    $from = $filters['date_from_sql'] . ' 00:00:00';
    $to = $filters['date_to_sql'] . ' 23:59:59';
    $branch_sql = ($branch_id > 0) ? " AND tokans.branch_id = '$branch_id' " : '';
    // Use sample/collected time — not token registration date — so tests added later still appear.
    $date_expr = "COALESCE(
            NULLIF(lab_tests.sample_date_time, '0000-00-00 00:00:00'),
            NULLIF(lab_tests.lab_test_collected_created_at, '0000-00-00 00:00:00'),
            tokans.created
        )";

    return "SELECT
            lab_tests.lab_test_id,
            lab_tests.token_no,
            items.name AS test_name,
            patients.name,
            patients.age,
            patients.phone,
            patients.cnic,
            branchs.tag_name AS main_branch_name,
            tokans.created AS register_at,
            register.u_name AS register_by,
            lab_tests.sample_date_time AS collected_at,
            collected.u_name AS collected_by
        FROM lab_tests
        INNER JOIN tokans ON lab_tests.token_no = tokans.id
        INNER JOIN patients ON tokans.patient_id = patients.id
        LEFT JOIN users register ON tokans.user_id = register.id
        LEFT JOIN users collected ON lab_tests.user_id = collected.id
        INNER JOIN items ON lab_tests.item_id = items.id
        INNER JOIN branchs ON tokans.branch_id = branchs.id
        WHERE lab_tests.lab_test_status_id = '$status_id'
        $branch_sql
        AND $date_expr >= '$from'
        AND $date_expr <= '$to'
        ORDER BY lab_tests.lab_test_id DESC
        LIMIT $limit";
}

function lab_test_list_fetch($con, $filters, $status_id, $limit = 500, $variant = 'full')
{
    if (empty($filters['should_run'])) {
        return array('rows' => array(), 'truncated' => false);
    }

    $sql = ($variant === 'simple')
        ? lab_test_list_build_sql_simple($filters, $status_id, $limit + 1)
        : lab_test_list_build_sql($filters, $status_id, $limit + 1);
    $rows = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $rows[] = $row;
        }
    }

    $truncated = count($rows) > $limit;
    if ($truncated) {
        $rows = array_slice($rows, 0, $limit);
    }

    return array('rows' => $rows, 'truncated' => $truncated);
}
