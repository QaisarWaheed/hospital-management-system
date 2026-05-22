<?php include 'includes/connect.php';
if (isset($_GET['print_summary'], $_GET['from_date'], $_GET['to_date'], $_GET['b_id'])) {
	$from_date = $_GET['from_date'];
	$to_date = $_GET['to_date'];
	$b_id = $_GET['b_id'];
	$b_address = 'ALL';
	$user = "SELECT * FROM branchs WHERE id = '$b_id' ";
	$run_user = mysqli_query($con, $user);
	if ($run_user && mysqli_num_rows($run_user) > 0) {
		while ($row_user = mysqli_fetch_array($run_user)) {
			$b_address = $row_user['name'];
		}
	}
?>
<script>
window.open("print_summary_login.php?s=<?php echo urlencode($from_date); ?>&e=<?php echo urlencode($to_date); ?>&u=<?php echo urlencode($b_id); ?>&un=<?php echo urlencode($b_address); ?>", "_blank", "toolbar=no,scrollbars=yes,resizable=yes,top=50,left=50,width=1200,height=800");
location.replace("user_summary_login.php");
</script>
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
	<div class="col-md-9 background_image_ycdo">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
		<form method="GET" target="_blank">
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
$user = "SELECT * FROM branchs WHERE id = '$branch_id' AND status = 1 ";
$run_user = mysqli_query($con, $user);
if ($run_user && mysqli_num_rows($run_user) > 0) {
    while ($row_user = mysqli_fetch_array($run_user)) {
        echo '<option value="'.$row_user['id'].'">'.htmlspecialchars($row_user['address']).'</option>';
    }
} else {
    echo '<option value="">No branch found</option>';
}
?>
                </select>
				</div>
				<div class="col-md-12 col-sm-12 col-xs-12" style="margin-top: 1em;">
					<input class="btn btn-sm btn-primary" type="submit" name="print_summary" value="PRINT SUMMARY" />
					<input class="btn btn-sm btn-danger" type="reset" value="CLEAR FORM" />
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
