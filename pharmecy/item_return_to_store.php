<?php
require_once __DIR__ . '/includes/connect.php';
include 'includes/head.php';

if (isset($_POST['save'])) {
    $return_no = (int) $_POST['return_no'];
    $quantity = (float) $_POST['quantity'];
    $expire_date = mysqli_real_escape_string($con, $_POST['expire_date'] ?? '');
    $item_id = (int) $_POST['item_id'];
    $branch_item_id = get_branch_item_id_from_item_id($item_id, $branch_id);

    if ($return_no < 1 || $quantity < 1 || $item_id < 1 || $branch_item_id < 1) {
        echo '<script>alert("Invalid return data. Check item, quantity, and return number.");location.replace("item_return_to_store.php");</script>';
        exit;
    }

    $insert = "INSERT INTO `return_item_by_branch`(`branch_item_id`, `quantity`, `branch_admin`, `status`, `created`, `return_no`, `expire_date`) VALUES 
    ('$branch_item_id', '$quantity', '$user_id', '1', '$current_date', '$return_no', '$expire_date')";
    $run_insert = mysqli_query($con, $insert);
    if ($run_insert) {
        echo '<script>alert("Item return to store saved.");location.replace("item_return_to_store.php");</script>';
    } else {
        $err = htmlspecialchars(mysqli_error($con), ENT_QUOTES, 'UTF-8');
        echo '<script>alert("Save failed: '.$err.'");location.replace("item_return_to_store.php");</script>';
    }
    exit;
}
?>
	<title>Return Items - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div class = "row">
        <div class = "col-12 bg-light p-1">
            <?php include "navigation_top.php"; ?>
        </div>
<div class = "col-12">
	<div class="" style="margin: 10px 15px;">
		<table class="table table-hover table-bordered">
			<caption style="caption-side: top;text-align: center;">
				<h2>RETURN ITEMS </h2>
			</caption>
			<thead>
				<tr>
					<th>S NO</th>
					<th>Return No</th>
					<th>Item Name</th>
					<th>Branch Name</th>
					<th>Expiry</th>
					<th>Quantity</th>
					<th>Action</th>
				</tr>
<form method="POST">
				<tr>
				    <td></td>
				    <td><input type="number" min="1" value="1" required name="return_no" id="return_no" class="form-control" placeholder="Return No"/></td>
				    <td>
				        <select name="item_id" class="form-control" required>
                        <?php
                        $select = "SELECT items.id AS id, items.name AS name, categories.name AS category_name FROM `items` INNER JOIN categories ON items.category_id = categories.id WHERE items.id IN (SELECT `item_id` FROM `item_register_to_branches` WHERE `branch_id` = '$branch_id') ORDER BY items.name";
                        $run = mysqli_query($con, $select);
                        if ($run && mysqli_num_rows($run) > 0) {
                            echo '<option value="">SELECT ITEM</option>';
                            while ($row = mysqli_fetch_array($run)) {
                                echo '<option value="'.$row['id'].'">'.htmlspecialchars($row['name']).'('.htmlspecialchars($row['category_name']).')</option>';
                            }
                        } else {
                            echo '<option value="">NO REGISTER ITEM</option>';
                        }
                        ?>
				        </select>
				    </td>
				    <td>
				        <input readonly type="text" value="<?php echo htmlspecialchars($branch_name); ?>" name="branch_name" id="branch_name" class="disabled form-control"/>
				    </td>
				    <td><input type="date" required name="expire_date" id="expire_date" class="form-control"/></td>
				    <td><input type="number" min="1" value="1" required name="quantity" id="quantity" class="form-control" placeholder="Quantity"/></td>
				    <td><input type="submit" name="save" id="save" class="btn btn-info" value="Save" /></td>
				</tr>
</form>
			</thead>
			<tbody>
<?php
$sr = 0;
$return_item = "SELECT * FROM `return_item_by_branch` WHERE status = 1 ORDER BY `return_no` DESC";
$run_return_item = mysqli_query($con, $return_item);
if ($run_return_item && mysqli_num_rows($run_return_item) > 0) {
    while ($row_return_item = mysqli_fetch_array($run_return_item)) {
        $return_no = $row_return_item['return_no'];
        $return_quantity = $row_return_item['quantity'];
        $expire_date = $row_return_item['expire_date'];
        $item_name = get_item_name_by_register_item_id($row_return_item['branch_item_id']);
        $sr++;
        echo '
		<tr>
			<th>'.$sr.'</th>
			<th>'.$row_return_item['return_no'].'</th>
			<th>'.htmlspecialchars($item_name).'</th>
			<th>'.htmlspecialchars($branch_name).'</th>
			<th>'.htmlspecialchars($expire_date).'</th>
			<th>'.$return_quantity.'</th>
			<th></th>
		</tr>
    ';
    }
}
?>
			</tbody>
		</table>
	</div>
</div>
</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
