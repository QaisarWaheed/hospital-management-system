<?php include 'includes/connect_logout.php'; 
?>
<?php include 'includes/head.php'; ?>
	<title>Logout - <?php echo $company_trademark; ?></title>
<script>
    document.oncontextmenu = function(e) {return false;};
    document.onkeydown = function(e) {
            if (e.ctrlKey && (e.keyCode === 67 || e.keyCode === 86 || e.keyCode === 85 || e.keyCode === 117)) {//Alt+c, Alt+v will also be disabled sadly.
                alert('not allowed');
            return false;
            }
    };
</script>
</head>

<body>

<?php 
if (isset($_POST['physicall_cash_again']) && $_POST['physicall_cash_again'] != '') 
{
    $submitted_cash = $_POST['physicall_cash_again'];
    $total_cash = $_POST['total_cash'];
    $admin_id = $_POST['admin_id'];
    $admin_password = md5($_POST['admin_password']);
    $reason_for_admin_password_used = $_POST['reason_for_admin_password_used'];
    $staff_name_who_uses_admin_password = $_POST['staff_name_who_uses_admin_password'];
    $old_physicall_cash = $_POST['old_physicall_cash'];
$select = "SELECT * FROM `users` WHERE `id` = '$admin_id' AND `password` = '$admin_password' ";
$select_admin = mysqli_query($con, $select);
if(mysqli_num_rows($select_admin) == 1)
{
    $login_id = $_SESSION['login_id'];
    mysqli_query($con, "UPDATE logins_detail SET logout_at = '$current_date', status = '2' WHERE id = '$login_id' AND status = '1' ");
        $search = "SELECT * FROM logins_detail WHERE id = '$login_id' ";
        $run = mysqli_query($con, $search);
        if(mysqli_num_rows($run) == 1)
        {
            while($row = mysqli_fetch_array($run))
            {
                $login_at = $row['login_at'];
                $logout_at = $row['logout_at'];
            }
        }
$cash =                      get_total_token_cash($user_id, $login_at,  $logout_at);
$cash_received =     get_total_token_cash_received($user_id, $login_at, $logout_at);
$donation_amount =   get_total_donation_collection($user_id, $login_at, $logout_at);
if($submitted_cash > ($cash_received + $donation_amount) ){$short_amount = 0;$extra_amount = $submitted_cash - ($cash_received + $donation_amount);}
elseif($submitted_cash < ($cash_received + $donation_amount) ){$short_amount = ($cash_received + $donation_amount)-$submitted_cash;$extra_amount = 0;}
else{$short_amount = 0;$extra_amount = 0;}

mysqli_query($con, "INSERT INTO `summary_details`
(`login_id`, `computer_total`, `received_amount`, `donation_collection`, `submitted_amount`, `submitted_to`, `short_amount`, `extra_amount`, `status`, `created`, `user_id`) VALUES 
('$login_id', '$cash', '$cash_received', '$donation_amount', '$submitted_cash', '0', '$short_amount', '$extra_amount', '1', '$current_date', '$user_id')");
mysqli_query($con, "INSERT INTO `summary_by_admin`( `login_id`, `admin_id`, `submmited_cash`, `total_cash`, `created`, `user_id`, `reason_for_admin_password_used`, `staff_name_who_uses_admin_password`, `old_physicall_cash`) VALUES
    ('$login_id', '$admin_id', '$submitted_cash', '$cash_received', '$current_date', '$user_id', '$reason_for_admin_password_used', '$staff_name_who_uses_admin_password', '$old_physicall_cash')");
?>
<table class="table">
    <tr>                
        <td colspan="3">
            <h2 align="center"><?php echo $branch_name; ?></h2>
        </td>            
        <td>
            <img src="images/label.jpg" alt="Girl in a jacket" width="55" height="70" align="left" />
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
		<td><?php echo $user_name; ?></td>
		<th>Login Time</th>
		<td><?php echo date_format(date_create($login_at), 'd-M-Y h:i:s A'); ?></td>
	</tr>
	<tr>
		<th>Login As</th>
		<td><?php echo show_role_by_user_id($user_id); ?></td>
		<th>Logout Time</th>
		<td><?php echo date_format(date_create($logout_at), 'd-M-Y h:i:s A'); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Computer Total Cash</th>
		<td colspan="2"><?php echo number_format($cash); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Cash Received</th>
		<td colspan="2"><?php echo  number_format($cash_received); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Donation Collection</th>
		<td colspan="2"><?php echo number_format($donation_amount); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Total Cash</th>
		<td colspan="2"><?php echo number_format($cash_received + $donation_amount); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Submitted / Physical Cash</th>
		<td colspan="2"><?php echo number_format($submitted_cash); ?></td>
	</tr>

	<tr>
		<th colspan="2" style="text-align: right;">Extra Cash</th>
		<td colspan="2"><?php echo number_format($extra_amount); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Short Cash</th>
		<td colspan="2"><?php echo number_format($short_amount); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Submitted Amount with Report</th>
		<td colspan="2"><?php echo number_format($submitted_cash+$short_amount); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Submitted By Admin</th>
		<td colspan="2"><?php echo get_uname_by_id($admin_id); ?></td>
	</tr>
	<tr>
		<th style="text-align: right;">REASON: </th>
		<td colspan="3"><?php echo $reason_for_admin_password_used; ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">PASSWORD BY: </th>
		<td colspan="2"><?php echo $staff_name_who_uses_admin_password; ?></td>
	</tr>
<?php
$select = "SELECT distinct tokan_type_id ,cash_received FROM tokans WHERE 
	user_id = '$user_id' AND 
	`created` <= '$logout_at' AND 
	`created` >= '$login_at' AND tokan_type_id < 100
	ORDER BY `tokan_type_id` ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) 
{
	while ($row = mysqli_fetch_array($run)) 
	{
		$tokan_type_id = $row['tokan_type_id'];
			$select_count = "SELECT * FROM tokans WHERE 
				user_id = '$user_id' AND 
				`created` <= '$logout_at' AND 
				`created` >= '$login_at' AND tokan_type_id = '$tokan_type_id' ";
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
			<th style="text-align: right;" colspan="1">'.$title.'</th>
			<th style="text-align: center;" colspan="1">'.$count_tokens.'</th>
			<th style="text-align: left;" colspan="2">'.($count_tokens * $row['cash_received']).'</th>
		</tr>';
	}
}
// else
// {
//     echo '<tr><td colspan="2">'.$select.'</td></tr>';
// }
?>    
</table>
<?php
}
else
{ echo '<script>alert("'.$select.'");</script>';
?>
<div class="container">
<form method="POST">
	<div class="row">
		<div class="col-md-12">
			<label>ENTER PHYSICALL AMOUNT</label>
			<input type="text" name="total_cash" id="total_cash" value="<?php echo get_total_token_cash_received($user_id, $login_at, $current_date); ?>"> 
			<input type="number" min="0" value="<?php echo $submitted_cash; ?>" name="physicall_cash_again" id="physicall_cash_again" class="form-control"> 
		</div>
		<div class="col-md-12">
			<label>SELECT ADMIN</label>
			<select name="admin_id" required class="form-control">
			    <option value="">SELECT ADMIN</option>
<?php
$select_admin = mysqli_query($con, "SELECT * FROM users WHERE branch_id = '$branch_id' AND role_id = '2' AND  is_admin = '2' AND status = '1' ");
if(mysqli_num_rows($select_admin) > 0)
{
    while($row_admin = mysqli_fetch_array($select_admin))
    {
        $admin_id = $row_admin['id'];
        $admin_name = $row_admin['u_name'];
        echo '<option value="'.$admin_id.'">'.$admin_name.'</option>';
    }
}
?>
			</select>
		</div>
		<div class="col-md-12">
			<label>ENTER ADMIN PASSWORD</label>
			<input type="password" name="admin_password" id="admin_password" required class="form-control"> 
		</div>
		<div class="col-md-12">
			<label>REASON WHY ADMIN PASSWORD USED</label>
			<input type="text" name="reason_for_admin_password_used" id="reason_for_admin_password_used" required class="form-control"> 
			<input type="hidden" value = "<?php echo $submitted_cash; ?>" name="old_physicall_cash" id="old_physicall_cash" required class="form-control"> 
		</div>
		<div class="col-md-12">
			<label>ENTER YOUR NAME (whi is puttung the password of admin)</label>
			<input type="text" name="staff_name_who_uses_admin_password" id="staff_name_who_uses_admin_password" required class="form-control"> 
		</div>
		<div class="col-md-12" style="margin-top: 30px;">
			<input type="submit" value="SUBMIT PHYSICALL CASH" name="physical_again" class="form-control btn btn-outline-primary"> 
		</div>
	</div>
</form>	
</div>
<?php }
}
elseif (isset($_POST['physicall_cash']) && $_POST['physicall_cash'] != '') 
{
    $submitted_cash = $_POST['physicall_cash'];
    $total_cash = $_POST['total_cash'];
if($submitted_cash >= $total_cash)
{
    $login_id = $_SESSION['login_id'];
    mysqli_query($con, "UPDATE logins_detail SET logout_at = '$current_date', status = '2' WHERE id = '$login_id' AND status = '1' ");
        $search = "SELECT * FROM logins_detail WHERE id = '$login_id' ";
        $run = mysqli_query($con, $search);
        if(mysqli_num_rows($run) == 1)
        {
            while($row = mysqli_fetch_array($run))
            {
                $login_at = $row['login_at'];
                $logout_at = $row['logout_at'];
            }
        }
$cash =                      get_total_token_cash($user_id, $login_at,  $logout_at);
$cash_received =     get_total_token_cash_received($user_id, $login_at, $logout_at);
$donation_amount =   get_total_donation_collection($user_id, $login_at, $logout_at);
if($submitted_cash > ($cash_received + $donation_amount) ){$short_amount = 0;$extra_amount = $submitted_cash - ($cash_received + $donation_amount);}
elseif($submitted_cash < ($cash_received + $donation_amount) ){$short_amount = ($cash_received + $donation_amount)-$submitted_cash;$extra_amount = 0;}
else{$short_amount = 0;$extra_amount = 0;}

mysqli_query($con, "INSERT INTO `summary_details`
(`login_id`, `computer_total`, `received_amount`, `donation_collection`, `submitted_amount`, `submitted_to`, `short_amount`, `extra_amount`, `status`, `created`, `user_id`) VALUES 
('$login_id', '$cash', '$cash_received', '$donation_amount', '$submitted_cash', '0', '$short_amount', '$extra_amount', '1', '$current_date', '$user_id')");
?>
<table class="table">
    <tr>                
        <td colspan="3">
            <h2 align="center"><?php echo $branch_name; ?></h2>
        </td>            
        <td>
            <img src="images/label.jpg" alt="Girl in a jacket" width="55" height="70" align="left" />
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
		<td><?php echo $user_name; ?></td>
		<th>Login Time</th>
		<td><?php echo date_format(date_create($login_at), 'd-M-Y h:i:s A'); ?></td>
	</tr>
	<tr>
		<th>Login As</th>
		<td><?php echo show_role_by_user_id($user_id); ?></td>
		<th>Logout Time</th>
		<td><?php echo date_format(date_create($logout_at), 'd-M-Y h:i:s A'); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Computer Total Cash</th>
		<td colspan="2"><?php echo number_format($cash); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Cash Received</th>
		<td colspan="2"><?php echo  number_format($cash_received); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Donation Collection</th>
		<td colspan="2"><?php echo number_format($donation_amount); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Total Cash</th>
		<td colspan="2"><?php echo number_format($cash_received + $donation_amount); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Submitted / Physical Cash</th>
		<td colspan="2"><?php echo number_format($submitted_cash); ?></td>
	</tr>

	<tr>
		<th colspan="2" style="text-align: right;">Extra Cash</th>
		<td colspan="2"><?php echo number_format($extra_amount); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Short Cash</th>
		<td colspan="2"><?php echo number_format($short_amount); ?></td>
	</tr>
	<tr>
		<th colspan="2" style="text-align: right;">Submitted Amount with Report</th>
		<td colspan="2"><?php echo number_format($submitted_cash+$short_amount); ?></td>
	</tr>
<?php
$total_tokens = 0;
$total_token_cesh = 0;
$select = "SELECT distinct tokan_type_id ,cash_received FROM tokans WHERE 
	user_id = '$user_id' AND 
	`created` <= '$logout_at' AND 
	`created` >= '$login_at' AND tokan_type_id < 100
	ORDER BY `tokan_type_id` ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) 
{
    echo '<tr><td colspan="4" style="text-align: center;">Total Patients Detail</td></tr>';
	while ($row = mysqli_fetch_array($run)) 
	{
		$tokan_type_id = $row['tokan_type_id'];
			$select_count = "SELECT * FROM tokans WHERE 
				user_id = '$user_id' AND 
				`created` <= '$logout_at' AND 
				`created` >= '$login_at' AND tokan_type_id = '$tokan_type_id' ";
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
		}
        $total_tokens = $total_tokens + $count_tokens;
        $total_token_cesh = $total_token_cesh +($count_tokens * $row['cash_received']);
		echo '<tr>
			<th style="text-align: right;" colspan="2">'.$title.'</th>
			<th style="text-align: center;" colspan="1">'.$count_tokens.'</th>
			<th style="text-align: left;" colspan="1">'.($count_tokens * $row['cash_received']).'</th>
		</tr>';
	}
echo '
<tr>
    <th style="text-align: right;" colspan="2"></th>
    <th style="text-align: center;" colspan="1"><strong>'.$total_tokens.'</strong></th>
    <th style="text-align: left;" colspan="1"><strong>'.intval($total_token_cesh).'</strong></th>
</tr>';
}
// else
// {
//     echo '<tr><td colspan="2">'.$select.'</td></tr>';
// }
?>      
</table>
<?php
}
else
{    ?>
<div class="container">
<form method="POST">
	<div class="row">
		<div class="col-md-12">
		    <label style="text-align: center;"><h2><?php echo $branch_name; ?></h2></label><br>
			<label>ENTER PHYSICALL AMOUNT</label>
			<input type="hidden" name="total_cash" id="total_cash" value="<?php echo get_total_token_cash_received($user_id, $login_at, $current_date); ?>"> 
			<input type="number" min="0" value="<?php echo $submitted_cash; ?>" name="physicall_cash_again" id="physicall_cash_again" class="form-control"> 
		</div>
		<div class="col-md-12">
			<label>SELECT ADMIN</label>
			<select name="admin_id" required class="form-control">
			    <option value="">SELECT ADMIN</option>
<?php
$select_admin = mysqli_query($con, "SELECT * FROM users WHERE branch_id = '$branch_id' AND role_id = '2' AND  is_admin = '2' AND status = '1' ");
if(mysqli_num_rows($select_admin) > 0)
{
    while($row_admin = mysqli_fetch_array($select_admin))
    {
        $admin_id = $row_admin['id'];
        $admin_name = $row_admin['u_name'];
        echo '<option value="'.$admin_id.'">'.$admin_name.'</option>';
    }
}
?>
			</select>
		</div>
		<div class="col-md-12">
			<label>ENTER ADMIN PASSWORD</label>
			<input type="password" name="admin_password" id="admin_password" required class="form-control"> 
		</div>
		<div class="col-md-12">
			<label>REASON WHY ADMIN PASSWORD USED</label>
			<input type="text" name="reason_for_admin_password_used" id="reason_for_admin_password_used" required class="form-control"> 
			<input type="hidden" value = "<?php echo $submitted_cash; ?>" name="old_physicall_cash" id="old_physicall_cash" required class="form-control"> 
		</div>
		<div class="col-md-12">
			<label>ENTER YOUR NAME (whi is puttung the password of admin)</label>
			<input type="text" name="staff_name_who_uses_admin_password" id="staff_name_who_uses_admin_password" required class="form-control"> 
		</div>
		<div class="col-md-12" style="margin-top: 30px;">
			<input type="submit" value="SUBMIT PHYSICALL CASH" name="physical_again" class="form-control btn btn-outline-primary"> 
		</div>
	</div>
</form>	
</div>
<?php }
}
else
{
    mysqli_query($con, "UPDATE logins_detail SET logout_at = '$current_date', status = '2' WHERE id = '$login_id' AND status = '1' ");
        $search = "SELECT * FROM logins_detail WHERE id = '$login_id' ";
        $run = mysqli_query($con, $search);
        if(mysqli_num_rows($run) == 1)
        {
            while($row = mysqli_fetch_array($run))
            {
                $login_at = $row['login_at'];
            }
        }
?>
<div class="container">
<form method="POST" onsubmit="return checknumber(this);">
	<div class="row">
		<div class="col-md-12">
		    <label style="text-align: center;"><h2><?php echo $branch_name; ?></h2></label><br>
			<label>ENTER PHYSICALL AMOUNT</label>
			<input type="hidden" name="total_cash" id="total_cash" value="<?php echo get_total_token_cash_received($user_id, $login_at, $current_date); ?>"> 
			<input type="number" min="0" name="physicall_cash" id="physicall_cash" class="form-control"> 
		</div>
		<div class="col-md-12" style="margin-top: 30px;">
			<input type="submit" value="SUBMIT PHYSICALL CASH" name="physical" class="form-control btn btn-outline-primary"> 
		</div>
	</div>
</form>	
</div>
<?php }
?>
</body>
</html>