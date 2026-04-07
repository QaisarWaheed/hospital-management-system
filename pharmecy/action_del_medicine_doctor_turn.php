<?php
include 'includes/connect_second_turn.php'; 
if (isset($_GET['del_medicine']) && $_GET['del_medicine'] != '' & $_GET['search_tokan_no'] != '') 
{
    $del_id = $_GET['del_medicine'];
    $search_tokan_no = $_GET['search_tokan_no'];
	$delete = "DELETE FROM items_by_doctor WHERE id = '$del_id' AND user_id = '$user_id'  ";
		$reg_item_id = get_branch_item_id_from_items_by_doctor_id($del_id);
		$quantity = get_item_quantity_from_item_by_docotor_id($del_id);
		$get_available_quantity = get_register_item_quantity_from_item_id($reg_item_id);
		$new_quantity = $get_available_quantity + $quantity;
		$update = "UPDATE `item_register_to_branches` SET `quantity`= quantity+$quantity WHERE id = '$reg_item_id' ";
	if (mysqli_query($con, $delete)) 
	{
		mysqli_query($con, $update);
        header('location: second_turn_by_doctor.php?search_tokan_no='.$search_tokan_no);
        exit(0);
	}
}
else
{
    header('location: dashboard.php');
}
?>