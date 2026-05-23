<?php

/**
 * Pure helpers for summary / progress report pages (unit-testable).
 */

function summary_resolve_branch_id(array $get, array $post, int $sessionDefault = 0): int
{
    foreach (array('b_id', 'br_id') as $key) {
        if (isset($get[$key]) && $get[$key] !== '') {
            return (int) $get[$key];
        }
        if (isset($post[$key]) && $post[$key] !== '') {
            return (int) $post[$key];
        }
    }

    return $sessionDefault;
}

/** Legacy login summary URLs pass branch as u= */
function summary_login_branch_id(array $get, array $post, int $sessionDefault = 0): int
{
    foreach (array('b_id', 'u', 'br_id') as $key) {
        if (isset($get[$key]) && $get[$key] !== '') {
            return (int) $get[$key];
        }
        if (isset($post[$key]) && $post[$key] !== '') {
            return (int) $post[$key];
        }
    }

    return $sessionDefault;
}

function summary_gender_code($gender): string
{
    if ((int) $gender === 1) {
        return 'F';
    }
    if ((int) $gender === 2) {
        return 'M';
    }

    return 'O';
}

function summary_lab_conversion_percent(int $opd, int $diaPatients): int
{
    if ($opd <= 0 || $diaPatients <= 0) {
        return 0;
    }
    if ($opd >= $diaPatients) {
        return (int) (($diaPatients / $opd) * 100);
    }

    return 100;
}

function summary_previous_tokan_display($previousTokanNo): string
{
    if ($previousTokanNo === null || $previousTokanNo === '' || $previousTokanNo === 'NULL') {
        return 'NULL';
    }

    return (string) $previousTokanNo;
}

/**
 * @return array{from: string, to: string, branch_id: int, user_id: int, user_name: string}|null
 */
function summary_token_report_params(array $get, array $post): ?array
{
    if (isset($get['s']) && $get['s'] !== '') {
        $from = (string) $get['s'];
        $to = (string) ($get['e'] ?? '');
        $userId = (int) ($get['u'] ?? 0);
        $userName = (string) ($get['un'] ?? 'ALL');
        $branchId = summary_resolve_branch_id($get, $post, 0);
    } elseif (isset($post['s']) && $post['s'] !== '') {
        $from = (string) $post['s'];
        $to = (string) ($post['e'] ?? '');
        $userId = (int) ($post['u'] ?? 0);
        $userName = (string) ($post['un'] ?? 'ALL');
        $branchId = summary_resolve_branch_id($get, $post, 0);
    } else {
        return null;
    }

    if ($from === '' || $to === '') {
        return null;
    }

    return array(
        'from' => $from,
        'to' => $to,
        'branch_id' => $branchId,
        'user_id' => $userId,
        'user_name' => $userName,
    );
}

/**
 * @return array{from: string, to: string, branch_id: int}|null
 */
function summary_login_report_params(array $get, array $post, int $sessionBranchId = 0): ?array
{
    if (isset($get['s']) && $get['s'] !== '') {
        $from = (string) $get['s'];
        $to = (string) ($get['e'] ?? '');
        $branchId = summary_login_branch_id($get, $post, $sessionBranchId);
    } elseif (isset($post['s']) && $post['s'] !== '') {
        $from = (string) $post['s'];
        $to = (string) ($post['e'] ?? '');
        $branchId = summary_login_branch_id($get, $post, $sessionBranchId);
    } else {
        return null;
    }

    if ($from === '' || $to === '') {
        return null;
    }

    return array(
        'from' => $from,
        'to' => $to,
        'branch_id' => $branchId,
    );
}

function progress_tokans_subquery_sql(int $br_id, string $like): string
{
    $br_id = (int) $br_id;
    return "(SELECT id FROM tokans WHERE branch_id = '$br_id' AND status = 1 AND created LIKE '$like')";
}

/**
 * Days in month without PHP calendar extension (replaces cal_days_in_month).
 */
function ycdo_days_in_month(int $year, int $month): int
{
    if ($month < 1 || $month > 12) {
        return 0;
    }

    return (int) date('t', mktime(0, 0, 0, $month, 1, $year));
}

/**
 * Parse YYYY-MM or YYYY-MM-DD into year, zero-padded month, and day count.
 *
 * @return array{year: int, month: string, month_int: int, days: int}
 */
function ycdo_parse_year_month(string $date): array
{
    $dt = date_create($date);
    if ($dt === false) {
        $year = (int) date('Y');
        $monthInt = (int) date('m');
    } else {
        $year = (int) $dt->format('Y');
        $monthInt = (int) $dt->format('m');
    }

    return array(
        'year' => $year,
        'month' => sprintf('%02d', $monthInt),
        'month_int' => $monthInt,
        'days' => ycdo_days_in_month($year, $monthInt),
    );
}
