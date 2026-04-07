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
	$b_id = $_POST['b_id'];
}
elseif (isset($_GET['s']) && $_GET['s'] != '') {
	$from_date = $_GET['s'];
	$to_date = $_GET['e'];
	$b_id = $_GET['b_id'];
}

?>

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

         <div style="float:left"><strong>User Login:</strong> <span style="text-align: left;">All Logins</span></div>

         <div style="float:right">Print Date:<?php echo date('d-m-Y'); ?></div>
         </td>

	</tr>
		<tr>
			<th>S #</th>
			<th>Name</th>
			<th>Login Time</th>
			<th>Logout Time</th>
			<th>Computer Amount</th>
			<th>Received Amount</th>
			<th>Extra</th>
			<th>Short</th>
			<th>Total</th>
		</tr>
	</thead>
	<tbody>
<?php 
$last_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date)));
$s = 0;
$total_cash = 0;
$total_extra = 0;
$total_short = 0;
$total_r_a = 0;
$total_cash_received = 0;
$select = "SELECT * FROM tokans WHERE 
	`branch_id` = '$br_id' AND 
	`created` <= '$last_date' AND 
	`created` >= '$from_date' AND
	`status` = '1' AND 
	user_id IN (SELECT id FROM users WHERE branch_id = '$b_id') 
	ORDER BY `created` ";
// $run = mysqli_query($con, $select);

$users = "SELECT * FROM `summary_details` WHERE login_id IN (SELECT id FROM logins_detail WHERE branch_id = '$b_id' AND login_at <= '$last_date' AND `login_at` >= '$from_date' AND `status` = '2') ORDER BY `created` ";
$run_users = mysqli_query($con, $users);
if(mysqli_num_rows($run_users) > 0)
{
    while($row_users = mysqli_fetch_array($run_users))
    {
        $s = $s + 1;
        $user_login_id = $row_users['user_id'];
        $login_id = $row_users['login_id'];
        $login_detail = "SELECT * FROM logins_detail WHERE id = '$login_id' ";
        $run = mysqli_query($con, $login_detail);
        if(mysqli_num_rows($run) == 1)
        {
            while($row = mysqli_fetch_array($run))
            {
                $login_at = $row['login_at'];
                $logout_at = $row['logout_at'];
            }
        }        
        $computer_total= $row_users['computer_total'];
        $total_cash = $total_cash + $computer_total;
        $received_amount= $row_users['received_amount'];
        $total_cash_received = $total_cash_received + $received_amount;
        $short_amount = $row_users['short_amount'];
        $total_short = $total_short + $short_amount;
        $extra_amount = $row_users['extra_amount'];
        $total_extra = $total_extra + $extra_amount;
        $total_receiveable = $received_amount + $extra_amount;
        $total_r_a = $total_r_a + $total_receiveable;
        echo '
		<tr>
			<td>'.$s.'</td>
			<td>'.get_uname_by_id($user_login_id).'</td>
			<td>'.$login_at.'</td>
			<td>'.$logout_at.'</td>
			<td>'.$computer_total.'</td>
			<td>'.$received_amount.'</td>
			<td>'.$extra_amount.'</td>
			<td>'.$short_amount.'</td>
			<td>'.$total_receiveable.'</td>
		</tr>';
    }
}
?>
		<tr>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th><?php echo $total_cash;?></th>
			<th><?php echo $total_cash_received;?></th>
			<th><?php echo $total_extra;?></th>
			<th><?php echo $total_short;?></th>
			<th><?php echo $total_r_a;?></th>
		</tr>
	</tbody>
</table>
<?php
$select = "SELECT distinct tokan_type_id ,cash_received FROM tokans WHERE 
	(`branch_id` = '$br_id' AND `created` like '$to_date%' AND tokan_type_id < 100 ) OR 
	(`branch_id` = '$br_id' AND `created` <= '$to_date' AND `created` >= '$from_date' AND tokan_type_id < 100 )
	ORDER BY `tokan_type_id` ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) 
{
    // echo $select;
	while ($row = mysqli_fetch_array($run)) 
	{
		$tokan_type_id = $row['tokan_type_id'];
			$select_count = "SELECT * FROM tokans WHERE 
            	(`branch_id` = '$br_id' AND `created` like '$to_date%' AND tokan_type_id = '$tokan_type_id' AND `status` = '1') OR 
	(`branch_id` = '$br_id' AND `created` <= '$to_date' AND `created` >= '$from_date' AND tokan_type_id = '$tokan_type_id' AND `status` = '1' ) ";
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
		}		
		echo 
		'
			<p style="text-align: center;"><strong>'.$title.' -> '.$count_tokens.' Amount('.intval($count_tokens * $row['cash_received']).')</strong></p>
		';
	}
    // echo $select;
}
else
{
    // echo $select;
}
?>
</body>
</html>
 <script type="text/javascript">
    //   setTimeout(window.close, 50);
</script>


<?php mysqli_close($con); ?>