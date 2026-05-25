<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; ?>
	<title>Print Summary - <?php echo $company_trademark; ?></title>
<style>
body
{
    text-transform: uppercase;
}
</style>
</head>

<body onload="window.print()">
<?php
if (isset($_POST['s']) && $_POST['s'] != '') {
	$from_date = $_POST['s'];
	$to_date = $_POST['e'];
	$user_id = $_POST['u'];
	$user_name = $_POST['un'];
	//echo print_summary($from_date, $to_date, $user_id, $user_name); 
}
elseif (isset($_GET['s']) && $_GET['s'] != '') {
	$from_date = $_GET['s'];
	$to_date = $_GET['e'];
	$user_id = $_GET['u'];
	$user_name = $_GET['un'];
	//echo print_summary($from_date, $to_date, $user_id, $user_name);
}

?>
<div style="text-align: center;">
	<h6><?php echo $branch_name; ?></h6>
	<h6><?php echo $branch_address; ?></h6>
	<h5>Token Summary</h5>	
	<div>
		<div style="float: left;">
			
				<th>Date</th>
				<th style="text-align: left;"><?php echo date_format(date_create($from_date), 'd-m-Y'); ?> To <?php echo date_format(date_create($to_date), 'd-m-Y'); ?></th><br>
				<th>User Name:</th>
				<th style="text-align: left;"><?php echo $user_name; ?></th>
		</div>
		<div style="float: right;">
				Print Time: <?php echo date('h:i:s A'); ?><br>
				Print Date:<?php echo date('d-m-Y'); ?>
		</div>
	</div>
</div>
<table class="table" style="font-size: 10px">
	<caption style="caption-side: top;text-align: center;">

		<table class="table">
			<tr>
			</tr>
			<tr>
			</tr>
</table>
		
	</caption>
	<thead>
		<tr>
			<th>S #</th>
			<th>Date</th>
			<th>Tokan</th>
			<th>Patient</th>
			<th>Age</th>
			<th>Gender</th>
			<th>Total Amount</th>
			<th>Type</th>
			<th>Received Amount</th>
		</tr>
	</thead>
	<tbody>
<?php 
$last_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date)));
$s = 0;
$total_cash = 0;
$total_cash_received = 0;
$select = "SELECT * FROM tokans WHERE 
	user_id = '$user_id' AND 
	`created` <= '$last_date' AND 
	`created` >= '$from_date' AND
	`status` = '1' 
	ORDER BY `created` ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) 
{
	while ($row = mysqli_fetch_array($run)) 
	{
		$s = $s + 1;
		$token_date = $row['created'];
		$total_cash = $total_cash + $row['cash'];
		$total_cash_received = $total_cash_received + $row['cash_received'];
		$patient_id = $row['patient_id'];
		$select_patient = "SELECT * FROM patients WHERE id = '$patient_id' ";
		$run_patient = mysqli_query($con, $select_patient);
		if (mysqli_num_rows($run_patient) > 0) 
		{
			while ($row_patient = mysqli_fetch_array($run_patient)) 
			{
				$name = $row_patient['name'];
				$age = $row_patient['age'];
				$gender = $row_patient['gender'];
				if($gender == 1){$genders = 'Female';}
				elseif($gender == 2){$genders = 'Male';}
				else{$genders = 'Other';}
			}
		}
		else
		{
				$name = "No Name";
				$age = 0;
				$genders = 'Other';
		}
		$tokan_type_id = $row['tokan_type_id'];
		$select_tokan_type = "SELECT * FROM tokan_types WHERE id = '$tokan_type_id' ";
		$run_tokan_type = mysqli_query($con, $select_tokan_type);
		if (mysqli_num_rows($run_tokan_type) > 0) 
		{
			while ($row_tokan_type = mysqli_fetch_array($run_tokan_type)) 
			{
				$title = $row_tokan_type['title'];
			}
		}
		else
		{
				$title = "No Title";
		}
		echo '
		<tr>
			<td>'.$s.'</td>
			<td>'.date_format(date_create($token_date), "d-m-y").'</td>
			<td style="text-align: right;">'.$row['id'].'</td>
			<td>'.$name.'</td>
			<td style="text-align: right;">'.$age.'</td>
			<td>'.$genders.'</td>
			<td style="text-align: right;">'.$row['cash'].'</td>
			<td>'.$title.'</td>
			<td style="text-align: right;">'.$row['cash_received'].'</td>
		</tr>
		';
	}
}
?>
<tr style="text-align: right;">
	<th colspan="5"></th>
	<th colspan="2"><?php echo $total_cash; ?></th>
	<th colspan="2"><?php echo $total_cash_received; ?></th>
</tr>
<?php
$select = "SELECT distinct tokan_type_id ,cash_received FROM tokans WHERE 
	user_id = '$user_id' AND 
	`created` <= '$last_date' AND 
	`created` >= '$from_date' AND tokan_type_id < 100
	ORDER BY `tokan_type_id` ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) 
{
	while ($row = mysqli_fetch_array($run)) 
	{
		$tokan_type_id = $row['tokan_type_id'];
			$select_count = "SELECT * FROM tokans WHERE 
				user_id = '$user_id' AND 
				`created` <= '$last_date' AND 
				`created` >= '$from_date' AND tokan_type_id = '$tokan_type_id' ";
			$count_tokens = mysqli_num_rows(mysqli_query($con, $select_count));
		$select_tokan_type = "SELECT * FROM tokan_types WHERE id = '$tokan_type_id' ";
		$run_tokan_type = mysqli_query($con, $select_tokan_type);
		if (mysqli_num_rows($run_tokan_type) > 0) 
		{
			while ($row_tokan_type = mysqli_fetch_array($run_tokan_type)) 
			{
				$title = $row_tokan_type['title'];
			}
		}
		else
		{
				$title = "No Title";
		}		echo '<tr>
			<th style="text-align: right;" colspan="3">'.$title.'</th>
			<th style="text-align: center;" colspan="2">'.$count_tokens.'</th>
			<th style="text-align: left;" colspan="4">'.($count_tokens * $row['cash_received']).'</th>
		</tr>';
	}
}
?>
	</tbody>
</table>

</body>
</html>
<!-- <script type="text/javascript">
        window.onfocus = function () { window.close();}
</script> -->

