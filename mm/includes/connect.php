<?php
require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d G:i:s A');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['mm_id'])) {
    header('Location: logout.php');
    exit;
}

$user_id = (int) $_SESSION['mm_id'];
$user_name = $_SESSION['mm_name'] ?? '';
$branch_id = $_SESSION['branch_id'] ?? 0;
$is_admin = $_SESSION['is_admin'] ?? 0;
$is_incharge = $_SESSION['is_incharge'] ?? 0;
$role_id = $_SESSION['role_id'] ?? 0;
$branch_name = $_SESSION['branch_name'] ?? '';
$branch_address = $_SESSION['branch_address'] ?? '';
$branch_phone = $_SESSION['branch_phone'] ?? '';

if ($user_id < 1) {
    header('Location: logout.php');
    exit;
}

include 'company_info.php';
$con = ycdo_db_connect();
function available_items_in_store_by_register_item($branch_item_id)
{
    $quanity = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `items` WHERE `id` IN (SELECT item_id FROM `item_register_to_branches` WHERE `id` = '$branch_item_id')");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $quanity = $row['quantity'];
        }    
    }    
    return $quanity;
}

function get_file_password($title)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT `file_password` FROM `files_password` WHERE `file_password_title` LIKE  '$title' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['file_password'];
        }    
    }    
    return $output;
}

function get_store_item_quantity_from_item_id($item_id)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `items` WHERE `id` = '$item_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['quantity'];
        }    
    }    
    return $output;
}

function get_branch_item_quantity_by_id($id)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `item_register_to_branches` WHERE id = '$id'  ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['quantity'];
        }    
    }    
    return $output;
}

function get_br_item_quantity_from_item_id($item_id, $branch_id)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND branch_id = '$branch_id'  ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['quantity'];
        }    
    }    
    return $output;
}

function get_br_item_id_from_item_id($item_id, $branch_id)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND branch_id = '$branch_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['id'];
        }    
    }    
    return $output;
}

function get_br_item_is_update_quantity($item_id, $branch_id)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT is_update_quantity FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND branch_id = '$branch_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['is_update_quantity'];
        }    
    }    
    return $output;
}

function get_party_bill_no($invoice_no)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT `party_invoice_no` FROM `purchase_items` WHERE `invoice_no` = '$invoice_no' LIMIT 0,1 ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['party_invoice_no'];
        }    
    }    
    return $output;
}


function get_branch_item_quantity_from_item_id($item_id)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `item_register_to_branches` WHERE `id` = '$item_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['quantity'];
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

function get_items_id_store_by_register_item($branch_item_id)
{
    $item_id = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT item_id FROM `item_register_to_branches` WHERE `id` = '$branch_item_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_id = $row['item_id'];
        }    
    }    
    return $item_id;
}

function get_uname_by_issue_id($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT u_name FROM `users` WHERE `id` IN (SELECT DISTINCT ba_id FROM item_register_branchs_by_sm WHERE issue_id = '$id') ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['u_name']. ' ';
        }    
    }    
    return $output;
}


function get_branch_name_by($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT address FROM `branchs` WHERE `id` = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['address'];
        }    
    }    
    return $output;
}

function get_branch_tag_name_by($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT tag_name FROM `branchs` WHERE `id` = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['tag_name'];
        }    
    }    
    return $output;
}

function get_role_title_by($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT title FROM `roles` WHERE `id` = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['title'];
        }    
    }    
    return $output;
}

function get_given_services_by_token_no($token_no)
{
    $quanity = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT item_id FROM `item_by_doctor` WHERE `tokan_no` = '$token_no' ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_name = get_item_name_by_register_item_id($row['item_id']);
            $quanity .= $item_name . '</br>';
        }    
    }  
    else
    {
        $quanity .= 'N/A';
    }
    return $quanity;
}

function get_item_quantity_from_item_by_docotr_by_id($id)
{
    $quanity = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT dose, feed, days FROM item_by_doctor WHERE id = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $quanity = $row['dose'] * $row['feed'] * $row['days'];
        }    
    }    
    return $quanity;
}

function get_branch_item_id_from_select_by_doctor_id($id)
{
    $con = $GLOBALS['con'];
    $item_id = '';
    $query = "SELECT item_id FROM item_by_doctor WHERE id = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $item_id = $row['item_id'];
        }    
    }    
        return $item_id;
}

function get_register_item_id_from_item_id($item_id, $branch_idd)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '1' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['id'];
        }    
    }    
    return $output;
}


function get_register_item_quantity_from_item_id($item_id)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `item_register_to_branches` WHERE `id` = '$item_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['quantity'];
        }    
    }    
    return $output;
}

function get_purchase_by_item_id($item_id)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT `per_item_price` FROM `purchase_items` WHERE `item_id` = '$item_id' AND `per_item_price` != 0  ORDER BY `id` DESC limit 0, 1  ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['per_item_price'];
        }    
    }    
    return $output;
}

function get_branch_item_id($item_id, $branch_idd)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_idd' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['id'];
        }
    }
    else
    {
        $output = 0;
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

function get_item_name_by_register_item_id($register_item_id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT name FROM `items` WHERE `id` iN (SELECT item_id FROM `item_register_to_branches` WHERE id = '$register_item_id') ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['name'];
        }
    }
    else
    {
        $output = 0;
    }    
    return $output;
}

function get_item_name_and_category_by_item_id($item_id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT items.name AS item_name, categories.name AS category_name FROM `items` INNER JOIN categories ON items.category_id = categories.id WHERE items.`id` = '$item_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['item_name'].' - '.$row['category_name'];
        }
    }
    else
    {
        $output = 0;
    }    
    return $output;
}

function get_branch_item_quantity($item_id, $branch_idd)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `item_register_to_branches` WHERE `id` = '$item_id'");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['quantity'];
        }
    }
    else
    {
        $output = 0;
    }        
    return $output;
}

function item_available_quantity($item_id)
{
    $quanity = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `items` WHERE `id` = '$item_id' AND `status` = '1' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $quanity = $row['quantity'];
        }
    }    
    return $quanity;
}


function get_purchase_amount($item_id = 18)
{   
    $amount = 0;
    $query = "SELECT per_item_price FROM `purchase_items` WHERE item_id = '18' ";
    $run = mysqli_query($GLOBALS['con'], $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $amount = $row['per_item_price'];
        }
    }
    return $amount;
}

function get_amount($type_id)
{   $amount = 0;
    if($type_id == 1){$select = 'poor';}
    elseif($type_id == 2){$select = 'member';}
    elseif($type_id == 3){$select = 'deserving';}
    elseif($type_id == 4){$select = 'general';}
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE branch_id = ".$GLOBALS['branch_id']." AND user_id = ".$GLOBALS['user_id']." AND status = '1' ");
    if (mysqli_num_rows($run1) > 0) 
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $quanity = $row1['days'] * $row1['dose'];
            $item_id = $row1['item_id'];
    $run = mysqli_query($GLOBALS['con'], "SELECT `$select` FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id') ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $amount = $amount + ($row['0'] * $quanity);
        }
    }
        }
    }
    return $amount;
}

function next_tokan_no()
{
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM tokans ORDER BY id desc limit 0,1");
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) {
            $id = $row['0']+1;
        }
    }
    else{
        return 1;
    }
    return $id;
}

function next_audit_no()
{
    $run = mysqli_query($GLOBALS['con'], "SELECT audit_no FROM branch_audits ORDER BY audit_no desc limit 0,1");
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) {
            $id = $row['0']+1;
        }
    }
    else{
        return 1;
    }
    return $id;
}

function show_category_options($select_value)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `categories` WHERE status = '1' ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $id = $row['id'];
            $name = $row['name'];
            $output .= '<option ';
            if($select_value == $id)
            {
            $output .= 'SELECTED';
            }
            $output .= ' value="'.$id.'">'.$name.'</option>';
        }
    }
    else{
        return '<option value="">NOT DATA AVAILABLE</option>';
    }
    return $output;
}

function show_category_name($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `categories` WHERE id= '$id' ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $name = $row['name'];
            $output .= $name;
        }
    }
    return $output;
}


function show_category_name_by_register_branch_id($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT name FROM `categories` WHERE id IN (SELECT category_id FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$id') )");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $name = $row['name'];
            $output .= $name;
        }
    }
    return $output;
}


function show_purchase_price_by_item_id($item_register_id)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT `purchase` FROM `items` WHERE  `id` IN (SELECT `item_id` FROM `item_register_to_branches` WHERE `id` = '$item_register_id') LIMIT 0,1 ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['purchase'];
        }
    }
    if($output == 0)
    {
        $run = mysqli_query($GLOBALS['con'], "SELECT `per_item_price` FROM `purchase_items` WHERE `item_id` IN (SELECT `item_id` FROM `item_register_to_branches` WHERE `id` = '$item_register_id') ORDER BY `id` DESC LIMIT 0,1 ");
        if (mysqli_num_rows($run) > 0) 
        {
            while ($row = mysqli_fetch_array($run)) 
            {
                $output = $row['per_item_price'];
            }
        }
    }
    return $output;
}


function show_price_by_register_branch_id($id)
{
    $output = '0';
    $run = mysqli_query($GLOBALS['con'], "SELECT `per_item_price` FROM `purchase_items` WHERE `item_id` IN (SELECT `item_id` FROM `item_register_to_branches` WHERE `id` = '$id') LIMIT 0,1 ");
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['per_item_price'];
        }
    }
    return $output;
}


function show_category_name_by_branch_register_item_id($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `categories` WHERE id IN (SELECT category_id FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$id')) ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $name = $row['name'];
            $output .= $name;
        }
    }
    return $output;
}

function show_category_name_itemid($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `categories` WHERE id IN (SELECT category_id FROM items WHERE id =  '$id') ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $name = $row['name'];
            $output .= $name;
        }
    }
    return $output;
}

function show_item_name($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `items` WHERE id= '$id' ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $name = $row['name'];
            $output .= $name;
        }
    }
    return $output;
}


function show_item_name_branch_register_item_id($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `items` WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$id') ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $name = $row['name'];
            $output .= $name;
        }
    }
    return $output;
}


function show_party_name($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `parties` WHERE id= '$id' ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $name = $row['name'];
            $output .= $name;
        }
    }
    return $output;
}

function show_branch_options($select_value)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `branchs` ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $id = $row['id'];
            $name = $row['address'];
            $output .= '<option ';
            if($select_value == $id)
            {
            $output .= 'SELECTED';
            }
            $output .= ' value="'.$id.'">'.$name.'</option>';
        }
    }
    else{
        return '<option value="">NOT DATA AVAILABLE</option>';
    }
    return $output;
}


function branch_medicines()
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_register_to_branches` WHERE branch_id = ".$GLOBALS['branch_id']." ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) {
            $item_id = $row['item_id'];
            $run1 = mysqli_query($GLOBALS['con'], "SELECT name FROM items WHERE id = '$item_id' ");
            if (mysqli_num_rows($run1) == 1) {
                while ($row1 = mysqli_fetch_array($run1)) {
                    $item_name = $row1['0'];
                }
            }
            $output .= '<option value="'.$row['id'].'">'.$item_name.'</option>';
        }
    }
    else{
        return '<option value="">NOT DATA AVAILABLE</option>';
    }
    return $output;
}


function medicine_selected()
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE branch_id = ".$GLOBALS['branch_id']." AND user_id = ".$GLOBALS['user_id']." AND status = '1' ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_id = $row['item_id'];
            $quantity = $row['dose'] * $row['days'];

            $select1 = "SELECT name FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id')  ";
            $run1 = mysqli_query($GLOBALS['con'], $select1);
            if (mysqli_num_rows($run1) == 1) 
            {
                while ($row1 = mysqli_fetch_array($run1)) 
                {
                    $item_name = $row1['0'];
                }
            }
            $output .= '<option value="'.$row['id'].'">'.$item_name.' - '.$quantity.'</option>';
        }
    }
    else{
        return '<option value="">ADD DATA IN BRANCH</option>';
    }
    return $output;
}


function branch_medicines_by_name()
{
    $branch_id = $GLOBALS['branch_id'];
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT id,name,category_id FROM `items` ORDER BY `name` ");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $item_id = $row1['id'];
            $item_name = $row1['name'];
            $category_id = $row1['category_id'];
            $categories = mysqli_query($GLOBALS['con'], "SELECT name FROM `categories` WHERE id = '$category_id' ");
            if (mysqli_num_rows($categories) == 1) 
            {
                while ($row_category = mysqli_fetch_array($categories)) 
                {
                    $cat_name = $row_category['name'];
                }
            }
            $select2 = "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_id' ";
            $run2 = mysqli_query($GLOBALS['con'], $select2);
            if (mysqli_num_rows($run2) > 0)  
            {
                while ($row2 = mysqli_fetch_array($run2)) 
                {
                    $reg_item_id = $row2['id'];
                 $output .= '<option value="'.$reg_item_id.'">'.$item_name.' - '.$cat_name.'</option>';   
                }
            }
            
        }
    }
    else
    {
        return '<option>NO DATA FOUND</option>';
    }
    return $output;
}


function next_patient_id()
{
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM patients ORDER BY id desc limit 0,1");
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) {
            $id = $row['0']+1;
        }
    }
    else{
        return 1;
    }
    return $id;
}

function print_summary($from_date, $to_date, $user_id, $user_name)
{
    $output = '';

    

    return $output;
}

function get_patient_name_by_id($patient_id)
{
    $output = '';
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id = '$patient_id' ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['name'];
        }
    }
    return $output;
}

function get_patient_name_by_token_no($token_no)
{
    $output = '';
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id IN (SELECT `patient_id` FROM `tokans` WHERE `id` = '$token_no') ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['name'];
        }
    }
    return $output;
}

function token_type_title($tokan_type_id)
{
    $output = '';
$get_tokan_type_title = mysqli_query($GLOBALS['con'], "SELECT * FROM tokan_types WHERE id = '$tokan_type_id' ");
if (mysqli_num_rows($get_tokan_type_title) == 1) 
{
    while ($row_tokan_type_title = mysqli_fetch_array($get_tokan_type_title)) 
    {
        $output .= $row_tokan_type_title['title'];
    }
}
return $output;
}

function print_branch_audit($branch_audit_no)
{
$s = 0;
$output = '';
$output .= '<table>';
$select_detail = mysqli_query($GLOBALS['con'], "SELECT * FROM branch_audit_details WHERE audit_no = '$branch_audit_no' ");
if (mysqli_num_rows($select_detail) == 1) 
{
    while ($row_detail = mysqli_fetch_array($select_detail)) 
    {
        $branch_addresss = get_branch_address($row_detail['branch_id']);
        $created = $row_detail['created'];

$output .= '
    <caption style="caption-side: top;text-align: center;">
        <h1>BRANCH AUDIT REPORT</h1>
        <h2>'.$branch_addresss.'</h2>
        <h3>Date And Time : <span>'.date_format(date_create($created), "m:h:i A d-M-Y").'</span></h3>
    </caption>';
    }
}
$select_audit = mysqli_query($GLOBALS['con'], "SELECT * FROM branch_audits WHERE audit_no = '$branch_audit_no' ");
if (mysqli_num_rows($select_audit) > 0) 
{
$output .= '
<thead>
    <tr>
        <th width="10%">S No</th>
        <th width="30%">Item Name</th>
        <th width="20%">Pharmecy Quantity</th>
        <th width="20%">Short</th>
        <th width="20%">Extra</th>
    </tr>
</thead>
</body>';

    while ($row_audit = mysqli_fetch_array($select_audit)) 
    {
        $item_name = get_item_name_by_register_item_id($row_audit['register_item_id']);
        $created = $row_audit['created'];
        $counter_quantity = $row_audit['counter_quantity'];
        $available_quantity = $row_audit['available_quantity'];
        if ($available_quantity >= $counter_quantity) 
        {
            $action_msg = '<span>Short</span>';
$output .= '
    <tr>
        <td>'.++$s.'</td>
        <td>'.$item_name.'</td>
        <td>'.$counter_quantity.'</td>
        <td>'.$action_msg.'</td>
        <td></td>
    </tr>';
        }
        elseif ($available_quantity < $counter_quantity) 
        {
            $action_msg = '<span>Extra</span>';
$output .= '
    <tr>
        <td>'.++$s.'</td>
        <td>'.$item_name.'</td>
        <td>'.$counter_quantity.'</td>
        <td></td>
        <td>'.$action_msg.'</td>
    </tr>';
        }
        else 
        {
            $action_msg = '<span>Same</span>';
$output .= '
    <tr>
        <td>'.++$s.'</td>
        <td>'.$item_name.'</td>
        <td>'.$counter_quantity.'</td>
        <td></td>
        <td></td>
    </tr>';
        }

    }
}
$output .= '</tbody>';
$output .= '</table>';
return $output;
}

function print_tokan($id)
{
    $output = '';
    $output .= '';
    $doctor_name = 'no name';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM tokans WHERE id = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {

            $cash_received = $row['cash_received'];
            $created = $row['created'];
            $tokan_type_id = $row['tokan_type_id'];
            $get_tokan_type = mysqli_query($GLOBALS['con'], "SELECT * FROM tokan_types WHERE id = '$tokan_type_id' ");
            if (mysqli_num_rows($get_tokan_type) == 1) 
            {
                while ($row_tokan_type = mysqli_fetch_array($get_tokan_type)) 
                {
                    $tokan_type_title = $row_tokan_type['title'];
                }
            }

            $doctor_id = $row['doctor_id'];
            $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE id = '$doctor_id' ");
            if (mysqli_num_rows($get_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($get_patient)) 
                {
                    $doctor_name = $row_patient['u_name'];
                }
            }


            $user_id = $row['user_id'];
            $get_user = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE id = '$user_id' ");
            if (mysqli_num_rows($get_user) == 1) 
            {
                while ($row_user = mysqli_fetch_array($get_user)) 
                {
                    $username = $row_user['u_name'];
                }
            }

            $patient_id = $row['patient_id'];
            $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id = '$patient_id' ");
            if (mysqli_num_rows($get_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($get_patient)) 
                {
                    $name = $row_patient['name'];
                    $age = $row_patient['age'];
                    $gender = $row_patient['gender'];
                    if($gender == 1){$gender_title = 'Female';}
                    elseif($gender == 2){$gender_title = 'Male';}
                    else{$gender_title = 'Other';}
                }
            }
        }
    }
    $output .= '
<table style="font-size: 10px;">
<caption style="text-align: center;caption-side: top;margin: auto auto;min-width: 80mm;"><strong>
    <h5 align="center">YCDO Central Hospital</h5>
    <h6 align="center">Masoom shah Road, Multan</h6>
    <h6 align="center">UAN : 0304-1110222</h6>
    <h5 align="center"> '.$id.' / '.date('y').'</h5>
</strong></caption>
    <tr>
        <td>Date & Time</td>
        <td>'.date_format(date_create($created),'d-M-Y h:i:s A').'</td>
    </tr>
    <tr>
        <td>Name:</td>
        <td>'.$name.'</td>
    </tr>
    <tr>
        <td>Age:</td>
        <td>'.$age.'</td>
    </tr>
    <tr>
        <td>Gender:</td>
        <td>'.$gender_title.'</td>
    </tr>
    <tr>
        <td>Ref. To Dr.:</td>
        <td>'.$doctor_name.'</td>
    </tr>
    <tr>
        <td>Tokan_Type:</td>
        <td>'.$tokan_type_title.'</td>
    </tr>
    <tr>
        <td>Cash Received:</td>
        <td>'.$cash_received.'</td>
    </tr>
    <tr>
        <td>Tokan_By:</td>
        <td>'.$username.'</td>
    </tr>
    <tr>
        <td>Print Time</td>
        <td>'.date('d-M-Y h:i:s A').'</td>
    </tr>
</table>
    ';
    return $output;
}

function print_medicine_slip($id)
{
    $output = '';
    $output .= '';
    $doctor_name = 'no name';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM tokans WHERE id = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {

            $cash_received = $row['cash_received'];
            $cash = $row['cash'];
            $created = $row['created'];
            $tokan_type_id = $row['tokan_type_id'];
            $get_tokan_type = mysqli_query($GLOBALS['con'], "SELECT * FROM tokan_types WHERE id = '$tokan_type_id' ");
            if (mysqli_num_rows($get_tokan_type) == 1) 
            {
                while ($row_tokan_type = mysqli_fetch_array($get_tokan_type)) 
                {
                    $tokan_type_title = $row_tokan_type['title'];
                }
            }

            $doctor_id = $row['doctor_id'];
            $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE id = '$doctor_id' ");
            if (mysqli_num_rows($get_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($get_patient)) 
                {
                    $doctor_name = $row_patient['u_name'];
                }
            }


            $user_id = $row['user_id'];
            $get_user = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE id = '$user_id' ");
            if (mysqli_num_rows($get_user) == 1) 
            {
                while ($row_user = mysqli_fetch_array($get_user)) 
                {
                    $username = $row_user['u_name'];
                }
            }

            $patient_id = $row['patient_id'];
            $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id = '$patient_id' ");
            if (mysqli_num_rows($get_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($get_patient)) 
                {
                    $name = $row_patient['name'];
                    $age = $row_patient['age'];
                    $gender = $row_patient['gender'];
                    if($gender == 1){$gender_title = 'Female';}
                    elseif($gender == 2){$gender_title = 'Male';}
                    else{$gender_title = 'Other';}
                }
            }
        }
    }
    $output .= '
<table style="font-size: 10px;">
<caption style="text-align: center;caption-side: top;margin: auto auto;min-width: 80mm;"><strong>
    <h5 align="center">YCDO Central Hospital</h5>
    <h6 align="center">Masoom shah Road, Multan</h6>
    <h6 align="center">UAN : 0304-1110222</h6>
    <h5 align="center"> '.$id.' / '.date('y').'</h5>
    <h5 align="center;line-height: 0px; "> Prescription & Diagnostic</h5>
</strong></caption>
    <tr>
        <td>Date & Time</td>
        <td>'.date_format(date_create($created),'d-M-Y h:i:s A').'</td>
    </tr>
    <tr>
        <td>Name:</td>
        <td>'.$name.'</td>
    </tr>
    <tr>
        <td>Age:</td>
        <td>'.$age.'</td>
    </tr>
    <tr>
        <td>Gender:</td>
        <td>'.$gender_title.'</td>
    </tr>
    <tr>
        <td>Checked By Dr.:</td>
        <td>'.$doctor_name.'</td>
    </tr>
    <tr>
        <td>Tokan_Type:</td>
        <td>'.$tokan_type_title.'</td>
    </tr>
    <tr>
        <td>Total Amount:</td>
        <td>'.$cash.'</td>
    </tr>
    <tr>
        <td>Received Amount:</td>
        <td>'.$cash_received.'</td>
    </tr>
    <tr>
        <td>Tokan_By:</td>
        <td>'.$username.'</td>
    </tr>
    <tr>
        <td>Print Time</td>
        <td>'.date('d-M-Y h:i:s A').'</td>
    </tr>';
    $output .= '
    <tr>
        <td colspan="2"><strong>Tests / Medicines as Follow:</strong></td>
    </tr>';
$get_selected_medicines = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE `tokan_no` = '$id' ");
if (mysqli_num_rows($get_selected_medicines) > 0) {
    while ($row_selected_medicines = mysqli_fetch_array($get_selected_medicines)) {
        $item_id = get_item_name_by_register_item_id($row_selected_medicines['item_id']);
        $dose = $row_selected_medicines['dose'];
        $quantity = $dose * $row_selected_medicines['days'];
        $feed = $row_selected_medicines['feed'];
        $select_feed = "SELECT * FROM `feeds` WHERE `id` = '$feed' ";
        $get_feed_str = mysqli_query($GLOBALS['con'], $select_feed);
        if (mysqli_num_rows($get_feed_str) > 0) {
            while ($row_feed_str = mysqli_fetch_array($get_feed_str)) {
                $feed_title = $row_feed_str['title'];
            }
        }
        if ($dose == 1) {$dose_title = 'صبح ';}
        elseif ($dose == 2){$dose_title = 'صبح شام';}
        elseif ($dose == 3){$dose_title = 'صبح   دوپہر شام';}
$output .= '
    <tr>
        <td colspan="2">'.$item_id.' ('.$quantity.')</br>'.$feed_title.' '.$dose_title.'</td>
    </tr>';
    }
}
echo '</table>';
    return $output;
}

?>