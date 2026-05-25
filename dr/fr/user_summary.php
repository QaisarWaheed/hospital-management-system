<?php
include 'includes/connect.php';
require_once __DIR__ . '/../../includes/summary_form_actions.php';

if (isset($_GET['print_summary'])) {
	$from_date = $_GET['from_date'] ?? '';
	$to_date = $_GET['to_date'] ?? '';
	$br_id = $_GET['br_id'] ?? $branch_id;
	$user_id_s = $_GET['user_id'] ?? '0';

	if ($from_date === '' || $to_date === '') {
		http_response_code(400);
		exit('From and to dates are required.');
	}

	$user_name_s = 'ALL';
	if ($user_id_s != '0' && $user_id_s !== 0) {
		$user = "SELECT u_name FROM users WHERE id = '$user_id_s' LIMIT 1";
		$run_user = mysqli_query($con, $user);
		if ($run_user && ($row_user = mysqli_fetch_assoc($run_user))) {
			$user_name_s = $row_user['u_name'];
		}
	}

	$print_url = 'print_summary.php?' . http_build_query(array(
		's' => $from_date,
		'e' => $to_date,
		'u' => $user_id_s,
		'un' => $user_name_s,
		'br_id' => $br_id,
	));
	?>
<!DOCTYPE html>
<html>
<head><title>Opening summary...</title></head>
<body>
<script>
window.open(<?php echo json_encode(ycdo_absolute_url_if_relative($print_url)); ?>, '_blank');
window.location.replace('user_summary.php');
</script>
</body>
</html>
<?php
	exit;
}
?>
<?php include 'includes/head.php'; ?>
<title>User Summary - <?php echo $company_trademark; ?></title>
</head>

<body class="">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke" style="min-height: 450px">
		<?php include 'left_navigation.php'; ?>
	</div>
	<?php fr_summary_content_open(); ?>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
		<form method="GET" class="container-fluid">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label for="from_date">From:</label>
					<input type="date" name="from_date" class="form-control" required id="from_date" value="<?php echo date('Y-m-d'); ?>">
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label for="to_date">To:</label>
					<input type="date" name="to_date" class="form-control" required id="to_date" value="<?php echo date('Y-m-d'); ?>">
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
                <label>SELECT BRANCH</label>
                <select class="form-control" style="min-width: 200px;text-transform: uppercase;" name="br_id" required>
                    <option value="<?php echo (int) $branch_id; ?>"><?php echo htmlspecialchars($branch_address); ?></option>
                </select>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6">
                <label>SELECT USER</label>
                <select class="form-control" style="min-width: 200px;text-transform: uppercase;" name="user_id">
                    <option value="0">ALL</option>
<?php
$user = "SELECT * FROM users WHERE role_id IN (1, 2, 7) AND status = 1 AND branch_id = '$branch_id' ORDER BY `u_name` ASC ";
$run_user = mysqli_query($con, $user);
if ($run_user && mysqli_num_rows($run_user) > 0) {
    while ($row_user = mysqli_fetch_array($run_user)) {
        echo '<option value="'.$row_user['id'].'">'.htmlspecialchars($row_user['u_name']).'</option>';
    }
} else {
    echo '<option value="">No users found</option>';
}
?>
                </select>
				</div>
				<?php fr_summary_form_actions('print_summary', 'PRINT SUMMARY'); ?>
			</div>
		</form>
		</div>
	</div>
	</div>
</div>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
