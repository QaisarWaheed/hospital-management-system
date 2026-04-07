		<nav class = "row">
		    <div class = "col">
		        <a class = "btn btn-sm btn-info" href = "dashboard.php">Dashboard</a>
		    </div>
<?php if($is_admin == 2){ ?>
		<div class = "col">
			<a style="cursor: pointer;" href='user_summary.php' class="btn btn-sm btn-info">Summary</a>
		</div>
		<div class = "col">
			<a style="cursor: pointer;" href='user_summary_login.php' class="btn btn-sm btn-info">Summary Login Wise</a>
		</div>
		<div class = "col">
			<a style="cursor: pointer;" href='progress_report.php' class="btn btn-sm btn-info">Progress Report</a>
		</div>
		<div class = "col">
			<a style="cursor: pointer;" href='report_account.php' class="btn btn-sm btn-info">Accounts Report</a>
		</div>
		<div class = "col">
			<a style="cursor: pointer;" href='account_summary.php' class="btn btn-sm btn-info">Accounts Summary</a>
		</div>
		<div class = "col">
			<a style="cursor: pointer;" href='user_complete_summary.php' class="btn btn-sm btn-info">Complete Summary</a>
		</div>
		<div class = "col">
			<a style="cursor: pointer;" href='general_pending.php' class="btn btn-sm btn-info">General Pending</a>
		</div>
		<div class = "col">
			<a style="cursor: pointer;" href='show_members.php' class="btn btn-sm btn-info">Member's Lg</a>
		</div>
		<div class = "col">
			<a style="cursor: pointer;" href='operate_pending.php' class="btn btn-sm btn-info">Operate Pending</a>
		</div>
		<div class = "col">
			<a style="cursor: pointer;" href='branchs_collection.php' class="btn btn-sm btn-info">Branch's Collection</a>
		</div>
		<div class = "col">
			<a style="cursor: pointer;" href='return_tokens.php' class="btn btn-sm btn-info">Return Tokens</a>
		</div>
<?php }	if($is_admin == 1){ ?>
	    <div class = "col">
	        <a class = "btn btn-sm btn-info" href = "general_pending.php">General Pending</a>
	    </div>
	    <div class = "col">
	        <a class = "btn btn-sm btn-info" href = "operate_pending.php">Operate Pending</a>
	    </div>
	    <div class = "col">
	        <a class = "btn btn-sm btn-info" href = "lg_operate_pending.php">Operate LG</a>
	    </div>
<?php }	?>		    
		    <div class = "col">
		        <a class = "btn btn-sm btn-info" href = "logout.php">Logout(<?php echo $_SESSION['fr_name']; ?>)</a>
		    </div>
		</nav>