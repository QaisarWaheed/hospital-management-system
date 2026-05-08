<?php
require_once __DIR__ . '/includes/db_connect.php';

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

function get_item_branch_name_by_register_item_id($register_item_id)
{
    $output = '';
    $run = mysqli_query($GLOBALS['con'], "SELECT branch_id FROM `item_register_to_branches` WHERE id = '$register_item_id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
        		$branch_id = $row['branch_id'];
            $output .= $branch_id;
        }
    }
    else
    {
        $output = 0;
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

function get_branch_name_by($id)
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
<html>
<head>
    
</head>
<body>
<table border = "solid">
    <thead>
        <tr>
            <th>Sr. No</th>
            <th>Id</th>
            <th>TOKEN</th>
            <th>DOCTOR</th>
            <th>ITEM</th>
            <th>QUANTITY</th>
        </tr>
    </thead>
    <tbod>
<?php
$s = 0;
$select = "SELECT * FROM `item_by_doctor` WHERE `user_id` = 0 ORDER BY `id` ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $id = $row['id'];
        $token_no = $row['tokan_no'];
        $created = $row['created'];
        $doctor = get_uname_by_id($row['doctor_id']);
        $item = get_item_name_by_register_item_id($row['item_id']);
        $item_branch = get_branch_name_by(get_item_branch_name_by_register_item_id($row['item_id']));
        
            $fix_dose = $row['fix_dose'];
            if ($fix_dose == 0) 
            {
                $quantity = $row['dose'] * $row['feed'] * $row['days'];
            }
            else
            {
                $quantity = $fix_dose;
            }
        $s = $s + 1;
        echo '
        <tr>
            <td>'.$s.'</td>
            <td>'.$id.'</td>
            <td>'.$token_no.'</td>
            <td>'.$doctor.'</td>
            <td>'.$item_branch.'</td>
            <td>'.$item.'</td>
            <td>'.$quantity.'</td>
            <td>'.$created.'</td>
        </tr>
        ';
    }
}?>
    </tbod>
</table>    
</body>
</html>