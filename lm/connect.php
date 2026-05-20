<?php
require_once __DIR__ . '/../includes/ycdo_bootstrap.php';
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d H:i:s');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['lab_manager_user_id'])) {
    header('Location: logout.php');
    exit;
}

$lab_manager_user_id = (int) $_SESSION['lab_manager_user_id'];
$lab_manager_user_name = $_SESSION['lab_manager_user_name'] ?? '';
$lab_manager_login_branch_id = $_SESSION['lab_manager_login_branch_id'] ?? 0;
$lab_manager_login_is_admin = $_SESSION['lab_manager_login_is_admin'] ?? 0;
$lab_manager_login_is_incharge = $_SESSION['lab_manager_login_is_incharge'] ?? 0;
$lab_manager_login_branch_name = $_SESSION['lab_manager_login_branch_name'] ?? '';
$lab_manager_login_branch_address = $_SESSION['lab_manager_login_branch_address'] ?? '';
$lab_manager_login_branch_phone = $_SESSION['lab_manager_login_branch_phone'] ?? '';

if ($lab_manager_user_id < 1) {
    header('Location: logout.php');
    exit;
}

$con = ycdo_db_connect();
$GLOBALS['con'] = $con;

include __DIR__ . '/../lab/includes/company_info.php';

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
