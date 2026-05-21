<?php include 'includes/connect.php';

if(isset($_GET['audit_id']) && $_GET['audit_id'] != '')
{
    $audit_id = $_GET['audit_id'];
    $br_id = $_GET['br_id'];
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
    $update = "UPDATE `audit_lab_detail` SET `manual_quantity`= '$manual_quantity',`computer_quantity`= '$computer_quantity',`audit_lab_detail_update_by`= '$user_id',`audit_lab_detail_tries`= '$tries' WHERE `audit_lab_detail_id` = '$branch_form_id' ";
    mysqli_query($con, $update);
    header('Location: audit_lab_form.php?audit_id='.$audit_id.'&br_id='.$br_id.'#show'.$branch_form_id);
    exit;
}

include 'includes/head.php';
?>
	<title>LAB AUDIT FORM - <?php echo $company_trademark; ?></title>
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
            <th width = "15%">ITEM NAME</th>
            <th width = "7%">CAT.</th>
            <th width = "10%">PRICE</th>
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
$select = "SELECT branch_item_id, audit_lab_detail_id, audit_lab_detail_tries, item_poor_price, item_register_to_branches.quantity, categories.name as cat_name, items.name as item_name  FROM `audit_lab_detail` INNER JOIN item_register_to_branches ON audit_lab_detail.branch_item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN categories ON items.category_id = categories.id WHERE `audit_lab_form_id` = '$audit_id' order by cat_name ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        if($row['audit_lab_detail_tries'] < 2)
        {
        $s = $s + 1;
        echo
        '<tr id = "show'.$row['audit_lab_detail_id'].'">
            <td>'.$s.'</td>
            <td>'.$row['item_name'].'</td>
            <td>'.$row['cat_name'].'</td>
            <td>'.$row['item_poor_price'].'</td>
            <td>'.$row['audit_lab_detail_tries'].'</td>
            <td>'.$row['quantity'].'</td>';
        echo '
            <td>
                <form method = "POST">
                    <input type = "hidden" name = "id" value = "'.$row['audit_lab_detail_id'].'" />
                    <input type = "hidden" name = "audit_id" value = "'.$audit_id.'" />
                    <input type = "hidden" name = "tries" value = "'.$row['audit_lab_detail_tries'].'" />
                    <input type = "hidden" name = "br_id" value = "'.$br_id.'" />
                    <input type = "hidden" name = "branch_item_id" value = "'.$row['branch_item_id'].'" />
                    <input type = "hidden" name = "computer_quantity"  value = "'.$row['quantity'].'"/>
                    <input type = "number" name = "manual_quantity" onchange="this.form.submit()" />
                </form>
            </td>';
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