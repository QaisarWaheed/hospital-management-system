<?php 
include 'includes/connect.php'; 
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
	<title>Dashboard - <?php echo $company_trademark; ?></title>
<script>
* {
    font-size: 14px;
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
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body onload="window.print()" onafterprint="window.location.href = 'dashboard.php'">
<?php
if (isset($_POST['tokan_no']) && $_POST['tokan_no'] != '') {
	$tokan_id = $_POST['tokan_no'];
	echo print_medicine_slip($tokan_id); 
}
if (isset($_GET['tokan_no']) && $_GET['tokan_no'] != '') {
	$tokan_id = $_GET['tokan_no'];
	echo print_medicine_slip($tokan_id); 
}

?>

</body>
</html>
<script type="text/javascript">
        setTimeout(function () { window.close(); }, 1200);
</script>