<?php 
$con = mysqli_connect('localhost', 'ycdoeh1', 'ycdoeh1', 'ycdomlt');
session_start();
session_destroy();
header('location: ../index.php');
mysqli_close($con); ?>