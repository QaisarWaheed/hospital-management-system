<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
include 'includes/head.php'; 
if(isset($_POST['save_referal_test']) && $_POST['item_id'] != '')
{
    $item_id = $_POST['item_id'];
    $insert = "INSERT INTO `referral_tests`
    (`referral_test_id`, `item_id`, `user_id`, `referral_test_status`, `referral_test_created`) 
    VALUES
    (NULL, '$item_id', '$lab_user_id', '1', '$current_date')";
    if(mysqli_query($con, $insert))
    {
        header('location: referal_tests.php');
    }
}
?>
	<title>ALL REFERAL TESTS - <?php echo $company_trademark; ?></title>
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
	    <table class = "table table-bordered table-hover" style = "color: black;">
	        <caption style = "caption-side: top;color: black;text-align: center;"><h2>ALL REFERAL TESTS</h2></caption>
	        <thead>
	            <form method = "POST" action = "referal_tests.php">
	            <tr class = "nodisplay_print">
	                <th colspan = "3">
                        <input list="items" name="item_id" id="item_id" class = "form-control text-danger" required>
                        <datalist id="items">
    	                <?php
    	                $select = "SELECT id, name FROM items WHERE status = '1' AND id NOT IN (SELECT item_id FROM referral_tests) ";
    	                $run = mysqli_query($con, $select);
    	                if(mysqli_num_rows($run) > 0)
    	                {
    	                    while($row = mysqli_fetch_array($run))
    	                    {
        	                    echo '<option value = "'.$row['id'].'">'.$row['name'].'</option>';
    	                    }
    	                }
    	                else
    	                    echo '<option value = ""> NO DATA FOUND</option>';
    	                ?>
	                    </datalist>
	                </th>
	                <th colspan = "2">
	                    <input type = "submit" class = "btn btn-primary" style = "min-width: 100%;" name = "save_referal_test" value = "SAVE TEST DETAIL" />
	                </th>
	            </tr>
	            </form>
	            <tr>
	                <th>S#</th>
	                <th>Name</th>
	                <th>POOR</th>
	                <th>MEMBER</th>
	                <th>GENERAL</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 0;
$select = "SELECT referral_test_id, items.name AS name, items.poor AS poor, items.member AS member, items.general AS general FROM `referral_tests` INNER JOIN items ON referral_tests.item_id = items.id WHERE referral_tests.referral_test_status = '1' AND items.status = '1' ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s++;
?>
                <tr>
                    <td><?php echo $s; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['poor']; ?></td>
                    <td><?php echo $row['member']; ?></td>
                    <td><?php echo $row['general']; ?></td>
                </tr>
<?php
    }
}
?>
	        </tbody>
	    </table>
	</div>
</div>

</body>
</html>