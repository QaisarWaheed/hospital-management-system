<?php 
include 'includes/connect.php'; 
?>
	<title>Lab Dashboard - <?php echo $company_trademark; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
    .background_image{
        background-image: url('../images/background.png');
        background-size: cover;
    }
    </style>    
    <style>
        @media print {
            body {
                /* Reduce the base font size for the entire page to 12px */
                font-size: 12px; 
            }

            table {
                font-size: 0.8em; 
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body class="background_image">
<?php include 'top_navigation.php'; ?>
    <div class="row">
    	<div class="col-md-6 py-3">
    	    <div class = "row">

    	        <div class = "col">
                    <div class="card">
                        <div class="card-header text-center">Fill Daily Shift Over Performa</div>
                        <div class="card-body text-center">
                            <a href="daily_over_performa.php" class="btn btn-primary">
                                click here to open performa
                            </a>                    
                        </div>
                    </div>
    	        </div>
    	        <div class = "col">
                    <div class="card">
                        <div class="card-header text-center">Test Rate List</div>
                        <div class="card-body text-center">
                            <a href="lab_test_rate_list.php" class="btn btn-primary">
                                click here to open rate list
                            </a>                    
                        </div>
                    </div>
    	        </div>
    	        <div class = "col">
                    <div class="card">
                        <div class="card-header text-center">Fill Leave Performa</div>
                        <div class="card-body text-center">
                            <a href="performa_leave_application.php" class="btn btn-primary">
                                click here to open leave performa
                            </a>                    
                        </div>
                    </div>
    	        </div>
    	    </div>
    	</div>
    	<div class="col-md-6">
        	<div style="text-align: right;float: right;margin-right: 10px;">
        		<h2 style="color: white;"><?php echo $company_name; ?></h2>
        		<h6 style="color: brown;"><?php echo $company_ambition; ?></h6>
        		<h3 style="color: red;"><?php echo $lab_login_branch_id; ?></h3>
        		<h4 style="color: white;"><?php echo $lab_login_branch_address; ?></h4>
        		<h4 style="color: white;"><?php echo $lab_login_branch_phone; ?></h4>
        		<h4 style="color: white;">UAN : <?php echo $company_phone; ?></h4>
        		<h3 style="text-align: center;">USER: <?php echo $_SESSION['lab_user_name'];if($_SESSION['lab_login_is_incharge'] == 2){ echo " Incharge ";} ?>(<?php echo $role_title; ?>)</h3>
        	</div>
    	</div>
    </div>
</body>
</html>