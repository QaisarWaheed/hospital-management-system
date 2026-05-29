<?php 
include 'includes/connect.php'; 

if (isset($_POST['receive_quantity'])) 
{
	$id = $_POST['id'];
	$receive_quantity = $_POST['receive_quantity'];
	$branch_item_id = $_POST['branch_item_id'];
	$select = "SELECT * FROM item_register_branchs_by_sm WHERE id = '$id' ";
	$select_branch_item = mysqli_query($con, $select);
	if (mysqli_num_rows($select_branch_item) == 1) 
	{
		while ($row_branch_item = mysqli_fetch_array($select_branch_item)) 
		{
			$item_reciving_status = $row_branch_item['status'];
			$quantity = $row_branch_item['quantity']+$row_branch_item['difference'];
		}
	}
	if ($receive_quantity == $quantity) 
	{
		if($item_reciving_status == 1)
		{		
    		$date = date('Y-m-d');
		    mysqli_query($con, "UPDATE item_register_branchs_by_sm SET `ba_id` = '$user_id', `receiving_date` = '$date', `status` = '2' WHERE id = '$id' AND `status` = '1' ");
		    mysqli_query($con, "UPDATE item_register_to_branches SET `quantity` = quantity+$receive_quantity WHERE id = '$branch_item_id' ");		    
		}
	}
	else
	{		
		mysqli_query($con, "UPDATE item_register_branchs_by_sm SET `attempts` = `attempts`+1 WHERE branch_item_id = '$branch_item_id' AND status = '1' ");
		header('Location: item_receive_branch.php?msg=not-received');
		exit;
	}
	header('Location: item_receive_branch.php?msg=received');
	exit;
}

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
		<table class="table table-hover table-bordered">
			<caption style="caption-side: top;text-align: center;">
				<h1>ALL USERS LIST</h1>
				<h2>PENDING ITEMS </h2>
			</caption>
			<thead>
				<tr>
					<th>S NO</th>
					<th>Issue</th>
					<th>Item Name</th>
					<th>Branch Name</th>
					<th>Quantity</th>
					<th>Input</th>
					<th>SM</th>
				</tr>
			</thead>
			<tbody></tbody>
<?php 
$s = 0;
$select = "SELECT * FROM `item_register_branchs_by_sm` WHERE branch_id = '$branch_id' AND status = '1' ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) {
	while ($row = mysqli_fetch_array($run)) {
		$s = $s + 1;
		$id = $row['id'];
		$branch_item_id = $row['branch_item_id'];
		$issue_id = $row['issue_id'];
		$quantity = $row['quantity'];
		echo '
			<tr>
				<td>'.$s.'</td>
				<td>'.$issue_id.'</td>
				<td>'.get_item_name_by_register_item_id($row['branch_item_id']).'</td>
				<td>'.get_branch_name_by($row['branch_id']).'</td>
				<td></td>
				<td>
					<form method="POST">
						<input type="hidden" name="id" value="'.$id.'" />
						<input type="hidden" name="branch_item_id" value="'.$branch_item_id.'" />
						<input onchange="this.form.submit()" type="number" name="receive_quantity" style="max-width: 100px;">
					</form>
				</td>
				<td>'.get_uname_by_id($row['sm_id']).'</th>
			</tr>
		';
	}
}
?>
		</table>
		<table class="table table-hover table-bordered">
			<caption style="caption-side: top;text-align: center;">
				<h2>TODAY RECEIVED ITEMS </h2>
			</caption>
			<thead>
				<tr>
					<th>S NO</th>
					<th>Issue Id</th>
					<th>Issue Date</th>
					<th>Item Name</th>
					<th>Receive Date</th>
					<th>Branch Name</th>
					<th>Quantity</th>
					<th>Attempts</th>
					<th>SM</th>
				</tr>
			</thead>
			<tbody></tbody>
<?php 
$s = 0;
$receiving_date = date("Y-m-d");
$select = "SELECT * FROM `item_register_branchs_by_sm` WHERE status = '2' AND `receiving_date` = '$receiving_date' AND branch_id = '$branch_id' ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) {
	while ($row = mysqli_fetch_array($run)) {
		$s = $s + 1;
		$id = $row['id'];
		$branch_item_id = $row['branch_item_id'];
		$issue_id = $row['issue_id'];
		$quantity = $row['quantity'];
		echo '
			<tr>
				<td>'.$s.'</td>
				<td>'.$issue_id.'</td>
				<td>'.date_format(date_create($row['created']), "d-m-Y").'</td>
				<td>'.get_item_name_by_register_item_id($row['branch_item_id']).'</td>
				<td>'.date_format(date_create($row['receiving_date']), "d-m-Y").'</td>
				<td>'.get_branch_name_by($row['branch_id']).'</td>
				<td>'.$quantity.'</td>
				<td>'.$row['attempts'].'</td>
				<td>'.get_uname_by_id($row['sm_id']).'</th>
			</tr>
		';
	}
}
mysqli_close($con);
?>
		</table>
	</div>
</div>
</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>