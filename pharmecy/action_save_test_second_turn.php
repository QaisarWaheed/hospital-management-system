<?php
include 'company_info.php'; 
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d H:i:s');
session_start();
if (isset($_SESSION['ph_id'])) 
{
    $user_id = $_SESSION['ph_id'];
    $login_id = $_SESSION['login_id'];
    $login_expire_at = $_SESSION['login_expire_at'];
    $user_name = $_SESSION['ph_name'];
    $branch_id = $_SESSION['branch_id'];
    $is_admin = $_SESSION['is_admin'];
    $is_incharge = $_SESSION['is_incharge'];
    $branch_name = $_SESSION['branch_name'];
    $branch_address = $_SESSION['branch_address'];
    $branch_phone = $_SESSION['branch_phone'];
}
else
{
    header('location: logout.php'); 
}
if($user_id < 1 || $user_id == '')
{
    header('location: logout.php'); 
}
if(substr($current_date,0,10) != substr($login_expire_at,0,10))
{
    header('location: logout_with_report.php');
}
 
require_once __DIR__ . '/../includes/db_connect.php';