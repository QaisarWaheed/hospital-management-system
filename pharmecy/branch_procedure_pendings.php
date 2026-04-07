<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 

if (isset($_GET['add_token']) && $_GET['enter_token'] != '' && $_GET['add_token']) 
{
	$token_no = $_GET['enter_token'];
	$select = "SELECT * FROM tokans WHERE id = '$token_no' AND `tokan_type_id` < 100 ";
	$run = mysqli_query($con, $select);
	if (mysqli_num_rows($run) == 1) 
	{
		while ($row = mysqli_fetch_array($run)) 
		{
			$patient_id = $row['patient_id'];
            $select_patient = "SELECT * FROM patients WHERE id = '$patient_id' ";
            $run_patient = mysqli_query($con, $select_patient);
            if (mysqli_num_rows($run_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($run_patient)) 
                {
                	$phone = $row_patient['phone'];
                	$cnic = $row_patient['cnic'];
                	if ($phone == NULL || $cnic == NULL) 
                	{
                		$error = "NOT VALID TOKEN NO";
                	}
                	else
                	{
                		header('location: second_procedure_turn.php?search_tokan_no='.$token_no);
                	}
                }
            }
		}
	}
	else
	{
	$error = "ENTER A VALID TOKEN NO";
	}
}
?>
	<title>Branch Procedure Pendings - <?php echo $company_trademark; ?></title>
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
					<h3>BRANCHS PROCEDURE</h3>
				</caption>
				<?php
				if (isset($error)) {
					echo '<tr><td colspan="14">'.$error.'</td></tr>';
				 } 
				?>
				<tr>
					<th colspan="6">
						<form method="GET">
							<input type="number" name="enter_token" />
							<input type="submit" name="add_token" class="btn btn-sm btn-primary" />
						</form>
					</th>
					<th colspan="4">
						<form method="GET">
						    <?php
						    ?>
							<input type="text" onchange="this.form.submit()" placeholder = "Search In Pending Records" class = "form-control" name="enter_search_token" />
							<!--<input type="submit" name="search_token" value = "SEARCH" class="btn btn-sm btn-info" />-->
						</form>
					</th>
					<th colspan="5">
						<form method="GET">
<?php 
$select_b = "SELECT * FROM `branchs` WHERE id = '$branch_id' ";
$run_b = mysqli_query($con, $select_b);
if (mysqli_num_rows($run_b) == 1) {
	while ($row_b = mysqli_fetch_array($run_b)) {
		echo '<input readonly input="text" class="form-control" value="'.$row_b['address'].'"/>';
	}
}
?>
						</form>
					</th>
				</tr>
				<tr>
					<th>S #</th>
					<th>Token Date</th>
					<th>Patient Name</th>
					<th>Gardian Name</th>
					<th>Token No</th>
					<th>Token Type</th>
					<th>Procedure Name</th>
					<th>Total Amount</th>
					<th>Received Amount</th>
					<th>Pending Amount</th>
					<th>Pending Recomended BY</th>
					<th>Give Medicines</th>
					<th>Pending Recievings</th>
					<th>Update</th>
				</tr>
			</thead>
			<tbody>
<?php
$date = date('Y-m');
$s = 0;
$pending_received = 0;
if (isset($_GET['enter_search_token']) && $_GET['enter_search_token'] != '') 
{
    $enter_search_token = $_GET['enter_search_token'];
    $select_branch_pending_query = "SELECT branch_pending_details.id, branch_pending_details.token_no, branch_pending_details.gardian_name, branch_pending_details.gardian_phone, branch_pending_details.recommended_by, tokans.cash, tokans.cash_received, item_by_doctor.item_id, items.category_id, items.name AS item_name, patients.name, tokans.created, SUM(branch_pending_receive.amount) AS pending_received, tokan_types.title FROM `branch_pending_details` INNER JOIN tokans ON branch_pending_details.token_no = tokans.id INNER JOIN item_by_doctor ON branch_pending_details.token_no = item_by_doctor.tokan_no INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id LEFT JOIN branch_pending_receive ON tokans.id = branch_pending_receive.token_no INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN tokan_types ON tokans.tokan_type_id = tokan_types.id WHERE branch_pending_details.token_no = $enter_search_token AND branch_pending_details.status = 1 ";
}
$select_branch_pending = mysqli_query($con, $select_branch_pending_query);
if (mysqli_num_rows($select_branch_pending) > 0) 
{
	while ($row_branch_data = mysqli_fetch_array($select_branch_pending) ) 
	{
		$s = $s + 1;
		$pending_received = $row_branch_data['pending_received'];
		$token_type_title = $row_branch_data['title'];
		$token_no = $enter_search_token;
		$patient_name = $row_branch_data['name'];
		$procedure_name = $row_branch_data['item_name'];
		$branch_pending_id = $row_branch_data['id'];
		$category_id = $row_branch_data['category_id'];
		$gardian_name = $row_branch_data['gardian_name'];
		$gardian_phone = $row_branch_data['gardian_phone'];
		$recommended_by = $row_branch_data['recommended_by'];
		$total_amount = $row_branch_data['cash'];
		$recieved_amount = $row_branch_data['cash_received'];
		$created = $row_branch_data['created'];
		$total_received = $recieved_amount+$pending_received;
        $pending_amount = intval($total_amount-($total_received));
        $medicine_limit = intval($total_amount/100*(25));
echo '
<tr>
	<td>'.$s.'</td>
	<td>'.date_format(date_create($created), "d-m-Y").'</td>
	<td>'.$patient_name.'</td>
	<td>'.$gardian_name.'</td>
	<td><a class="btn btn-sm btn-outline-info" href="branch_pending_complete_detail.php?token_no='.$token_no.'"">'.$token_no.'</a></td>
	<td>'.$token_type_title.'</td>
	<td>'.$procedure_name.'</td>
	<td>'.$total_amount.'</td>
	<td>'.$total_received.'</td>
	<td>'.$pending_amount.'</td>	
	<td>'.$recommended_by.'</td>';
	if($category_id == '3' || $category_id == '37' || $category_id == '38')
	{
    	echo '<td>
    	    <div> LIMIT: '.$medicine_limit.'</div>
    		<a class="btn btn-sm btn-outline-primary" href="second_procedure_turn_medicines.php?search_tokan_no='.$token_no.'">Medicines</a>
            	</td>';
	}
	else
	{
	    echo '<td></td>';
	}

	if($pending_amount > 0)
	{
	echo '<td>
		<a class="btn btn-sm btn-outline-info" href="procedure_pending_amount.php?search_tokan_no='.$token_no.'">Pay Amount</a>
	</td>';
	}
	else
	{
	echo '<td>
		<a class="btn btn-sm btn-success" >CLEAR</a>
	</td>';
	}
	if ($branch_pending_id != 0) {
	echo '<td><a href="branch_pending_detail_update.php?u_id='.$branch_pending_id.'">Update</a></td>';
	}
	else
		echo '<td></td>';	
echo '</tr>
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