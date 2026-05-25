<?php
$last_audit_id = 0;
$select_audit = "SELECT id FROM `audit_branch_form` WHERE `branch_id` = '$branch_id' ORDER BY `id` DESC LIMIT 1,1 ";
$run_audit = mysqli_query($con, $select_audit);
if(mysqli_num_rows($run_audit) == 1)
{
    while($row_audit = mysqli_fetch_array($run_audit))
    {
        $last_audit_id = $row_audit['0'];
    }
}
?>
<nav>
	<ul id="nav_1">
		<li>
			<a style="cursor: pointer;" href='dashboard.php' class="">Dashboard</a>
		</li>
		<!--<li>-->
		<!--	<a style="cursor: pointer;" href='add_user.php' class="">Add User</a>-->
		<!--</li>-->
		<!--<li>-->
		<!--	<a style="cursor: pointer;" href='verify_tokens.php' class="">Verify Tokens</a>-->
		<!--</li>-->
		<li>
			<a style="cursor: pointer;" href='branch_item_consumed_details.php?audit_id=<?php echo $last_audit_id; ?>&br_id=<?php echo $branch_id; ?>' class="">Branch Consumption</a>
		</li>
<?php if($is_admin == 2){ ?>
		<li>
			<a style="cursor: pointer;" href='audit_detail_form.php' class="">Performa Audit(Branch)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='audit_lab_detail_form.php' class="">Performa Audit(Lab)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='update_branch_item.php' class="">Update Branch Item</a>
		</li>
    <?php if($branch_id == 1){ ?>
		<li>
			<a style="cursor: pointer;" href='audit_detail_form_lab_store.php' class="">Performa Audit(Lab-Store)</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='audit_detail_form_store.php' class="">Performa Audit(Store)</a>
		</li>
    <?php } ?>
		<li>
			<a style="cursor: pointer;" href='varify_token.php' class="">Verify Token</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='deserving_medicines.php' class="">Show Deserving</a>
		</li>
<?php } ?>
		<li>
			<a style="cursor: pointer;" href='show_return_purchase_item.php' class="">Return Store Stock</a>
		</li>
		<li>
			<a style="cursor: pointer;" href='show_stock.php' class="">Show Stock</a>
		</li>
		<li>
			<a href="logout.php">Logout</a>
		</li>
	</ul>
</nav>
<?php include __DIR__ . '/../includes/nav_active_highlight.php'; ?>
