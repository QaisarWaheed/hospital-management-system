<?php
include 'includes/connect.php';

if (isset($_GET['print_comparison'], $_GET['first_month'], $_GET['second_month'])) {
	$first_month = $_GET['first_month'];
	$second_month = $_GET['second_month'];
	if ($first_month === '' || $second_month === '') {
		http_response_code(400);
		exit('Both months are required.');
	}
	$print_url = 'print_comparison_report.php?s=' . urlencode($first_month) . '&e=' . urlencode($second_month);
	?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Opening comparison report…</title>
<script>
if ('serviceWorker' in navigator) {
	navigator.serviceWorker.getRegistrations().then(function (regs) {
		regs.forEach(function (reg) { reg.unregister(); });
	});
}
</script>
</head>
<body>
<p>Opening comparison report…</p>
<script>
window.open(<?php echo json_encode(ycdo_absolute_url_if_relative($print_url)); ?>, '_blank', 'toolbar=no,scrollbars=yes,resizable=yes,width=1200,height=800');
window.location.replace('comparison_report.php');
</script>
</body>
</html>
<?php
	exit;
}

include 'includes/head.php';
?>
	<title>Comparison Report - <?php echo $company_trademark; ?></title>
</head>
<body class="background_image">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO</h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9 background_image_ycdo">
		<form method="GET" class="container" style="margin-top: 2em;">
			<div class="row">
				<div class="col-md-12" style="text-align: center;">
            		<label><h2>Comparison Report (All Branches)</h2></label>
				</div>
				<div class="col-md-6">
					<label for="first_month">1st Month</label>
					<input type="month" name="first_month" class="form-control" required id="first_month" value="<?php echo date('Y-m', strtotime('-1 month')); ?>">
				</div>
				<div class="col-md-6">
					<label for="second_month">2nd Month</label>
					<input type="month" name="second_month" class="form-control" required id="second_month" value="<?php echo date('Y-m'); ?>">
				</div>
				<div class="col-md-12" style="margin-top: 1em;">
					<input class="btn btn-sm btn-primary" type="submit" name="print_comparison" value="PRINT COMPARISON" />
					<input class="btn btn-sm btn-danger" type="reset" value="CLEAR FORM" />
				</div>
			</div>
		</form>
	</div>
</div>
</body>
</html>
<?php mysqli_close($con); ?>
