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
 * Stats for two months in one pass per metric (5 queries total, not 10).
 *
 * @return array{first: array<int, array<string, int|float>>, second: array<int, array<string, int|float>>}
 */
function comparison_two_month_stats($con, $first_month, $second_month)
{
    $m1 = comparison_month_bounds($first_month);
    $m2 = comparison_month_bounds($second_month);
    $range_start = min($m1[0], $m2[0]);
    $range_end = max($m1[1], $m2[1]);

    $m1s = mysqli_real_escape_string($con, $m1[0]);
    $m1e = mysqli_real_escape_string($con, $m1[1]);
    $m2s = mysqli_real_escape_string($con, $m2[0]);
    $m2e = mysqli_real_escape_string($con, $m2[1]);
    $rs = mysqli_real_escape_string($con, $range_start);
    $re = mysqli_real_escape_string($con, $range_end);

    $first = array();
    $second = array();

    $init = static function (&$bucket, $bid) {
        if (!isset($bucket[$bid])) {
            $bucket[$bid] = array(
                'patients' => 0,
                'cons' => 0,
                'collection' => 0.0,
                'procedures' => 0,
                'lab' => 0.0,
            );
        }
    };

    $tokans_sql = "SELECT branch_id,
        COUNT(CASE WHEN created >= '$m1s' AND created < '$m1e' AND tokan_type_id <= 10 THEN 1 END) AS patients_m1,
        COUNT(CASE WHEN created >= '$m2s' AND created < '$m2e' AND tokan_type_id <= 10 THEN 1 END) AS patients_m2,
        COALESCE(SUM(CASE WHEN created >= '$m1s' AND created < '$m1e' THEN cash_received ELSE 0 END), 0) AS collection_m1,
        COALESCE(SUM(CASE WHEN created >= '$m2s' AND created < '$m2e' THEN cash_received ELSE 0 END), 0) AS collection_m2
    FROM tokans
    WHERE status = 1 AND created >= '$rs' AND created < '$re'
    GROUP BY branch_id";

    $run = mysqli_query($con, $tokans_sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $bid = (int) $row['branch_id'];
            $init($first, $bid);
            $init($second, $bid);
            $first[$bid]['patients'] = (int) $row['patients_m1'];
            $second[$bid]['patients'] = (int) $row['patients_m2'];
            $first[$bid]['collection'] = (float) $row['collection_m1'];
            $second[$bid]['collection'] = (float) $row['collection_m2'];
        }
    }

    $ibd_sql = "SELECT branch_id,
        COUNT(CASE WHEN created >= '$m1s' AND created < '$m1e' AND category_id = 29 THEN 1 END) AS cons_m1,
        COUNT(CASE WHEN created >= '$m2s' AND created < '$m2e' AND category_id = 29 THEN 1 END) AS cons_m2,
        COUNT(DISTINCT CASE WHEN created >= '$m1s' AND created < '$m1e' AND category_id = 3 THEN tokan_no END) AS procedures_m1,
        COUNT(DISTINCT CASE WHEN created >= '$m2s' AND created < '$m2e' AND category_id = 3 THEN tokan_no END) AS procedures_m2
    FROM item_by_doctor
    WHERE status = 2 AND created >= '$rs' AND created < '$re'
        AND category_id IN (3, 29)
    GROUP BY branch_id";

    $run = mysqli_query($con, $ibd_sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $bid = (int) $row['branch_id'];
            $init($first, $bid);
            $init($second, $bid);
            $first[$bid]['cons'] = (int) $row['cons_m1'];
            $second[$bid]['cons'] = (int) $row['cons_m2'];
            $first[$bid]['procedures'] = (int) $row['procedures_m1'];
            $second[$bid]['procedures'] = (int) $row['procedures_m2'];
        }
    }

    $lab_sql = "SELECT branch_id,
        COALESCE(SUM(CASE WHEN created >= '$m1s' AND created < '$m1e' THEN sale_price ELSE 0 END), 0) AS lab_m1,
        COALESCE(SUM(CASE WHEN created >= '$m2s' AND created < '$m2e' THEN sale_price ELSE 0 END), 0) AS lab_m2
    FROM item_by_doctor
    WHERE status = 2 AND category_id = 2
        AND created >= '$rs' AND created < '$re'
    GROUP BY branch_id";

    $run = mysqli_query($con, $lab_sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $bid = (int) $row['branch_id'];
            $init($first, $bid);
            $init($second, $bid);
            $first[$bid]['lab'] = (float) $row['lab_m1'];
            $second[$bid]['lab'] = (float) $row['lab_m2'];
        }
    }

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
