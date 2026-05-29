<?php
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d H:i:s');
$ip_address = $_SERVER['SERVER_ADDR'];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['ph_id'])) {
    header('Location: logout.php');
    exit;
}

$user_id = (int) $_SESSION['ph_id'];
$login_id = $_SESSION['login_id'] ?? 0;
$login_expire_at = $_SESSION['login_expire_at'] ?? '';
$user_name = $_SESSION['ph_name'] ?? '';
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

if ($login_expire_at !== '' && substr($current_date, 0, 10) !== substr($login_expire_at, 0, 10)) {
    header('Location: logout_with_report.php');
    exit;
}

$db_host = getenv('DB_HOST');
if ($db_host === false || $db_host === '') {
    $db_host = file_exists('/.dockerenv') ? 'srv-captain--mysql-db' : 'localhost';
}
if ($db_host === 'localhost' && PHP_OS_FAMILY !== 'Windows') {
    $db_host = '127.0.0.1';
}
$con = mysqli_connect($db_host, getenv('DB_USER') ?: 'ycdoeh1', getenv('DB_PASS') ?: 'ycdoeh1', getenv('DB_NAME') ?: 'ycdomlt');
if (!$con) {
    die(mysqli_connect_error());
}

include 'company_info.php';

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

function show_from_doctors_by_token_id($token_id)
{
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM users INNER JOIN branchs ON users.branch_id = branchs.id WHERE users.id IN (SELECT `from_user_id` FROM `referral_patients` WHERE `opd_token_id` = '$token_id') ");
    if (mysqli_num_rows($run1) == 1)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $consultant_id = $row1['id'];
            $consultant_name = $row1['u_name'];
            $consultant_in_time = date_format(date_create($row1['in_time']), "h:i:s A");
            $consultant_out_time = date_format(date_create($row1['out_time']), "h:i:s A");
            $consultant_qualification = $row1['qualification'];
            // $consultant_qualification = str_replace(',', '</br>&nbsp;&nbsp;<i class="fa fa-file-text" style="font-size:16px;color:green"></i>',$row1['qualification']);
            if($consultant_qualification == ''){$consultant_qualification = "OPD STAFF";}
            $consultant_phone = $row1['phone'];
            $output .= ' <p>&nbsp; <i class="fa fa-user-md" style="font-size:16px;color:green;"></i> '.$consultant_name.'</br>';
            $output .= ' <span style="font-size:10px;">&nbsp; <i class="fa fa-drivers-license" style="font-size:10px;color:green"></i> '.$consultant_qualification.'</span></br>';
            $output .= ' &nbsp; <i class="fa fa-hand-o-right" style="font-size:16px;color:green;"></i>'.$row1['name'].'</br> '.$row1['address'].'</br>';
            $output .= ' &nbsp; <i class="fa fa-envelope" style="font-size:16px;color:green"></i> <i class="fa fa-phone-square" style="font-size:16px;color:green"></i> '.$row1['phone'].'</br>';
            $output .= ' &nbsp; <i class="fa fa-clock-o" style="font-size:16px;color:green"></i> '.$consultant_in_time.' TO '.$consultant_out_time.'</p>';
        }
    }
    else
    {
        return '<p>NO DATA FOUND</p>';
    }
    return $output;
}

function show_to_doctors_by_token_id($token_id)
{
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM users INNER JOIN branchs ON users.branch_id = branchs.id WHERE users.id IN (SELECT `to_user_id` FROM `referral_patients` WHERE `opd_token_id` = '$token_id') ");
    if (mysqli_num_rows($run1) == 1)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $consultant_id = $row1['id'];
            $consultant_name = $row1['u_name'];
            $consultant_in_time = date_format(date_create($row1['in_time']), "h:i:s A");
            $consultant_out_time = date_format(date_create($row1['out_time']), "h:i:s A");
            $consultant_qualification = $row1['qualification'];
            // $consultant_qualification = str_replace(',', '</br>&nbsp;&nbsp;<i class="fa fa-file-text" style="font-size:16px;color:green"></i>',$row1['qualification']);
            if($consultant_qualification == ''){$consultant_qualification = "OPD STAFF";}
            $consultant_phone = $row1['phone'];
            $output .= ' <p>&nbsp; <i class="fa fa-user-md" style="font-size:16px;color:green;"></i> '.$consultant_name.'</br>';
            $output .= ' <span style="font-size:10px;">&nbsp; <i class="fa fa-drivers-license" style="font-size:10px;color:green"></i> '.$consultant_qualification.'</span></br>';
            $output .= ' &nbsp; <i class="fa fa-hand-o-right" style="font-size:16px;color:green;"></i>'.$row1['name'].'</br> '.$row1['address'].'</br>';
            $output .= ' &nbsp; <i class="fa fa-envelope" style="font-size:16px;color:green"></i> <i class="fa fa-phone-square" style="font-size:16px;color:green"></i> '.$row1['phone'].'</br>';
            $output .= ' &nbsp; <i class="fa fa-clock-o" style="font-size:16px;color:green"></i> '.$consultant_in_time.' TO '.$consultant_out_time.'</p>';
        }
    }
    else
    {
        return '<p>NO DATA FOUND</p>';
    }
    return $output;
}


?>