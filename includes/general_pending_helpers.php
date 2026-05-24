<?php

require_once __DIR__ . '/report_helpers.php';

/**
 * @return array{br_id: int, from_date: string, to_date_label: string, to_date_end: string, from_input: string, to_input: string}
 */
function general_pending_parse_filters(array $get, int $defaultBranchId, $rangeMode = true)
{
    $br_id = $defaultBranchId;
    if (isset($get['br_id']) && $get['br_id'] !== '') {
        $br_id = (int) $get['br_id'];
    }

    $from_date = date('Y-m-d');
    if (isset($get['from_date']) && $get['from_date'] !== '') {
        $from_date = substr((string) $get['from_date'], 0, 10);
    }

    $to_date_label = $from_date;
    if ($rangeMode && isset($get['to_date']) && $get['to_date'] !== '') {
        $to_date_label = substr((string) $get['to_date'], 0, 10);
    }

    return array(
        'br_id' => $br_id,
        'from_date' => $from_date,
        'to_date_label' => $to_date_label,
        'to_date_end' => $to_date_label . ' 23:59:59',
        'from_input' => htmlspecialchars($from_date, ENT_QUOTES, 'UTF-8'),
        'to_input' => htmlspecialchars($to_date_label, ENT_QUOTES, 'UTF-8'),
    );
}

/**
 * @return string
 */
function general_pending_list_sql($con, $br_id, $from_date, $to_date_end)
{
    $from_esc = mysqli_real_escape_string($con, $from_date . ' 00:00:00');
    $to_esc = mysqli_real_escape_string($con, $to_date_end);
    $br_id = (int) $br_id;

    if ($br_id > 0) {
        return "SELECT bdpd.id, bdpd.created, branchs.tag_name, tokans.branch_id, patients.name,
                bdpd.ref_name, bdpd.ref_phone, bdpd.recommended_by, tokans.cash, tokans.cash_received,
                users.u_name, tokans.id AS token_no
            FROM branch_daily_pending_details bdpd
            INNER JOIN tokans ON bdpd.token_no = tokans.id
            INNER JOIN patients ON tokans.patient_id = patients.id
            INNER JOIN branchs ON tokans.branch_id = branchs.id
            INNER JOIN users ON tokans.user_id = users.id
            WHERE tokans.status = '1' AND tokans.branch_id = '$br_id'
                AND bdpd.created >= '$from_esc' AND bdpd.created <= '$to_esc'
                AND (tokans.cash - tokans.cash_received) > 0";
    }

    return "SELECT bdpd.id, bdpd.created, branchs.tag_name, patients.name,
            bdpd.ref_name, bdpd.ref_phone, bdpd.recommended_by, tokans.cash, tokans.cash_received,
            users.u_name, tokans.id AS token_no
        FROM branch_daily_pending_details bdpd
        INNER JOIN tokans ON bdpd.token_no = tokans.id AND tokans.status = '1'
        INNER JOIN patients ON tokans.patient_id = patients.id
        INNER JOIN branchs ON tokans.branch_id = branchs.id
        INNER JOIN users ON tokans.user_id = users.id
        WHERE bdpd.created >= '$from_esc' AND bdpd.created <= '$to_esc'
            AND (tokans.cash - tokans.cash_received) > 0
        GROUP BY bdpd.id";
}

/**
 * @return array<int, array<int, array{item_name: string, quantity: int|float}>>
 */
function general_pending_items_by_tokens($con, array $tokenIds)
{
    $tokenIds = array_values(array_unique(array_filter(array_map('intval', $tokenIds))));
    if ($tokenIds === array()) {
        return array();
    }

    $idList = implode(',', $tokenIds);
    $sql = "SELECT ibd.tokan_no, i.name AS item_name,
            CASE WHEN ibd.fix_dose = 0 THEN ibd.dose * ibd.feed * ibd.days ELSE ibd.fix_dose END AS quantity
        FROM item_by_doctor ibd
        INNER JOIN item_register_to_branches irb ON ibd.item_id = irb.id
        INNER JOIN items i ON irb.item_id = i.id
        WHERE ibd.tokan_no IN ($idList)
        ORDER BY ibd.tokan_no, i.name";

    $byToken = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $tid = (int) $row['tokan_no'];
            if (!isset($byToken[$tid])) {
                $byToken[$tid] = array();
            }
            $byToken[$tid][] = array(
                'item_name' => (string) $row['item_name'],
                'quantity' => $row['quantity'],
            );
        }
    }

    return $byToken;
}

/**
 * @return array<int, array<string, mixed>>
 */
function general_pending_fetch_rows($con, $sql)
{
    $rows = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $rows[] = $row;
        }
    }

    return $rows;
}
