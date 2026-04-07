<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
include 'includes/head.php'; 
if(isset($_POST['update_tests']) && $_POST['available_quantity'] != '')
{
    $register_item_id = $_POST['register_item_id'];
    $register_item_quantity = $_POST['register_item_quantity'];
    $available_quantity = $_POST['available_quantity'];
    $update = "UPDATE `item_register_to_branches` SET `quantity` = '$available_quantity', `updated_by` = '$lab_user_id', `updated_at` = '$current_date', `update_quantity` = '$register_item_quantity', `update_quantity_new` = '$available_quantity' WHERE `status` = '1' AND `id` = '$register_item_id' ";
    if(mysqli_query($con, $update))
    {
        echo $update_query = "INSERT INTO `update_quires`(`update_query_id`, `update_query`, `old_value`, `user_id`, `created`, `table_name`, `table_id`) VALUES(NULL, '$available_quantity', '$register_item_quantity', '$lab_user_id', '$current_date', 'item_register_to_branches', '$register_item_id')";
        if(mysqli_query($con, $update_query))
        {   
            header('location: item_receive_in_lab.php?msg=success');
            exit(0);
        }
        else
        {
            echo $con->error;
            exit(0);
        }
    }
    header('location: item_receive_in_lab.php?msg=error');
    exit(0);
}
?>
	<title>Receive Items - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div class = "row">
        <div class = "col-12 bg-light p-1">
            <?php include "navigation_top.php"; ?>
        </div>
<div class = "col-12">
	<div class="" style="margin: 10px 15px;">
		<table class="table table-hover table-bordered" style = "color: black;">
			<caption style="caption-side: top;text-align: center;color: black;">
				<h2> UPDATE LAB TESTS RECORDS </h2>
			</caption>
			<thead>
				<tr>
					<th>S NO</th>
					<th>Item Id</th>
					<th>Item Name</th>
					<th>Quantity</th>
					<th>Available</th>
				</tr>
			</thead>
			<tbody>
<?php 
$s = 0;
$select = "SELECT items.name, item_register_to_branches.quantity, item_register_to_branches.id FROM `item_register_to_branches` INNER JOIN items ON item_register_to_branches.item_id = items.id WHERE items.category_id IN (2, 7, 28) AND items.status = '1' AND item_register_to_branches.status = '1' AND item_register_to_branches.branch_id = '$lab_login_branch_id' AND item_register_to_branches.updated_by = '0' ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) {
	while ($row = mysqli_fetch_array($run)) 
	{
		$s = $s + 1;
		$register_item_id = $row['register_item_id'];
		echo '<tr>';
				echo '<td style = "text-align: center;">'.$s.'</td>';
				echo '<td style = "text-align: center;">'.$row['id'].'</td>';
				echo '<td style = "text-align: left;">'.$row['name'].'</td>';
				echo '<td style = "text-align: center;">'.$row['quantity'].'</td>'; ?>
				<th>
				<form method = "POST" action = "item_receive_in_lab.php">
				    <input type = "hidden" name = "register_item_id" id = "register_item_id" value = "<?php echo $row['id']; ?>" class = "form-control" />
				    <input type = "hidden" name = "register_item_quantity" id = "register_item_quantity" value = "<?php echo $row['quantity']; ?>" class = "form-control" />
				    <input type = "number" min = "0" name = "available_quantity" id = "available_quantity" value = "<?php echo $row['quantity']; ?>" class = "form-control" />
				    <input type = "submit" value = "UPDATE TESTS" name = "update_tests" class = "btn btn-sm btn-primary" />
				</form>
				</th>
				<?php
		echo '</tr>';
	}
}
?>          </tbody>
		</table>
	</div>
</div>
</div>


</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>