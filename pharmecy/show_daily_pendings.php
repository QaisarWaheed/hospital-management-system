<?php 
include 'includes/connect.php'; 
include 'includes/head.php';
$select_value = $branch_id;
?>
	<title>Show Daily Pendings - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">

		<table class="table">
			<thead>
				<caption style="caption-side: top;text-align: center;">
					<h3>PENDDING DAILY DETAILS</h3>
				</caption>
				<tr>
					<th>S #</th>
					<th>Token No</th>
					<th>Patient Name</th>
					<th>Refference Name</th>
					<th>Phone</th>
					<th>Recomended By</th>
					<th>Return Date</th>
					<th>Amount</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
<?php
$s = 0;
$select_branch_item = mysqli_query($con, "SELECT * FROM `branch_daily_pending_details` WHERE status = '1' ORDER BY `token_no` ");
if (mysqli_num_rows($select_branch_item) > 0) 
{
	while ($row_branch = mysqli_fetch_array($select_branch_item)) 
	{
		$id = $row_branch['id'];
		$token_no = $row_branch['token_no'];
		$recommended_by = $row_branch['recommended_by'];
		$ref_name = $row_branch['ref_name'];
		$ref_phone = $row_branch['ref_phone'];
		$amount = $row_branch['amount'];
		$select_patient = mysqli_query($con, "SELECT * FROM patients WHERE id IN (SELECT patient_id FROM tokans WHERE id = '$token_no') ");
		if (mysqli_num_rows($select_branch_data) > 0) 
		{
			while ($row_branch_data = mysqli_fetch_array($select_branch_data)) 
			{
				$patient_name = $row_branch_data['name'];
			}
		}

					$status = '<span style="color:red;">'.intval($max_limit-$quantity).'</span>';
					echo '
					<tr>
						<td>'.$s.'</td>
						<td>'.$token_no.'</td>
						<td>'.$patient_name.'</td>
						<td>'.$ref_name.'</td>
						<td>'.$ref_phone.'</td>
						<td>'.$recommended_by.'</td>
						<td>'.$amount.'</td>
						<td></td>
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