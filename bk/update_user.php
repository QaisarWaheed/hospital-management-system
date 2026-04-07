<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
?>
	<title>User Update - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>
			<?php
if(isset($_POST['update']) && $_POST['update'] != '')
{
	$up_id = $_POST['up_id'];
	$is_admin = $_POST['is_admin'];
	$is_incharge = $_POST['is_incharge'];
	$status = $_POST['status'];

	$update = "UPDATE users SET
    is_admin = '$is_admin',
    is_incharge = '$is_incharge',
    status = '$status'
	WHERE id = '$up_id' ";

    if(mysqli_query($con, $update))
    {
    echo '<script>alert("Update Item")</script>';
    echo '<script>location.replace("show_user.php")</script>';
    }
    exit(0);
}
elseif(isset($_GET['up']) && $_GET['up'] != '')
{
    $up_id = $_GET['up'];
    $select = mysqli_query($con,"SELECT * FROM users WHERE id = '$up_id' ");
    if (mysqli_num_rows($select) == 1) 
    {
    	while ($row = mysqli_fetch_array($select)) 
    	{
    		$u_name = $row['u_name'];
    		$is_admin = $row['is_admin'];
    		$is_incharge = $row['is_incharge'];
    		$status = $row['status'];
    		$role_id = $row['role_id'];
    		$roles = mysqli_query($con, "SELECT title FROM roles WHERE id = '$role_id' ");
    		while ($row_role = mysqli_fetch_array($roles)) 
    		{
    			$role_title = $row_role['title'];
    		}
    	}
    }

?>
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Update Item Form</h1></label>
			</div>
			<div class="col-md-12">

				<form name="test" method = "POST" autocomplete="off">

					<div class="row">

						<div class="col-md-2">
							<label>Id</label>
							<input type = "text" name = "up_id" value = "<?php echo $up_id; ?>" class = "form-control" readonly />
						</div>
						<div class="col-md-4">
							<label>Role</label>
							<input type = "text" name = "role_id" value = "<?php echo $role_title; ?>" class = "form-control" readonly />
						</div>
						<div class="col-md-6">
							<label>User Name</label>
							<input type="text" name="name" class="form-control" required value = "<?php echo $u_name; ?>" readonly />
						</div>

						<div class="col-md-2">
							<label for = "is_admin">Is Admin</label>
							<select name = "is_admin" class = "form-control" required>
							    <option value = "">SELECT ADMIN STATUS</option>
							    <option <?php if($is_admin == 1){echo 'SELECTED';}?> value = "1">NO</option>
							    <option <?php if($is_admin == 2){echo 'SELECTED';}?> value = "2">YES</option>
							</select>
						</div>
						<div class="col-md-4">
							<label for = "is_incharge">Is Incharge</label>
							<select name = "is_incharge" class = "form-control" required>
							    <option value = "">SELECT INCHARGE STATUS</option>
							    <option <?php if($is_incharge == 1){echo 'SELECTED';}?> value = "1">NO</option>
							    <option <?php if($is_incharge == 2){echo 'SELECTED';}?> value = "2">YES</option>
							</select>
						</div>
						<div class="col-md-6">
							<label>User Status</label>
							<select name = "status" class = "form-control" required>
							    <option value = "">SELECT STATUS</option>
							    <option <?php if($status == 1){echo 'SELECTED';}?> value = "1">ACTIVE</option>
							    <option <?php if($status == 2){echo 'SELECTED';}?> value = "2">CLOSED</option>
							</select>
						</div>

						<div class="col-md-12" style="margin: 20px 0px;">
						
							<input type="submit" name="update" value="UPDATE USER" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
							<a target="_blank" href="show_user.php" class="btn btn-info">SHOW USERS</a>
						</div>
					</div>

				</form>
			</div>    
<?php    
exit(0);
}
else
{
    header('location: show_user.php');
}
?>
	</div>
</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<!-- 
 -->
<?php mysqli_close($con); ?>