<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
include 'includes/head.php'; 
?>
	<title>Receive Items - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div class = "row">
        <div class = "col-12 bg-light p-1">
            <?php include "navigation_top.php"; ?>
        </div>
<div class = "col-12">
	<div class="" style="margin: 10px 15px;">
		<table class="table table-hover table-bordered" style = "color: black;">
			<caption style="caption-side: top;text-align: center;color: black;">
				<h2> RECEIVED ITEMS </h2>
			</caption>
			<thead>
				<tr>
					<th>S NO</th>
					<th>Item Id</th>
					<th>Item Name</th>
					<th>Issue Quantity</th>
					<th>Available Quantity</th>
				</tr>
			</thead>
			<tbody>
<?php 
$s = 0;
$select = "SELECT item_register_to_branches.id AS register_item_id, item_register_to_branches.quantity, items.name, items.id AS item_id FROM `item_register_to_branches` INNER JOIN items ON item_register_to_branches.item_id = items.id WHERE `branch_id` = '$lab_login_branch_id' AND item_id IN (SELECT id FROM items WHERE items.category_id = '28') ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) {
	while ($row = mysqli_fetch_array($run)) 
	{
		$s = $s + 1;
		$register_item_id = $row['register_item_id'];
		echo '
			<tr>
				<td style = "text-align: center;">'.$s.'</td>
				<td style = "text-align: center;">'.$row['item_id'].'</td>
				<td>'.$row['name'].'</td>
				<td style = "text-align: right;">'.$row['quantity'].'</td>
				<td></td>
			</tr>
		';
	}
}
?>          </tbody>
		</table>
	</div>
</div>
</div>


</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>