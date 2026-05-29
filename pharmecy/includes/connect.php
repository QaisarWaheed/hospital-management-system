<?php
require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d H:i:s');
$ip_address = $_SERVER['SERVER_ADDR'];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['ph_id'])) {
    header('Location: logout.php');
    exit;
}

$user_id = (int) $_SESSION['ph_id'];
$login_id = $_SESSION['login_id'] ?? 0;
$login_expire_at = $_SESSION['login_expire_at'] ?? '';
$user_name = $_SESSION['ph_name'] ?? '';
$branch_id = $_SESSION['branch_id'] ?? 0;
$is_admin = $_SESSION['is_admin'] ?? 0;
$is_incharge = $_SESSION['is_incharge'] ?? 0;
$branch_name = $_SESSION['branch_name'] ?? '';
$branch_address = $_SESSION['branch_address'] ?? '';
$branch_phone = $_SESSION['branch_phone'] ?? '';
if (!isset($role_title)) {
    $role_title = '';
}

if ($user_id < 1) {
    header('Location: logout.php');
    exit;
}

if ($login_expire_at !== '' && substr($current_date, 0, 10) !== substr($login_expire_at, 0, 10)) {
    header('Location: logout_with_report.php');
    exit;
}

if (empty($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

$con = ycdo_db_connect();


//Check Expire Login
// $search = "SELECT `login_expire_at` FROM `logins_detail` WHERE `id` = '$login_id' ";
// $run = mysqli_query($con, $search);
// if(mysqli_num_rows($run) == 1)
// {
//     while($row = mysqli_fetch_array($run))
//     {
//         $logout_at = $row['logout_at'];
//         if($logout_at < $current_date)
//         {
//              echo "<script>alert('Receive Medicines SUCCESSFULLY')</script>";
//             // echo "<script>alert('".$search."')</script>";
//             header('location: '.$search);
//         }
//     }
// }

include 'company_info.php'; 
//$con = mysqli_connect('184.168.103.144', 'anmol', 'Anmol_122', 'ycdo');
if (!$con) {
    die(mysqli_connect_error());
}


function get_staff_time_in($staff_id)
{
    $quanity = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT `staff_time_in` FROM `staff` WHERE `staff_id` = '$staff_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $quanity = $row['staff_time_in'];
        }    
    }    
    return $quanity;
}

function get_staff_time_out($staff_id)
{
    $quanity = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT `staff_time_out` FROM `staff` WHERE `staff_id` =  '$staff_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $quanity = $row['staff_time_out'];
        }    
    }    
    return $quanity;
}

function available_items_in_store_by_register_item($branch_item_id)
{
    $quanity = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `items` WHERE `id` IN (SELECT item_id FROM `item_register_to_branches` WHERE `id` = '$branch_item_id')");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $quanity = $row['quantity'];
        }    
    }    
    return $quanity;
}

function get_select_amount_array()
{
    $amount_poor = 0;
    $amount_member = 0;
    $amount_general = 0;
    $select = 'general';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `items_by_doctor` WHERE user_id = ".$GLOBALS['user_id']." AND status = '1' ");
    if (mysqli_num_rows($run1) > 0) 
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $fix_dose = $row1['fix_dose'];
            if($fix_dose == 0)
            {
            $quanity = $row1['days'] * $row1['dose'] * $row1['feed'];
            }
            else
            {
                $quanity = $fix_dose;
            }
            $item_id = $row1['item_id'];
    $run = mysqli_query($GLOBALS['con'], "SELECT poor, member, general FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id') ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $amount_poor = $amount_poor + ($row['0'] * $quanity);
            $amount_member = $amount_member + ($row['1'] * $quanity);
            $amount_general = $amount_general + ($row['2'] * $quanity);
        }
    }
        }
    }
    return array($amount_poor, $amount_member, $amount_general);
}

function medicine_select_list_by_doctor_turn($token_no)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `items_by_doctor` WHERE user_id = ".$GLOBALS['user_id']." AND status = '1' ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_id = $row['item_id'];
            $fix_dose = $row['fix_dose'];
            if ($fix_dose == 0) 
            {
                $quantity = $row['dose'] * $row['days'] * $row['feed'];
            }
            else
            {
                $quantity = $fix_dose;
            }


            $select1 = "SELECT name FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id')  ";
            $run1 = mysqli_query($GLOBALS['con'], $select1);
            if (mysqli_num_rows($run1) == 1) 
            {
                while ($row1 = mysqli_fetch_array($run1)) 
                {
                    $item_name = $row1['0'];
                }
            }
            $output .= '<a href = "action_del_medicine_doctor_turn.php?del_medicine='.$row['id'].'&search_tokan_no='.$token_no.'" style = "color: red;">X </a>'.$item_name.' - '.$quantity.'</br>';
        }
    }
    else{
        return '<p>ADD DATA IN BRANCH</p>';
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
            $consultant_in_time = (!empty($row1['in_time']) && $row1['in_time'] != '0000-00-00' && $row1['in_time'] != '0000-00-00 00:00:00')
                ? date_format(date_create($row1['in_time']), "h:i:s A")
                : '';
            $consultant_out_time = (!empty($row1['out_time']) && $row1['out_time'] != '0000-00-00' && $row1['out_time'] != '0000-00-00 00:00:00')
                ? date_format(date_create($row1['out_time']), "h:i:s A")
                : '';
            $consultant_qualification = str_replace(',', '</br>',$row1['qualification']);
            if($consultant_qualification == ''){$consultant_qualification = "OPD STAFF";}
            $consultant_phone = $row1['phone'];
            $output .= '<h4>'.$consultant_name.'</h4>';
            $output .= '<h4>'.$consultant_in_time.' TO '.$consultant_out_time.'</h4>';
            $output .= '<h4>'.$consultant_qualification.'</h4>';
            $output .= '<h4>'.$row1['address'].'</h4>';
            $output .= '<h4>'.$row1['phone'].'</h4>';
        }
    }
    else
    {
        return '<h4>NO DATA FOUND</h4>';
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
            $consultant_in_time = (!empty($row1['in_time']) && $row1['in_time'] != '0000-00-00' && $row1['in_time'] != '0000-00-00 00:00:00')
                ? date_format(date_create($row1['in_time']), "h:i:s A")
                : '';
            $consultant_out_time = (!empty($row1['out_time']) && $row1['out_time'] != '0000-00-00' && $row1['out_time'] != '0000-00-00 00:00:00')
                ? date_format(date_create($row1['out_time']), "h:i:s A")
                : '';
            $consultant_qualification = str_replace(',', '</br>',$row1['qualification']);
            if($consultant_qualification == ''){$consultant_qualification = "OPD STAFF";}
            $consultant_phone = $row1['phone'];
            $output .= '<h4>'.$consultant_name.'</h4>';
            $output .= '<h4>'.$consultant_in_time.' TO '.$consultant_out_time.'</h4>';
            $output .= '<h4>'.$consultant_qualification.'</h4>';
            $output .= '<h4>'.$row1['address'].'</h4>';
            $output .= '<h4>'.$row1['phone'].'</h4>';
        }
    }
    else
    {
        return '<h4>NO DATA FOUND</h4>';
    }
    return $output;
}

function get_store_item_quantity_from_item_id($item_id)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `items` WHERE `id` = '$item_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['quantity'];
        }    
    }    
    return $output;
}

function get_branch_item_quantity_from_item_id($item_id)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `item_register_to_branches` WHERE `id` = '$item_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['quantity'];
        }    
    }    
    return $output;
}

function get_branch_item_id_from_item_id($item_id, $branch_idd)
{
    $item_id = (int) $item_id;
    $branch_idd = (int) $branch_idd;
    $con = $GLOBALS['con'] ?? null;
    if ($item_id < 1 || $branch_idd < 1 || !$con) {
        return 0;
    }
    $output = 0;
    $run = mysqli_query($con, "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_idd' LIMIT 1");
    if ($run && mysqli_num_rows($run) === 1) {
        $row = mysqli_fetch_array($run);
        $output = (int) $row['id'];
    }
    if ($output < 1) {
        mysqli_query($con, "INSERT INTO `item_register_to_branches` (`item_id`, `branch_id`, `quantity`, `status`) VALUES ('$item_id', '$branch_idd', '0', '1')");
        $output = (int) mysqli_insert_id($con);
    }
    return $output;
}



function get_items_id_store_by_register_item($branch_item_id)
{
    $item_id = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT item_id FROM `item_register_to_branches` WHERE `id` = '$branch_item_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_id = $row['item_id'];
        }    
    }    
    return $item_id;
}

function get_uname_and_time_by_id($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT id, u_name, in_time, out_time FROM `users` WHERE `id` = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $doctor_id = $row['id'];
            $output .= '<a href = "show_referred_pateints_to_docotor.php?dr_id='.$doctor_id.'" class = "btn btn-sm btn-ouline-success">'. $row['u_name']. '</a><br>(' .$row['in_time'] . 'To: ' . $row['out_time'] .' )';
        }    
    }    
    return $output;
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

function get_user_phone_by_id($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT phone FROM `users` WHERE `id` = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['phone'];
        }    
    }    
    return $output;
}

function get_item_name_by_id($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT name FROM `items` WHERE `id` = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['name'];
        }    
    }    
    return $output;
}

function get_total_token_cash($id, $login_at, $logout_at)
{
    $output = 0;
    $query = "SELECT sum(cash) FROM `tokans` WHERE `user_id` = '$id' AND created > '$login_at' AND created < '$logout_at' AND status = '1' ";
    $run = mysqli_query($GLOBALS['con'], $query);
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['0'];
        }    
    }    
    return $output;
}

function get_total_token_cash_received($id, $login_at, $logout_at)
{
    $output = 0;
    $query = "SELECT sum(cash_received) FROM `tokans` WHERE `user_id` = '$id' AND created > '$login_at' AND created < '$logout_at' AND `status` = '1' ";
    $run = mysqli_query($GLOBALS['con'], $query);
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['0'];
        }    
    }    
    return $output;
}

function get_total_donation_collection($id, $login_at, $logout_at)
{
    $output = 0;
    $query = "SELECT sum(amount) FROM `donation_collection` WHERE `user_id` = '$id' AND created > '$login_at' AND created < '$logout_at' ";
    $run = mysqli_query($GLOBALS['con'], $query);
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['0'];
        }    
    }    
    return $output;
}

function login_time($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT login_at FROM `logins_detail` WHERE `user_id` = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['login_at'];
        }    
    }    
    return $output;
}

function show_role_by_user_id($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT title FROM `roles` WHERE `id` = (SELECT role_id FROM users WHERE id = '$id') ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['title'];
        }    
    }    
    return $output;
}


function get_pending_id($branch_id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM `items` WHERE category_id = '3' AND `id` IN (SELECT `item_id` FROM item_register_to_branches WHERE id IN (SELECT item_id FROM item_by_doctor WHERE tokan_no = '$token_no') ) ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['name'] . "</br>";
        }    
    }    
    return $output;
}

function get_procedure_name($token_no)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT name FROM `items` WHERE category_id IN (3, 37, 38) AND `id` IN (SELECT `item_id` FROM item_register_to_branches WHERE id IN (SELECT item_id FROM item_by_doctor WHERE tokan_no = '$token_no') ) ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['name'] . "</br>";
        }    
    }    
    return $output;
}

function get_item_quantity_from_item_by_docotr_by_id($id)
{
    $quantity = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT dose, feed, days, fix_dose FROM item_by_doctor WHERE id = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $fix_dose = $row['fix_dose'];
            if ($fix_dose == 0) 
            {
                $quantity = $row['dose'] * $row['feed'] * $row['days'];
            }
            else
            {
                $quantity = $fix_dose;
            }
        }    
    }    
    return $quantity;
}

function get_branch_item_id_from_select_by_doctor_id($id)
{
    $con = $GLOBALS['con'];
    $item_id = '';
    $query = "SELECT item_id FROM item_by_doctor WHERE id = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $item_id = $row['item_id'];
        }    
    }    
        return $item_id;
}

function get_relationship_title_by_id($relationship_id)
{
    $con = $GLOBALS['con'];
    $output = '';
    $query = "SELECT * FROM `relationships` WHERE `relationship_id` = '$relationship_id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output .= $row['relationship_title'];
        }    
    }    
        return $output;    
}

function get_designation_title_by_id($designation_id)
{
    $con = $GLOBALS['con'];
    $output = '';
    $query = "SELECT * FROM `designations` WHERE `designation_id` = '$designation_id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output .= $row['designation_title'];
        }    
    }    
        return $output;    
}

function get_branch_name_by($id)
{
    $con = $GLOBALS['con'];
    $output = '';
    $query = "SELECT address FROM branchs WHERE id = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output .= $row['address'];
        }    
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

function get_branch_name_by_branch_id($id)
{
    $con = $GLOBALS['con'];
    $output = '';
    $query = "SELECT name FROM branchs WHERE id = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output .= $row['name'];
        }    
    }    
        return $output;
}

function get_branch_phone_by_branch_id($id)
{
    $con = $GLOBALS['con'];
    $output = '';
    $query = "SELECT phone FROM branchs WHERE id = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output .= $row['phone'];
        }    
    }    
        return $output;
}

function get_register_item_id_from_item_id($item_id, $branch_idd)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_idd' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['id'];
        }    
    }    
    return $output;
}

function get_register_item_quantity_from_item_id($item_id)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `item_register_to_branches` WHERE `id` = '$item_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output = $row['quantity'];
        }    
    }    
    return $output;
}

function get_branch_item_id($item_id, $branch_idd)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_idd' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['id'];
        }
    }
    else
    {
        $output = 0;
    }    
    return $output;
}

function get_branch_address($branch_id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT address FROM `branchs` WHERE `id` = '$branch_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['address'];
        }
    }
    else
    {
        $output = 0;
    }    
    return $output;
}

function get_item_name_by_register_item_id($register_item_id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT name,category_id FROM `items` WHERE `id` iN (SELECT item_id FROM `item_register_to_branches` WHERE id = '$register_item_id') ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
        		$category_id = $row['category_id'];
        		$categories = "SELECT * FROM `categories` WHERE id = '$category_id'  ";
        		$select_category = mysqli_query($GLOBALS['con'], $categories);
        		if (mysqli_num_rows($select_category) == 1) 
        		{
        			while ($row_category = mysqli_fetch_array($select_category)) 
        			{
        				$category_name = $row_category['name'];
        			}
        		}
            $output .= $row['name'].' - '.$category_name;
        }
    }
    else
    {
        $output = 0;
    }    
    return $output;
}

function get_branch_item_quantity($item_id, $branch_idd)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `item_register_to_branches` WHERE `id` = '$item_id'");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['quantity'];
        }
    }
    else
    {
        $output = 0;
    }        
    return $output;
}

function item_available_quantity($item_id)
{
    $quanity = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT quantity FROM `items` WHERE `id` = '$item_id' AND `status` = '1' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $quanity = $row['quantity'];
        }
    }    
    return $quanity;
}
function get_selected_amount()
{
    $amount = 0;
    $type_id = 104;
    $select = 'general';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE branch_id = ".$GLOBALS['branch_id']." AND user_id = ".$GLOBALS['user_id']." AND status = '1' ");
    if (mysqli_num_rows($run1) > 0) 
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $fix_dose = $row1['fix_dose'];
            if($fix_dose == 0)
            {
            $quanity = $row1['days'] * $row1['dose'] * $row1['feed'];
            }
            else
            {
                $quanity = $fix_dose;
            }
            $item_id = $row1['item_id'];
    $run = mysqli_query($GLOBALS['con'], "SELECT `$select` FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id') ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $amount = $amount + ($row['0'] * $quanity);
        }
    }
        }
    }
    return $amount;
    
}

function get_procedure_pending_amount($tokan_no)
{
    $amount = 0;
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `tokans` WHERE id = '$tokan_no' AND status = '1' ");
    if (mysqli_num_rows($run1) == 1) 
    {
        while ($row = mysqli_fetch_array($run1)) 
        {
            $total_cash = $row['cash'];
            $received_cash = $row['cash_received'];
            $amount = $amount + ($total_cash - $received_cash);
            
        }
    }
    $run2 = mysqli_query($GLOBALS['con'], "SELECT * FROM `branch_pending_receive` WHERE token_no = '$tokan_no' AND status = '1' ");
    if (mysqli_num_rows($run2) > 0) 
    {
        while ($row = mysqli_fetch_array($run2)) 
        {
            $amount = $amount - $row['amount'];
        }
    }
    return $amount;
}


function get_amount($type_id)
{
    $amount = 0;
    if($type_id == 102){$select = 'poor';}
    elseif($type_id == 103){$select = 'member';}
    elseif($type_id == 101){$select = 'deserving';}
    elseif($type_id == 104){$select = 'general';}
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE branch_id = ".$GLOBALS['branch_id']." AND user_id = ".$GLOBALS['user_id']." AND status = '1' ");
    if (mysqli_num_rows($run1) > 0) 
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $fix_dose = $row1['fix_dose'];
            if($fix_dose == 0)
            {
            $quanity = $row1['days'] * $row1['dose'] * $row1['feed'];
            }
            else
            {
                $quanity = $fix_dose;
            }
            $item_id = $row1['item_id'];
    $run = mysqli_query($GLOBALS['con'], "SELECT `$select` FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id') ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $amount = $amount + ($row['0'] * $quanity);
        }
    }
        }
    }
    return $amount;
}

function last_token_by_user($user_id)
{
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM tokans WHERE user_id = '$user_id' ORDER BY id desc limit 0,1");
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) {
            $id = $row['0'];
        }
    }
    else{
        return 1;
    }
    return $id;
}

function next_tokan_no()
{
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM tokans ORDER BY id desc limit 0,1");
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) {
            $id = $row['0']+1;
        }
    }
    else{
        return 1;
    }
    return $id;
}

function last_tokan_no()
{
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM tokans ORDER BY id desc limit 0,1");
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $id = $row['0'];
        }
    }
    else
    {
        return 1;
    }
    return $id;
}

function get_token_user_id($token_id)
{
    $run = mysqli_query($GLOBALS['con'], "SELECT user_id FROM tokans WHERE  id = '$token_id' ");
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $id = $row['0'];
        }
    }
    else
    {
        return 1;
    }
    return $id;
}

function next_donation_id()
{
    $run = mysqli_query($GLOBALS['con'], "SELECT fr_collection_id FROM fr_collection ORDER BY fr_collection_id desc limit 0,1");
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) {
            $id = $row['0']+1;
        }
    }
    else{
        return 1;
    }
    return $id;
}

function next_audit_no()
{
    $run = mysqli_query($GLOBALS['con'], "SELECT audit_no FROM branch_audits ORDER BY audit_no desc limit 0,1");
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) {
            $id = $row['0']+1;
        }
    }
    else{
        return 1;
    }
    return $id;
}

function show_category_options($select_value)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `categories` WHERE status = '1' ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $id = $row['id'];
            $name = $row['name'];
            $output .= '<option ';
            if($select_value == $id)
            {
            $output .= 'SELECTED';
            }
            $output .= ' value="'.$id.'">'.$name.'</option>';
        }
    }
    else{
        return '<option value="">NOT DATA AVAILABLE</option>';
    }
    return $output;
}

function show_category_name($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `categories` WHERE id= '$id' ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $name = $row['name'];
            $output .= $name;
        }
    }
    return $output;
}

function show_item_name($id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `items` WHERE id= '$id' ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $name = $row['name'];
            $output .= $name;
        }
    }
    return $output;
}

function rate_by_reg_item_id($id, $quantity)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT general FROM `items` WHERE id = '$id' ");
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $rate = $row['general'] ;
            $output = $rate;
        }
    }
    return $output;
}

function show_branch_options($select_value)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `branchs` ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) 
        {
            $id = $row['id'];
            $name = $row['address'];
            $output .= '<option ';
            if($select_value == $id)
            {
            $output .= 'SELECTED';
            }
            $output .= ' value="'.$id.'">'.$name.'</option>';
        }
    }
    else{
        return '<option value="">NOT DATA AVAILABLE</option>';
    }
    return $output;
}


function branch_medicines()
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_register_to_branches` WHERE branch_id = ".$GLOBALS['branch_id']." ");
    if (mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) {
            $item_id = $row['item_id'];
            $run1 = mysqli_query($GLOBALS['con'], "SELECT name FROM items WHERE id = '$item_id' ");
            if (mysqli_num_rows($run1) == 1) {
                while ($row1 = mysqli_fetch_array($run1)) {
                    $item_name = $row1['0'];
                }
            }
            $output .= '<option value="'.$row['id'].'">'.$item_name.'</option>';
        }
    }
    else{
        return '<option value="">NOT DATA AVAILABLE</option>';
    }
    return $output;
}


function medicine_selected_by_doctor($token_id)
{
    $output = '';
    $branch_id = $GLOBALS['branch_id'];
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `select_by_doctor` WHERE tokan_no = ".$token_id." AND status = '1' AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (SELECT id FROM `items` WHERE `category_id` != 2)) ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_id = $row['item_id'];
            $fix_dose = $row['fix_dose'];
            if ($fix_dose == 0) 
            {
                $quantity = $row['dose'] * $row['days'] * $row['feed'];
            }
            else
            {
                $quantity = $fix_dose;
            }


            $select1 = "SELECT name FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id')  ";
            $run1 = mysqli_query($GLOBALS['con'], $select1);
            if (mysqli_num_rows($run1) == 1) 
            {
                while ($row1 = mysqli_fetch_array($run1)) 
                {
                    $item_name = $row1['0'];
                }
            }
            $output .= '<option value="'.$row['id'].'">'.$item_name.' - '.$quantity.'</option>';
        }
    }
    else{
        return '<option value="">ADD DATA IN BRANCH</option>';
    }
    return $output;
}

function test_selected_by_doctor($token_id)
{
    $output = '';
    $branch_id = $GLOBALS['branch_id'];
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `select_by_doctor` WHERE branch_id = ".$branch_id." AND tokan_no = ".$token_id." AND status = '1' AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (SELECT id FROM `items` WHERE `category_id` = 2)) ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_id = $row['item_id'];
            $fix_dose = $row['fix_dose'];
            if ($fix_dose == 0) 
            {
                $quantity = $row['dose'] * $row['days'] * $row['feed'];
            }
            else
            {
                $quantity = $fix_dose;
            }


            $select1 = "SELECT name FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id')  ";
            $run1 = mysqli_query($GLOBALS['con'], $select1);
            if (mysqli_num_rows($run1) == 1) 
            {
                while ($row1 = mysqli_fetch_array($run1)) 
                {
                    $item_name = $row1['0'];
                }
            }
            $output .= '<option value="'.$row['id'].'">'.$item_name.' - '.$quantity.'</option>';
        }
    }
    else{
        return '<option value="">ADD DATA IN BRANCH</option>';
    }
    return $output;
}

function medicine_selected()
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE branch_id = ".$GLOBALS['branch_id']." AND user_id = ".$GLOBALS['user_id']." AND status = '1' ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_id = $row['item_id'];
            $fix_dose = $row['fix_dose'];
            if ($fix_dose == 0) 
            {
                $quantity = $row['dose'] * $row['days'] * $row['feed'];
            }
            else
            {
                $quantity = $fix_dose;
            }


            $select1 = "SELECT name FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id')  ";
            $run1 = mysqli_query($GLOBALS['con'], $select1);
            if (mysqli_num_rows($run1) == 1) 
            {
                while ($row1 = mysqli_fetch_array($run1)) 
                {
                    $item_name = $row1['0'];
                }
            }
            $output .= '<option value="'.$row['id'].'">'.$item_name.' - '.$quantity.'</option>';
        }
    }
    else{
        return '<option value="">ADD DATA IN BRANCH</option>';
    }
    return $output;
}

function medicine_selected_by_token($token_no)
{
    $output = '';
    $branch_id = $GLOBALS['branch_id'];
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE tokan_no = '$token_no' AND branch_id = '$branch_id' ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_id = $row['item_id'];
            $fix_dose = $row['fix_dose'];
            if ($fix_dose == 0) 
            {
                $quantity = $row['dose'] * $row['days'] * $row['feed'];
            }
            else
            {
                $quantity = $fix_dose;
            }


            $select1 = "SELECT name FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id')  ";
            $run1 = mysqli_query($GLOBALS['con'], $select1);
            if (mysqli_num_rows($run1) == 1) 
            {
                while ($row1 = mysqli_fetch_array($run1)) 
                {
                    $item_name = $row1['0'];
                }
            }
            $rate = rate_by_reg_item_id($item_id, $quantity);
            $output .= '<option value="'.$row['id'].'">'.$item_name.' - '.$quantity.' - '.$rate.'</option>';
        }
    }
    else{
        return '<option value=""></option>';
    }
    return $output;
}


function return_medicine_selected_by_token($token_no)
{
    $output = '';
    $branch_id = $GLOBALS['branch_id'];
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE tokan_no = '$token_no' AND branch_id = '$branch_id' ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_id = $row['item_id'];
            $fix_dose = $row['fix_dose'];
            if ($fix_dose == 0) 
            {
                $quantity = $row['dose'] * $row['days'] * $row['feed'];
            }
            else
            {
                $quantity = $fix_dose;
            }


            $select1 = "SELECT name, general FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id')  ";
            $run1 = mysqli_query($GLOBALS['con'], $select1);
            if (mysqli_num_rows($run1) == 1) 
            {
                while ($row1 = mysqli_fetch_array($run1)) 
                {
                    $item_name = $row1['0'];
                    $item_general_price = $row1['1'];
                }
            }
            // $rate = rate_by_reg_item_id($item_id, $quantity);
            $output .= '<option value="'.$row['id'].'">'.$item_name.' - '.$quantity.' - '.($item_general_price*$quantity).'</option>';
        }
    }
    else{
        return '<option value=""></option>';
    }
    return $output;
}


function medicine_selected_by_token_update($token_no)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE tokan_no = '$token_no' ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $id = $row['id'];
            $item_id = $row['item_id'];
            $fix_dose = $row['fix_dose'];
            if ($fix_dose == 0) 
            {
                $quantity = $row['dose'] * $row['days'] * $row['feed'];
            }
            else
            {
                $quantity = $fix_dose;
            }


            $select1 = "SELECT name FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item_id')  ";
            $run1 = mysqli_query($GLOBALS['con'], $select1);
            if (mysqli_num_rows($run1) == 1) 
            {
                while ($row1 = mysqli_fetch_array($run1)) 
                {
                    $item_name = $row1['0'];
                }
            }
            $update_1 = "UPDATE `item_by_doctor` SET `status`= '3' WHERE id = '$id';<br>";
        		$get_available_quantity = get_register_item_quantity_from_item_id($item_id);
        		$new_quantity = $get_available_quantity + $quantity;
            $update_2 = "UPDATE `item_register_to_branches` SET `quantity`= '$new_quantity' WHERE `id` = '$item_id';<br>";
        }
    }
    else{
        return '';
    }
    return $output;
}


function branch_medicines_by_name()
{
    $branch_id = $GLOBALS['branch_id'];
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT id,name,category_id FROM `items` WHERE category_id NOT IN (3,28) AND status = '1' ORDER BY `name` ");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $item_id = $row1['id'];
            $item_name = $row1['name'];
        		$category_id = $row1['category_id'];
        		$categories = "SELECT * FROM `categories` WHERE id = '$category_id'  ";
        		$select_category = mysqli_query($GLOBALS['con'], $categories);
        		if (mysqli_num_rows($select_category) == 1) 
        		{
        			while ($row_category = mysqli_fetch_array($select_category)) 
        			{
        				$category_name = $row_category['name'];
        			}
        		}
        	if($category_id == 2 || $category_id == 3 || $category_id == 8 || $category_id == 28)
        	{
                $select2 = "SELECT id, quantity FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_id' AND status = '1' ";
                $run2 = mysqli_query($GLOBALS['con'], $select2);
                if (mysqli_num_rows($run2) > 0)  
                {
                    while ($row2 = mysqli_fetch_array($run2)) 
                    {
                        $reg_item_id = $row2['id'];
                     $output .= '<option value="'.$reg_item_id.'">'.$item_name.' - '.$category_name.'</option>';   
                    }
                }
        	}
        	else
        	{
                $select2 = "SELECT id, quantity FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_id' AND status = '1' ";
                $run2 = mysqli_query($GLOBALS['con'], $select2);
                if (mysqli_num_rows($run2) > 0)  
                {
                    while ($row2 = mysqli_fetch_array($run2)) 
                    {
                        $reg_item_id = $row2['id'];
                     $output .= '<option value="'.$reg_item_id.'">'.$item_name.' - '.$category_name.'</option>';   
                    }
                }
        	}
            
        }
    }
    else
    {
        return '<option>NO DATA FOUND</option>';
    }
    return $output;
}
function procedure_medicines_by_name()
{
    $branch_id = $GLOBALS['branch_id'];
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT id,name,category_id FROM `items` WHERE category_id NOT IN (2, 3, 18, 28) AND status = '1' ORDER BY `name` ");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $item_id = $row1['id'];
            $item_name = $row1['name'];
        		$category_id = $row1['category_id'];
        		$categories = "SELECT * FROM `categories` WHERE id = '$category_id'  ";
        		$select_category = mysqli_query($GLOBALS['con'], $categories);
        		if (mysqli_num_rows($select_category) == 1) 
        		{
        			while ($row_category = mysqli_fetch_array($select_category)) 
        			{
        				$category_name = $row_category['name'];
        			}
        		}
        	if($category_id == 2 || $category_id == 3 || $category_id == 8 || $category_id == 28)
        	{
                $select2 = "SELECT id, quantity FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_id' AND status = '1' ";
                $run2 = mysqli_query($GLOBALS['con'], $select2);
                if (mysqli_num_rows($run2) > 0)  
                {
                    while ($row2 = mysqli_fetch_array($run2)) 
                    {
                        $reg_item_id = $row2['id'];
                     $output .= '<option value="'.$reg_item_id.'">'.$item_name.' - '.$category_name.'</option>';   
                    }
                }
        	}
        	else
        	{
                $select2 = "SELECT id, quantity FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_id' AND status = '1' ";
                $run2 = mysqli_query($GLOBALS['con'], $select2);
                if (mysqli_num_rows($run2) > 0)  
                {
                    while ($row2 = mysqli_fetch_array($run2)) 
                    {
                        $reg_item_id = $row2['id'];
                     $output .= '<option value="'.$reg_item_id.'">'.$item_name.' - '.$category_name.'</option>';   
                    }
                }
        	}
            
        }
    }
    else
    {
        return '<option>NO DATA FOUND</option>';
    }
    return $output;
}

function branch_procedures_by_name()
{
    $branch_id = $GLOBALS['branch_id'];
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT id,name FROM `items` WHERE category_id IN (3, 37, 38) ORDER BY `name` ");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $item_id = $row1['id'];
            $item_name = $row1['name'];

            $select2 = "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_id' and status = '1' ";
            $run2 = mysqli_query($GLOBALS['con'], $select2);
            if (mysqli_num_rows($run2) > 0)  
            {
                while ($row2 = mysqli_fetch_array($run2)) 
                {
                    $reg_item_id = $row2['id'];
                 $output .= '<option value="'.$reg_item_id.'">'.$item_name.'</option>';   
                }
            }
            
        }
    }
    else
    {
        return '<option>NO DATA FOUND</option>';
    }
    return $output;
}


function next_patient_id()
{
    $output = 1;
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM patients ORDER BY id desc limit 0,1");
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) {
            $output = $row['0']+1;
        }
    }
    return $output;
}

function print_summary($from_date, $to_date, $user_id, $user_name)
{
    $output = '';

    

    return $output;
}

function get_patient_name_by_id($patient_id)
{
    $output = '';
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT name FROM patients WHERE id = '$patient_id' ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['name'];
        }
    }
    return $output;
}

function get_patient_phone_by_id($token_no)
{
    $output_array = array();
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT name, phone, cnic, age FROM patients WHERE id IN (SELECT patient_id FROM tokans WHERE tokans.id = '$token_no') ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output_array['name'] = $row_patient['name'];
            $output_array['phone'] = $row_patient['phone'];
            $output_array['age'] = $row_patient['age'];
            $output_array['cnic'] = $row_patient['cnic'];
        }
    }
    return $output_array;
}

function weeks_between($datefrom, $dateto)
{
    $datefrom = DateTime::createFromFormat('d/m/Y H:i:s',$datefrom);
    $dateto = DateTime::createFromFormat('d/m/Y H:i:s',$dateto);
    $interval = $dateto->diff($datefrom);
    $week_total = $interval->format('%a')/7;
    return floor($week_total)-33;

}

function get_docotr_id_by_token_no($token_no)
{
    $output = '';
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT doctor_id FROM tokans WHERE id = '$token_no' ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['doctor_id'];
        }
    }
    return $output;
}

function get_patient_name_by_token_no($token_no)
{
    $output = '';
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id IN (SELECT patient_id FROM tokans WHERE id = '$token_no') ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['name'];
        }
    }
    return $output;
}

function get_patient_id_by_token_no($token_no)
{
    $output = '';
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT id FROM patients WHERE id IN (SELECT patient_id FROM tokans WHERE id = '$token_no') ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['id'];
        }
    }
    return $output;
}

function get_patient_age_by_token_no($token_no)
{
    $output = '';
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT age FROM patients WHERE id IN (SELECT patient_id FROM tokans WHERE id = '$token_no') ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['age'];
        }
    }
    return $output;
}

function token_type_title($tokan_type_id)
{
    $output = '';
$get_tokan_type_title = mysqli_query($GLOBALS['con'], "SELECT * FROM tokan_types WHERE id = '$tokan_type_id' ");
if (mysqli_num_rows($get_tokan_type_title) == 1) 
{
    while ($row_tokan_type_title = mysqli_fetch_array($get_tokan_type_title)) 
    {
        $output .= $row_tokan_type_title['title'];
    }
}
return $output;
}

function print_branch_audit($branch_audit_no)
{
$s = 0;
$output = '';
$output .= '<table>';
$select_detail = mysqli_query($GLOBALS['con'], "SELECT * FROM branch_audit_details WHERE audit_no = '$branch_audit_no' ");
if (mysqli_num_rows($select_detail) == 1) 
{
    while ($row_detail = mysqli_fetch_array($select_detail)) 
    {
        $branch_addresss = get_branch_address($row_detail['branch_id']);
        $created = $row_detail['created'];

$output .= '
    <caption style="caption-side: top;text-align: center;">
        <h1>BRANCH AUDIT REPORT</h1>
        <h2>'.$branch_addresss.'</h2>
        <h3>Date And Time : <span>'.date_format(date_create($created), "m:h:i A d-M-Y").'</span></h3>
    </caption>';
    }
}
$select_audit = mysqli_query($GLOBALS['con'], "SELECT * FROM branch_audits WHERE audit_no = '$branch_audit_no' ");
if (mysqli_num_rows($select_audit) > 0) 
{
$output .= '
<thead>
    <tr>
        <th width="10%">S No</th>
        <th width="30%">Item Name</th>
        <th width="20%">Pharmecy Quantity</th>
        <th width="20%">Short</th>
        <th width="20%">Extra</th>
    </tr>
</thead>
</body>';

    while ($row_audit = mysqli_fetch_array($select_audit)) 
    {
        $item_name = get_item_name_by_register_item_id($row_audit['register_item_id']);
        $created = $row_audit['created'];
        $counter_quantity = $row_audit['counter_quantity'];
        $available_quantity = $row_audit['available_quantity'];
        if ($available_quantity >= $counter_quantity) 
        {
            $action_msg = '<span>Short</span>';
$output .= '
    <tr>
        <td>'.++$s.'</td>
        <td>'.$item_name.'</td>
        <td>'.$counter_quantity.'</td>
        <td>'.$action_msg.'</td>
        <td></td>
    </tr>';
        }
        elseif ($available_quantity < $counter_quantity) 
        {
            $action_msg = '<span>Extra</span>';
$output .= '
    <tr>
        <td>'.++$s.'</td>
        <td>'.$item_name.'</td>
        <td>'.$counter_quantity.'</td>
        <td></td>
        <td>'.$action_msg.'</td>
    </tr>';
        }
        else 
        {
            $action_msg = '<span>Same</span>';
$output .= '
    <tr>
        <td>'.++$s.'</td>
        <td>'.$item_name.'</td>
        <td>'.$counter_quantity.'</td>
        <td></td>
        <td></td>
    </tr>';
        }

    }
}
$output .= '</tbody>';
$output .= '</table>';
return $output;
}

function print_tokan($id)
{
    $output = '';
    $output .= '';
    $doctor_name = 'no name';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM tokans WHERE id = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {

            $cash_received = $row['cash_received'];
            $created = $row['created'];
            $tokan_type_id = $row['tokan_type_id'];
            $get_tokan_type = mysqli_query($GLOBALS['con'], "SELECT * FROM tokan_types WHERE id = '$tokan_type_id' ");
            if (mysqli_num_rows($get_tokan_type) == 1) 
            {
                while ($row_tokan_type = mysqli_fetch_array($get_tokan_type)) 
                {
                    $tokan_type_title = $row_tokan_type['title'];
                }
            }

            $doctor_id = $row['doctor_id'];
            $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE id = '$doctor_id' ");
            if (mysqli_num_rows($get_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($get_patient)) 
                {
                    $doctor_name = $row_patient['u_name'];
                }
            }


            $user_id = $row['user_id'];
            $get_user = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE id = '$user_id' ");
            if (mysqli_num_rows($get_user) == 1) 
            {
                while ($row_user = mysqli_fetch_array($get_user)) 
                {
                    $username = $row_user['u_name'];
                }
            }

            $patient_id = $row['patient_id'];
            $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id = '$patient_id' ");
            if (mysqli_num_rows($get_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($get_patient)) 
                {
                    $name = $row_patient['name'];
                    $age = $row_patient['age'];
                    $gender = $row_patient['gender'];
                    if($gender == 1){$gender_title = 'Female';}
                    elseif($gender == 2){$gender_title = 'Male';}
                    else{$gender_title = 'Other';}
                }
            }
        }
    }
if($GLOBALS['branch_id'] == 15)
{        
$output .=  '<table style="font-size: 12px;">
            <caption style="text-align: center;caption-side: top;margin: auto auto;min-width: 80mm;font-weight: 900;"><strong>
            <table>
            <tr>
                <td>
                    <img src="images/city_police_multan_logo.png" alt="POLICE" width="55" height="70" align="right" />
                </td>
                <td>
                    <h3 style="text-align: center;margin: auto auto;min-width: 60mm;max-width: 60mm;">'.$GLOBALS['branch_name'].'</h3>
                    <p style="font-size: 14px;text-align: center;" >'.$GLOBALS['branch_address'].'</p>
                    <h3 align="center">UAN : 0304-1110222</h3>
                </td>
                <td>
                    <img src="images/label.jpg" alt="YCDO" width="55" height="70" align="left" />
                </td>
            </tr>
        </table>';
}
else
{
$output .=  '<table style="font-size: 12px;">
            <caption style="text-align: center;caption-side: top;margin: auto auto;min-width: 80mm;font-weight: 900;"><strong>
            <table>
            <tr>
                <td>
                    <h3 style="text-align: center;margin: auto auto;min-width: 60mm;max-width: 60mm;">'.$GLOBALS['branch_name'].'</h3>
                    <p style="font-size: 14px;text-align: center;" >'.$GLOBALS['branch_address'].'</p>
                    <h3 align="center">UAN : 0304-1110222</h3>
                    </td>
                <td>
                    <img src="images/label.jpg" alt="YCDO" width="55" height="70" align="left" />
                </td>
            </tr>
        </table>';
}

    $output .= '<h3 align="center"> '.$id.' / '.date('y').'</h3>
</strong></caption>
    <tr>
        <td>Date & Time</td>
        <td>'.date_format(date_create($created),'d-M-Y h:i:s A').'</td>
    </tr>
    <tr>
        <td>Name:</td>
        <td>'.$name.'</td>
    </tr>
    <tr>
        <td>Age:</td>
        <td>'.$age.'</td>
    </tr>
    <tr>
        <td>Gender:</td>
        <td>'.$gender_title.'</td>
    </tr>
    <tr>
        <td>Ref. To Dr.:</td>
        <td>'.$doctor_name.'</td>
    </tr>
    <tr>
        <td>Tokan_Type:</td>
        <td>'.$tokan_type_title.'</td>
    </tr>
    <tr>
        <td>Cash Received:</td>
        <td>'.$cash_received.'</td>
    </tr>
    <tr>
        <td>Tokan_By:</td>
        <td>'.$username.'</td>
    </tr>
    <tr>
        <td>Print Time</td>
        <td>'.date('d-M-Y h:i:s A').'</td>
    </tr>
</table>
    ';
    return $output;
}


function print_tokan_duplicate($id)
{
    $output = '';
    $output .= '';
    $doctor_name = 'no name';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM tokans WHERE id = '$id' AND status IN (1, 2) ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {

            $cash_received = $row['cash_received'];
            $created = $row['created'];
            $tokan_type_id = $row['tokan_type_id'];
            $get_tokan_type = mysqli_query($GLOBALS['con'], "SELECT * FROM tokan_types WHERE id = '$tokan_type_id' ");
            if (mysqli_num_rows($get_tokan_type) == 1) 
            {
                while ($row_tokan_type = mysqli_fetch_array($get_tokan_type)) 
                {
                    $tokan_type_title = $row_tokan_type['title'];
                }
            }

            $doctor_id = $row['doctor_id'];
            $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE id = '$doctor_id' ");
            if (mysqli_num_rows($get_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($get_patient)) 
                {
                    $doctor_name = $row_patient['u_name'];
                }
            }


            $user_id = $row['user_id'];
            $get_user = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE id = '$user_id' ");
            if (mysqli_num_rows($get_user) == 1) 
            {
                while ($row_user = mysqli_fetch_array($get_user)) 
                {
                    $username = $row_user['u_name'];
                }
            }

            $patient_id = $row['patient_id'];
            $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id = '$patient_id' ");
            if (mysqli_num_rows($get_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($get_patient)) 
                {
                    $name = $row_patient['name'];
                    $age = $row_patient['age'];
                    $gender = $row_patient['gender'];
                    if($gender == 1){$gender_title = 'Female';}
                    elseif($gender == 2){$gender_title = 'Male';}
                    else{$gender_title = 'Other';}
                }
            }
        }
    }
    else
    {
        return '';
    }
    $branch_id = $GLOBALS['branch_id'];
    $output .= '
<style>
 :root:after {   
            content: "DUPLICATE NOT_FOR_USE"; 
            position: fixed; 
            transform: rotate(300deg); 
            -webkit-transform: rotate(300deg); 
            color: rgba(0, 0, 0, 0.9); 
            top: 150px;                     
            z-index: -1;
            font-size: 25px; 
        } 
</style>
<table style="font-size: 12px;">
<caption style="text-align: center;caption-side: top;margin: auto auto;min-width: 80mm;font-weight: 900;">
<strong>';
if($GLOBALS['branch_id'] == 15)
{        
$output .=  '<table>
            <tr>
                <td>
                    <img src="images/city_police_multan_logo.png" alt="POLICE" width="55" height="70" align="right" />
                </td>
                <td>
                    <h3 style="text-align: center;margin: auto auto;min-width: 60mm;max-width: 60mm;">'.$GLOBALS['branch_name'].'</h3>
                    <p style="font-size: 14px;text-align: center;" >'.$GLOBALS['branch_address'].'</p>
                    <p style = "font-size: 18px;line-height:20px;text-align: center;">'.$tokan_type_title.' Token</br>
                    <span style = "font-size: 20px;line-height:20px;text-align: center;">'.$id.' / '.date('y').'</span></p>
                </td>
                <td>
                    <img src="images/label.jpg" alt="YCDO" width="55" height="70" align="left" />
                    <p><span style = "font-size: 6.5px;line-height:0px;text-align: center;">SERVE HUMANITY</span></p>
                    <h4 style = "line-height:10px;" align="center">UAN </br> 0304-1110222</h4>
                </td>
            </tr>
        </table>';
}
else
{
$output .=  '<table>
            <tr>
                <td>
                    <h3 style="text-align: center;margin: auto auto;min-width: 60mm;max-width: 60mm;">'.$GLOBALS['branch_name'].'</h3>
                    <p style="font-size: 14px;text-align: center;" >'.$GLOBALS['branch_address'].'</p>
                    <p style = "font-size: 18px;line-height:20px;text-align: center;">'.$tokan_type_title.' Token</br>
                    <span style = "font-size: 20px;line-height:20px;text-align: center;">'.$id.' / '.date('y').'</span></p>
                </td>
                <td>
                    <img src="images/label.jpg" alt="YCDO" width="55" height="70" align="left" />
                    <p><span style = "font-size: 6.5px;line-height:0px;text-align: center;">SERVE HUMANITY</span></p>
                    <p style = "font-size: 10px;line-height:10px;" align="center">UAN </br> 0304-1110222</p>
                </td>
            </tr>
        </table>';
}
    $output .= '
</strong>
</caption>
    <tr>
        <td>Date & Time</td>
        <td>'.date_format(date_create($created),'d-M-Y h:i:s A').'</td>
    </tr>
    <tr>
        <td>Name:</td>
        <td>'.$name.'</td>
    </tr>
    <tr>
        <td>Age:</td>
        <td>'.$age.'</td>
    </tr>
    <tr>
        <td>Gender:</td>
        <td>'.$gender_title.'</td>
    </tr>
    <tr>
        <td>Ref. To Dr.:</td>
        <td>'.$doctor_name.'</td>
    </tr>
    <tr>
        <td>Tokan_Type:</td>
        <td>'.$tokan_type_title.'</td>
    </tr>
    <tr>
        <td>Cash Received:</td>
        <td>'.$cash_received.'</td>
    </tr>
    <tr>
        <td>Tokan_By:</td>
        <td>'.$username.'</td>
    </tr>
    <tr>
        <td class="quantity">Duplicate By:</td>
        <td class="description">'.$GLOBALS['user_name'].'</td>
    </tr>
    <tr>
        <td>Print Time</td>
        <td>'.date('d-M-Y h:i:s A').'</td>
    </tr>
</table>
    ';
    return $output;
}


function print_medicine_slip($id)
{
    $output = '';
    $output .= '';
    $doctor_name = 'no name';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM tokans WHERE id = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {

            $cash_received = $row['cash_received'];
            $cash = $row['cash'];
            $created = $row['created'];
            $tokan_type_id = $row['tokan_type_id'];
            $get_tokan_type = mysqli_query($GLOBALS['con'], "SELECT * FROM tokan_types WHERE id = '$tokan_type_id' ");
            if (mysqli_num_rows($get_tokan_type) == 1) 
            {
                while ($row_tokan_type = mysqli_fetch_array($get_tokan_type)) 
                {
                    $tokan_type_title = $row_tokan_type['title'];
                }
            }

            $doctor_id = $row['doctor_id'];
            $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE id = '$doctor_id' ");
            if (mysqli_num_rows($get_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($get_patient)) 
                {
                    $doctor_name = $row_patient['u_name'];
                }
            }


            $user_id = $row['user_id'];
            $get_user = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE id = '$user_id' ");
            if (mysqli_num_rows($get_user) == 1) 
            {
                while ($row_user = mysqli_fetch_array($get_user)) 
                {
                    $username = $row_user['u_name'];
                }
            }

            $patient_id = $row['patient_id'];
            $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id = '$patient_id' ");
            if (mysqli_num_rows($get_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($get_patient)) 
                {
                    $name = $row_patient['name'];
                    $age = $row_patient['age'];
                    $gender = $row_patient['gender'];
                    if($gender == 1){$gender_title = 'Female';}
                    elseif($gender == 2){$gender_title = 'Male';}
                    else{$gender_title = 'Other';}
                }
            }
        }
    }

    $output .= '
<table style="font-size: 12px;">
<caption style="text-align: center;caption-side: top;margin: auto auto;min-width: 80mm;font-weight: 900;"><strong>
        <table>
            <tr>
                <td>
                    <h3 style="text-align: center;margin: auto auto;min-width: 60mm;max-width: 60mm;">'.$GLOBALS['branch_name'].'</h3>
                    <p style="font-size: 14px;text-align: center;" >'.$GLOBALS['branch_address'].'</p>
                    <p style = "font-size: 18px;line-height:20px;text-align: center;">'.$tokan_type_title.' Token</br>
                    <span style = "font-size: 20px;line-height:20px;text-align: center;">'.$id.' / '.date('y').'</span></p>
                </td>
                <td>
                    <img src="images/label.jpg" alt="YCDO" width="55" height="70" align="left" />
                    <p><span style = "font-size: 6.5px;line-height:0px;text-align: center;">SERVE HUMANITY</span></p>
                    <h4 style = "font-size: 10px;" align="center">UAN </br> 0304-1110222</h4>
                </td>
            </tr>
        </table>
</caption>
    <tr>
        <td>Date & Time</td>
        <td>'.date_format(date_create($created),'d-M-Y h:i:s A').'</td>
    </tr>
    <tr>
        <td>Name:</td>
        <td>'.$name.'</td>
    </tr>
    <tr>
        <td>Age /Gender</td>
        <td>'.$age.' Y / '.$gender_title.'</td>
    </tr>
    <tr>
        <td>Dr Name.:</td>
        <td>'.$doctor_name.'</td>
    </tr>
    <tr>
        <td>Total Bill:</td>
        <td>'.$cash.'</td>
    </tr>
    <tr>
        <td>Received Rs.:</td>
        <td>'.$cash_received.'</td>
    </tr>
    <tr>
        <td>Token_By:</td>
        <td>'.$username.'</td>
    </tr>
    <tr>
        <td>Print Time</td>
        <td>'.date('d-M-Y h:i:s A').'</td>
    </tr>';
    $output .= '</table>';
        $output .= '<table style = "margin: auto auto;min-width: 80mm;font-size: 11px;">';
        $output .= '<caption style = "caption-side: top; text-align: center; color: black;font-size: 12px;"><h3>Tests / Medicines as Follow:</h3></caption>';
        $output .= '<tr>';
        $output .= '<th style = "text-align: left;">ITEM</th>';
        $output .= '<th style = "text-align: right;">RATE</th>';
        $output .= '<th style = "text-align: right;">QTY</th>';
        $output .= '<th style = "text-align: right;">PRICE</th>';
        $output .= '</tr>';
$get_selected_medicines = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE `tokan_no` = '$id' ORDER BY `feed` DESC ");
if (mysqli_num_rows($get_selected_medicines) > 0) {
    while ($row_selected_medicines = mysqli_fetch_array($get_selected_medicines)) {
        $item = $row_selected_medicines['item_id'];
        if($tokan_type_id == 101)
        {
            $item_price = $row_selected_medicines['sale_price_general'];
        }
        elseif($tokan_type_id == 102)
        {
            $item_price = $row_selected_medicines['sale_price_poor'];
        }
        elseif($tokan_type_id == 103)
        {
            $item_price = $row_selected_medicines['sale_price_member'];
        }
        elseif($tokan_type_id == 104)
        {
            $item_price = $row_selected_medicines['sale_price_general'];
        }
        else
        {
            $item_price = $row_selected_medicines['sale_price_general'];
        }
        $sale_price = $row_selected_medicines['sale_price'];
        $item_id = get_item_name_by_register_item_id($row_selected_medicines['item_id']);
        $dose = $row_selected_medicines['dose'];
        $fix_dose = $row_selected_medicines['fix_dose'];
        if($fix_dose == 0)
        {
        $quantity = $dose * $row_selected_medicines['days'] * $row_selected_medicines['feed'];
        }
        else
        {
        $quantity = $fix_dose;
        }
        $feed = $row_selected_medicines['feed'];

        $select_feed = "SELECT * FROM `feeds` WHERE `category_id` = (SELECT category_id FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item'))  ORDER BY `id` ";
        $get_feed_str = mysqli_query($GLOBALS['con'], $select_feed);
        if (mysqli_num_rows($get_feed_str) > 0) 
        {
            while ($row_feed_str = mysqli_fetch_array($get_feed_str)) 
            {
                $feed_cat_id = $row_feed_str['category_id'];
                if($feed_cat_id  == 1)
                {
                $feed_title = 'گولی/گولیاں';
                }
                elseif($feed_cat_id  == 4)
                {
                $feed_title = 'ٹیکا/ٹیکے';
                }
                elseif($feed_cat_id  == 5)
                {
                $feed_title = 'چمچ';
                }
                elseif($feed_cat_id  == 6)
                {
                $feed_title = 'قطرہ/قطرے';
                }
                elseif($feed_cat_id  >= 7 && $feed_cat_id  <= 11)
                {
                $feed_title = 'بار لگایئں';
                }
                else
                {
                $feed_title = '';
                }
            }
        }
        else
        {
            $feed_title = 1;
        }        
        if ($dose == 1) {$dose_title = 'صبح ';}
        elseif ($dose == 2){$dose_title = 'صبح شام';}
        elseif ($dose == 3){$dose_title = 'صبح   دوپہر شام';}


        if ($feed == 1) {   $feed_urdu = 'ایک';}
        elseif ($feed == 2) {   $feed_urdu = 'دو';}
        elseif ($feed == 3) {   $feed_urdu = 'تین';}
        elseif ($feed == 4) {   $feed_urdu = 'چار';}
        elseif ($feed == 5) {   $feed_urdu = 'پانچ';}
        elseif ($feed == 6) {   $feed_urdu = 'چھ';}
        elseif ($feed == 7) {   $feed_urdu = 'سات';}
        else {   $feed_urdu = 'آدھی';}
    $output .= '<tr>';
        $output .= '<td class="">'.$item_id.' ';
        if ($feed_title != 1) 
        {
                $output .= '</br><span> '.$feed_urdu .' '.$feed_title. ' ' .$dose_title.'</span>';
        }
        $output .= '</td>';
        $output .= '<td style = "text-align: right;" class="">'.$item_price.'</td>';
        $output .= '<td style = "text-align: right;" class="">'.$quantity.'</td>';
        $output .= '<td style = "text-align: right;" class="">'.$sale_price.'</td>';
    $output .= '</tr>';
    }
}
echo '</table></div>';
    return $output;
}

function print_medicine_slip_duplicate($id)
{
    if($id < 0)
    {
        return 0;
    }
    $output = '';
    $output .= '';
    $doctor_name = 'no name';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM tokans WHERE id = '$id' AND status IN (1, 2) ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $cash_received = $row['cash_received'];
            $cash = $row['cash'];
            $created = $row['created'];
            $tokan_type_id = $row['tokan_type_id'];
            $get_tokan_type = mysqli_query($GLOBALS['con'], "SELECT * FROM tokan_types WHERE id = '$tokan_type_id' ");
            if (mysqli_num_rows($get_tokan_type) == 1) 
            {
                while ($row_tokan_type = mysqli_fetch_array($get_tokan_type)) 
                {
                    $tokan_type_title = $row_tokan_type['title'];
                }
            }

            $doctor_id = $row['doctor_id'];
            $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE id = '$doctor_id' ");
            if (mysqli_num_rows($get_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($get_patient)) 
                {
                    $doctor_name = $row_patient['u_name'];
                }
            }


            $user_id = $row['user_id'];
            $get_user = mysqli_query($GLOBALS['con'], "SELECT * FROM users WHERE id = '$user_id' ");
            if (mysqli_num_rows($get_user) == 1) 
            {
                while ($row_user = mysqli_fetch_array($get_user)) 
                {
                    $username = $row_user['u_name'];
                }
            }

            $patient_id = $row['patient_id'];
            $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id = '$patient_id' ");
            if (mysqli_num_rows($get_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($get_patient)) 
                {
                    $name = $row_patient['name'];
                    $age = $row_patient['age'];
                    $gender = $row_patient['gender'];
                    if($gender == 1){$gender_title = 'Female';}
                    elseif($gender == 2){$gender_title = 'Male';}
                    else{$gender_title = 'Other';}
                }
            }
        }
    }
    else
    {
        return '';
    }
    $output .= '
<style>
 :root:after {    content: "DUPLICATE NOT_FOR_USE"; position: fixed; transform: rotate(250deg); -webkit-transform: rotate(250deg);    color: rgba(0, 0, 0, 0.9);     top: 230px;     z-index: -1;     font-size: 27px;     }

* {    font-size: 12px;    font-family: "Times New Roman";font-weight: 900;}
td,th,tr,table {    border-top: 1px solid black;    border-collapse: collapse;}
td.description, th.description {    width: 220px;    max-width: 220px;font-weight: 900;}
td.quantity, th.quantity {    width: 100px;    max-width: 100px;    word-break: break-all;font-weight: 900;}
.centered {    text-align: center;    align-content: center;}
.ticket {    width: 320px;    max-width: 320px;}
img {    max-width: inherit;    width: inherit;}

</style>

<table>
<caption style="text-align: center;caption-side: top;margin: auto auto;min-width: 80mm;font-weight: 900;"><strong>';
if($GLOBALS['branch_id'] == 15)
{        
$output .=  '<table>
            <tr>
                <td>
                    <img src="images/city_police_multan_logo.png" alt="POLICE" width="55" height="70" align="right" />
                </td>
                <td>
                    <h3 style="text-align: center;margin: auto auto;min-width: 60mm;max-width: 60mm;">'.$GLOBALS['branch_name'].'</h3>
                    <p style="font-size: 14px;text-align: center;" >'.$GLOBALS['branch_address'].'</p>
                    <p style = "font-size: 18px;line-height:20px;text-align: center;">'.$tokan_type_title.' Token</br>
                    <span style = "font-size: 20px;line-height:20px;text-align: center;">'.$id.' / '.date('y').'</span></p>
                </td>
                <td>
                    <img src="images/label.jpg" alt="YCDO" width="55" height="70" align="left" />
                    <p><span style = "font-size: 6.5px;line-height:0px;text-align: center;">SERVE HUMANITY</span></p>
                    <h4 style = "line-height:10px;" align="center">UAN </br> 0304-1110222</h4>
                </td>
            </tr>
        </table>';
}
else
{
$output .=  '<table>
            <tr>
                <td>
                    <h3 style="text-align: center;margin: auto auto;min-width: 60mm;max-width: 60mm;">'.$GLOBALS['branch_name'].'</h3>
                    <p style="font-size: 14px;text-align: center;" >'.$GLOBALS['branch_address'].'</p>
                    <p style = "font-size: 18px;line-height:20px;text-align: center;">'.$tokan_type_title.' Token</br>
                    <span style = "font-size: 20px;line-height:20px;text-align: center;">'.$id.' / '.date('y').'</span></p>
                </td>
                <td>
                    <img src="images/label.jpg" alt="YCDO" width="55" height="70" align="left" />
                    <p><span style = "font-size: 6.5px;line-height:0px;text-align: center;">SERVE HUMANITY</span></p>
                    <h4 style = "line-height:10px;" align="center">UAN </br> 0304-1110222</h4>
                </td>
            </tr>
        </table>';
}    
$output .=  '
</strong></caption>

    <tr>
        <td class="quantity">Date & Time</td>
        <td class="description">'.date_format(date_create($created),'d-M-Y h:i:s A').'</td>
    </tr>
    <tr>
        <td class="quantity">Name:</td>
        <td class="description">'.$name.'</td>
    </tr>
    <tr>
        <td class="quantity">Age / Sex:</td>
        <td class="description">'.$age.' / '.$gender_title.'</td>
    </tr>
    <tr>
        <td class="quantity">Dr Name.:</td>
        <td class="description">'.$doctor_name.'</td>
    </tr>
    <tr>
        <td class="quantity">Total Bill:</td>
        <td class="description">'.$cash.'</td>
    </tr>
    <tr>
        <td class="quantity">Received Rs.:</td>
        <td class="description">'.$cash_received.'</td>
    </tr>
    <tr>
        <td class="quantity">Token_By:</td>
        <td class="description">'.$username.'</td>
    </tr>
    <tr>
        <td class="quantity">Duplicate By:</td>
        <td class="description">'.$GLOBALS['user_name'].'</td>
    </tr>
    <tr>
        <td class="quantity">Print Time</td>
        <td class="description">'.date('d-M-Y h:i:s A').'</td>
    </tr>';
    $output .= '</table>';
    $output .= '<table style = "margin: auto auto;min-width: 80mm;">';
    $output .= '<caption style = "caption-side: top; text-align: center; color: black;font-size: 16px;"><h3>Tests / Medicines as Follow:</h3></caption>';
    $output .= '<tr>';
        $output .= '<th style = "text-align: left;">ITEM</th>';
        $output .= '<th style = "text-align: right;">RATE</th>';
        $output .= '<th style = "text-align: right;">QTY</th>';
        $output .= '<th style = "text-align: right;">PRICE</th>';
    $output .= '</tr>';
$get_selected_medicines = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE `tokan_no` = '$id' ORDER BY `feed` DESC ");
if (mysqli_num_rows($get_selected_medicines) > 0) {
    while ($row_selected_medicines = mysqli_fetch_array($get_selected_medicines)) {
        $item = $row_selected_medicines['item_id'];
        if($tokan_type_id == 101)
        {
            $item_price = $row_selected_medicines['sale_price_general'];
        }
        elseif($tokan_type_id == 102)
        {
            $item_price = $row_selected_medicines['sale_price_poor'];
        }
        elseif($tokan_type_id == 103)
        {
            $item_price = $row_selected_medicines['sale_price_member'];
        }
        elseif($tokan_type_id == 104)
        {
            $item_price = $row_selected_medicines['sale_price_general'];
        }
        else
        {
            $item_price = $row_selected_medicines['sale_price_general'];
        }
        $sale_price = $row_selected_medicines['sale_price'];
        $item_id = get_item_name_by_register_item_id($row_selected_medicines['item_id']);
        $dose = $row_selected_medicines['dose'];
        $fix_dose = $row_selected_medicines['fix_dose'];
        if($fix_dose == 0)
        {
        $quantity = $dose * $row_selected_medicines['days'] * $row_selected_medicines['feed'];
        }
        else
        {
        $quantity = $fix_dose;
        }
        $feed = $row_selected_medicines['feed'];

        $select_feed = "SELECT * FROM `feeds` WHERE `category_id` = (SELECT category_id FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id = '$item'))  ORDER BY `id` ";
        $get_feed_str = mysqli_query($GLOBALS['con'], $select_feed);
        if (mysqli_num_rows($get_feed_str) > 0) 
        {
            while ($row_feed_str = mysqli_fetch_array($get_feed_str)) 
            {
                $feed_cat_id = $row_feed_str['category_id'];
                if($feed_cat_id  == 1)
                {
                $feed_title = 'گولی/گولیاں';
                }
                elseif($feed_cat_id  == 4)
                {
                $feed_title = 'ٹیکا/ٹیکے';
                }
                elseif($feed_cat_id  == 5)
                {
                $feed_title = 'چمچ';
                }
                elseif($feed_cat_id  == 6)
                {
                $feed_title = 'قطرہ/قطرے';
                }
                elseif($feed_cat_id  == 10)
                {
                $feed_title = 'کیپسول';
                }
                elseif($feed_cat_id  >= 7 && $feed_cat_id  <= 11)
                {
                $feed_title = 'بار لگایئں';
                }
                else
                {
                $feed_title = '';
                }
            }
        }
        else
        {
            $feed_title = 1;
        }

        if ($dose == 1) {$dose_title = 'صبح ';}
        elseif ($dose == 2){$dose_title = 'صبح شام';}
        elseif ($dose == 3){$dose_title = 'صبح   دوپہر شام';}

        if ($feed == 1) {   $feed_urdu = 'ایک';}
        elseif ($feed == 2) {   $feed_urdu = 'دو';}
        elseif ($feed == 3) {   $feed_urdu = 'تین';}
        elseif ($feed == 4) {   $feed_urdu = 'چار';}
        elseif ($feed == 5) {   $feed_urdu = 'پانچ';}
        elseif ($feed == 6) {   $feed_urdu = 'چھ';}
        elseif ($feed == 7) {   $feed_urdu = 'سات';}
        else {   $feed_urdu = 'آدھی';}

    $output .= '<tr>';
        $output .= '<td class="">'.$item_id.' ';
        if ($feed_title != 1) 
        {
                $output .= '</br><span> '.$feed_urdu .' '.$feed_title. ' ' .$dose_title.'</span>';
        }
        $output .= '</td>';
        $output .= '<td style = "text-align: right;" class="">'.$item_price.'</td>';
        $output .= '<td style = "text-align: right;" class="">'.$quantity.'</td>';
        $output .= '<td style = "text-align: right;" class="">'.$sale_price.'</td>';

    $output .= '</tr>';
    }
}
$output .= '</table></div>';
    return $output;
}

require_once __DIR__ . '/branch_pending_helpers.php';
?>