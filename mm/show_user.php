<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
?>
	<title>Show User - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">
		<table class="table table-hover table-bordered">
			<caption style="caption-side: top;text-align: center;">
				<h1>ALL USERS LIST</h1>
			</caption>
			<thead>
				<tr>
					<th>S NO</th>
					<th>User Name</th>
					<th>Role</th>
					<th>Branch</th>
					<th>IS ADMIN</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
<?php 
$s = 0;
$select = "SELECT * FROM users WHERE branch_id > '0' ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) {
	while ($row = mysqli_fetch_array($run)) {
		$s = $s + 1;
		$id = $row['id'];
		$is_admin = $row['is_admin'];
		if ($is_admin == 1) {$msg = "NO";}else{$msg = "YES";}
		$status = $row['status'];
		if ($status == 1) {$status_msg = "ACTIVE";}else{$status_msg = "CLOSED";}
		echo '
			<tr>
				<td>'.$s.'</td>
				<td>'.$row['u_name'].'</td>
				<td>'.get_role_title_by($row['role_id']).'</td>
				<td>'.get_branch_name_by($row['branch_id']).'</td>
				<td>'.$msg.'</th>
				<td>'.$status_msg.'</th>
            	<td><a href="update_user.php?up='.$id.'" class="btn btn-success btn-sm">Update</a>
			</tr>
		';
	}
}
?>
            </tbody>
		</table>
	</div>

</div>


</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>