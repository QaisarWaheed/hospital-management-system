<?php
require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d H:i:s');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['dr_id'])) {
    header('Location: logout.php');
    exit;
}

$user_id = (int) $_SESSION['dr_id'];
$user_name = $_SESSION['dr_name'] ?? '';
$branch_id = $_SESSION['branch_id'] ?? 0;
$is_admin = $_SESSION['is_admin'] ?? 0;
$is_incharge = $_SESSION['is_incharge'] ?? 0;
$branch_name = $_SESSION['branch_name'] ?? '';
$branch_address = $_SESSION['branch_address'] ?? '';
$branch_phone = $_SESSION['branch_phone'] ?? '';

if ($user_id < 1) {
    header('Location: logout.php');
    exit;
}

$con = ycdo_db_connect();
$GLOBALS['con'] = $con;
$GLOBALS['branch_id'] = $branch_id;
$GLOBALS['user_id'] = $user_id;

include 'company_info.php';

function get_doctor_id_by_token_no($token_no)
{
    $output = '';
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT doctor_id FROM tokans WHERE id = '$token_no' ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['doctor_id'];
        }
    }
    return $output;
}
function get_patient_name_by_token_no($token_no)
{
    $output = '';
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id IN (SELECT patient_id FROM tokans WHERE id = '$token_no') ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['name'];
        }
    }
    return $output;
}

function get_patient_phone_by_token_no($token_no)
{
    $output = '';
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id IN (SELECT patient_id FROM tokans WHERE id = '$token_no') ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['phone'];
        }
    }
    return $output;
}

function get_patient_age_by_token_no($token_no)
{
    $output = '';
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT age FROM patients WHERE id IN (SELECT patient_id FROM tokans WHERE id = '$token_no') ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['age'];
        }
    }
    return $output;
}

function get_uname_by_id($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT u_name FROM `users` WHERE `id` = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['u_name'];
        }    
    }    
    return $output;
}

function weeks_between($datefrom, $dateto)
{
    $from = DateTime::createFromFormat('d/m/Y H:i:s', $datefrom);
    $to = DateTime::createFromFormat('d/m/Y H:i:s', $dateto);
    if (!$from || !$to) {
        return 0;
    }
    $interval = $to->diff($from);
    $week_total = $interval->format('%a') / 7;
    return (int) floor($week_total) - 33;
}

function get_branch_name_by_branch_id($id)
{
    $con = $GLOBALS['con'];
    $output = '';
    $query = "SELECT name FROM branchs WHERE id = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output .= $row['name'];
        }    
    }    
        return $output;
}

function get_branch_name_by($id)
{
    $con = $GLOBALS['con'];
    $output = '';
    $query = "SELECT address FROM branchs WHERE id = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output .= $row['address'];
        }    
    }    
        return $output;
}

function get_item_id_by_register_item_id($id)
{
    $con = $GLOBALS['con'];
    $output = '';
    $query = "SELECT `item_id` FROM `item_register_to_branches` WHERE `id` = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0)
    {
        while ( $row = mysqli_fetch_array($run) )
        {
            $output .= $row['item_id'];
        }
    }
        return $output;
}

/**
 * Create branch stock row when missing (e.g. after DB migration).
 */
function ycdo_get_or_create_branch_item_register($branch_id, $item_id)
{
    $branch_id = (int) $branch_id;
    $item_id = (int) $item_id;
    $con = $GLOBALS['con'];
    if ($branch_id < 1 || $item_id < 1 || !$con) {
        return 0;
    }
    $run = mysqli_query($con, "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_id' LIMIT 1");
    if ($run && mysqli_num_rows($run) === 1) {
        $row = mysqli_fetch_array($run);
        return (int) $row['id'];
    }
    mysqli_query($con, "INSERT INTO `item_register_to_branches` (`item_id`, `branch_id`, `quantity`, `status`) VALUES ('$item_id', '$branch_id', '0', '1')");
    return (int) mysqli_insert_id($con);
}

/**
 * Accept either item_register_to_branches.id or bare items.id from the dropdown.
 */
function ycdo_resolve_register_item_id($branch_id, $id)
{
    $branch_id = (int) $branch_id;
    $id = (int) $id;
    $con = $GLOBALS['con'];
    if ($branch_id < 1 || $id < 1 || !$con) {
        return 0;
    }
    $run = mysqli_query($con, "SELECT id FROM `item_register_to_branches` WHERE `id` = '$id' AND `branch_id` = '$branch_id' LIMIT 1");
    if ($run && mysqli_num_rows($run) === 1) {
        return $id;
    }
    $item_check = mysqli_query($con, "SELECT id FROM `items` WHERE `id` = '$id' AND `status` = '1' LIMIT 1");
    if ($item_check && mysqli_num_rows($item_check) === 1) {
        return ycdo_get_or_create_branch_item_register($branch_id, $id);
    }
    return 0;
}

function get_branch_phone_by_branch_id($id)
{
    $con = $GLOBALS['con'];
    $output = '';
    $query = "SELECT phone FROM branchs WHERE id = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output .= $row['phone'];
        }    
    }    
        return $output;
}


function show_departments_option()
{
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `departments` WHERE `department_status` = '1' ORDER BY `department_title` ");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $department_id = $row1['department_id'];
            $department_title = $row1['department_title'];
            $output .= '<option value="'.$department_id.'">'.$department_title.'</option>';   
        }
    }
    else
    {
        return '<option>NO DATA FOUND</option>';
    }
    return $output;
}

function branch_medicines_by_name()
{
    $branch_id = (int) ($GLOBALS['branch_id'] ?? 0);
    $con = $GLOBALS['con'] ?? null;
    if ($branch_id < 1 || !$con) {
        return '<option>NO DATA FOUND</option>';
    }

    $testCategories = '2, 8, 29, 39, 40, 41, 42';
    $medicineCategories = '1, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15, 16, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27';
    $output = '';

    $run1 = mysqli_query($con, "SELECT item_register_to_branches.id AS item_register_id, items.id, items.category_id, items.name, categories.name AS cat_name, item_register_to_branches.quantity AS available_branch_stock FROM `items` INNER JOIN categories ON items.category_id = categories.id INNER JOIN item_register_to_branches ON items.id = item_register_to_branches.item_id WHERE item_register_to_branches.branch_id = '$branch_id' AND items.category_id IN ($testCategories) AND items.status = '1' AND item_register_to_branches.status = '1' ORDER BY items.`name`");
    if ($run1 && mysqli_num_rows($run1) > 0) {
        while ($row1 = mysqli_fetch_array($run1)) {
            $output .= '<option value="'.$row1['item_register_id'].'">'.htmlspecialchars($row1['name']).' - '.htmlspecialchars($row1['cat_name']).'</option>';
        }
    }

    $run2 = mysqli_query($con, "SELECT item_register_to_branches.id AS item_register_id, items.id, items.category_id, items.name, categories.name AS cat_name, item_register_to_branches.quantity AS available_branch_stock FROM `items` INNER JOIN categories ON items.category_id = categories.id INNER JOIN item_register_to_branches ON items.id = item_register_to_branches.item_id WHERE item_register_to_branches.branch_id = '$branch_id' AND items.category_id IN ($medicineCategories) AND items.status = '1' AND item_register_to_branches.status = '1' ORDER BY items.`name`");
    if ($run2 && mysqli_num_rows($run2) > 0) {
        while ($row2 = mysqli_fetch_array($run2)) {
            $label = htmlspecialchars($row2['name']).' - '.htmlspecialchars($row2['cat_name']);
            if ((float) $row2['available_branch_stock'] < 1) {
                $label = 'OUT OF STOCK '.$label;
            }
            $output .= '<option value="'.$row2['item_register_id'].'">'.$label.'</option>';
        }
    }

    if ($output === '') {
        $allCategories = $testCategories.', '.$medicineCategories;
        $fallback = mysqli_query($con, "SELECT items.id AS item_id, items.name AS item_name, categories.name AS cat_name, items.category_id, item_register_to_branches.id AS item_register_id, COALESCE(item_register_to_branches.quantity, 0) AS available_branch_stock FROM `items` INNER JOIN categories ON items.category_id = categories.id LEFT JOIN item_register_to_branches ON items.id = item_register_to_branches.item_id AND item_register_to_branches.branch_id = '$branch_id' AND item_register_to_branches.status = '1' WHERE items.status = '1' AND items.category_id IN ($allCategories) ORDER BY items.`name`");
        if ($fallback && mysqli_num_rows($fallback) > 0) {
            while ($row = mysqli_fetch_array($fallback)) {
                $option_value = $row['item_register_id'] ? (int) $row['item_register_id'] : (int) $row['item_id'];
                $label = htmlspecialchars($row['item_name']).' - '.htmlspecialchars($row['cat_name']);
                $is_medicine_cat = !in_array((int) $row['category_id'], array(2, 8, 29, 39, 40, 41, 42), true);
                if ($is_medicine_cat && (float) $row['available_branch_stock'] < 1) {
                    $label = 'OUT OF STOCK '.$label;
                }
                if (!$row['item_register_id']) {
                    $label .= ' (register on save)';
                }
                $output .= '<option value="'.$option_value.'">'.$label.'</option>';
            }
        }
    }

    if ($output === '') {
        return '<option>NO DATA FOUND</option>';
    }
    return $output;
}

function branch_medicines_by_name2()
{
    $branch_id = $GLOBALS['branch_id'];
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT id,name,category_id FROM `items` WHERE category_id NOT IN (3, 28) AND status = '1' ORDER BY `name` ");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $item_id = $row1['id'];
            $item_name = $row1['name'];
                $category_id = $row1['category_id'];
                $categories = "SELECT * FROM `categories` WHERE id = '$category_id'  ";
                $select_category = mysqli_query($GLOBALS['con'], $categories);
                if (mysqli_num_rows($select_category) == 1) 
                {
                    while ($row_category = mysqli_fetch_array($select_category)) 
                    {
                        $category_name = $row_category['name'];
                    }
                }
            $select2 = "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_id' ";
            $run2 = mysqli_query($GLOBALS['con'], $select2);
            if (mysqli_num_rows($run2) > 0)  
            {
                while ($row2 = mysqli_fetch_array($run2)) 
                {
                    $reg_item_id = $row2['id'];
                 $output .= '<option value="'.$reg_item_id.'">'.$item_name.' - '.$category_name.'</option>';   
                }
            }
            
        }
    }
    if ($output === '') {
        return '<option>NO DATA FOUND</option>';
    }
    return $output;
}

function medicine_selected_by_doctor($token_id)
{
    $output = '';
    $branch_id = $GLOBALS['branch_id'];
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `select_by_doctor` WHERE branch_id = ".$branch_id." AND tokan_no = ".$token_id." AND status = '1' AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (SELECT id FROM `items` WHERE `category_id` != 2)) ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_id = $row['item_id'];
            $fix_dose = $row['fix_dose'];
            if ($fix_dose == 0) 
            {
                $quantity = $row['dose'] * $row['days'] * $row['feed'];
            }
            else
            {
                $quantity = $fix_dose;
            }


            $select1 = "SELECT name FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id')  ";
            $run1 = mysqli_query($GLOBALS['con'], $select1);
            if (mysqli_num_rows($run1) == 1) 
            {
                while ($row1 = mysqli_fetch_array($run1)) 
                {
                    $item_name = $row1['0'];
                }
            }
            $output .= '<a href = "patient_by_token.php?token_id='.$token_id.'&del_medicine='.$row['id'].'" style = "color: red;">X</a>'.$item_name.' - '.$quantity.'</br>';
        }
    }
    else{
        return '';
    }
    return $output;
}

function get_select_amount_array($token_no)
{
    $amount_poor = 0;
    $amount_member = 0;
    $amount_general = 0;
    $select = 'general';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `select_by_doctor` WHERE tokan_no = '$token_no' AND  status = '1' ");
    if (mysqli_num_rows($run1) > 0) 
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $fix_dose = $row1['fix_dose'];
            if($fix_dose == 0)
            {
            $quanity = $row1['days'] * $row1['dose'] * $row1['feed'];
            }
            else
            {
                $quanity = $fix_dose;
            }
            $item_id = $row1['item_id'];
    $run = mysqli_query($GLOBALS['con'], "SELECT poor, member, general FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id') ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $amount_poor = $amount_poor + ($row['0'] * $quanity);
            $amount_member = $amount_member + ($row['1'] * $quanity);
            $amount_general = $amount_general + ($row['2'] * $quanity);
        }
    }
        }
    }
    return array($amount_poor, $amount_member, $amount_general);
}
function test_selected_by_doctor($token_id)
{
    $output = '';
    $branch_id = $GLOBALS['branch_id'];
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `select_by_doctor` WHERE branch_id = ".$branch_id." AND tokan_no = ".$token_id." AND status = '1' AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (SELECT id FROM `items` WHERE `category_id` = 2)) ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_id = $row['item_id'];
            $fix_dose = $row['fix_dose'];
            if ($fix_dose == 0) 
            {
                $quantity = $row['dose'] * $row['days'] * $row['feed'];
            }
            else
            {
                $quantity = $fix_dose;
            }


            $select1 = "SELECT name FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id')  ";
            $run1 = mysqli_query($GLOBALS['con'], $select1);
            if (mysqli_num_rows($run1) == 1) 
            {
                while ($row1 = mysqli_fetch_array($run1)) 
                {
                    $item_name = $row1['0'];
                }
            }
            $output .= '<a href = "patient_by_token.php?token_id='.$token_id.'&del_medicine='.$row['id'].'" style = "color: red;">X</a>'.$item_name.' - '.$quantity.'</br>';
        }
    }
    else{
        return '';
    }
    return $output;
}

function show_department_by_id($department_id)
{
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `departments` WHERE `department_status` = '1' AND `department_id` = '$department_id' ");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $department_id = $row1['department_id'];
            $department_title = $row1['department_title'];
            $output .= '<option value="'.$department_id.'">'.$department_title.'</option>';   
        }
    }
    else
    {
        return '<option>NO DATA FOUND</option>';
    }
    return $output;
}

function get_branch_address($branch_id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT address FROM `branchs` WHERE `id` = '$branch_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['address'];
        }
    }
    else
    {
        $output = 0;
    }    
    return $output;
}


function ycdo_format_user_time($time)
{
    if ($time === null || $time === '' || $time === '00:00:00') {
        return 'N/A';
    }
    $dt = date_create($time);
    return $dt ? date_format($dt, 'h:i A') : 'N/A';
}

/**
 * Referral consultant dropdown (referral_patient_by_token.php).
 * Uses users.department_id + consultant_status — not select_by_doctor.
 */
function show_doctors_by_department_id($department_id)
{
    $department_id = (int) $department_id;
    $con = $GLOBALS['con'] ?? null;
    if ($department_id < 1 || !$con) {
        return '<option value="">NO DATA FOUND</option>';
    }

    $queries = array(
        "SELECT * FROM `users` WHERE `status` = '1' AND `department_id` = '$department_id' AND (`consultant_status` = '1' OR `consultant_status` = 1) ORDER BY `u_name`",
        "SELECT * FROM `users` WHERE `status` = '1' AND `department_id` = '$department_id' AND `role_id` IN (3, 0) ORDER BY `u_name`",
        "SELECT * FROM `users` WHERE `status` = '1' AND `department_id` = '$department_id' ORDER BY `u_name`",
    );

    $output = '';
    foreach ($queries as $sql) {
        $run = mysqli_query($con, $sql);
        if (!$run || mysqli_num_rows($run) < 1) {
            continue;
        }
        while ($row = mysqli_fetch_array($run)) {
            $consultant_id = (int) $row['id'];
            $consultant_name = htmlspecialchars($row['u_name'] ?? '', ENT_QUOTES, 'UTF-8');
            $consultant_branch_name = htmlspecialchars(get_branch_address($row['branch_id'] ?? 0), ENT_QUOTES, 'UTF-8');
            $consultant_in_time = ycdo_format_user_time($row['in_time'] ?? null);
            $consultant_out_time = ycdo_format_user_time($row['out_time'] ?? null);
            $consultant_qualification = htmlspecialchars($row['qualification'] ?? '', ENT_QUOTES, 'UTF-8');
            $output .= '<option value="'.$consultant_id.'">'.$consultant_name.' ('.$consultant_in_time.' - '.$consultant_out_time.') '.$consultant_qualification.' ('.$consultant_branch_name.')</option>';
        }
        if (getenv('YCDO_DEBUG') === '1') {
            error_log('show_doctors_by_department_id dept='.$department_id.' sql='.$sql.' count='.mysqli_num_rows($run));
        }
        break;
    }

    if ($output === '') {
        if (getenv('YCDO_DEBUG') === '1') {
            error_log('show_doctors_by_department_id: no users for department_id='.$department_id);
        }
        return '<option value="">NO DATA FOUND</option>';
    }

    return '<option value="">Select Doctor...</option>'.$output;
}

function get_branch_tag_by($id)
{
    $con = $GLOBALS['con'];
    $output = '';
    $query = "SELECT tag_name FROM branchs WHERE id = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output .= $row['tag_name'];
        }    
    }    
        return $output;
}
?>