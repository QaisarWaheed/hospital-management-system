<nav class = "nodisplay_print">
	<ul id="nav_1">
		<li>
			<a href="dashboard.php">Home</a>
		</li>
<?php if($lab_admin_login_is_incharge == 2 && $lab_admin_login_is_incharge == 2) { ?>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily.php' title = "DOCTOR WISE">Progress Daily</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_monthly.php' title = "DOCTOR WISE">Progress Monthly</a>
		</li>
		<li>
			<a style="cursor: pointer;" href="lab_tests.php">Lab Tests</a>
		</li>
<?php } ?>		
		<li>
			<a style="cursor: pointer;" href="receive_purchase_item.php">Receive Purchase Item</a>
		</li>	
		<li>
			<a style="cursor: pointer;" href="issue_item_in_lab.php">Issue Stoke In Branch</a>
		</li>
		<li>
			<a href="issued_item_in_lab.php">Isuued Item In Lab</a>
		</li>
		<li>
			<a href="ycdo_phone_book.php">Ycdo Phone No</a>
		</li>
		<li>
			<a href="logout.php">Logout</a>
		</li>
	</ul>
</nav>
<?php include __DIR__ . '/../includes/nav_active_highlight.php'; ?>

