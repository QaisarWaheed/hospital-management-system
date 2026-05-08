<?php 
require_once __DIR__ . '/../includes/db_connect.php';
if(!empty($_POST["major_id"])){ 
    // Fetch state data based on the specific country 
    $query = "SELECT * FROM item_register_to_branches WHERE branch_id = ".$_POST['major_id']." "; 
    $result = mysqli_query($con, $query); 
    if($result->num_rows > 0)
    { 
        echo '<option value="">Select Item</option>'; 
        while($row = $result->fetch_assoc())
        {  
           $item_id = $row['item_id'];
            $select_item = "SELECT name, category_id FROM items WHERE id = '$item_id' ";
            $run_item = mysqli_query($con, $select_item);
            if (mysqli_num_rows($run_item) > 0) 
            {
                while ($row_item = mysqli_fetch_array($run_item)) 
                {
                    $item_name = $row_item['name'];
                    $category_id = $row_item['category_id'];
                    $select_category = "SELECT name FROM categories WHERE id = '$category_id' ";
                    $run_category = mysqli_query($con, $select_category);
                    if (mysqli_num_rows($run_category) > 0) 
                    {
                        while ($row_category = mysqli_fetch_array($run_category)) 
                        {
                            $category_name = $row_category['name'];
                        }
                    }

                }
            }
                 echo '<option value="'.$row['id'].'">'.$item_name.'('.$category_name.')</option>'; 
        } 
    }else{ 
        echo '<option value="">Medicine not available</option>'; 
    } 
}
elseif(!empty($_POST["subject_id"])){ 
    // Fetch state data based on the specific country 
    $query = "SELECT * FROM item_register_to_branches WHERE id = '".$_POST['subject_id']."' "; 
    $result = mysqli_query($con, $query); 
     
    // Generate HTML of state options list 
    if($result->num_rows > 0){ 
        while($row = $result->fetch_assoc()){  
            echo '
            <label for="book_id"> Available Quantity</label>
            <input readonly type="text" name="available_quantity" class="form-control"  value="'.$row['quantity'].'"/>
            <label> MIN-LIMIT</label>
            <input readonly type="text" name="min_limit" class="form-control"  value="'.$row['min_limit'].'"/>
            <label> MAX-LIMIT</label>
            <input readonly type="text" name="max_limit" class="form-control"  value="'.$row['max_limit'].'"/>            
            <label> Quantity</label>
            <input type="number" name="quantity" class="form-control"/>
            '; 
        } 
    }else{ 
        echo '<option value="">0</option>'; 
    } 
}
elseif(!empty($_POST["book_id"])){ 
    // Fetch city data based on the specific state 
    $query = "SELECT * FROM item_register_to_branches "; 
    $result = mysqli_query($con, $query); 
     
    // Generate HTML of city options list 
    if($result->num_rows > 0){ 
        echo '<option value="">Select Book</option>'; 
        while($row = $result->fetch_assoc()){  
            echo '<option value="'.$row['id'].'">'.$query.'</option>'; 
        } 
    }else{ 
        echo '<option value="">Chapter not available</option>'; 
    } 
} 
?>
