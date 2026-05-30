<?php
include 'includes/connect_doctor_turn.php';
include_once 'includes/rehab_fingerprint.php';
$amount_array = get_select_amount_array();
$select_item = "SELECT * FROM `items_by_doctor` WHERE `branch_id` = '$branch_id' AND `user_id` = '$user_id' AND `status` = '1' ";
$run_select_item = mysqli_query($con, $select_item);
$count_item = mysqli_num_rows($run_select_item);
if (isset($_POST['save']) && $_POST['save'] != '') 
{
if($count_item >= 1)
{
	$tokan_type = $_POST['tokan_payment'];
    if($tokan_type == 101)
    {
        $cash = $amount_array['2'];
    }
    elseif($tokan_type == 102)
    {
        $cash = $amount_array['0'];
    }
    elseif($tokan_type == 103)
    {
        $cash = $amount_array['1'];
    }
    else
    {
        $cash = $amount_array['2'];
    }
    
	if (isset($_POST['previous_tokan_no'])) 
	{
		$previous_tokan_no = $_POST['previous_tokan_no'];
		$patient_id = $_POST['patient_id'];
		if (is_rehabilitation_branch($branch_id) && !rehab_fingerprint_verify_if_probe_provided($con, $patient_id, $_POST['fp_thumb_verify'] ?? '')) {
			echo '<script>alert("Fingerprint verification failed. Scan a registered thumb or leave verification blank to continue."); history.back();</script>';
			exit(0);
		}
		$doctor_id = $_POST['doctor_id'];
		$cash_received = $_POST['cash_received'];
		$insert = "INSERT INTO `tokans`
		(`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`,`cash_received`, `user_id`, `previous_tokan_no`, `created`, `branch_id`) 
		VALUES 
		(NULL, '$patient_id','$doctor_id', '$tokan_type', '$cash', '$cash_received', '$user_id', '$previous_tokan_no', '$current_date', '$branch_id')";
	}
	else
	{
		$name = $_POST['name'];
		$age = $_POST['age'];
		$phone = $_POST['phone'];
		$gender = $_POST['gender'];
		$fp_left = trim($_POST['fp_thumb_left'] ?? '');
		$fp_right = trim($_POST['fp_thumb_right'] ?? '');
		$cash_received = $_POST['cash_received'];
		$doctor_id = $_POST['doctor_id'];
			$run2 = mysqli_query($con, "INSERT INTO `patients`
			(`id`, `name` ,`age` , `gender`, `created`, `phone`) 
			VALUES 
			(NULL , '$name', '$age', '$gender', '$current_date', '$phone')");
			    $patient_id = mysqli_insert_id($con);
		$insert = "INSERT INTO `tokans`
		(`id`, `patient_id`, `doctor_id`, `tokan_type_id`, `cash`,`cash_received`, `user_id`, `previous_tokan_no`, `created`, `branch_id`) 
		VALUES 
		(NULL, '$patient_id','$doctor_id', '$tokan_type', '$cash', '$cash_received', '$user_id', NULL, '$current_date', '$branch_id')";

	}
			if (mysqli_query($con, $insert)) 
			{
			    $tokan_no = mysqli_insert_id($con);
				if (is_rehabilitation_branch($branch_id) && !isset($_POST['previous_tokan_no'])) {
					rehab_fingerprint_save_if_provided($con, $patient_id, $fp_left, $fp_right);
				}
				if ($cash > $cash_received-1) 
				{
					pharmecy_insert_branch_pending_details($con, $tokan_no, $current_date, $branch_id, '2');
				}

				while ($row_select_item = mysqli_fetch_array($run_select_item)) 
				{
            	    $del_record_id = $row_select_item['id'];
                	    $purchase = $row_select_item['purchase_price'];
                	    $poor = $row_select_item['sale_price_poor'];
                	    $member = $row_select_item['sale_price_member'];
                	    $general = $row_select_item['sale_price_general'];
                	    $category_id = $row_select_item['category_id'];					
    	            $reg_item_id = $row_select_item['item_id'];
					$dose = $row_select_item['dose'];
					$feed = $row_select_item['feed'];
					$days = $row_select_item['days'];
					$fix_dose = $row_select_item['fix_dose'];
                	if ($fix_dose == 0) 
                	{
                	    $quantity = $dose * $days * $feed;
                	}
                	else
                	{
            			$quantity = $fix_dose;
                	}	
                	$sale_price = 0;
                	$sale_quantity = $quantity;
                	if($tokan_type == 102)
                	{
                	    $sale_price = $poor*$sale_quantity;
                	}
                	elseif($tokan_type == 103)
                	{
                	    $sale_price = $member*$sale_quantity;
                	}
                	else
                	{
                	    $sale_price = $general*$sale_quantity;
                	}
					mysqli_query($con, "INSERT INTO `item_by_doctor`
					(`tokan_no`,`item_id`, `dose`,  `feed`,  `days`,  `user_id`,  `branch_id`, `fix_dose`, `created`, `doctor_id`, `status`, `purchase_price`, `sale_price_general`, `sale_price_member`, `sale_price_poor`, `category_id`, `tokan_type_id`, `sale_price`, `sale_quantity`) 
					VALUES 
					('$tokan_no','$reg_item_id', '$dose', '$feed', '$days', '$user_id','$branch_id', '$fix_dose', '$current_date', '$doctor_id', '2', '$purchase', '$general', '$member', '$poor', '$category_id', '$tokan_type', '$sale_price', '$sale_quantity')");
    				mysqli_query($con, "DELETE FROM `items_by_doctor` WHERE id = '$del_record_id' AND user_id = '$user_id' ");
				}
				// mysqli_query($con, "DELETE FROM `items_by_doctor` WHERE branch_id = '$branch_id' AND user_id = '$user_id' ");
				header('Location: print_medicine_slip.php?tokan_no=' . (int) $tokan_no);
				exit;
			}
			echo '<script>alert("Token could not be saved. Please try again.");history.back();</script>';
			exit;
}
else
{
	echo '<script>alert("Add at least one medicine or test before saving the token.");history.back();</script>';
	exit;
}
}
mysqli_close($con);