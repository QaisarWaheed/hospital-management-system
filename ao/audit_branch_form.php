<?php include 'includes/connect.php';

if(isset($_GET['audit_id']) && $_GET['audit_id'] != '')
{
    $audit_id = $_GET['audit_id'];
    $br_id = $_GET['br_id'];
    if($user_id != 1)
    {
        $select_check = "SELECT * FROM `audit_branch_form` WHERE `audit_officer_id` = '$user_id' AND `id` = '$audit_id' ";
        $run_check = mysqli_query($con, $select_check);
        if(mysqli_num_rows($run_check)!=1)
        {
            header('Location: dashboard.php');
            exit;
        }
    }
}
else
{
    header('Location: logout.php');
    exit;
}
if(isset($_POST['manual_quantity']) && $_POST['manual_quantity'] != '')
{
    $audit_id = $_POST['audit_id'];
    $branch_form_id = $_POST['id'];
    $tries = $_POST['tries'] + 1;
    $computer_quantity = $_POST['computer_quantity'];
    $manual_quantity = $_POST['manual_quantity'];
    $update = "UPDATE `audit_branch_detail` SET `manual_quantity`= '$manual_quantity',`computer_quantity`= '$computer_quantity',`user_id`= '$user_id',`tries`= '$tries' WHERE `id` = '$branch_form_id' ";
    mysqli_query($con, $update);
    header('Location: audit_branch_form.php?audit_id='.$audit_id.'&br_id='.$br_id.'#show'.$branch_form_id);
    exit;
}

include 'includes/head.php';
?>
	<title>AUDIT BRANCH FORM - <?php echo $company_trademark; ?></title>
<style>
@page 
{
  size: A4;
  margin: 10px 0px 10px 0px;
}
@media print 
{
html, body 
{
    width: 210mm;
    height: 297mm;
    font-size: 9px;
}
.noprint
{
    display: none;
}
}    
</style>
</head>

<body>
<div class="col-md-12 noprint" style="text-align: center;">
    <?php include 'top_row.php'; ?>
</div>
<table class = "table" border = "1" style = "font-size: 17px;">
    <thead>
        <tr>
            <th colspan="10">
                <h3 align = "center"><?php echo $branch_address; ?></h3>
            </th>
        </tr>
        <tr>
            <th width = "10%">SR</th>
            <th width = "22%">ITEM NAME</th>
            <th width = "10%">PRICE</th>
            <th width = "10%">CATEGORY</th>
            <th width = "10%">EXPIRY</th>
            <th width = "10%">TRIES</th>
            <th width = "10%">QUANTITY</th>
            <th width = "10%">MANUAL</th>
            <th width = "10%">COMPUTER</th>
            <th width = "10%">DIFFERENCE</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$select = "SELECT * FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE id IN (SELECT `branch_item_id` FROM `audit_branch_detail` WHERE `audit_id` = '$audit_id' ) ) ORDER BY category_id";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $expiry = '';
        $s = $s + 1;
        $id = $row['id'];
        $select_expiry = "SELECT `expiry_date` FROM `purchase_items` WHERE item_id = '$id' AND expiry_date > '".date('Y-m-d')."' AND expiry_date < DATE(NOW() + INTERVAL 6 MONTH) ORDER BY `purchase_items`.`expiry_date` DESC LIMIT 0, 1";
        $run_expiry = mysqli_query($con, $select_expiry);
        if(mysqli_num_rows($run_expiry))
        {
            while($row_expiry = mysqli_fetch_array($run_expiry))
            {
                if($row_expiry['0'] != '0000-00-00')
                $expiry = date_format(date_create($row_expiry['0']),"d-M-y");
            }
        }
        else
        {
            $expiry = '';
        }
        $name = $row['name'];
        $category_id = show_category_name($row['category_id']);
        $branch_item_id = get_br_item_id_from_item_id($id, $br_id);
        $computer_quantity = get_branch_item_quantity_from_item_id($branch_item_id);
        $audit_branch_form_id = get_audit_id_from_branch_item_id($branch_item_id, $audit_id);
        $audit_branch_form_price = get_audit_price_from_branch_item_id($branch_item_id, $audit_id);
        $tries = get_audit_tries_from_branch_item_id($branch_item_id, $audit_id);
        $manual = get_audit_manual_from_branch_item_id($branch_item_id, $audit_id);
        if($tries < 3)
        {
        echo
        '<tr id = "show'.$audit_branch_form_id.'">
            <td>'.$s.'</td>
            <td>'.$branch_item_id.' - '.$name.'</td>
            <td>'.$audit_branch_form_price.'</td>
            <td>'.$category_id.'</td>
            <td>'.$expiry.'</td>
            <td>'.$tries.'</td>
            <td>'.$computer_quantity.'</td>';
            if($tries == 0)
            {
        echo '
            <td>
                <form method = "POST">
                    <input type = "hidden" name = "id" value = "'.$audit_branch_form_id.'" />
                    <input type = "hidden" name = "audit_id" value = "'.$audit_id.'" />
                    <input type = "hidden" name = "tries" value = "'.$tries.'" />
                    <input type = "hidden" name = "br_id" value = "'.$br_id.'" />
                    <input type = "hidden" name = "branch_item_id" value = "'.$branch_item_id.'" />
                    <input type = "hidden" name = "computer_quantity"  value = "'.$computer_quantity.'"/>
                    <input type = "number" value = "'.$manual.'" name = "manual_quantity" onchange="this.form.submit()" />
                </form>
            </td>';
            }            
            elseif($manual != $computer_quantity)
            {
        echo '
            <td>
                <form method = "POST">
                    <input type = "hidden" name = "id" value = "'.$audit_branch_form_id.'" />
                    <input type = "hidden" name = "audit_id" value = "'.$audit_id.'" />
                    <input type = "hidden" name = "tries" value = "'.$tries.'" />
                    <input type = "hidden" name = "br_id" value = "'.$br_id.'" />
                    <input type = "hidden" name = "branch_item_id" value = "'.$branch_item_id.'" />
                    <input type = "hidden" name = "computer_quantity"  value = "'.$computer_quantity.'"/>
                    <input type = "number" value = "'.$manual.'" name = "manual_quantity" onchange="this.form.submit()" />
                </form>
            </td>';
            }
            else
            {
                echo '<td><div class = "badge badge-info">OK</div></td>';
            }
        echo '
            <td></td>
            <td></td>
        </tr>';
        }
    }
}
?>
    </tbody>
</table>
</body>
</html>
<?php mysqli_close($con); ?>