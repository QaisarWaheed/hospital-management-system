<?php include 'includes/connect.php'; 
if (isset($_GET['save'])) {
	$tokan_no = $_GET['tokan_no'];
?>
<script>
  window.open(<?php echo json_encode(ycdo_absolute_url('print_tokan.php', 'tokan_no=' . rawurlencode((string) $tokan_no))); ?>, "_blank", "toolbar=no,scrollbars=no,resizable=no,top=500,left=500,width=400,height=400,status=no");
</script>
<?php
}
?>
<?php include 'includes/head.php'; ?>
	<title>Patient Registeration - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">
		<form>
			<div class="row" style="margin-top: 20px">
				<div class="col-md-12">
					<label>ENTER TOKAN NO</label>
					<input required type="number" class="form-control" name="tokan_no" min="1" max="<?php echo intval(next_tokan_no()-1); ?>">
				</div>
				<div class="col-md-12" style="margin-top: 20px">
					<input type="submit" value="DUPLICATE" name="save" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>

</div>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>