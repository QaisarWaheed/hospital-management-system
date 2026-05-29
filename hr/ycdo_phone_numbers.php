<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
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
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	    <div class = "row">
	        <div class = "col-md-12">
	                <h2 style = "text-align: center;">ALL BRANCHES <?php echo $company_phone; ?></h2>
	            <?php
	            $select_branch = "SELECT * FROM `branchs` WHERE `status` = '1' ";
	            $run_branch = mysqli_query($con, $select_branch);
	            if(mysqli_num_rows($run_branch) > 0)
	            {
	                while($row_branch = mysqli_fetch_array($run_branch))
	                {
	                    $br_id = $row_branch['id'];
	                    $br_name = $row_branch['tag_name'];
	                    $br_address = $row_branch['address'];
	                    $br_phone = $row_branch['phone'];
	                    ?>
	                    <details>
                                  <summary style = "font-size: 24px;"><?php echo $br_name; ?> ( <?php echo $br_address; ?> ) <?php echo $br_phone; ?></summary>
                                  <table class = "table">
                                        <thead>
                                            <tr>
                                                <th>Ser</th>
                                                <th>Name</th>
                                                <th>Designation</th>
                                                <th>Duty Time</th>
                                                <th>Phone</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $s = 0;
                                        $select_user = "SELECT `u_name`, roles.title,`in_time`,`out_time`,`phone` FROM `users` INNER JOIN roles ON users.role_id = roles.id WHERE users.phone  > 0 AND users.status = '1' AND branch_id = '$br_id' AND roles.id IN (2, 3, 7, 8, 13, 14, 15) ORDER BY roles.id ";
                                        $run_user = mysqli_query($con, $select_user);
                        	            if(mysqli_num_rows($run_user) > 0)
                        	            {
                        	                while($row_user = mysqli_fetch_array($run_user))
                        	                {
                        	                    $s++;
                        	                    $user_name = $row_user['0'];
                        	                    $role_title = $row_user['1'];
                        	                    $user_in_time = $row_user['2'];
                        	                    $user_out_time = $row_user['3'];
                        	                    $user_phone = $row_user['4']; ?>
                        	                    <tr>
                        	                        <td><?php echo $s; ?></td>
                        	                        <td><?php echo $user_name; ?></td>
                        	                        <td><?php echo $role_title; ?></td>
                        	                        <td><?php echo date_format(date_create($user_in_time), "h:i:s A").' - '.date_format(date_create($user_out_time), "h:i:s A"); ?></td>
                        	                        <td><?php echo $user_phone; ?></td>
                        	                    </tr>
                    	           <?php     }
                        	            }
                                        ?>
                                        </tbody>
                                  </table>
                              </details>
           <?php     }
	            }
	            ?>
	        </div>
	    </div>
	</div>
</div>

</body>
</html>