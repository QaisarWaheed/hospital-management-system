<?php include 'includes/connect.php'; 
if (isset($_GET['print_comparision'])) {
	$first_month = $_GET['first_month'];
 	$second_month = $_GET['second_month'];
	$br_id = $_GET['br_id'];
	$user_id_s = $_GET['user_id'];
        $user = "SELECT * FROM users WHERE id = '$user_id_s' ";
        $run_user = mysqli_query($con, $user);
        if (mysqli_num_rows($run_user) > 0) 
        {
            while ($row_user = mysqli_fetch_array($run_user)) {
                $user_name_s = $row_user['u_name'];
            }
        }
        else
        {
            $user_name_s = "ALL";
        }
?>
<script>
    window.open("print_comparision_test.php?s=<?php echo $first_month; ?>&e=<?php echo $second_month; ?>", "_blank", "toolbar=no,scrollbars=no,resizable=no,top=50,left=50,status=no");
    location.replace("comparision_all_branches.php");
window.close();
</script>
<?php
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
	<div class="col-md-3 background_whitesmoke" style="min-height: 150px">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9 background_image_ycdo">
	<div class="row">
		
		<div class="col-md-12 col-sm-12 col-xs-12">
			
		<form method="GET"  target="_blank">
			
			<div class="row">
				
				<div class="col-md-12 col-sm-12 col-xs-12" style = "text-align: center;">
            		<label><h2>COMPARISION ALL BRANCHES </h2></label>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6">
					<label for="first_month">1st Month:</label>
					<input type="month" name="first_month" class="form-control" required id="first_month">
				
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6">

					<label for="second_month">2nd Month:</label>
					<input type="month" name="second_month" class="form-control" required id="second_month">
				
				</div>

				<div class="col-md-6 col-sm-6 col-xs-6">
					<br>
					<input class="btn btn-sm btn-primary" type="submit" name="print_comparision" value="PRINT COMPARISION" />

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