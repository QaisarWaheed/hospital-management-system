<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<title>Lab Dashboard - <?php echo $company_trademark; ?></title>
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
</head>

<body class="background_image">
<?php include 'top_navigation.php'; ?>
<div class="row">
	<div class="col-md-12">
	    <table class = "table table-bordered">
	        <caption class = "text-center" style = "caption-side: top; color: black;">
	            <h2>YCDO STAFF PHONE BOOK</h2>
	        </caption>
	        <thead>
	            <tr>
	                <th>SR</th>
	                <th>NAME</th>
	                <th>DEPARTMENT</th>
	                <th>DUTY TIME</th>
	                <th>PHONE</th>
	                <th>BRANCH</th>
	            </tr>
	        </thead>
	        <tbody>
	    <?php
	    $sr = 0;
	    $select = "SELECT u_name, in_time, out_time, branchs.tag_name, users.phone, users.department_id FROM `users` INNER JOIN branchs ON users.branch_id = branchs.id WHERE users.u_name NOT LIKE '%SELF%' AND users.role_id = 3 AND users.status = '1' ORDER BY branch_id ";
	    $run = mysqli_query($con, $select);
	    if(mysqli_num_rows($run) > 0)
	    {
	        while($row = mysqli_fetch_array($run))
	        {
	            $sr++;
	            $department_id = $row['department_id'];
	            $select_department = "SELECT * FROM departments WHERE department_id = '$department_id' ";
        	    $run_department = mysqli_query($con, $select_department);
        	    if(mysqli_num_rows($run_department) > 0)
        	    {
        	        while($row_department = mysqli_fetch_array($run_department))
        	        {
        	            $department_title = $row_department['department_title'];
        	        }
        	    }
        	    if($department_id == 0){$department_title = 'DOCTOR / OPD'; }
	        ?>
	            <tr>
	                <td><?php echo $sr;?></td>
	                <td><?php echo $row['u_name'];?></td>
	                <td><?php echo $department_title; ?></td>
	                <td><?php echo $row['in_time'].' TO '.$row['out_time'];?></td>
	                <td><?php echo $row['phone'];?></td>
	                <td><?php echo $row['tag_name'];?></td>
	            </tr>
	        <?php
	        }
	    }
	    else
	    {
	        echo '<tr><td colspan = "5">'.$select.'</td></tr>';
	    }
	    ?>
	        </tbody>
	    </table>
	</div>
</div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>