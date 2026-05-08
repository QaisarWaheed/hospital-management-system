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
include 'company_info.php'; 
date_default_timezone_set("Asia/Karachi");
$current_date = date('Y-m-d H:i:s');
session_start();
$_SESSION['form_token'] = bin2hex(random_bytes(32));
if (isset($_SESSION['ph_id'])) 
{
    $user_id = $_SESSION['ph_id'];
    $login_id = $_SESSION['login_id'];
    $login_expire_at = $_SESSION['login_expire_at'];
    $user_name = $_SESSION['ph_name'];
    $branch_id = $_SESSION['branch_id'];
    $is_admin = $_SESSION['is_admin'];
    $is_incharge = $_SESSION['is_incharge'];
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
if(substr($current_date,0,10) != substr($login_expire_at,0,10))
{
    header('location: logout_with_report.php');
    // print_r($_SESSION);
    // echo substr($current_date,0,10) . '<br>' . substr($login_expire_at,0,10); 
}
 
require_once __DIR__ . '/../../includes/db_connect.php';

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
    $branch_id = $GLOBALS['branch_id'];
    $output = '';
    $run1 = mysqli_query($GLOBALS['con'], "SELECT item_register_to_branches.id AS item_register_id,items.id,items.category_id,items.name, categories.name AS cat_name, item_register_to_branches.quantity AS available_branch_stock FROM `items` INNER JOIN categories ON items.category_id = categories.id INNER JOIN item_register_to_branches ON items.id = item_register_to_branches.item_id WHERE item_register_to_branches.branch_id = '$branch_id' AND items.category_id IN (2, 8, 29, 31, 32, 33, 34, 36, 39, 40, 41, 42, 44) AND items.status = '1' AND item_register_to_branches.status = '1' ORDER BY items.`name` ");
    $run2 = mysqli_query($GLOBALS['con'], "SELECT item_register_to_branches.id AS item_register_id,items.id,items.category_id,items.name, categories.name AS cat_name, item_register_to_branches.quantity AS available_branch_stock FROM `items` INNER JOIN categories ON items.category_id = categories.id INNER JOIN item_register_to_branches ON items.id = item_register_to_branches.item_id WHERE item_register_to_branches.branch_id = '$branch_id' AND items.category_id IN (1, 4, 5, 6, 7, 9, 10, 11, 12, 13, 14, 15, 16, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27) AND items.status = '1' AND item_register_to_branches.status = '1' AND item_register_to_branches.quantity > 0 ORDER BY items.`name` ");
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
            // if($available_branch_stock < 1)
            // {
                //  $output .= '<option value="'.$reg_item_id2.'">OUT OF STOCK '.$item_name2.' - '.$category_name2.'</option>';   
            // }
            // else
            // {
                 $output .= '<option value="'.$reg_item_id2.'">'.$item_name2.' - '.$category_name2.'</option>';   
            // }
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

?>