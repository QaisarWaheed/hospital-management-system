<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
if(!isset($_SESSION['mm_id']))
{
    header('location: logout.php');
}

if(isset($_POST['update_item_limits']) && $_POST['update_item_limits'])
{
    $item_id = $_POST['item_id'];
    $category_id = $_POST['category_id'];
    $min_limit = $_POST['min_limit'];
    $max_limit = $_POST['max_limit'];
    $update = "UPDATE `items` SET `min_limit` = '$min_limit', `max_limit` = '$max_limit', `limit_updated_at` = '$current_date', `limit_updated_by` = '$user_id' WHERE id = '$item_id' ";
    if(mysqli_query($con, $update))
    {
        header('location: update_item_store_limits.php?msg=success&category_id='.$category_id.'&#'.$item_id);
    }
}

if(isset($_GET['category_id']) && $_GET['category_id'] != '')
{
    $category_id = $_GET['category_id'];
    $select = "SELECT items.id, items.name, categories.name AS cat_name, items.min_limit, items.max_limit FROM `items` INNER JOIN categories ON items.category_id = categories.id WHERE items.status = '1' AND items.category_id = '$category_id' ";
}

?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	    <table class = "table table-hover table-sm">
	        <thead>
	            <tr>
	                <th>S #</th>
	                <th>ITEM NAME</th>
	                <th>CATEGORY
	                    <form method = "GET">
    	                    <select onchange = "this.form.submit()" name = "category_id" class = "form-control" required>
    	                        <option value = "">SELECT CATEGORY</option>
    	                        <?php
    	                        $categories = "SELECT * FROM `categories` WHERE `status` = '1' ORDER BY `categories`.`name` ASC ";
    	                        $run_category = mysqli_query($con, $categories);
    	                        if(mysqli_num_rows($run_category) > 0)
    	                        {
    	                            while($row_category = mysqli_fetch_array($run_category))
    	                            {
    	                                if($category_id == $row_category['id'])
    	                                {
        	                                echo '<option SELECTED value = "'.$row_category['id'].'">'.$row_category['name'].'</option>';
    	                                }
    	                                else
    	                                {
        	                                echo '<option value = "'.$row_category['id'].'">'.$row_category['name'].'</option>';
    	                                }
    	                            }
    	                        }
    	                        ?>
    	                    </select>
	                    </form>
	                </th>
	                <th>MIN-LIMIT</th>
	                <th>MAX-LIMIT</th>
	                <th>ACTION</th>
	            </tr>
	        </thead>
	        <tbody>
	            <?php
	            $s = 0;
	            $run = mysqli_query($con, $select);
	            if(mysqli_num_rows($run) > 0)
	            {
	                while($row = mysqli_fetch_array($run))
	                {
	                    $s++; ?>
	            <form method = "POST">
                <tr id = "<?php echo $row['id']; ?>">
                    <td><?php echo $s; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['cat_name']; ?></td>
                    <td>
                        <input type = "hidden" value = "<?php echo $row['id']; ?>" name = "item_id" />
                        <input type = "hidden" value = "<?php echo $category_id; ?>" name = "category_id" />
                        <input required type = "number" value = "<?php echo $row['min_limit']; ?>" name = "min_limit" class = "form-control" />
                    </td>
                    <td><input required type = "number" value = "<?php echo $row['max_limit']; ?>" name = "max_limit" class = "form-control" /></td>
                    <td><input type = "submit" value = "UPDATE" name = "update_item_limits" class = "btn btn-sm btn-success" /></td>
                </tr>
                </form>
	                <?php }
	            }
	            ?>
	        </tbody>
	    </table>
	</div>
</div>

</body>
</html>