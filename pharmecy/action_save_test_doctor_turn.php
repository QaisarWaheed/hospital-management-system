<?php include 'includes/connect_doctor_turn.php'; 
if (isset($_GET['save_test'])) 
{
    $search_tokan_no = $_GET['search_tokan_no'];
    $select_medicine_by_doctor_id = $_GET['save_test'];
    $select_data = "SELECT * FROM `select_by_doctor` WHERE `id` = '$select_medicine_by_doctor_id' ";
    $run_data = mysqli_query($con, $select_data);
    if (mysqli_num_rows($run_data) == 1) 
    {
        while ($row_data = mysqli_fetch_array($run_data)) 
        {
            $reg_item_id = $row_data['item_id'];
            $fix_dose = $row_data['fix_dose'];
            $dose = $row_data['dose'];
            $feed = $row_data['feed'];
            $days = $row_data['days'];
            if ($fix_dose == 0) 
            {
            $quantity = $dose * $days * $feed;
            }
            else
            {
                    $quantity = $fix_dose;
            }
        }
    }
    else
    {
        header('location: second_turn_by_doctor.php?error='.$select_medicine_by_doctor_id);
        exit();
    }
    $check_item = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `item_by_doctor` WHERE item_id = '$reg_item_id' AND user_id = '$user_id' AND status = '1' "));
    $check_test = mysqli_num_rows(mysqli_query($con, "SELECT id FROM `items` WHERE category_id = '2' AND `id` IN (SELECT item_id FROM `item_register_to_branches` WHERE id = '$reg_item_id') "));
    $insert = "INSERT INTO `item_by_doctor`
    (`item_id`,      `dose`,  `feed`,  `days`,  `user_id`,  `branch_id`, `fix_dose`, `created`) VALUES 
    ('$reg_item_id', '$dose', '$feed', '$days', '$user_id','$branch_id', '$fix_dose', '$current_date')";
    if($check_item == 0)
    {   
        $get_available_quantity = get_register_item_quantity_from_item_id($reg_item_id);
        $new_quantity = $get_available_quantity - $quantity;
        mysqli_query($con, "UPDATE `item_register_to_branches` SET `quantity`= '$new_quantity' WHERE id = '$reg_item_id' ");
        if (mysqli_query($con, $insert))        { 
            if ($check_test == 1) {
                mysqli_query($con, "INSERT INTO `tests_by_doctor`(`test_id`, `user_id`, `created`) VALUES('$reg_item_id', '$user_id', '$current_date')");
            }
            ?>
    <script type="text/javascript">
              location.replace("testing_second_turn_by_doctor.php?search_tokan_no=<?php echo $search_tokan_no; ?>");
            </script>
    <?php   }
    }
    else
    { ?>
    <script type="text/javascript">
        alert("INFO: Data already addad");
      location.replace("testing_second_turn_by_doctor.php?search_tokan_no=<?php echo $search_tokan_no; ?>");    
    </script>
<?php }
exit(0);
}
else
{
    header('location: dashboard.php');
}
?>