<?php
if (isset($_POST['role_id'])) 
{
    $role_id = $_POST['role_id'];
    $branch_id = $_POST['branch_id'];
    if($role_id == 1)
    {
        header('location: admin_login.php?branch_id='.$branch_id);    
    }    
    elseif($role_id == 2)
    {
        header('location: login.php?branch_id='.$branch_id);    
    }    
    elseif($role_id == 3)
    {
        header('location: dr_login.php?branch_id='.$branch_id);    
    }    
    elseif($role_id == 4)
    {
        header('location: sm_login.php?branch_id='.$branch_id);    
    }    
    elseif($role_id == 6)
    {
        header('location: mm_login.php?branch_id='.$branch_id);    
    } 
    elseif($role_id == 7)
    {
        header('location: login.php?branch_id='.$branch_id);    
    } 
    elseif($role_id == 8)
    {
        header('location: lab_login.php?branch_id='.$branch_id);    
    }
    elseif($role_id == 9)
    {
        header('location: fr_login.php?branch_id='.$branch_id);    
    }
    elseif($role_id == 10)
    {
        header('location: ao_login.php?branch_id='.$branch_id);    
    }
    elseif($role_id == 11)
    {
        header('location: bk_login.php?branch_id='.$branch_id);    
    }
    elseif($role_id == 12)
    {
        header('location: hr_login.php?branch_id='.$branch_id);    
    }
    elseif($role_id == 18)
    {
        header('location: la_login.php?branch_id='.$branch_id);    
    }
    elseif($role_id == 19)
    {
        header('location: lm_login.php?branch_id='.$branch_id);    
    }
    else
    {
        header('location: index.php');
    }
    
}
?>