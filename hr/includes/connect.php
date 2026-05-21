<?php
require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';
date_default_timezone_set("Asia/Karachi");
$ip_address = $_SERVER['SERVER_ADDR'] ?? '';
$current_date = date('Y-m-d G:i:s A');
error_reporting(1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['hr_id'])) {
    header('Location: logout.php');
    exit;
}

$hr_id = (int) $_SESSION['hr_id'];
$hr_name = $_SESSION['hr_name'] ?? '';
$hr_branch_id = $_SESSION['branch_id'] ?? 0;
$hr_is_admin = $_SESSION['is_admin'] ?? 0;
$hr_is_incharge = $_SESSION['is_incharge'] ?? 0;
$hr_branch_name = $_SESSION['branch_name'] ?? '';
$hr_branch_address = $_SESSION['branch_address'] ?? '';
$hr_branch_phone = $_SESSION['branch_phone'] ?? '';

if ($hr_id < 1) {
    header('Location: logout.php');
    exit;
}

$con = ycdo_db_connect();

include 'company_info.php';

function get_extra_staff_duty($staff_id, $month, $branch_id = 0)
{
    $map = get_extra_staff_duty_map($month, $branch_id);
    return $map[(int) $staff_id] ?? 0;
}

/** @return array<int, int> staff_id => extra duty count */
function get_extra_staff_duty_map($month, $branch_id = 0)
{
    $con = $GLOBALS['con'];
    $month_esc = mysqli_real_escape_string($con, $month);
    $branch_id = (int) $branch_id;
    $sql = "SELECT arr.releaver_staff_id, COUNT(*) AS extra_cnt
        FROM attendance_releaver_records arr
        INNER JOIN attendance_records ar ON arr.attendance_record_id = ar.attendance_record_id
        WHERE ar.attendance_record_month = '$month_esc'
        AND ar.branch_id = '$branch_id'
        GROUP BY arr.releaver_staff_id";
    $map = array();
    $run = mysqli_query($con, $sql);
    if ($run) {
        while ($row = mysqli_fetch_assoc($run)) {
            $map[(int) $row['releaver_staff_id']] = (int) $row['extra_cnt'];
        }
    }
    return $map;
}

function get_staff_time_in($staff_id)
{
    $quanity = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT `staff_time_in` FROM `staff` WHERE `staff_id` = '$staff_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $quanity = $row['staff_time_in'];
        }    
    }    
    return $quanity;
}

function get_staff_time_out($staff_id)
{
    $quanity = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT `staff_time_out` FROM `staff` WHERE `staff_id` =  '$staff_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $quanity = $row['staff_time_out'];
        }    
    }    
    return $quanity;
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
// REFERREAL
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

function get_branch_tag_name_by_id($id)
{
    $con = $GLOBALS['con'];
    $output = '';
    $query = "SELECT tag_name FROM branchs WHERE id = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) == 1) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output .= $row['tag_name'];
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
    $interval = $from->diff($to);
    $week_total = $interval->format('%a') / 7;
    return (int) floor($week_total) + 1;
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


?>