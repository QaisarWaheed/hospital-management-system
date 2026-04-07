<?php
include 'includes/connect_doctor_turn.php'; 
if (isset($_POST['save_test'])) 
{
	$reg_item_id = $_POST['reg_item_id'];
	$select_items = "SELECT id, purchase, poor, member, general, deserving, category_id FROM `items` WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE item_register_to_branches.branch_id = '$branch_id' AND id = '$reg_item_id')";
	$run_items = mysqli_query($con, $select_items);
	if(mysqli_num_rows($run_items) == 1)
	{
	    while($row_item = mysqli_fetch_array($run_items))
	    {
    	    $items_id = $row_item['id'];
    	    $purchase = $row_item['purchase'];
    	    $poor = $row_item['poor'];
    	    $member = $row_item['member'];
    	    $general = $row_item['general'];
    	    $category_id = $row_item['category_id'];
	    }
	}
	else
	{
	    $id = 0;
	    $purchase = 0;
	    $poor = 0;
	    $member = 0;
	    $general = 0;
	    $category_id = 0;
	}
	$token_data = $_POST['token_data'];
	$fix_dose = $_POST['fix_dose'];
	$dose = $_POST['dose'];
	$feed = $_POST['feed'];
	$days = $_POST['days'];
	if ($fix_dose == 0) 
	{
	$quantity = $dose * $days * $feed;
	}
	else
	{
			$quantity = $fix_dose;
	}
	$check_item = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `items_by_doctor` WHERE item_id = '$reg_item_id' AND user_id = '$user_id' AND status = '1' "));
	$insert = "INSERT INTO `items_by_doctor`
	(`item_id`,      `dose`,  `feed`,  `days`,  `user_id`,  `branch_id`, `fix_dose`, `created`, `purchase_price`, `sale_price_general`, `sale_price_member`, `sale_price_poor`, `category_id`) VALUES 
	('$reg_item_id', '$dose', '$feed', '$days', '$user_id','$branch_id', '$fix_dose', '$current_date', '$purchase', '$general', '$member', '$poor', '$category_id')";
	if($check_item == 0)
	{	
// 		$get_available_quantity = get_register_item_quantity_from_item_id($reg_item_id);
// 		$new_quantity = $get_available_quantity - $quantity;
		mysqli_query($con, "UPDATE `item_register_to_branches` SET `quantity`= `quantity`-$quantity WHERE id = '$reg_item_id' ");
		if (mysqli_query($con, $insert))		
		{ 
			    if($token_data == 1)
			    {
			?>
	<script type="text/javascript">
			  location.replace("testing_second_turn.php");
			</script>
    	<?php	}
			    else
			    {
			?>
	<script type="text/javascript">
			  location.replace("lab_second_turn.php");
			</script>
    	<?php	}
		}
	}
	else
	{ ?>
	<script type="text/javascript">
	  location.replace("lab_second_turn.php");	
	</script>
<?php }
}else
	{ ?>
	<script type="text/javascript">
	  location.replace("dashboard.php");	
	</script>
<?php }
mysqli_close($con);
?>