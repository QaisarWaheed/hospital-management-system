<?php

/**
 * Resolve date / branch for progress print pages.
 *
 * @return array{date: string, br_id: int, date_esc: string, like: string}
 */
function progress_report_resolve_request($con)
{
    if (isset($_GET['date'])) {
        $date = (string) $_GET['date'];
        $br_id = isset($_GET['br_id']) ? (int) $_GET['br_id'] : 0;
    } elseif (isset($_POST['date'])) {
        $date = (string) $_POST['date'];
        $br_id = isset($_POST['br_id']) ? (int) $_POST['br_id'] : 0;
    } else {
        exit(0);
    }

    $date_esc = mysqli_real_escape_string($con, $date);

    return array(
        'date' => $date,
        'br_id' => $br_id,
        'date_esc' => $date_esc,
        'like' => $date_esc . '%',
    );
}

function progress_tokans_subquery($br_id, $like)
{
    $br_id = (int) $br_id;
    return "(SELECT id FROM tokans WHERE branch_id = '$br_id' AND status = 1 AND created LIKE '$like')";
}

/**
 * @return array<int, int>
 */
function progress_map_int($con, $sql, $key_col, $val_col)
{
    $map = array();
    $run = mysqli_query($con, $sql);
    if (!$run) {
        return $map;
    }
    while ($row = mysqli_fetch_assoc($run)) {
        $map[(int) $row[$key_col]] = (int) $row[$val_col];
    }
    return $map;
}

/**
 * @return array<int, float>
 */
function progress_map_float($con, $sql, $key_col, $val_col)
{
    $map = array();
    $run = mysqli_query($con, $sql);
    if (!$run) {
        return $map;
    }
    while ($row = mysqli_fetch_assoc($run)) {
        $map[(int) $row[$key_col]] = (float) $row[$val_col];
    }
    return $map;
}

function progress_item_count_by_doctor($con, $br_id, $like, $item_ids_sql)
{
    $br_id = (int) $br_id;
    $tokens = progress_tokans_subquery($br_id, $like);
    $sql = "SELECT doctor_id, COUNT(DISTINCT tokan_no) AS cnt
        FROM item_by_doctor
        WHERE branch_id = '$br_id' AND status = '2'
        AND tokan_no IN $tokens
        AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN ($item_ids_sql))
        GROUP BY doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_opd_count_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $sql = "SELECT doctor_id, COUNT(id) AS cnt FROM tokans
        WHERE tokan_type_id < 9 AND status = 1 AND branch_id = '$br_id' AND created LIKE '$like'
        GROUP BY doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_gynae_register_count_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $sql = "SELECT doctor_id, COUNT(*) AS cnt FROM gynae_register
        WHERE branch_id = '$br_id' AND created LIKE '$like'
        GROUP BY doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_opd_count_by_doctor_lte10($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $sql = "SELECT doctor_id, COUNT(id) AS cnt FROM tokans
        WHERE branch_id = '$br_id' AND status = 1 AND tokan_type_id <= 10 AND created LIKE '$like'
        GROUP BY doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_gynae_token_count_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $like = mysqli_real_escape_string($con, $like);
    $sql = "SELECT doctor_id, COUNT(id) AS cnt FROM item_by_doctor
        WHERE branch_id = '$br_id' AND category_id = '41' AND created LIKE '$like'
        GROUP BY doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_gynae_register_count_by_doctor_since($con, $br_id, $since_date)
{
    $br_id = (int) $br_id;
    $since_date = mysqli_real_escape_string($con, $since_date);
    $sql = "SELECT doctor_id, COUNT(*) AS cnt FROM gynae_register
        WHERE branch_id = '$br_id' AND created > '$since_date'
        GROUP BY doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_gynae_token_count_by_doctor_since($con, $br_id, $since_date)
{
    $br_id = (int) $br_id;
    $since_date = mysqli_real_escape_string($con, $since_date);
    $sql = "SELECT doctor_id, COUNT(id) AS cnt FROM item_by_doctor
        WHERE branch_id = '$br_id' AND category_id = '41' AND created > '$since_date'
        GROUP BY doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_gynae_daily_doctor_ids($con, $br_id, $month_like)
{
    $br_id = (int) $br_id;
    $month_like = mysqli_real_escape_string($con, $month_like);
    $sql = "SELECT DISTINCT doctor_id AS id FROM item_by_doctor
        WHERE branch_id = '$br_id' AND category_id = '41' AND created LIKE '$month_like'";
    $ids = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $ids[] = (int) $row['id'];
        }
    }
    sort($ids, SORT_NUMERIC);
    return $ids;
}

function progress_referral_from_count_by_doctor($con, $like, $only_successful = true)
{
    $status_sql = $only_successful ? " AND referral_patient_status > '1' " : '';
    $sql = "SELECT from_user_id AS doctor_id, COUNT(*) AS cnt FROM referral_patients
        WHERE referral_patient_created LIKE '$like' $status_sql
        GROUP BY from_user_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

/**
 * @return array<int, array<int, array{count_token: int, total_cash: float}>>
 */
function progress_category_stats_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $sql = "SELECT doctor_id, category_id,
        COUNT(item_by_doctor.category_id) AS count_data,
        COUNT(DISTINCT item_by_doctor.tokan_no) AS count_token,
        SUM(
            CASE
                WHEN (fix_dose = 0 AND tokan_type_id = 102) THEN (dose * feed * days) * sale_price_poor
                WHEN (fix_dose = 0 AND tokan_type_id = 103) THEN (dose * feed * days) * sale_price_member
                WHEN (fix_dose = 0 AND tokan_type_id = 104) THEN (dose * feed * days) * sale_price_general
                WHEN (fix_dose > 0 AND tokan_type_id = 102) THEN fix_dose * sale_price_poor
                WHEN (fix_dose > 0 AND tokan_type_id = 103) THEN fix_dose * sale_price_member
                WHEN (fix_dose > 0 AND tokan_type_id = 104) THEN fix_dose * sale_price_general
                ELSE 0
            END
        ) AS total_cash
        FROM item_by_doctor
        WHERE created LIKE '$like' AND branch_id = '$br_id'
        AND category_id IN (2, 3, 29, 31, 32, 33, 34, 36, 37, 38, 39, 40, 41, 42, 44)
        GROUP BY doctor_id, category_id";
    $stats = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $did = (int) $row['doctor_id'];
            $cid = (int) $row['category_id'];
            if (!isset($stats[$did])) {
                $stats[$did] = array();
            }
            $stats[$did][$cid] = array(
                'count_token' => (int) $row['count_token'],
                'total_cash' => (float) $row['total_cash'],
            );
        }
    }
    return $stats;
}

function progress_referral_to_count_by_doctor($con, $like)
{
    $sql = "SELECT to_user_id AS doctor_id, COUNT(*) AS cnt FROM referral_patients
        WHERE referral_patient_created LIKE '$like' AND referral_patient_status > '1'
        GROUP BY to_user_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_cash_sum_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $sql = "SELECT doctor_id, COALESCE(SUM(cash), 0) AS total FROM tokans
        WHERE status = 1 AND branch_id = '$br_id' AND created LIKE '$like'
        GROUP BY doctor_id";
    return progress_map_float($con, $sql, 'doctor_id', 'total');
}

function progress_lab_stats_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $tokens = progress_tokans_subquery($br_id, $like);
    $sql = "SELECT doctor_id, COUNT(cash_received) AS token_cnt, COALESCE(SUM(cash_received), 0) AS cash_sum
        FROM tokans
        WHERE doctor_id > 0 AND status = 1 AND branch_id = '$br_id' AND created LIKE '$like'
        AND id IN (
            SELECT tokan_no FROM item_by_doctor
            WHERE item_id IN (
                SELECT id FROM item_register_to_branches
                WHERE branch_id = '$br_id' AND item_id IN (SELECT id FROM items WHERE category_id = 2)
            )
        )
        GROUP BY doctor_id";
    $stats = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $stats[(int) $row['doctor_id']] = array(
                'count' => (int) $row['token_cnt'],
                'cash' => (float) $row['cash_sum'],
            );
        }
    }
    return $stats;
}

/**
 * @return array<int, array{count: int, cash: float}>
 */
function progress_dia_patient_stats_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $sql = "SELECT doctor_id, COUNT(DISTINCT tokan_no) AS cnt, COALESCE(SUM(sale_price), 0) AS cash_sum
        FROM item_by_doctor
        WHERE category_id = 2 AND branch_id = '$br_id' AND created LIKE '$like'
        GROUP BY doctor_id";
    $stats = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $stats[(int) $row['doctor_id']] = array(
                'count' => (int) $row['cnt'],
                'cash' => (float) $row['cash_sum'],
            );
        }
    }
    return $stats;
}

/**
 * Row counts per category (not distinct tokens) for branch daily progress.
 *
 * @return array<int, array<string, int>>
 */
function progress_item_row_counts_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $sql = "SELECT doctor_id,
        COUNT(CASE WHEN category_id = 2 THEN 1 END) AS tests,
        COUNT(CASE WHEN category_id = 3 THEN 1 END) AS procedures,
        COUNT(CASE WHEN category_id = 29 THEN 1 END) AS consultants,
        COUNT(CASE WHEN category_id = 31 THEN 1 END) AS dentals,
        COUNT(CASE WHEN category_id = 32 THEN 1 END) AS skins,
        COUNT(CASE WHEN category_id = 33 THEN 1 END) AS eyes,
        COUNT(CASE WHEN category_id = 34 THEN 1 END) AS physiotherapies,
        COUNT(CASE WHEN category_id = 36 THEN 1 END) AS minir_procedures,
        COUNT(CASE WHEN category_id = 37 THEN 1 END) AS svds,
        COUNT(CASE WHEN category_id = 38 THEN 1 END) AS dncs,
        COUNT(CASE WHEN category_id = 39 THEN 1 END) AS usgs,
        COUNT(CASE WHEN category_id = 40 THEN 1 END) AS admissions,
        COUNT(CASE WHEN category_id = 41 THEN 1 END) AS gyneas,
        COUNT(CASE WHEN category_id = 42 THEN 1 END) AS emergency,
        COUNT(CASE WHEN category_id = 44 THEN 1 END) AS ecgs
        FROM item_by_doctor
        WHERE created LIKE '$like' AND branch_id = '$br_id'
        AND category_id IN (2, 3, 29, 31, 32, 33, 34, 36, 37, 38, 39, 40, 41, 42, 44)
        GROUP BY doctor_id";
    $stats = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $stats[(int) $row['doctor_id']] = array(
                'tests' => (int) $row['tests'],
                'procedures' => (int) $row['procedures'],
                'consultants' => (int) $row['consultants'],
                'dentals' => (int) $row['dentals'],
                'skins' => (int) $row['skins'],
                'eyes' => (int) $row['eyes'],
                'physiotherapies' => (int) $row['physiotherapies'],
                'minir_procedures' => (int) $row['minir_procedures'],
                'svds' => (int) $row['svds'],
                'dncs' => (int) $row['dncs'],
                'usgs' => (int) $row['usgs'],
                'admissions' => (int) $row['admissions'],
                'gyneas' => (int) $row['gyneas'],
                'emergency' => (int) $row['emergency'],
                'ecgs' => (int) $row['ecgs'],
            );
        }
    }
    return $stats;
}

function progress_referral_from_count_by_branch($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $sql = "SELECT from_user_id AS doctor_id, COUNT(*) AS cnt FROM referral_patients
        WHERE referral_patient_created LIKE '$like' AND referral_patient_status > '1' AND branch_id = '$br_id'
        GROUP BY from_user_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

/**
 * Resolve date / branch / time window for timed progress print pages.
 *
 * @return array{date: string, br_id: int, start_from: string, end_at: string, start_at: string, end_at_ts: string}
 */
function progress_report_resolve_time_request($con)
{
    if (isset($_GET['date'])) {
        $date = (string) $_GET['date'];
        $start_from = (string) ($_GET['start_from'] ?? '');
        $end_at = (string) ($_GET['end_at'] ?? '');
        $br_id = isset($_GET['br_id']) ? (int) $_GET['br_id'] : 0;
    } elseif (isset($_POST['date'])) {
        $date = (string) $_POST['date'];
        $start_from = (string) ($_POST['start_from'] ?? '');
        $end_at = (string) ($_POST['end_at'] ?? '');
        $br_id = isset($_POST['br_id']) ? (int) $_POST['br_id'] : 0;
    } else {
        exit(0);
    }

    $date_esc = mysqli_real_escape_string($con, $date);
    $start_from_esc = mysqli_real_escape_string($con, $start_from);
    $end_at_esc = mysqli_real_escape_string($con, $end_at);

    return array(
        'date' => $date,
        'br_id' => $br_id,
        'start_from' => $start_from,
        'end_at' => $end_at,
        'start_at' => $date_esc . ' ' . $start_from_esc,
        'end_at_ts' => $date_esc . ' ' . $end_at_esc,
    );
}

/**
 * @return array<int, array{count: int, cash: float}>
 */
function progress_dia_patient_stats_by_doctor_range($con, $br_id, $start_at, $end_at)
{
    $br_id = (int) $br_id;
    $sql = "SELECT doctor_id, COUNT(DISTINCT tokan_no) AS cnt, COALESCE(SUM(sale_price), 0) AS cash_sum
        FROM item_by_doctor
        WHERE category_id = 2 AND branch_id = '$br_id'
        AND created >= '$start_at' AND created < '$end_at'
        GROUP BY doctor_id";
    $stats = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $stats[(int) $row['doctor_id']] = array(
                'count' => (int) $row['cnt'],
                'cash' => (float) $row['cash_sum'],
            );
        }
    }
    return $stats;
}

/**
 * @return array<int, array<string, int>>
 */
function progress_item_row_counts_by_doctor_range($con, $br_id, $start_at, $end_at)
{
    $br_id = (int) $br_id;
    $sql = "SELECT doctor_id,
        COUNT(CASE WHEN category_id = 2 THEN 1 END) AS tests,
        COUNT(CASE WHEN category_id = 3 THEN 1 END) AS procedures,
        COUNT(CASE WHEN category_id = 29 THEN 1 END) AS consultants,
        COUNT(CASE WHEN category_id = 31 THEN 1 END) AS dentals,
        COUNT(CASE WHEN category_id = 32 THEN 1 END) AS skins,
        COUNT(CASE WHEN category_id = 33 THEN 1 END) AS eyes,
        COUNT(CASE WHEN category_id = 34 THEN 1 END) AS physiotherapies,
        COUNT(CASE WHEN category_id = 36 THEN 1 END) AS minir_procedures,
        COUNT(CASE WHEN category_id = 37 THEN 1 END) AS svds,
        COUNT(CASE WHEN category_id = 38 THEN 1 END) AS dncs,
        COUNT(CASE WHEN category_id = 39 THEN 1 END) AS usgs,
        COUNT(CASE WHEN category_id = 40 THEN 1 END) AS admissions,
        COUNT(CASE WHEN category_id = 41 THEN 1 END) AS gyneas,
        COUNT(CASE WHEN category_id = 42 THEN 1 END) AS emergency,
        COUNT(CASE WHEN category_id = 44 THEN 1 END) AS ecgs
        FROM item_by_doctor
        WHERE branch_id = '$br_id'
        AND created >= '$start_at' AND created < '$end_at'
        AND category_id IN (2, 3, 29, 31, 32, 33, 34, 36, 37, 38, 39, 40, 41, 42, 44)
        GROUP BY doctor_id";
    $stats = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $stats[(int) $row['doctor_id']] = array(
                'tests' => (int) $row['tests'],
                'procedures' => (int) $row['procedures'],
                'consultants' => (int) $row['consultants'],
                'dentals' => (int) $row['dentals'],
                'skins' => (int) $row['skins'],
                'eyes' => (int) $row['eyes'],
                'physiotherapies' => (int) $row['physiotherapies'],
                'minir_procedures' => (int) $row['minir_procedures'],
                'svds' => (int) $row['svds'],
                'dncs' => (int) $row['dncs'],
                'usgs' => (int) $row['usgs'],
                'admissions' => (int) $row['admissions'],
                'gyneas' => (int) $row['gyneas'],
                'emergency' => (int) $row['emergency'],
                'ecgs' => (int) $row['ecgs'],
            );
        }
    }
    return $stats;
}

function progress_gynae_register_count_by_doctor_range($con, $br_id, $start_at, $end_at)
{
    $br_id = (int) $br_id;
    $sql = "SELECT doctor_id, COUNT(*) AS cnt FROM gynae_register
        WHERE branch_id = '$br_id' AND created >= '$start_at' AND created < '$end_at'
        GROUP BY doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_referral_from_count_by_branch_range($con, $br_id, $start_at, $end_at)
{
    $br_id = (int) $br_id;
    $sql = "SELECT from_user_id AS doctor_id, COUNT(*) AS cnt FROM referral_patients
        WHERE referral_patient_created >= '$start_at' AND referral_patient_created <= '$end_at'
        AND referral_patient_status > '1' AND branch_id = '$br_id'
        GROUP BY from_user_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_referral_to_count_by_doctor_range($con, $start_at, $end_at)
{
    $sql = "SELECT to_user_id AS doctor_id, COUNT(*) AS cnt FROM referral_patients
        WHERE referral_patient_created >= '$start_at' AND referral_patient_created <= '$end_at'
        AND referral_patient_status > '1'
        GROUP BY to_user_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_cons_opd_count_by_doctor($con, $br_id, $like)
{
    return progress_item_count_by_doctor(
        $con,
        $br_id,
        $like,
        '489, 849, 850, 1415, 1327, 1139, 1141, 1477, 1154'
    );
}

function progress_usg_count_by_doctor($con, $br_id, $like)
{
    return progress_item_count_by_doctor(
        $con,
        $br_id,
        $like,
        '476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435'
    );
}

/**
 * Cash collection for Progress Monthly (Doctors) print report.
 *
 * @return array<int, float>
 */
function progress_doctor_progress_collection_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $tokens = progress_tokans_subquery($br_id, $like);
    $cons_items = '489, 849, 850, 1415, 1327, 1139, 1141, 1477, 1154, 476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435';
    $sql = "SELECT doctor_id, COALESCE(SUM(cash), 0) AS total FROM tokans
        WHERE status = 1 AND branch_id = '$br_id' AND created LIKE '$like'
        AND (
            tokan_type_id < 9
            OR id IN (
                SELECT tokan_no FROM item_by_doctor
                WHERE status = '2' AND tokan_no IN $tokens
                AND item_id IN (
                    SELECT id FROM item_register_to_branches WHERE item_id IN ($cons_items)
                )
            )
        )
        GROUP BY doctor_id";
    return progress_map_float($con, $sql, 'doctor_id', 'total');
}

/**
 * Month window for YYYY-MM style progress reports.
 *
 * @return array{start_date: string, end_date: string}
 */
function progress_month_date_range($date)
{
    $month = substr((string) $date, 0, 7);
    $timestamp = strtotime('first day of next month', strtotime($month . '-01'));

    return array(
        'start_date' => $month . '-01',
        'end_date' => date('Y-m-d', $timestamp),
    );
}

/**
 * Branch IDs with token activity on a given day.
 *
 * @return int[]
 */
function progress_branch_ids_for_date($con, $like)
{
    $like = mysqli_real_escape_string($con, $like);
    $ids = array();
    $run = mysqli_query($con, "SELECT DISTINCT branch_id FROM tokans WHERE created LIKE '$like' ORDER BY branch_id");
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $ids[] = (int) $row['branch_id'];
        }
    }
    return $ids;
}

/**
 * @return array<int, int>
 */
function progress_opd_count_by_branch($con, $like)
{
    $like = mysqli_real_escape_string($con, $like);
    $sql = "SELECT branch_id, COUNT(id) AS cnt FROM tokans
        WHERE tokan_type_id < 9 AND status = 1 AND created LIKE '$like'
        GROUP BY branch_id";
    return progress_map_int($con, $sql, 'branch_id', 'cnt');
}

/**
 * Distinct item_by_doctor rows (tokan_no) per branch for mapped items.
 *
 * @return array<int, int>
 */
function progress_item_tokan_count_by_branch($con, $like, $item_ids_sql)
{
    $like = mysqli_real_escape_string($con, $like);
    $sql = "SELECT ibd.branch_id, COUNT(ibd.tokan_no) AS cnt
        FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        WHERE t.created LIKE '$like' AND t.status = 1 AND ibd.status = '2'
        AND ir.item_id IN ($item_ids_sql)
        GROUP BY ibd.branch_id";
    return progress_map_int($con, $sql, 'branch_id', 'cnt');
}

/**
 * @return array<int, int>
 */
function progress_procedure_tokan_count_by_branch($con, $like)
{
    $like = mysqli_real_escape_string($con, $like);
    $sql = "SELECT ibd.branch_id, COUNT(ibd.tokan_no) AS cnt
        FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        INNER JOIN items i ON ir.item_id = i.id
        WHERE t.created LIKE '$like' AND t.status = 1 AND ibd.status = '2'
        AND i.category_id = '3'
        GROUP BY ibd.branch_id";
    return progress_map_int($con, $sql, 'branch_id', 'cnt');
}

/**
 * @return array<int, int>
 */
function progress_gynae_register_count_by_branch($con, $like)
{
    $like = mysqli_real_escape_string($con, $like);
    $sql = "SELECT branch_id, COUNT(*) AS cnt FROM gynae_register
        WHERE created LIKE '$like'
        GROUP BY branch_id";
    return progress_map_int($con, $sql, 'branch_id', 'cnt');
}

/**
 * @return array<int, string>
 */
function progress_branch_tag_map($con)
{
    $map = array();
    $run = mysqli_query($con, "SELECT id, tag_name FROM branchs WHERE status = '1' ORDER BY id");
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $map[(int) $row['id']] = (string) $row['tag_name'];
        }
    }
    return $map;
}

/**
 * @return array<int, int>
 */
function progress_opd_count_by_branch_day($con, $date_esc)
{
    $sql = "SELECT branch_id, COUNT(id) AS cnt FROM tokans
        WHERE tokan_type_id < 9 AND status = 1 AND DATE(created) = '$date_esc'
        GROUP BY branch_id";
    return progress_map_int($con, $sql, 'branch_id', 'cnt');
}

/**
 * Single scan for all item_by_doctor metrics (org daily progress report).
 *
 * @return array<int, array<string, int>>
 */
function progress_item_metrics_by_branch_day($con, $date_esc)
{
    $sql = "SELECT ibd.branch_id,
        COUNT(CASE WHEN ir.item_id IN (489, 849, 850, 1415, 1327, 1139, 1141, 1477, 1154) THEN 1 END) AS cons,
        COUNT(CASE WHEN ir.item_id IN (444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301) THEN 1 END) AS admissions,
        COUNT(CASE WHEN i.category_id = 3 THEN 1 END) AS procedures,
        COUNT(CASE WHEN ir.item_id IN (472, 1118, 1313) THEN 1 END) AS svds,
        COUNT(CASE WHEN ir.item_id IN (473, 1119, 1314) THEN 1 END) AS dncs,
        COUNT(CASE WHEN ir.item_id IN (476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435) THEN 1 END) AS usgs,
        COUNT(CASE WHEN ir.item_id IN (483, 1159, 1321, 1414, 1576) THEN 1 END) AS gynae
    FROM item_by_doctor ibd
    INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        AND t.status = 1 AND DATE(t.created) = '$date_esc'
    INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
    LEFT JOIN items i ON ir.item_id = i.id
    WHERE ibd.status = '2'
    GROUP BY ibd.branch_id";

    $stats = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $bid = (int) $row['branch_id'];
            $stats[$bid] = array(
                'cons' => (int) $row['cons'],
                'admissions' => (int) $row['admissions'],
                'procedures' => (int) $row['procedures'],
                'svds' => (int) $row['svds'],
                'dncs' => (int) $row['dncs'],
                'usgs' => (int) $row['usgs'],
                'gynae' => (int) $row['gynae'],
            );
        }
    }
    return $stats;
}

/**
 * @return array<int, int>
 */
function progress_gynae_register_count_by_branch_day($con, $date_esc)
{
    $sql = "SELECT branch_id, COUNT(*) AS cnt FROM gynae_register
        WHERE DATE(created) = '$date_esc'
        GROUP BY branch_id";
    return progress_map_int($con, $sql, 'branch_id', 'cnt');
}

/**
 * @param string $date Y-m-d
 * @return array{
 *   branch_ids: int[],
 *   branch_tags: array<int, string>,
 *   opd: array<int, int>,
 *   item: array<int, array<string, int>>,
 *   gynae_system: array<int, int>
 * }
 */
function progress_organization_daily_branch_summary($con, $date)
{
    $date_esc = mysqli_real_escape_string($con, $date);
    $opd = progress_opd_count_by_branch_day($con, $date_esc);
    $item = progress_item_metrics_by_branch_day($con, $date_esc);
    $gynae_system = progress_gynae_register_count_by_branch_day($con, $date_esc);

    $branch_ids = array_unique(array_merge(
        array_keys($opd),
        array_keys($item),
        array_keys($gynae_system)
    ));
    sort($branch_ids, SORT_NUMERIC);

    return array(
        'branch_ids' => $branch_ids,
        'branch_tags' => progress_branch_tag_map($con),
        'opd' => $opd,
        'item' => $item,
        'gynae_system' => $gynae_system,
    );
}
