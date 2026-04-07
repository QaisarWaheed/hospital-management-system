<?php 
$con = mysqli_connect('localhost', 'ycdoeh1', 'ycdoeh1', 'ycdomlt');
session_start();
if (isset($_SESSION['ph_id'])) 
{
    $user_id = $_SESSION['ph_id'];
    $loginid = $_SESSION['login_id'];
    mysqli_query($con, "INSERT INTO logout_user (login_id, user_id) VALUES ('$loginid', '$user_id')");
}
session_destroy();
header('location: ../index.php');
?>