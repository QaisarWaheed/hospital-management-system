<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 

if (isset($_POST['receive_quantity'])) 
{
	$issue_lab_item_record_id = $_POST['issue_lab_item_record_id'];
	$receive_quantity = $_POST['receive_quantity'];
	$select = "SELECT * FROM `issue_lab_item_records` WHERE `issue_lab_item_record_id` = '$issue_lab_item_record_id' AND `issue_lab_item_record_quantity` = '$receive_quantity' AND `issue_lab_item_record_status` = '1' ";
	$select_branch_item = mysqli_query($con, $select);
	if (mysqli_num_rows($select_branch_item) == 1) 
	{
		while ($row_branch_item = mysqli_fetch_array($select_branch_item)) 
		{
			$reg_branch_item_id = $row_branch_item['reg_branch_item_id'];
			$branch_item_id = $row_branch_item['branch_item_id'];
			$item_id = $row_branch_item['item_id'];
			$update_branch = "UPDATE `item_register_to_branches` SET `quantity` = `quantity`+'$receive_quantity', `updated_by` = '$lab_user_id', `updated_at` = '$current_date' WHERE `id` = '$reg_branch_item_id' ";
     		$update_issuance_record = "UPDATE `issue_lab_item_records` SET `issue_lab_item_record_status` = '2', `issue_lab_item_record_updated_at` = '$current_date', `issue_lab_item_record_updated_by` = '$lab_user_id' WHERE `issue_lab_item_record_id` = '$issue_lab_item_record_id' AND `issue_lab_item_record_status` = '1' AND `issue_lab_item_record_quantity` = '$receive_quantity' ";
			mysqli_query($con, $update_branch);
			mysqli_query($con, $update_issuance_record);
		}
    	header('location: item_receive_lab.php');
	}
	else
	{
		$update_issuance_record = "UPDATE `issue_lab_item_records` SET `issue_lab_item_record_attempt` = issue_lab_item_record_attempt+1, `issue_lab_item_record_updated_at` = '$current_date', `issue_lab_item_record_updated_by` = '$lab_user_id' WHERE `issue_lab_item_record_id` = '$issue_lab_item_record_id' AND `issue_lab_item_record_status` = '1' ";
		mysqli_query($con, $update_issuance_record);
	?>
	<script>
		alert('Not Receive Medicine');
		location.replace("item_receive_lab.php");
	</script>
	<?php
	}
	exit(0);
}
?>
	<title>Receive Items - <?php echo $company_trademark; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
    .background_image{
        background-image: url('../images/background.png');
        background-size: cover;
    }
    </style>    
    <style>
        @media print {
            body {
                /* Reduce the base font size for the entire page to 12px */
                font-size: 12px; 
            }

            table {
                font-size: 0.8em; 
            }
        }
    </style>
</head>

<body class="background_image">
<?php include "top_navigation.php"; ?>
<div class = "row">
<div class = "col-md-12">
	<div class="" style="margin: 10px 15px;">
		<table class="table table-hover table-bordered">
			<caption style="caption-side: top;text-align: center;color: black;">
				<h2>PENDING ITEMS </h2>
			</caption>
			<thead>
				<tr>
					<th>S #</th>
					<th>Issue #</th>
					<th>Id</th>
					<th>Date</th>
					<th>Item Name</th>
					<th>Branch Name</th>
					<th>Issued By</th>
					<th>Input</th>
				</tr>
			</thead>
			<tbody></tbody>
<?php 
$s = 0;
$select = "SELECT issue_lab_item_records.issue_lab_item_record_id, issue_lab_item_records.issue_lab_item_record_date, issue_lab_item_records.issue_lab_item_id , issue_lab_item_records.issue_lab_item_record_quantity, items.name AS item_name, categories.name AS cat_name, `reg_branch_item_id`, branchs.tag_name, users.u_name FROM `issue_lab_item_records` INNER JOIN items ON branch_item_id = items.id INNER JOIN categories ON items.category_id = categories.id INNER JOIN issue_lab_items ON issue_lab_item_records.issue_lab_item_id = issue_lab_items.issue_lab_item_id INNER JOIN branchs ON issue_lab_items.branch_id = branchs.id INNER JOIN users ON `issue_lab_item_record_created_by` = users.id WHERE `issue_lab_items`.`branch_id` = '$lab_login_branch_id' AND `issue_lab_item_record_status` = '1' ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) {
	while ($row = mysqli_fetch_array($run)) {
		$s = $s + 1;
		$issue_lab_item_record_id = $row['issue_lab_item_record_id'];
		$issue_lab_item_id = $row['issue_lab_item_id'];
		$issue_lab_item_record_date = $row['issue_lab_item_record_date'];
		$issue_lab_item_record_quantity = $row['issue_lab_item_record_quantity'];
		echo '
			<tr>
				<td>'.$s.'</td>
				<td>'.$issue_lab_item_id.'</td>
				<td>'.$issue_lab_item_record_id.'</td>
                <td>'.date_format(date_create($issue_lab_item_record_date), "d-M-Y").'</td>
				<td>'.$row['item_name'].'</td>
				<td>'.$row['tag_name'].'</td>
				<td>'.$row['u_name'].'</td>
				<td>
					<form method="POST">
						<input type="hidden" name="issue_lab_item_record_id" value="'.$issue_lab_item_record_id.'" />
						<input onchange="this.form.submit()" type="number" name="receive_quantity" style="max-width: 100px;">
					</form>
				</td>
			</tr>
		';
	}
}
?>
		</table>
		<table class="table table-hover table-bordered">
			<caption style="caption-side: top;text-align: center;color: black;">
				<h2>CURRENT MONTH RECEIVED ITEMS </h2>
			</caption>
			<thead>
				<tr>
					<th>S NO</th>
					<th>Issue Id</th>
					<th>Issue Date</th>
					<th>Item Name</th>
					<th>Receive Date</th>
					<th>Branch</th>
					<th>Quantity</th>
					<th>Attempts</th>
					<th>Lab Admin</th>
					<th>Lab Staff</th>
				</tr>
			</thead>
			<tbody>
<?php 
$s = 0;
$receiving_date = date("Y-m");
$select = "SELECT issue_lab_item_records.issue_lab_item_record_id, issue_lab_item_record_attempt, issue_lab_item_record_updated_at, issue_lab_item_records.issue_lab_item_record_date, issue_lab_item_records.issue_lab_item_id , issue_lab_item_records.issue_lab_item_record_quantity, items.name AS item_name, categories.name AS cat_name, `reg_branch_item_id`, branchs.tag_name, lab_admins.u_name AS lab_admin, lab_staffs.u_name AS lab_staff FROM `issue_lab_item_records` INNER JOIN items ON branch_item_id = items.id INNER JOIN categories ON items.category_id = categories.id INNER JOIN issue_lab_items ON issue_lab_item_records.issue_lab_item_id = issue_lab_items.issue_lab_item_id INNER JOIN branchs ON issue_lab_items.branch_id = branchs.id INNER JOIN users lab_admins ON `issue_lab_item_record_created_by` = lab_admins.id INNER JOIN users lab_staffs ON issue_lab_item_records.issue_lab_item_record_updated_by = lab_staffs.id WHERE `issue_lab_items`.`branch_id` = '$lab_login_branch_id' AND issue_lab_item_record_updated_at LIKE '$receiving_date%' AND `issue_lab_item_record_status` = '2' ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) {
	while ($row = mysqli_fetch_array($run)) {
		$s = $s + 1;
		$issue_lab_item_record_id = $row['issue_lab_item_record_id'];
		$issue_lab_item_id = $row['issue_lab_item_id'];
		$issue_lab_item_record_date = $row['issue_lab_item_record_date'];
		$issue_lab_item_record_updated_at = $row['issue_lab_item_record_updated_at'];
		$issue_lab_item_record_quantity = $row['issue_lab_item_record_quantity'];
		echo '
			<tr>
				<td>'.$s.'</td>
				<td>'.$issue_lab_item_id.'</td>
                <td>'.date_format(date_create($issue_lab_item_record_date), "d-M-Y").'</td>
				<td>'.$row['item_name'].'</td>
                <td>'.date_format(date_create($issue_lab_item_record_updated_at), "d-M-Y").'</td>
				<td>'.$row['tag_name'].'</td>
				<td>'.$row['issue_lab_item_record_quantity'].'</td>
				<td>'.$row['issue_lab_item_record_attempt']+'1'.'</td>
				<td>'.$row['lab_admin'].'</td>
				<td>'.$row['lab_staff'].'</td>
			</tr>
		';
	}
}
?>      </tbody>
	</table>
	</div>
</div>
</div>
<?php 
mysqli_close($con);
?>    
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>