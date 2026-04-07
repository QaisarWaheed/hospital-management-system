<nav class="navbar navbar-expand-lg navbar-light bg-inverse">
    <div class = "row">
        <div class = "col">
            <a class = "btn btn-sm active" href = "dashboard.php">Home</a>
        </div>
<?php if($bk_is_admin == 1 && $bk_is_incharge == 1){ ?>        
        <div class = "col">
            <a class = "btn btn-sm active" href = "gynae_registeration.php">Gynae Register</a>
        </div>
        <div class = "col">
            <a class = "btn btn-sm active" href = "user_summary.php">Summary</a>
        </div>
        <div class = "col">
            <a class = "btn btn-sm active" href = "user_summary_login.php">Summary Login Wise</a>
        </div>
        <div class = "col">
            <a class = "btn btn-sm active" href = "add_user.php">User</a>
        </div>
<?php }	?>
        <div class = "col">
            <a class = "btn btn-sm btn-info right" href = "logout.php">Logout</a>
        </div>
        
    </div>
</nav>