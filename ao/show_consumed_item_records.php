<?php
include 'includes/connect.php';
if(isset($_POST['branch_item_id']) && $_POST['show_consumed_data'] != '')
{
    $branch_item_id = $_POST['branch_item_id'];
    $updated_at = $_POST['updated_at'];
    $select_consume = "SELECT SUM(`sale_quantity`) FROM `item_by_doctor` WHERE `item_id` = '$branch_item_id' AND `created` > '$updated_at' ";
    $run_consume = mysqli_query($con, $select_consume);
    if(mysqli_num_rows($run_consume) == 1)
    {
        while($row_consume = mysqli_fetch_array($run_consume))
        {
            $consumed_quantity = $row_consume['0'];
            if (is_null($consumed_quantity)) 
            {
                $consumed_quantity =  0;
            }
        }
    }
}
else
{
    
}
echo $consumed_quantity;
?>