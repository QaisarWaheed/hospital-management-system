<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
if (isset($_GET['category_idds']) && $_GET['category_idds'] != ''&& $_GET['category_idds'] != '0') 
{
$select_value = $_GET['category_idds'];
$select = mysqli_query($con, "SELECT * FROM items WHERE status = 1 AND category_id = '$select_value' ");
}
else
{
$select_value = 0;
$select = mysqli_query($con, "SELECT * FROM items WHERE status = 1 ORDER BY `name` ");
}
?>
	<title>Show Store Stock - <?php echo $company_trademark; ?></title>
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

<body class="background_image_ycdo" style = "dispaly: none;">

<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>
			
<?php

?>
		<table class="table table-bordered table-hover">
			<thead>
				<caption style="caption-side: top;text-align: center;">
					<h3>SHOW ITEMS</h3>
				</caption>
				<tr class="select_category">
					<th colspan="5"></th>
					<th colspan="4">
						<form method="GET">
							<select onchange="this.form.submit()" class="form-control" name="category_idds">
								<option value="0">All</option>
								<?php 
								echo show_category_options($select_value);
								?>
							</select>
						</form>
					</th>
				</tr>
				<tr>
					<th>S #</th>
					<th>Item Name</th>
					<th>Category</th>
					<th>Retail</th>
					<th>Purchase</th>
					<th>Poor</th>
					<th>Member</th>
					<TH>GENERAL</TH>
					<th class = "noprint">Action</th>
			</thead>
			<tbody>
<?php
$s = 0;
if (mysqli_num_rows($select) > 0) 
{
	while ($row = mysqli_fetch_array($select)) 
	{
		$s = $s + 1;
		$id = $row['id'];
		$purchase = get_purchase_by_item_id($id);
		$retail = $row['retail'];
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

// 	<td>'.$purchase.'</td>
echo '
<tr>
	<td>'.$s.'</td>
	<td>'.$name.'</td>
	<td>'.$cat_name.'</td>
	<td>'.$row['retail'].'</td>
	<td>'.$row['purchase'].'</td>
	<td>'.$row['poor'].'</td>
	<td>'.$row['member'].'</td>
	<td>'.$row['general'].'</td>
	<td class = "noprint"><a href="update_item_purchase.php?up='.$id.'" class="btn btn-success btn-sm">Update</a>
	</td>
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