<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
if(!isset($_SESSION['mm_id']))
{
    header('location: logout.php');
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	    <?php
	    $id = '21';
	    $s = 0;
	    $select = "SELECT items.id FROM items WHERE items.category_id = 2 AND items.name LIKE '%e1%' AND items.status = '1' ";
	    $run = mysqli_query($con, $select);
	    if(mysqli_num_rows($run) > 0)
	    {
	        while($row = mysqli_fetch_array($run))
	        {
	            $s++;
	            $item_id = $row['id'];
	            $insert = "INSERT INTO `item_register_to_branches`
	            (`id`, `item_id`, `branch_id`, `quantity`, `min_limit`, `max_limit`, `status`, `created`, `user_id`) 
	            VALUES 
	            (NULL, '$item_id', '$id', '0', '0', '0', '1', '$current_date', '$user_id') ";
	            if(mysqli_query($con, $insert))
	            {
    	            echo $s.' - >DATA ADDED.<br>';
	            }
	            else
	            {
    	            echo $insert.'<br>';
	            }
	        }
	    }
	    ?>
	</div>
</div>

</body>
</html>