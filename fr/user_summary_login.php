<?php
include 'includes/connect.php';

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
window.open(<?php echo json_encode($print_url); ?>, '_blank');
window.location.replace('user_summary_login.php');
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
	<div class="col-md-9 background_image_ycdo">
	<div class="row">
		
		<div class="col-md-12 col-sm-12 col-xs-12">
			
		<form method="GET">
			
			<div class="row">
				
				<div class="col-md-6 col-sm-6 col-xs-6">

					<label for="from_date">From:</label>
					<input type="date" name="from_date" class="form-control" required id="from_date">
				
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6">

					<label for="to_date">To:</label>
					<input type="date" name="to_date" class="form-control" required id="to_date">
				
				</div>

				<div class="col-md-12 col-sm-12 col-xs-12">
                <label>SELECT BRANCH</label>
                <select class="form-control" style="min-width: 200px;text-transform: uppercase;" name="b_id">
<?php 

$user = "SELECT * FROM branchs WHERE id = '$branch_id'";
$run_user = mysqli_query($con, $user);
if (mysqli_num_rows($run_user) > 0) 
{
    while ($row_user = mysqli_fetch_array($run_user)) {
        echo '<option value="'.$row_user['id'].'">'.$row_user['address'].'</option>';
    }
}
else
{
    echo '<option value="">Add Doctors Data</option>';
}
?>
                </select>
				</div>

				<div class="col-md-6 col-sm-6 col-xs-6">
					<br>
					<input class="btn btn-sm btn-primary" type="submit" name="print_summary" value="PRINT SUMMARY" />

					<input class="btn btn-sm btn-danger" type="reset" name="clear" value="CLEAR FORM" />

				</div>

			</div>

		</form>
	
		</div>

	</div>		
	</div>
</div>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
