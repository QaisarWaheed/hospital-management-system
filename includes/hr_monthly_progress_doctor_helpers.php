<?php

require_once __DIR__ . '/account_report_helpers.php';
require_once __DIR__ . '/report_helpers.php';

/**
 * @return array{year: int, month: string, start: string, end: string}
 */
function hr_monthly_progress_month_bounds($con, $dateInput)
{
    $raw = substr((string) $dateInput, 0, 10);
    if (strlen($raw ?? '') === 7) {
        $raw .= '-01';
    }
    $ym = ycdo_parse_year_month($raw);
    $bounds = account_report_month_datetime_bounds($ym['year'], $ym['month']);

    return array(
        'year' => $ym['year'],
        'month' => $ym['month'],
        'start' => mysqli_real_escape_string($con, $bounds['start']),
        'end' => mysqli_real_escape_string($con, $bounds['end']),
    );
}

/**
 * @return array<int, array<string, mixed>>
 */
function hr_monthly_progress_doctor_empty_row()
{
    return array(
        'doctor_id' => 0,
        'u_name' => '',
        'tag_name' => '',
        'opd' => 0,
        'cash' => 0.0,
        'tests' => 0,
        'lab_test_cash' => 0.0,
        'procedures' => 0,
        'consultants' => 0,
        'dentals' => 0,
        'skins' => 0,
        'eyes' => 0,
        'physiotherapies' => 0,
        'minir_procedures' => 0,
        'svds' => 0,
        'dncs' => 0,
        'usgs' => 0,
        'admissions' => 0,
        'gyneas' => 0,
        'emergency' => 0,
        'ecgs' => 0,
        'gynae_system' => 0,
        'refered' => 0,
        'refered_to' => 0,
    );
}

/**
 * Monthly progress (doctor) for one branch — batched queries.
 *
 * @return array<int, array<string, mixed>>
 */
function hr_monthly_progress_doctor_rows($con, $branch_id, $dateInput)
{
    $branch_id = (int) $branch_id;
    $bounds = hr_monthly_progress_month_bounds($con, $dateInput);
    $start = $bounds['start'];
    $end = $bounds['end'];

    $doctors = array();

    $tokanSql = "SELECT t.doctor_id, u.u_name, b.tag_name,
            COUNT(CASE WHEN t.tokan_type_id < 100 THEN 1 END) AS opd,
            COALESCE(SUM(t.cash), 0) AS cash
        FROM tokans t
        INNER JOIN users u ON u.id = t.doctor_id
        INNER JOIN branchs b ON b.id = u.branch_id
        WHERE t.branch_id = $branch_id AND t.status = 1
            AND t.created >= '$start' AND t.created < '$end'
            AND t.doctor_id > 0
        GROUP BY t.doctor_id, u.u_name, b.tag_name
        ORDER BY t.doctor_id";
    $run = mysqli_query($con, $tokanSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $did = (int) $row['doctor_id'];
            $base = hr_monthly_progress_doctor_empty_row();
            $base['doctor_id'] = $did;
            $base['u_name'] = (string) $row['u_name'];
            $base['tag_name'] = (string) $row['tag_name'];
            $base['opd'] = (int) $row['opd'];
            $base['cash'] = (float) $row['cash'];
            $doctors[$did] = $base;
        }
    }

    $ibdSql = "SELECT ibd.doctor_id,
            COUNT(CASE WHEN ibd.category_id = 2 THEN 1 END) AS tests,
            COALESCE(SUM(CASE WHEN ibd.category_id = 2 THEN ibd.sale_price END), 0) AS lab_test_cash,
            COUNT(CASE WHEN ibd.category_id = 3 THEN 1 END) AS procedures,
            COUNT(CASE WHEN ibd.category_id = 29 THEN 1 END) AS consultants,
            COUNT(CASE WHEN ibd.category_id = 31 THEN 1 END) AS dentals,
            COUNT(CASE WHEN ibd.category_id = 32 THEN 1 END) AS skins,
            COUNT(CASE WHEN ibd.category_id = 33 THEN 1 END) AS eyes,
            COUNT(CASE WHEN ibd.category_id = 34 THEN 1 END) AS physiotherapies,
            COUNT(CASE WHEN ibd.category_id = 36 THEN 1 END) AS minir_procedures,
            COUNT(CASE WHEN ibd.category_id = 37 THEN 1 END) AS svds,
            COUNT(CASE WHEN ibd.category_id = 38 THEN 1 END) AS dncs,
            COUNT(CASE WHEN ibd.category_id = 39 THEN 1 END) AS usgs,
            COUNT(CASE WHEN ibd.category_id = 40 THEN 1 END) AS admissions,
            COUNT(CASE WHEN ibd.category_id = 41 THEN 1 END) AS gyneas,
            COUNT(CASE WHEN ibd.category_id = 42 THEN 1 END) AS emergency,
            COUNT(CASE WHEN ibd.category_id = 44 THEN 1 END) AS ecgs
        FROM item_by_doctor ibd
        WHERE ibd.branch_id = $branch_id
            AND ibd.created >= '$start' AND ibd.created < '$end'
            AND ibd.category_id IN (2, 3, 29, 31, 32, 33, 34, 36, 37, 38, 39, 40, 41, 42, 44)
        GROUP BY ibd.doctor_id";
    $run = mysqli_query($con, $ibdSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $did = (int) $row['doctor_id'];
            if (!isset($doctors[$did])) {
                continue;
            }
            $doctors[$did]['tests'] = (int) $row['tests'];
            $doctors[$did]['lab_test_cash'] = (float) $row['lab_test_cash'];
            $doctors[$did]['procedures'] = (int) $row['procedures'];
            $doctors[$did]['consultants'] = (int) $row['consultants'];
            $doctors[$did]['dentals'] = (int) $row['dentals'];
            $doctors[$did]['skins'] = (int) $row['skins'];
            $doctors[$did]['eyes'] = (int) $row['eyes'];
            $doctors[$did]['physiotherapies'] = (int) $row['physiotherapies'];
            $doctors[$did]['minir_procedures'] = (int) $row['minir_procedures'];
            $doctors[$did]['svds'] = (int) $row['svds'];
            $doctors[$did]['dncs'] = (int) $row['dncs'];
            $doctors[$did]['usgs'] = (int) $row['usgs'];
            $doctors[$did]['admissions'] = (int) $row['admissions'];
            $doctors[$did]['gyneas'] = (int) $row['gyneas'];
            $doctors[$did]['emergency'] = (int) $row['emergency'];
            $doctors[$did]['ecgs'] = (int) $row['ecgs'];
        }
    }

    $gynaeSql = "SELECT doctor_id, COUNT(*) AS cnt FROM gynae_register
        WHERE branch_id = $branch_id AND created >= '$start' AND created < '$end'
        GROUP BY doctor_id";
    $run = mysqli_query($con, $gynaeSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $did = (int) $row['doctor_id'];
            if (isset($doctors[$did])) {
                $doctors[$did]['gynae_system'] = (int) $row['cnt'];
            }
        }
    }

    $refFromSql = "SELECT from_user_id AS doctor_id, COUNT(*) AS cnt
        FROM referral_patients
        WHERE branch_id = $branch_id AND referral_patient_status > 1
            AND referral_patient_created >= '$start' AND referral_patient_created < '$end'
        GROUP BY from_user_id";
    $run = mysqli_query($con, $refFromSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $did = (int) $row['doctor_id'];
            if (isset($doctors[$did])) {
                $doctors[$did]['refered'] = (int) $row['cnt'];
            }
        }
    }

    $refToSql = "SELECT to_user_id AS doctor_id, COUNT(*) AS cnt
        FROM referral_patients
        WHERE referral_patient_status > 1
            AND referral_patient_created >= '$start' AND referral_patient_created < '$end'
        GROUP BY to_user_id";
    $run = mysqli_query($con, $refToSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $did = (int) $row['doctor_id'];
            if (isset($doctors[$did])) {
                $doctors[$did]['refered_to'] = (int) $row['cnt'];
            }
        }
    }

    ksort($doctors);

    return $doctors;
}

function hr_monthly_progress_lab_percent($opd, $tests)
{
    $opd = (int) $opd;
    $tests = (int) $tests;
    if ($opd < 1 || $tests < 1) {
        return 0;
    }
    if ($opd >= $tests) {
        return (int) (($tests / $opd) * 100);
    }

    return 100;
}
