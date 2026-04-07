<?php 
include 'includes/config.php'; 
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
	    <table class = "table table-bordered">
	        <caption>
	            
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

</body>
</html>