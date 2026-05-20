<?php
require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d H:i:s');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['lab_admin_user_id'])) {
    header('Location: logout.php');
    exit;
}

$lab_admin_user_id = (int) $_SESSION['lab_admin_user_id'];
$lab_admin_user_name = $_SESSION['lab_admin_user_name'] ?? '';
$lab_admin_login_branch_id = $_SESSION['lab_admin_login_branch_id'] ?? 0;
$lab_admin_login_is_admin = $_SESSION['lab_admin_login_is_admin'] ?? 0;
$lab_admin_login_is_incharge = $_SESSION['lab_admin_login_is_incharge'] ?? 0;
$lab_admin_login_branch_name = $_SESSION['lab_admin_login_branch_name'] ?? '';
$lab_admin_login_branch_address = $_SESSION['lab_admin_login_branch_address'] ?? '';
$lab_admin_login_branch_phone = $_SESSION['lab_admin_login_branch_phone'] ?? '';

if ($lab_admin_user_id < 1) {
    header('Location: logout.php');
    exit;
}

$con = ycdo_db_connect();
$GLOBALS['con'] = $con;

include 'company_info.php';

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
function reg_branch_item_id($branch_id, $item_id)
{
    $con = $GLOBALS['con'];
    $output = 0;
    $query = "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) == 1) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output = $row['id'];
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

function get_given_services_by_token_no($token_no)
{
    $quanity = '';
    $ser = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT item_by_doctor.id AS record_id, items.id AS item_id, items.name AS item_name FROM `item_by_doctor` INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id WHERE `tokan_no` = '$token_no' AND items.category_id = 2");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $ser = $ser + 1;
            $quanity .= '<tr class = "td1"><td class = "td1">'.$ser.'</td><td class = "td1">' .$row['item_name'] . '</td><td class = "td1"></td><td class = "td1"></td></tr>';
        }    
    }  
    else
    {
        $quanity .= '<tr class = "td1"><td class = "td1" colspan = "4">NOT A TEST TOKEN</td></tr>';
    }
    return $quanity;
}

function insert_test_by_token_no($token_no)
{
    $quanity = '';
    $ser = 0;
    $user_id = $GLOBALS['lab_user_id'] ?? 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT item_by_doctor.id AS record_id, items.id AS item_id, items.name AS item_name FROM `item_by_doctor` INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id WHERE `tokan_no` = '$token_no' AND items.category_id = 2");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $ser = $ser + 1;
            $item_id = $row['item_id'];
            $insert = "INSERT INTO `lab_tests`
            (`lab_test_id`, `token_no`, `item_id`, `lab_test_status`, `user_id`) 
            VALUES
            (NULL, '$token_no', '$item_id', '1', '$user_id')";
            mysqli_query($GLOBALS['con'], $insert);
        }    
    }
    return $quanity;
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

