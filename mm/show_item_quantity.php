<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
if (isset($_GET['category_idds']) && $_GET['category_idds'] != ''&& $_GET['category_idds'] != '0') 
{
    $select_value = $_GET['category_idds'];
    if($branch_id == '9' || $branch_id == '10')
    {
        $select = mysqli_query($con, "SELECT * FROM items WHERE category_id = '$select_value' AND id IN (SELECT item_id FROM item_register_to_branches WHERE branch_id = '$branch_id') ");
    }
    else
    {
        $select = mysqli_query($con, "SELECT * FROM items WHERE category_id = '$select_value' ");    
    }
}
else
{
    $select_value = 0;
    if($branch_id == '9' || $branch_id == '10')
    {
        $select = mysqli_query($con, "SELECT * FROM items WHERE id IN (SELECT item_id FROM item_register_to_branches WHERE branch_id = '$branch_id') ORDER BY `name` ");
    }
    else
    {
        $select = mysqli_query($con, "SELECT * FROM items ORDER BY `name` ");
    }
}
?>
	<title>Show Store Stock - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>
			
		<table class="table table-bordered">
			<thead>
				<caption style="caption-side: top;text-align: center;">
					<h3>SHOW ITEMS</h3>
				</caption>
				<tr class="select_category">
					<th colspan="2">
						<form method="GET">
							<select onchange="this.form.submit()" class="form-control" name="category_idds">
								<option value="0">All</option>
								<?php 
								echo show_category_options($select_value);
								?>
							</select>
						</form>
					</th>
					<th colspan="5"></th>
				</tr>
				<tr>
					<th>S #</th>
					<th>Item Name</th>
					<th>Category</th>
					<th>Store</th>
					<th>Branch</th>
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
		$br_quantity = 0;
		$item_id = $row['id'];
		$select_br = "SELECT `quantity` FROM `item_register_to_branches` WHERE `item_id` = '$item_id' AND branch_id = '$branch_id' ";
		$run_br = mysqli_query($con, $select_br);
		if(mysqli_num_rows($run_br) > 0)
		{
		    while($row_br = mysqli_fetch_array($run_br))
		    {  
		        $br_quantity = $row_br['quantity'];
		    }
		}
		else
		{
		    $br_quantity = 'N/A';
		}
		$name = $row['name'];
		$quantity = $row['quantity'];
		$category_id = $row['category_id'];
		$categories = mysqli_query($con, "SELECT name FROM categories WHERE id = '$category_id' ");
		while ($row_category = mysqli_fetch_array($categories)) 
		{
			$cat_name = $row_category['name'];
		}
		$min_limit = $row['min_limit'];
		$max_limit = $row['max_limit'];
		if($quantity > $max_limit){$status = '<span style="color:green;">Over Stock</span>';}
		elseif($quantity <= $min_limit){$status = '<span style="color:red;">'.intval($max_limit-$quantity).'</span>';}
		else{$status = "OK";}
echo '
<tr>
	<td>'.$s.'</td>
	<td>'.$name.'</td>
	<td>'.$cat_name.'</td>
	<td>'.$quantity.'</td>
	<td>'.$br_quantity.'</td>
</tr>
';

	}
}
?>
			</tbody>
		</table>

	</div>
</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<!-- 
 -->