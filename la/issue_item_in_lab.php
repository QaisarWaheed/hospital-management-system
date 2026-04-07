<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
include 'includes/head.php'; 

$select_last_lab_issue_no = "SELECT `issue_lab_item_id`, branch_id, issue_lab_item_date FROM `issue_lab_items` ORDER BY `issue_lab_items`.`issue_lab_item_id` DESC LIMIT 0,1 ";
$run_last_lab_issue_no = mysqli_query($con, $select_last_lab_issue_no);
if(mysqli_num_rows($run_last_lab_issue_no) == 1)
{
    while($row_last_lab_issue_no = mysqli_fetch_array($run_last_lab_issue_no))
    {
        $last_lab_issue_no = $row_last_lab_issue_no['issue_lab_item_id'];
        $last_lab_issue_branch = $row_last_lab_issue_no['branch_id'];
        $last_lab_issue_date = $row_last_lab_issue_no['issue_lab_item_date'];
    }
}
else
{
    $last_lab_issue_no = 0;
}

if(isset($_POST['save_issue_lab_item']))
{
    $last_lab_issue_date_added = date('Y-m-d');
    $branch_id = $_POST['branch_id'];
    $insert = "INSERT INTO `issue_lab_items`(`issue_lab_item_id`, `issue_lab_item_date`, `branch_id`, `issue_lab_item_created_by`, `issue_lab_item_created_at`, `issue_lab_item_status`) VALUES (NULL, '$last_lab_issue_date_added', '$branch_id', '$lab_admin_user_id', '$current_date', '1')";
    if(mysqli_query($con, $insert))
    {
        header('location: issue_item_in_lab.php?msg=success');
    }
    else
    {
        header('location: issue_item_in_lab.php?msg=error');
    }
    exit(0);
}
elseif(isset($_POST['save_issue_item_in_branch']))
{
    // print_r($_POST);
    $issue_lab_item_record_date = date('Y-m-d');
    $issue_lab_item_record_quantity = $_POST['issue_lab_item_record_quantity'];
    $last_lab_issue_branch = $_POST['last_lab_issue_branch'];
    $issue_lab_item_id = $_POST['lab_item_issue_no'];
    $branch_item_id = $_POST['branch_item_id'];
    $item_id = $_POST['item_id'];
    if($item_id == 0)
    {
        $item_id = $branch_item_id;       
    }
    $reg_branch_item_id = reg_branch_item_id($last_lab_issue_branch, $item_id);
    $select_item_quantity = "SELECT * FROM `items` WHERE `id` = '$branch_item_id' AND `quantity`-'$issue_lab_item_record_quantity' >= 0 ";
    if(mysqli_num_rows(mysqli_query($con, $select_item_quantity)) == 1)
    {
        $select = "SELECT * FROM `issue_lab_item_records` WHERE `issue_lab_item_id` = '$issue_lab_item_id' AND `item_id` = '$item_id' ";
        if(mysqli_num_rows(mysqli_query($con, $select)) == 0)
        {
            echo 'ITEM ALREADY ISSUE IN THIS ISSSUANCE. <br>PLEASE SELECT AN OTHER ITEM';
            echo '<br><a href = "dashboard.php" class = "btn btn-info btn-sm">goto dashboard</a> - ';
            echo '<a href = "issue_item_in_lab.php" class = "btn btn-success btn-sm">goto issuance portal</a>';
            $insert = "INSERT INTO `issue_lab_item_records`(`issue_lab_item_record_id`, `issue_lab_item_record_date`, `issue_lab_item_id`, `branch_item_id`, `issue_lab_item_record_quantity`, `issue_lab_item_record_status`, `issue_lab_item_record_created_by`, `issue_lab_item_record_created_at`, `item_id`, `reg_branch_item_id`) 
            VALUES  
            (NULL, '$issue_lab_item_record_date', '$issue_lab_item_id', '$item_id', '$issue_lab_item_record_quantity', '1', '$lab_admin_user_id', '$current_date', '$branch_item_id', '$reg_branch_item_id')";
            if(mysqli_query($con, $insert))
            {
                $update_lab_store = "UPDATE `items` SET `quantity` = `quantity` - '$issue_lab_item_record_quantity', `updated_at` = '$current_date', `updated_by` = '$lab_admin_user_id' WHERE `id` = '$branch_item_id' ";
                mysqli_query($con, $update_lab_store);
                header('location: issue_item_in_lab.php?msg=success');
            }
            else
            {
                echo 'STOCK NOT AVAILABLE. <br>PLEASE PURCHASE ITEM';
                header('location: issue_item_in_lab.php?msg=error');
            } 
        }        
        else
        {
            header('location: issue_item_in_lab.php?msg=duplicate-error');
        } 
    }       
    else
    {
        header('location: issue_item_in_lab.php?msg=stoke-error');
    } 
    echo 'STOCK NOT AVAILABLE. <br>PLEASE PURCHASE ITEM';
            echo '<br><a href = "dashboard.php" class = "btn btn-info btn-sm">goto dashboard</a> - ';
            echo '<a href = "issue_item_in_lab.php" class = "btn btn-success btn-sm">goto issuance portal</a>';
    exit(0);
}
// elseif(isset($_POST['branch_item_id']) && $_POST['branch_item_id'] != '')
// {
//     print_r($_POST);
// }
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
	<title>Lab ISSUE STOCK IN BRANCHES - <?php echo $company_trademark; ?></title>
<script>
$(document).ready(function() {
  $('select').selectize({
    sortField: 'text'
  });
});    
</script>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke nodisplay_print">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
        <form method = "POST">
	    <div class = "row">
	        <div class = "col">
	            <label for = "last_lab_issue_date">ISSUE DATE</label>
	            <input type = "date" readonly value = "<?php echo date('Y-m-d'); ?>" class = "form-control" name = "" id = ""/>
	            <input type = "hidden" value = "<?php echo $last_lab_issue_no+1; ?>" max = "<?php echo $last_lab_issue_no+1; ?>" min = "<?php echo $last_lab_issue_no+1; ?>" class = "form-control" name = "last_lab_issue_date" id = "last_lab_issue_date"/>
	        </div> 
	        <div class = "col">
	            <label for = "last_lab_issue_id">BRANCH ISSUE #</label>
	            <input type = "number" readonly value = "<?php echo $last_lab_issue_no+1; ?>" max = "<?php echo $last_lab_issue_no+1; ?>" min = "<?php echo $last_lab_issue_no+1; ?>" class = "form-control" name = "" id = ""/>
	            <input type = "hidden" value = "<?php echo $last_lab_issue_no+1; ?>" max = "<?php echo $last_lab_issue_no+1; ?>" min = "<?php echo $last_lab_issue_no+1; ?>" class = "form-control" name = "last_lab_issue_id" id = "last_lab_issue_id"/>
	        </div> 
	        <div class = "col">
	            <label>SELECT BRANCH</label>
                <select name = "branch_id" placeholder="Pick a Branch..." required class = "form-control form-control-sm">
                    <?php
                    $select = "SELECT * FROM `branchs` WHERE `status` = '1'  ";
                    $run = mysqli_query($con, $select);
                    if(mysqli_num_rows($run) > 0)
                    {
                        while($row = mysqli_fetch_array($run))
                        {
                            echo '<option value="'.$row['id'].'">'.$row['tag_name'].'</option>';
                        }
                    }
                    ?>
                </select>
	        </div>
	        <div class = "col">
	            <label>ACTOIN #</label><br>
	            <div class = "">
    	            <input type = "submit" class = "btn-sm btn btn-info" name = "save_issue_lab_item" id = "save_issue_lab_item"/>
    	            <input type = "reset" class = "btn-sm btn btn-warning" name = "save_issue_lab_item_reset" id = "save_issue_lab_item_reset"/>
	            </div>
	        </div>
	    </div>
        </form>
	    <table class = "table table-bordered">
	        <caption style = "caption-side: top; color: black;text-align: center;">
	          <h2> ISSUE STOCK IN BRANCHES</h2>
	        </caption>
	        <thead>
	            <form method = "POST">
	            <tr>
	                <th></th>
	                <th>
	                    <input value = "<?php echo $last_lab_issue_date; ?>" type = "date" readonly size = "2" name = "" id = "" class = "form-control"/>
	                    <input value = "<?php echo $last_lab_issue_date; ?>" type = "hidden" size = "2" name = "last_lab_issue_date" id = "" class = "form-control"/>
	                    <input value = "<?php echo $last_lab_issue_branch; ?>" type = "hidden" size = "2" name = "last_lab_issue_branch" id = "" class = "form-control"/>
	                </th>
	                <th>
	                    <input value = "<?php echo $last_lab_issue_no; ?>" type = "number" readonly size = "3" class = "form-control"/>
	                    <input value = "<?php echo $last_lab_issue_no; ?>" type = "hidden" size = "3" name = "lab_item_issue_no" id = "" class = "form-control"/>
	                </th>
    	            <th>
	                    <select onchange = "this.form.submit()" placeholder="Pick a Item..." required class = "form-control" name = "branch_item_id">
                            <?php
                            if(isset($_POST['branch_item_id']) && $_POST['branch_item_id'] != '')
                            {
                                $select = "SELECT items.id, items.name AS item_name, categories.id AS cat_id, categories.name AS cat_name, items.quantity AS item_quantity, item_register_to_branches.branch_id FROM `items` INNER JOIN item_register_to_branches ON items.id = item_register_to_branches.item_id INNER JOIN categories ON items.category_id = categories.id WHERE items.id = '".$_POST['branch_item_id']."' AND item_register_to_branches.branch_id = '$last_lab_issue_branch' ";
                            }
                            else
                            {                                
                                $select = "SELECT items.id, items.name AS item_name, categories.id AS cat_id, categories.name AS cat_name, items.quantity AS item_quantity, item_register_to_branches.branch_id FROM `items` INNER JOIN item_register_to_branches ON items.id = item_register_to_branches.item_id INNER JOIN categories ON items.category_id = categories.id WHERE items.quantity > 0 AND items.status = '1' AND items.category_id IN (2, 7, 28, 43) AND item_register_to_branches.branch_id = '$last_lab_issue_branch' ORDER BY `item_name` ASC  ";
                            echo '<option value="">Select a Item...</option>';
                            }
                            $run = mysqli_query($con, $select);
                            if(mysqli_num_rows($run) > 0)
                            {
                                while($row = mysqli_fetch_array($run))
                                {
                                    $select_item_id = $row['id'];
                                    $select_item_name = $row['item_name'];
                                    $select_item_cat_name = $row['cat_name'];
                                    $select_item_cat_id = $row['cat_id'];
                                    echo '<option value="'.$row['id'].'">'.$row['item_name'].' ('.$row['cat_name'].')</option>';
                                }
                            }
                            ?>
	                    </select>
	                </th>
    	            <th>
                            <?php
                            if(isset($_POST['branch_item_id']) && $_POST['branch_item_id'] != '')
                            {
    	                        echo '<select placeholder="Pick a Item..." required class = "form-control" name = "item_id">';
    	                        if($select_item_cat_id != '28')
    	                        {
                                    echo '<option selected value="'.$select_item_id.'">'.$select_item_cat_id.' -> '.$select_item_name.' ('.$select_item_cat_name.')</option>';
    	                        }
                                $select_data = "SELECT items.id, items.name AS item_name, categories.name AS cat_name, items.quantity AS item_quantity, item_register_to_branches.branch_id FROM `items` INNER JOIN item_register_to_branches ON items.id = item_register_to_branches.item_id INNER JOIN categories ON items.category_id = categories.id WHERE items.status = '1' AND items.category_id IN (2) AND item_register_to_branches.branch_id = '$last_lab_issue_branch' ORDER BY `item_name` ASC  ";
                                $run_data = mysqli_query($con, $select_data);
                                if(mysqli_num_rows($run_data) > 0)
                                {
                                    while($row_data = mysqli_fetch_array($run_data))
                                    {
                                        echo '<option value="'.$row_data['id'].'">'.$row_data['item_name'].' ('.$row_data['cat_name'].')</option>';
                                    }
                                }
	                        echo '</select>';
                            }
                            ?>
	                </th>
	                <th>
	                    <input type = "number" name = "issue_lab_item_record_quantity" id = "issue_lab_item_record_quantity" class = "form-control"/>
	                </th>
	                <th>
	                    <select name = "branch_id" placeholder="Pick a Branch..." required class = "form-control">
                            <?php
                            $select = "SELECT * FROM `branchs` WHERE `id` = '$last_lab_issue_branch' ";
                            $run = mysqli_query($con, $select);
                            if(mysqli_num_rows($run) > 0)
                            {
                                while($row = mysqli_fetch_array($run))
                                {
                                    echo '<option value="'.$row['id'].'">'.$row['tag_name'].'</option>';
                                }
                            }
                            ?>
	                    </select>
	                </th>
	                <th>
	                    <input type = "submit" name = "save_issue_item_in_branch" id = "save_issue_item_in_branch" class = "btn btn-success btn-sm"/>
	                </th>
	            </tr>
	            </form>
	            <tr>
	                <th>SR #</th>
	                <th>DATE</th>
	                <th>ISSUE #</th>
	                <th>ITEM NAME</th>
	                <th>CATEGORY</th>
	                <th>QUANTITY</th>
	                <th>BRANCH</th>
	                <th>LAB ADMIN</th>
	            </tr>
	        </thead>
	        <tbody>
	            <?php
	            $s = 0;
	            $select = "SELECT issue_lab_item_records.issue_lab_item_record_id, issue_lab_item_records.issue_lab_item_record_date, issue_lab_item_records.issue_lab_item_id , issue_lab_item_records.issue_lab_item_record_quantity, items.name AS item_name, categories.name AS cat_name, `reg_branch_item_id`, branchs.tag_name, users.u_name FROM `issue_lab_item_records` INNER JOIN items ON branch_item_id = items.id INNER JOIN categories ON items.category_id = categories.id INNER JOIN issue_lab_items ON issue_lab_item_records.issue_lab_item_id = issue_lab_items.issue_lab_item_id INNER JOIN branchs ON issue_lab_items.branch_id = branchs.id INNER JOIN users ON `issue_lab_item_record_created_by` = users.id WHERE `issue_lab_item_record_status` = '1' ";
	            $run = mysqli_query($con, $select);
	            if(mysqli_num_rows($run) > 0)
	            {
	                while($row = mysqli_fetch_array($run))
	                {
	                    $s++;   ?>
	            <tr>
	                <td><?php echo $s; ?></td>
	                <td><?php echo date_format(date_create($row['issue_lab_item_record_date']), "d-M-Y"); ?></td>
	                <td><?php echo $row['issue_lab_item_id']; ?></td>
	                <td><?php echo $row['item_name']; ?></td>
	                <td><?php echo $row['cat_name']; ?></td>
	                <td><?php echo $row['issue_lab_item_record_quantity']; ?></td>
	                <td><?php echo $row['tag_name']; ?></td>
	                <td><?php echo $row['u_name']; ?></td>
	            </tr>
	                <?php }
	            }
	            ?>
	        </tbody>
	    </table>
	</div>
</div>

</body>
</html>