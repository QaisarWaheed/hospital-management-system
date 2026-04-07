<?php include 'includes/connect.php';
if (isset($_POST['save'])) {
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$address = $_POST['address'];

	$run2 = mysqli_query($con, "INSERT INTO `parties`
	(`name`, `phone`, `address`, `created`) 
	VALUES 
	( '$name', '$phone', '$address', '$current_date')");
?>
<script>
	alert('DATA SAVE SUCCESSFULLY');
</script>
<?php
}
elseif (isset($_POST['update'])) {
	$u_id = $_POST['u_id'];
	$name = $_POST['name'];
	$phone = $_POST['phone'];
	$address = $_POST['address'];

	$run2 = mysqli_query($con, "UPDATE `parties`
	SET
	`name` = '$name',
	`phone` = '$phone',
	`address` = '$address'
	WHERE id = '$u_id' ");
?>
<script>
	alert('DATA SAVE SUCCESSFULLY');
</script>
<?php
    header('location: add_party.php ');
}
?>
<?php include 'includes/head.php'; ?>
	<title>Add Party - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

		<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>
			
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Add Party Form</h1></label>
			</div>
<?php
if(isset($_GET['u_id']) && $_GET['u_id'] != '')
{
    $u_id = $_GET['u_id'];
    $select = "SELECT * FROM parties WHERE id = '$u_id' ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run) == 1)
    {
        while($row = mysqli_fetch_array($run))
        {
            $name = $row['name'];
            $phone = $row['phone'];
            $address = $row['address'];
        }
    }

?>
			<div class="col-md-12">
				<form method = "POST">
					<div class="row">
						<div class="col-md-3">
							<label>Party Name</label>
							<input readonly type="hidden" name="u_id" value = "<?php echo $u_id; ?>" class="" required>
							<input type="text" name="name" value = "<?php echo $name; ?>" class="form-control" required>
						</div>
						<div class="col-md-3">
							<label>Phone No</label>
							<input type="text" name="phone"  value = "<?php echo $phone; ?>" maxlength="11" required class="form-control">
						</div>
						<div class="col-md-3">
							<label>Address</label>
							<textarea name="address" class="form-control" rows="1"><?php echo $address; ?></textarea>
						</div>
						<div class="col-md-3" style="margin: 35px 0px;">
							<input type="submit" name="update" value="Update ITEM" class="btn btn-sm btn-success">
						</div>
					</div>

				</form>
			</div>    
<?php 
exit(0);
} ?>
			<div class="col-md-12">
				<form method = "POST">
					<div class="row">
						<div class="col-md-3">
							<label>Party Name</label>
							<input type="text" name="name" class="form-control" required>
						</div>
						<div class="col-md-3">
							<label>Phone No</label>
							<input type="text" name="phone" maxlength="11" required class="form-control">
						</div>
						<div class="col-md-3">
							<label>Address</label>
							<textarea name="address" class="form-control" rows="1"></textarea>
						</div>
						<div class="col-md-3" style="margin: 35px 0px;">
							<input type="submit" name="save" value="SAVE ITEM" class="btn btn-sm btn-success">
							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-sm btn-warning">
						</div>
					</div>

				</form>
			</div>
			
			<div class="col-md-12" style="text-align: center;">
				<label><h1> Party Details</h1></label>
				<div class = "table">
				    <table class = "table">
				        <thead>
				            <tr>
				                <th>#</th>
				                <th>Name</th>
				                <th>Phone</th>
				                <th>Address</th>
				                <th>Action</th>
				            </tr>
				        </thead>
				        <tboby>
<?php
$s = 0;
$select = "SELECT * FROM parties";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $id = $row['id'];
        echo '				            
        <tr>
            <th>'.$s.'</th>
            <th>'.$row['name'].'</th>
            <th>'.$row['phone'].'</th>
            <th>'.$row['address'].'</th>
            <th>
                <a href="add_party.php?u_id='.$id.'" class = "btn btn-sm btn-primary">update</a>
            </th>
        </tr>
';
    }
}
?>
				        </tboby>
				    </table>
				</div>
			</div>

		</div>

	</div>
	

</div>


</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>