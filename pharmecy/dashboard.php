<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 

$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if(mysqli_num_rows($run_roles) == 1)
{
    while($row_role = mysqli_fetch_array($run_roles))
    {
        $role_title = $row_role['title'];
    }
}
else
{
    $role_title = '';
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image" oncontextmenu="return false;">
<div id="loadingSpinner" style="display: none;">
    <div class = "container">
        <div class = "row p-5 g-5">
            <div class = "col text-center">
                <div aria-busy="true" aria-describedby="progress-bar">
                    <h2>LOADING...</h2>
                    <p>Please Wait Untill Processing Completed.</p>
                    <p>Data Processing...</p>
                </div>
                <progress id="progress-bar" aria-label="Content loading…"></progress>    
                
            </div>
        </div>        
    </div>
</div>
<div class="row" style="margin: 0px;" id = "submitBody">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke" style = "text-transform: uppercase;">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	<div style="text-align: right;float: right;margin-right: 10px;">
	<?php
	if($branch_id == 15)
	{
	    echo '
	    <div style = "text-align: center;">
    	    <img width = "100%" src= "images/city_police_multan_2.png" alt = "POLICE & YCDO DRUG REHABILITATION HOSPITAL" />
	    </div>';
	}
	else
	{
	?>
		<h2 style="color: white;"><?php echo $company_name; ?></h2>
		<h6 style="color: brown;"><?php echo $company_ambition; ?></h6>
		<h3 style="color: red;"><?php echo $branch_name; ?></h3>
		<h4 style="color: white;"><?php echo $branch_address; ?></h4>
		<!--<h4 style="color: white;"><?php echo $branch_phone; ?></h4>-->
	<?php
	}
	?>
		<h4 style="color: white;">UAN : 0304-1110222</h4>
		<h3 style="margin-top: 250px;text-align: center;">USER: <?php echo htmlspecialchars($user_name); if ($is_incharge == 2) { echo ' Incharge '; } ?>(<?php echo htmlspecialchars($role_title); ?>)</h3>
		<h3>LAST TOKEN NO: <?php echo last_token_by_user($user_id); ?></h3>
	</div>
			
	</div>
</div>

</body>
</html>
<script>
function showProgress() {
  document.getElementById('submitBody').style.display = 'none';
//   document.getElementById('submitButton').style.display = 'none';
  document.getElementById('loadingSpinner').style.display = 'block';
}    
</script>
<?php mysqli_close($con); ?>