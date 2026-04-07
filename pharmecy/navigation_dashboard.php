<a class= "btn btn-outline-info btn-sm" href="dashboard.php">Home</a>
<a class= "btn btn-outline-info btn-sm" href="logout.php">Logout</a>
<h5 style="">USER: <?php echo $_SESSION['ph_name'];if($_SESSION['is_incharge'] == 2){ echo " Incharge ";} ?>(<?php echo $role_title; ?>)</h5>