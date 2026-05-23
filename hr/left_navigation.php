<?php $dropdown_date = date('Y-m-d'); ?>
<nav>
	<ul id="nav_1">
		<li>
			<a style="cursor: pointer;" href='dashboard.php' class="">Dashboard</a>
		</li>

		<a>
		</a>
<?php if ($hr_is_admin == 2)
{ ?>
		<li>
			<a href="attendance_records.php">Attendance Register</a>
            <div class="dropdown show">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Attendance</a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item"  href = "print_attendance_daily.php?br_id=0&report_date=<?php echo $dropdown_date; ?>&generate_report=OPEN" >ORGANIZATION</a>
                    <a class="dropdown-item"  href = "print_attendance_daily.php?br_id=-1&report_date=<?php echo $dropdown_date; ?>&generate_report=OPEN" >ALL BRANCHES</a>
                    <?php
                    $dropdown_branch = "SELECT * FROM `branchs` WHERE `status` = '1' ";
                    $run_dropdown_branch = mysqli_query($con, $dropdown_branch);
                    if(mysqli_num_rows($run_dropdown_branch) > 0)
                    {
                        while($row_dropdown_branch = mysqli_fetch_array($run_dropdown_branch))
                        {
                            echo '<a class="dropdown-item" href = "print_attendance_daily.php?br_id='.$row_dropdown_branch['id'].'&report_date='.$dropdown_date.'&generate_report=OPEN">'.$row_dropdown_branch['tag_name'].'</a>';
                        }
                    }
                    ?>
                </div>
            </div>
		</li>
		<li>
			<a href="add_user.php">User</a>
		</li>
<?php }
if ($hr_is_incharge == 2 || $hr_is_admin == 2)
{ ?>
		<li>
			<a href="gynae_registeration.php">Gynae Register</a>
		</li>
		<li>
			<a href="gynae_registeration_summery.php">Gynae Register Summery</a>
		</li>
		<li>
			<a href="referred_patient_report.php">Referred Patient Report</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily.php' class="" title = "DOCTOR WISE">Progress Daily</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily_summery.php' class="" title = "DOCTOR WISE">Progress Daily Summery</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily_other_services.php' class="" title = "DOCTOR WISE">Progress Daily(OTHER SERVICES)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily_procedure.php' class="" title = "DOCTOR WISE">Progress Daily(OPD & PROCEDURE)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily_lab.php' class="" title = "DOCTOR WISE">Progress Daily(LAB)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily_gynae.php' class="" title = "DOCTOR WISE">Progress Daily(GYNAE)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_monthly.php' class="" title = "DOCTOR WISE">Monthly Progress(Doctor)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_monthly_gynae.php' class="" title = "GYNAE">Monthly Progress(Gynae)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_monthly_reception.php' class="" title = "Reception">Monthly Progress(Reception)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_monthly_pharmacy.php' class="" title = "Reception">Monthly Progress(Pharmacy)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='ycdo_phone_numbers.php' class="" title = "DOCTOR WISE">YCDO PHONE NO</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='employees.php' class="">EMPLOYEES</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='gynae_report.php' class="" title = "DOCTOR WISE">GYNAE REPORT</a>
		</li>
<?php }
if ($hr_is_incharge == 1 || $hr_is_admin == 1)
{ ?>
<?php } ?>
		<li>
			<a href="logout.php">Logout</a>
		</li>
	</ul>
</nav>
<?php include __DIR__ . '/../includes/nav_active_highlight.php'; ?>
