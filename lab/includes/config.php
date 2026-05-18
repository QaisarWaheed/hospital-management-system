<?php
if (!isset($con)) {
    require_once __DIR__ . '/../../includes/ycdo_mysqli_vars.php';
    $con = mysqli_connect($ycdo_db_host, $ycdo_db_user, $ycdo_db_pass, $ycdo_db_name);
    if (!$con) {
        die(mysqli_connect_error());
    }
}
