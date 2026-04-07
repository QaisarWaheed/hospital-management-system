<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
?>
	<title>Show User - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">
		<?php include 'navigation_top.php'; ?>
<div>

	<div class="" style="margin: 10px 15px;">
		<table class="table table-hover table-bordered" id="myTable">
			<caption style="caption-side: top;text-align: center;">
				<h1>ALL USERS LIST</h1>
			</caption>
			<thead>
				<tr>
                    <th colspan = "11">
                        <input type="text" class = "form-control" id="myInput" onkeyup="myFunction()" placeholder="Search for names.." title="Type in a name">
                    </th>
				</tr>
				<tr>
					<th>Sr.</th>
					<th>User Name</th>
					<th>Job Hours</th>
					<th>Role</th>
					<th>Branch</th>
					<th>ADMIN</th>
					<th>INCHARGE</th>
					<th>Qualification</th>
					<th>Phone</th>
					<th>Status</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
<?php 
$s = 0;
$select = "SELECT users.id AS user_id, is_admin, qualification, users.phone, in_time, out_time, is_incharge, users.status AS user_status, u_name, title, tag_name FROM users INNER JOIN roles ON users.role_id = roles.id INNER JOIN branchs ON users.branch_id = branchs.id WHERE users.branch_id > '0' ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) {
	while ($row = mysqli_fetch_array($run)) {
		$s = $s + 1;
		$id = $row['user_id'];
		$is_admin = $row['is_admin'];
		$qualification = $row['qualification'];
		$phone = $row['phone'];
		if($row['in_time'] != '00:00:00' && $row['in_time'] != '00:00:00')
		{
    		$job_hours = date_format(date_create($row['in_time']), "h:i:s A") . " TO " . date_format(date_create($row['out_time']), "h:i:s A");
		}
		else
		{
		    
    		$job_hours = "NOT SET";
		}
		if ($is_admin == 1) {$msg = "NO";}else{$msg = "YES";}
		$is_incharge = $row['is_incharge'];
		if ($is_incharge == 1) {$msg_incharge = "NO";}else{$msg_incharge = "YES";}
		$status = $row['user_status'];
		if ($status == 1) {$status_msg = "ACTIVE";}else{$status_msg = "CLOSED";}
		echo '
			<tr>
				<td>'.$s.'</td>
				<td>'.$row['u_name'].'</td>
				<td>'.$job_hours.'</td>
				<td>'.($row['title']).'</td>
				<td>'.($row['tag_name']).'</td>
				<td>'.$msg.'</th>
				<td>'.$msg_incharge.'</td>
				<td>'.$qualification.'</td>
				<td>'.$phone.'</td>
				<td>'.$status_msg.'</td>
            	<td>
                	<a href="update_user.php?up='.$id.'" class="btn btn-success btn-sm">Update</a>
                	<a href="update_user_password.php?up='.$id.'" class="btn btn-info btn-sm">Password</a>
                </td>
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
<script>
function myFunction() 
{
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) 
    {
        user_name = tr[i].getElementsByTagName("td")[1];
        if (user_name) 
        {
            txtValue = user_name.textContent || user_name.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) 
            {
                tr[i].style.display = "";
            } 
            else 
            {
                tr[i].style.display = "none";
            }
        }       
    }
}
</script>
<?php mysqli_close($con); ?>