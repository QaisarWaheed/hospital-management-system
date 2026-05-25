<?php include 'includes/connect.php'; 
if (isset($_GET['print_summary'])) {
	$from_date = $_GET['from_date'];
	$to_date = $_GET['to_date'];
	$user_id_s = $_GET['user_id'];
	$user_name_s = $_GET['user_name'];
?>
<script>
window.open(<?php echo json_encode(ycdo_absolute_url('print_summary.php', 's=' . rawurlencode((string) $from_date) . '&e=' . rawurlencode((string) $to_date) . '&u=' . rawurlencode((string) $user_id_s) . '&un=' . rawurlencode((string) $user_name_s))); ?>, "_blank", "toolbar=no,scrollbars=no,resizable=no,top=50,left=50,status=no");
	  location.replace(<?php echo json_encode(ycdo_absolute_url('user_summary.php')); ?>);
</script>
<?php
}
?>
<?php include 'includes/head.php'; ?>
<title>User Summary - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div class="" style="margin: 10px 15px;">

	<div class="row">
		
		<div class="col-md-12 col-sm-12 col-xs-12">
			
		<form>
			
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

					<label for="user_name">User Name:</label>
					<input type="hidden" value="<?php echo $user_id; ?>" name="user_id" class="form-control" id="user_id">
					<input type="text" value="<?php echo $user_name; ?>" readonly name="user_name" class="form-control" required id="user_name">
				
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

</body>

</html>

<script type="text/javascript" src="js/bootstrap.min.js"></script>