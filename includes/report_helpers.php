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
 * Branch title block for token summary printouts.
 *
 * @return array{name: string, address: string}
 */
function summary_branch_header(mysqli $con, int $br_id, string $defaultName = ''): array
{
    $branch_name = $defaultName !== '' ? $defaultName : 'YCDO';
    $branch_address = '';
    $br_id = (int) $br_id;
    if ($br_id < 1) {
        return array('name' => $branch_name, 'address' => $branch_address);
    }
    $run = mysqli_query($con, "SELECT name, address FROM branchs WHERE id = '$br_id' LIMIT 1");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        if (!empty($row['name'])) {
            $branch_name = (string) $row['name'];
        } elseif (!empty($row['address'])) {
            $branch_name = (string) $row['address'];
        }
        $branch_address = (string) ($row['address'] ?? '');
    }
    return array('name' => $branch_name, 'address' => $branch_address);
}

function summary_tokans_date_sql(string $from_date, string $to_date, mysqli $con): string
{
    $from = mysqli_real_escape_string($con, $from_date);
    $to = mysqli_real_escape_string($con, $to_date);
    return "DATE(`created`) >= '$from' AND DATE(`created`) <= '$to'";
}

function summary_pending_date_sql(string $from_date, string $to_date, mysqli $con): string
{
    $from = mysqli_real_escape_string($con, $from_date);
    $to = mysqli_real_escape_string($con, $to_date);
    return "DATE(`created`) >= '$from' AND DATE(`created`) <= '$to'";
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

/**
 * @return array<int, array{id: int, address: string}>
 */
function summary_active_branches($con, bool $allBranches = true, int $sessionBranchId = 0): array
{
    if ($allBranches) {
        $sql = "SELECT id, address FROM branchs WHERE status = '1' ORDER BY address";
    } else {
        $sessionBranchId = (int) $sessionBranchId;
        $sql = "SELECT id, address FROM branchs WHERE status = '1' AND id = '$sessionBranchId' ORDER BY address";
    }

    $branches = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $branches[] = array(
                'id' => (int) $row['id'],
                'address' => (string) $row['address'],
            );
        }
    }

    return $branches;
}

function summary_branch_may_select_all(int $isAdmin = 0, int $isIncharge = 0): bool
{
    return $isAdmin === 1 || $isIncharge === 2;
}

function summary_branch_select_html($con, int $selectedId, int $sessionBranchId, bool $allBranches, string $name = 'br_id'): string
{
    $html = '';
    foreach (summary_active_branches($con, $allBranches, $sessionBranchId) as $branch) {
        $selected = ((int) $branch['id'] === $selectedId) ? ' selected' : '';
        $html .= '<option value="' . (int) $branch['id'] . '"' . $selected . '>'
            . htmlspecialchars($branch['address'], ENT_QUOTES, 'UTF-8') . '</option>';
    }

    if ($html === '') {
        $html = '<option value="">No branch found</option>';
    }

    return $html;
}

/**
 * @return array{br_id: int, date: string}
 */
function gynae_report_resolve_params(array $get, array $post, int $sessionBranchId = 0): array
{
    $br_id = summary_resolve_branch_id($get, $post, $sessionBranchId);
    $date = date('Y-m-d');
    if (isset($get['date']) && $get['date'] !== '') {
        $date = (string) $get['date'];
    } elseif (isset($post['date']) && $post['date'] !== '') {
        $date = (string) $post['date'];
    }

    return array(
        'br_id' => $br_id,
        'date' => $date,
    );
}

function report_safe_number_format((float)($value ?? 0), int $decimals = 0): string
{
    if ($value === null || $value === '') {
        return number_format((float)(0 ?? 0), $decimals);
    }

    return number_format((float)((float) $value ?? 0), $decimals);
}
