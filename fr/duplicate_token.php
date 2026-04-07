<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 

?>
	<title>Duplicate Token - <?php echo $company_trademark; ?></title>
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
    	<div style="">
    	    <form METHOD="GET" action = "action_duplicate_token.php">
    	        <div class="row">
    	            <div class="col-md-12">
    	                <h3 align="center">ENTER DUPLICATE TOKEN NO</h3>
    	            </div>
    	            <div class="col-md-10">
    	                <input type="number" min="1" max="<?php echo next_tokan_no()-1; ?>" name="token_no" class="form-control" />
    	            </div>
    	            <div class="col-md-2">
    	                <input type="submit" value="SEARCH" name="duplicate" class="btn btn-info btn-sm" />
    	            </div>
    	        </div>
    	    </form>
    	</div>
	</div>
</div>

</body>
</html>
<?php mysqli_close($con); ?>