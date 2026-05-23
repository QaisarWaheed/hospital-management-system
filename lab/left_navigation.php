<nav class = "nodisplay_print">
	<ul id="nav_1">
		<li>
			<a href="dashboard.php">Home</a>
		</li>
		<li>
			<a href="patient_lab_by_token.php">Token(1st Turn)</a>
		</li>
		<li>
            <div class="dropdown show">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Test Records</a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <a class="dropdown-item"  href = "lab_test_received_samples.php" >Received Samples</a>
                    <a class="dropdown-item"  href = "lab_test_in_process.php" >Test In Process</a>
                    <a class="dropdown-item"  href = "lab_test_conducted.php" >Conducted</a>
                    <a class="dropdown-item"  href = "lab_test_approved_report.php" >Approved Report</a>
                    <a class="dropdown-item"  href = "lab_test_print_report.php" >Print Report</a>
                </div>
            </div>
		</li>
		<li>
			<a href="#">REPORT PRINT</a>
		</li>
<?php if($lab_login_is_incharge == 1 && $lab_login_is_incharge == 2) { ?>
		<li>
			<a href="referral_patients.php">Referral Patient</a>
		</li>
		<li>
			<a href="referal_tests.php">Referral Tests</a>
		</li>
		<!--<li>-->
		<!--	<a href="item_receive_in_lab.php">Receive Lab Item</a>-->
		<!--</li>-->
		<li>
			<a href="lab_tests.php">Lab Tests</a>
		</li>
<?php } ?>
<?php if($lab_login_is_incharge == 2 && $lab_login_is_incharge == 1) { ?>
		<li>
			<a href="referral_patients.php">Referral Patient</a>
		</li>
		<li>
			<a href="referal_tests.php">Referral Tests</a>
		</li>
		<!--<li>-->
		<!--	<a href="item_receive_in_lab.php">Receive Lab Item</a>-->
		<!--</li>-->
<?php } ?>
<?php if($lab_login_is_incharge == 2 && $lab_login_is_incharge == 2) { ?>
		<li>
			<a style="cursor: pointer;" href='progress_report_daily.php' class="" title = "DOCTOR WISE">Progress Daily</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='progress_report_monthly.php' class="" title = "DOCTOR WISE">Progress Monthly</a>
		</li>		
		<!--<li>-->
		<!--	<a href="item_receive_in_lab.php">Receive Lab Item</a>-->
		<!--</li>-->
		<li>
			<a href="referral_patients.php">Referral Patient</a>
		</li>
		<li>
			<a href="referal_tests.php">Referral Tests</a>
		</li>
		<!--<li>-->
		<!--	<a href="item_receive_in_lab.php">Receive Lab Item</a>-->
		<!--</li>-->
<?php } ?>

<?php if($lab_login_branch_id == 9) { ?>
		<li>
			<a href="lab_tests.php">Lab Tests</a>
		</li>
<?php } ?>
		<li>
			<a href="item_receive_lab.php">Receive Item In Lab</a>
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

