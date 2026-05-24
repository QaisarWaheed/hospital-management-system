<?php

/**
 * Inclusive month start and exclusive end (Y-m-d) for range filters.
 *
 * @return array{0: string, 1: string}
 */
function comparison_month_bounds($month)
{
    $month = substr((string) $month, 0, 7);
    $start = $month . '-01';
    $end = date('Y-m-d', strtotime('first day of next month', strtotime($start)));

    return array($start, $end);
}

/**
 * @param array<int, array<string, int|float>> $bucket
 */
function comparison_ensure_branch(array &$bucket, $branch_id)
{
    $branch_id = (int) $branch_id;
    if (!isset($bucket[$branch_id])) {
        $bucket[$branch_id] = array(
            'patients' => 0,
            'cons' => 0,
            'collection' => 0.0,
            'procedures' => 0,
            'lab' => 0.0,
        );
    }
}

/**
 * @param array<int, array<string, int|float>> $bucket
 */
function comparison_load_cons_month($con, $start, $end, array &$bucket)
{
    $s = mysqli_real_escape_string($con, $start);
    $e = mysqli_real_escape_string($con, $end);
    $sql = "SELECT branch_id, COUNT(*) AS cnt
        FROM item_by_doctor
        WHERE status = 2 AND category_id = 29
            AND created >= '$s' AND created < '$e'
        GROUP BY branch_id";
    $run = mysqli_query($con, $sql);
    if (!$run) {
        return;
    }
    while ($row = mysqli_fetch_assoc($run)) {
        $bid = (int) $row['branch_id'];
        comparison_ensure_branch($bucket, $bid);
        $bucket[$bid]['cons'] = (int) $row['cnt'];
    }
}

/**
 * Procedure count = tokans with at least one procedure line (matches legacy print_comparision).
 *
 * @param array<int, array<string, int|float>> $bucket
 */
function comparison_load_procedures_month($con, $start, $end, array &$bucket)
{
    $s = mysqli_real_escape_string($con, $start);
    $e = mysqli_real_escape_string($con, $end);
    $sql = "SELECT t.branch_id, COUNT(DISTINCT t.id) AS cnt
        FROM tokans t
        INNER JOIN item_by_doctor ibd
            ON ibd.tokan_no = t.id
            AND ibd.branch_id = t.branch_id
            AND ibd.status = 2
            AND ibd.category_id = 3
            AND ibd.created >= '$s'
            AND ibd.created < '$e'
        WHERE t.status = 1
            AND t.created >= '$s'
            AND t.created < '$e'
        GROUP BY t.branch_id";
    $run = mysqli_query($con, $sql);
    if (!$run) {
        return;
    }
    while ($row = mysqli_fetch_assoc($run)) {
        $bid = (int) $row['branch_id'];
        comparison_ensure_branch($bucket, $bid);
        $bucket[$bid]['procedures'] = (int) $row['cnt'];
    }
}

/**
 * Lab income = sum of tokans.cash_received for tokens with lab items (legacy behaviour).
 *
 * @param array<int, array<string, int|float>> $bucket
 */
function comparison_load_lab_month($con, $start, $end, array &$bucket)
{
    $s = mysqli_real_escape_string($con, $start);
    $e = mysqli_real_escape_string($con, $end);
    $sql = "SELECT t.branch_id, COALESCE(SUM(t.cash_received), 0) AS lab_sum
        FROM tokans t
        INNER JOIN (
            SELECT DISTINCT tokan_no, branch_id
            FROM item_by_doctor
            WHERE status = 2
                AND category_id = 2
                AND created >= '$s'
                AND created < '$e'
        ) lab ON lab.tokan_no = t.id AND lab.branch_id = t.branch_id
        WHERE t.status = 1
            AND t.created >= '$s'
            AND t.created < '$e'
        GROUP BY t.branch_id";
    $run = mysqli_query($con, $sql);
    if (!$run) {
        return;
    }
    while ($row = mysqli_fetch_assoc($run)) {
        $bid = (int) $row['branch_id'];
        comparison_ensure_branch($bucket, $bid);
        $bucket[$bid]['lab'] = (float) $row['lab_sum'];
    }
}

/**
 * Tokan patients + collection for both months in one scan.
 *
 * @param array<int, array<string, int|float>> $first
 * @param array<int, array<string, int|float>> $second
 */
function comparison_load_tokans_both_months($con, $m1, $m2, $range_start, $range_end, array &$first, array &$second)
{
    $m1s = mysqli_real_escape_string($con, $m1[0]);
    $m1e = mysqli_real_escape_string($con, $m1[1]);
    $m2s = mysqli_real_escape_string($con, $m2[0]);
    $m2e = mysqli_real_escape_string($con, $m2[1]);
    $rs = mysqli_real_escape_string($con, $range_start);
    $re = mysqli_real_escape_string($con, $range_end);

    $sql = "SELECT branch_id,
        COUNT(CASE WHEN created >= '$m1s' AND created < '$m1e' AND tokan_type_id <= 10 THEN 1 END) AS patients_m1,
        COUNT(CASE WHEN created >= '$m2s' AND created < '$m2e' AND tokan_type_id <= 10 THEN 1 END) AS patients_m2,
        COALESCE(SUM(CASE WHEN created >= '$m1s' AND created < '$m1e' THEN cash_received ELSE 0 END), 0) AS collection_m1,
        COALESCE(SUM(CASE WHEN created >= '$m2s' AND created < '$m2e' THEN cash_received ELSE 0 END), 0) AS collection_m2
    FROM tokans
    WHERE status = 1 AND created >= '$rs' AND created < '$re'
    GROUP BY branch_id";

    $run = mysqli_query($con, $sql);
    if (!$run) {
        return;
    }
    while ($row = mysqli_fetch_assoc($run)) {
        $bid = (int) $row['branch_id'];
        comparison_ensure_branch($first, $bid);
        comparison_ensure_branch($second, $bid);
        $first[$bid]['patients'] = (int) $row['patients_m1'];
        $second[$bid]['patients'] = (int) $row['patients_m2'];
        $first[$bid]['collection'] = (float) $row['collection_m1'];
        $second[$bid]['collection'] = (float) $row['collection_m2'];
    }
}

/**
 * Stats for two months (simple per-month queries; matches legacy comparison report).
 *
 * @return array{first: array<int, array<string, int|float>>, second: array<int, array<string, int|float>>}
 */
function comparison_two_month_stats($con, $first_month, $second_month)
{
    $m1 = comparison_month_bounds($first_month);
    $m2 = comparison_month_bounds($second_month);
    $range_start = min($m1[0], $m2[0]);
    $range_end = max($m1[1], $m2[1]);

    $first = array();
    $second = array();

    comparison_load_tokans_both_months($con, $m1, $m2, $range_start, $range_end, $first, $second);

    comparison_load_cons_month($con, $m1[0], $m1[1], $first);
    comparison_load_cons_month($con, $m2[0], $m2[1], $second);

    comparison_load_procedures_month($con, $m1[0], $m1[1], $first);
    comparison_load_procedures_month($con, $m2[0], $m2[1], $second);

    comparison_load_lab_month($con, $m1[0], $m1[1], $first);
    comparison_load_lab_month($con, $m2[0], $m2[1], $second);

    return array('first' => $first, 'second' => $second);
}

/** @deprecated Use comparison_two_month_stats */
function comparison_branch_month_stats($con, $month)
{
    $pair = comparison_two_month_stats($con, $month, $month);
    return $pair['first'];
}

function comparison_branch_stat($stats, $branch_id, $key)
{
    $branch_id = (int) $branch_id;
    if (!isset($stats[$branch_id][$key])) {
        return ($key === 'collection' || $key === 'lab') ? 0.0 : 0;
    }

    return $stats[$branch_id][$key];
}
