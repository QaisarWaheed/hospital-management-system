<nav>
	<ul id="nav_1">
		<li>
			<a style="cursor: pointer;" href='dashboard.php' class="">Dashboard</a>
		</li>
		<li>
			<a href="gynae_registeration.php">Gynae Register</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily_gynae.php' class="" title = "DOCTOR WISE">Progress - Gynae</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily.php' class="" title = "DOCTOR WISE">Progress Daily(GYNAE)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_monthly_gynae.php' class="" title = "DOCTOR WISE">Progress Monthly - Gynae</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='doctor_monthly_profile.php' class="">Doctor Monthly Profile</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily_branch.php' class="" title = "DOCTOR WISE">Progress Daily</a>
		</li>
		
<?php if($bk_is_admin == 1 && $bk_is_incharge == 2){ ?>
		<li>
			<a href="referred_patient_report.php">Referred Patient Report</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily_branch.php' class="" title = "DOCTOR WISE">Progress Daily</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='user_summary.php' class="">Summary</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='user_summary_login.php' class="">Summary Login Wise</a>
		</li>
<?php }	?>
<?php if($bk_is_admin == 2 && $bk_is_incharge == 2){ ?>
		<li>
			<a style="cursor: pointer;" href='progress_report.php' class="" title = "BRANCH WISE">Progress</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily_branch_time.php' class="" title = "DOCTOR WISE">Progress Daily Time</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_monthly.php' class="" title = "DOCTOR WISE">Progress Monthly</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='user_summary.php' class="">Summary</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='user_summary_login.php' class="">Summary Login Wise</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='doctor_prescription_token_wise.php' class="">Doctor Prescriptions</a>
		</li>
<?php }	?>
		<li>
			<a href="logout.php">Logout</a>
		</li>
	</ul>
</nav>
