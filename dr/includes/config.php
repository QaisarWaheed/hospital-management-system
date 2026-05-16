<script>
    document.oncontextmenu = function(e) {return false;};
    document.onkeydown = function(e) {
            if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 85 || e.keyCode === 117)) {//Alt+c, Alt+v will also be disabled sadly.
                alert('not allowed');
            return false;
            }
    };
</script>
<?php
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d H:i:s');
session_start();
if (isset($_SESSION['dr_id'])) {
    $user_id = $_SESSION['dr_id'];
    $user_name = $_SESSION['dr_name'];
    $branch_id = $_SESSION['branch_id'];
    $is_admin = $_SESSION['is_admin'];
    $branch_name = $_SESSION['branch_name'];
    $branch_address = $_SESSION['branch_address'];
    $branch_phone = $_SESSION['branch_phone'];
}
else
{
    header('location: logout.php'); 
}

if($user_id < 1 || $user_id == '')
{
    header('location: logout.php'); 
}


// if(substr($current_date,0,10) != substr($login_expire_at,0,10))
// {
//     header('location: logout_with_report.php'); 
// }
 
$con = mysqli_connect('localhost', 'ycdoeh1', 'ycdoeh1', 'ycdomlt');


include 'company_info.php'; 
if(!$con)
    {
        echo $con->error;
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

function show_from_doctors_by_token_id($token_id)
{
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM users INNER JOIN branchs ON users.branch_id = branchs.id WHERE users.id IN (SELECT `from_user_id` FROM `referral_patients` WHERE `opd_token_id` = '$token_id') ");
    if (mysqli_num_rows($run1) == 1)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $consultant_id = $row1['id'];
            $consultant_name = $row1['u_name'];
            $consultant_in_time = date_format(date_create($row1['in_time']), "h:i:s A");
            $consultant_out_time = date_format(date_create($row1['out_time']), "h:i:s A");
            $consultant_qualification = $row1['qualification'];
            // $consultant_qualification = str_replace(',', '</br>&nbsp;&nbsp;<i class="fa fa-file-text" style="font-size:16px;color:green"></i>',$row1['qualification']);
            if($consultant_qualification == ''){$consultant_qualification = "OPD STAFF";}
            $consultant_phone = $row1['phone'];
            $output .= ' <p>&nbsp; <i class="fa fa-user-md" style="font-size:16px;color:green;"></i> '.$consultant_name.'</br>';
            $output .= ' <span style="font-size:10px;">&nbsp; <i class="fa fa-drivers-license" style="font-size:10px;color:green"></i> '.$consultant_qualification.'</span></br>';
            $output .= ' &nbsp; <i class="fa fa-hand-o-right" style="font-size:16px;color:green;"></i>'.$row1['name'].'</br> '.$row1['address'].'</br>';
            $output .= ' &nbsp; <i class="fa fa-envelope" style="font-size:16px;color:green"></i> <i class="fa fa-phone-square" style="font-size:16px;color:green"></i> '.$row1['phone'].'</br>';
            $output .= ' &nbsp; <i class="fa fa-clock-o" style="font-size:16px;color:green"></i> '.$consultant_in_time.' TO '.$consultant_out_time.'</p>';
        }
    }
    else
    {
        return '<p>NO DATA FOUND</p>';
    }
    return $output;
}

function show_to_doctors_by_token_id($token_id)
{
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM users INNER JOIN branchs ON users.branch_id = branchs.id WHERE users.id IN (SELECT `to_user_id` FROM `referral_patients` WHERE `opd_token_id` = '$token_id') ");
    if (mysqli_num_rows($run1) == 1)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $consultant_id = $row1['id'];
            $consultant_name = $row1['u_name'];
            $consultant_in_time = date_format(date_create($row1['in_time']), "h:i:s A");
            $consultant_out_time = date_format(date_create($row1['out_time']), "h:i:s A");
            $consultant_qualification = $row1['qualification'];
            // $consultant_qualification = str_replace(',', '</br>&nbsp;&nbsp;<i class="fa fa-file-text" style="font-size:16px;color:green"></i>',$row1['qualification']);
            if($consultant_qualification == ''){$consultant_qualification = "OPD STAFF";}
            $consultant_phone = $row1['phone'];
            $output .= ' <p>&nbsp; <i class="fa fa-user-md" style="font-size:16px;color:green;"></i> '.$consultant_name.'</br>';
            $output .= ' <span style="font-size:10px;">&nbsp; <i class="fa fa-drivers-license" style="font-size:10px;color:green"></i> '.$consultant_qualification.'</span></br>';
            $output .= ' &nbsp; <i class="fa fa-hand-o-right" style="font-size:16px;color:green;"></i>'.$row1['name'].'</br> '.$row1['address'].'</br>';
            $output .= ' &nbsp; <i class="fa fa-envelope" style="font-size:16px;color:green"></i> <i class="fa fa-phone-square" style="font-size:16px;color:green"></i> '.$row1['phone'].'</br>';
            $output .= ' &nbsp; <i class="fa fa-clock-o" style="font-size:16px;color:green"></i> '.$consultant_in_time.' TO '.$consultant_out_time.'</p>';
        }
    }
    else
    {
        return '<p>NO DATA FOUND</p>';
    }
    return $output;
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

?>