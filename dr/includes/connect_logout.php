<?php
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d H:i:s');
session_start();
if (isset($_SESSION['ph_id'])) {
    $user_id = $_SESSION['ph_id'];
    $login_id = $_SESSION['login_id'];
    $login_expire_at = $_SESSION['login_expire_at'];
    $user_name = $_SESSION['ph_name'];
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

// if(substr($current_date,0,10) != substr($login_expire_at,0,10))
// {
//     header('location: logout_with_report.php');
//     // print_r($_SESSION);
//     // echo substr($current_date,0,10) . '<br>' . substr($login_expire_at,0,10); 
// }
 
require_once __DIR__ . '/../../includes/ycdo_mysqli_vars.php';
$con = mysqli_connect($ycdo_db_host, $ycdo_db_user, $ycdo_db_pass, $ycdo_db_name);


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
if(!$con)
    {
        echo $con->error;
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
    $query = "SELECT sum(cash_received) FROM `tokans` WHERE `user_id` = '$id' AND created > '$login_at' AND created < '$logout_at' AND `status`= '1' ";
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


function get_procedure_name($token_no)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT name FROM `items` WHERE category_id = '3' AND `id` IN (SELECT `item_id` FROM item_register_to_branches WHERE id IN (SELECT item_id FROM item_by_doctor WHERE tokan_no = '$token_no') ) ");
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

function get_register_item_id_from_item_id($item_id, $branch_idd)
{
    $output = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '1' ");
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

function next_donation_id()
{
    $run = mysqli_query($GLOBALS['con'], "SELECT id FROM donation_collection ORDER BY id desc limit 0,1");
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


function branch_medicines_by_name()
{
    $branch_id = $GLOBALS['branch_id'];
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT id,name,category_id FROM `items` ORDER BY `name` ");
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
            $select2 = "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_id' ";
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
    $run1 = mysqli_query($GLOBALS['con'], "SELECT id,name FROM `items` WHERE category_id = '3' ORDER BY `name` ");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $item_id = $row1['id'];
            $item_name = $row1['name'];

            $select2 = "SELECT id FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND `branch_id` = '$branch_id' ";
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
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id = '$patient_id' ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['name'];
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
    $output .= '
<table style="font-size: 12px;">
<caption style="text-align: center;caption-side: top;margin: auto auto;min-width: 80mm;font-weight: 900;"><strong>
        <table>
            <tr>
                <td>
                    <h3 style="text-align: centen;margin: auto auto;min-width: 60mm;max-width: 60mm;">'.$GLOBALS['branch_name'].'</h3>
                    <p style="font-size: 14px;text-align: center" >'.$GLOBALS['branch_address'].'</p>
                    <h3 align="center">UAN : 0304-1110222</h3>
                    </td>
                <td>
                    <img src="images/label.jpg" alt="Girl in a jacket" width="55" height="70" align="left" />
                </td>
            </tr>
        </table>
    
    <h3 align="center"> '.$id.' / '.date('y').'</h3>
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
<caption style="text-align: center;caption-side: top;margin: auto auto;min-width: 80mm;font-weight: 900;"><strong>
        <table>
            <tr>
                <td>
                    <h3 style="text-align: centen;margin: auto auto;min-width: 60mm;max-width: 60mm;">'.$GLOBALS['branch_name'].'</h3>
                    <p style="font-size: 14px;text-align: center" >'.$GLOBALS['branch_address'].'</p>
                    <h3 align="center">UAN : 0304-1110222</h3>
                    </td>
                <td>
                    <img src="images/label.jpg" alt="Girl in a jacket" width="55" height="70" align="left" />
                </td>
            </tr>
        </table>
    
    <h3 align="center"> '.$id.' / '.date('y').'</h3>
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
<table style="font-size: 10px;">
<caption style="text-align: center;caption-side: top;margin: auto auto;min-width: 80mm;font-weight: 900;"><strong>
        <table>
            <tr>
                <td>
                    <h5 style="text-align: centen;margin: auto auto;min-width: 60mm;max-width: 60mm;">'.$GLOBALS['branch_name'].'</h5>
                    <p style="font-size: 11px;text-align: center" >'.$GLOBALS['branch_address'].'</p>
                    <h3 align="center">UAN : 0304-1110222</h5>
                    </td>
                <td>
                    <img src="images/label.jpg" alt="Girl in a jacket" width="55" height="70" align="left" />
                </td>
            </tr>
        </table>
    
    <h3 align="center"> '.$id.' / '.date('y').'</h3>
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
        <td>Checked By Dr.:</td>
        <td>'.$doctor_name.'</td>
    </tr>
    <tr>
        <td>Tokan_Type:</td>
        <td>'.$tokan_type_title.'</td>
    </tr>
    <tr>
        <td>Total Amount:</td>
        <td>'.$cash.'</td>
    </tr>
    <tr>
        <td>Received Amount:</td>
        <td>'.$cash_received.'</td>
    </tr>
    <tr>
        <td>Tokan_By:</td>
        <td>'.$username.'</td>
    </tr>
    <tr>
        <td>Print Time</td>
        <td>'.date('d-M-Y h:i:s A').'</td>
    </tr>';
    $output .= '
    <tr>
        <td colspan="2"><strong>Tests / Medicines as Follow:</strong></td>
    </tr>';
$get_selected_medicines = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE `tokan_no` = '$id' ORDER BY `feed` DESC ");
if (mysqli_num_rows($get_selected_medicines) > 0) {
    while ($row_selected_medicines = mysqli_fetch_array($get_selected_medicines)) {
        $item = $row_selected_medicines['item_id'];
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


$output .= '
    <tr>
        <td colspan="2">'.$item_id.' ('.$quantity.')</br>';
    if ($feed_title != 1) {
        $output .= '<span> '.$feed_urdu .' '.$feed_title. ' ' .$dose_title.'</span>';
    }
$output .= '</td></tr>';
    }
}
echo '</table>';
    return $output;
}

function print_medicine_slip_duplicate($id)
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
<style>
 :root:after {    content: "DUPLICATE NOT_FOR_USE"; position: fixed; transform: rotate(300deg); -webkit-transform: rotate(300deg);    color: rgba(0, 0, 0, 0.9);     top: 200px;     z-index: -1;     font-size: 30px;     }

* {    font-size: 12px;    font-family: "Times New Roman";font-weight: 900;}
td,th,tr,table {    border-top: 1px solid black;    border-collapse: collapse;}
td.description, th.description {    width: 220px;    max-width: 220px;font-weight: 900;}
td.quantity, th.quantity {    width: 100px;    max-width: 100px;    word-break: break-all;font-weight: 900;}
.centered {    text-align: center;    align-content: center;}
.ticket {    width: 320px;    max-width: 320px;}
img {    max-width: inherit;    width: inherit;}

</style>

<table>
<caption style="text-align: center;caption-side: top;margin: auto auto;min-width: 80mm;font-weight: 900;"><strong>
        <table>
            <tr>
                <td>
                    <h3 style="text-align: centen;margin: auto auto;min-width: 60mm;max-width: 60mm;">'.$GLOBALS['branch_name'].'</h3>
                    <p style="font-size: 14px;text-align: center" >'.$GLOBALS['branch_address'].'</p>
                    <h3 align="center">UAN : 0304-1110222</h3>
                    </td>
                <td>
                    <img src="images/label.jpg" alt="Girl in a jacket" width="55" height="70" align="left" />
                </td>
            </tr>
        </table>
    
    <h3 align="center"> '.$id.' / '.date('y').'</h3>
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
        <td class="quantity">Age:</td>
        <td class="description">'.$age.'</td>
    </tr>
    <tr>
        <td class="quantity">Gender:</td>
        <td class="description">'.$gender_title.'</td>
    </tr>
    <tr>
        <td class="quantity">Checked By Dr.:</td>
        <td class="description">'.$doctor_name.'</td>
    </tr>
    <tr>
        <td class="quantity">Tokan_Type:</td>
        <td class="description">'.$tokan_type_title.'</td>
    </tr>
    <tr>
        <td class="quantity">Total Amount:</td>
        <td class="description">'.$cash.'</td>
    </tr>
    <tr>
        <td class="quantity">Received Amount:</td>
        <td class="description">'.$cash_received.'</td>
    </tr>
    <tr>
        <td class="quantity">Tokan_By:</td>
        <td class="description">'.$username.'</td>
    </tr>
    <tr>
        <td class="quantity">Print Time</td>
        <td class="description">'.date('d-M-Y h:i:s A').'</td>
    </tr>';
    $output .= '
    <tr>
        <td colspan="2"><strong>Tests / Medicines as Follow:</strong></td>
    </tr>';
$get_selected_medicines = mysqli_query($GLOBALS['con'], "SELECT * FROM `item_by_doctor` WHERE `tokan_no` = '$id' ORDER BY `feed` DESC ");
if (mysqli_num_rows($get_selected_medicines) > 0) {
    while ($row_selected_medicines = mysqli_fetch_array($get_selected_medicines)) {
        $item = $row_selected_medicines['item_id'];
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

$output .= '
    <tr>
        <td colspan="2" class="description">'.$item_id.' ('.$quantity.')</br>';
    if ($feed_title != 1) {
        $output .= '<span> '.$feed_urdu .' '.$feed_title. ' ' .$dose_title.'</span>';
    }
$output .= '</td></tr>';
    }
}
echo '</table></div>';
    return $output;
}

?>
<!--             $select_patient = "SELECT * FROM patients WHERE id = '$patient_id' ";
            $run_patient = mysqli_query($con, $select_patient);
            if (mysqli_num_rows($run_patient) == 1) {
                while ($row_patient = mysqli_fetch_array($run_patient)) {
                    // code...
                }
            }
 -->