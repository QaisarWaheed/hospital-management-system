<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
?>
	<title>User Update - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
	    <div class = "row">
    	    <div class = "col"><a class = "btn btn-info" href = "dashboard.php">Dashboard</a></div>
    	    <div class = "col">YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</div>
	    </div>
	</div>
<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">

			<?php
if(isset($_POST['update']) && $_POST['update'] != '')
{
	$up_id = $_POST['up_id'];
	$password = md5($_POST['password']);

	$update = "UPDATE users SET
    password = '$password'
	WHERE id = '$up_id' ";

    if(mysqli_query($con, $update))
    {
    echo '<script>alert("Update User Password")</script>';
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
						<div class="col-md-6">
							<label>User Status</label>
							<input type="text" name="name" class="form-control" required value = "<?php if($status == 1){echo 'ACTIVE';}else{echo 'CLOSED';}?>" readonly />
						</div>
						<div class="col-md-6">
							<label>New Password</label>
							<input type="text" name="password" class="form-control" required />
						</div>

						<div class="col-md-12" style="margin: 20px 0px;">
						
							<input type="submit" name="update" value="UPDATE USER PASSWORD" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
							<a href="show_user.php" class="btn btn-info">SHOW USERS</a>
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
<?php mysqli_close($con); ?>