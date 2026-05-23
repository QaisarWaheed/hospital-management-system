<nav>
	<ul id="nav_1">
		<li>
			<a href="dashboard.php">Home</a>
		</li>

<?php if($user_id == 1){ ?>
		<li>
			<a href="print_summery_by_admin.php">Print Summery</a>
		</li>
		<li>
			<a href="testing_turn.php">Testing Turn</a>
		</li>
		<li>
			<a href="return_token_full.php">Return Token (Full)</a>
		</li>	
		<li>
			<a href="admission_form_for_drug_addicts.php">Admission Form Drug Addicts</a>
		</li>	
<?php } ?>
		<li>
			<a onclick = "showProgress(); return true;" href="referral_turn.php">Referral Turn</a>
		</li>
		<li>
			<a href="referred_patients.php">Referrad Patient</a>
		</li>
		<li>
			<a href="patient_registeration.php">Patient Registeration</a>
		</li>
		<li>
			<a href="patient_registeration_complete.php">Patient Registeration(COMPLETE)</a>
		</li>
		<li>
			<a href="testing_second_turn.php">Second Turn(Medicine)</a>
		</li>
		<!--<li>-->
		<!--	<a href="lab_second_turn.php">Second Turn(Lab)</a>-->
		<!--</li>-->
		<li>
			<a href="duplicate_token.php">Duplicate Token</a>
		</li>
		<li>
			<a href="second_turn_by_doctor.php">Doctor Turn</a>
		</li>	
<?php if($is_admin == 2){ ?>			
		<li>
			<a href="second_turn_pending.php">Second Turn(Pending)</a>
		</li>
<?php } ?>	
		<li>
			<a href="donation_collection.php">Donation Collection</a>
		</li>
		<li>
			<a href="gynae_registeration.php">Gynae Register</a>
		</li>
<?php 
// if($branch_id == 9)
// { 
?>
		<li>
			<a href="item_return_to_store.php">Return Item To Store</a>
		</li>
<?php 
// }	
?>
<?php if($is_incharge == 2){ ?>
		<li>
			<a href="item_receive_branch.php">Receive Item In Branch</a>
		</li>
		<li>
			<a href="deserving_medicines.php">Deserving Medicines</a>
		</li>
<?php }	?>
<?php if($is_admin == 2){ ?>
		<li>
			<a href="add_staff.php">ADD STAFF</a>
		</li>
		<li>
			<a href="attendance_records.php">ATTENDANCE REGISTER</a>
		</li>
		<li>
			<a href="branch_procedure_pending_token.php">Procedure Token</a>
		</li>
		<li>
			<a href="branch_procedure_pendings.php">Procedure Turn</a>
		</li>
<?php if($branch_id == 15 || $branch_id == 24) { ?>
		<li>
			<a href="admission_form_for_drug_addicts.php">Admission Form Drug Addicts</a>
		</li>	
		<li>
			<a href="patients_record.php">Patients Record</a>
		</li>
<?php } ?>
		<li>
			<a href="show_branch_stock_deemand.php">Show Branch Stock Deemand</a>
		</li>
		<li>
			<a href="expense_procedure.php">Expense Procedure</a>
		</li>	
<?php 
if($branch_id != '0')
{
?>		
		<li>
			<a href="progress_report.php">Progress</a>
		</li>		
<?php 
}
}	?>
		<!--<li>-->
		<!--	<a style="cursor: pointer;" onclick="window.open('user_summary.php','_blank','width=620,height=300')" class="">Summary</a>-->
		<!--</li>-->
		<li>
			<a href="logout.php">Logout</a>
		</li>
		<li>
			<a href="logout_with_report.php">Logout (Report)</a>
		</li>

	</ul>
	<h5 style="margin-top: 150px;text-align: center;">USER: <?php echo htmlspecialchars($user_name); if ($is_incharge == 2) { echo ' Incharge '; } ?></h5>
</nav>
<?php include __DIR__ . '/../includes/nav_active_highlight.php'; ?>