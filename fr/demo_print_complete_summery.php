    <?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; ?>
	<title>Print Summary - <?php echo $company_trademark; ?></title>
<style>
*{
    font-size: 16px;
}
</style>
</head>

<body onload="window.print()">
<?php
if (isset($_POST['s']) && $_POST['s'] != '') {
	$from_date = $_POST['s'];
	$to_date = $_POST['e'];
	$u_id = $_POST['u'];
	$u_name = $_POST['un'];
	//echo print_summary($from_date, $to_date, $user_id, $user_name); 
}
elseif (isset($_GET['s']) && $_GET['s'] != '') {
	$from_date = $_GET['s'];
	$to_date = $_GET['e'];
	$u_id = $_GET['u'];
	$u_name = $_GET['un'];
	//echo print_summary($from_date, $to_date, $user_id, $user_name);
}

?>

<table class="table" style="font-size: 10px" BORDER = "solid">

	<thead>
	<tr style="caption-side: top;text-align: center;">
	    <td colspan="11">
	    YCDO EXECUTIVE HOSPITAL 2
    	<h6><?php echo $branch_address; ?></h6>
    	<h5>Token Summary</h5>

         <div style="float:left"><strong>Date:</strong><span style="text-align: left;"><?php echo date_format(date_create($from_date), 'd-m-Y'); ?> To <?php echo date_format(date_create($to_date), 'd-m-Y'); ?></span></div>

         <div style="float:right">Print Time: <?php echo date('h:i:s A'); ?></div>
         </br>

         <div style="float:left"><strong>User Name:</strong> <span style="text-align: left;"><?php echo $u_name; ?></span></div>

         <div style="float:right">Print Date:<?php echo date('d-m-Y'); ?></div>
         </td>

	</tr>
		<tr style="text-align: center;">
			<th colspan="5">Total Amount</th>
			<th colspan="6">Received Amount</th>
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
$select = "SELECT sum(`cash`),sum(`cash_received`) FROM tokans WHERE 
	user_id = '$u_id' AND 
	`created` <= '$last_date' AND 
	`created` >= '$from_date' AND
	`status` = '1' 
	ORDER BY `created` ";
}
else
{
$select = "SELECT sum(`cash`),sum(`cash_received`) FROM tokans WHERE 
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
		$total_cash =  $row['0'];
		$total_cash_received =  $row['1'];
	}
}
?>
<tr style="text-align: center;">
	<th colspan="5"><?php echo number_format((float)($total_cash ?? 0)); ?></th>
	<th colspan="6"><?php echo number_format((float)($total_cash_received ?? 0)); ?></th>
</tr>

<?php
if($u_id != 0)
{
$select = "SELECT distinct tokan_type_id ,cash_received FROM tokans WHERE 
	user_id = '$u_id' AND 
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
    $opd_total = 0;
    $opd_general = 0;
    $count_opd_poor = 0;
    $count_opd_member = 0;
    $count_opd_general = 0;
    $opd_poor = 0;
    $opd_member = 0;
    $opd_private = 0;
    $consultent_opd_total = 0;
    $consultent_opd_general = 0;
    $consultent_opd_poor = 0;
    $consultent_opd_member = 0;
    $count_opd_consultent_poor = 0;
    $count_opd_consultent_member = 0;
    $count_opd_consultent_general = 0;
echo 
'<tr>
    <th colspan="3">Category</th>
    <th colspan="2">Poor</th>
    <th colspan="2">Member</th>
    <th colspan="2">General</th>
    <th colspan="2">Total</th>    
</tr>';
$select = "SELECT distinct tokan_type_id ,SUM(cash_received),COUNT(`id`) FROM tokans WHERE
    `status` = '1' AND
	`created` <= '$last_date' AND 
	`created` >= '$from_date' AND tokan_type_id < 100
	GROUP BY tokan_type_id
	ORDER BY `tokan_type_id` ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) 
{
    
	while ($row = mysqli_fetch_array($run)) 
	{	    
	    $token_type_id = $row['tokan_type_id'];
	    if($token_type_id == 1)
	    {
    	    $opd_poor = $row['1'];
    	    $count_opd_poor = $count_opd_poor + $row['2'];
    	    $opd_total = $opd_poor + $opd_total;
	    }
	    elseif($token_type_id == 2)
	    {
    	    $opd_general = $row['1'];
    	    $count_opd_general = $count_opd_general + $row['2'];
    	    $opd_total = $opd_general + $opd_total;
	    }
	    elseif($token_type_id == 3)
	    {
    	    $opd_private = $row['1'];
    	    $count_opd_member = $count_opd_member + $row['2'];
    	    $opd_total = $opd_private + $opd_total;
	    }
	    elseif($token_type_id == 9)
	    {
    	    $opd_member = $row['1'];
    	    $count_opd_member = $count_opd_member + $row['2'];
    	    $opd_total = $opd_member + $opd_total;
	    }
	    elseif($token_type_id == 5)
	    {
    	    $consultent_opd_general = $row['1'];
    	    $count_opd_consultent_general = $count_opd_consultent_general + $row['2'];
    	    $consultent_opd_total = $consultent_opd_general + $consultent_opd_total;
	    }
	    elseif($token_type_id == 6)
	    {
    	    $consultent_opd_member = $row['1'];
    	    $count_opd_consultent_member = $count_opd_consultent_member + $row['2'];
    	    $consultent_opd_total = $consultent_opd_member + $consultent_opd_total;
	    }
	    elseif($token_type_id == 7)
	    {
    	    $consultent_opd_poor = $row['1'];
    	    $count_opd_consultent_poor = $count_opd_consultent_poor + $row['2'];
    	    $consultent_opd_total = $consultent_opd_poor + $consultent_opd_total;
	    }
		$select_tokan_type = "SELECT * FROM tokan_types WHERE id = '$token_type_id' AND `status` = '1' ";
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
	}
}
		echo '
		<tr>
			<th colspan="3">GENERAL OPD</th>
			<th style="text-align: center;" colspan="2">'.$opd_poor.'('.$count_opd_poor.')</th>
			<th style="text-align: center;" colspan="2">'.$opd_member+$opd_private.'('.$count_opd_member.')</th>
			<th style="text-align: center;" colspan="2">'.$opd_general.'('.$count_opd_general.')</th>
			<th style="text-align: center;" colspan="2">'.$opd_total.'</th>
		</tr>';
		echo '
		<tr>
			<th colspan="3">CONSULTENT OPD</th>
			<th style="text-align: center;" colspan="2">'.$consultent_opd_poor.'('.$count_opd_consultent_poor.')</th>
			<th style="text-align: center;" colspan="2">'.$consultent_opd_member.'('.$count_opd_consultent_member.')</th>
			<th style="text-align: center;" colspan="2">'.$consultent_opd_general.'('.$count_opd_consultent_general.')</th>
			<th style="text-align: center;" colspan="2">'.$consultent_opd_total.'</th>
		</tr>';
// Complete OPD

    $lab_amount_poor = 0;
    $lab_count_poor = 0;
    
    $lab_amount_member = 0;
    $lab_count_member = 0;
    
    $lab_amount_general = 0;
    $lab_count_general = 0;
    
    $lab_amount_deserving = 0;
    $lab_count_deserving = 0;
    
    $lab_amount = 0;
    $lab = "SELECT DISTINCT `tokan_type_id`, COUNT(`id`),SUM(`cash`),SUM(`cash_received`) FROM tokans WHERE `status` = '1' AND `created` <= '2023-01-01' AND `created` >= '2022-12-01' AND (tokan_type_id > 100 AND tokan_type_id < 200) AND id IN (SELECT DISTINCT tokan_no FROM item_by_doctor WHERE item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (SELECT id FROM items WHERE category_id = 2))) GROUP BY `tokan_type_id` ";
    $run_lab = mysqli_query($con, $lab);
if (mysqli_num_rows($run_lab) > 0) 
{
	while ($row_lab = mysqli_fetch_array($run_lab)) 
	{	    
	    $tokan_type_id = $lab_count + $row_lab['0'];
	    if($tokan_type_id == 102)
	    {
    	    $lab_count_poor = $lab_count + $row_lab['1'];
    	    $lab_amount_poor = $lab_amount_poor + $row_lab['2'];
    	    $lab_amount = $lab_amount + $row_lab['2'];
	    }
	    elseif($tokan_type_id == 103)
	    {
    	    $lab_count_member = $lab_count + $row_lab['1'];
    	    $lab_amount_member = $lab_amount_member + $row_lab['2'];
    	    $lab_amount = $lab_amount + $row_lab['2'];
	    }
	    elseif($tokan_type_id == 104)
	    {
    	    $lab_count_general = $lab_count + $row_lab['1'];
    	    $lab_amount_general = $lab_amount_general + $row_lab['2'];
    	    $lab_amount = $lab_amount + $row_lab['2'];
	    }
	}
}
		echo '
		<tr>
			<th colspan="3">LAB </th>
			<th style="text-align: center;" colspan="2">'.$lab_amount_poor.'('.$lab_count_poor.')</th>
			<th style="text-align: center;" colspan="2">'.$lab_amount_member.'('.$lab_count_member.')</th>
			<th style="text-align: center;" colspan="2">'.$lab_amount_general.'('.$lab_count_general.')</th>
			<th style="text-align: center;" colspan="2">'.$lab_amount.'</th>
		</tr>';


    $procedure_amount_poor = 0;
    $procedure_count_poor = 0;
    
    $procedure_amount_member = 0;
    $procedure_count_member = 0;
    
    $procedure_amount_general = 0;
    $procedure_count_general = 0;
    
    $procedure_amount_deserving = 0;
    $procedure_count_deserving = 0;
    
    $procedure_amount = 0;
    $procedure = "SELECT DISTINCT `tokan_type_id`, COUNT(`id`),SUM(`cash`),SUM(`cash_received`) FROM tokans WHERE `status` = '1' AND `created` <= '2023-01-01' AND `created` >= '2022-12-01' AND (tokan_type_id > 100 AND tokan_type_id < 200) AND id IN (SELECT DISTINCT tokan_no FROM item_by_doctor WHERE item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (SELECT id FROM items WHERE category_id = 3))) GROUP BY `tokan_type_id` ";
    $run_procedure = mysqli_query($con, $procedure);
if (mysqli_num_rows($run_procedure) > 0) 
{
	while ($row_procedure = mysqli_fetch_array($run_procedure)) 
	{	    
	    $tokan_type_id = $procedure_count + $row_procedure['0'];
	    if($tokan_type_id == 102)
	    {
    	    $procedure_count_poor = $procedure_count + $row_procedure['1'];
    	    $procedure_amount_poor = $procedure_amount_poor + $row_procedure['2'];
    	    $procedure_amount = $procedure_amount + $row_procedure['2'];
	    }
	    elseif($tokan_type_id == 103)
	    {
    	    $procedure_count_member = $procedure_count + $row_procedure['1'];
    	    $procedure_amount_member = $procedure_amount_member + $row_procedure['2'];
    	    $procedure_amount = $procedure_amount + $row_procedure['2'];
	    }
	    elseif($tokan_type_id == 104)
	    {
    	    $procedure_count_general = $procedure_count + $row_procedure['1'];
    	    $procedure_amount_general = $procedure_amount_general + $row_procedure['2'];
    	    $procedure_amount = $procedure_amount + $row_procedure['2'];
	    }
	}
}
		echo '
		<tr>
			<th colspan="3">PROCEDURE</th>
			<th style="text-align: center;" colspan="2">'.$procedure_amount_poor.'('.$procedure_count_poor.')</th>
			<th style="text-align: center;" colspan="2">'.$procedure_amount_member.'('.$procedure_count_member.')</th>
			<th style="text-align: center;" colspan="2">'.$procedure_amount_general.'('.$procedure_count_general.')</th>
			<th style="text-align: center;" colspan="2">'.$procedure_amount.'</th>
		</tr>';


    $medicine_amount_poor = 0;
    $medicine_count_poor = 0;
    
    $medicine_amount_member = 0;
    $medicine_count_member = 0;
    
    $medicine_amount_general = 0;
    $medicine_count_general = 0;
    
    $medicine_amount_deserving = 0;
    $medicine_count_deserving = 0;
    
    $medicine_amount = 0;
    $medicine = "SELECT DISTINCT `tokan_type_id`, COUNT(`id`),SUM(`cash`),SUM(`cash_received`) FROM tokans WHERE `status` = '1' AND `created` <= '2023-01-01' AND `created` >= '2022-12-01' AND (tokan_type_id > 100 AND tokan_type_id < 200) AND id IN (SELECT DISTINCT tokan_no FROM item_by_doctor WHERE item_id IN (SELECT id FROM item_register_to_branches WHERE item_id IN (SELECT id FROM items WHERE id > 0))) GROUP BY `tokan_type_id` ";
    $run_medicine = mysqli_query($con, $medicine);
if (mysqli_num_rows($run_medicine) > 0) 
{
	while ($row_medicine = mysqli_fetch_array($run_medicine)) 
	{	    
	    $tokan_type_id = $medicine_count + $row_medicine['0'];
	    if($tokan_type_id == 102)
	    {
    	    $medicine_count_poor = $medicine_count + $row_medicine['1'];
    	    $medicine_amount_poor = $medicine_amount_poor + $row_medicine['2'];
    	    $medicine_amount = $medicine_amount + $row_medicine['2'];
	    }
	    elseif($tokan_type_id == 103)
	    {
    	    $medicine_count_member = $medicine_count + $row_medicine['1'];
    	    $medicine_amount_member = $medicine_amount_member + $row_medicine['2'];
    	    $medicine_amount = $medicine_amount + $row_medicine['2'];
	    }
	    elseif($tokan_type_id == 104)
	    {
    	    $medicine_count_general = $medicine_count + $row_medicine['1'];
    	    $medicine_amount_general = $medicine_amount_general + $row_medicine['2'];
    	    $medicine_amount = $medicine_amount + $row_medicine['2'];
	    }
	}
}
		echo '
		<tr>
			<th colspan="3">MEDICINE & OTHER</th>
			<th style="text-align: center;" colspan="2">'.($medicine_amount_poor-($procedure_amount_poor+$lab_amount_poor)).'('.$medicine_count_poor.')</th>
			<th style="text-align: center;" colspan="2">'.($medicine_amount_member-($procedure_amount_member+$lab_amount_member)).'('.$medicine_count_member.')</th>
			<th style="text-align: center;" colspan="2">'.($medicine_amount_general-($procedure_amount_general+$lab_amount_general)).'('.$medicine_count_general.')</th>
			<th style="text-align: center;" colspan="2">'.$medicine_amount-($procedure_amount+$lab_amount).'</th>
		</tr>';

		echo '
		<tr>
			<th colspan="9" style="text-align: right;">TOTAL AMOUNT</th>
			<th style="text-align: center;" colspan="2"><u>'.number_format((float)($consultent_opd_total+$opd_total+$medicine_amount ?? 0)).'</u></th>
		</tr>';



    $return_token_amount = 0;
    $return_tokens = '';
    $return = "SELECT `id`, `cash_received` FROM `tokans` WHERE status = '3' AND `created` <= '$last_date' AND  `created` >= '$from_date'";
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
    
      
    $pending_token_amount = 0;
    $pending_tokens = '';
    $pending = "SELECT * FROM `branch_pending_details` WHERE status = '1' AND `created` <= '$last_date' AND  `created` >= '$from_date' ";
    $run_pending = mysqli_query($con, $pending);
    if(mysqli_num_rows($run_pending) > 0)
    {
        echo 
    '<tr>
	<td colspan="11">';
// 	echo '<table border="solid" style="margin: auto atuo;"><tr>';
    // echo '<tr><th>TOKEN NO</th><th>AMOUNT</th></tr>';
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
                        $amount = $computer - $cash_received;
                    }
                }
                        if($amount > 0)
                        {
                        $pending_token_amount = $pending_token_amount + $amount;
                        // echo '<tr><td>'.$token_no.'</td><td>'.$amount.'</td></tr>';
                        }
        }
        echo 
    '<h3 style="text-align: left;text-align: center;" colspan="11">PENDING TOKEN Amount -> '.number_format((float)($pending_token_amount ?? 0)).' </h3>';
// 	echo '</table>';
	echo '</td></tr>';
	}
    
    
    $pending_receive_amount = 0;
    $receive_tokens = '';
    $receive = "SELECT * FROM `branch_pending_receive` WHERE status = '1' AND `created` <= '$last_date' AND  `created` >= '$from_date' ";
    $run_receive = mysqli_query($con, $receive);
    if(mysqli_num_rows($run_receive) > 0)
    {
        echo 
    '<tr>
	<td colspan="11">
	<table border="solid" style="margin: auto atuo;"><tr>';
    // echo '<tr><th>TOKEN NO</th><th>AMOUNT</th></tr>';
        while($row_receive = mysqli_fetch_array($run_receive))
        {
            $pending_tokens .= $row_receive['token_no'] . " ";
            $token_no = $row_receive['token_no'];
            $amount = $row_receive['amount'];
            $pending_receive_amount = $pending_receive_amount + $amount;
                // echo '<tr><td>'.$token_no.'</td><td>'.$amount.'</td></tr>';
        }
        echo 
    '
    <h3 style="text-align: left;text-align: center;" colspan="11">PENDING RECEIVED AMOUNT -> '.number_format((float)($pending_receive_amount ?? 0)).' </h3>
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

