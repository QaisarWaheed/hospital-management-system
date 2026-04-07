<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
if (isset($_GET['category_idds']) && $_GET['category_idds'] != ''&& $_GET['category_idds'] != '0') 
{
$select_value = $_GET['category_idds'];
$select = mysqli_query($con, "SELECT * FROM items WHERE category_id = '$select_value' ");
}
else
{
$select_value = 0;
$select = mysqli_query($con, "SELECT * FROM items ORDER BY `name` ");
}
?>
	<title>Update Issue Item- <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">
<?php
if(isset($_POST['update']) && $_POST['update'] != '')
{
	$up_id = $_POST['up_id'];
	$issue_no = $_POST['issue_no'];
	$difference = $_POST['difference'];
	
	$update = "UPDATE item_register_branchs_by_sm SET
	`difference` = '$difference'
	WHERE id = '$up_id' ";
    if(mysqli_query($con, $update))
    {
    echo '<script>location.replace("item_register_branch.php?bill_no='.$issue_no.'")</script>';
    }
    exit(0);
}
elseif(isset($_GET['up']) && $_GET['up'] != '')
{
    $up_id = $_GET['up'];
    $select = mysqli_query($con,"SELECT * FROM item_register_branchs_by_sm WHERE id = '$up_id' ");
    if (mysqli_num_rows($select) == 1) 
    {
    	while ($row = mysqli_fetch_array($select)) 
    	{
    		$item_status = $row['status'];
    		$name = get_item_name_by_register_item_id($row['branch_item_id']);
    		$issue_no = $row['issue_id'];
    		$quantity = $row['quantity'];
    		$difference = $row['difference'];
    		$issued_branch_name = get_branch_name_by($row['branch_id']);
    	}
    }
?>
			<div class="col-md-12" style="text-align: center;">
				<label><h1>Update Item Form</h1></label>
			</div>
			<div class="col-md-12">

				<form name="test" method = "POST" autocomplete="off">

					<div class="row">

						<div class="col-md-3">
							<label>Id</label>
							<input type = "text" name = "up_id" value = "<?php echo $up_id; ?>" class = "form-control" readonly />
						</div>
						<div class="col-md-3">
							<label>Issue No</label>
							<input readonly type="text" name="issue_no" class="form-control" value = "<?php echo $issue_no; ?>" />
						</div>
						<div class="col-md-6">
							<label>Item Name</label>
							<input type="text" name="item_name" class="form-control" required value = "<?php echo $name; ?>" readonly />
						</div>
						<div class="col-md-3">
							<label>Branch Name</label>
							<input type="text" name="br_name" class="form-control" required value = "<?php echo $issued_branch_name; ?>" readonly />
						</div>
						<div class="col-md-3">
							<label for="quantity">Quantity</label>
							<input type="number" readonly id="quantity" name="quantity" class="form-control" value = "<?php echo $quantity; ?>" />
						</div>
						<div class="col-md-3">
							<label>STATUS</label>
							    <?php if($item_status == 1)
							    {
    							    echo '<input type="text" readonly class="form-control" value = "NOT RECEIVED" />';
							    }
							    else
							    {
    							    echo '<input type="text" readonly class="form-control" value = "RECEIVED" />';
							    } 
							    ?>
						</div>						
						<div class="col-md-3">
							<label for="difference">Difference</label>
							<input type="number" min = "-<?php echo $quantity; ?>" max = "<?php echo $quantity; ?>" id="difference" name="difference" class="form-control" value = "<?php echo $difference; ?>" />
						</div>

						<div class="col-md-12" style="margin: 20px 0px;">
						
							<input type="submit" name="update" value="UPDATE ITEM" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
							<a target="_blank" href="item_register_branch.php?bill_no=<?php echo $issue_no;?>" class="btn btn-info">SHOW ISSUES</a>
						</div>
					</div>

				</form>
			</div>    
<?php    exit(0);
}
?>
	</div>
</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<!-- 
 -->