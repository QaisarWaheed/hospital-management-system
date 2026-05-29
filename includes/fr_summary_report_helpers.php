<?php

require_once __DIR__ . '/report_helpers.php';

/**
 * @return array{start: string, end: string}
 */
function fr_summary_range_bounds($from_date, $to_date, $datetime_end = false)
{
    $start = substr((string) $from_date, 0, 19);
    if ($datetime_end) {
        $end = substr((string) $to_date, 0, 19);
    } else {
        $end = date('Y-m-d', strtotime('+1 day', strtotime(substr((string) $to_date, 0, 10))));
    }

    return array('start' => $start, 'end' => $end);
}

/**
 * @return array{cash: float, cash_received: float}
 */
function fr_summary_tokans_totals($con, $from_date, $to_date, $user_id, $branch_id, $datetime_end = false)
{
    $bounds = fr_summary_range_bounds($from_date, $to_date, $datetime_end);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);
    $user_id = (int) $user_id;
    $branch_id = (int) $branch_id;

    $where = "`status` = '1' AND `created` >= '$start' AND `created` < '$end'";
    if ($user_id > 0) {
        $where .= " AND `user_id` = '$user_id'";
    } elseif ($branch_id > 0) {
        $where .= " AND `branch_id` = '$branch_id'";
    }

    $sql = "SELECT COALESCE(SUM(cash), 0) AS cash, COALESCE(SUM(cash_received), 0) AS cash_received FROM tokans WHERE $where";
    $run = mysqli_query($con, $sql);
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return array('cash' => (float) $row['cash'], 'cash_received' => (float) $row['cash_received']);
    }

    return array('cash' => 0.0, 'cash_received' => 0.0);
}

/**
 * @return array<int, array{title: string, count: int, amount: float}>
 */
function fr_summary_tokan_type_breakdown($con, $from_date, $to_date, $user_id, $branch_id, $datetime_end = false)
{
    $bounds = fr_summary_range_bounds($from_date, $to_date, $datetime_end);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);
    $user_id = (int) $user_id;
    $branch_id = (int) $branch_id;

    $where = "t.`status` = '1' AND t.`created` >= '$start' AND t.`created` < '$end' AND t.tokan_type_id < 100";
    if ($user_id > 0) {
        $where .= " AND t.`user_id` = '$user_id'";
    } elseif ($branch_id > 0) {
        $where .= " AND t.`branch_id` = '$branch_id'";
    }

    $sql = "SELECT t.tokan_type_id, COALESCE(tt.title, 'No Title') AS title,
            COUNT(*) AS cnt,
            COALESCE(SUM(t.cash_received), 0) AS received_sum
        FROM tokans t
        LEFT JOIN tokan_types tt ON tt.id = t.tokan_type_id AND tt.status = '1'
        WHERE $where
        GROUP BY t.tokan_type_id, tt.title
        ORDER BY t.tokan_type_id";

    $rows = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $cnt = (int) $row['cnt'];
            $rows[] = array(
                'title' => (string) $row['title'],
                'count' => $cnt,
                'amount' => (float) $row['received_sum'],
            );
        }
    }

    return $rows;
}

/**
 * Lab (category 2) or non-lab medicine lines.
 *
 * @return array{count: int, amount: float}
 */
function fr_summary_ibd_category_totals($con, $from_date, $to_date, $lab_only, $datetime_end = false)
{
    $bounds = fr_summary_range_bounds($from_date, $to_date, $datetime_end);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);
    $cat = $lab_only ? 'i.category_id = 2' : 'i.category_id NOT IN (2)';

    $sql = "SELECT ibd.dose, ibd.feed, ibd.days, ibd.fix_dose, t.tokan_type_id,
            i.deserving, i.poor, i.member, i.general
        FROM item_by_doctor ibd
        INNER JOIN item_register_to_branches irb ON irb.id = ibd.item_id
        INNER JOIN items i ON i.id = irb.item_id
        INNER JOIN tokans t ON t.id = ibd.tokan_no
        WHERE ibd.created >= '$start' AND ibd.created < '$end' AND $cat";

    $count = 0;
    $amount = 0.0;
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $count++;
            $qty = (int) $row['fix_dose'] !== 0
                ? (int) $row['fix_dose']
                : (int) $row['dose'] * (int) $row['feed'] * (int) $row['days'];
            $type = (int) $row['tokan_type_id'];
            if ($type === 101) {
                $price = (float) $row['deserving'];
            } elseif ($type === 102) {
                $price = (float) $row['poor'];
            } elseif ($type === 103) {
                $price = (float) $row['member'];
            } else {
                $price = (float) $row['general'];
            }
            $amount += $qty * $price;
        }
    }

    return array('count' => $count, 'amount' => $amount);
}

/**
 * @return array{amount: float, token_list: string}
 */
function fr_summary_return_tokens($con, $from_date, $to_date, $branch_id, $datetime_end = false)
{
    $bounds = fr_summary_range_bounds($from_date, $to_date, $datetime_end);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);
    $branch_id = (int) $branch_id;

    $where = "status = '3' AND `created` >= '$start' AND `created` < '$end'";
    if ($branch_id > 0) {
        $where .= " AND branch_id = '$branch_id'";
    }

    $sql = "SELECT COALESCE(SUM(cash_received), 0) AS amt, GROUP_CONCAT(id ORDER BY id SEPARATOR ' ') AS ids
        FROM tokans WHERE $where";
    $run = mysqli_query($con, $sql);
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return array(
            'amount' => (float) $row['amt'],
            'token_list' => (string) ($row['ids'] ?? ''),
        );
    }

    return array('amount' => 0.0, 'token_list' => '');
}

/**
 * @return float
 */
function fr_summary_pending_amount($con, $from_date, $to_date, $datetime_end = false)
{
    $bounds = fr_summary_range_bounds($from_date, $to_date, $datetime_end);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);

    $sql = "SELECT COALESCE(SUM(GREATEST(t.cash - t.cash_received, 0)), 0) AS pending
        FROM branch_pending_details bpd
        INNER JOIN tokans t ON t.id = bpd.token_no
        WHERE bpd.status = '1' AND bpd.created >= '$start' AND bpd.created < '$end'";

    $run = mysqli_query($con, $sql);
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return (float) $row['pending'];
    }

    return 0.0;
}

/**
 * @return float
 */
function fr_summary_pending_receive_amount($con, $from_date, $to_date, $datetime_end = false)
{
    $bounds = fr_summary_range_bounds($from_date, $to_date, $datetime_end);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);

    $sql = "SELECT COALESCE(SUM(amount), 0) AS amt FROM branch_pending_receive
        WHERE status = '1' AND created >= '$start' AND created < '$end'";

    $run = mysqli_query($con, $sql);
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return (float) $row['amt'];
    }

    return 0.0;
}

/**
 * Token rows with patient/doctor/type (one query).
 *
 * @return mysqli_result|false
 */
function fr_summary_tokans_detail_result($con, $from_date, $to_date, $user_id, $branch_id, $datetime_end = false)
{
    $bounds = fr_summary_range_bounds($from_date, $to_date, $datetime_end);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);
    $user_id = (int) $user_id;
    $branch_id = (int) $branch_id;

    $where = "t.`status` = '1' AND t.`created` >= '$start' AND t.`created` < '$end'";
    if ($user_id > 0) {
        $where .= " AND t.`user_id` = '$user_id'";
    } elseif ($branch_id > 0) {
        $where .= " AND t.`branch_id` = '$branch_id'";
    }

    $sql = "SELECT t.*, p.name AS patient_name, p.age AS patient_age, p.gender AS patient_gender,
            COALESCE(u.u_name, 'Self') AS doctor_name, COALESCE(tt.title, 'No Title') AS type_title
        FROM tokans t
        LEFT JOIN patients p ON p.id = t.patient_id
        LEFT JOIN users u ON u.id = t.doctor_id
        LEFT JOIN tokan_types tt ON tt.id = t.tokan_type_id
        WHERE $where
        ORDER BY t.created";

    return mysqli_query($con, $sql);
}

/**
 * FR daily progress by doctor for one branch.
 *
 * @return array<int, array{name: string, opd: int, cons: int, lab: int}>
 */
function fr_progress_doctors_day($con, $branch_id, $date)
{
    $branch_id = (int) $branch_id;
    $date = substr((string) $date, 0, 10);
    $day = mysqli_real_escape_string($con, $date);
    $start = $day . ' 00:00:00';
    $end = date('Y-m-d H:i:s', strtotime($start . ' +1 day'));

    $doctors = array();
    $sql = "SELECT t.doctor_id, COALESCE(u.u_name, 'Unknown') AS doctor_name,
            SUM(CASE WHEN t.tokan_type_id < 9 THEN 1 ELSE 0 END) AS opd
        FROM tokans t
        LEFT JOIN users u ON u.id = t.doctor_id
        WHERE t.branch_id = $branch_id AND t.status = 1
            AND t.created >= '$start' AND t.created < '$end'
        GROUP BY t.doctor_id, u.u_name
        HAVING t.doctor_id > 0
        ORDER BY t.doctor_id";
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $did = (int) $row['doctor_id'];
            $doctors[$did] = array(
                'name' => (string) $row['doctor_name'],
                'opd' => (int) $row['opd'],
                'cons' => 0,
                'lab' => 0,
            );
        }
    }

    $consSql = "SELECT t.doctor_id, COUNT(ibd.tokan_no) AS cons
        FROM item_by_doctor ibd
        INNER JOIN tokans t ON t.id = ibd.tokan_no AND t.branch_id = ibd.branch_id
        INNER JOIN item_register_to_branches irb ON irb.id = ibd.item_id
        INNER JOIN items i ON i.id = irb.item_id AND i.category_id = 29
        WHERE ibd.branch_id = $branch_id AND ibd.status = 2
            AND t.created >= '$start' AND t.created < '$end' AND t.status = 1
        GROUP BY t.doctor_id";
    $run = mysqli_query($con, $consSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $did = (int) $row['doctor_id'];
            if (!isset($doctors[$did])) {
                $doctors[$did] = array('name' => 'Unknown', 'opd' => 0, 'cons' => 0, 'lab' => 0);
            }
            $doctors[$did]['cons'] = (int) $row['cons'];
        }
    }

    $labSql = "SELECT t.doctor_id, COALESCE(SUM(t.cash_received), 0) AS lab
        FROM tokans t
        WHERE t.branch_id = $branch_id AND t.status = 1
            AND t.created >= '$start' AND t.created < '$end'
            AND EXISTS (
                SELECT 1 FROM item_by_doctor ibd
                INNER JOIN item_register_to_branches irb ON irb.id = ibd.item_id
                INNER JOIN items i ON i.id = irb.item_id AND i.category_id = 2
                WHERE ibd.tokan_no = t.id AND ibd.branch_id = t.branch_id AND ibd.status = 2
            )
        GROUP BY t.doctor_id";
    $run = mysqli_query($con, $labSql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $did = (int) $row['doctor_id'];
            if (!isset($doctors[$did])) {
                $doctors[$did] = array('name' => 'Unknown', 'opd' => 0, 'cons' => 0, 'lab' => 0.0);
            }
            $doctors[$did]['lab'] = (float) $row['lab'];
        }
    }

    return $doctors;
}

function fr_summary_gender_letter($gender)
{
    $gender = (int) $gender;
    if ($gender === 1) {
        return 'F';
    }
    if ($gender === 2) {
        return 'M';
    }

    return 'O';
}

/**
 * @return mysqli_result|false
 */
function fr_summary_return_tokens_detail_result($con, $from_date, $to_date, $branch_id, $datetime_end = false)
{
    $bounds = fr_summary_range_bounds($from_date, $to_date, $datetime_end);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);
    $branch_id = (int) $branch_id;

    $where = "t.status = '3' AND t.created >= '$start' AND t.created < '$end'";
    if ($branch_id > 0) {
        $where .= " AND t.branch_id = '$branch_id'";
    }

    $sql = "SELECT t.id, t.cash, t.cash_received, return_tokens.created AS return_created,
            return_tokens.retuen_token_reason, return_tokens.return_token_recomended_by,
            reception.u_name AS reception_staff, admin.u_name AS admin_staff
        FROM tokans t
        INNER JOIN users reception ON t.user_id = reception.id
        INNER JOIN return_tokens ON t.id = return_tokens.token_no
        INNER JOIN users AS admin ON return_tokens.return_by = admin.id
        WHERE $where
        ORDER BY t.id";

    return mysqli_query($con, $sql);
}

/**
 * @return array<int, array{token_no: int, amount: float, patient_name: string, ref_name: string, token_by: string}>
 */
function fr_summary_pending_receive_rows($con, $from_date, $to_date, $branch_id, $datetime_end = false)
{
    $bounds = fr_summary_range_bounds($from_date, $to_date, $datetime_end);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);
    $branch_id = (int) $branch_id;

    $where = "bpr.status = '1' AND bpr.created >= '$start' AND bpr.created < '$end'";
    if ($branch_id > 0) {
        $where .= " AND bpr.branch_id = '$branch_id'";
    }

    $sql = "SELECT bpr.token_no, bpr.amount, COALESCE(u.u_name, '') AS token_by,
            COALESCE(p.name, '') AS patient_name,
            COALESCE(bdp.ref_name, '') AS ref_name
        FROM branch_pending_receive bpr
        LEFT JOIN users u ON u.id = bpr.user_id
        LEFT JOIN tokans t ON t.id = bpr.token_no
        LEFT JOIN patients p ON p.id = t.patient_id
        LEFT JOIN branch_daily_pending_details bdp ON bdp.token_no = bpr.token_no
        WHERE $where
        ORDER BY bpr.token_no";

    $rows = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $rows[] = array(
                'token_no' => (int) $row['token_no'],
                'amount' => (float) $row['amount'],
                'patient_name' => (string) $row['patient_name'],
                'ref_name' => (string) $row['ref_name'],
                'token_by' => (string) $row['token_by'],
            );
        }
    }

    return $rows;
}

/**
 * @return array<int, array{token_no: int, amount: float, patient_name: string, ref_name: string, token_by: string}>
 */
function fr_summary_pending_token_rows($con, $from_date, $to_date, $branch_id, $datetime_end = false)
{
    $bounds = fr_summary_range_bounds($from_date, $to_date, $datetime_end);
    $start = mysqli_real_escape_string($con, $bounds['start']);
    $end = mysqli_real_escape_string($con, $bounds['end']);
    $branch_id = (int) $branch_id;

    $where = "bpd.status = '1' AND bpd.created >= '$start' AND bpd.created < '$end'";
    if ($branch_id > 0) {
        $where .= " AND bpd.branch_id = '$branch_id'";
    }

    $sql = "SELECT bpd.token_no,
            GREATEST(t.cash - t.cash_received, 0) AS amount,
            COALESCE(u.u_name, '') AS token_by,
            COALESCE(p.name, '') AS patient_name,
            COALESCE(bdp.ref_name, '') AS ref_name
        FROM branch_pending_details bpd
        INNER JOIN tokans t ON t.id = bpd.token_no
        LEFT JOIN users u ON u.id = t.user_id
        LEFT JOIN patients p ON p.id = t.patient_id
        LEFT JOIN branch_daily_pending_details bdp ON bdp.token_no = bpd.token_no
        WHERE $where AND GREATEST(t.cash - t.cash_received, 0) > 0
        ORDER BY bpd.token_no";

    $rows = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $rows[] = array(
                'token_no' => (int) $row['token_no'],
                'amount' => (float) $row['amount'],
                'patient_name' => (string) $row['patient_name'],
                'ref_name' => (string) $row['ref_name'],
                'token_by' => (string) $row['token_by'],
            );
        }
    }

    return $rows;
}

/**
 * Token-type breakdown rows (after detail table).
 */
function fr_render_summary_type_breakdown_rows($con, $from_date, $to_date, $user_id, $branch_id, $datetime_end = false)
{
    foreach (fr_summary_tokan_type_breakdown($con, $from_date, $to_date, $user_id, $branch_id, $datetime_end) as $typeRow) {
        echo '<tr>
            <th style="text-align: right;" colspan="4">' . htmlspecialchars($typeRow['title']) . '</th>
            <th style="text-align: center;" colspan="3">' . (int) $typeRow['count'] . '</th>
            <th style="text-align: left;" colspan="4">' . number_format((float)($typeRow['amount'] ?? 0)) . '</th>
        </tr>';
    }
}

/**
 * Return / pending sections (branch summary only).
 */
function fr_render_summary_branch_extras($con, $from_date, $to_date, $branch_id, $datetime_end = false)
{
    $branch_id = (int) $branch_id;
    if ($branch_id < 1) {
        return;
    }

    $returnRun = fr_summary_return_tokens_detail_result($con, $from_date, $to_date, $branch_id, $datetime_end);
    if ($returnRun && mysqli_num_rows($returnRun) > 0) {
        echo '<tr><th style="text-align: center;" colspan="11">DETAILS OF RETURN TOKENS</th></tr>
        <tr>
            <th>TOKEN #</th><th>TIME</th><th>DATE</th><th>TOKEN BY</th><th>REASON</th>
            <th>RECOMMENDED BY</th><th>ADMIN</th><th>CASH</th><th>RECEIVED</th>
        </tr>';
        while ($row = mysqli_fetch_assoc($returnRun)) {
            $created = $row['return_created'];
            echo '<tr>
                <td>' . (int) $row['id'] . '</td>
                <td>' . htmlspecialchars(ycdo_safe_date_format($created, 'h:i A', '')) . '</td>
                <td>' . htmlspecialchars(ycdo_safe_date_format($created, 'd M', '')) . '</td>
                <td>' . htmlspecialchars($row['reception_staff']) . '</td>
                <td>' . htmlspecialchars($row['retuen_token_reason']) . '</td>
                <td>' . htmlspecialchars($row['return_token_recomended_by']) . '</td>
                <td>' . htmlspecialchars($row['admin_staff']) . '</td>
                <td>' . htmlspecialchars((string) $row['cash']) . '</td>
                <td>' . htmlspecialchars((string) $row['cash_received']) . '</td>
            </tr>';
        }
    }

    $receiveRows = fr_summary_pending_receive_rows($con, $from_date, $to_date, $branch_id, $datetime_end);
    if ($receiveRows !== array()) {
        $pending_receive_amount = 0.0;
        echo '<tr><td colspan="11"><table border="solid" style="margin: auto;"><tr>
            <tr><th>TOKEN NO</th><th>NAME</th><th>AMOUNT</th><th>REF NAME</th><th>TOKEN BY</th></tr>';
        foreach ($receiveRows as $row) {
            $pending_receive_amount += $row['amount'];
            echo '<tr><td>' . (int) $row['token_no'] . '</td><td style="text-transform: uppercase;">'
                . htmlspecialchars($row['patient_name']) . '</td><td>' . number_format((float)($row['amount'] ?? 0))
                . '</td><td>' . htmlspecialchars($row['ref_name']) . '</td><td>'
                . htmlspecialchars($row['token_by']) . '</td></tr>';
        }
        echo '<caption style="text-align: center;color: black;" colspan="11"><strong>PENDING RECEIVED: AMOUNT -> <u>'
            . number_format((float)($pending_receive_amount ?? 0)) . '</u></strong></caption></table></td></tr>';
    }

    $pendingRows = fr_summary_pending_token_rows($con, $from_date, $to_date, $branch_id, $datetime_end);
    if ($pendingRows !== array()) {
        $pending_token_amount = 0.0;
        echo '<tr><td colspan="11"><table border="solid" style="margin: auto;"><tr>
            <tr><th>TOKEN NO</th><th>NAME</th><th>AMOUNT</th><th>Ref. Name</th><th>TOKEN BY</th></tr>';
        foreach ($pendingRows as $row) {
            $pending_token_amount += $row['amount'];
            echo '<tr><td>' . (int) $row['token_no'] . '</td><td style="text-transform: uppercase;">'
                . htmlspecialchars($row['patient_name']) . '</td><td>' . number_format((float)($row['amount'] ?? 0))
                . '</td><td>' . htmlspecialchars($row['ref_name']) . '</td><td>'
                . htmlspecialchars($row['token_by']) . '</td></tr>';
        }
        echo '<caption style="text-align: center;color: black;" colspan="11"><strong>PENDING TOKEN: Amount -> <u>'
            . number_format((float)($pending_token_amount ?? 0)) . '</u></strong></caption></table></td></tr>';
    }
}
