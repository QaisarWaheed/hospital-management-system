<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries

require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';

/**
 * Convert YYYY-MM or YYYY-MM-DD (with optional %) to [start, end) datetime strings.
 *
 * @return array{start: string, end: string}
 */
function progress_range_from_like($con, $like)
{
    $prefix = rtrim((string) $like, '%');
    $prefix = mysqli_real_escape_string($con, $prefix);

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $prefix)) {
        $day = ycdo_sql_day_range($prefix);

        return array(
            'start' => mysqli_real_escape_string($con, $day['start']),
            'end' => mysqli_real_escape_string($con, $day['end']),
        );
    }

    if (preg_match('/^\d{4}-\d{2}$/', $prefix)) {
        $month = progress_month_date_range($prefix);

        return array(
            'start' => mysqli_real_escape_string($con, $month['start_date'] . ' 00:00:00'),
            'end' => mysqli_real_escape_string($con, $month['end_date'] . ' 00:00:00'),
        );
    }

    return array(
        'start' => $prefix,
        'end' => date('Y-m-d H:i:s', strtotime($prefix . ' +1 day')),
    );
}

/**
 * @return string SQL fragment for tokans.created (or other column)
 */
function progress_sql_date_clause($con, $like, $column = 'created')
{
    $range = progress_range_from_like($con, $like);
    $column = preg_replace('/[^a-zA-Z0-9_.]/', '', $column);

    return $column . " >= '" . $range['start'] . "' AND " . $column . " < '" . $range['end'] . "'";
}

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

function progress_tokans_subquery($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    if ($con) {
        $date_clause = progress_sql_date_clause($con, $like);
    } else {
        $like_esc = addslashes(rtrim((string) $like, '%'));
        $date_clause = "created LIKE '" . $like_esc . "%'";
    }

    return "(SELECT id FROM tokans WHERE branch_id = '$br_id' AND status = 1 AND $date_clause)";
}

/** @deprecated Use JOIN + progress_sql_date_clause instead of IN (subquery). */

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
    $date_clause = progress_sql_date_clause($con, $like, 't.created');
    $sql = "SELECT ibd.doctor_id, COUNT(DISTINCT ibd.tokan_no) AS cnt
        FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        WHERE ibd.branch_id = '$br_id' AND ibd.status = '2' AND t.status = 1
        AND $date_clause
        AND ir.item_id IN ($item_ids_sql)
        GROUP BY ibd.doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

/**
 * Count item_by_doctor.tokan_no rows (legacy reports used mysqli_num_rows on tokan_no list).
 *
 * @return array<int, int>
 */
function progress_ibd_tokan_row_count_by_doctor($con, $br_id, $like, $item_ids_sql)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like, 't.created');
    $sql = "SELECT ibd.doctor_id, COUNT(ibd.tokan_no) AS cnt
        FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        WHERE ibd.branch_id = '$br_id' AND ibd.status = '2' AND t.status = 1
        AND $date_clause
        AND ir.item_id IN ($item_ids_sql)
        GROUP BY ibd.doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

/**
 * Procedure tokan rows excluding SVD/DNC catalog items (gynae daily report).
 *
 * @return array<int, int>
 */
function progress_gynae_procedure_tokan_count_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like, 't.created');
    $exclude_proc = '473, 1119, 1314, 472, 1118, 1313';
    $sql = "SELECT ibd.doctor_id, COUNT(ibd.tokan_no) AS cnt
        FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        INNER JOIN items i ON ir.item_id = i.id
        WHERE ibd.branch_id = '$br_id' AND ibd.status = '2' AND t.status = 1
        AND $date_clause AND i.category_id = 3 AND i.id NOT IN ($exclude_proc)
        GROUP BY ibd.doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

/**
 * Branch list for organization gynae print report.
 *
 * @return array<int, array{id: int, address: string, tag_name: string}>
 */
function progress_gynae_report_branches($con, $month_like)
{
    $date_clause = progress_sql_date_clause($con, $month_like, 'ibd.created');
    $gynae_items = '483, 1159, 1321, 1414, 473, 1119, 1314, 472, 1118, 1313';
    $sql = "SELECT DISTINCT b.id, b.address, b.tag_name FROM branchs b
        INNER JOIN item_by_doctor ibd ON ibd.branch_id = b.id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        WHERE b.status = '1' AND $date_clause AND ir.item_id IN ($gynae_items)
        ORDER BY b.id";
    $branches = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $id = (int) $row['id'];
            $branches[$id] = array(
                'id' => $id,
                'address' => (string) $row['address'],
                'tag_name' => (string) $row['tag_name'],
            );
        }
    }
    return $branches;
}

/**
 * Doctor IDs for gynae organization report (branch + month activity).
 *
 * @return int[]
 */
function progress_gynae_report_doctor_ids($con, $br_id, $month_like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $month_like, 'ibd.created');
    $gynae_items = '483, 1159, 1321, 1414, 473, 1119, 1314, 472, 1118, 1313';
    $sql = "SELECT DISTINCT t.doctor_id FROM tokans t
        WHERE t.doctor_id IN (
            SELECT DISTINCT ibd.doctor_id FROM item_by_doctor ibd
            INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
            WHERE ibd.branch_id = '$br_id' AND $date_clause AND ir.item_id IN ($gynae_items)
        )
        AND t.doctor_id IN (SELECT id FROM users WHERE branch_id = '$br_id')
        ORDER BY t.doctor_id";
    $ids = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $ids[] = (int) $row['doctor_id'];
        }
    }
    return $ids;
}

/**
 * @return array<int, array<int, int>> branch_id => doctor_id => count
 */
function progress_map_branch_doctor_int($con, $sql)
{
    $map = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $bid = (int) $row['branch_id'];
            $did = (int) $row['doctor_id'];
            if (!isset($map[$bid])) {
                $map[$bid] = array();
            }
            $map[$bid][$did] = (int) $row['cnt'];
        }
    }
    return $map;
}

/**
 * All branches: item_by_doctor tokan row counts by doctor.
 *
 * @return array<int, array<int, int>>
 */
function progress_gynae_org_ibd_tokan_counts($con, $like, $item_ids_sql)
{
    $date_clause = progress_sql_date_clause($con, $like, 't.created');
    $sql = "SELECT ibd.branch_id, ibd.doctor_id, COUNT(ibd.tokan_no) AS cnt
        FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        WHERE ibd.status = '2' AND t.status = 1 AND $date_clause
        AND ir.item_id IN ($item_ids_sql)
        GROUP BY ibd.branch_id, ibd.doctor_id";
    return progress_map_branch_doctor_int($con, $sql);
}

/**
 * @return array<int, array<int, int>>
 */
function progress_gynae_org_register_counts($con, $like)
{
    $date_clause = progress_sql_date_clause($con, $like);
    $sql = "SELECT branch_id, doctor_id, COUNT(*) AS cnt FROM gynae_register
        WHERE $date_clause
        GROUP BY branch_id, doctor_id";
    return progress_map_branch_doctor_int($con, $sql);
}

/**
 * @return array<int, array<int, int>>
 */
function progress_gynae_org_procedure_counts($con, $like)
{
    $date_clause = progress_sql_date_clause($con, $like, 't.created');
    $exclude_proc = '473, 1119, 1314, 472, 1118, 1313';
    $sql = "SELECT ibd.branch_id, ibd.doctor_id, COUNT(ibd.tokan_no) AS cnt
        FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        INNER JOIN items i ON ir.item_id = i.id
        WHERE ibd.status = '2' AND t.status = 1 AND $date_clause
        AND i.category_id = 3 AND i.id NOT IN ($exclude_proc)
        GROUP BY ibd.branch_id, ibd.doctor_id";
    return progress_map_branch_doctor_int($con, $sql);
}

/**
 * @return array<int, int[]> branch_id => sorted doctor ids
 */
function progress_gynae_org_doctors_by_branch($con, $month_like)
{
    $date_clause = progress_sql_date_clause($con, $month_like, 'ibd.created');
    $gynae_items = '483, 1159, 1321, 1414, 473, 1119, 1314, 472, 1118, 1313';
    $sql = "SELECT DISTINCT ibd.branch_id, t.doctor_id
        FROM item_by_doctor ibd
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN users u ON u.id = t.doctor_id AND u.branch_id = ibd.branch_id
        WHERE $date_clause AND ir.item_id IN ($gynae_items)
        ORDER BY ibd.branch_id, t.doctor_id";
    $by_branch = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $bid = (int) $row['branch_id'];
            $did = (int) $row['doctor_id'];
            if (!isset($by_branch[$bid])) {
                $by_branch[$bid] = array();
            }
            $by_branch[$bid][$did] = $did;
        }
    }
    foreach ($by_branch as $bid => $docs) {
        $by_branch[$bid] = array_values($docs);
    }
    return $by_branch;
}

/**
 * @param int[] $doctor_ids
 * @return array<int, string>
 */
function progress_user_names_by_ids($con, $doctor_ids)
{
    $doctor_ids = array_values(array_unique(array_map('intval', $doctor_ids)));
    if (count($doctor_ids) === 0) {
        return array();
    }
    $id_list = implode(',', $doctor_ids);
    $names = array();
    $run = mysqli_query($con, "SELECT id, u_name FROM users WHERE id IN ($id_list)");
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $names[(int) $row['id']] = (string) $row['u_name'];
        }
    }
    return $names;
}

/**
 * Full dataset for organization gynae print (HR + BK) — fixed query count regardless of branch count.
 *
 * @return array{
 *   branches: array<int, array{id: int, address: string, tag_name: string}>,
 *   doctors_by_branch: array<int, int[]>,
 *   doctor_names: array<int, string>,
 *   day_svd: array<int, array<int, int>>,
 *   month_svd: array<int, array<int, int>>,
 *   day_dnc: array<int, array<int, int>>,
 *   month_dnc: array<int, array<int, int>>,
 *   day_gynae: array<int, array<int, int>>,
 *   month_gynae: array<int, array<int, int>>,
 *   day_gynae_system: array<int, array<int, int>>,
 *   month_gynae_system: array<int, array<int, int>>,
 *   day_procedure: array<int, array<int, int>>,
 *   month_procedure: array<int, array<int, int>>
 * }
 */
function progress_gynae_organization_report_dataset($con, $day_like, $month_like)
{
    $svd_items = '472, 1118, 1313';
    $dnc_items = '473, 1119, 1314';
    $gynae_items = '483, 1159, 1321, 1414';

    $branches = progress_gynae_report_branches($con, $month_like);
    $doctors_by_branch = progress_gynae_org_doctors_by_branch($con, $month_like);

    $all_doctor_ids = array();
    foreach ($doctors_by_branch as $ids) {
        foreach ($ids as $id) {
            $all_doctor_ids[] = (int) $id;
        }
    }

    return array(
        'branches' => $branches,
        'doctors_by_branch' => $doctors_by_branch,
        'doctor_names' => progress_user_names_by_ids($con, $all_doctor_ids),
        'day_svd' => progress_gynae_org_ibd_tokan_counts($con, $day_like, $svd_items),
        'month_svd' => progress_gynae_org_ibd_tokan_counts($con, $month_like, $svd_items),
        'day_dnc' => progress_gynae_org_ibd_tokan_counts($con, $day_like, $dnc_items),
        'month_dnc' => progress_gynae_org_ibd_tokan_counts($con, $month_like, $dnc_items),
        'day_gynae' => progress_gynae_org_ibd_tokan_counts($con, $day_like, $gynae_items),
        'month_gynae' => progress_gynae_org_ibd_tokan_counts($con, $month_like, $gynae_items),
        'day_gynae_system' => progress_gynae_org_register_counts($con, $day_like),
        'month_gynae_system' => progress_gynae_org_register_counts($con, $month_like),
        'day_procedure' => progress_gynae_org_procedure_counts($con, $day_like),
        'month_procedure' => progress_gynae_org_procedure_counts($con, $month_like),
    );
}

/**
 * Render organization gynae report table body (shared by bk/print_gynae_report.php and hr/print_gynae_report.php).
 */
function progress_render_gynae_organization_report($dataset, $company_name, $date_label)
{
    $branches = $dataset['branches'];
    $doctors_by_branch = $dataset['doctors_by_branch'];
    $doctor_names = $dataset['doctor_names'];
    $has_data = false;

    $company_safe = htmlspecialchars((string) $company_name, ENT_QUOTES, 'UTF-8');
    $date_label_safe = htmlspecialchars((string) $date_label, ENT_QUOTES, 'UTF-8');

    echo '
<table border = "solid">
<caption>
    <h2>' . $company_safe . '</h2>
    <h3>GYNAE REPORT DATE ' . $date_label_safe . '</h3>
</caption>
';

    foreach ($branches as $br_row) {
        $br_id = $br_row['id'];
        $address = $br_row['address'];
        $doctor_ids = $doctors_by_branch[$br_id] ?? array();
        ?>
        <tr>
            <th colspan = "12"><h2><?php echo $address; ?></h2></th>
        </tr>
        <tr>
            <th>S#</th>
            <th>NAME</th>
            <th>SVD</th>
            <th>TOTAL SVD</th>
            <th>DNC</th>
            <th>TOTAL DNC</th>
            <th>PROCEDURE</th>
            <th>TOTAL PROCEDURE</th>
            <th>GYNAE TOKEN</th>
            <th>TOTAL TOKEN</th>
            <th>GYNAE SYSTEM</th>
            <th>TOTAL SYSTEM</th>
        </tr>
<?php
        $s = 0;
        $total_svd = 0;
        $total_dnc = 0;
        $total_procedure = 0;
        $total_gynaes = 0;
        $total_gynae_systems = 0;
        $total_total_svd = 0;
        $total_total_dnc = 0;
        $total_total_procedure = 0;
        $total_total_gynae = 0;
        $total_total_gynae_system = 0;

        if (count($doctor_ids) > 0) {
            foreach ($doctor_ids as $doctor) {
                $doctor = (int) $doctor;
                $has_data = true;
                $s = $s + 1;
                $svds = $dataset['day_svd'][$br_id][$doctor] ?? 0;
                $total_svd = $total_svd + $svds;
                $total_svds = $dataset['month_svd'][$br_id][$doctor] ?? 0;
                $total_total_svd = $total_total_svd + $total_svds;
                $total_dncs = $dataset['month_dnc'][$br_id][$doctor] ?? 0;
                $total_total_dnc = $total_total_dnc + $total_dncs;
                $dncs = $dataset['day_dnc'][$br_id][$doctor] ?? 0;
                $total_dnc = $total_dnc + $dncs;
                $gynaes = $dataset['day_gynae'][$br_id][$doctor] ?? 0;
                $total_gynaes = $total_gynaes + $gynaes;
                $total_gynae = $dataset['month_gynae'][$br_id][$doctor] ?? 0;
                $total_total_gynae = $total_total_gynae + $total_gynae;
                $gynae_systems = $dataset['day_gynae_system'][$br_id][$doctor] ?? 0;
                $total_gynae_systems = $total_gynae_systems + $gynae_systems;
                $total_gynae_system = $dataset['month_gynae_system'][$br_id][$doctor] ?? 0;
                $total_total_gynae_system = $total_total_gynae_system + $total_gynae_system;
                $procedures = $dataset['day_procedure'][$br_id][$doctor] ?? 0;
                $total_procedure = $total_procedure + $procedures;
                $total_procedures = $dataset['month_procedure'][$br_id][$doctor] ?? 0;
                $total_total_procedure = $total_total_procedure + $total_procedures;

                $doctor_name = $doctor_names[$doctor] ?? ('Doctor #' . $doctor);
                echo ' <tr style = "text-align: right;">
                <td>' . $s . '</td>
                <td style = "text-align: left;">' . $doctor_name . '</td>
                <td>' . $svds . '</td>
                <td>' . $total_svds . '</td>
                <td>' . $dncs . '</td>
                <td>' . $total_dncs . '</td>
                <td>' . $procedures . '</td>
                <td>' . $total_procedures . '</td>
                <td>' . $gynaes . '</td>
                <td>' . $total_gynae . '</td>
                <td>' . $gynae_systems . '</td>
                <td>' . $total_gynae_system . '</td>
            </tr>';
            }
            echo '<tr style = "text-align: right;">
                <th></th>
                <th></th>
                <th>' . $total_svd . '</th>
                <th>' . $total_total_svd . '</th>
                <th>' . $total_dnc . '</th>
                <th>' . $total_total_dnc . '</th>
                <th>' . $total_procedure . '</th>
                <th>' . $total_total_procedure . '</th>
                <th>' . $total_gynaes . '</th>
                <th>' . $total_total_gynae . '</th>
                <th>' . $total_gynae_systems . '</th>
                <th>' . $total_total_gynae_system . '</th>
            </tr>';
        }
    }

    echo '</table>';
    if (!$has_data) {
        ycdo_echo_report_no_data_found();
    }
}


function progress_opd_count_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like);
    $sql = "SELECT doctor_id, COUNT(id) AS cnt FROM tokans
        WHERE tokan_type_id < 9 AND status = 1 AND branch_id = '$br_id' AND $date_clause
        GROUP BY doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_gynae_register_count_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like);
    $sql = "SELECT doctor_id, COUNT(*) AS cnt FROM gynae_register
        WHERE branch_id = '$br_id' AND $date_clause
        GROUP BY doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_opd_count_by_doctor_lte10($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like);
    $sql = "SELECT doctor_id, COUNT(id) AS cnt FROM tokans
        WHERE branch_id = '$br_id' AND status = 1 AND tokan_type_id <= 10 AND $date_clause
        GROUP BY doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_gynae_token_count_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like, 'ibd.created');
    $sql = "SELECT ibd.doctor_id, COUNT(DISTINCT ibd.tokan_no) AS cnt
        FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        WHERE ibd.branch_id = '$br_id' AND ibd.category_id = '41' AND ibd.status = '2' AND t.status = 1
        AND $date_clause
        GROUP BY ibd.doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

/**
 * Gynae item_by_doctor row counts (matches legacy COUNT(id) on category 41).
 *
 * @return array<int, int>
 */
function progress_gynae_ibd_row_count_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like, 'created');
    $sql = "SELECT doctor_id, COUNT(id) AS cnt FROM item_by_doctor
        WHERE branch_id = '$br_id' AND category_id = '41' AND $date_clause
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
    $date_clause = progress_sql_date_clause($con, $month_like, 'ibd.created');
    $sql = "SELECT DISTINCT ibd.doctor_id AS id FROM item_by_doctor ibd
        WHERE ibd.branch_id = '$br_id' AND ibd.category_id = '41' AND $date_clause";
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
    $date_clause = progress_sql_date_clause($con, $like, 'referral_patient_created');
    $sql = "SELECT from_user_id AS doctor_id, COUNT(*) AS cnt FROM referral_patients
        WHERE $date_clause $status_sql
        GROUP BY from_user_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

/**
 * @return array<int, array<int, array{count_token: int, total_cash: float}>>
 */
function progress_category_stats_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like, 'item_by_doctor.created');
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
        WHERE $date_clause AND branch_id = '$br_id'
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
    $date_clause = progress_sql_date_clause($con, $like, 'referral_patient_created');
    $sql = "SELECT to_user_id AS doctor_id, COUNT(*) AS cnt FROM referral_patients
        WHERE $date_clause AND referral_patient_status > '1'
        GROUP BY to_user_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

function progress_cash_sum_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like);
    $sql = "SELECT doctor_id, COALESCE(SUM(cash), 0) AS total FROM tokans
        WHERE status = 1 AND branch_id = '$br_id' AND $date_clause
        GROUP BY doctor_id";
    return progress_map_float($con, $sql, 'doctor_id', 'total');
}

/**
 * @return array<int, float>
 */
function progress_cash_received_sum_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like);
    $sql = "SELECT doctor_id, COALESCE(SUM(cash_received), 0) AS total FROM tokans
        WHERE status = 1 AND branch_id = '$br_id' AND $date_clause
        GROUP BY doctor_id";
    return progress_map_float($con, $sql, 'doctor_id', 'total');
}

/**
 * Lab diagnostic stats per doctor — one query for all doctors (replaces per-doctor category_id IN (2) loops).
 *
 * Matches legacy: COUNT(DISTINCT item_by_doctor.tokan_no) with tokans.status = 1 and items.category_id = 2.
 *
 * @return array<int, array{lab_count: int, lab_amount: float}>
 */
function progress_ibd_lab_stats_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like, 'ibd.created');
    $sql = "SELECT ibd.doctor_id,
        COUNT(DISTINCT ibd.tokan_no) AS lab_count,
        COALESCE(SUM(ibd.sale_price), 0) AS lab_amount
        FROM item_by_doctor ibd
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        INNER JOIN items i ON ir.item_id = i.id AND i.category_id = 2
        INNER JOIN tokans t ON ibd.tokan_no = t.id AND ibd.branch_id = t.branch_id AND t.status = 1
        WHERE ibd.branch_id = '$br_id' AND ibd.status = '2' AND $date_clause
        GROUP BY ibd.doctor_id";
    $stats = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $stats[(int) $row['doctor_id']] = array(
                'lab_count' => (int) $row['lab_count'],
                'lab_amount' => (float) $row['lab_amount'],
            );
        }
    }
    return $stats;
}

/**
 * Lab token cash/count per doctor (monthly lab progress report).
 *
 * @return array<int, array{lab_cash: float, lab_count: int}>
 */
function progress_lab_token_cash_by_doctor($con, $br_id, $like)
{
    $stats = array();
    foreach (progress_ibd_lab_stats_by_doctor($con, $br_id, $like) as $doctor_id => $row) {
        $stats[$doctor_id] = array(
            'lab_cash' => $row['lab_amount'],
            'lab_count' => $row['lab_count'],
        );
    }
    return $stats;
}

/**
 * @return array<int, array{id: int, u_name: string}>
 */
function progress_lab_monthly_doctors($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like, 't.created');
    $sql = "SELECT DISTINCT u.id, u.u_name FROM users u
        INNER JOIN tokans t ON t.doctor_id = u.id
        WHERE u.role_id = '3' AND t.branch_id = '$br_id' AND $date_clause
        ORDER BY u.u_name";
    $doctors = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $id = (int) $row['id'];
            $doctors[$id] = array(
                'id' => $id,
                'u_name' => (string) $row['u_name'],
            );
        }
    }
    return $doctors;
}

/**
 * Pre-aggregated maps for print_progress_report_monthly_lab.php (fixed query count).
 *
 * @return array{
 *   doctors: array<int, array{id: int, u_name: string}>,
 *   collection_map: array<int, float>,
 *   opd_map: array<int, int>,
 *   cons_map: array<int, int>,
 *   lab_map: array<int, array{lab_cash: float, lab_count: int}>
 * }
 */
function progress_lab_monthly_report_maps($con, $br_id, $like)
{
    return array(
        'doctors' => progress_lab_monthly_doctors($con, $br_id, $like),
        'collection_map' => progress_cash_received_sum_by_doctor($con, $br_id, $like),
        'opd_map' => progress_opd_count_by_doctor_lte10($con, $br_id, $like),
        'cons_map' => progress_tokan_count_by_item_category_doctor($con, $br_id, $like, 29),
        'lab_map' => progress_lab_token_cash_by_doctor($con, $br_id, $like),
    );
}

function progress_lab_stats_by_doctor($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like, 't.created');
    $sql = "SELECT t.doctor_id, COUNT(t.id) AS token_cnt, COALESCE(SUM(t.cash_received), 0) AS cash_sum
        FROM tokans t
        INNER JOIN item_by_doctor ibd ON ibd.tokan_no = t.id AND ibd.branch_id = t.branch_id AND ibd.status = '2'
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        INNER JOIN items i ON ir.item_id = i.id AND i.category_id = 2
        WHERE t.doctor_id > 0 AND t.status = 1 AND t.branch_id = '$br_id' AND $date_clause
        GROUP BY t.doctor_id";
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
    $stats = array();
    foreach (progress_ibd_lab_stats_by_doctor($con, $br_id, $like) as $doctor_id => $row) {
        $stats[$doctor_id] = array(
            'count' => $row['lab_count'],
            'cash' => $row['lab_amount'],
        );
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
    $date_clause = progress_sql_date_clause($con, $like, 'item_by_doctor.created');
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
        WHERE branch_id = '$br_id' AND $date_clause
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
    $date_clause = progress_sql_date_clause($con, $like, 'referral_patient_created');
    $sql = "SELECT from_user_id AS doctor_id, COUNT(*) AS cnt FROM referral_patients
        WHERE $date_clause AND referral_patient_status > '1' AND branch_id = '$br_id'
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
 * @return array<int, array{lab_count: int, lab_amount: float}>
 */
function progress_ibd_lab_stats_by_doctor_range($con, $br_id, $start_at, $end_at)
{
    $br_id = (int) $br_id;
    $start_at = mysqli_real_escape_string($con, (string) $start_at);
    $end_at = mysqli_real_escape_string($con, (string) $end_at);
    $sql = "SELECT ibd.doctor_id,
        COUNT(DISTINCT ibd.tokan_no) AS lab_count,
        COALESCE(SUM(ibd.sale_price), 0) AS lab_amount
        FROM item_by_doctor ibd
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        INNER JOIN items i ON ir.item_id = i.id AND i.category_id = 2
        INNER JOIN tokans t ON ibd.tokan_no = t.id AND ibd.branch_id = t.branch_id AND t.status = 1
        WHERE ibd.branch_id = '$br_id' AND ibd.status = '2'
        AND ibd.created >= '$start_at' AND ibd.created < '$end_at'
        GROUP BY ibd.doctor_id";
    $stats = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $stats[(int) $row['doctor_id']] = array(
                'lab_count' => (int) $row['lab_count'],
                'lab_amount' => (float) $row['lab_amount'],
            );
        }
    }
    return $stats;
}

function progress_dia_patient_stats_by_doctor_range($con, $br_id, $start_at, $end_at)
{
    $stats = array();
    foreach (progress_ibd_lab_stats_by_doctor_range($con, $br_id, $start_at, $end_at) as $doctor_id => $row) {
        $stats[$doctor_id] = array(
            'count' => $row['lab_count'],
            'cash' => $row['lab_amount'],
        );
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
    $date_clause = progress_sql_date_clause($con, $like, 't.created');
    $cons_items = '489, 849, 850, 1415, 1327, 1139, 1141, 1477, 1154, 476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435';
    $sql = "SELECT t.doctor_id, COALESCE(SUM(t.cash), 0) AS total FROM tokans t
        WHERE t.status = 1 AND t.branch_id = '$br_id' AND $date_clause
        AND (
            t.tokan_type_id < 9
            OR EXISTS (
                SELECT 1 FROM item_by_doctor ibd
                INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
                WHERE ibd.tokan_no = t.id AND ibd.status = '2' AND ir.item_id IN ($cons_items)
            )
        )
        GROUP BY t.doctor_id";
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
    $date_clause = progress_sql_date_clause($con, $like);
    $ids = array();
    $run = mysqli_query($con, "SELECT DISTINCT branch_id FROM tokans WHERE $date_clause ORDER BY branch_id");
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
    $date_clause = progress_sql_date_clause($con, $like);
    $sql = "SELECT branch_id, COUNT(id) AS cnt FROM tokans
        WHERE tokan_type_id < 9 AND status = 1 AND $date_clause
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
    $date_clause = progress_sql_date_clause($con, $like, 't.created');
    $sql = "SELECT ibd.branch_id, COUNT(ibd.tokan_no) AS cnt
        FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        WHERE $date_clause AND t.status = 1 AND ibd.status = '2'
        AND ir.item_id IN ($item_ids_sql)
        GROUP BY ibd.branch_id";
    return progress_map_int($con, $sql, 'branch_id', 'cnt');
}

/**
 * @return array<int, int>
 */
function progress_procedure_tokan_count_by_branch($con, $like)
{
    $date_clause = progress_sql_date_clause($con, $like, 't.created');
    $sql = "SELECT ibd.branch_id, COUNT(ibd.tokan_no) AS cnt
        FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        INNER JOIN items i ON ir.item_id = i.id
        WHERE $date_clause AND t.status = 1 AND ibd.status = '2'
        AND i.category_id = '3'
        GROUP BY ibd.branch_id";
    return progress_map_int($con, $sql, 'branch_id', 'cnt');
}

/**
 * @return array<int, int>
 */
function progress_gynae_register_count_by_branch($con, $like)
{
    $date_clause = progress_sql_date_clause($con, $like);
    $sql = "SELECT branch_id, COUNT(*) AS cnt FROM gynae_register
        WHERE $date_clause
        GROUP BY branch_id";
    return progress_map_int($con, $sql, 'branch_id', 'cnt');
}

/**
 * Distinct tokans per doctor for items in a catalog category (e.g. consultant OPD, procedures).
 *
 * @return array<int, int>
 */
function progress_tokan_count_by_item_category_doctor($con, $br_id, $like, $category_id)
{
    $br_id = (int) $br_id;
    $category_id = (int) $category_id;
    $date_clause = progress_sql_date_clause($con, $like, 't.created');
    $sql = "SELECT t.doctor_id, COUNT(DISTINCT t.id) AS cnt
        FROM tokans t
        INNER JOIN item_by_doctor ibd ON ibd.tokan_no = t.id AND ibd.branch_id = t.branch_id AND ibd.status = '2'
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        INNER JOIN items i ON ir.item_id = i.id AND i.category_id = '$category_id'
        WHERE t.status = 1 AND t.branch_id = '$br_id' AND $date_clause
        GROUP BY t.doctor_id";
    return progress_map_int($con, $sql, 'doctor_id', 'cnt');
}

/**
 * Doctors for BK gynae monthly progress (OPD + gynae items + referrals in month).
 *
 * @return array<int, array{id: int, u_name: string}>
 */
function progress_gynae_progress_monthly_doctors($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $t_clause = progress_sql_date_clause($con, $like, 't.created');
    $ibd_clause = progress_sql_date_clause($con, $like, 'ibd.created');
    $ref_clause = progress_sql_date_clause($con, $like, 'rp.referral_patient_created');
    $gynae_items = '483, 1159, 1321, 1414';

    $sql = "SELECT u.id, u.u_name FROM users u
        WHERE (
            u.role_id = '3'
            AND u.id IN (SELECT DISTINCT t.doctor_id FROM tokans t WHERE t.branch_id = '$br_id' AND $t_clause)
            AND u.id IN (
                SELECT DISTINCT ibd.doctor_id FROM item_by_doctor ibd
                INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
                WHERE ibd.branch_id = '$br_id' AND $ibd_clause AND ir.item_id IN ($gynae_items)
            )
        ) OR u.id IN (
            SELECT rp.from_user_id FROM referral_patients rp
            WHERE rp.branch_id = '$br_id' AND $ref_clause AND rp.referral_patient_status > 1
        )
        ORDER BY u.u_name";

    $doctors = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $id = (int) $row['id'];
            $doctors[$id] = array(
                'id' => $id,
                'u_name' => (string) $row['u_name'],
            );
        }
    }
    return $doctors;
}

/**
 * Doctor rows for paginated monthly progress (half1–half6).
 *
 * @return array<int, array{doctor_id: int, u_name: string, tag_name: string, opd: int, cash_collection: float}>
 */
/**
 * Pre-aggregated maps for monthly progress half1–half6 pages.
 *
 * @return array<string, mixed>
 */
function progress_monthly_half_batch_maps($con, $br_id, $like)
{
    return array(
        'cash_map' => progress_cash_sum_by_doctor($con, $br_id, $like),
        'dia_stats' => progress_dia_patient_stats_by_doctor($con, $br_id, $like),
        'item_rows' => progress_item_row_counts_by_doctor($con, $br_id, $like),
        'gynae_system_map' => progress_gynae_register_count_by_doctor($con, $br_id, $like),
        'refer_from' => progress_referral_from_count_by_branch($con, $br_id, $like),
        'refer_to' => progress_referral_to_count_by_doctor($con, $like),
    );
}

function progress_monthly_doctors_opd_cash_rows($con, $br_id, $like, $offset = null, $limit = null)
{
    $br_id = (int) $br_id;
    $date_clause = progress_sql_date_clause($con, $like, 'tokans.created');
    $limit_sql = '';
    if ($offset !== null && $limit !== null) {
        $limit_sql = ' LIMIT ' . (int) $offset . ', ' . (int) $limit;
    }

    $sql = "SELECT tokans.doctor_id, users.u_name, branchs.tag_name,
        COUNT(CASE WHEN tokans.tokan_type_id <= 100 THEN tokans.tokan_type_id END) AS opd,
        COALESCE(SUM(tokans.cash), 0) AS cash_collection
        FROM tokans
        INNER JOIN users ON tokans.doctor_id = users.id
        INNER JOIN branchs ON users.branch_id = branchs.id
        WHERE $date_clause AND tokans.branch_id = '$br_id' AND tokans.status = '1'
        GROUP BY tokans.doctor_id, users.u_name, branchs.tag_name
        ORDER BY tokans.doctor_id" . $limit_sql;

    $rows = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $rows[] = array(
                'doctor_id' => (int) $row['doctor_id'],
                'u_name' => (string) $row['u_name'],
                'tag_name' => (string) $row['tag_name'],
                'opd' => (int) $row['opd'],
                'cash_collection' => (float) $row['cash_collection'],
            );
        }
    }
    return $rows;
}

/**
 * Single-branch daily summary metrics (replaces many LIKE queries on one row).
 *
 * @return array<string, float|int>
 */
function progress_single_branch_day_summary($con, $br_id, $like)
{
    $br_id = (int) $br_id;
    $date_clause_t = progress_sql_date_clause($con, $like, 't.created');
    $date_clause_ibd = progress_sql_date_clause($con, $like, 'ibd.created');
    $date_clause_gr = progress_sql_date_clause($con, $like);
    $date_clause_ref = progress_sql_date_clause($con, $like, 'referral_patient_created');

    $out = array(
        'collection' => 0.0,
        'opd' => 0,
        'cons_opd' => 0,
        'svd' => 0,
        'dnc' => 0,
        'procedure' => 0,
        'admission' => 0,
        'referred' => 0,
        'usg' => 0,
        'gynae_system' => 0,
        'lab_cash' => 0.0,
    );

    $run = mysqli_query($con, "SELECT COALESCE(SUM(cash_received), 0) AS total FROM tokans
        WHERE status = 1 AND branch_id = '$br_id' AND $date_clause_t");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $out['collection'] = (float) $row['total'];
    }

    $run = mysqli_query($con, "SELECT COUNT(id) AS total FROM tokans
        WHERE tokan_type_id < 9 AND status = 1 AND branch_id = '$br_id' AND $date_clause_t");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $out['opd'] = (int) $row['total'];
    }

    $cons_items = '489, 849, 850, 1415, 1327, 1139, 1141, 1477, 1154';
    $run = mysqli_query($con, "SELECT COUNT(ibd.tokan_no) AS total FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        WHERE t.status = 1 AND ibd.branch_id = '$br_id' AND ibd.status = '2'
        AND $date_clause_t AND ir.item_id IN ($cons_items)");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $out['cons_opd'] = (int) $row['total'];
    }

    $svd_items = '472, 1118, 1313, 1577';
    $run = mysqli_query($con, "SELECT COUNT(ibd.tokan_no) AS total FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        WHERE t.status = 1 AND ibd.branch_id = '$br_id' AND ibd.status = '2'
        AND $date_clause_t AND ir.item_id IN ($svd_items)");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $out['svd'] = (int) $row['total'];
    }

    $dnc_items = '473, 1119, 1314, 1578';
    $run = mysqli_query($con, "SELECT COUNT(ibd.tokan_no) AS total FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        WHERE t.status = 1 AND ibd.branch_id = '$br_id' AND ibd.status = '2'
        AND $date_clause_t AND ir.item_id IN ($dnc_items)");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $out['dnc'] = (int) $row['total'];
    }

    $exclude_proc = '473, 1119, 1314, 472, 1118, 1313';
    $run = mysqli_query($con, "SELECT COUNT(DISTINCT ibd.tokan_no) AS total FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        INNER JOIN items i ON ir.item_id = i.id
        WHERE t.status = 1 AND ibd.branch_id = '$br_id' AND ibd.status = '2'
        AND $date_clause_t AND i.category_id = 3 AND i.id NOT IN ($exclude_proc)");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $out['procedure'] = (int) $row['total'];
    }

    $admission_items = '444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301, 1579, 1580, 1741, 1742, 1743, 1744';
    $run = mysqli_query($con, "SELECT COUNT(ibd.tokan_no) AS total FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        WHERE t.status = 1 AND ibd.branch_id = '$br_id' AND ibd.status = '2'
        AND $date_clause_t AND ir.item_id IN ($admission_items)");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $out['admission'] = (int) $row['total'];
    }

    $run = mysqli_query($con, "SELECT COUNT(*) AS total FROM referral_patients
        WHERE $date_clause_ref AND referral_patient_status > '1'");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $out['referred'] = (int) $row['total'];
    }

    $usg_items = '476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435';
    $run = mysqli_query($con, "SELECT COUNT(ibd.tokan_no) AS total FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        WHERE t.status = 1 AND ibd.branch_id = '$br_id' AND ibd.status = '2'
        AND $date_clause_t AND ir.item_id IN ($usg_items)");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $out['usg'] = (int) $row['total'];
    }

    $run = mysqli_query($con, "SELECT COUNT(*) AS total FROM gynae_register
        WHERE branch_id = '$br_id' AND $date_clause_gr");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $out['gynae_system'] = (int) $row['total'];
    }

    $run = mysqli_query($con, "SELECT COALESCE(SUM(t.cash_received), 0) AS total FROM tokans t
        INNER JOIN item_by_doctor ibd ON ibd.tokan_no = t.id AND ibd.branch_id = t.branch_id AND ibd.status = '2'
        INNER JOIN item_register_to_branches ir ON ibd.item_id = ir.id AND ir.branch_id = ibd.branch_id
        INNER JOIN items i ON ir.item_id = i.id AND i.category_id = 2
        WHERE t.status = 1 AND t.branch_id = '$br_id' AND $date_clause_t");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        $out['lab_cash'] = (float) $row['total'];
    }

    return $out;
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
    $range = ycdo_sql_day_range($date_esc);
    $start = mysqli_real_escape_string($con, $range['start']);
    $end = mysqli_real_escape_string($con, $range['end']);
    $sql = "SELECT branch_id, COUNT(id) AS cnt FROM tokans
        WHERE tokan_type_id < 9 AND status = 1
        AND created >= '$start' AND created < '$end'
        GROUP BY branch_id";
    return progress_map_int($con, $sql, 'branch_id', 'cnt');
}

/**
 * Aggregate item rows for tokens created on the report day (tokans-led join).
 *
 * @return array<int, array<string, int>>
 */
function progress_item_metrics_by_branch_day($con, $date_esc)
{
    $range = ycdo_sql_day_range($date_esc);
    $start = mysqli_real_escape_string($con, $range['start']);
    $end = mysqli_real_escape_string($con, $range['end']);
    $sql = "SELECT t.branch_id,
        COUNT(CASE WHEN ibd.category_id = 29 THEN 1 END) AS cons,
        COUNT(CASE WHEN ibd.category_id = 40 THEN 1 END) AS admissions,
        COUNT(CASE WHEN ibd.category_id = 3 THEN 1 END) AS procedures,
        COUNT(CASE WHEN ibd.category_id = 37 THEN 1 END) AS svds,
        COUNT(CASE WHEN ibd.category_id = 38 THEN 1 END) AS dncs,
        COUNT(CASE WHEN ibd.category_id = 39 THEN 1 END) AS usgs,
        COUNT(CASE WHEN ibd.category_id = 41 THEN 1 END) AS gynae
    FROM tokans t
    INNER JOIN item_by_doctor ibd ON ibd.tokan_no = t.id AND ibd.branch_id = t.branch_id
        AND ibd.status = 2 AND ibd.category_id IN (3, 29, 37, 38, 39, 40, 41)
    WHERE t.status = 1 AND t.created >= '$start' AND t.created < '$end'
    GROUP BY t.branch_id";

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
    $range = ycdo_sql_day_range($date_esc);
    $start = mysqli_real_escape_string($con, $range['start']);
    $end = mysqli_real_escape_string($con, $range['end']);
    $sql = "SELECT branch_id, COUNT(*) AS cnt FROM gynae_register
        WHERE created >= '$start' AND created < '$end'
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
    $date_esc = mysqli_real_escape_string($con, substr((string) $date, 0, 10));
    $opd = progress_opd_count_by_branch_day($con, $date_esc);
    $item = progress_item_metrics_by_branch_day($con, $date_esc);
    $gynae_system = progress_gynae_register_count_by_branch_day($con, $date_esc);

    $branch_ids = array_unique(array_merge(
        array_keys($opd),
        array_keys($item),
        array_keys($gynae_system)
    ));
    sort($branch_ids, SORT_NUMERIC);

    $branch_tags = array();
    if (count($branch_ids) > 0) {
        $id_list = implode(',', array_map('intval', $branch_ids));
        $run = mysqli_query($con, "SELECT id, tag_name FROM branchs WHERE id IN ($id_list)");
        if ($run) {
            while ($row = mysqli_fetch_assoc($run)) {
                $branch_tags[(int) $row['id']] = (string) $row['tag_name'];
            }
        }
    }

    return array(
        'branch_ids' => $branch_ids,
        'branch_tags' => $branch_tags,
        'opd' => $opd,
        'item' => $item,
        'gynae_system' => $gynae_system,
    );
}
