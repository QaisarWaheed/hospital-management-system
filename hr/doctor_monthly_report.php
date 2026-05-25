<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
$br_id = $hr_branch_id;
if(!isset($_SESSION['hr_id']))
{
    header('location: logout.php');
}

?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
	    <div class = "row">
    	    <div class = "col"><a class = "btn btn-info" href = "dashboard.php">Dashboard</a></div>
    	    <div class = "col">YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</div>
	    </div>
	</div>
	<div class="col-md-12 bg-success">
	    <table class = "table table-bordered table-hover">
	        <caption>
	            
	        </caption>
	        <thead>
	            <tr>
	                <th>
	                    <input required type = "month" value = "<?php echo date('Y-m'); ?>" name = "select_month" id = "select_month" class = "form-control" />
	                </th>
	                <th>
	                    <select required name = "doctor_id" class = "form-control" id = "doctor_id">
	                        <?php
	                        $select = "SELECT * FROM users WHERE status = '1' AND role_id = '3' AND branch_id = '$hr_branch_id' ";
	                        $run = mysqli_query($con, $select);
	                        if(mysqli_num_rows($run) > 0)
	                        {
	                            while($row = mysqli_fetch_array($run))
	                            {
	                                $user_id = $row['id'];
	                                $user_name = $row['u_name'];
	                                echo '<option value = "'.$user_id.'">'.$user_name.'</option>';
	                            }
	                        }
	                        else
	                        {
	                            echo '<option>NO DATA FOUND</option>';
	                        }
	                        ?>
	                    </select>
	                </th>
	            </tr>
	            <tr>
	                <th>DATE</th>
	                <th>OPD</th>
	                <th>OPD(REFERREAL)</th>
	                <th>USG</th>
	                <th>ADMISSION</th>
	                <th>PROCEDURE</th>
	                <th>LAB</th>
	                <th>REFERRED TO</th>
	            </tr>
	        </thead>
	        <tbody>
	        <?php
	        $s = 0;
	        ?>
	        </tbody>
</table>
	</div>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
<script src="js/bootstrap.js"></script>
</body>
</html>
<?php mysqli_close($con); ?>