<?php 
include 'includes/connect.php'; 
?>
<?php include 'includes/head.php'; 

$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if(mysqli_num_rows($run_roles) == 1)
{
    while($row_role = mysqli_fetch_array($run_roles))
    {
        $role_title = $row_role['title'];
    }
}
else
{
    $role_title = '';
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	<div style="text-align: right;float: right;margin-right: 10px;">
		<!--<h2 class = "bg-danger">PATIENT MEDICINE TURN SUSPENDED DUE TO SOME ISUEES. PLEASE DO NOT PANIK</h2>-->
		<h2 style="color: white;"><?php echo $company_name; ?></h2>
		<h6 style="color: brown;"><?php echo $company_ambition; ?></h6>
		<h3 style="color: red;"><?php echo $branch_name; ?></h3>
		<h4 style="color: white;"><?php echo $branch_address; ?></h4>
		<h4 style="color: white;"><?php echo $branch_phone; ?></h4>
		<h4 style="color: white;">UAN : 0304-1110222</h4>
		<h3 style="margin-top: 200px;text-align: center;">USER: <?php echo $_SESSION['dr_name'];if($_SESSION['is_incharge'] == 2){ echo " Incharge ";} ?>(<?php echo $role_title; ?>)</h3>
	</div>
			
	</div>
</div>

</body>
</html>
<?php mysqli_close($con); ?>