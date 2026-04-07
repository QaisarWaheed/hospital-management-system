<?php
$conn = new mysqli('localhost', 'ycdoeh1', 'ycdoeh1', 'ycdomlt');
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d G:i:s A');
error_reporting(1);
// session_start();
// if (isset($_SESSION['mm_id'])) {
//     $user_id = $_SESSION['mm_id'];
//     $is_admin = $_SESSION['is_admin'];
//     $is_incharge = $_SESSION['is_incharge'];
//     $role_id = $_SESSION['role_id'];
//     $user_name = $_SESSION['mm_name'];
//     $branch_id = $_SESSION['branch_id'];
//     $branch_name = $_SESSION['branch_name'];
//     $branch_address = $_SESSION['branch_address'];
//     $branch_phone = $_SESSION['branch_phone'];
// }
$con = mysqli_connect('localhost', 'ycdoeh1', 'ycdoeh1', 'ycdomlt');
?>