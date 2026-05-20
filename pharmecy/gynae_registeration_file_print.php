<?php
require_once __DIR__ . '/includes/connect.php';

if (isset($_GET['reg_id']) && $_GET['reg_id'] != '') 
{
	$reg_id = $_GET['reg_id'];
	$select = "SELECT * FROM `gynae_register` WHERE `id` = '$reg_id' ";
	$run = mysqli_query($con, $select);
	if (mysqli_num_rows($run) == 1) 
	{
		while ($row = mysqli_fetch_array($run)) 
		{
		    $token_no = $row['token_no'];
            $patinet_name = get_patient_name_by_token_no($token_no);
		    $weeks = $row['weeks'];
		    $remarks = $row['remarks'];
		    $phone = $row['phone'];
		    $lmp = $row['lmp'];
		    $years_marriage = $row['years_marriage'];
		    $height = $row['height'];
		    $weight = $row['weight'];
		    $blood_group = $row['blood_group'];
		    $husband_name = $row['husband_name'];
		    $husband_blood_group = $row['husband_blood_group'];
		    $menstrual_cycle = $row['menstrual_cycle'];
		    $psh = $row['psh'];
		    $pmh = $row['pmh'];
		    $husband_name = $row['husband_name'];
		    $husband_phone = $row['husband_phone'];
		    $gravida = $row['gravide'];
		    $usg_report = $row['usg_report'];
		    $next_visit_date = $row['next_visit_date'];
		    $doctor_name = get_uname_by_id($row['doctor_id']);
		    $reg_date = $row['created'];
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>REGISTER FILE</title>
<style type="text/css">
*{
	text-transform: uppercase;
	text-align: left;
}
</style>
</head>
<body>
<!--<div style = "text-align: center; min-heigth:150px; max-heigth:150px;"></div>-->
<table border="1" style="">
	<caption style="caption-side: top;text-align: center; min-heigth:150px; max-heigth:150px;margin-top: 150px;">
		<img src="images/logo_black.jpg" height="150" >
	</caption>
	<tr>
		<th width = "25%">REGISTERATION NO</th>
		<td width = "75%"><?php echo $reg_id; ?></td>
	</tr>
	<tr>
		<th>REGISTERATION DATE</th>
		<td><?php echo ycdo_safe_date_format($reg_date, 'd F Y', 'N/A'); ?></td>
	</tr>
	<tr>
		<th>token_no</th>
		<td><?php echo $token_no; ?></td>
	</tr>
	<tr>
		<th>Patient</th>
		<td><?php echo $patinet_name; ?></td>
	</tr>
	<tr>
		<th>E.D.D</th>
		<td><?php echo $weeks; ?></td>
	</tr>
	<tr>
		<th>remarks</th>
		<td><?php echo $remarks; ?></td>
	</tr>
	<tr>
		<th>patient phone</th>
		<td><?php echo $phone; ?></td>
	</tr>
	<tr>
		<th>lmp</th>
		<td><?php echo $lmp; ?></td>
	</tr>
	<tr>
		<th>years_marriage</th>
		<td><?php echo $years_marriage; ?></td>
	</tr>
	<tr>
		<th>height</th>
		<td><?php echo $height; ?></td>
	</tr>
	<tr>
		<th>weight</th>
		<td><?php echo $weight; ?></td>
	</tr>
	<tr>
		<th>blood_group</th>
		<td><?php echo $blood_group; ?></td>
	</tr>
	<tr>
		<th>husband_name</th>
		<td><?php echo $husband_name; ?></td>
	</tr>
	<tr>
		<th>husband b/g</th>
		<td><?php echo $husband_blood_group; ?></td>
	</tr>
	<tr>
		<th>menstrual_cycle</th>
		<td><?php echo $menstrual_cycle; ?></td>
	</tr>
	<tr>
		<th>psh</th>
		<td><?php echo $psh; ?></td>
	</tr>
	<tr>
		<th>pmh</th>
		<td><?php echo $pmh; ?></td>
	</tr>
	<tr>
		<th>gravida</th>
		<td><?php echo $gravida; ?></td>
	</tr>
	<tr>
		<th>usg report</th>
		<td><?php echo $usg_report; ?></td>
	</tr>
	<tr>
		<th>next_visit_date</th>
		<td><?php echo $next_visit_date; ?></td>
	</tr>
	<tr>
		<th>DR NAME</th>
		<td><?php echo $doctor_name; ?></td>
	</tr>
	<tr>
		<th>PRINT DATE</th>
		<td><?php echo date('d F Y'); ?></td>
	</tr>
</table>
<h3>Rx:</h3>
</body>
</html>