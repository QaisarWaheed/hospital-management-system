<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) 
{
	$branch_item_id = $_POST['item_id'];
	$br_id = $_POST['br_id'];
	$issue_id = $_POST['issue_id'];
	$date_1 = "SELECT * FROM `item_register_branchs_by_sm` WHERE `issue_id` = '$issue_id' ";
	$check_1_issue_no = mysqli_num_rows(mysqli_query($con, $date_1));
	if($check_1_issue_no == 0)
	{
        	$quantity = $_POST['quantity'];
        	$pack_size = $_POST['pack_size'];
        	$available_quantity_in_store = available_items_in_store_by_register_item($branch_item_id);
        	$update_item_id = get_items_id_store_by_register_item($branch_item_id);
        	$insert = "INSERT INTO `item_register_branchs_by_sm`
        	(`branch_item_id`, `branch_id`, `quantity`, `sm_id`, `issue_id`, `created`, `pack_size`) 
        	VALUES 
        	('$branch_item_id', '$br_id', '$quantity','$user_id', '$issue_id', '$current_date', '$pack_size')";
        	if ($available_quantity_in_store >= $quantity) 
        	{
                 $run = mysqli_query($con, $insert);
                if ($run) 
                {	
                    $new_quantity = $available_quantity_in_store - $quantity;
                    mysqli_query($con, "UPDATE items SET quantity = '$new_quantity' WHERE id = '$update_item_id' ");
                ?>
                    <script>
                    alert('DATA SAVE SUCCESSFULLY');
                    location.replace("item_register_branch.php?br_id=<?php echo $br_id; ?>&i_id=<?php echo $issue_id; ?>");
                    </script>
                    <?php
                }
                else
                {
                    echo $con->error;
                }
         	}
         	else
         	{
         			?>
             		<script>
             			alert('STOCK IS NOT AVAILABLE.');
         				location.replace("item_register_branch.php");
             		</script>
         		<?php
        	}	    
	} 
	else
 	{
    	$date = "SELECT * FROM `item_register_branchs_by_sm` WHERE `issue_id` = '$issue_id' AND branch_id = '$br_id' ";
    	$check_2_issue_no = mysqli_num_rows(mysqli_query($con, $date));
    	if($check_2_issue_no != 0)
    	{

        	$quantity = $_POST['quantity'];
        	$available_quantity_in_store = available_items_in_store_by_register_item($branch_item_id);
        	$update_item_id = get_items_id_store_by_register_item($branch_item_id);
        	$insert = "INSERT INTO `item_register_branchs_by_sm`
        	(`branch_item_id`, `branch_id`, `quantity`, `sm_id`, `issue_id`, `created`) 
        	VALUES 
        	('$branch_item_id', '$br_id', '$quantity','$user_id', '$issue_id', '$current_date')";
        	if ($available_quantity_in_store >= $quantity) 
        	{
                 $run = mysqli_query($con, $insert);
                if ($run) 
                {	
                    $new_quantity = $available_quantity_in_store - $quantity;
                    mysqli_query($con, "UPDATE items SET quantity = '$new_quantity' WHERE id = '$update_item_id' ");
                ?>
                    <script>
                    alert('DATA SAVE SUCCESSFULLY');
                    location.replace("item_register_branch.php?br_id=<?php echo $br_id; ?>&i_id=<?php echo $issue_id; ?>");
                    </script>
                    <?php
                }
                else
                {
                    echo $con->error;
                }
         	}
         	else
         	{
         			?>
             		<script>
             			alert('STOCK IS NOT AVAILABLE.');
         				location.replace("item_register_branch.php");
             		</script>
         		<?php
        	}    	
    	    
    	}     	
    	else
     	{
     			?>
         		<script>
         			alert('HURRY! NOT ALLOWED.');
     				location.replace("item_register_branch.php");
         		</script>
     		<?php
     		exit(0);
    	}	    
 			?>
     		<script>
     			alert('HURRY! ISSUE NO IS ADDED ALREADY.');
 				location.replace("item_register_branch.php");
     		</script>
 		<?php
 		exit(0);
	}
// echo $insert;
// exit(0);
}
$current_issue_no = mysqli_num_rows(mysqli_query($con, "SELECT DISTINCT issue_id FROM `item_register_branchs_by_sm`"));
?>
<?php include 'includes/head.php'; ?>
	<title>Add Item - <?php echo $company_trademark; ?></title>
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

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

		<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
				<label><h1>Add Item To Branch Stock Form</h1></label>
			</div>
			<div class="col-md-12 ">
<form class="noprint">
<?php
if(isset($_GET['br_id']) && $_GET['br_id'] != '')
{
    $br_id = $_GET['br_id'];
    $query_major = "SELECT * FROM branchs WHERE id = '$br_id' AND status = 1"; 
    $result_major = mysqli_query($con, $query_major); 
}
else
{
    $query_major = "SELECT * FROM branchs WHERE status = 1 ORDER BY address ASC"; 
    $result_major = mysqli_query($con, $query_major); 
}
?>
    <label for="br_id"> SELECT BRANCH</label>
    <select id="branch" name="br_id" required class="form-control" onchange="this.form.submit()">
        <?php if(!isset($_GET['br_id']))
        {
            echo '<option value="">Select Item</option>';
        }?>    <?php 
    if($result_major->num_rows > 0)
    { 
        while($row_major = $result_major->fetch_assoc())
        {  
            echo '<option value="'.$row_major['id'].'">'.$row_major['address'].'</option>'; 
        } 
    }else{ 
            echo '<option value="">item not available in branch</option>'; 
    } 
?>
    </select>
</form>
<form method="POST" style="" action = "item_register_branch.php">

  <div class="form-group noprint">
     
    <label for="issue_id"> ISSUE NO</label>
<?php
if(isset($_GET['i_id']) && $_GET['i_id'] != '')
{
    $issue_id = $_GET['i_id'];
    echo '<input required type="number" min = "'.intval($current_issue_no-15).'" max = "'.intval($current_issue_no+1).'" name="issue_id" value = "'.$issue_id.'" class="form-control" />';
}
else
{
    echo '<input required type="number" min = "'.intval($current_issue_no-15).'" max = "'.intval($current_issue_no+1).'" name="issue_id" value = "'.$current_issue_no.'" class="form-control" />';
}
?>
    
<?php
if(isset($_GET['br_id']) && $_GET['br_id'] != '')
{
    $issue_id = $_GET['i_id'];
    $select_item = "SELECT * FROM items WHERE category_id NOT IN (2 ,3 ,7 ,8, 28) AND status = '1' AND id IN (SELECT item_id FROM item_register_to_branches WHERE branch_id = '$br_id')ORDER BY `items`.`name` ASC ";
    $run_item = mysqli_query($con, $select_item);
    if(mysqli_num_rows($run_item) > 0)
    {
        echo '
            <input required type="hidden" name="br_id" value="'.$br_id.'" />
            <label for="item_id"> SELECT Item</label>
            <select id="item_id" name="item_id" required class="form-control">
            ';
        while($row_item = mysqli_fetch_array($run_item))
        {
            $item_id = $row_item['id'];
            $category_id = $row_item['category_id'];
            $item_name = $row_item['name'];
            $category_name = show_category_name($category_id);
                $select_br_item = "SELECT id FROM item_register_to_branches WHERE item_id = '$item_id' AND branch_id = '$br_id' ";
                $run_br_item = mysqli_query($con, $select_br_item);
                if(mysqli_num_rows($run_br_item) > 0)
                {
                    while($row_br_item = mysqli_fetch_array($run_br_item))
                    {
                        $br_item_id = $row_br_item['id'];
                    }
                }
 
        echo '
                <option value="'.$br_item_id.'">'.$item_name.' ('.$category_name.')'.$br_item_id.'</option>
            ';            
        }
        echo '</select>';
    }
    else
    {
        echo '
            <label for="item_id"> SELECT Item</label>
            <select id="item" name="item_id" required class="form-control">
                <option value="">Select branch first</option>
            </select>
            ';
        
    }
}
else
{
    echo '
        <label for="item_id"> SELECT Item</label>
        <select id="item" name="item_id" required class="form-control">
            <option value="">Select branch first</option>
        </select>
        ';
}
?>
    <label for="quantity"> Quantity</label>
    <input required type="number" name="quantity" class="form-control" min = "0" />
            
    <label for="pack_size"> PACK SIZE / NO of TEST IN ONE BOX</label>
    <input required type="number" name="pack_size" class="form-control" min = "0" />
            
    <div style="margin-top: 30px;">
      <button type="submit" onclick="myDisplayGoneAdd()" id="add" name="save" class="btn btn-primary">Submit</button>  
    </div>
  
</form>
			</div>

			<div class="col-md-12" style="text-align: center;">
				<label><h1 style = "text-decoration: underline;">Item Issue LIST</h1></label>
				<div class = "table">
				    <table class = "table" border = "1">
				        <thead>
				            <tr class = "noprint">
				                <form method = "GET">
				                    <td colspan = "5"><input required type = "number" min = "1" value = "<?php echo ($_GET['bill_no']>0 ? $_GET['bill_no'] : $current_purchase_no); ?>" max = "<?php echo $current_purchase_no; ?>" name = "bill_no" class = "form-control" /></td>
				                    <td colspan = "2"><input type = "submit" value = "SEARCH" class = "btn btn-sm btn-info" /></td>
				                </form>
				            </tr>
				            <tr style = "font-size: 16px;">
				                <th>Sr #</th>
				                <th>Item Name </th>
				                <th>Category </th>
				                <th>Pack Size</th>
				                <th>QTY</th>
				                <th>Status</th>
				                <th class = "noprint">Action</th>
				            </tr>
				        </thead>
				        <tboby>
<?php
$s = 0;
$total_amount = 0;
if(isset($_GET['bill_no']) && $_GET['bill_no'] != '')
{
    $bill_no = $_GET['bill_no'];
    $select = "SELECT * FROM `item_register_branchs_by_sm` WHERE issue_id = '$bill_no' AND status != 3 ";
}
else
{
    $select = "SELECT * FROM `item_register_branchs_by_sm` WHERE issue_id = '$current_issue_no' AND status != 3 ";
    
}
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $id = $row['id'];
        $ba_id = $row['ba_id'];
        $status = $row['status'];
        if($status == 1){$status_msg = "Issued";}else{$status_msg = "RECEIVED";}
        $quantity = $row['quantity'];
        $pack_size = $row['pack_size'];
        $difference = $row['difference'];
        $branch_item_id = $row['branch_item_id'];
        $branch_id = $row['branch_id'];
        $created = $row['created'];
        $item_name = get_item_name_by_register_item_id($branch_item_id);
        $category_name = show_category_name_by_register_branch_id($branch_item_id);
        echo '				            
        <tr  style = "font-size: 15px;">
            <td style = "text-align: center;">'.$s.'</td>
            <td style = "text-align: left">'.$item_name.'</td>
            <td style = "text-align: center">'.$category_name.'</td>
            <td style = "text-align: center">'.$pack_size.'</td>
            <td style = "text-align: right;">'.intval($quantity+$difference).'</td>
            <td>
                '.$status_msg.'('.$ba_id.')
            </td>';
        if($status == 1)
        {
        echo '<td class = "noprint"><a href="update_item_register_branch.php?up='.$id.'" class="btn btn-success btn-sm">Update</a></td>';
        }
        else
        {
        echo '<td class = "noprint">OK</td>';
        }
        echo '</tr>
';

                // <a href="add_party.php?u_id='.$id.'" class = "btn btn-sm btn-primary">update</a>
    }
}
    $tag_name = get_branch_name_by($branch_id);
    $receiver_name = get_uname_by_issue_id($bill_no);
?>
				        </tboby>
    <caption style = "caption-side: top;">
        <div style = "font-size:15px;">BRANCH NAME: <?php echo $tag_name; ?></div>
        <div style = "font-size:15px;">Issue No:  <?php echo $bill_no; ?></div>
        <div style = "font-size:15px;">Issue Date: <?php echo date_format(date_create($created), "d-m-Y"); ?></div>
        <div style = "font-size:15px;">RECEIVED BY:  <?php echo $receiver_name; ?></div>
    </caption>    
				        
				    </table>
				</div>
			</div>


		</div>

	</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script>
function myDisplayGoneAdd() {
  document.getElementById("add").style.display = "none";
}
</script> 
