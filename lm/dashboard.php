<?php 
include '../lab/includes/config.php';
include 'connect.php'; 
include '../lab/includes/head.php'; 
?>
	<link rel="stylesheet" type="text/css" href="../lab/css/nav_style.css"> 
	<title>Lab Dashboard - <?php echo $company_trademark; ?></title>
</head>
<body class="background_image">
    <div class="row" style="margin: 0px;">
    	<div class="col-md-12" style="text-align: center;background: lightgreen;">
    		<label><h1><?php echo $company_name; ?> </h1></label>
    	</div>
    	<div class="col-md-2 background_whitesmoke nodisplay_print">
    		<?php include 'left_navigation.php'; ?>
    	</div>
    	<div class="col-md-10">
        	<div style="text-align: right;float: right;margin-right: 10px;">
        		<h2 style="color: white;"><?php echo $company_name; ?></h2>
        		<h6 style="color: brown;"><?php echo $company_ambition; ?></h6>
        		<h3 style="color: red;"><?php echo $lab_manager_login_branch_id; ?></h3>
        		<h4 style="color: white;"><?php echo $lab_manager_login_branch_address; ?></h4>
        		<h4 style="color: white;">TEL : <?php echo $lab_manager_login_branch_phone; ?></h4>
        		<h4 style="color: white;">UAN : <?php echo $company_phone; ?></h4>
        		<h4 style="color: white;">IS ADMIN : <?php if($lab_manager_login_is_admin == 2){echo "YES"; }else{echo "NO"; } ?></h4>
        		<h4 style="color: white;">IS INCHARGE : <?php if($lab_manager_login_is_incharge == 2){echo "YES"; }else{echo "NO"; } ?></h4>
        		<h3 style="margin-top: 200px;text-align: center;">USER: <?php echo $_SESSION['lab_manager_user_name'];?>(LAB MANAGER)</h3>
        	</div>
    	</div>
    </div>
</body>
</html>