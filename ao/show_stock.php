<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php';
if (isset($_GET['category_idds']) && $_GET['category_idds'] != ''&& $_GET['category_idds'] != '0') 
{
$select_value = $_GET['category_idds'];
$select = mysqli_query($con, "SELECT items.name as medicine_name, item_register_to_branches.quantity AS branch_quantity, categories.name AS category_name FROM `item_register_to_branches` INNER JOIN items ON items.id = item_register_to_branches.item_id INNER JOIN categories ON categories.id = items.category_id WHERE items.status = 1 AND items.category_id = '$select_value' AND item_register_to_branches.branch_id = '$branch_id' ORDER BY items.name  ");
}
else
{
$select_value = 0;
$select = mysqli_query($con, "SELECT items.name as medicine_name, item_register_to_branches.quantity AS branch_quantity, categories.name AS category_name FROM `item_register_to_branches` INNER JOIN items ON items.id = item_register_to_branches.item_id INNER JOIN categories ON categories.id = items.category_id WHERE item_register_to_branches.branch_id = '$branch_id' ORDER BY items.name ");
}
?>
	<title>Show Stock - <?php echo $company_trademark; ?></title>
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

<body style = "background-color: whitesmoke;">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO (<?php echo get_branch_name_by($branch_id); ?>)</h1></label>
	</div>
	<div class="col-md-12">
		<?php include 'top_row.php'; ?>
	</div>
	<div class = "col-md-12">
		<table class="capitalize table table-bordered table-hover">
			<thead>
				<caption style="caption-side: top;text-align: center;text-transform: capitalize;">
					<h3>SHOW ITEMS</h3>
					
					<div id="hidden_div" style="display: none;">I was hidden until the right password was entered.</div>
				</caption>
				<tr class="select_category capitalize">
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
				<tr style = "text-transform: capitalize;">
					<th>S #</th>
					<th>Item Name</th>
					<th>Category</th>
					<th>Quantity</th>
			</thead>
			<tbody>
<?php
$s = 0;
if (mysqli_num_rows($select) > 0) 
{
	while ($row = mysqli_fetch_array($select)) 
	{
		$s = $s + 1;
echo '
<tr style = "text-transform: capitalize;">
	<td>'.$s.'</td>
	<td>'.$row['medicine_name'].'</td>
	<td>'.$row['category_name'].'</td>
	<td>'.$row['branch_quantity'].'</td>

</tr>
';
	}
}
?>
			</tbody>
		</table>	    
	</div>
</div>

</body>
</html>
<?php mysqli_close($con); ?>