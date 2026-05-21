<?php include 'includes/connect.php';

if(isset($_GET['audit_store_form_id']) && $_GET['audit_store_form_id'] != '')
{
    $audit_store_form_id = $_GET['audit_store_form_id'];
    if($user_id != 1)
    {
        $select_check = "SELECT * FROM `audit_store_form` WHERE `user_id` = '$user_id' AND `audit_store_form_id`= '$audit_store_form_id' ";
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
if(isset($_POST['manual_stock']) && $_POST['manual_stock'] != '')
{
    $audit_store_form_id = $_POST['audit_store_form_id'];
    $audit_store_detail_id = $_POST['audit_store_detail_id'];
    $computer_stock = $_POST['computer_stock'];
    $manual_stock = $_POST['manual_stock'];
    if($computer_stock != $manual_stock)
    {
        $update = "UPDATE `audit_store_detail` SET `manual_stock`= '$manual_stock',`computer_stock`= '$computer_stock',`user_id`= '$user_id',`manual_tries`= `manual_tries`+1 WHERE `audit_store_detail_id` = '$audit_store_detail_id' ";
    }
    else
    {
        $update = "UPDATE `audit_store_detail` SET `manual_stock`= '$manual_stock',`computer_stock`= '$computer_stock',`user_id`= '$user_id',`manual_tries`= `manual_tries`+1, audit_store_detail_status = '2' WHERE `audit_store_detail_id` = '$audit_store_detail_id' ";
    }
    mysqli_query($con, $update);
    header('Location: audit_store_form.php?audit_store_form_id='.$audit_store_form_id.'#show'.$audit_store_detail_id);
    exit;
}

include 'includes/head.php';
?>
	<title>AUDIT store FORM - <?php echo $company_trademark; ?></title>
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
                <h3 align = "center"><?php echo $store_address; ?></h3>
            </th>
        </tr>
        <tr>
            <th width = "10%">SR</th>
            <th width = "22%">ITEM NAME</th>
            <th width = "5%">EXPIRY</th>
            <th width = "5%">PRICE</th>
            <th width = "10%">CATEGORY</th>
            <th width = "10%">MANUAL TRIES</th>
            <th width = "10%">COMPUTER</th>
            <th width = "10%">MANUAL</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$select = "SELECT items.id AS item_id, items.name AS name, categories.name AS category_name, poor, quantity FROM items INNER JOIN categories ON items.category_id = categories.id WHERE items.id IN (SELECT item_id FROM audit_store_detail WHERE `audit_store_form_id` = '$audit_store_form_id' ) ORDER BY category_id";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $item_id = $row['item_id'];
        $name = $row['name'];
        $computer_stock = $row['quantity'];
        $category_name = $row['category_name'];
        $poor = $row['poor'];
        $run_tries = mysqli_query($con, "SELECT * FROM `audit_store_detail` WHERE `audit_store_form_id` = '$audit_store_form_id' AND `item_id` = '$item_id' ");
        if(mysqli_num_rows($run_tries) == 1)
        {
            while($row_tries = mysqli_fetch_array($run_tries))
            {
                $audit_store_detail_id = $row_tries['audit_store_detail_id'];
                $manual_tries = $row_tries['manual_tries'];
                $manual_stock = $row_tries['manual_stock'];
                $audit_store_detail_status = $row_tries['audit_store_detail_status'];
            }
        }
        else
        {
            $manual_tries = 0;
        }
        if($manual_tries < 5)
        {
        $s = $s + 1;
        echo
        '<tr id = "show'.$audit_store_detail_id.'">
            <td>'.$s.'</td>
            <td>'.$name.'</td>
            <td></td>
            <td>'.$poor.'</td>
            <td>'.$category_name.'</td>
            <td>'.$manual_tries.'</td>
            <td>'.$computer_stock.'</td>';
            if($audit_store_detail_status == 1)
            {
                echo '
                    <td>
                        <form method = "POST">
                            <input type = "hidden" name = "audit_store_form_id" value = "'.$audit_store_form_id.'" />
                            <input type = "hidden" name = "audit_store_detail_id" value = "'.$audit_store_detail_id.'" />
                            <input type = "hidden" name = "item_id" value = "'.$item_id.'" />
                            <input type = "hidden" name = "manual_tries" value = "'.$manual_tries.'" />
                            <input type = "hidden" name = "computer_stock"  value = "'.$computer_stock.'"/>
                            <input type = "number" name = "manual_stock" value = "'.$manual_stock.'" onchange="this.form.submit()" />
                        </form>
                    </td>';
            }
            else
            {
                echo '<td><span class = "badge badge-info">OK</span></td>';                
            }
        echo '
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