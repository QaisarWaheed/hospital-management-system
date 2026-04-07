<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<title>ALL REFERAL TESTS - <?php echo $company_trademark; ?></title>
    <style>
    .background_image{
    	background-image: url('../images/background.png');
    	background-size: cover;
    }
    </style>    
    <style>
        @media print {
            body {
                /* Reduce the base font size for the entire page to 12px */
                font-size: 12px; 
            }

            table {
                font-size: 0.8em; 
            }
        }
    </style>
</head>
<body class="background_image">
<?php include 'top_navigation.php'; ?>
<div class="row">
	<div class="col-md-12">
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
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>