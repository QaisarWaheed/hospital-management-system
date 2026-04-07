<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
?>
	<title>Lab Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke nodisplay_print">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	<div style="text-align: right;float: right;margin-right: 10px;">
		<h2 style="color: white;"><?php echo $company_name; ?></h2>
		<h6 style="color: brown;"><?php echo $company_ambition; ?></h6>
		<h3 style="color: red;"><?php echo $lab_login_branch_id; ?></h3>
		<h4 style="color: white;"><?php echo $branch_address; ?></h4>
		<h4 style="color: white;"><?php echo $branch_phone; ?></h4>
		<h4 style="color: white;">UAN : <?php echo $company_phone; ?></h4>
		<h3 style="margin-top: 200px;text-align: center;">USER: <?php echo $_SESSION['lab_admin_user_name'];if($_SESSION['lab_admin_login_is_incharge'] == 2){ echo " Incharge ";} ?>(<?php echo $role_title; ?>)</h3>
	</div>
			
	</div>
</div>

</body>
</html>