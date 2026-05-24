<?php

/**
 * Gestational-age gynae list reports (LMP in gynae_register.weeks).
 *
 * @return string SQL SELECT ... GROUP BY (no trailing semicolon)
 */
function ycdo_gynae_gestational_report_sql($range, $br_id = 0)
{
    $range = in_array($range, ['lt4', '4to8', 'gt8'], true) ? $range : 'lt4';
    $br_id = (int) $br_id;
    $monthExpr = 'TIMESTAMPDIFF(MONTH, gynae_register.weeks, CURDATE())';

    $where = " gynae_register.status = '1'
        AND gynae_register.weeks IS NOT NULL
        AND gynae_register.weeks != '0000-00-00'
        AND gynae_register.weeks <= CURDATE() ";

    if ($range === 'lt4') {
        $where .= " AND {$monthExpr} < 4 ";
    } elseif ($range === '4to8') {
        $where .= " AND {$monthExpr} >= 4 AND {$monthExpr} < 8 ";
    } else {
        $where .= " AND {$monthExpr} >= 8 ";
    }

    if ($br_id > 0) {
        $where .= " AND gynae_register.branch_id = '{$br_id}' ";
    }

    return "SELECT
        gynae_register.id,
        gynae_register.token_no,
        gynae_register.next_visit_date,
        gynae_register.weeks,
        patients.name,
        gynae_register.phone,
        gynae_register.created,
        branchs.tag_name,
        users.u_name,
        {$monthExpr} AS gestational_months,
        COUNT(gynae_register_history.id) AS total_visits
    FROM gynae_register
    INNER JOIN users ON users.id = gynae_register.doctor_id
    INNER JOIN branchs ON gynae_register.branch_id = branchs.id
    LEFT JOIN gynae_register_history ON gynae_register.id = gynae_register_history.gynae_register_id
    INNER JOIN tokans ON gynae_register.token_no = tokans.id
    INNER JOIN patients ON tokans.patient_id = patients.id
    WHERE {$where}
    GROUP BY gynae_register.id
    ORDER BY gynae_register.weeks ASC";
}

function ycdo_gynae_gestational_report_title($range)
{
    if ($range === 'lt4') {
        return 'GESTATIONAL AGE < 4 MONTHS';
    }
    if ($range === '4to8') {
        return 'GESTATIONAL AGE > 4 MONTHS & < 8 MONTHS';
    }
    return 'GESTATIONAL AGE > 8 MONTHS';
}

function ycdo_gynae_branch_options_html($con, $selected_br_id, $include_all = true)
{
    $selected_br_id = (int) $selected_br_id;
    $html = '';
    if ($include_all) {
        $sel = $selected_br_id === 0 ? ' selected' : '';
        $html .= '<option value="0"' . $sel . '>ALL BRANCHES</option>';
    }
    $run_br = mysqli_query($con, "SELECT id, address FROM branchs WHERE status = '1' ORDER BY address ASC");
    if ($run_br && mysqli_num_rows($run_br) > 0) {
        while ($row_br = mysqli_fetch_array($run_br)) {
            $bid = (int) $row_br['id'];
            $sel = ($selected_br_id === $bid) ? ' selected' : '';
            $html .= '<option value="' . $bid . '"' . $sel . '>' . htmlspecialchars($row_br['address']) . '</option>';
        }
    }
    return $html;
}
