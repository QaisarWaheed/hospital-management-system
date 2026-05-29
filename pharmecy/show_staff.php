<?php 
    include 'includes/connect.php'; 
    if($is_admin != 2)
    {
        header('location: logout.php'); 
    }
?>
<?php include 'includes/head.php'; ?>
	<title>Add Staff - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
	    <div class = "row">
    	    <div class = "col"><a class = "btn btn-info" href = "dashboard.php">Dashboard</a></div>
    	    <div class = "col">YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</div>
	    </div>
	</div>
	<div class="" style="margin: 10px 15px;">
		<div class="row">
			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>
			<div class="col-md-12" style="text-align: center;">
			    <table class = "table table-hover table-bordered">
			        <caption style = "caption-side: top;color: black;" class = "text-center fs-2 fw-bold">
			            <label>ALL STAFF REGISTER IN BRANCH</label>
			        </caption>
			        <thead>
			            <tr class="text-center">
			                <th>S #</th>
			                <th>NAME</th>
			                <th>S/O, D/O, W/O</th>
			                <th>PHONE</th>
			                <th>DESIGNATION</th>
			                <th>BRANCH</th>
			                <th>IN TIME</th>
			                <th>OUT TIME</th>
			                <th>DUTY HOURS</th>
			            </tr>
			        </thead>
			        <tbody>
			        <?php 
			        $s = 0;
			        $select = "SELECT * FROM `staff` INNER JOIN branchs ON staff.branch_id = branchs.id INNER JOIN designations ON staff.designation_id = designations.designation_id WHERE staff.branch_id = '$branch_id' ";
			        $run = mysqli_query($con, $select);
			        if(mysqli_num_rows($run) > 0)
			        {
			            while($row = mysqli_fetch_array($run))
			            {
			                $s++;
			                echo '
			             <tr>
			                <td>'.$s.'</td>
			                <td>'.$row['staff_name'].'</td>
			                <td>'.$row['staff_spouse'].'</td>
			                <td>'.$row['staff_phone'].'</td>
			                <td>'.$row['designation_title'].'</td>
			                <td>'.$row['tag_name'].'</td>
			                <td>'.date_format(date_create($row['staff_time_in']), "h:i:s A").'</td>
			                <td>'.date_format(date_create($row['staff_time_out']), "h:i:s A").'</td>
			                <td>'.$row['staff_duty_hours'].'</td>
			             </tr>
			                ';
			            }
			        }
			        else
			        {
			            echo '<tr><td colspan = "9">NO RECORDS FOUND</td></tr>';
			        }
			        ?>
			        </tbody>
			    </table>
			</div>
		</div>

	</div>
</div>


</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<?php mysqli_close($con); ?>