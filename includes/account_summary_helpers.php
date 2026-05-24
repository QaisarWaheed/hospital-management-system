<?php

require_once __DIR__ . '/account_report_helpers.php';

/**
 * @return array<string, float>
 */
function account_summary_empty_day()
{
    return array(
        'total_cash' => 0.0,
        'total_cash_received' => 0.0,
        'pending' => 0.0,
        'pending_receive' => 0.0,
    );
}

/**
 * Account summary per day for one branch/month (replaces ~3 queries per day).
 *
 * @return array<int, array<string, float>>
 */
function account_summary_month_by_day($con, $branch_id, $year, $month)
{
    $branch_id = (int) $branch_id;
    $bounds = account_report_month_datetime_bounds($year, $month);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);
    $byDay = array();

    $sql = "SELECT DATE(created) AS day_key,
            COALESCE(SUM(cash), 0) AS total_cash,
            COALESCE(SUM(cash_received), 0) AS total_cash_received,
            COALESCE(SUM(CASE WHEN cash > cash_received THEN cash ELSE 0 END), 0)
                - COALESCE(SUM(CASE WHEN cash > cash_received THEN cash_received ELSE 0 END), 0) AS pending,
            COALESCE(SUM(CASE WHEN cash = 0 THEN ABS(cash - cash_received) ELSE 0 END), 0) AS pending_receive
        FROM tokans
        WHERE branch_id = $branch_id
            AND status = 1
            AND created >= '$start'
            AND created < '$end'
        GROUP BY DATE(created)";

    $run = mysqli_query($con, $sql);
    if (!$run) {
        return $byDay;
    }

    while ($row = mysqli_fetch_assoc($run)) {
        $day = (int) date('j', strtotime($row['day_key']));
        $byDay[$day] = array(
            'total_cash' => (float) $row['total_cash'],
            'total_cash_received' => (float) $row['total_cash_received'],
            'pending' => (float) $row['pending'],
            'pending_receive' => (float) $row['pending_receive'],
        );
    }

    return $byDay;
}
