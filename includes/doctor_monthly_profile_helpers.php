<?php

require_once __DIR__ . '/account_report_helpers.php';

function doctor_monthly_svd_item_ids()
{
    return '472, 1118, 1313, 1577';
}

function doctor_monthly_dnc_item_ids()
{
    return '473, 1119, 1314, 1578';
}

function doctor_monthly_admission_item_ids()
{
    return '444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301, 1579, 1580, 1741, 1742, 1743, 1744';
}

function doctor_monthly_usg_item_ids()
{
    return '476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435';
}

function doctor_monthly_procedure_exclude_item_ids()
{
    return '473, 1119, 1314, 1578, 472, 1118, 1313, 1577';
}

/**
 * @return array{start: string, end: string}
 */
function doctor_monthly_bounds($con, $year, $month)
{
    $bounds = account_report_month_datetime_bounds($year, $month);

    return array(
        'start' => mysqli_real_escape_string($con, $bounds['start']),
        'end' => mysqli_real_escape_string($con, $bounds['end']),
    );
}

/**
 * @return array{year: int, month: string}|null
 */
function doctor_monthly_doctor_name($con, $doctor_id)
{
    $doctor_id = (int) $doctor_id;
    $run = mysqli_query($con, "SELECT u_name FROM users WHERE id = $doctor_id LIMIT 1");
    if ($run && ($row = mysqli_fetch_assoc($run)) && !empty($row['u_name'])) {
        return (string) $row['u_name'];
    }

    return 'Unknown';
}

function doctor_monthly_profile_parse_month($monthInput)
{
    $monthInput = substr((string) $monthInput, 0, 7);
    if (!preg_match('/^\d{4}-\d{2}$/', $monthInput)) {
        return null;
    }

    return array(
        'year' => (int) substr($monthInput, 0, 4),
        'month' => substr($monthInput, 5, 2),
    );
}

/**
 * One scan of item_by_doctor for lab/SVD/DNC/procedure/admission counts.
 *
 * @return array{labs: int, svds: int, dncs: int, procedures: int, admissions: int}
 */
function doctor_monthly_ibd_category_counts($con, $doctor_id, $branch_id, $start, $end)
{
    $doctor_id = (int) $doctor_id;
    $branch_id = (int) $branch_id;
    $svdIds = doctor_monthly_svd_item_ids();
    $dncIds = doctor_monthly_dnc_item_ids();
    $admIds = doctor_monthly_admission_item_ids();
    $procEx = doctor_monthly_procedure_exclude_item_ids();

    $empty = array('labs' => 0, 'svds' => 0, 'dncs' => 0, 'procedures' => 0, 'admissions' => 0);
    $sql = "SELECT
            COUNT(DISTINCT CASE WHEN i.category_id = 2 THEN ibd.tokan_no END) AS labs,
            COUNT(DISTINCT CASE WHEN irb.item_id IN ($svdIds) THEN ibd.tokan_no END) AS svds,
            COUNT(DISTINCT CASE WHEN irb.item_id IN ($dncIds) THEN ibd.tokan_no END) AS dncs,
            COUNT(DISTINCT CASE WHEN i.category_id = 3 AND irb.item_id NOT IN ($procEx) THEN ibd.tokan_no END) AS procedures,
            COUNT(DISTINCT CASE WHEN irb.item_id IN ($admIds) THEN ibd.tokan_no END) AS admissions
        FROM item_by_doctor ibd
        INNER JOIN item_register_to_branches irb ON irb.id = ibd.item_id
        INNER JOIN items i ON i.id = irb.item_id
        WHERE ibd.doctor_id = $doctor_id
            AND ibd.branch_id = $branch_id
            AND ibd.status = 2
            AND ibd.created >= '$start' AND ibd.created < '$end'";

    $run = mysqli_query($con, $sql);
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return array(
            'labs' => (int) $row['labs'],
            'svds' => (int) $row['svds'],
            'dncs' => (int) $row['dncs'],
            'procedures' => (int) $row['procedures'],
            'admissions' => (int) $row['admissions'],
        );
    }

    return $empty;
}

/**
 * @return array<string, int|float>
 */
function doctor_monthly_profile_summary($con, $doctor_id, $branch_id, $year, $month)
{
    $doctor_id = (int) $doctor_id;
    $branch_id = (int) $branch_id;
    $bounds = doctor_monthly_bounds($con, $year, $month);
    $start = $bounds['start'];
    $end = $bounds['end'];

    $stats = array(
        'opds' => 0,
        'collections' => 0.0,
        'cons_opds' => 0,
        'cons_opds_cash' => 0.0,
        'svds' => 0,
        'dncs' => 0,
        'procedures' => 0,
        'labs' => 0,
        'admissions' => 0,
        'referred' => 0,
        'referred_opd' => 0,
        'usgs' => 0,
        'gynae_system' => 0,
    );

    $tokanSql = "SELECT
            COUNT(CASE WHEN tokan_type_id < 100 THEN 1 END) AS opds,
            COALESCE(SUM(cash), 0) AS collections
        FROM tokans
        WHERE status = 1 AND doctor_id = $doctor_id AND branch_id = $branch_id
            AND created >= '$start' AND created < '$end'";
    $run = mysqli_query($con, $tokanSql);
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $stats['opds'] = (int) $row['opds'];
        $stats['collections'] = (float) $row['collections'];
    }

    $consSql = "SELECT COUNT(*) AS cnt, COALESCE(SUM(cash_per_token), 0) AS cash_sum FROM (
            SELECT ibd.tokan_no, MAX(t.cash) AS cash_per_token
            FROM item_by_doctor ibd
            INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
            INNER JOIN item_register_to_branches irb ON irb.id = ibd.item_id
            INNER JOIN items i ON i.id = irb.item_id AND i.category_id = 29
            WHERE ibd.doctor_id = $doctor_id AND ibd.branch_id = $branch_id AND ibd.status = 2
                AND ibd.created >= '$start' AND ibd.created < '$end'
            GROUP BY ibd.tokan_no
        ) cons_tokens";
    $run = mysqli_query($con, $consSql);
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $stats['cons_opds'] = (int) $row['cnt'];
        $stats['cons_opds_cash'] = (float) $row['cash_sum'];
    }

    $ibd = doctor_monthly_ibd_category_counts($con, $doctor_id, $branch_id, $start, $end);
    $stats['labs'] = $ibd['labs'];
    $stats['svds'] = $ibd['svds'];
    $stats['dncs'] = $ibd['dncs'];
    $stats['procedures'] = $ibd['procedures'];
    $stats['admissions'] = $ibd['admissions'];

    $usgIds = doctor_monthly_usg_item_ids();
    $usgSql = "SELECT COUNT(*) AS cnt
        FROM item_by_doctor ibd
        INNER JOIN item_register_to_branches irb ON irb.id = ibd.item_id AND irb.item_id IN ($usgIds)
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        WHERE ibd.doctor_id = $doctor_id AND ibd.branch_id = $branch_id AND ibd.status = 2
            AND t.doctor_id = $doctor_id AND t.status = 1
            AND t.created >= '$start' AND t.created < '$end'";
    $run = mysqli_query($con, $usgSql);
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $stats['usgs'] = (int) $row['cnt'];
    }

    $refSql = "SELECT
            SUM(CASE WHEN from_user_id = $doctor_id THEN 1 ELSE 0 END) AS referred,
            SUM(CASE WHEN to_user_id = $doctor_id THEN 1 ELSE 0 END) AS referred_opd
        FROM referral_patients
        WHERE referral_patient_status > 1
            AND referral_patient_created >= '$start' AND referral_patient_created < '$end'
            AND (from_user_id = $doctor_id OR to_user_id = $doctor_id)";
    $run = mysqli_query($con, $refSql);
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $stats['referred'] = (int) $row['referred'];
        $stats['referred_opd'] = (int) $row['referred_opd'];
    }

    $gynaeSql = "SELECT COUNT(*) AS cnt FROM gynae_register
        WHERE doctor_id = $doctor_id AND branch_id = $branch_id
            AND created >= '$start' AND created < '$end'";
    $run = mysqli_query($con, $gynaeSql);
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $stats['gynae_system'] = (int) $row['cnt'];
    }

    return $stats;
}

/**
 * @return array<int, array{title: string, count: int, total: float, rate: float}>
 */
function doctor_monthly_profile_opd_breakdown($con, $doctor_id, $branch_id, $year, $month)
{
    $doctor_id = (int) $doctor_id;
    $branch_id = (int) $branch_id;
    $bounds = doctor_monthly_bounds($con, $year, $month);
    $start = $bounds['start'];
    $end = $bounds['end'];

    $sql = "SELECT tt.title,
            COUNT(*) AS token_count,
            COALESCE(SUM(t.cash), 0) AS cash_sum,
            COALESCE(AVG(t.cash), 0) AS avg_cash
        FROM tokans t
        INNER JOIN tokan_types tt ON t.tokan_type_id = tt.id
        WHERE t.created >= '$start' AND t.created < '$end'
            AND t.doctor_id = $doctor_id AND t.tokan_type_id < 100
            AND t.branch_id = $branch_id AND t.status = 1
        GROUP BY t.tokan_type_id, tt.title
        ORDER BY t.tokan_type_id";

    $rows = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $rows[] = array(
                'title' => (string) $row['title'],
                'count' => (int) $row['token_count'],
                'total' => (float) $row['cash_sum'],
                'rate' => (float) $row['avg_cash'],
            );
        }
    }

    return $rows;
}

/**
 * One row per procedure token (not per line item).
 *
 * @return array<int, array{token_no: int, cash: float, title: string, created: string}>
 */
function doctor_monthly_profile_procedure_rows($con, $doctor_id, $branch_id, $year, $month)
{
    $doctor_id = (int) $doctor_id;
    $branch_id = (int) $branch_id;
    $bounds = doctor_monthly_bounds($con, $year, $month);
    $start = $bounds['start'];
    $end = $bounds['end'];

    $sql = "SELECT ibd.tokan_no,
            MAX(t.cash) AS cash,
            MAX(tt.title) AS title,
            MAX(t.created) AS created
        FROM item_by_doctor ibd
        INNER JOIN tokans t ON ibd.tokan_no = t.id AND t.branch_id = ibd.branch_id
        INNER JOIN tokan_types tt ON t.tokan_type_id = tt.id
        INNER JOIN item_register_to_branches irb ON ibd.item_id = irb.id
        INNER JOIN items i ON irb.item_id = i.id AND i.category_id IN (3, 31, 32)
        WHERE ibd.doctor_id = $doctor_id
            AND ibd.created >= '$start' AND ibd.created < '$end'
            AND ibd.branch_id = $branch_id AND ibd.status = 2
        GROUP BY ibd.tokan_no
        ORDER BY created, ibd.tokan_no";

    $rows = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $rows[] = array(
                'token_no' => (int) $row['tokan_no'],
                'cash' => (float) $row['cash'],
                'title' => (string) $row['title'],
                'created' => (string) $row['created'],
            );
        }
    }

    return $rows;
}

/**
 * @return array{count: int, sum: float}
 */
function doctor_monthly_profile_referral_received($con, $doctor_id, $year, $month)
{
    $doctor_id = (int) $doctor_id;
    $bounds = doctor_monthly_bounds($con, $year, $month);
    $start = $bounds['start'];
    $end = $bounds['end'];

    $sql = "SELECT COUNT(referral_patient_id) AS cnt, COALESCE(SUM(received_cash), 0) AS amt
        FROM referral_patients
        WHERE to_user_id = $doctor_id AND received_cash > 0
            AND referral_patient_created >= '$start' AND referral_patient_created < '$end'";
    $run = mysqli_query($con, $sql);
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return array('count' => (int) $row['cnt'], 'sum' => (float) $row['amt']);
    }

    return array('count' => 0, 'sum' => 0.0);
}
