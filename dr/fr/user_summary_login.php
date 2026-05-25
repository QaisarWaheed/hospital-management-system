<?php
include 'includes/connect.php';
require_once __DIR__ . '/../../includes/summary_form_actions.php';

if (isset($_GET['print_summary'])) {
	$from_date = $_GET['from_date'] ?? '';
	$to_date = $_GET['to_date'] ?? '';
	$b_id = $_GET['b_id'] ?? $branch_id;

	if ($from_date === '' || $to_date === '') {
		http_response_code(400);
		exit('From and to dates are required.');
	}

	$print_url = 'print_summary_login.php?' . http_build_query(array(
		's' => $from_date,
		'e' => $to_date,
		'b_id' => $b_id,
	));
	?>
<!DOCTYPE html>
<html>
<head><title>Opening summary...</title></head>
<body>
<script>
window.open(<?php echo json_encode(ycdo_absolute_url_if_relative($print_url)); ?>, '_blank');
window.location.replace('user_summary_login.php');
</script>
</body>
</html>
<?php
	exit;
}
?>
<?php include 'includes/head.php'; ?>
<title>User Summary Login Wise - <?php echo $company_trademark; ?></title>
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
				<div class="col-md-6 col-sm-6 col-xs-6">
					<label for="from_date">From:</label>
					<input type="date" name="from_date" class="form-control" required id="from_date" value="<?php echo date('Y-m-d'); ?>">
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6">
					<label for="to_date">To:</label>
					<input type="date" name="to_date" class="form-control" required id="to_date" value="<?php echo date('Y-m-d'); ?>">
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12">
                <label>SELECT BRANCH</label>
                <select class="form-control" style="min-width: 200px;text-transform: uppercase;" name="b_id" required>
<?php
require_once __DIR__ . '/../../includes/report_helpers.php';
$fr_all_branches = summary_branch_may_select_all((int) $is_admin, (int) $is_incharge);
echo summary_branch_select_html($con, (int) $branch_id, (int) $branch_id, $fr_all_branches, 'b_id');
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
