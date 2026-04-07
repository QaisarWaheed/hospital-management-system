<nav>
	<ul id="nav_1">
		<li>
			<a style="cursor: pointer;" href='dashboard.php' class="">Dashboard</a>
		</li>
<?php if($lab_manager_login_is_admin == 2){ ?>
		<li>
			<a style="cursor: pointer;" href='purchase_lab_items.php' class="">Purchase Lab Item</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='add_item.php' class="">Add Item</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='add_item_to_branch.php' class="">Add Item To Branch</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='verify_item_issuance.php' class="">Verify Item Issuance</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='verify_item_purchase.php' class="">Verify Item Purchase</a>
		</li>
<?php }
if($lab_manager_login_is_admin == 1){} ?>
		<li>
			<a href="logout.php">Logout</a>
		</li>
	</ul>
</nav>
