<?php
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d H:i:s');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['dr_id'])) {
    header('Location: logout.php');
    exit;
}

$user_id = (int) $_SESSION['dr_id'];
$user_name = $_SESSION['dr_name'] ?? '';
$branch_id = $_SESSION['branch_id'] ?? 0;
$is_admin = $_SESSION['is_admin'] ?? 0;
$is_incharge = $_SESSION['is_incharge'] ?? 0;
$branch_name = $_SESSION['branch_name'] ?? '';
$branch_address = $_SESSION['branch_address'] ?? '';
$branch_phone = $_SESSION['branch_phone'] ?? '';

if ($user_id < 1) {
    header('Location: logout.php');
    exit;
}

require_once __DIR__ . '/../../includes/ycdo_mysqli_vars.php';
mysqli_report(MYSQLI_REPORT_OFF);
$con = mysqli_connect($ycdo_db_host, $ycdo_db_user, $ycdo_db_pass, $ycdo_db_name);
if (!$con) {
    die(mysqli_connect_error());
}

include 'company_info.php';

function get_doctor_id_by_token_no($token_no)
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

function get_patient_phone_by_token_no($token_no)
{
    $output = '';
    $get_patient = mysqli_query($GLOBALS['con'], "SELECT * FROM patients WHERE id IN (SELECT patient_id FROM tokans WHERE id = '$token_no') ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['phone'];
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

function weeks_between($datefrom, $dateto)
{
    $datefrom = DateTime::createFromFormat('d/m/Y H:i:s',$datefrom);
    $dateto = DateTime::createFromFormat('d/m/Y H:i:s',$dateto);
    $interval = $dateto->diff($datefrom);
    $week_total = $interval->format('%a')/7;
    return floor($week_total)-33;

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

function get_item_id_by_register_item_id($id)
{
    $con = $GLOBALS['con'];
    $output = '';
    $query = "SELECT `item_id` FROM `item_register_to_branches` WHERE `id` = '$id' ";
    $run = mysqli_query($con,  $query);
    if (mysqli_num_rows($run) > 0) 
    {
        while ( $row = mysqli_fetch_array($run) ) 
        {
            $output .= $row['item_id'];
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


function show_departments_option()
{
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `departments` WHERE `department_status` = '1' ORDER BY `department_title` ");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $department_id = $row1['department_id'];
            $department_title = $row1['department_title'];
            $output .= '<option value="'.$department_id.'">'.$department_title.'</option>';   
        }
    }
    else
    {
        return '<option>NO DATA FOUND</option>';
    }
    return $output;
}

function branch_medicines_by_name()
{
    $branch_id = $GLOBALS['branch_id'];
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT item_register_to_branches.id AS item_register_id,items.id,items.category_id,items.name, categories.name AS cat_name, item_register_to_branches.quantity AS available_branch_stock FROM `items` INNER JOIN categories ON items.category_id = categories.id INNER JOIN item_register_to_branches ON items.id = item_register_to_branches.item_id WHERE item_register_to_branches.branch_id = '$branch_id' AND items.category_id IN (2, 8, 29, 39, 40, 41, 42) AND items.status = '1' AND item_register_to_branches.status = '1' ORDER BY items.`name` ");
    $run2 = mysqli_query($GLOBALS['con'], "SELECT item_register_to_branches.id AS item_register_id,items.id,items.category_id,items.name, categories.name AS cat_name, item_register_to_branches.quantity AS available_branch_stock FROM `items` INNER JOIN categories ON items.category_id = categories.id INNER JOIN item_register_to_branches ON items.id = item_register_to_branches.item_id WHERE item_register_to_branches.branch_id = '$branch_id' AND items.category_id IN (1, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15, 16, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27) AND items.status = '1' AND item_register_to_branches.status = '1' ORDER BY items.`name` ");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $item_id = $row1['id'];
            $category_id = $row1['category_id'];
            $item_name = $row1['name'];
            $category_name = $row1['cat_name'];
            $reg_item_id = $row1['item_register_id'];
                 $output .= '<option value="'.$reg_item_id.'">'.$item_name.' - '.$category_name.'</option>';   
        }
    }    
    if(mysqli_num_rows($run2) > 0)  
    {
        while ($row2 = mysqli_fetch_array($run2)) 
        {
            $available_branch_stock = $row2['available_branch_stock'];
            $item_id2 = $row2['id'];
            $category_id2 = $row2['category_id'];
            $item_name2 = $row2['name'];
            $category_name2 = $row2['cat_name'];
            $reg_item_id2 = $row2['item_register_id'];
            if($available_branch_stock < 1)
            {
                 $output .= '<option value="'.$reg_item_id2.'">OUT OF STOCK '.$item_name2.' - '.$category_name2.'</option>';   
            }
            else
            {
                 $output .= '<option value="'.$reg_item_id2.'">'.$item_name2.' - '.$category_name2.'</option>';   
            }
        }
    }
    else
    {
        return '<option>NO DATA FOUND</option>';
    }
    return $output;
}

function branch_medicines_by_name2()
{
    $branch_id = $GLOBALS['branch_id'];
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT id,name,category_id FROM `items` WHERE category_id NOT IN (3, 28) AND status = '1' ORDER BY `name` ");
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

function medicine_selected_by_doctor($token_id)
{
    $output = '';
    $branch_id = $GLOBALS['branch_id'];
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `select_by_doctor` WHERE branch_id = ".$branch_id." AND tokan_no = ".$token_id." AND status = '1' AND item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (SELECT id FROM `items` WHERE `category_id` != 2)) ");
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
            $output .= '<a href = "patient_by_token.php?token_id='.$token_id.'&del_medicine='.$row['id'].'" style = "color: red;">X</a>'.$item_name.' - '.$quantity.'</br>';
        }
    }
    else{
        return '';
    }
    return $output;
}

function get_select_amount_array($token_no)
{
    $amount_poor = 0;
    $amount_member = 0;
    $amount_general = 0;
    $select = 'general';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `select_by_doctor` WHERE tokan_no = '$token_no' AND  status = '1' ");
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
            $output .= '<a href = "patient_by_token.php?token_id='.$token_id.'&del_medicine='.$row['id'].'" style = "color: red;">X</a>'.$item_name.' - '.$quantity.'</br>';
        }
    }
    else{
        return '';
    }
    return $output;
}

function show_department_by_id($department_id)
{
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `departments` WHERE `department_status` = '1' AND `department_id` = '$department_id' ");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $department_id = $row1['department_id'];
            $department_title = $row1['department_title'];
            $output .= '<option value="'.$department_id.'">'.$department_title.'</option>';   
        }
    }
    else
    {
        return '<option>NO DATA FOUND</option>';
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


function show_doctors_by_department_id($department_id)
{
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `users` WHERE `consultant_status` = '1' AND `department_id` = '$department_id' ");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $consultant_id = $row1['id'];
            $consultant_name = $row1['u_name'];
            $consultant_branch_name = get_branch_address($row1['branch_id']);
            $consultant_in_time = date_format(date_create($row1['in_time']), "h:i:s A");
            $consultant_out_time = date_format(date_create($row1['out_time']), "h:i:s A");
            $consultant_qualification = $row1['qualification'];
            $consultant_phone = $row1['phone'];
            $output .= '<option value="'.$consultant_id.'">'.$consultant_name.' ('.$consultant_in_time.' - '.$consultant_out_time.') '.$consultant_qualification.' ('.$consultant_branch_name.')</option>';   
        }
    }
    else
    {
        return '<option>NO DATA FOUND</option>';
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