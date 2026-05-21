<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; ?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO</h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	<div style="text-align: right;float: right;margin-right: 10px;">
		<h2 style="color: white;"><?php echo $company_name; ?></h2>
		<h6 style="color: brown;"><?php echo $company_ambition; ?></h6>
		<h3 style="color: red;"><?php echo $branch_name; ?></h3>
		<form method = "POST" action = "update_branch_session.php">
		    <select onchange = "this.form.submit()" name = "change_branch_id" class = "form-control">
		        <?php
		        $select_branch = "SELECT * FROM branchs WHERE status = '1' ";
		        $run_branch = mysqli_query($con, $select_branch);
		        if(mysqli_num_rows($run_branch) > 0)
		        {
		            while($row_branch = mysqli_fetch_array($run_branch))
		            {
		                $br_id = $row_branch['id'];
		                $br_address = $row_branch['address'];
		                if($br_id == $branch_id)
		                {
    		                echo '<option SELECTED value = "'.$br_id.'">'.$br_address.'</option>';
		                }
		                else
		                {
    		                echo '<option value = "'.$br_id.'">'.$br_address.'</option>';
		                }
		            }
		        }
		        ?>
		    </select>
		</form>
		<h4 style="color: white;"><?php echo $branch_address; ?></h4>
		<h4 style="color: white;"><?php echo $branch_phone; ?></h4>
		<h3 style="margin-top: 350px;text-align: center;">USER: <?php echo $_SESSION['fr_name']; ?></h3>
	</div>
			
	</div>
</div>

</body>
</html>