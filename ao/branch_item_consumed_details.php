<?php 
include 'includes/connect.php';
include 'includes/head.php'; 
if(!isset($_SESSION['ao_id']))
{
    header('location: logout.php');
}
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
            header('location: dashboard.php');
            exit(0);
        }
    }
}
else
{
    header('location: logout.php');
}
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
            <th>SR</th>
            <th>ITEM NAME</th>
            <th>PRICE</th>
            <th>CATEGORY</th>
            <th>UPDATED AT</th>
            <th>COMPUTER</th>
            <th>MANUAL</th>
            <th>ISSUED</th>
            <th>CONSUMED</th>
            <th>CURRENT STOCK</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$select = "SELECT audit_branch_detail.id ,audit_branch_detail.branch_item_id , item_register_to_branches.quantity AS available_quantity, items.name AS item_name, categories.name AS cat_name, audit_branch_detail.computer_quantity, audit_branch_detail.manual_quantity, audit_branch_detail.item_poor_price, audit_branch_detail.updated_at FROM `audit_branch_detail` INNER JOIN item_register_to_branches ON audit_branch_detail.branch_item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id AND item_register_to_branches.branch_id = '$br_id' INNER JOIN categories ON items.category_id = categories.id WHERE `audit_id` = '$audit_id' ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $issued_quantity = 0;
        $consumed_quantity = 0;
        $branch_item_id = $row['branch_item_id'];
        $updated_at = $row['updated_at'];
        $select_issue = "SELECT SUM(`quantity`) FROM `item_register_branchs_by_sm` WHERE `branch_item_id` = '$branch_item_id' AND `created` > '$updated_at' ";
        $run_issue = mysqli_query($con, $select_issue);
        if(mysqli_num_rows($run_issue) == 1)
        {
            while($row_issue = mysqli_fetch_array($run_issue))
            {
                $issued_quantity = $row_issue['0'];
                if (is_null($issued_quantity)) {
                    $issued_quantity =  0;
                }
            }
        }
        echo
        '<tr id = "show'.$row['id'].'">
            <td>'.$s.'</td>
            <td>'.$branch_item_id.' - '.$row['item_name'].'</td>
            <td>'.$row['item_poor_price'].'</td>
            <td>'.$row['cat_name'].'</td>
            <td>'.date_format(date_create($updated_at), 'd-m-Y').'</td>
            <td>'.$row['computer_quantity'].'</td>
            <td>'.$row['manual_quantity'].'</td>
            <td>'.$issued_quantity.'</td>
            <td>
                <form method = "POST" action = "show_consumed_item_records.php" target = "_blank">
                    <input type = "hidden" value = "'.$br_id.'" name = "br_id" />
                    <input type = "hidden" value = "'.$branch_item_id.'" name = "branch_item_id" />
                    <input type = "hidden" value = "'.$updated_at.'" name = "updated_at" />
                    <input type = "submit" value = "VIEW DETAILS" name = "show_consumed_data" class = "btn btn-default btn-sm" />
                </form>
            </td>
            <td>'.$row['available_quantity'].'</td>
        </tr>';
    }
}
?>
    </tbody>
</table>
</body>
</html>
<?php mysqli_close($con); ?>