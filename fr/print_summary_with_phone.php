<?php
include 'includes/connect.php';

if (isset($_POST['s']) && $_POST['s'] != '') {
	$from_date = $_POST['s'];
	$to_date = $_POST['e'];
	$u_id = $_POST['u'];
	$br_id = $_POST['br_id'];
	$u_name = $_POST['un'];
} elseif (isset($_GET['s']) && $_GET['s'] != '') {
	$from_date = $_GET['s'];
	$to_date = $_GET['e'];
	$u_id = $_GET['u'];
	$br_id = $_GET['br_id'];
	$u_name = $_GET['un'];
} else {
	http_response_code(400);
	exit('Date range is required.');
}
?>
<?php include 'includes/head.php'; ?>
	<title>Print Summary - <?php echo $company_trademark; ?></title>
<style>
*{
    font-size: 16px;
    text-transform: uppercase;
}
</style>
</head>

<body>

<table class="table" style="font-size: 10px">

	<thead>
	<tr style="caption-side: top;text-align: center;">
	    <td colspan="9">
	    <?php echo $branch_name; ?>
    	<h6><?php echo $branch_address; ?></h6>
    	<h5>Token Summary</h5>

         <div style="float:left"><strong>Date:</strong><span style="text-align: left;"><?php echo date_format(date_create($from_date), 'd-m-Y'); ?> To <?php echo date_format(date_create($to_date), 'd-m-Y'); ?></span></div>

         <div style="float:right">Print Time: <?php echo date('h:i:s A'); ?></div>
         </br>

         <div style="float:left"><strong>User Name:</strong> <span style="text-align: left;"><?php echo $u_name; ?></span></div>

         <div style="float:right">Print Date:<?php echo date('d-m-Y'); ?></div>
         </td>

	</tr>
		<tr>
			<th>S #</th>
			<th>Time</th>
			<th>Date</th>
			<th>Tokan</th>
			<th>Patient</th>
			<th>Phone</th>
			<th>Age</th>
			<th>Pre</th>
			<th>Dr Id</th>
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
if($u_id != 0)
{
$select = "SELECT * FROM tokans WHERE 
	`user_id` = '$u_id' AND 
	`created` <= '$last_date' AND 
	`created` >= '$from_date' AND
	`status` = '1' 
	ORDER BY `created` ";
}
else
{
$select = "SELECT * FROM tokans WHERE 
	`branch_id` = '$br_id' AND 
	`created` <= '$last_date' AND 
	`created` >= '$from_date' AND
	`status` = '1' 
	ORDER BY `created` ";
}
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) 
{
	while ($row = mysqli_fetch_array($run)) 
	{
		$s = $s + 1;
		$token_date = $row['created'];
		$previous_tokan_no = $row['previous_tokan_no'];
		if($previous_tokan_no != 'NULL'){$pre =  $previous_tokan_no;}else{$pre = "NULL";}
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
				$phone = $row_patient['phone'];
				$age = $row_patient['age'];
				$gender = $row_patient['gender'];
				if($gender == 1){$genders = 'F';}
				elseif($gender == 2){$genders = 'M';}
				else{$genders = 'O';}
			}
		}
		else
		{
				$name = "No Name";
				$age = 0;
				$genders = 'O';
		}		
		$doctor_id = $row['doctor_id'];
		$select_doctor = "SELECT * FROM users WHERE id = '$doctor_id' ";
		$run_doctor = mysqli_query($con, $select_doctor);
		if (mysqli_num_rows($run_doctor) > 0) 
		{
			while ($row_doctor = mysqli_fetch_array($run_doctor)) 
			{
				$dr_name = $row_doctor['u_name'];
			}
		}
		else
		{
				$dr_name = "Self";
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
			<td>'.date_format(date_create($token_date), "h:i A").'</td>
			<td>'.date_format(date_create($token_date), "d M").'</td>
			<td style="text-align: right;">'.$row['id'].'</td>
			<td>'.$name.'('.$genders.')</td>
			<td>'.$phone.'</td>
			<td style="text-align: right;">'.$age.'</td>
			<td>'.$pre.'</td>
			<td>'.$doctor_id.'</td>
			<td style="text-align: right;">'.$row['cash'].'</td>
			<td>'.$title.'</td>
			<td style="text-align: right;">'.$row['cash_received'].'</td>
		</tr>
		';
	}
}
?>
<tr style="text-align: right;">
	<th colspan="8"></th>
	<th colspan="2"><?php echo $total_cash; ?></th>
	<th colspan="2"><?php echo $total_cash_received; ?></th>
</tr>
<?php
if($u_id != 0)
{
$select = "SELECT distinct tokan_type_id ,cash_received FROM tokans WHERE 
	`user_id` = '$u_id' AND 
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
				user_id = '$u_id' AND 
				`created` <= '$last_date' AND 
				`created` >= '$from_date' AND tokan_type_id = '$tokan_type_id' AND `status` = '1' ";
			$count_tokens = mysqli_num_rows(mysqli_query($con, $select_count));
		$select_tokan_type = "SELECT * FROM tokan_types WHERE id = '$tokan_type_id' AND `status` = '1' ";
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
			<th style="text-align: right;" colspan="4">'.$title.'</th>
			<th style="text-align: center;" colspan="3">'.$count_tokens.'</th>
			<th style="text-align: left;" colspan="4">'.($count_tokens * $row['cash_received']).'</th>
		</tr>';
	}
}
}
else
{
$select = "SELECT distinct tokan_type_id ,cash_received FROM tokans WHERE 
	`branch_id` = '$br_id' AND 
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
            	`branch_id` = '$br_id' AND 
				`created` <= '$last_date' AND 
				`created` >= '$from_date' AND tokan_type_id = '$tokan_type_id' AND `status` = '1' ";
			$count_tokens = mysqli_num_rows(mysqli_query($con, $select_count));
		$select_tokan_type = "SELECT * FROM tokan_types WHERE id = '$tokan_type_id' AND `status` = '1' ";
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
			<th style="text-align: right;" colspan="4">'.$title.'</th>
			<th style="text-align: center;" colspan="3">'.$count_tokens.'</th>
			<th style="text-align: left;" colspan="4">'.($count_tokens * $row['cash_received']).'</th>
		</tr>';
	}
}
    $return_token_amount = 0;
    $return_tokens = '';
    $return = "SELECT `id`, `cash_received` FROM `tokans` WHERE `branch_id` = '$br_id' AND status = '3' AND `created` <= '$last_date' AND  `created` >= '$from_date'";
    $run_return = mysqli_query($con, $return);
    if(mysqli_num_rows($run_return) > 0)
    {
        while($row_return = mysqli_fetch_array($run_return))
        {
            $return_tokens .= $row_return['id'] . " ";
            $return_token_amount = $return_token_amount + $row_return['cash_received'];
        }
        echo 
    '<tr>
			<th style="text-align: left;" colspan="11">RETURN TOKEN: Amount -> <u>'.$return_token_amount.'</u> --- Token Nos -> <u>'.$return_tokens.'</u> </th>
	</tr>';
	}
    $pending_receive_amount = 0;
    $receive_tokens = '';
    $receive = "SELECT * FROM `branch_pending_receive` WHERE`branch_id` = '$br_id' AND status = '1' AND `created` <= '$last_date' AND  `created` >= '$from_date' ";
    $run_receive = mysqli_query($con, $receive);
    if(mysqli_num_rows($run_receive) > 0)
    {
        echo 
    '<tr>
	<td colspan="11">
	<table border="solid" style="margin: auto atuo;"><tr>';
    echo '<tr><th>TOKEN NO</th><th>NAME</th><th>AMOUNT</th><th>REF NAME</th><th>TOKEN BY</th></tr>';
        while($row_receive = mysqli_fetch_array($run_receive))
        {
            $pending_tokens .= $row_receive['token_no'] . " ";
            $token_no = $row_receive['token_no'];
            $amount = $row_receive['amount'];
            $token_by = get_uname_by_id($row_receive['user_id']);
            $pending_receive_amount = $pending_receive_amount + $amount;
            $pending_patient_name = get_patient_name_by_token_id($token_no);
            $pending_ref_name = get_ref_name_by_token_id($token_no);
                echo '<tr><td>'.$token_no.'</td><td style = "text-transform: uppercase;">'.$pending_patient_name.'</td><td>'.$amount.'</td><td>'.$pending_ref_name.'</td><td>'.$token_by.'</td></tr>';
        }
        echo 
    '
    <caption style="text-align: left;caption-side: top;color: black;text-align: center;" colspan="11"><strong>PENDING RECEIVED: AMOUNT -> <u>'.$pending_receive_amount.'</strong></caption></caption>
</table>
	</td></tr>';
	}
      
    $pending_token_amount = 0;
    $pending_tokens = '';
    $pending = "SELECT * FROM `branch_pending_details` WHERE`branch_id` = '$br_id' AND status = '1' AND `created` <= '$last_date' AND  `created` >= '$from_date' ";
    $run_pending = mysqli_query($con, $pending);
    if(mysqli_num_rows($run_pending) > 0)
    {
        echo 
    '<tr>
	<td colspan="11">
	<table border="solid" style="margin: auto atuo;"><tr>';
    echo '<tr><th>TOKEN NO</th><th>NAME</th><th>AMOUNT</th><th>Ref. Name</th><th>TOKEN BY</th></tr>';
        while($row_pending = mysqli_fetch_array($run_pending))
        {
            $pending_tokens .= $row_pending['token_no'] . " ";
            $token_no = $row_pending['token_no'];
                $select_token = "SELECT * FROM tokans WHERE id = '$token_no' ";
                $run_token = mysqli_query($con, $select_token);
                if(mysqli_num_rows($run_token) == 1)
                {
                    while($row_token = mysqli_fetch_array($run_token))
                    {
                        $computer = $row_token['cash'];
                        $cash_received = $row_token['cash_received'];
                        $token_by = get_uname_by_id($row_token['user_id']);
                        $amount = $computer - $cash_received;
                        $pending_patient_name = get_patient_name_by_token_id($token_no);
                        $pending_ref_name = get_ref_name_by_token_id($token_no);
                    }
                }
                        if($amount > 0)
                        {
                        $pending_token_amount = $pending_token_amount + $amount;
                        echo '<tr><td>'.$token_no.'</td><td style = "text-transform: uppercase;">'.$pending_patient_name.'</td><td>'.$amount.'</td><td>'.$pending_ref_name.'</td><td>'.$token_by.'</td></tr>';
                        }
        }
        echo 
    '<caption style="text-align: left;caption-side: top;color: black;text-align: center;" colspan="11"><strong>PENDING TOKEN: Amount -> <u>'.$pending_token_amount.'</strong></caption></caption>
	</table>
	</td></tr>';
	}
}
?>
	</tbody>
</table>

</body>
</html>
 <script type="text/javascript">
    //   setTimeout(window.close, 50);
</script>