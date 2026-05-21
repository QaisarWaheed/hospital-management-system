<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php';
if(isset($_POST['audit_lab_store_form_id']) && $_POST['audit_lab_store_form_id'] != '')
{
    $audit_lab_store_form_id = $_POST['audit_lab_store_form_id'];
}
elseif(isset($_GET['audit_lab_store_form_id']) && $_GET['audit_lab_store_form_id'] != '')
{
    $audit_lab_store_form_id = $_GET['audit_lab_store_form_id'];
    // if($user_id != 1)
    // {
    //     $select_check = "SELECT * FROM `audit_lab_store_form` WHERE `user_id` = '$user_id' AND `audit_lab_store_form_id`= '$audit_lab_store_form_id' ";
    //     $run_check = mysqli_query($con, $select_check);
    //     if(mysqli_num_rows($run_check)!=1)
    //     {
    //         header('location: dashboard.php');
    //         exit(0);
    //     }
    // }
}
else
{
    header('location: logout.php');
}
if(isset($_POST['manual_stock']) && $_POST['manual_stock'] != '')
{
    print_r($_POST);
    $audit_lab_store_form_id = $_POST['audit_lab_store_form_id'];
    $audit_lab_store_detail_id = $_POST['audit_lab_store_detail_id'];
    $computer_stock = $_POST['computer_stock'];
    $manual_stock = $_POST['manual_stock'];
    if($computer_stock != $manual_stock)
    {
        echo $update = "UPDATE `audit_lab_store_detail` SET `manual_stock` = '$manual_stock', computer_stock = '$computer_stock', `audit_lab_store_detail_updated_by` = '$user_id', `manual_tries` = `manual_tries`+1 WHERE audit_lab_store_detail_status = '1' AND `audit_lab_store_detail_id` = '$audit_lab_store_detail_id' ";
    }
    else
    {
        echo $update = "UPDATE `audit_lab_store_detail` SET `manual_stock` = '$manual_stock', computer_stock = '$computer_stock', `audit_lab_store_detail_updated_by` = '$user_id', `manual_tries` = `manual_tries`+1, audit_lab_store_detail_status = '2' WHERE audit_lab_store_detail_status = '1' AND `audit_lab_store_detail_id` = '$audit_lab_store_detail_id' ";    
    }
    mysqli_query($con, $update);
    header("location: audit_lab_store_form.php?audit_lab_store_form_id=".$audit_lab_store_form_id."&#show".$audit_lab_store_detail_id);
    exit(0);
}
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
            <th width = "5%">SR</th>
            <th width = "5%">ID</th>
            <th width = "22%">ITEM NAME</th>
            <th width = "5%">EXPIRY</th>
            <th width = "5%">PRICE</th>
            <th width = "10%">CATEGORY</th>
            <th width = "10%">MANUAL TRIES</th>
            <th width = "10%">COMPUTER</th>
            <th width = "10%">MANUAL</th>
            <th width = "10%">QUANTITY</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
if(isset($_GET['audit_lab_store_form_id']) && $_GET['audit_lab_store_form_id'] != '')
{
    $audit_lab_store_form_id =$_GET['audit_lab_store_form_id'];
}
$select = "SELECT items.name, items.category_id, items.poor, items.quantity AS item_quantity, computer_stock, manual_stock, manual_tries, audit_lab_store_detail_status, audit_lab_store_detail_id, audit_lab_store_form_id FROM `audit_lab_store_detail` INNER JOIN items ON audit_lab_store_detail.item_id = items.id WHERE `audit_lab_store_form_id` = '$audit_lab_store_form_id' ORDER BY items.category_id";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $item_id = $row['item_id'];
        $name = $row['name'];
        $item_quantity = $row['item_quantity'];
        $computer_stock = $row['computer_stock'];
        $manual_stock = $row['manual_stock'];
        $category_name = $row['category_id'];
        $manual_tries = $row['manual_tries'];
        $poor = $row['poor'];
        $audit_lab_store_detail_id = $row['audit_lab_store_detail_id'];
        $audit_lab_store_form_id = $row['audit_lab_store_form_id'];
        $audit_lab_store_detail_status = $row['audit_lab_store_detail_status'];
        // if($manual_tries < 5 && ($item_quantity > 0 || $manual_tries > 0) )
        if($manual_tries < 5 )
        {
        $s = $s + 1;
        echo
        '<tr id = "show'.$audit_lab_store_detail_id.'">
            <td>'.$s.'</td>
            <td>'.$audit_lab_store_detail_id.'</td>
            <td>'.$name.' ('.$row['category_id'].')</td>
            <td></td>
            <td>'.$poor.'</td>
            <td>'.$category_name.'</td>
            <td>'.$manual_tries.'</td>
            <td>'.$computer_stock.' - '.$item_quantity.'</td>
            <td>'.$manual_stock.'</td>';
            if($audit_lab_store_detail_status == 1)
            {
                echo '
                    <td>
                        <form method = "POST">
                            <input type = "hidden" name = "audit_lab_store_form_id" value = "'.$audit_lab_store_form_id.'" />
                            <input type = "hidden" name = "audit_lab_store_detail_id" value = "'.$audit_lab_store_detail_id.'" />
                            <input type = "hidden" name = "item_id" value = "'.$item_id.'" />
                            <input type = "hidden" name = "manual_tries" value = "'.$manual_tries.'" />
                            <input type = "hidden" name = "computer_stock"  value = "'.$item_quantity.'"/>
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