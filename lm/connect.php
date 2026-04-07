<?php
include '../lab/includes/company_info.php';
session_start();
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d H:i:s');
if (isset($_SESSION['lab_manager_user_id'])) {
    $lab_manager_user_id = $_SESSION['lab_manager_user_id'];
    $lab_manager_user_name = $_SESSION['lab_manager_user_name'];
    $lab_manager_login_branch_id = $_SESSION['lab_manager_login_branch_id'];
    $lab_manager_login_is_admin = $_SESSION['lab_manager_login_is_admin'];
    $lab_manager_login_is_incharge = $_SESSION['lab_manager_login_is_incharge'];
    $lab_manager_login_branch_name = $_SESSION['lab_manager_login_branch_name'];
    $lab_manager_login_branch_address = $_SESSION['lab_manager_login_branch_address'];
    $lab_manager_login_branch_phone = $_SESSION['lab_manager_login_branch_phone'];
}
else
{
    header('location: logout.php'); 
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
?>