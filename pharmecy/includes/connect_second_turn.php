<?php
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d H:i:s');

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

$db_host = getenv('DB_HOST');
if ($db_host === false || $db_host === '') {
    $db_host = file_exists('/.dockerenv') ? 'srv-captain--mysql-db' : 'localhost';
}
if ($db_host === 'localhost' && PHP_OS_FAMILY !== 'Windows') {
    $db_host = '127.0.0.1';
}
$con = mysqli_connect($db_host, getenv('DB_USER') ?: 'ycdoeh1', getenv('DB_PASS') ?: 'ycdoeh1', getenv('DB_NAME') ?: 'ycdomlt');
if (!$con) {
    die(mysqli_connect_error());
}

include 'company_info.php';

$role_title = '';
$roles = "SELECT title FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') LIMIT 1";
$run_roles = mysqli_query($con, $roles);
if ($run_roles && mysqli_num_rows($run_roles) === 1) {
    $row_role = mysqli_fetch_array($run_roles);
    $role_title = $row_role['title'] ?? '';
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
function get_branch_item_id_from_items_by_doctor_id($id)
{
    $con = $GLOBALS['con'];
    $item_id = '';
    $query = "SELECT item_id FROM items_by_doctor WHERE id = '$id' ";
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
function get_item_quantity_from_item_by_docotor_id($id)
{
    $quantity = 0;
    $run = mysqli_query($GLOBALS['con'], "SELECT dose, feed, days, fix_dose FROM items_by_doctor WHERE id = '$id' ");
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
function get_selected_amount_array()
{
    $amount_poor = 0;
    $amount_member = 0;
    $amount_general = 0;
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
function get_select_amount_array()
{
    $amount_poor = 0;
    $amount_member = 0;
    $amount_general = 0;
    $select = 'general';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT * FROM `items_by_doctor` WHERE branch_id = ".$GLOBALS['branch_id']." AND user_id = ".$GLOBALS['user_id']." AND status = '1' ");
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
function branch_medicines_by_name()
{
    $branch_id = (int) $GLOBALS['branch_id'];
    $output = '';
    if ($branch_id < 1) {
        return '<option value="" disabled>No branch in session — log in with a branch user</option>';
    }
    // All items mapped to this branch (not only hard-coded category IDs).
    $sql = "SELECT irb.id AS reg_item_id, i.name AS item_name, i.category_id, c.name AS cat_name, irb.quantity AS qty
        FROM item_register_to_branches irb
        INNER JOIN items i ON irb.item_id = i.id
        INNER JOIN categories c ON i.category_id = c.id
        WHERE irb.branch_id = $branch_id
          AND irb.status = 1
          AND i.status = 1
          AND i.category_id NOT IN (3, 28)
        ORDER BY i.name ASC";
    $run = mysqli_query($GLOBALS['con'], $sql);
    if (!$run) {
        return '<option value="" disabled>Unable to load medicines list</option>';
    }
    while ($row = mysqli_fetch_assoc($run)) {
        $reg_item_id = (int) $row['reg_item_id'];
        $item_name = htmlspecialchars($row['item_name'], ENT_QUOTES, 'UTF-8');
        $cat_name = htmlspecialchars($row['cat_name'], ENT_QUOTES, 'UTF-8');
        $prefix = ((int) $row['qty'] < 1 && pharmecy_item_requires_stock_check($row['category_id'])) ? 'OUT OF STOCK ' : '';
        $output .= '<option value="'.$reg_item_id.'">'.$prefix.$item_name.' - '.$cat_name.'</option>';
    }
    if ($output === '') {
        return '<option value="" disabled>No items mapped to this branch in item_register_to_branches</option>';
    }
    return $output;
}

function branch_medicines_by_name2()
{
    $branch_id = $GLOBALS['branch_id'];
    $output = '';
    // $run1 = mysqli_query($GLOBALS['con'], "SELECT item_register_to_branches.id as reg_item_id, items.name as item_name,categories.name as cat_name FROM items INNER JOIN categories ON items.category_id = categories.id INNER JOIN item_register_to_branches ON item_register_to_branches.item_id = items.id WHERE item_register_to_branches.quantity > 0 AND category_id NOT IN (2, 3,28) AND items.status = '1' AND item_register_to_branches.branch_id = '$branch_id' ORDER BY items.name");
    $run1 = mysqli_query($GLOBALS['con'], "SELECT item_register_to_branches.id as reg_item_id, items.name as item_name,categories.name as cat_name FROM items INNER JOIN categories ON items.category_id = categories.id INNER JOIN item_register_to_branches ON item_register_to_branches.item_id = items.id WHERE category_id NOT IN (2, 3,28) AND items.status = '1' AND item_register_to_branches.branch_id = '$branch_id' ORDER BY items.name");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $reg_item_id = $row1['reg_item_id'];
            $item_name = $row1['item_name'];
    		$category_name = $row1['cat_name'];
            $output .= '<option value="'.$reg_item_id.'">'.$item_name.' - '.$category_name.'</option>';   
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
    $run1 = mysqli_query($GLOBALS['con'], "SELECT item_register_to_branches.id as reg_item_id, items.name as item_name,categories.name as cat_name FROM items INNER JOIN categories ON items.category_id = categories.id INNER JOIN item_register_to_branches ON item_register_to_branches.item_id = items.id WHERE category_id NOT IN (2, 3, 18, 28) AND items.status = '1' AND item_register_to_branches.branch_id = '$branch_id' ORDER BY items.name");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $reg_item_id = $row1['reg_item_id'];
            $item_name = $row1['item_name'];
    		$category_name = $row1['cat_name'];
            $output .= '<option value="'.$reg_item_id.'">'.$item_name.' - '.$category_name.'</option>';   
        }
    }
    else
    {
        return '<option>NO DATA FOUND</option>';
    }
    return $output;
}

function branch_lab_by_name()
{
    $branch_id = $GLOBALS['branch_id'];
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT item_register_to_branches.id as reg_item_id, items.name as item_name,categories.name as cat_name FROM items INNER JOIN categories ON items.category_id = categories.id INNER JOIN item_register_to_branches ON item_register_to_branches.item_id = items.id WHERE category_id IN (2) AND items.status = '1' AND item_register_to_branches.branch_id = '$branch_id' ORDER BY items.name");
    if (mysqli_num_rows($run1) > 0)  
    {
        while ($row1 = mysqli_fetch_array($run1)) 
        {
            $reg_item_id = $row1['reg_item_id'];
            $item_name = $row1['item_name'];
    		$category_name = $row1['cat_name'];
            $output .= '<option value="'.$reg_item_id.'">'.$item_name.' - '.$category_name.'</option>';   
        }
    }
    else
    {
        return '<option>NO DATA FOUND</option>';
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
function medicines_selected_list()
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `items_by_doctor` WHERE branch_id = ".$GLOBALS['branch_id']." AND user_id = ".$GLOBALS['user_id']." AND status = '1' ");
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
            $output .= '<a href = "action_del_medicine.php?del_medicine='.$row['id'].'" style = "color: red;">X </a>'.$item_name.' - '.$quantity.'</br>';
        }
    }
    else{
        return '<p>ADD DATA IN BRANCH</p>';
    }
    return $output;
}
function medicine_selected_list()
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
            $output .= '<a href = "action_del_medicine.php?del_medicine='.$row['id'].'" style = "color: red;">X </a>'.$item_name.' - '.$quantity.'</br>';
        }
    }
    else{
        return '<p>ADD DATA IN BRANCH</p>';
    }
    return $output;
}
function medicine_select_list()
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `items_by_doctor` WHERE branch_id = ".$GLOBALS['branch_id']." AND user_id = ".$GLOBALS['user_id']." AND status = '1' ");
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
            // $output .= '<a href = "action_del_medicine.php?del_medicine='.$row['id'].'" style = "color: red;">X </a>'.$item_name.' - '.$quantity.'</br>';
            $output .= '<form method="GET" action="action_del_medicine.php" onsubmit="showProgress(); return true;"><input type="hidden" name="del_medicine" value="'.$row['id'].'"><input class = "btn btn-sm btn-danger" type="submit" name="X" value="x">'.$item_name.' - '.$quantity.'</form>';
        }
    }
    else{
        return '<p>ADD DATA IN BRANCH</p>';
    }
    return $output;
}
function medicine_select_list_pending()
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `items_by_doctor` WHERE branch_id = ".$GLOBALS['branch_id']." AND user_id = ".$GLOBALS['user_id']." AND status = '1' ");
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
            $output .= '<a onclick="showProgress(); return true;" href = "second_turn_pending.php?del_medicine='.$row['id'].'" style = "color: red;">X </a>'.$item_name.' - '.$quantity.'</br>';
        }
    }
    else{
        return '<p>ADD DATA IN BRANCH</p>';
    }
    return $output;
}
function medicine_select_list_procedure()
{
    $output = '';
    $search_tokan_no = $GLOBALS['search_tokan_no'];
    $run = mysqli_query($GLOBALS['con'], "SELECT * FROM `items_by_doctor` WHERE branch_id = ".$GLOBALS['branch_id']." AND user_id = ".$GLOBALS['user_id']." AND status = '1' ");
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
            $output .= '<a href = "second_procedure_turn_medicines.php?del_medicine='.$row['id'].'&search_tokan_no='.$search_tokan_no.'" style = "color: red;">X </a>'.$item_name.' - '.$quantity.'</br>';
        }
    }
    else{
        return '<p>ADD DATA IN BRANCH</p>';
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

if(!$con)
    {
        echo $con->error;
    }

require_once __DIR__ . '/branch_pending_helpers.php';
?>