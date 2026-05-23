<nav>
	<ul id="nav_1">
		<li>
			<a style="cursor: pointer;" href="dashboard.php">Dashboard</a>
		</li>
		<li>
			<a href="gynae_registeration.php">Gynae Register</a>
		</li>
		<li>
			<a href="progress_report_daily_gynae.php" title="DOCTOR WISE">Progress - Gynae</a>
		</li>
		<li>
			<a href="progress_report_daily.php" title="DOCTOR WISE">Progress Daily(GYNAE)</a>
		</li>
		<li>
			<a href="progress_report_monthly_gynae.php" title="DOCTOR WISE">Progress Monthly - Gynae</a>
		</li>
		<li>
			<a href="doctor_monthly_profile.php">Doctor Monthly Profile</a>
		</li>
		<li>
			<a href="progress_report_daily_branch.php" title="DOCTOR WISE">Progress Daily</a>
		</li>
		<li>
			<a href="progress_report.php" title="BRANCH WISE">Progress</a>
		</li>
		<li>
			<a href="progress_report_daily_branch_time.php" title="DOCTOR WISE">Progress Daily Time</a>
		</li>
		<li>
			<a href="progress_report_monthly.php" title="DOCTOR WISE">Progress Monthly</a>
		</li>
		<li>
			<a href="progress_report_monthly_doctors.php" title="DOCTOR WISE">Progress Monthly(Doctors)</a>
		</li>
		<li>
			<a href="progress_report_monthly_time.php" title="PAGINATED MONTHLY">Progress Monthly(TIME)</a>
		</li>
		<li>
			<a href="comparison_report.php">Comparison Report</a>
		</li>
		<li>
			<a href="user_summary.php">Summary</a>
		</li>
		<li>
			<a href="user_summary_login.php">Summary Login Wise</a>
		</li>
		<li>
			<a href="doctor_prescription_token_wise.php">Doctor Prescriptions</a>
		</li>
<?php if ($bk_is_admin == 2 || $bk_is_incharge == 2) { ?>
		<li>
			<a href="referred_patient_report.php">Referred Patient Report</a>
		</li>
<?php } ?>
		<li>
			<a href="logout.php">Logout</a>
		</li>
	</ul>
</nav>
<script>
(function () {
    var page = window.location.pathname.split('/').pop().split('?')[0].toLowerCase();
    if (!page) {
        return;
    }
    document.querySelectorAll('#nav_1 a[href]').forEach(function (link) {
        var href = link.getAttribute('href');
        if (!href || href.indexOf('javascript:') === 0) {
            return;
        }
        var target = href.split('/').pop().split('?')[0].toLowerCase();
        if (target && target === page) {
            link.classList.add('active');
            if (link.parentElement && link.parentElement.tagName === 'LI') {
                link.parentElement.classList.add('active');
            }
        }
    });
})();
</script>
