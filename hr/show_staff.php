<?php 
include 'includes/connect.php';

if (isset($_GET['br_id']) && $_GET['br_id'] != '') {
    $br_id = $_GET['br_id'];
} else {
    $br_id = $hr_branch_id;
}

if (isset($_POST['update_staff_id']) && $_POST['update_staff_id'] != '') {
    $br_id = $_POST['br_id'] ?? $br_id;
    $update_staff_id = (int) $_POST['update_staff_id'];
    $update_staff_status = (int) $_POST['update_staff_status'];
    $query = "UPDATE `staff` SET `staff_status` = '$update_staff_status', `staff_deleted_by` = '$hr_id', `staff_deleted_at` = '$current_date' WHERE `staff_status` > 0 AND `staff_id` = '$update_staff_id' ";
    if (mysqli_query($con, $query)) {
        $activity_logs = "INSERT INTO `activity_logs`
        (`activity_log_id`, `user_id`, `activity_log_title`, `table_name`, `record_id`, `parameter_names`, `activity_log_new_value`, `activity_log_status`, `activity_logs_created_at`, `activity_log_location`, `ip_address`)
        VALUES
        (NULL, '$hr_id', 'UPDATE STAFF STATUS', 'staff', '$update_staff_id', 'staff_status', '$update_staff_status', '1', '$current_date', '', '$ip_address')";
        mysqli_query($con, $activity_logs);
        header('Location: show_staff.php?msg=upddate_status&br_id=' . urlencode((string) $br_id));
        exit;
    }
    header('Location: show_staff.php?br_id=' . urlencode((string) $br_id) . '&msg=error');
    exit;
}

if (isset($_POST['update_staff_branch_id']) && $_POST['update_staff_branch_id'] != '') {
    $update_staff_branch_id = (int) $_POST['update_staff_branch_id'];
    $update_br_id = $_POST['update_br_id'] ?? $br_id;
    $query = "UPDATE `staff` SET `branch_id` = '$update_br_id', `staff_deleted_by` = '$hr_id', `staff_deleted_at` = '$current_date' WHERE `staff_status` > 0 AND `staff_id` = '$update_staff_branch_id' ";
    if (mysqli_query($con, $query)) {
        $activity_logs = "INSERT INTO `activity_logs`
        (`activity_log_id`, `user_id`, `activity_log_title`, `table_name`, `record_id`, `parameter_names`, `activity_log_new_value`, `activity_log_status`, `activity_logs_created_at`, `activity_log_location`, `ip_address`)
        VALUES
        (NULL, '$hr_id', 'UPDATE STAFF BRANCH', 'staff', '$update_staff_branch_id', 'branch_id', '$update_br_id', '1', '$current_date', '', '$ip_address')";
        mysqli_query($con, $activity_logs);
        header('Location: show_staff.php?msg=upddate_branch&br_id=' . urlencode((string) $update_br_id));
        exit;
    }
    header('Location: show_staff.php?br_id=' . urlencode((string) $br_id) . '&msg=error');
    exit;
}

include 'includes/head.php';
?>
	<title>Show Staff - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

		<?php include 'navigation_top.php'; ?>
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
			        <thead>
			            <tr>
			                <th>S #</th>
			                <th>ID</th>
			                <th>NAME</th>
			                <th>S/O, D/O, W/O</th>
			                <th>IMAGE</th>
			                <th>PHONE</th>
			                <th>DESIGNATION</th>
			                <th>
			                    <form>
                                    <label>BRANCH</label>
                                    <select onchange="this.form.submit();" name = "br_id" class = "form-control" required>
                                        <?php
                                        if(!isset($_GET['br_id']))
                                        {
                                            echo '<option value = "'.$hr_branch_id.'">'.$hr_branch_address.'</option>';
                                        }
                            			    $query = "SELECT * FROM `branchs` WHERE `status` = '1' ";
                            			    $run = mysqli_query($con, $query);
                            			    if(mysqli_num_rows($run) > 0)
                            			    {
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
			                <th>IN TIME</th>
			                <th>OUT TIME</th>
			                <th>DUTY HOURS</th>
			                <th>STATUS</th>
			                <th>ACTION</th>
			            </tr>
			        </thead>
			        <tbody>
			        <?php 
			        $s = 0;
			        if(isset($_GET['br_id']) && $_GET['br_id'] != '')
			        {
			            $select = "SELECT * FROM `staff` LEFT JOIN branchs ON staff.branch_id = branchs.id INNER JOIN designations ON staff.designation_id = designations.designation_id INNER JOIN statuses ON staff.staff_status = statuses.staff_status_id WHERE staff_status > '0' AND staff.branch_id = '".$_GET['br_id']."' ORDER BY staff.branch_id ";
			        }
			        else
			        {
			            $select = "SELECT * FROM `staff` LEFT JOIN branchs ON staff.branch_id = branchs.id INNER JOIN designations ON staff.designation_id = designations.designation_id INNER JOIN statuses ON staff.staff_status = statuses.staff_status_id WHERE staff_status > '0' AND staff.branch_id = '$hr_branch_id' ORDER BY staff.branch_id ";
			        }
			        $run = mysqli_query($con, $select);
			        if(mysqli_num_rows($run) > 0)
			        {
			            while($row = mysqli_fetch_array($run))
			            {
			                $s++;
			                $staff_status = $row['staff_status'];
			                $staff_image_href = $row['staff_image_href'];
			            if($staff_image_href == '')
			            {
			                echo '
			             <tr>
			                <td>'.$s.'</td>
			                <td style = "text-align: left;">'.$row['staff_id'].'</td>
			                <td style = "text-align: left;">'.$row['staff_name'].'</td>
			                <td style = "text-align: left;">'.$row['staff_spouse'].'</td>
			                <td>
			                    <form action = "update_staff_image.php" method = "POST">
			                        <input type = "hidden" name = "staff_id" value = "'.$row['staff_id'].'" />
			                        <input type = "submit" name = "update_image" value = "UPLOAD" class = "btn btn-primary" />
			                    </form>
			                </td>
			                <td>'.$row['staff_phone'].'</td>
			                <td>'.$row['designation_title'].'</td>
			                <td>';
        			    $staff_query = "SELECT * FROM `branchs` WHERE `status` = '1' ";
        			    $staff_run = mysqli_query($con, $staff_query);
        			    if(mysqli_num_rows($staff_run) > 0)
        			    {
        			            echo '
        			            <form method = "POST" onSubmit="return confirm(\'Do you want to submit?\') ">
        			            <input type = "hidden" name = "update_staff_branch_id" value = "'.$row['staff_id'].'" />
        			            <select required name = "update_br_id" class = "form-control">';
        			            echo '<option value = "0">Organization Staff</option>';
        			        while($staff_row = mysqli_fetch_array($staff_run))
        			        {
        			            if($staff_row['id'] == $row['branch_id'])
        			            {
            			            echo '<option SELECTED value = "'.$staff_row['id'].'">'.$staff_row['address'].'</option>';
        			            }
        			            else
        			            {
            			            echo '<option value = "'.$staff_row['id'].'">'.$staff_row['address'].'</option>';
        			            }
        			        }
        			            echo '</select>
        			            <input type="submit" value = "CHANGE" />
        			            </form>';
        			    }
			                echo '</td>
			                <td>'.date_format(date_create($row['staff_time_in']), "h:i:s A").'</td>
			                <td>'.date_format(date_create($row['staff_time_out']), "h:i:s A").'</td>
			                <td>'.$row['staff_duty_hours'].'</td>
			                <td>';
        			    $staff_query = "SELECT * FROM `statuses` ";
        			    $staff_run = mysqli_query($con, $staff_query);
        			    if(mysqli_num_rows($staff_run) > 0)
        			    {
        			            echo '
        			            <form method = "POST">
        			            <input type = "hidden" name = "br_id" value = "'.$br_id.'" />
        			            <input type = "hidden" name = "update_staff_id" value = "'.$row['staff_id'].'" />
        			            <select id="select_option" onchange="this.form.submit();" required name = "update_staff_status" class = "form-control '.$row['staff_status_class'].'">';
        			        while($staff_row = mysqli_fetch_array($staff_run))
        			        {
        			            $staff_status_id = $staff_row['staff_status_id'];
        			            $staff_status_title = $staff_row['staff_status_title'];
        			            $staff_status_class = $staff_row['staff_status_class'];
        			            if($staff_status_id == $staff_status)
        			            {
            			            echo '<option class = "'.$staff_status_class.'" SELECTED value = "'.$staff_status_id.'">'.$staff_status_title.'</option>';
        			            }
        			            else
        			            {
            			            echo '<option value = "'.$staff_status_id.'">'.$staff_status_title.'</option>';
        			            }
        			        }
        			            echo '</select></form>';
        			    }
			                echo '</td>
			                <td>
			                    <a class = "btn btn-primary btn-sm" href = "print_staff.php?staff_id='.$row['staff_id'].'">PRINT</a>
			                    <a class = "btn btn-warning btn-sm" href = "update_staff.php?staff_id='.$row['staff_id'].'">UPDATE</a>
			                </td>
			             </tr>';
			            }
			            else
			            {
			                echo '
			             <tr>
			                <td>'.$s.'</td>
			                <td style = "text-align: left;">'.$row['staff_id'].'</td>
			                <td style = "text-align: left;">'.$row['staff_name'].'</td>
			                <td style = "text-align: left;">'.$row['staff_spouse'].'</td>
			                <td>
			                    <a target = "_blank" href = "https://ozsaphire.vpreps.com/images/staff/'.$row['staff_image_href'].'">
			                        <img loading = "lazy" width = "120" height = "50" src = "https://ozsaphire.vpreps.com/images/staff/'.$row['staff_image_href'].'" alt = "https://ozsaphire.vpreps.com/images/staff/'.$row['staff_image_href'].'" /></td>
			                    </a>
			                <td>'.$row['staff_phone'].'</td>
			                <td>'.$row['designation_title'].'</td>
			                <td>';
        			    $staff_query = "SELECT * FROM `branchs` WHERE `status` = '1' ";
        			    $staff_run = mysqli_query($con, $staff_query);
        			    if(mysqli_num_rows($staff_run) > 0)
        			    {
        			            echo '
        			            <form method = "POST" onSubmit="return confirm(\'Do you want to submit?\') ">
        			            <input type = "hidden" name = "update_staff_branch_id" value = "'.$row['staff_id'].'" />
        			            <select required name = "update_br_id" class = "form-control">';
        			            echo '<option value = "0">Organization Staff</option>';
        			        while($staff_row = mysqli_fetch_array($staff_run))
        			        {
        			            if($staff_row['id'] == $row['branch_id'])
        			            {
            			            echo '<option SELECTED value = "'.$staff_row['id'].'">'.$staff_row['address'].'</option>';
        			            }
        			            else
        			            {
            			            echo '<option value = "'.$staff_row['id'].'">'.$staff_row['address'].'</option>';
        			            }
        			        }
        			            echo '</select>
        			            <input type="submit" value = "CHANGE" />
        			            </form>';
        			    }
			                echo '</td>
			                <td>'.date_format(date_create($row['staff_time_in']), "h:i:s A").'</td>
			                <td>'.date_format(date_create($row['staff_time_out']), "h:i:s A").'</td>
			                <td>'.$row['staff_duty_hours'].'</td>
			                <td>';
        			    $staff_query = "SELECT * FROM `statuses` ";
        			    $staff_run = mysqli_query($con, $staff_query);
        			    if(mysqli_num_rows($staff_run) > 0)
        			    {
        			            echo '
        			            <form method = "POST">
        			            <input type = "hidden" name = "br_id" value = "'.$br_id.'" />
        			            <input type = "hidden" name = "update_staff_id" value = "'.$row['staff_id'].'" />
        			            <select id="select_option" onchange="this.form.submit();" required name = "update_staff_status" class = "form-control '.$row['staff_status_class'].'">';
        			        while($staff_row = mysqli_fetch_array($staff_run))
        			        {
        			            $staff_status_id = $staff_row['staff_status_id'];
        			            $staff_status_title = $staff_row['staff_status_title'];
        			            $staff_status_class = $staff_row['staff_status_class'];
        			            if($staff_status_id == $staff_status)
        			            {
            			            echo '<option class = "'.$staff_status_class.'" SELECTED value = "'.$staff_status_id.'">'.$staff_status_title.'</option>';
        			            }
        			            else
        			            {
            			            echo '<option value = "'.$staff_status_id.'">'.$staff_status_title.'</option>';
        			            }
        			        }
        			            echo '</select></form>';
        			    }
			                echo '</td>			                
			                <td>
			                    <a class = "btn btn-primary btn-sm" href = "print_staff.php?staff_id='.$row['staff_id'].'">PRINT</a>
			                    <a class = "btn btn-warning btn-sm" href = "update_staff.php?staff_id='.$row['staff_id'].'">UPDATE</a>
			                </td>
			             </tr>
			                ';
			            }
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