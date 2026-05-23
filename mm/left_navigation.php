<nav>
	<ul id="nav_1">
		<li>
			<a style="cursor: pointer;" href='add_bill.php' class="">Testing Add Bill</a>
		</li>
<?php if($is_admin == 2){ ?>
		<li>
			<a style="cursor: pointer;" href='dashboard.php' class="">Dashboard</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='add_party.php' class="">Add Party</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='add_company.php' class="">Add Company</a>
		</li>
		
		<li>
			<a style="cursor: pointer;" href='add_item.php' class="">Add Item</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='add_item_purchase.php' class="">Add Item Purchase</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='add_audit_extra_item_purchase.php' class="">Audit Extra Purchase</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='add_item_to_branch.php' class="">ADD ITEM TO BRANCH</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='add_user.php' class="">Add User</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='generate_bill.php' class="">Generate Bill</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='verify_tokens.php' class="">Verify Tokens</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='verify_item_issuance.php' class="">Verify Item Issuance</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='verify_item_purchase.php' class="">Verify Item Purchase</a>
		</li>
		<!--<li>-->
		<!--	<a target="_blank" href="patient_registeration.php">Patient Registeration</a>-->
		<!--</li>-->
		<!--<li>-->
		<!--	<a target="_blank" href="patient_registeration_complete.php">Patient Registeration(COMPLETE)</a>-->
		<!--</li>-->
		<!--<li>-->
		<!--	<a target="_blank" href="second_turn.php">Second Turn</a>-->
		<!--</li>		-->
		<!--<li>-->
		<!--	<a target="_blank" href="second_turn_pending.php">Second Turn(Pending)</a>-->
		<!--</li>-->
		<!--<li>-->
		<!--	<a target="_blank" href="donation_collection.php">Donation Collection</a>-->
		<!--</li>-->
		<!--<li>-->
		<!--	<a target="_blank" href="branch_procedure_pendings.php">Procedure Turn</a>-->
		<!--</li>-->
		<!--<li>-->
		<!--	<a target="_blank" href="item_receive_branch.php">Receive Item In Branch</a>-->
		<!--</li>-->
		<!--<li>-->
		<!--	<a target="_blank" href="show_branch_stock_deemand.php">Show Branch Stock Deemand</a>-->
		<!--</li>-->
<?php }
if($is_admin == 1){ ?>
		<li>
			<a style="cursor: pointer;" href='dashboard.php' class="">Dashboard</a>
		</li>
<?php if($branch_id == 9 || $branch_id == 10){ ?>		
		<li>
			<a style="cursor: pointer;" href='show_item_quantity.php' class="">Show Item</a>
		</li>
<?php } ?>
		<li>
			<a style="cursor: pointer;" href='check_token.php' class="">Check Token</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='generate_bill.php' class="">Generate Bill</a>
		</li>
<?php } ?>
		<!--<li>-->
		<!--	<a style="cursor: pointer;" href='user_complete_summary.php' class="">Complete Summary</a>-->
		<!--</li>-->
		<li>
			<a href="logout.php">Logout</a>
		</li>
	</ul>
</nav>
<?php include __DIR__ . '/../includes/nav_active_highlight.php'; ?>
