<?php include 'includes/connect.php'; 
if (isset($_POST['login_id']) && $_POST['login_id'] != '') 
{
	$login_id = $_POST['login_id'];
}
elseif (isset($_GET['login_id']) && $_GET['login_id'] != '') 
{
	$login_id = $_GET['login_id'];
}
else
{
    header('location: logout.php');
}

$users = "SELECT `login_id`, `computer_total`,`received_amount`,`donation_collection`,`submitted_amount`,`submitted_to`,`short_amount`,`extra_amount`, users.u_name, logins_detail.login_at, logins_detail.logout_at, roles.title FROM `summary_details` INNER JOIN logins_detail ON summary_details.login_id = logins_detail.id INNER JOIN users ON summary_details.user_id = users.id INNER JOIN roles ON users.role_id = roles.id WHERE login_id = '$login_id' ORDER BY login_at ";
$run_users = mysqli_query($con, $users);
if(mysqli_num_rows($run_users) > 0)
{
    while($row_users = mysqli_fetch_array($run_users))
    {
        $s++;
        $user_login_name = $row_users['u_name'];
        $role_title = $row_users['title'];
        $login_id = $row_users['login_id'];
        $login_at = $row_users['login_at'];
        $logout_at = $row_users['logout_at'];
        $submitted_amount = $row_users['submitted_amount'];
        $computer_total= $row_users['computer_total'];
        $donation_collection = $row_users['donation_collection'];
        $total_cash = $total_cash + $computer_total;
        $received_amount= $row_users['received_amount'];
        $total_cash_received = $total_cash_received + $received_amount;
        $short_amount = $row_users['short_amount'];
        $total_short = $total_short + $short_amount;
        $extra_amount = $row_users['extra_amount'];
        $total_extra = $total_extra + $extra_amount;
        $total_receiveable = $received_amount + $extra_amount;
        $total_r_a = $total_r_a + $total_receiveable;
    }
}
?>
<?php include 'includes/head.php'; ?>
	<title>Logout - <?php echo $company_trademark; ?></title>
<style>
body
{
    text-transform: uppercase;
}
</style>
</head>
<body>
<table class="table">
    <tr>                
        <td colspan="3">
            <h2 align="center"><?php echo $branch_name; ?></h2>
        </td>            
        <td>
            <img src="images/label.jpg" alt="LOGO YCDO" width="55" height="70" align="left" />
        </td>
    </tr>
	<tr>
		<th>Login No</th>
		<td><?php echo $login_id; ?></td>
		<th>Branch</th>
		<td><?php echo $branch_address; ?></td>
	</tr>
	<tr>
		<th>Name</th>
		<td><?php echo $user_login_name; ?></td>
		<th>Login Time</th>
		<td><?php echo date_format(date_create($login_at), 'd-M-Y h:i:s A'); ?></td>
	</tr>
	<tr>
		<th>Login As</th>
		<td><?php echo $role_title; ?></td>
		<th>Logout Time</th>
		<td><?php echo date_format(date_create($logout_at), 'd-M-Y h:i:s A'); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Computer Total Cash</th>
		<td colspan="2"><?php echo number_format((float)($computer_total ?? 0)); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Cash Received</th>
		<td colspan="2"><?php echo  number_format((float)($received_amount ?? 0)); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Donation Collection</th>
		<td colspan="2"><?php echo number_format((float)($donation_collection ?? 0)); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Total Cash</th>
		<td colspan="2"><?php echo number_format((float)($received_amount + $donation_collection ?? 0)); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Submitted / Physical Cash</th>
		<td colspan="2"><?php echo number_format((float)($submitted_amount ?? 0)); ?></td>
	</tr>

	<tr>
		<th colspan="2" style="text-align: right;">Extra Cash</th>
		<td colspan="2"><?php echo number_format((float)($extra_amount ?? 0)); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Short Cash</th>
		<td colspan="2"><?php echo number_format((float)($short_amount ?? 0)); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Submitted Amount with Report</th>
		<td colspan="2"><?php echo number_format((float)($submitted_amount+$short_amount ?? 0)); ?></td>
	</tr>
<?php
?>
</table>
</body>
</html>