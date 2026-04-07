<?php include 'includes/connect.php'; 
if (isset($_GET['print_summary'])) {
	$from_date = $_GET['from_date'];
 	$to_date = $_GET['to_date'];
	$b_id = $_GET['b_id'];
?>
<script>
window.open("print_summary_login.php?s=<?php echo $from_date; ?>&e=<?php echo $to_date; ?>&b_id=<?php echo $b_id;?>", "_blank", "toolbar=no,scrollbars=no,resizable=no,top=50,left=50,status=no");
// 	  location.replace("user_summary_login.php");
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
	<div class="col-md-3 background_whitesmoke" style="min-height: 450px">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9 background_image_ycdo">
	<div class="row">
		
		<div class="col-md-12 col-sm-12 col-xs-12">
			
		<form target="_blank">
			
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

$user = "SELECT * FROM branchs WHERE id = '$bk_branch_id'";
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
<?php mysqli_close($con); ?>