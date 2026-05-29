<?php 
    include 'includes/connect.php'; 
    include 'includes/head.php'; 
if(isset($_GET['br_id']) && $_GET['br_id'] != '')
{
    $br_id = $_GET['br_id'];   
}
else
{
    $br_id = $branch_id;    
}
?>
	<title>Show Staff - <?php echo $company_trademark; ?></title>
<style>
@media print{    
    .print_size
    {
        background-color: #FFFFFF;
        font-size: 0.5em;
    }
}    
</style>
</head>

<body class="background_image_ycdo print_size">
<div>
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
	    <div class = "row">
    	    <div class = "col"><a class = "btn btn-info" href = "dashboard.php">Dashboard</a></div>
    	    <div class = "col">YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</div>
	    </div>
	</div>
	<div class="" style="margin: 10px 15px;">
		<div class="row">
			<div class="col-md-12" style="text-align: center;">
			    <table class = "table table-hover table-bordered">
			        <thead>
    			        <tr class  = "d-print-none">
    			            <th colspan = "6"></th>
    			            <th colspan = "3">
    		                    <form>
                                    <select onchange="this.form.submit();" name = "br_id" class = "form-control" required>
                                        <?php
                                        if(!isset($_GET['br_id']))
                                        {
                                            echo '<option value = "'.$branch_id.'">'.$branch_address.'</option>';
                                        }
                            			    $query = "SELECT * FROM `branchs` WHERE `status` = '1' ";
                            			    $run = mysqli_query($con, $query);
                            			    if(mysqli_num_rows($run) > 0)
                            			    {
                    			                echo '<option value = "-1">ALL STAFF</option>';
                    			                echo '<option value = "0">ORGANIZATION STAFF</option>';
                            			        while($row = mysqli_fetch_array($run))
                            			        {
                            			            $id = $row['id'];
                            			            $address = $row['address'];
                            			            $tag_name = $row['tag_name'];
                            			            if($id == $_GET['br_id'])
                            			            {
                            			                echo '<option SELECTED value = "'.$id.'">'.$tag_name.'</option>';
                            			            }
                            			            else
                            			            {
                            			                echo '<option value = "'.$id.'">'.$tag_name.'</option>';
                            			            }
                            			        }
                            			    }
                                        ?>
                                    </select>
                                </form>
    			            </th>
    			            <th colspan = "2">
    			                <form method = "GET" action = "generate_salary_slip.php">
    			                    <div class = "row">
    			                        <div class = "col">
    			                            <input class = "form-control" type = "month" value = "<?php echo date('Y-m'); ?>" name = "for_the_month" />
    			                        </div>
    			                        <div class = "col">
    			                            <input  type = "submit" name = "generate_salary_for_the_month" value = "GENERATE" class = "btn btn-info" />
    			                            <input formaction = "print_salary_slip.php" type = "submit" value = "PRINT" name = "print_salary_for_the_month" class = "btn btn-primary" />
    			                        </div>
    			                    </div>
    			                </form>
    			            </th>
    			        </tr>
			            <tr>
			                <th>S #</th>
			                <th>ID</th>
			                <th>NAME</th>
			                <th>S/O, D/O, W/O</th>
			                <th>PHONE</th>
			                <th>DESIGNATION</th>
			                <th>
                                <label>BRANCH</label>
			                </th>
			                <th>IN TIME</th>
			                <th>OUT TIME</th>
			                <th>DUTY HOURS</th>
			                <th class = "d-print-none">ACTION</th>
			            </tr>
			        </thead>
			        <tbody>
			        <?php 
			        $s = 0;
			        if(isset($_GET['br_id']) && $_GET['br_id'] == '-1')
			        {
			            $select = "SELECT * FROM `staff` LEFT JOIN branchs ON staff.branch_id = branchs.id INNER JOIN designations ON staff.designation_id = designations.designation_id INNER JOIN statuses ON staff.staff_status = statuses.staff_status_id WHERE staff_status = '1' ORDER BY staff.branch_id, staff.designation_id ";
			        }
			        elseif(isset($_GET['br_id']) && $_GET['br_id'] != '')
			        {
			            $select = "SELECT * FROM `staff` LEFT JOIN branchs ON staff.branch_id = branchs.id INNER JOIN designations ON staff.designation_id = designations.designation_id INNER JOIN statuses ON staff.staff_status = statuses.staff_status_id WHERE staff_status = '1' AND staff.branch_id = '".$_GET['br_id']."' ORDER BY staff.branch_id, staff.designation_id ";
			        }
			        else
			        {
			            $select = "SELECT * FROM `staff` LEFT JOIN branchs ON staff.branch_id = branchs.id INNER JOIN designations ON staff.designation_id = designations.designation_id INNER JOIN statuses ON staff.staff_status = statuses.staff_status_id WHERE staff_status = '1' AND staff.branch_id = '$branch_id' ORDER BY staff.branch_id, staff.designation_id ";
			        }
			        $run = mysqli_query($con, $select);
			        if(mysqli_num_rows($run) > 0)
			        {
			            while($row = mysqli_fetch_array($run))
			            {
			                $s++;
			                $staff_status = $row['staff_status'];
			                $staff_image_href = $row['staff_image_href'];
			                echo '
			             <tr>
			                <td>'.$s.'</td>
			                <td style = "text-align: left;">'.$row['staff_id'].'</td>
			                <td style = "text-align: left;">'.$row['staff_name'].'</td>
			                <td style = "text-align: left;">'.$row['staff_spouse'].'</td>
			                <td>'.$row['staff_phone'].'</td>
			                <td>'.$row['designation_title'].'</td>
			                <td>'.$row['tag_name'].'</td>
			                <td>'.date_format(date_create($row['staff_time_in']), "h:i:s A").'</td>
			                <td>'.date_format(date_create($row['staff_time_out']), "h:i:s A").'</td>
			                <td>'.$row['staff_duty_hours'].'</td>
			                <td class = "d-print-none">
			                    <a class = "btn btn-primary btn-sm" href = "print_staff.php?staff_id='.$row['staff_id'].'">PRINT</a>
			                </td>
			             </tr>';
			            }
			        }
			        else
			        {
			            echo '<tr><td colspan = "9">NO RECORDS FOUND</td></tr>';
			        }
			        ?>
			        </tbody>
			                    <!--<a class = "btn btn-warning btn-sm" href = "update_staff.php?staff_id='.$row['staff_id'].'">UPDATE</a>-->
			    </table>
			</div>
		</div>

	</div>
</div>


</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<?php mysqli_close($con); ?>