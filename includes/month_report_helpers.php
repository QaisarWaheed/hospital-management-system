<?php

require_once __DIR__ . '/account_report_helpers.php';

/**
 * @return array<string, float>
 */
function month_report_empty_day()
{
    return array(
        'cash' => 0.0,
        'collection' => 0.0,
        'return_token' => 0.0,
        'received_amount' => 0.0,
        'extra_amount' => 0.0,
        'short_amount' => 0.0,
    );
}

/**
 * @param array<int, array<string, float>> $byDay
 * @param array<string, float> $values
 */
function month_report_merge_day(array &$byDay, $day, array $values)
{
    $day = (int) $day;
    if ($day < 1) {
        return;
    }
    if (!isset($byDay[$day])) {
        $byDay[$day] = month_report_empty_day();
    }
    foreach ($values as $key => $value) {
        if (array_key_exists($key, $byDay[$day])) {
            $byDay[$day][$key] = (float) $value;
        }
    }
}

/**
 * FR month report: tokans + login summary per day (replaces per-day query loop).
 *
 * @return array<int, array<string, float>>
 */
function month_report_month_by_day($con, $branch_id, $year, $month)
{
    $branch_id = (int) $branch_id;
    $bounds = account_report_month_datetime_bounds($year, $month);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);
    $byDay = array();

    $tokansSql = "SELECT DATE(created) AS day_key,
            COALESCE(SUM(CASE WHEN status = 1 THEN cash_received ELSE 0 END), 0) AS collection,
            COALESCE(SUM(CASE WHEN status = 1 THEN cash ELSE 0 END), 0) AS cash,
            COALESCE(SUM(CASE WHEN status = 3 THEN cash_received ELSE 0 END), 0) AS return_token
        FROM tokans
        WHERE branch_id = $branch_id
            AND created >= '$start' AND created < '$end'
            AND status IN (1, 3)
        GROUP BY DATE(created)";
    $run = mysqli_query($con, $tokansSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            month_report_merge_day($byDay, (int) date('j', strtotime($row['day_key'])), array(
                'collection' => (float) $row['collection'],
                'cash' => (float) $row['cash'],
                'return_token' => (float) $row['return_token'],
            ));
        }
    }

    $loginSql = "SELECT DATE(ld.login_at) AS day_key,
            COALESCE(SUM(sd.received_amount), 0) AS received_amount,
            COALESCE(SUM(sd.extra_amount), 0) AS extra_amount,
            COALESCE(SUM(sd.short_amount), 0) AS short_amount
        FROM summary_details sd
        INNER JOIN logins_detail ld ON ld.id = sd.login_id
        WHERE ld.branch_id = $branch_id
            AND ld.status = '2'
            AND ld.login_at >= '$start' AND ld.login_at < '$end'
        GROUP BY DATE(ld.login_at)";
    $run = mysqli_query($con, $loginSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            month_report_merge_day($byDay, (int) date('j', strtotime($row['day_key'])), array(
                'received_amount' => (float) $row['received_amount'],
                'extra_amount' => (float) $row['extra_amount'],
                'short_amount' => (float) $row['short_amount'],
            ));
        }
    }

    return $byDay;
}
