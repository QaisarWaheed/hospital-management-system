<?php
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d h:i:s A');
error_reporting(1);
session_start();
if (isset($_SESSION['bk_id'])) {
    $bk_id = $_SESSION['bk_id'];
    $bk_name = $_SESSION['bk_name'];
    $bk_branch_id = $_SESSION['branch_id'];
    $bk_is_admin = $_SESSION['is_admin'];;
    $bk_is_incharge = $_SESSION['is_incharge'];
    $bk_branch_name = $_SESSION['branch_name'];
    $bk_branch_address = $_SESSION['branch_address'];
    $bk_branch_phone = $_SESSION['branch_phone'];
}
else
{
//    header('location: logout.php'); 
}
include 'company_info.php'; 
require_once __DIR__ . '/../../includes/db_connect.php';
if(!$con)
{
    echo $con->error;
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

function get_branch_id_by_user_id($id)
{
    $con = $GLOBALS['con'];
    $output = 0;
    $query = "SELECT `branch_id` FROM `users` WHERE `id` = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output = $row['branch_id'];
        }    
    }    
        return $output;
}

function get_doctor_option()
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT users.id, users.u_name, branchs.tag_name FROM `users` INNER JOIN branchs ON users.branch_id = branchs.id WHERE users.status = '1' AND users.role_id = '3' ORDER BY `branchs`.`tag_name` ASC ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $id = $row['id'];
            $name = $row['u_name'];
            $tag_name = $row['tag_name'];
            $output .= '<option value = "'.$id.'">'.$tag_name.' '.$name.'</option>';
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

function weeks_between($datefrom, $dateto)
{
    $datefrom = DateTime::createFromFormat('d/m/Y H:i:s',$datefrom);
    $dateto = DateTime::createFromFormat('d/m/Y H:i:s',$dateto);
    $interval = $datefrom->diff($dateto);
    $week_total = $interval->format('%a')/7;
    return floor($week_total)+1;

}
?>