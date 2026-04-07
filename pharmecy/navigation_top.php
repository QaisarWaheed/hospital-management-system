<a class= "btn btn-outline-info btn-sm" href="dashboard.php">Home</a>
<a class= "btn btn-outline-info btn-sm" href="patient_registeration.php">Patient Registeration</a>
<a class= "btn btn-outline-info btn-sm" href="patient_registeration_complete.php">Patient Registeration(COMPLETE)</a>
<a class= "btn btn-outline-info btn-sm" href="second_turn.php">Second Turn</a>
<a class= "btn btn-outline-info btn-sm" href="second_turn_by_doctor.php">Doctor Turn</a>
<a class= "btn btn-outline-info btn-sm" href="second_turn_pending.php">Second Turn(Pending)</a>
<a class= "btn btn-outline-info btn-sm" href="donation_collection.php">Donation Collection</a>
<a class= "btn btn-outline-info btn-sm" href="gynae_registeration.php">Gynae Register</a>
<?php if($is_incharge == 2){ ?>
<a class= "btn btn-outline-info btn-sm" href="item_receive_branch.php">Receive Item In Branch</a>
<!--<a class= "btn btn-outline-info btn-sm" href="item_return_to_store.php">Return Item To Store</a>-->
<a class= "btn btn-outline-info btn-sm" href="deserving_medicines.php">Deserving Medicines</a>
<?php }	?>
<?php if($is_admin == 2){ ?>
<!--<a class= "btn btn-outline-info btn-sm" href="branch_procedure_pendings.php">Procedure Turn</a>-->
<a class= "btn btn-outline-info btn-sm" href="show_branch_stock_deemand.php">Show Branch Stock Deemand</a>
<?php if($user_id == 1 || ($user_id >= 150 && $user_id <= 159) ){ ?>
<a class= "btn btn-outline-info btn-sm" href="return_token_full.php">Return Token (Full)</a>
<?php } ?>
<a class= "btn btn-outline-info btn-sm" href="progress_report.php">Progress</a>
<?php }	?>
<a class= "btn btn-outline-info btn-sm" href="logout.php">Logout</a>
<a class= "btn btn-outline-info btn-sm" href="logout_with_report.php">Logout (Report)</a>
<h5 style="">USER: <?php echo $_SESSION['ph_name'];if($_SESSION['is_incharge'] == 2){ echo " Incharge ";} ?>(<?php echo $role_title; ?>)</h5>
