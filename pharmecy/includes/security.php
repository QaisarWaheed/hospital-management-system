<?php
session_start();
if (!isset($_SESSION['ph_id'])) 
{    
    header('location: logout.php'); 
    
}
?>