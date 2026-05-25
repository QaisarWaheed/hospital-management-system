<?php
include '../lab/includes/config.php';
include 'connect.php';
include '../lab/includes/head.php';
?>
	<link rel="stylesheet" type="text/css" href="../lab/css/nav_style.css">
	<title>Verify Item Issuance - <?php echo htmlspecialchars($company_trademark, ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body class="background_image">
    <div class="row" style="margin: 0px;">
    	<div class="col-md-12" style="text-align: center;background: lightgreen;">
    		<label><h1><?php echo htmlspecialchars($company_name, ENT_QUOTES, 'UTF-8'); ?></h1></label>
    	</div>
    	<div class="col-md-2 background_whitesmoke nodisplay_print">
    		<?php include 'left_navigation.php'; ?>
    	</div>
    	<div class="col-md-10">
    		<h2 style="color: white; margin-top: 40px;">Verify Item Issuance</h2>
    		<p style="color: white; font-size: 18px;">This feature is coming soon.</p>
    	</div>
    </div>
</body>
</html>
