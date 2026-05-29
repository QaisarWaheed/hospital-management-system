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
