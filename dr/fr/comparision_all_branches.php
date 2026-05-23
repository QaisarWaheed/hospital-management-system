<?php
include 'includes/connect.php';
require_once __DIR__ . '/../../includes/summary_form_actions.php';

if (isset($_GET['print_comparision'])) {
	$first_month = $_GET['first_month'] ?? '';
	$second_month = $_GET['second_month'] ?? '';

	if ($first_month === '' || $second_month === '') {
		http_response_code(400);
		exit('Both months are required.');
	}

	$print_url = 'print_comparision_test.php?' . http_build_query(array(
		's' => $first_month,
		'e' => $second_month,
	));
	fr_summary_print_redirect($print_url, 'comparision_all_branches.php');
	exit;
}
?>
<?php include 'includes/head.php'; ?>
<title>Comparison All Branches - <?php echo $company_trademark; ?></title>
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
				<div class="col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
					<label><h2>COMPARISION ALL BRANCHES</h2></label>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label for="first_month">1st Month:</label>
					<input type="month" name="first_month" class="form-control" required id="first_month">
				</div>
				<div class="col-md-6 col-sm-6 col-xs-12">
					<label for="second_month">2nd Month:</label>
					<input type="month" name="second_month" class="form-control" required id="second_month">
				</div>
				<?php fr_summary_form_actions('print_comparision', 'PRINT COMPARISION'); ?>
			</div>
		</form>
		</div>
	</div>
	</div>
</div>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
