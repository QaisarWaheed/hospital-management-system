<nav>
	<ul id="nav_1">
		<li>
			<a style="cursor: pointer;" href='dashboard.php' class="">Dashboard</a>
		</li>
		<li>
			<a href="duplicate_token.php">Duplicate Token</a>
		</li>

		<li>
			<a href="ycdo_phone_numbers.php">Ycdo Phone Book</a>
		</li>
<?php if($is_admin == 2){ ?>
		<li>
			<a style="cursor: pointer;" href='notifications.php' class="">Notifications</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='user_summary.php' class="">Summary</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='user_summary_time.php' class="">Summary Time</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='user_summary_login.php' class="">Summary Login Wise</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report.php' class="">Progress Report</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily_lab.php' class="" title = "DOCTOR WISE">Progress Daily(LAB)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='report_account.php' class="">Accounts Report</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='report_month.php' class="">Month Report</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='account_summary.php' class="">Accounts Summary</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='user_complete_summary.php' class="">Complete Summary</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='general_pending.php' class="">General Pending</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='operate_pending.php' class="">Operate Pending</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='branchs_collection.php' class="">Branch's Collection</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='branchs_summery.php' class="">Branch's Summery</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='return_tokens.php' class="">Return Tokens</a>
		</li>
		<span class = "h5 bg-primary">Account Section</span>
		<li>
			<a style="cursor: pointer;" href='accounts_monthly_report.php' class="">Monthly Report</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='doctor_monthly_profile.php' class="">Doctor Monthly Profile</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='show_members.php' class="">MEMBERS LG</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='parties_account.php' class="">Parties LG</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='lg_operate_pending.php' class="">Operate LG</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='lg_general_pending.php' class="">General Pending LG</a>
		</li>
		<li>
		<li>
			<a style="cursor: pointer;" href='lg_general_pending_detail.php' class="">General Pending Detail</a>
		</li>
			<a style="cursor: pointer;" href='comparision_all_branches.php' class="">Comparision All Branches</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='recepits.php' class=""> Receipt Book</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='fr_collection.php' class=""> FR COLLECTION</a>
		</li>
<?php }	if($is_admin == 1){ ?>
		<li>
			<a style="cursor: pointer;" href='general_pending.php' class="">General Pending</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='general_pending_detail.php' class="">General Pending Detail</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='operate_pending.php' class="">Operate Pending</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='lg_operate_pending.php' class="">Operate LG</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='lg_general_pending.php' class="">General Pending LG</a>
		</li>
<?php }	?>
		<li>
			<a style="cursor: pointer;" href='show_staff.php' class="">Employees</a>
		</li>
		<li>
			<a href="logout.php">Logout</a>
		</li>
	</ul>
</nav>
<?php include __DIR__ . '/../includes/nav_active_highlight.php'; ?>
