<?php
include 'includes/connect.php';
require_once __DIR__ . '/../../includes/summary_form_actions.php';

if (isset($_GET['print_summary'])) {
	$select_month = $_GET['select_month'] ?? '';
	$br_id = $_GET['br_id'] ?? $branch_id;

	if ($select_month === '') {
		http_response_code(400);
		exit('Month is required.');
	}

	$print_url = 'print_account_summary.php?' . http_build_query(array(
		'month' => $select_month,
		'br_id' => $br_id,
	));
	?>
<!DOCTYPE html>
<html>
<head><title>Opening summary...</title></head>
<body>
<script>
window.open(<?php echo json_encode(ycdo_absolute_url_if_relative($print_url)); ?>, '_blank');
window.location.replace('account_summary.php');
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
				
				<div class="col-md-12 col-sm-12 col-xs-12">

					<label for="br_id">Branch</label>
                        <select class="form-control" style="min-width: 200px;text-transform: uppercase;" name="br_id">
                        <?php 
                            $branch = "SELECT * FROM branchs WHERE id = '$branch_id' AND status = 1 ORDER BY `address` ASC ";
                            $run_branch = mysqli_query($con, $branch);
                            if (mysqli_num_rows($run_branch) > 0) 
                            {
                                while ($row_branch = mysqli_fetch_array($run_branch)) 
                                {
                                    echo '<option value="'.$row_branch['id'].'">'.$row_branch['address'].'</option>';
                                }
                            }
                            else
                            {
                                echo '<option value="">Add Doctors Data</option>';
                            }
                        ?>
                        </select>
                </div>

				<div class="col-md-12 col-sm-12 col-xs-12">

					<label for="select_month">Month:</label>
					<input type="month" name="select_month" class="form-control" required id="select_month" value="<?php echo htmlspecialchars(date('Y-m'), ENT_QUOTES, 'UTF-8'); ?>">
				
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
