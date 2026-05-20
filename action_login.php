<?php
require_once __DIR__ . '/includes/ycdo_bootstrap.php';
if (isset($_POST['role_id'])) 
{
    $role_id = $_POST['role_id'];
    $branch_id = $_POST['branch_id'];
    if($role_id == 1)
    {
        header('Location: admin_login.php?branch_id='.$branch_id);
        exit;
    }    
    elseif($role_id == 2)
    {
        header('Location: login.php?branch_id='.$branch_id);
        exit;
    }    
    elseif($role_id == 3)
    {
        header('Location: dr_login.php?branch_id='.$branch_id);
        exit;
    }    
    elseif($role_id == 4)
    {
        header('Location: sm_login.php?branch_id='.$branch_id);
        exit;
    }    
    elseif($role_id == 6)
    {
        header('Location: mm_login.php?branch_id='.$branch_id);
        exit;
    } 
    elseif($role_id == 7)
    {
        header('Location: login.php?branch_id='.$branch_id);
        exit;
    } 
    elseif($role_id == 8)
    {
        header('Location: lab_login.php?branch_id='.$branch_id);
        exit;
    }
    elseif($role_id == 9)
    {
        header('Location: fr_login.php?branch_id='.$branch_id);
        exit;
    }
    elseif($role_id == 10)
    {
        header('Location: ao_login.php?branch_id='.$branch_id);
        exit;
    }
    elseif($role_id == 11)
    {
        header('Location: bk_login.php?branch_id='.$branch_id);
        exit;
    }
    elseif($role_id == 12)
    {
        header('Location: hr_login.php?branch_id='.$branch_id);
        exit;
    }
    elseif($role_id == 18)
    {
        header('Location: la_login.php?branch_id='.$branch_id);
        exit;
    }
    elseif($role_id == 19)
    {
        header('Location: lm_login.php?branch_id='.$branch_id);
        exit;
    }
    else
    {
        header('Location: index.php');
        exit;
    }
    
}
?>