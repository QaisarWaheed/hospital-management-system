<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
if(!isset($_SESSION['mm_id']))
{
    header('location: logout.php');
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
		<h2 style="color: white;"><?php echo $company_name; ?></h2>
		<h6 style="color: brown;"><?php echo $company_ambition; ?></h6>
		<h3 style="color: red;"><?php echo $branch_name; ?></h3>
		<h4 style="color: white;"><?php echo $branch_address; ?></h4>
		<h4 style="color: white;"><?php echo $branch_phone; ?></h4>
		<h3 style="margin-top: 350px;text-align: center;">USER: <?php echo $_SESSION['mm_name']; ?></h3>
		<!--<h3 style="text-align: center;">USER: <?php print_r($_SESSION); ?></h3>-->
	</div>
			
	</div>
</div>

</body>
</html>