<nav>
	<ul id="nav_1">
		<li>
		    <!--<a href="#" onclick="location.reload()">Refresh (F5)</a>-->
			<!--<a target="_blank" href="patient_registeration.php"></a>-->
		</li>
		<li>
			<a accesskey="x" href="dashboard.php">X - Home</a>
		</li>
		<li>
			<a accesskey="1" href="patient_by_token.php">1 - Patient</a>
		</li>
		<li>
			<a accesskey="2" href="referral_patients.php">2 - Referral Patient</a>
		</li>
		<li>
			<a accesskey="3" href="gynae_registeration.php">3 - Gynae Register</a>
		</li>
		<li>
			<a accesskey="4" href="lab_report.php">4 - Lab Report</a>
		</li>
		<li>
			<a accesskey="5" href="usg.php">5 - USG</a>
		</li>
<?php if($is_admin == 2){ ?>
		<li>
			<a href="referred_patients.php">Referred Patient</a>
		</li>
		<!--<li>-->
		<!--	<a href="progress_report.php">Progress</a>-->
		<!--</li>-->
		<!--<li>-->
		<!--	<a target="_blank" href="branch_procedure_pendings.php">Procedure Turn</a>-->
		<!--</li>-->
		<!--<li>-->
		<!--	<a target="_blank" href="item_receive_branch.php">Receive Item In Branch</a>-->
		<!--</li>-->
		<!--<li>-->
		<!--	<a target="_blank" href="item_return_to_store.php">Return Item To Store</a>-->
		<!--</li>-->
		<!--<li>-->
		<!--	<a target="_blank" href="show_branch_stock_deemand.php">Show Branch Stock Deemand</a>-->
		<!--</li>-->
		<!--<li>-->
		<!--	<a href="return_token_full.php">Token ()</a>-->
		<!--</li>-->
<?php }	?>
		<li>
			<a href="logout.php">Logout</a>
		</li>
	</ul>
</nav>
<?php include __DIR__ . '/../includes/nav_active_highlight.php'; ?>
