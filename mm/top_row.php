<?php if($is_admin == 2){ ?>
<div class ="row bg-danger p-2">
    <div class = "col">
        <a href = "dashboard.php" class = "btn btn-info btn-sm">Dashboard</a>
    </div>
    <div class = "col">
        <a href = "add_party.php" class = "btn btn-info btn-sm">Add Party</a>
    </div>
    <div class = "col">
        <a href = "add_company.php" class = "btn btn-info btn-sm">Add Company</a>
    </div>
    <div class = "col">
        <a href = "add_item.php" class = "btn btn-info btn-sm">Add Item</a>
    </div>
    <div class = "col">
        <a href = "add_item_purchase.php" class = "btn btn-info btn-sm">Add Purchase</a>
    </div>
    <div class = "col">
        <a href = "add_item_to_branch.php" class = "btn btn-info btn-sm">Add Item To Branch</a>
    </div>
    <div class = "col">
        <a href = "add_user.php" class = "btn btn-info btn-sm">Add User</a>
    </div>
    <div class = "col">
        <a href = "logout.php" class = "btn btn-info btn-sm">Logout</a>
    </div>
</div>
<?php }
if($is_admin == 1){ ?>
<div class ="row bg-danger p-2">
    <div class = "col">
        <a href = "dashboard.php" class = "btn btn-info btn-sm">Dashboard</a>
    </div>
</div>
<?php } ?>