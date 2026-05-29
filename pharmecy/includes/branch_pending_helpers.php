<?php

/**
 * True when branch quantity should gate UI (OUT OF STOCK) and stock deductions.
 * Services, lab tests, imaging, and lab consumables (vials, rolls) skip stock checks.
 *
 * @param int|string $category_id
 * @param string $category_name categories.name (optional)
 * @param string $item_name items.name (optional)
 */
function pharmecy_item_requires_stock_check($category_id, $category_name = '', $item_name = '')
{
    static $service_category_ids = array(
        2,   // TEST / lab
        3,   // PROCEDURE
        8,   // USG / imaging
        20,
        28,
        29, 31, 32, 33, 34, 36, 37, 38, 39, 40, 41, 42, 44,
    );

    if (in_array((int) $category_id, $service_category_ids, true)) {
        return false;
    }

    $cat = strtoupper(trim((string) $category_name));
    $non_stock_category_patterns = array(
        'TEST', 'PROCEDURE', 'USG', 'ULTRASOUND', 'SCAN', 'IMAGING', 'RADIOLOGY',
        'SEROLOGY', 'HEMATOLOGY', 'PATHOLOGY', 'DIAGNOSTIC', 'CONSUMABLE', 'LAB',
        'VIAL', 'REAGENT',
    );
    foreach ($non_stock_category_patterns as $pattern) {
        if ($cat !== '' && strpos($cat, $pattern) !== false) {
            return false;
        }
    }

    $item = strtoupper(trim((string) $item_name));
    if ($item !== '' && preg_match('/\b(CBC|ESR|LFT|RFT|HBA1C|PCR|USG)\b/', $item)) {
        if (strpos($cat, 'TEST') !== false
            || strpos($cat, 'LAB') !== false
            || strpos($cat, 'CONSUMABLE') !== false
            || strpos($cat, 'VIAL') !== false
            || strpos($cat, 'SEROLOGY') !== false
            || strpos($cat, 'HEMATOLOGY') !== false) {
            return false;
        }
    }

    return true;
}

/**
 * Price column on items / item_by_doctor for a token payment type.
 */
function pharmecy_tokan_type_price_column($tokan_type_id)
{
    $tokan_type_id = (int) $tokan_type_id;
    if ($tokan_type_id === 102) {
        return 'poor';
    }
    if ($tokan_type_id === 103) {
        return 'member';
    }
    if ($tokan_type_id === 101) {
        return 'deserving';
    }
    return 'general';
}

/**
 * Sum bill for items still in the user's cart (status = 1, no token yet).
 */
function pharmecy_cart_amount_by_tokan_type($con, $user_id, $branch_id, $tokan_type_id)
{
    $user_id = (int) $user_id;
    $branch_id = (int) $branch_id;
    $price_col = pharmecy_tokan_type_price_column($tokan_type_id);

    $amount = 0.0;
    $run1 = mysqli_query(
        $con,
        "SELECT * FROM `item_by_doctor`
        WHERE branch_id = '$branch_id' AND user_id = '$user_id' AND status = '1'
        AND (tokan_no IS NULL OR tokan_no = '' OR tokan_no = '0')"
    );
    if (!$run1) {
        return 0.0;
    }

    while ($row1 = mysqli_fetch_assoc($run1)) {
        $fix_dose = (int) $row1['fix_dose'];
        $quantity = ($fix_dose === 0)
            ? (int) $row1['days'] * (int) $row1['dose'] * (int) $row1['feed']
            : $fix_dose;
        if ($quantity < 1) {
            $quantity = 1;
        }

        $item_id = (int) $row1['item_id'];
        $run = mysqli_query(
            $con,
            "SELECT `$price_col` FROM items
            WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id')"
        );
        if ($run && ($row = mysqli_fetch_assoc($run))) {
            $amount += (float) $row[$price_col] * $quantity;
        }
    }

    return $amount;
}

/**
 * Total bill for a saved token: sum line sale_price, else unit price × qty, else tokans.cash.
 */
function pharmecy_token_bill_amount($con, $token_no)
{
    $token_no = (int) $token_no;
    if ($token_no < 1) {
        return 0.0;
    }

    $tokan_type_id = 104;
    $cash = 0.0;
    $tq = mysqli_query($con, "SELECT cash, tokan_type_id FROM tokans WHERE id = '$token_no' LIMIT 1");
    if ($tq && ($tr = mysqli_fetch_assoc($tq))) {
        $cash = (float) $tr['cash'];
        $tokan_type_id = (int) $tr['tokan_type_id'];
    }

    $price_col = pharmecy_tokan_type_price_column($tokan_type_id);
    $sum = 0.0;
    $iq = mysqli_query(
        $con,
        "SELECT sale_price, sale_price_general, sale_price_member, sale_price_poor,
                sale_quantity, fix_dose, dose, feed, days
         FROM item_by_doctor WHERE tokan_no = '$token_no'"
    );
    if ($iq) {
        while ($row = mysqli_fetch_assoc($iq)) {
            if ((float) $row['sale_price'] > 0) {
                $sum += (float) $row['sale_price'];
                continue;
            }
            $qty = (int) $row['sale_quantity'];
            if ($qty < 1) {
                $fix_dose = (int) $row['fix_dose'];
                $qty = ($fix_dose === 0)
                    ? (int) $row['dose'] * (int) $row['feed'] * (int) $row['days']
                    : $fix_dose;
            }
            if ($qty < 1) {
                $qty = 1;
            }
            $unit = (float) $row['sale_price_general'];
            if ($price_col === 'poor') {
                $unit = (float) $row['sale_price_poor'];
            } elseif ($price_col === 'member') {
                $unit = (float) $row['sale_price_member'];
            } elseif ($price_col === 'deserving') {
                $unit = (float) ($row['sale_price_poor'] ?? $row['sale_price_general']);
            }
            $sum += $unit * $qty;
        }
    }

    if ($sum > 0) {
        return $sum;
    }

    return $cash;
}

/**
 * Amount to show on branch procedure lists.
 */
function pharmecy_resolve_branch_pending_display_amount($con, $token_no, $stored_amount = 0)
{
    if ((float) $stored_amount > 0) {
        return (float) $stored_amount;
    }

    return pharmecy_token_bill_amount($con, $token_no);
}

/**
 * Insert branch_pending_details with required NOT NULL columns (no DB defaults).
 *
 * @param mysqli $con
 * @param int|string $tokan_no
 * @param string $current_date
 * @param int|string $branch_id
 * @param string $status
 * @param array<string, mixed> $fields Optional: amount, gardian_name, gardian_phone, recommended_by, return_date, user_id, tokan_type_id
 * @return bool
 */
function pharmecy_insert_branch_pending_details($con, $tokan_no, $current_date, $branch_id, $status = '2', array $fields = array())
{
    $tokan_no = (int) $tokan_no;
    $branch_id = (int) $branch_id;
    $status = mysqli_real_escape_string($con, (string) $status);
    $current_date = mysqli_real_escape_string($con, (string) $current_date);

    $gardian_name = (string) ($fields['gardian_name'] ?? $_POST['gardian_name'] ?? $_GET['gardian_name'] ?? $_GET['ref_name'] ?? '');
    $gardian_phone = (string) ($fields['gardian_phone'] ?? $_POST['gardian_phone'] ?? $_GET['gardian_phone'] ?? $_GET['ref_phone'] ?? '');
    $recommended_by = (string) ($fields['recommended_by'] ?? $_POST['recommended_by'] ?? $_GET['recommended_by'] ?? '');
    $return_date = (string) ($fields['return_date'] ?? $_POST['return_date'] ?? $_GET['return_date'] ?? '0000-00-00');
    if ($return_date === '') {
        $return_date = '0000-00-00';
    }

    if (isset($fields['amount']) && (float) $fields['amount'] > 0) {
        $amount = (float) $fields['amount'];
    } else {
        $amount = pharmecy_token_bill_amount($con, $tokan_no);
        if ($amount <= 0) {
            $cart_user = (int) ($fields['user_id'] ?? $GLOBALS['user_id'] ?? 0);
            $cart_type = (int) ($fields['tokan_type_id'] ?? 104);
            $amount = pharmecy_cart_amount_by_tokan_type($con, $cart_user, $branch_id, $cart_type);
        }
    }

    $tokan_cash = pharmecy_tokan_cash_amount($con, $tokan_no);
    if ($tokan_cash > 0 && $amount <= 0) {
        $amount = (float) $tokan_cash;
    } elseif ($tokan_cash > $amount) {
        $amount = (float) $tokan_cash;
    }

    $gardian_name = mysqli_real_escape_string($con, $gardian_name);
    $gardian_phone = mysqli_real_escape_string($con, $gardian_phone);
    $recommended_by = mysqli_real_escape_string($con, $recommended_by);
    $return_date = mysqli_real_escape_string($con, $return_date);
    $amount_sql = mysqli_real_escape_string($con, (string) $amount);

    $sql = "INSERT INTO `branch_pending_details`
        (`token_no`, `branch_id`, `gardian_name`, `gardian_phone`, `recommended_by`, `return_date`, `amount`, `created`, `status`)
        VALUES
        ('$tokan_no', '$branch_id', '$gardian_name', '$gardian_phone', '$recommended_by', '$return_date', '$amount_sql', '$current_date', '$status')";

    $ok = (bool) mysqli_query($con, $sql);
    if ($ok && $amount > 0) {
        mysqli_query(
            $con,
            "UPDATE tokans SET cash = '$amount_sql'
            WHERE id = '$tokan_no' AND (cash IS NULL OR cash = '' OR cash = '0' OR cash = 0)"
        );
    }

    return $ok;
}

/**
 * Procedure token bill from tokans.cash.
 */
function pharmecy_tokan_cash_amount($con, $token_no)
{
    $token_no = (int) $token_no;
    if ($token_no < 1) {
        return 0;
    }
    $run = mysqli_query($con, "SELECT cash FROM tokans WHERE id = '$token_no' LIMIT 1");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return (int) round((float) ($row['cash'] ?? 0));
    }
    return 0;
}

/**
 * Backfill branch_pending_details.amount from tokans.cash when amount is zero.
 */
function pharmecy_sync_branch_pending_amount_from_tokan($con, $token_no)
{
    $token_no = (int) $token_no;
    if ($token_no < 1) {
        return false;
    }
    $cash = pharmecy_tokan_cash_amount($con, $token_no);
    if ($cash <= 0) {
        return false;
    }
    $cash_sql = mysqli_real_escape_string($con, (string) $cash);
    return (bool) mysqli_query(
        $con,
        "UPDATE branch_pending_details SET amount = '$cash_sql'
        WHERE token_no = '$token_no' AND status = '1'
        AND (amount IS NULL OR amount = '' OR amount = '0' OR amount = 0)"
    );
}

/**
 * Finalize cart lines for a new procedure token (per-line sale_price).
 */
function pharmecy_finalize_procedure_cart_items($con, $tokan_no, $user_id, $branch_id, $doctor_id, $tokan_type_id)
{
    $tokan_no = (int) $tokan_no;
    $user_id = (int) $user_id;
    $branch_id = (int) $branch_id;
    $doctor_id = (int) $doctor_id;
    $tokan_type_id = (int) $tokan_type_id;

    $run = mysqli_query(
        $con,
        "SELECT * FROM `item_by_doctor`
        WHERE branch_id = '$branch_id' AND user_id = '$user_id' AND status = '1'
        AND (tokan_no IS NULL OR tokan_no = '' OR tokan_no = '0')"
    );
    if (!$run) {
        return;
    }

    while ($row = mysqli_fetch_assoc($run)) {
        $reg_item_id = (int) $row['item_id'];
        $fix_dose = (int) $row['fix_dose'];
        $quantity = ($fix_dose === 0)
            ? (int) $row['days'] * (int) $row['dose'] * (int) $row['feed']
            : $fix_dose;
        if ($quantity < 1) {
            $quantity = 1;
        }

        $general = (float) $row['sale_price_general'];
        $member = (float) $row['sale_price_member'];
        $poor = (float) $row['sale_price_poor'];
        $sale_price = $general * $quantity;
        if ($tokan_type_id === 102) {
            $sale_price = $poor * $quantity;
        } elseif ($tokan_type_id === 103) {
            $sale_price = $member * $quantity;
        }

        $line_id = (int) $row['id'];
        mysqli_query(
            $con,
            "UPDATE `item_by_doctor` SET
                tokan_no = '$tokan_no',
                status = '2',
                tokan_type_id = '$tokan_type_id',
                sale_price = '$sale_price',
                sale_quantity = '$quantity',
                doctor_id = '$doctor_id'
            WHERE id = '$line_id'"
        );
    }
}

/** Max rows for branch procedure pending lists (avoids gateway timeouts). */
function pharmecy_branch_pending_list_limit()
{
    return 100;
}

/**
 * Pending procedure rows for branch_procedure_pending_token (single query, no per-row N+1).
 *
 * @return list<array<string, mixed>>
 */
function pharmecy_fetch_branch_pending_list($con, $branch_id, $search_token = '', $limit = 100)
{
    $branch_id = (int) $branch_id;
    $limit = max(1, min((int) $limit, pharmecy_branch_pending_list_limit()));

    $join = "
        FROM branch_pending_details bpd
        INNER JOIN tokans t ON bpd.token_no = t.id
        INNER JOIN patients p ON t.patient_id = p.id
        LEFT JOIN branch_pending_receive bpr ON bpr.token_no = t.id AND bpr.status = '1'
        WHERE bpd.status = '1' AND bpd.branch_id = '$branch_id'";

    if ($search_token !== '') {
        $search_esc = mysqli_real_escape_string($con, (string) $search_token);
        if (ctype_digit($search_esc)) {
            $search_sql = " AND (bpd.token_no = '$search_esc' OR p.name LIKE '%$search_esc%') ";
        } else {
            $search_sql = " AND p.name LIKE '%$search_esc%' ";
        }
        $join .= $search_sql;
    }

    $sql = "SELECT bpd.id AS branch_pending_id, bpd.token_no, bpd.gardian_name, bpd.recommended_by,
        bpd.amount AS stored_amount, t.cash, t.cash_received, t.created, t.tokan_type_id,
        p.name AS patient_name,
        COALESCE(SUM(bpr.amount), 0) AS receive_sum
        $join
        GROUP BY bpd.id, bpd.token_no, bpd.gardian_name, bpd.recommended_by, bpd.amount,
            t.cash, t.cash_received, t.created, t.tokan_type_id, p.name
        ORDER BY bpd.id DESC
        LIMIT $limit";

    $run = mysqli_query($con, $sql);
    if (!$run) {
        return array();
    }

    $out = array();
    while ($row = mysqli_fetch_assoc($run)) {
        $stored = (float) ($row['stored_amount'] ?? 0);
        $total_amount = $stored > 0 ? $stored : (float) ($row['cash'] ?? 0);
        if ($total_amount <= 0) {
            $total_amount = pharmecy_resolve_branch_pending_display_amount(
                $con,
                (int) $row['token_no'],
                $stored
            );
        }
        $receive_adj = -(float) ($row['receive_sum'] ?? 0);
        $received_amount = (float) ($row['cash_received'] ?? 0);
        $pending_amount = (int) ($total_amount - ($received_amount - $receive_adj));
        if ($pending_amount <= 0) {
            continue;
        }
        $row['total_amount'] = $total_amount;
        $row['received_amount'] = $received_amount;
        $row['pending_amount'] = $pending_amount;
        $out[] = $row;
    }

    return $out;
}

/**
 * HTML table rows for procedure pending list (used by page and AJAX).
 */
function pharmecy_render_branch_pending_procedure_rows($con, $branch_id, $search_token = '', $limit = 100)
{
    $rows = pharmecy_fetch_branch_pending_list($con, $branch_id, $search_token, $limit);
    if (count($rows) === 0) {
        echo '<tr><td colspan="12" class="text-center text-muted">No pending procedures found (showing latest '
            . (int) pharmecy_branch_pending_list_limit() . ' records).</td></tr>';
        return;
    }

    $s = 0;
    foreach ($rows as $row) {
        $s++;
        $token_no = (int) $row['token_no'];
        $branch_pending_id = (int) $row['branch_pending_id'];
        $patient_name = htmlspecialchars((string) $row['patient_name'], ENT_QUOTES, 'UTF-8');
        $gardian_name = htmlspecialchars((string) $row['gardian_name'], ENT_QUOTES, 'UTF-8');
        $recommended_by = htmlspecialchars((string) $row['recommended_by'], ENT_QUOTES, 'UTF-8');
        $token_type_title = htmlspecialchars(
            (string) token_type_title((int) $row['tokan_type_id']),
            ENT_QUOTES,
            'UTF-8'
        );
        $created_fmt = date_format(date_create((string) $row['created']), 'd-m-Y');
        $total_amount = (int) $row['total_amount'];
        $received_amount = (int) $row['received_amount'];
        $pending_amount = (int) $row['pending_amount'];

        echo '<tr>';
        echo '<td>' . $s . '</td>';
        echo '<td>' . $created_fmt . '</td>';
        echo '<td>' . $patient_name . '</td>';
        echo '<td>' . $gardian_name . '</td>';
        echo '<td><a class="btn btn-sm btn-outline-info" href="branch_pending_complete_detail.php?token_no='
            . $token_no . '">' . $token_no . '</a></td>';
        echo '<td>' . $token_type_title . '</td>';
        echo '<td>' . $total_amount . '</td>';
        echo '<td>' . $received_amount . '</td>';
        echo '<td>' . $pending_amount . '</td>';
        echo '<td>' . $recommended_by . '</td>';
        echo '<td><a class="btn btn-sm btn-outline-info" href="procedure_pending_amount.php?search_tokan_no='
            . $token_no . '">Pay Amount</a></td>';
        if ($branch_pending_id !== 0) {
            echo '<td><a href="branch_pending_detail_update.php?u_id=' . $branch_pending_id . '">Update</a></td>';
        } else {
            echo '<td></td>';
        }
        echo '</tr>';
    }
}

/** Procedure categories for branch procedure turn dropdown. */
function pharmecy_procedure_category_ids_sql_in()
{
    return '3, 37, 38';
}

/**
 * Fast next token number (MAX on primary key, not ORDER BY scan).
 */
function pharmecy_next_tokan_no_fast($con)
{
    $run = mysqli_query($con, 'SELECT COALESCE(MAX(id), 0) + 1 AS next_id FROM tokans');
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return (int) $row['next_id'];
    }
    return 1;
}

/**
 * Registration token + patient for second_procedure_turn (single query).
 *
 * @return array<string, mixed>|null
 */
function pharmecy_load_procedure_turn_token($con, $token_no)
{
    $token_no = (int) $token_no;
    if ($token_no < 1) {
        return null;
    }
    $sql = "SELECT t.id AS token_no, t.doctor_id, t.patient_id, p.name, p.age, p.gender
        FROM tokans t
        INNER JOIN patients p ON t.patient_id = p.id
        WHERE t.id = '$token_no' AND t.tokan_type_id < 100
        LIMIT 1";
    $run = mysqli_query($con, $sql);
    if (!$run || mysqli_num_rows($run) !== 1) {
        return null;
    }
    return mysqli_fetch_assoc($run);
}

/**
 * Cart lines not yet assigned to a procedure token (for procedure turn page).
 */
function pharmecy_procedure_turn_cart_count($con, $user_id, $branch_id = null)
{
    $user_id = (int) $user_id;
    if ($branch_id === null) {
        $branch_id = (int) ($GLOBALS['branch_id'] ?? 0);
    } else {
        $branch_id = (int) $branch_id;
    }

    $where = "user_id = '$user_id' AND status = '1'
        AND (tokan_no IS NULL OR tokan_no = '' OR tokan_no = '0')";
    if ($branch_id > 0) {
        $where = "branch_id = '$branch_id' AND " . $where;
    }

    $run = mysqli_query($con, "SELECT COUNT(*) AS c FROM item_by_doctor WHERE $where");
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return (int) $row['c'];
    }
    return 0;
}

/**
 * Add one procedure line to the staging cart (item_by_doctor, tokan_no NULL).
 */
function pharmecy_procedure_turn_add_cart_item($con, $reg_item_id, $user_id, $branch_id, $current_date, array $opts = array())
{
    $reg_item_id = (int) $reg_item_id;
    $user_id = (int) $user_id;
    $branch_id = (int) $branch_id;
    if ($reg_item_id < 1 || $user_id < 1 || $branch_id < 1) {
        return false;
    }

    $fix_dose = (int) ($opts['fix_dose'] ?? 0);
    $dose = max(1, (int) ($opts['dose'] ?? 1));
    $feed = max(1, (int) ($opts['feed'] ?? 1));
    $days = max(1, (int) ($opts['days'] ?? 1));
    $quantity = ($fix_dose === 0) ? ($dose * $days * $feed) : $fix_dose;
    if ($quantity < 1) {
        $quantity = 1;
    }

    $purchase = 0;
    $poor = 0;
    $member = 0;
    $general = 0;
    $category_id = 0;
    $select_items = "SELECT i.id, i.purchase, i.poor, i.member, i.general, i.deserving, i.category_id
        FROM items i
        INNER JOIN item_register_to_branches irb ON irb.item_id = i.id
        WHERE irb.branch_id = '$branch_id' AND irb.id = '$reg_item_id'
        LIMIT 1";
    $run_items = mysqli_query($con, $select_items);
    if (!$run_items || mysqli_num_rows($run_items) !== 1) {
        return false;
    }
    $row_item = mysqli_fetch_assoc($run_items);
    $purchase = $row_item['purchase'];
    $poor = $row_item['poor'];
    $member = $row_item['member'];
    $general = $row_item['general'];
    $category_id = $row_item['category_id'];

    $check_item = mysqli_num_rows(mysqli_query(
        $con,
        "SELECT id FROM `item_by_doctor`
        WHERE item_id = '$reg_item_id' AND user_id = '$user_id' AND branch_id = '$branch_id' AND status = '1'
        AND (tokan_no IS NULL OR tokan_no = '' OR tokan_no = '0')
        LIMIT 1"
    ));
    if ($check_item > 0) {
        return true;
    }

    $get_available_quantity = get_register_item_quantity_from_item_id($reg_item_id);
    $new_quantity = $get_available_quantity - $quantity;
    mysqli_query($con, "UPDATE `item_register_to_branches` SET `quantity`= '$new_quantity' WHERE id = '$reg_item_id' ");

    $insert = "INSERT INTO `item_by_doctor`
    (`item_id`, `dose`, `feed`, `days`, `user_id`, `branch_id`, `fix_dose`, `created`, `purchase_price`, `sale_price_general`, `sale_price_member`, `sale_price_poor`, `category_id`, `sale_quantity`) VALUES
    ('$reg_item_id', '$dose', '$feed', '$days', '$user_id','$branch_id', '$fix_dose', '$current_date', '$purchase', '$general', '$member', '$poor', '$category_id', '$quantity')";

    return (bool) mysqli_query($con, $insert);
}

/**
 * Attach a procedure line directly to a saved token (no staging cart).
 */
function pharmecy_attach_procedure_to_token($con, $tokan_no, $reg_item_id, $user_id, $branch_id, $doctor_id, $tokan_type_id, $current_date, array $opts = array())
{
    $tokan_no = (int) $tokan_no;
    $reg_item_id = (int) $reg_item_id;
    $user_id = (int) $user_id;
    $branch_id = (int) $branch_id;
    $doctor_id = (int) $doctor_id;
    $tokan_type_id = (int) $tokan_type_id;
    if ($tokan_no < 1 || $reg_item_id < 1 || $user_id < 1 || $branch_id < 1) {
        return false;
    }

    $existing = mysqli_num_rows(mysqli_query(
        $con,
        "SELECT id FROM item_by_doctor
        WHERE tokan_no = '$tokan_no' AND item_id = '$reg_item_id' AND status = '2'
        LIMIT 1"
    ));
    if ($existing > 0) {
        return true;
    }

    $fix_dose = (int) ($opts['fix_dose'] ?? 0);
    $dose = max(1, (int) ($opts['dose'] ?? 1));
    $feed = max(1, (int) ($opts['feed'] ?? 1));
    $days = max(1, (int) ($opts['days'] ?? 1));
    $quantity = ($fix_dose === 0) ? ($dose * $days * $feed) : $fix_dose;
    if ($quantity < 1) {
        $quantity = 1;
    }

    $run_items = mysqli_query(
        $con,
        "SELECT i.id, i.purchase, i.poor, i.member, i.general, i.category_id
        FROM items i
        INNER JOIN item_register_to_branches irb ON irb.item_id = i.id
        WHERE irb.branch_id = '$branch_id' AND irb.id = '$reg_item_id'
        LIMIT 1"
    );
    if (!$run_items || mysqli_num_rows($run_items) !== 1) {
        return false;
    }
    $row_item = mysqli_fetch_assoc($run_items);
    $purchase = $row_item['purchase'];
    $poor = $row_item['poor'];
    $member = $row_item['member'];
    $general = $row_item['general'];
    $category_id = $row_item['category_id'];

    $sale_price = (float) $general * $quantity;
    if ($tokan_type_id === 102) {
        $sale_price = (float) $poor * $quantity;
    } elseif ($tokan_type_id === 103) {
        $sale_price = (float) $member * $quantity;
    }

    $get_available_quantity = get_register_item_quantity_from_item_id($reg_item_id);
    $new_quantity = $get_available_quantity - $quantity;
    mysqli_query($con, "UPDATE `item_register_to_branches` SET `quantity`= '$new_quantity' WHERE id = '$reg_item_id' ");

    $insert = "INSERT INTO `item_by_doctor`
    (`item_id`, `dose`, `feed`, `days`, `user_id`, `branch_id`, `fix_dose`, `created`, `purchase_price`, `sale_price_general`, `sale_price_member`, `sale_price_poor`, `category_id`, `sale_quantity`, `tokan_no`, `status`, `tokan_type_id`, `sale_price`, `doctor_id`) VALUES
    ('$reg_item_id', '$dose', '$feed', '$days', '$user_id', '$branch_id', '$fix_dose', '$current_date', '$purchase', '$general', '$member', '$poor', '$category_id', '$quantity', '$tokan_no', '2', '$tokan_type_id', '$sale_price', '$doctor_id')";

    return (bool) mysqli_query($con, $insert);
}
function pharmecy_branch_procedures_options_html($con, $branch_id, $limit = 1500)
{
    $branch_id = (int) $branch_id;
    $limit = max(1, min((int) $limit, 2000));
    $cats = pharmecy_procedure_category_ids_sql_in();
    $sql = "SELECT irb.id AS reg_item_id, i.name AS item_name
        FROM item_register_to_branches irb
        INNER JOIN items i ON irb.item_id = i.id AND i.status = '1'
        WHERE irb.branch_id = '$branch_id' AND irb.status = '1'
          AND i.category_id IN ($cats)
        ORDER BY i.name ASC
        LIMIT $limit";
    $run = mysqli_query($con, $sql);
    if (!$run || mysqli_num_rows($run) < 1) {
        return '<option value="">NO DATA FOUND</option>';
    }
    $html = '';
    while ($row = mysqli_fetch_assoc($run)) {
        $id = (int) $row['reg_item_id'];
        $name = htmlspecialchars((string) $row['item_name'], ENT_QUOTES, 'UTF-8');
        $html .= '<option value="' . $id . '">' . $name . '</option>';
    }
    return $html;
}

/**
 * Selected cart items for procedure turn (one JOIN).
 */
/**
 * Token column on procedure_tokens_medicine_limits (schema varies by install).
 */
function pharmecy_procedure_limits_token_column($con)
{
    static $column = null;
    if ($column !== null) {
        return $column;
    }

    $column = '';
    $candidates = array('token_no', 'procedure_token_no', 'token_id', 'tokan_no');
    $run = mysqli_query($con, 'SHOW COLUMNS FROM procedure_tokens_medicine_limits');
    if (!$run) {
        return $column;
    }

    $found = array();
    while ($row = mysqli_fetch_assoc($run)) {
        $found[] = $row['Field'];
    }
    foreach ($candidates as $name) {
        if (in_array($name, $found, true)) {
            $column = $name;
            break;
        }
    }

    return $column;
}

/**
 * Procedure line on token: catalog items.id + branch register id (item_by_doctor.item_id).
 *
 * @return array{catalog_item_id: int, register_item_id: int}
 */
function pharmecy_procedure_item_ids_for_token($con, $token_no)
{
    $empty = array('catalog_item_id' => 0, 'register_item_id' => 0);
    $token_no = (int) $token_no;
    if ($token_no < 1) {
        return $empty;
    }

    $cats = pharmecy_procedure_category_ids_sql_in();
    $run = mysqli_query(
        $con,
        "SELECT irb.item_id AS catalog_item_id, ibd.item_id AS register_item_id
        FROM item_by_doctor ibd
        INNER JOIN item_register_to_branches irb ON ibd.item_id = irb.id
        INNER JOIN items i ON irb.item_id = i.id
        INNER JOIN categories c ON i.category_id = c.id
        WHERE ibd.tokan_no = '$token_no'
        AND (
            ibd.category_id IN ($cats)
            OR UPPER(c.name) LIKE '%PROCEDURE%'
            OR UPPER(c.name) LIKE '%OPERATION%'
            OR UPPER(c.name) LIKE '%SURGERY%'
        )
        ORDER BY ibd.id ASC
        LIMIT 1"
    );
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return array(
            'catalog_item_id' => (int) ($row['catalog_item_id'] ?? 0),
            'register_item_id' => (int) ($row['register_item_id'] ?? 0),
        );
    }

    return $empty;
}

/**
 * Catalog items.id for the procedure line on this token.
 */
function pharmecy_procedure_catalog_item_id_for_token($con, $token_no)
{
    $ids = pharmecy_procedure_item_ids_for_token($con, $token_no);
    return (int) $ids['catalog_item_id'];
}

/**
 * Lookup medicine_limit in procedure_medicine_limits for one item id key.
 */
function pharmecy_procedure_medicine_limit_by_item_key($con, $item_key)
{
    $item_key = (int) $item_key;
    if ($item_key < 1) {
        return 0;
    }
    $run = mysqli_query(
        $con,
        "SELECT medicine_limit FROM procedure_medicine_limits
        WHERE item_id = '$item_key'
        LIMIT 1"
    );
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return (int) round((float) ($row['medicine_limit'] ?? 0));
    }
    return 0;
}

/**
 * Medicine limit from procedure_medicine_limits for the procedure on this token.
 */
function pharmecy_procedure_medicine_limit_for_token($con, $token_no)
{
    $token_no = (int) $token_no;
    if ($token_no < 1) {
        return 0;
    }

    $ids = pharmecy_procedure_item_ids_for_token($con, $token_no);
    $catalog_item_id = (int) $ids['catalog_item_id'];
    $register_item_id = (int) $ids['register_item_id'];

    if ($catalog_item_id > 0) {
        $limit = pharmecy_procedure_medicine_limit_by_item_key($con, $catalog_item_id);
        if ($limit > 0) {
            return $limit;
        }
    }

    if ($register_item_id > 0 && $register_item_id !== $catalog_item_id) {
        $limit = pharmecy_procedure_medicine_limit_by_item_key($con, $register_item_id);
        if ($limit > 0) {
            return $limit;
        }
    }

    $procedure_cash = pharmecy_tokan_cash_amount($con, $token_no);
    if ($procedure_cash > 0) {
        return (int) round($procedure_cash * 0.25 ?? 0);
    }

    return 0;
}

/**
 * Total medicine cash collected for this procedure token (partial payments).
 */
function pharmecy_procedure_medicine_cash_received_total($con, $token_no)
{
    $token_no = (int) $token_no;
    if ($token_no < 1) {
        return 0;
    }
    $run = mysqli_query(
        $con,
        "SELECT COALESCE(SUM(amount), 0) AS total
        FROM branch_pending_receive
        WHERE token_no = '$token_no'"
    );
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return (int) round((float) ($row['total'] ?? 0));
    }
    return 0;
}

/**
 * branch_pending_details.id for an active procedure token.
 */
function pharmecy_branch_pending_id_for_token($con, $token_no)
{
    $token_no = (int) $token_no;
    if ($token_no < 1) {
        return 0;
    }
    $run = mysqli_query(
        $con,
        "SELECT id FROM branch_pending_details
        WHERE token_no = '$token_no' AND status = '1'
        ORDER BY id DESC
        LIMIT 1"
    );
    if ($run && ($row = mysqli_fetch_assoc($run))) {
        return (int) ($row['id'] ?? 0);
    }
    return 0;
}

/**
 * Record medicine payment and sync branch_pending_details.amount to issued total.
 */
function pharmecy_record_procedure_medicine_cash_received($con, $token_no, $amount, $user_id, $branch_id, $current_date)
{
    $token_no = (int) $token_no;
    $amount = (float) $amount;
    $user_id = (int) $user_id;
    $branch_id = (int) $branch_id;
    if ($token_no < 1 || $amount <= 0) {
        return false;
    }

    $pending_id = pharmecy_branch_pending_id_for_token($con, $token_no);
    $amount_sql = mysqli_real_escape_string($con, (string) $amount);
    $current_date = mysqli_real_escape_string($con, (string) $current_date);

    $ok = (bool) mysqli_query(
        $con,
        "INSERT INTO branch_pending_receive
        (token_no, pending_id, amount, user_id, branch_id, status, created)
        VALUES
        ('$token_no', '$pending_id', '$amount_sql', '$user_id', '$branch_id', '1', '$current_date')"
    );
    if (!$ok) {
        return false;
    }

    $issued_total = pharmecy_procedure_medicine_cash_received_total($con, $token_no);
    $issued_sql = mysqli_real_escape_string($con, (string) $issued_total);
    mysqli_query(
        $con,
        "UPDATE branch_pending_details SET amount = '$issued_sql'
        WHERE token_no = '$token_no' AND status = '1'"
    );

    return true;
}

/**
 * Keep branch_pending_details.amount aligned with branch_pending_receive totals.
 */
function pharmecy_sync_branch_pending_issued_from_receive($con, $token_no)
{
    $token_no = (int) $token_no;
    if ($token_no < 1) {
        return false;
    }
    $issued_total = pharmecy_procedure_medicine_cash_received_total($con, $token_no);
    $issued_sql = mysqli_real_escape_string($con, (string) $issued_total);
    return (bool) mysqli_query(
        $con,
        "UPDATE branch_pending_details SET amount = '$issued_sql'
        WHERE token_no = '$token_no' AND status = '1'"
    );
}

/**
 * Medicine limit: procedure_tokens_medicine_limits first, else branch_pending_details.amount.
 */
function pharmecy_procedure_medicine_limit_preset($con, $token_no)
{
    return pharmecy_procedure_medicine_limit_for_token($con, $token_no);
}

/**
 * Cart total for current session (items_by_doctor staging list).
 */
function pharmecy_items_by_doctor_cart_amount($con, $user_id, $branch_id, $tokan_type_id = 104)
{
    $user_id = (int) $user_id;
    $branch_id = (int) $branch_id;
    $tokan_type_id = (int) $tokan_type_id;
    $amount = 0.0;

    $run1 = mysqli_query(
        $con,
        "SELECT * FROM `items_by_doctor`
        WHERE branch_id = '$branch_id' AND user_id = '$user_id' AND status = '1'"
    );
    if (!$run1) {
        return 0;
    }

    while ($row1 = mysqli_fetch_assoc($run1)) {
        $fix_dose = (int) $row1['fix_dose'];
        $quantity = ($fix_dose === 0)
            ? (int) $row1['days'] * (int) $row1['dose'] * (float) $row1['feed']
            : $fix_dose;
        if ($quantity < 1) {
            $quantity = 1;
        }
        $item_id = (int) $row1['item_id'];
        $run = mysqli_query(
            $con,
            "SELECT poor, member, general FROM items
            WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id')
            LIMIT 1"
        );
        if (!$run || !($row = mysqli_fetch_assoc($run))) {
            continue;
        }
        if ($tokan_type_id === 102) {
            $amount += (float) $row['poor'] * $quantity;
        } elseif ($tokan_type_id === 103) {
            $amount += (float) $row['member'] * $quantity;
        } elseif ($tokan_type_id === 101) {
            $amount += (float) ($row['poor'] ?? $row['general']) * $quantity;
        } else {
            $amount += (float) $row['general'] * $quantity;
        }
    }

    return (int) round($amount ?? 0);
}

/**
 * Medicine limit for a procedure token (procedure_tokens_medicine_limits or 25% of bill).
 */
function pharmecy_procedure_medicine_limit($con, $token_no, $procedure_amount = 0.0)
{
    $token_no = (int) $token_no;
    $procedure_amount = (float) $procedure_amount;
    if ($token_no < 1) {
        return 0;
    }

    static $limits_table_exists = null;
    if ($limits_table_exists === null) {
        $chk = mysqli_query($con, "SHOW TABLES LIKE 'procedure_tokens_medicine_limits'");
        $limits_table_exists = ($chk && mysqli_num_rows($chk) > 0);
    }

    if ($limits_table_exists) {
        $token_col = pharmecy_procedure_limits_token_column($con);
        if ($token_col !== '') {
            $token_col_sql = '`' . mysqli_real_escape_string($con, $token_col) . '`';
            $run = mysqli_query(
                $con,
                "SELECT * FROM procedure_tokens_medicine_limits
                WHERE $token_col_sql = '$token_no'
                LIMIT 1"
            );
            if ($run && ($row = mysqli_fetch_assoc($run))) {
                foreach (array('medicine_limit', 'limit_amount', 'limit', 'amount') as $col) {
                    if (isset($row[$col]) && (float) $row[$col] > 0) {
                        return (int) $row[$col];
                    }
                }
            }
        }
    }

    if ($procedure_amount <= 0) {
        $procedure_amount = pharmecy_resolve_branch_pending_display_amount($con, $token_no, 0);
    }

    return (int) ($procedure_amount / 100 * 25);
}

/**
 * Issued medicine cash total from branch_pending_receive for this procedure token.
 */
function pharmecy_procedure_issued_medicine_amount($con, $token_no)
{
    return pharmecy_procedure_medicine_cash_received_total($con, $token_no);
}

/**
 * Token + patient + limit/issued/cash for second_procedure_turn_medicines.php.
 *
 * @return array<string, mixed>|null
 */
function pharmecy_load_procedure_medicine_turn($con, $token_no)
{
    $token_no = (int) $token_no;
    if ($token_no < 1) {
        return null;
    }

    $sql = "SELECT t.id, t.doctor_id, t.patient_id, t.cash,
        p.name, p.age, p.gender, bpd.amount AS pending_amount
        FROM tokans t
        INNER JOIN patients p ON t.patient_id = p.id
        LEFT JOIN branch_pending_details bpd ON bpd.token_no = t.id AND bpd.status = '1'
        WHERE t.id = '$token_no'
        LIMIT 1";
    $run = mysqli_query($con, $sql);
    if (!$run || mysqli_num_rows($run) !== 1) {
        return null;
    }

    $row = mysqli_fetch_assoc($run);
    $pending_amount = (float) ($row['pending_amount'] ?? 0);
    $procedure_cash = pharmecy_resolve_branch_pending_display_amount($con, $token_no, $pending_amount);
    if ($procedure_cash <= 0) {
        $procedure_cash = (float) ($row['cash'] ?? 0);
    }

    $row['procedure_cash'] = $procedure_cash;
    $row['medicine_limit'] = pharmecy_procedure_medicine_limit_for_token($con, $token_no);
    $row['issued_medicine'] = pharmecy_procedure_medicine_cash_received_total($con, $token_no);

    return $row;
}

function pharmecy_medicine_selected_cart_options_html($con, $branch_id, $user_id)
{
    $branch_id = (int) $branch_id;
    $user_id = (int) $user_id;
    $sql = "SELECT ibd.id, ibd.fix_dose, ibd.dose, ibd.feed, ibd.days, i.name AS item_name
        FROM item_by_doctor ibd
        INNER JOIN item_register_to_branches irb ON ibd.item_id = irb.id
        INNER JOIN items i ON irb.item_id = i.id
        WHERE ibd.branch_id = '$branch_id' AND ibd.user_id = '$user_id' AND ibd.status = '1'
          AND (ibd.tokan_no IS NULL OR ibd.tokan_no = '' OR ibd.tokan_no = '0')
        ORDER BY i.name ASC";
    $run = mysqli_query($con, $sql);
    if (!$run || mysqli_num_rows($run) < 1) {
        return '<option value="">ADD DATA IN BRANCH</option>';
    }
    $html = '';
    while ($row = mysqli_fetch_assoc($run)) {
        $fix_dose = (int) $row['fix_dose'];
        $quantity = ($fix_dose === 0)
            ? (int) $row['dose'] * (int) $row['days'] * (int) $row['feed']
            : $fix_dose;
        if ($quantity < 1) {
            $quantity = 1;
        }
        $id = (int) $row['id'];
        $name = htmlspecialchars((string) $row['item_name'], ENT_QUOTES, 'UTF-8');
        $html .= '<option value="' . $id . '">' . $name . ' - ' . $quantity . '</option>';
    }
    return $html;
}
