<?php 
require_once __DIR__ . '/../includes/db_connect.php';
session_start();
session_destroy();
header('location: ../index.php');
mysqli_close($con); ?>