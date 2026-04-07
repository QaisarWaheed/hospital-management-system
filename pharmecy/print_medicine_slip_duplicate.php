<?php include 'includes/connect.php'; ?>
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
	<title>Dashboard - <?php echo $company_trademark; ?></title>
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
<body onload="window.print()" onafterprint="window.location.href = 'dashboard.php'">
<?php
if (isset($_POST['tokan_no']) && $_POST['tokan_no'] != '') {
	$token_id = $_POST['tokan_no'];
}
elseif (isset($_GET['tokan_no']) && $_GET['tokan_no'] != '') {
	$token_id = $_GET['tokan_no'];
}
$select = "SELECT `tokan_type_id` FROM `tokans` WHERE `id` = '$token_id' ";
$run = mysqli_query($con, $select);
if (mysqli_num_rows($run) > 0) 
{
	while ($row = mysqli_fetch_array($run)) 
	{
		$tokan_type_id = $row['tokan_type_id'];
		if ($tokan_type_id > 100) 
		{
			echo print_medicine_slip_duplicate($token_id); 
		}
		else
		{
			echo print_tokan_duplicate($token_id); 
		}
	}
}

?>

</body>
</html>
<script type="text/javascript">
        setTimeout(function () { window.close(); }, 1200);
</script>
<?php mysqli_close($con); ?>