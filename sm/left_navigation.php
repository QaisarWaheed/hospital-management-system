<nav>
	<ul id="nav_1">

<?php
// $is_incharge
if($is_admin == 1)
{ ?>	<li>
			<a href="show_all_items.php">Show All Items</a>
		</li>
    	<li>
			<a href="branch_full_demand.php">Branch full Demand</a>
		</li>
		<li>
			<a href="branch_short_demand.php">Branch Short Demand</a>
		</li>
		<li>
			<a href="near_expiry_medicine.php">Near Expire</a>
		</li>
		<li>
			<a href="short_medicines.php">Short Medicines</a>
		</li>
		<li>
			<a href="last_audit_reports.php">Last Audit Report</a>
		</li>
<?php
}
elseif($user_id == 1 || $is_admin == 2)
{ ?>	<li>
			<a target="_blank" href="show_item_purchase.php">S.M Receive Purchase Item</a>
		</li>
		<li>
			<a target="_blank" href="item_register_branch.php">S.M Issue Branch Stock</a>
		</li>
		<li>
			<a target="_blank" href="item_receive_from_branch.php">Receive Items From Branch</a>
		</li>	<li>
			<a href="show_all_items.php">Show All Items</a>
		</li>
    	<li>
			<a href="show_all_item_with_rate.php">Items Rate</a>
		</li>
    	<li>
			<a href="branch_full_demand.php">Branch full Demand</a>
		</li>
		<li>
			<a href="branch_short_demand.php">Branch Short Demand</a>
		</li>
		<li>
			<a href="near_expiry_medicine.php">Near Expire</a>
		</li>
		<li>
			<a href="update_branch_item.php">Update Branch Item</a>
		</li>
		<li>
			<a href="purchase_order.php">Purchase Order</a>
		</li>
		<li>
			<a href="verify_item_purchase.php">Verify Purchase</a>
		</li>
		<li>
			<a href="short_medicines.php">Short Medicines</a>
		</li>
<?php
}
if($role_id == 0)
{ ?>
		<li>
			<a target="_blank" href="item_register_branch_update.php">S.M Issue Branch Update</a>
		</li>
    
<?php 
}
?>
		<li>
			<a target="_blank" href="register_branch_list.php">Issue Branch List</a>
		</li>
		<li>
			<a href="logout.php">Logout</a>
		</li>
	</ul>
</nav>
<?php include __DIR__ . '/../includes/nav_active_highlight.php'; ?>