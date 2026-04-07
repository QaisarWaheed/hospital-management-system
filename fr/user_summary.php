<?php include 'includes/connect.php'; 
if (isset($_GET['print_summary'])) {
	$from_date = $_GET['from_date'];
 	$to_date = $_GET['to_date'];
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

// 	$user_name_s = $_GET['user_name'];
?>
<script>
window.open("print_summary.php?s=<?php echo $from_date; ?>&e=<?php echo $to_date; ?>&u=<?php echo $user_id_s; ?>&un=<?php echo $user_name_s; ?>&br_id=<?php echo $br_id; ?>", "_blank", "toolbar=no,scrollbars=no,resizable=no,top=50,left=50,status=no");
	  location.replace("user_summary.php");
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
			
		<form method="GET"  target="_blank">
			
			<div class="row">
				
				<div class="col-md-6 col-sm-6 col-xs-6">

					<label for="from_date">From:</label>
					<input type="date" name="from_date" class="form-control" required id="from_date">
				
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6">

					<label for="to_date">To:</label>
					<input type="date" name="to_date" class="form-control" required id="to_date">
				
				</div>

				<div class="col-md-6 col-sm-6 col-xs-6">
                <label> SELECT BRANCH</label>
                <select class="form-control" style="min-width: 200px;text-transform: uppercase;" name="br_id">
                    <option value="<?php echo $branch_id; ?>"><?php echo $branch_address; ?></option>
                </select>
				</div>
				<div class="col-md-6 col-sm-6 col-xs-6">
                <label>SELECT USER</label>
                <select class="form-control" style="min-width: 200px;text-transform: uppercase;" name="user_id">
                    <option value="0">ALL</option>
<?php 

$con = mysqli_connect('localhost', 'ycdoeh1', 'ycdoeh1', 'ycdomlt');
$user = "SELECT * FROM users WHERE role_id IN (1, 2, 7) AND status = 1 AND branch_id = '$branch_id' ORDER BY `u_name` ASC ";
$run_user = mysqli_query($con, $user);
if (mysqli_num_rows($run_user) > 0) 
{
    while ($row_user = mysqli_fetch_array($run_user)) {
        echo '<option value="'.$row_user['id'].'">'.$row_user['u_name'].'</option>';
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