<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
$filter_branch = $branch_id;
$category_idds = 0;
?>
	<title>Show Branch Stock - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">
<?php
if(isset($_POST['category_idds']) && $_POST['category_idds'] != '')
{
        $category_idds = $_POST['category_idds'];
        if(isset($_POST['filter_branch']) && $_POST['filter_branch'] != '')
        {
            $filter_branch = $_POST['filter_branch'];
        }
        else
        {
            $filter_branch = $branch_id;
        }
    if($category_idds > 0)
    {
        $data = "SELECT item_register_to_branches.id, items.name AS item_name, categories.name AS category_name, item_register_to_branches.quantity AS branch_stock, item_register_to_branches.min_limit, item_register_to_branches.max_limit FROM `item_register_to_branches` INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN categories ON items.category_id = categories.id WHERE item_register_to_branches.branch_id = '$filter_branch' AND item_register_to_branches.quantity <= 0 AND items.category_id = '$category_idds' AND item_register_to_branches.status = '1' ";
    }
    else
    {
        $data = "SELECT * FROM `items` WHERE `category_id` = '$category_idds' AND `status` = '1' ";
    }
}
else
{
    $data = "SELECT item_register_to_branches.id, items.name AS item_name, categories.name AS category_name, item_register_to_branches.quantity AS branch_stock, item_register_to_branches.min_limit, item_register_to_branches.max_limit FROM `item_register_to_branches` INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN categories ON items.category_id = categories.id WHERE item_register_to_branches.branch_id = '$filter_branch' AND item_register_to_branches.quantity <= 0 AND item_register_to_branches.status = '1' ";
}
$select = mysqli_query($con, $data);
?>

			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>			
			<div class="col-md-12" style="text-align: center;">
			</div>
		<table class="table table-bordered table-hover">
			<thead>
				<caption style="caption-side: top;text-align: center;">
					<h3>SHOW ITEMS</h3>
				</caption>                      
				<tr>
					<th colspan="2">
					    <a class = "btn btn-success" href = "dashboard.php">Dashboard</a>
    			    </th>
                    <th>
                        <form method = "POST">
                            <input type = "hidden" name = "filter_branch" value = "<?php echo $filter_branch; ?>" />
                        <select class = "form-control bg-light" onchange="this.form.submit()" name="category_idds">
                        <option <?php if($category_idds == 0){echo " SELECTED ";} ?> value = "0">ALL</option>
                        <?php
                            $select_category = "SELECT * FROM categories WHERE id NOT IN (2,3,8, 29) AND status = '1' ORDER BY name ";
                            $run_category = mysqli_query($con, $select_category);
                            if(mysqli_num_rows($run_category) > 0)
                            {
                                while($row_category = mysqli_fetch_array($run_category))
                                {
                                $category_id = $row_category['id'];
                                $category_title = $row_category['name'];
                                if($category_id == $category_idds)
                                {
                                echo '<option SELECTED value = "'.$category_id.'" >'.$category_title.'</option>';
                                }
                            else
                            {
                            echo '<option value = "'.$category_id.'" >'.$category_title.'</option>';
                            }
                            }
                            }
                        ?>
                        </select>
                        </form>
                    </th>
                    <th>
                        <form method = "POST">
                            <input type = "hidden" name = "category_idds" value = "<?php echo $category_idds; ?>" />
                        <select class = "form-control bg-light" onchange="this.form.submit()" name="filter_branch">
                        <?php
                        $query = "SELECT * FROM `branchs` WHERE `status` = '1' ";
                        $run_query = mysqli_query($con, $query);
                        if(mysqli_num_rows($run_query) > 0)
                        {
                            while($row_query = mysqli_fetch_array($run_query))
                            {
                                $filter_branch_id = $row_query['id'];
                                if($filter_branch == $filter_branch_id)
                                {
                                    echo '<option SELECTED value = "'.$row_query['id'].'">'.$row_query['tag_name'].'</option>';
                                }
                                else
                                {
                                    echo '<option value = "'.$row_query['id'].'">'.$row_query['tag_name'].'</option>';
                                }
                            }
                        }
                        else
                        {
                            echo '<option value = "">ADD BRACHES DATA</option>';
                        }
                        ?>
                        </select>
                        </form>
                    </th>
                </tr>
				<tr>
					<th>S #</th>
					<th>Item Name</th>
					<th>Category</th>
					<th>Stock</th>
					<th>Min</th>
					<th>Max</th>
				</tr>
			</thead>
			<tbody>
<?php
$s = 0;
if (mysqli_num_rows($select) > 0) 
{
	while ($row = mysqli_fetch_array($select)) 
	{
		$s = $s + 1;
		$item_id = (int) $row['id'];
echo '
<tr>
	<td id = "id'.$item_id.'">'.$s.'</td>
	<td>'.$row['item_name'].'</td>
	<td>'.$row['category_name'].'</td>
	<td>'.$row['branch_stock'].'</td>
	<td>'.$row['min_limit'].'</td>
	<td>'.$row['max_limit'].'</td>
</tr>
';
	}
}
mysqli_close($con);
?>
			</tbody>
		</table>

	</div>
</div>
</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>