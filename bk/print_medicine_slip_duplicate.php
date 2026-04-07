<?php include 'includes/connect.php'; 
if (isset($_POST['tokan_no']) && $_POST['tokan_no'] != '') 
{
	$token_id = $_POST['tokan_no'];
}
elseif (isset($_GET['tokan_no']) && $_GET['tokan_no'] != '') 
{
	$token_id = $_GET['tokan_no'];
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta lang="en">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/nav_style.css">
	<!--<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">-->
<style>
body
{
    text-transform: uppercase;
}
</style>
<style>
@font-face{  font-family: "Jameel Noori Nastaleeq";  src: url("fonts/Jameel Noori Nastaleeq Regular.ttf") format("truetype");}    
</style>
	<title>SHOW PRESCRIPTION - <?php echo $company_trademark; ?></title>
<script>
* {
    font-size: 12px;
    font-family: 'Times New Roman';
}

td,
th,
tr,
table {
    border-top: 1px solid black;
    border-collapse: collapse;
}
    
</script>
</head>
<body>
    <table>
<?php
$select2 = "SELECT patient_history_detail FROM `patient_histories` WHERE `token_no` = '$token_id' ";
$run2 = mysqli_query($con, $select2);
if (mysqli_num_rows($run2) > 0) 
{
	while ($row2 = mysqli_fetch_array($run2)) 
	{
	    $patient_history_detail = $row2['patient_history_detail'];
	}
	if(is_null($patient_history_detail))
	{
	    $patient_history_detail = "NO DATA ADDED";
	}
}
echo '<caption stype = "text-align: center; color: black; font-size: 20px; caption-side: top;"><strong>HISTORY: </strong>'.$patient_history_detail.'</caption>';
?>
        <thead>
            <tr>
                <tr>
                    <th>Sr#</th>
                    <th>ITEM</th>
                    <th>CATEGORY</th>
                    <th>QUANTITY</th>
                </tr>
            </tr>
        </thead>
        <tbody>
<?php
$sr = 0;
$select = "SELECT item_by_doctor.tokan_no, item_by_doctor.item_id, items.name, categories.name AS category_name FROM `item_by_doctor` INNER  JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN categories ON items.category_id = categories.id WHERE `tokan_no` = '$token_id' ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) 
{
	while ($row = mysqli_fetch_array($run)) 
	{
	    $sr++;
	    ?>
	        <tr>
	            <td><?php echo $sr; ?></td>
	            <td><?php echo $row['name']; ?></td>
	            <td><?php echo $row['category_name']; ?></td>
	        </tr>
	<?php }
}
?>
        </tbody>
    </table>
</body>
</html>
<script type="text/javascript">
        setTimeout(function () { window.close(); }, 5000);
</script>
<?php mysqli_close($con); ?>