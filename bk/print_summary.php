<?php
require_once __DIR__ . '/includes/connect.php';
require_once __DIR__ . '/../includes/report_helpers.php';

$params = summary_token_report_params($_GET, $_POST);
if ($params === null) {
    http_response_code(400);
    exit('Date range is required.');
}

$from_date = $params['from'];
$to_date = $params['to'];
$u_id = (int) $params['user_id'];
$br_id = (int) $params['branch_id'];
$u_name = $params['user_name'];

$header = summary_branch_header($con, $br_id, $company_name);
$branch_name = $header['name'];
$branch_address = $header['address'];
$date_where = summary_tokans_date_sql($from_date, $to_date, $con);
$pending_date_where = summary_pending_date_sql($from_date, $to_date, $con);

$from_label = ycdo_safe_date_format($from_date, 'd-m-Y', $from_date);
$to_label = ycdo_safe_date_format($to_date, 'd-m-Y', $to_date);
?>
<?php include 'includes/head.php'; ?>
	<title>Print Summary - <?php echo htmlspecialchars($company_trademark); ?></title>
<style>
* { font-size: 16px; }
</style>
</head>

<body onload="window.print()">

<table class="table" style="font-size: 10px">
	<thead>
	<tr style="caption-side: top;text-align: center;">
	    <td colspan="11">
	    <?php echo htmlspecialchars($branch_name); ?>
    	<h6><?php echo htmlspecialchars($branch_address); ?></h6>
    	<h5>Token Summary</h5>
         <div style="float:left"><strong>Date:</strong><span style="text-align: left;"><?php echo htmlspecialchars($from_label); ?> To <?php echo htmlspecialchars($to_label); ?></span></div>
         <div style="float:right">Print Time: <?php echo date('h:i:s A'); ?></div>
         <br>
         <div style="float:left"><strong>User Name:</strong> <span style="text-align: left;"><?php echo htmlspecialchars($u_name); ?></span></div>
         <div style="float:right">Print Date:<?php echo date('d-m-Y'); ?></div>
         </td>
	</tr>
		<tr>
			<th>S #</th>
			<th>Time</th>
			<th>Date</th>
			<th>Tokan</th>
			<th>Patient</th>
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
$s = 0;
$total_cash = 0;
$total_cash_received = 0;

if ($u_id > 0) {
    $select = "SELECT * FROM tokans WHERE `user_id` = '$u_id' AND $date_where AND `status` = '1' ORDER BY `created`";
} else {
    $select = "SELECT * FROM tokans WHERE `branch_id` = '$br_id' AND $date_where AND `status` = '1' ORDER BY `created`";
}

$run = mysqli_query($con, $select);
$has_data = false;

if ($run && mysqli_num_rows($run) > 0) {
    while ($row = mysqli_fetch_array($run)) {
        $s++;
        $token_date = $row['created'];
        $pre = summary_previous_tokan_display($row['previous_tokan_no'] ?? null);
        $total_cash += (float) $row['cash'];
        $total_cash_received += (float) $row['cash_received'];
        $patient_id = (int) $row['patient_id'];
        $name = 'No Name';
        $age = 0;
        $genders = 'O';
        $select_patient = "SELECT name, age, gender FROM patients WHERE id = '$patient_id' LIMIT 1";
        $run_patient = mysqli_query($con, $select_patient);
        if ($run_patient && ($row_patient = mysqli_fetch_assoc($run_patient))) {
            $name = $row_patient['name'];
            $age = $row_patient['age'];
            $genders = summary_gender_code($row_patient['gender']);
        }

        $doctor_id = (int) $row['doctor_id'];
        $dr_name = 'Self';
        $select_doctor = "SELECT u_name FROM users WHERE id = '$doctor_id' LIMIT 1";
        $run_doctor = mysqli_query($con, $select_doctor);
        if ($run_doctor && ($row_doctor = mysqli_fetch_assoc($run_doctor))) {
            $dr_name = $row_doctor['u_name'];
        }

        $tokan_type_id = (int) $row['tokan_type_id'];
        $title = 'No Title';
        $select_tokan_type = "SELECT title FROM tokan_types WHERE id = '$tokan_type_id' LIMIT 1";
        $run_tokan_type = mysqli_query($con, $select_tokan_type);
        if ($run_tokan_type && ($row_tokan_type = mysqli_fetch_assoc($run_tokan_type))) {
            $title = $row_tokan_type['title'];
        }

        echo '<tr>';
        echo '<td>' . $s . '</td>';
        echo '<td>' . ycdo_safe_date_format($token_date, 'h:i A', '') . '</td>';
        echo '<td>' . ycdo_safe_date_format($token_date, 'd M', '') . '</td>';
        echo '<td style="text-align: right;">' . (int) $row['id'] . '</td>';
        echo '<td>' . htmlspecialchars($name) . '(' . $genders . ')</td>';
        echo '<td style="text-align: right;">' . htmlspecialchars((string) $age) . '</td>';
        echo '<td>' . htmlspecialchars($pre) . '</td>';
        echo '<td>' . $doctor_id . '</td>';
        echo '<td style="text-align: right;">' . htmlspecialchars((string) $row['cash']) . '</td>';
        echo '<td>' . htmlspecialchars($title) . '</td>';
        echo '<td style="text-align: right;">' . htmlspecialchars((string) $row['cash_received']) . '</td>';
        echo '</tr>';
    }
}
?>
<tr style="text-align: right;">
	<th colspan="8"></th>
	<th colspan="1"><?php echo $total_cash; ?></th>
	<th colspan="1"></th>
	<th colspan="1"><?php echo $total_cash_received; ?></th>
</tr>
<?php
if ($u_id > 0) {
    $select = "SELECT DISTINCT tokan_type_id, cash_received FROM tokans WHERE `user_id` = '$u_id' AND $date_where AND tokan_type_id < 100 ORDER BY `tokan_type_id`";
} else {
    $select = "SELECT DISTINCT tokan_type_id, cash_received FROM tokans WHERE `branch_id` = '$br_id' AND $date_where AND tokan_type_id < 100 ORDER BY `tokan_type_id`";
}
$run = mysqli_query($con, $select);
if ($run && mysqli_num_rows($run) > 0) {
    while ($row = mysqli_fetch_array($run)) {
        $tokan_type_id = (int) $row['tokan_type_id'];
        if ($u_id > 0) {
            $select_count = "SELECT COUNT(*) AS cnt FROM tokans WHERE user_id = '$u_id' AND $date_where AND tokan_type_id = '$tokan_type_id' AND `status` = '1'";
        } else {
            $select_count = "SELECT COUNT(*) AS cnt FROM tokans WHERE branch_id = '$br_id' AND $date_where AND tokan_type_id = '$tokan_type_id' AND `status` = '1'";
        }
        $count_tokens = 0;
        $run_count = mysqli_query($con, $select_count);
        if ($run_count && ($row_count = mysqli_fetch_assoc($run_count))) {
            $count_tokens = (int) $row_count['cnt'];
        }
        $title = 'No Title';
        $select_tokan_type = "SELECT title FROM tokan_types WHERE id = '$tokan_type_id' AND `status` = '1' LIMIT 1";
        $run_tokan_type = mysqli_query($con, $select_tokan_type);
        if ($run_tokan_type && ($row_tokan_type = mysqli_fetch_assoc($run_tokan_type))) {
            $title = $row_tokan_type['title'];
        }
        echo '<tr>';
        echo '<th style="text-align: right;" colspan="4">' . htmlspecialchars($title) . '</th>';
        echo '<th style="text-align: center;" colspan="3">' . $count_tokens . '</th>';
        echo '<th style="text-align: left;" colspan="4">' . ($count_tokens * (float) $row['cash_received']) . '</th>';
        echo '</tr>';
    }
}

if ($u_id === 0 && $br_id > 0) {
    $return_token_amount = 0;
    $return_tokens = '';
    $return = "SELECT id, cash_received FROM tokans WHERE branch_id = '$br_id' AND status = '3' AND $date_where";
    $run_return = mysqli_query($con, $return);
    if ($run_return && mysqli_num_rows($run_return) > 0) {
        while ($row_return = mysqli_fetch_array($run_return)) {
            $return_tokens .= $row_return['id'] . ' ';
            $return_token_amount += (float) $row_return['cash_received'];
        }
        echo '<tr><th style="text-align: left;" colspan="11">RETURN TOKEN: Amount -> <u>' . $return_token_amount . '</u> --- Token Nos -> <u>' . htmlspecialchars(trim($return_tokens)) . '</u></th></tr>';
    }

    $pending_receive_amount = 0;
    $receive = "SELECT * FROM branch_pending_receive WHERE branch_id = '$br_id' AND status = '1' AND $pending_date_where";
    $run_receive = mysqli_query($con, $receive);
    if ($run_receive && mysqli_num_rows($run_receive) > 0) {
        echo '<tr><td colspan="11"><table border="solid" style="margin: auto;"><tr><th>TOKEN NO</th><th>AMOUNT</th></tr>';
        while ($row_receive = mysqli_fetch_array($run_receive)) {
            $token_no = $row_receive['token_no'];
            $amount = (float) $row_receive['amount'];
            $pending_receive_amount += $amount;
            echo '<tr><td>' . htmlspecialchars((string) $token_no) . '</td><td>' . $amount . '</td></tr>';
        }
        echo '<caption style="background: black;color: white;text-align: center;">PENDING RECEIVED: AMOUNT -> <u>' . $pending_receive_amount . '</u></caption><?php if (!$has_data) { ycdo_echo_report_no_data_found(); } ?>
</table></td></tr>';
    }

    $pending_token_amount = 0;
    $pending = "SELECT * FROM branch_pending_details WHERE branch_id = '$br_id' AND status = '1' AND $pending_date_where";
    $run_pending = mysqli_query($con, $pending);
    if ($run_pending && mysqli_num_rows($run_pending) > 0) {
        echo '<tr><td colspan="11"><table border="solid" style="margin: auto;"><tr><th>TOKEN NO</th><th>AMOUNT</th></tr>';
        while ($row_pending = mysqli_fetch_array($run_pending)) {
            $token_no = (int) $row_pending['token_no'];
            $amount = 0;
            $select_token = "SELECT cash, cash_received FROM tokans WHERE id = '$token_no' LIMIT 1";
            $run_token = mysqli_query($con, $select_token);
            if ($run_token && ($row_token = mysqli_fetch_assoc($run_token))) {
                $amount = (float) $row_token['cash'] - (float) $row_token['cash_received'];
            }
            if ($amount > 0) {
                $pending_token_amount += $amount;
                echo '<tr><td>' . $token_no . '</td><td>' . $amount . '</td></tr>';
            }
        }
        echo '<caption style="background: black;color: white;text-align: center;">PENDING TOKEN: Amount -> <u>' . $pending_token_amount . '</u></caption></table></td></tr>';
    }
}
?>
	</tbody>
</table>

</body>
</html>
<?php
if ($con instanceof mysqli) {
    mysqli_close($con);
}
