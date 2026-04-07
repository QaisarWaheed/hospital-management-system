<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 

$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if(mysqli_num_rows($run_roles) == 1)
{
    while($row_role = mysqli_fetch_array($run_roles))
    {
        $role_title = $row_role['title'];
    }
}
else
{
    $role_title = '';
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
<style>
body
{
    text-transform: uppercase;
}
</style>
</head>

<body class="background_image" oncontextmenu="return false;">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke" style = "text-transform: uppercase;">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
<?php
if (isset($_POST['s']) && $_POST['s'] != '') 
{
	$from_date = $_POST['s'];
	$to_date = $_POST['e'];
	$b_id = $_POST['b_id'];
}
elseif (isset($_GET['s']) && $_GET['s'] != '') 
{
	$from_date = $_GET['s'];
	$to_date = $_GET['e'];
	$b_id = $_GET['b_id'];
}
else
{
    $today = date('Y-m-d');
	$from_date = $today;
	$to_date = $today;
	$b_id = $branch_id;
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
			<th>Id</th>
			<th>Name</th>
			<th>Login Time</th>
			<th>Logout Time</th>
			<th>Computer Amount</th>
			<th>Received Amount</th>
			<th>Extra</th>
			<th>Short</th>
			<th>Total</th>
			<th>Print</th>
		</tr>
	</thead>
	<tbody>
<?php 
$start_date = date('Y-m-d', strtotime('-1 day', strtotime($from_date)));
$last_date = date('Y-m-d', strtotime('+1 day', strtotime($to_date)));
$s = 0;
$total_cash = 0;
$total_extra = 0;
$total_short = 0;
$total_r_a = 0;
$total_cash_received = 0;

$users = "SELECT `login_id`, `computer_total`,`received_amount`,`donation_collection`,`submitted_amount`,`submitted_to`,`short_amount`,`extra_amount`, users.u_name, logins_detail.login_at, logins_detail.logout_at FROM `summary_details` INNER JOIN logins_detail ON summary_details.login_id = logins_detail.id INNER JOIN users ON summary_details.user_id = users.id WHERE logins_detail.login_at >= '$start_date' AND logins_detail.login_at <= '$last_date' AND logins_detail.status = '2' AND logins_detail.branch_id = '$b_id' ORDER BY login_at ";
$run_users = mysqli_query($con, $users);
if(mysqli_num_rows($run_users) > 0)
{
    while($row_users = mysqli_fetch_array($run_users))
    {
        $s++;
        $user_login_name = $row_users['u_name'];
        $login_id = $row_users['login_id'];
        $login_at = $row_users['login_at'];
        $logout_at = $row_users['logout_at'];
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
			<td>'.$login_id.'</td>
			<td>'.$user_login_name.'</td>
			<td>'.$login_at.'</td>
			<td>'.$logout_at.'</td>
			<td>'.$computer_total.'</td>
			<td>'.$received_amount.'</td>
			<td>'.$extra_amount.'</td>
			<td>'.$short_amount.'</td>
			<td>'.$total_receiveable.'</td>
			<td>
			    <a href = "print_summery_by_admin_user.php?login_id='.$login_id.'" class = "btn btn-sm btn-success"> <i class="fa fa-print" aria-hidden="true"></i></a>
			</td>
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
	</div>
</div>

</body>
</html>