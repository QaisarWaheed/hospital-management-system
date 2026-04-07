<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
if(!isset($_SESSION['mm_id']))
{
    header('location: logout.php');
}
if(isset($_POST['branch_item_id']) && $_POST['branch_item_id'] != '')
{
    // $br_item_id = $_POST['branch_item_id'];
    // $manual_quantity = $_POST['manual_quantity'];
    // $update = "UPDATE `item_register_to_branches` SET `quantity`= '$manual_quantity',`is_update_quantity`= '1' WHERE `id` = '$br_item_id' ";
    // echo $update;
    // mysqli_query($con, $update);
    header("location: performa_audit.php");
    exit(0);
}
?>
	<title>PERFOMRA - <?php echo $company_trademark; ?></title>
</head>

<body>

<table border = "1" style = "font-size: 17px;">
    <thead>
        <tr>
            <th colspan="7">
                <h3 align = "center"><?php echo $branch_address; ?></h3>
            </th>
        </tr>
        <tr>
            <th width = "10%">SR</th>
            <th width = "42%">ITEM NAME</th>
            <th width = "10%">CATEGORY</th>
            <th width = "10%">EXPIRY</th>
            <th width = "10%">MANUAL</th>
            <th width = "10%">COMPUTER</th>
            <th width = "10%">DIFFERENCE</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$select = "SELECT * FROM items WHERE category_id NOT IN (2, 3, 8, 28) AND status = 1 AND id IN (SELECT item_id FROM `item_register_to_branches` WHERE branch_id = '$branch_id') ORDER BY category_id,name ";
// $select = "SELECT * FROM items WHERE category_id IN (1, 4, 5, 6, 11, 16, 19) AND status = 1 AND id IN (SELECT item_id FROM `item_register_to_branches` WHERE branch_id = '$branch_id') ORDER BY category_id,name ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $id = $row['id'];
        $name = $row['name'];
        $category_id = show_category_name($row['category_id']);
        $item_quantity = get_br_item_quantity_from_item_id($id, $branch_id);
        $branch_item_id = get_br_item_id_from_item_id($id, $branch_id);
        $is_update_quantity = get_br_item_is_update_quantity($id, $branch_id);
        if($is_update_quantity == 0)
        {
        echo
        '<tr>
            <td>'.$s.'</td>
            <td>'.$name.'</td>
            <td>'.$category_id.'</td>
            <td></td>';
        echo '
            <td>
                <form method = "POST">
                    <input type = "hidden" name = "branch_item_id" value = "'.$branch_item_id.'" />
                    <input type = "number" name = "manual_quantity" onchange="this.form.submit()" />
                </form>
            </td>';
        echo '
        <td>'.$item_quantity.'</td>
            <td></td>
        </tr>';
        }
        else
        {
        // echo
        // '<tr>
        //     <td>'.$s.'</td>
        //     <td>'.$name.'</td>
        //     <td>'.$category_id.'</td>
        //     <td></td>';
        // echo '
        //     <td>
        //         OK
        //     </td>';
        // echo '
        // <td>'.$item_quantity.'</td>
        //     <td></td>
        // </tr>';
        }
    }
}
?>
    </tbody>
</table>
</body>
</html>