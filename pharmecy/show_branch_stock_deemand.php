<?php 
include 'includes/connect.php'; 
include 'includes/head.php';
$select_value = $branch_id;
?>
	<title>Show Branch Stock - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">
        <div class = "col-12 bg-light p-1">
            <?php include "navigation_top.php"; ?>
        </div>

		<table class="table">
			<thead>
				<caption style="caption-side: top;text-align: center;">
					<h3>BRANCHS STOCK DEEMAND</h3>
				</caption>
				<tr>
					<th colspan="2"></th>
					<th colspan="3">
						<form method="GET">
							<select onchange="this.form.submit()" class="form-control" name="branch_idds">
								<option value="0">All</option>
								<?php 
								echo show_branch_options($select_value);
								?>
							</select>
						</form>
					</th>
				</tr>
				<tr>
					<th>S #</th>
					<th>Item Name</th>
					<th>Branch Name</th>
					<th>Category</th>
					<th>Demand</th>
				</tr>
			</thead>
			<tbody>
<?php
$s = 0;
$select_branch_item = mysqli_query($con, "SELECT * FROM `item_register_to_branches` WHERE branch_id = '$branch_id' AND item_id IN (SELECT id FROM items WHERE category_id NOT IN (2,3,8,20,28)) ");
if (mysqli_num_rows($select_branch_item) > 0) 
{
	while ($row_branch = mysqli_fetch_array($select_branch_item)) 
	{
		$branch_idd = $row_branch['branch_id'];
		$quantity = $row_branch['quantity'];
		$min_limit = $row_branch['min_limit'];
		$max_limit = $row_branch['max_limit'];
		$select_branch_data = mysqli_query($con, "SELECT * FROM branchs WHERE id = '$branch_idd' ");
		if (mysqli_num_rows($select_branch_data) > 0) 
		{
			while ($row_branch_data = mysqli_fetch_array($select_branch_data)) 
			{
				$branch_names = $row_branch_data['address'];
			}
		}
		$item_id = $row_branch['item_id'];
		$select = mysqli_query($con, "SELECT * FROM items WHERE id = '$item_id' ");
		if (mysqli_num_rows($select) > 0) 
		{
			while ($row = mysqli_fetch_array($select)) 
			{
				$s = $s + 1;
				$name = $row['name'];
				$category_id = $row['category_id'];
				$categories = mysqli_query($con, "SELECT name FROM categories WHERE id = '$category_id' ");
				while ($row_category = mysqli_fetch_array($categories)) 
				{
					$cat_name = $row_category['name'];
				}
				if($quantity <= $min_limit)
				{
					$status = '<span style="color:red;">'.intval($max_limit-$quantity).'</span>';
					echo '
					<tr>
						<td>'.$s.'</td>
						<td>'.$name.'</td>
						<td>'.$branch_names.'</td>
						<td>'.$cat_name.'</td>
						<td>'.$status.'</td>
					</tr>
					';
				}
			}
		}

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