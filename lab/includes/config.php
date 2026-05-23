<?php
if (!isset($con)) {
    require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';
    $con = ycdo_db_connect();
    $GLOBALS['con'] = $con;
}
