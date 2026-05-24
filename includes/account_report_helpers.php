<?php

/** Master item ids — USG */
function account_report_usg_item_ids()
{
    return '476, 477, 478, 1411, 1435';
}

/** Master item ids — Gynae */
function account_report_gynae_item_ids()
{
    return '483, 1159, 1321, 1414';
}

/** Master item ids — Admission */
function account_report_admission_item_ids()
{
    return '444, 448, 452, 456, 460, 945';
}

/** Master item ids — Minor procedures (category 3 subset) */
function account_report_minor_procedure_item_ids()
{
    return '434, 435, 436, 437, 853, 864, 867, 868, 869, 870, 871, 872, 873, 874, 875, 876, 877, 878, 879, 880, 881, 882, 883, 884, 885, 886, 887, 888, 889, 890, 891, 892, 893, 899, 907, 908, 909, 910, 911, 912, 913, 914';
}

/**
 * @return array{start: string, end: string}
 */
function account_report_month_datetime_bounds($year, $month)
{
    $month = sprintf('%02d', (int) $month);
    $year = (int) $year;
    $start = $year . '-' . $month . '-01 00:00:00';
    $end = date('Y-m-d H:i:s', strtotime($start . ' +1 month'));

    return array('start' => $start, 'end' => $end);
}

/**
 * @return array<string, int|float>
 */
function account_report_empty_day()
{
    return array(
        'collection' => 0.0,
        'poor' => 0,
        'general' => 0,
        'private' => 0,
        'urgent' => 0,
        'consultant' => 0,
        'minor_procedure' => 0,
        'procedure' => 0,
        'usg' => 0,
        'gynae' => 0,
        'admission' => 0,
    );
}

/**
 * @param array<int, array<string, int|float>> $byDay
 * @param array<string, int|float> $values
 */
function account_report_merge_day(array &$byDay, $day, array $values)
{
    $day = (int) $day;
    if ($day < 1) {
        return;
    }
    if (!isset($byDay[$day])) {
        $byDay[$day] = account_report_empty_day();
    }
    foreach ($values as $key => $value) {
        $byDay[$day][$key] = $value;
    }
}

/**
 * Daily accounts report stats for one branch/month (replaces per-day query loops).
 *
 * @return array<int, array<string, int|float>>
 */
function account_report_month_by_day($con, $branch_id, $year, $month)
{
    $branch_id = (int) $branch_id;
    $bounds = account_report_month_datetime_bounds($year, $month);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);
    $byDay = array();

    $tokansSql = "SELECT DATE(created) AS day_key,
            COALESCE(SUM(cash_received), 0) AS collection,
            SUM(CASE WHEN tokan_type_id = 1 THEN 1 ELSE 0 END) AS poor,
            SUM(CASE WHEN tokan_type_id = 2 THEN 1 ELSE 0 END) AS general,
            SUM(CASE WHEN tokan_type_id = 3 THEN 1 ELSE 0 END) AS private,
            SUM(CASE WHEN tokan_type_id >= 4 AND tokan_type_id <= 10 THEN 1 ELSE 0 END) AS urgent
        FROM tokans
        WHERE branch_id = $branch_id AND status = 1
            AND created >= '$start' AND created < '$end'
        GROUP BY DATE(created)";
    $run = mysqli_query($con, $tokansSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            account_report_merge_day($byDay, (int) date('j', strtotime($row['day_key'])), array(
                'collection' => (float) $row['collection'],
                'poor' => (int) $row['poor'],
                'general' => (int) $row['general'],
                'private' => (int) $row['private'],
                'urgent' => (int) $row['urgent'],
            ));
        }
    }

    $usgIds = account_report_usg_item_ids();
    $gynaeIds = account_report_gynae_item_ids();
    $admissionIds = account_report_admission_item_ids();
    $ibdSql = "SELECT DATE(ibd.created) AS day_key,
            SUM(CASE WHEN irb.item_id IN ($usgIds) THEN 1 ELSE 0 END) AS usg,
            SUM(CASE WHEN irb.item_id IN ($gynaeIds) THEN 1 ELSE 0 END) AS gynae,
            SUM(CASE WHEN irb.item_id IN ($admissionIds) THEN 1 ELSE 0 END) AS admission
        FROM item_by_doctor ibd
        INNER JOIN item_register_to_branches irb
            ON irb.id = ibd.item_id AND irb.branch_id = ibd.branch_id
        WHERE ibd.branch_id = $branch_id AND ibd.status = 2
            AND ibd.created >= '$start' AND ibd.created < '$end'
        GROUP BY DATE(ibd.created)";
    $run = mysqli_query($con, $ibdSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            account_report_merge_day($byDay, (int) date('j', strtotime($row['day_key'])), array(
                'usg' => (int) $row['usg'],
                'gynae' => (int) $row['gynae'],
                'admission' => (int) $row['admission'],
            ));
        }
    }

    $minorIds = account_report_minor_procedure_item_ids();
    $procSql = "SELECT DATE(t.created) AS day_key,
            COUNT(DISTINCT CASE WHEN ibd.category_id = 29 THEN t.id END) AS consultant,
            COUNT(DISTINCT CASE WHEN ibd.category_id = 3 AND irb.item_id IN ($minorIds) THEN t.id END) AS minor_procedure,
            COUNT(DISTINCT CASE WHEN ibd.category_id = 3 AND (irb.item_id IS NULL OR irb.item_id NOT IN ($minorIds)) THEN t.id END) AS procedure
        FROM tokans t
        INNER JOIN item_by_doctor ibd
            ON ibd.tokan_no = t.id
            AND ibd.branch_id = t.branch_id
            AND ibd.status = 2
            AND ibd.created >= '$start'
            AND ibd.created < '$end'
        LEFT JOIN item_register_to_branches irb
            ON irb.id = ibd.item_id AND irb.branch_id = ibd.branch_id
        WHERE t.branch_id = $branch_id
            AND t.status = 1
            AND t.created >= '$start'
            AND t.created < '$end'
        GROUP BY DATE(t.created)";
    $run = mysqli_query($con, $procSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            account_report_merge_day($byDay, (int) date('j', strtotime($row['day_key'])), array(
                'consultant' => (int) $row['consultant'],
                'minor_procedure' => (int) $row['minor_procedure'],
                'procedure' => (int) $row['procedure'],
            ));
        }
    }

    return $byDay;
}

/**
 * @return array<string, int|float>
 */
function accounts_monthly_empty_day()
{
    return array(
        'collection' => 0.0,
        'poor' => 0,
        'general' => 0,
        'private' => 0,
        'urgent' => 0,
        'consultant' => 0,
        'procedure' => 0.0,
        'medicine' => 0.0,
        'lab' => 0.0,
    );
}

/**
 * FR accounts monthly report (procedure/medicine/lab as cash_received sums).
 *
 * @return array<int, array<string, int|float>>
 */
function accounts_monthly_report_month_by_day($con, $branch_id, $year, $month)
{
    $byDay = account_report_month_by_day($con, $branch_id, $year, $month);
    $branch_id = (int) $branch_id;
    $bounds = account_report_month_datetime_bounds($year, $month);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);

    $procSql = "SELECT DATE(t.created) AS day_key,
            COALESCE(SUM(t.cash_received), 0) AS procedure_sum
        FROM tokans t
        WHERE t.branch_id = $branch_id AND t.status = 1
            AND t.created >= '$start' AND t.created < '$end'
            AND EXISTS (
                SELECT 1 FROM item_by_doctor ibd
                INNER JOIN item_register_to_branches irb ON irb.id = ibd.item_id
                INNER JOIN items i ON i.id = irb.item_id AND i.category_id = 3
                WHERE ibd.tokan_no = t.id AND ibd.branch_id = t.branch_id AND ibd.status = 2
            )
        GROUP BY DATE(t.created)";
    $run = mysqli_query($con, $procSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $day = (int) date('j', strtotime($row['day_key']));
            account_report_merge_day($byDay, $day, array('procedure' => (float) $row['procedure_sum']));
        }
    }

    $medSql = "SELECT DATE(t.created) AS day_key,
            COALESCE(SUM(t.cash_received), 0) AS medicine_sum
        FROM tokans t
        WHERE t.branch_id = $branch_id AND t.status = 1
            AND t.created >= '$start' AND t.created < '$end'
            AND EXISTS (
                SELECT 1 FROM item_by_doctor ibd
                INNER JOIN item_register_to_branches irb ON irb.id = ibd.item_id
                INNER JOIN items i ON i.id = irb.item_id
                    AND i.category_id NOT IN (2, 3, 8, 17, 20, 28)
                WHERE ibd.tokan_no = t.id AND ibd.branch_id = t.branch_id AND ibd.status = 2
            )
        GROUP BY DATE(t.created)";
    $run = mysqli_query($con, $medSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $day = (int) date('j', strtotime($row['day_key']));
            account_report_merge_day($byDay, $day, array('medicine' => (float) $row['medicine_sum']));
        }
    }

    $labSql = "SELECT DATE(t.created) AS day_key,
            COALESCE(SUM(t.cash_received), 0) AS lab_sum
        FROM tokans t
        WHERE t.branch_id = $branch_id AND t.status = 1
            AND t.created >= '$start' AND t.created < '$end'
            AND EXISTS (
                SELECT 1 FROM item_by_doctor ibd
                INNER JOIN item_register_to_branches irb ON irb.id = ibd.item_id
                INNER JOIN items i ON i.id = irb.item_id AND i.category_id = 2
                WHERE ibd.tokan_no = t.id AND ibd.branch_id = t.branch_id AND ibd.status = 2
            )
        GROUP BY DATE(t.created)";
    $run = mysqli_query($con, $labSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $day = (int) date('j', strtotime($row['day_key']));
            account_report_merge_day($byDay, $day, array('lab' => (float) $row['lab_sum']));
        }
    }

    $consSql = "SELECT DATE(t.created) AS day_key, COUNT(DISTINCT t.id) AS consultant
        FROM tokans t
        WHERE t.branch_id = $branch_id AND t.status = 1
            AND t.created >= '$start' AND t.created < '$end'
            AND EXISTS (
                SELECT 1 FROM item_by_doctor ibd
                INNER JOIN item_register_to_branches irb ON irb.id = ibd.item_id
                WHERE ibd.tokan_no = t.id AND ibd.branch_id = t.branch_id AND ibd.status = 2
                    AND irb.item_id = 489
            )
        GROUP BY DATE(t.created)";
    $run = mysqli_query($con, $consSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $day = (int) date('j', strtotime($row['day_key']));
            account_report_merge_day($byDay, $day, array('consultant' => (int) $row['consultant']));
        }
    }

    return $byDay;
}
