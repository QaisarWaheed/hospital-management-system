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
