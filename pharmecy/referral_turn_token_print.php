<?php
date_default_timezone_set("Asia/Karachi");
include 'includes/config.php'; 
if (isset($_GET['referral_tokan_no']) && $_GET['referral_tokan_no'] != '') 
{
    $token_no = $_GET['referral_tokan_no'];
    $select_referral_token = "SELECT * FROM `referral_patients` WHERE `token_id` = '$token_no' ";
    $run_referral_token = mysqli_query($con, $select_referral_token);
    if(mysqli_num_rows($run_referral_token) == 1)
    {
        while($row_referral_token = mysqli_fetch_array($run_referral_token))
        {
            $referral_patient_id = $row_referral_token['referral_patient_id'];
            $opd_token_no = $row_referral_token['opd_token_id'];
            $token_by = get_uname_by_id($row_referral_token['user_id']);
            $received_cash = $row_referral_token['received_cash'];
            $required_opinion = $row_referral_token['required_opinion'];
            $referral_patient_phone = $row_referral_token['referral_patient_phone'];
            $referral_patient_created = $row_referral_token['referral_patient_created'];
        }
    }
	$select_tokan = "SELECT * FROM tokans WHERE id = '$token_no' ";
	$run_tokan = mysqli_query($con, $select_tokan);
	if (mysqli_num_rows($run_tokan) == 1) 
	{
		while ($row_tokan = mysqli_fetch_array($run_tokan)) 
		{
			$doctor_id = $row_tokan['doctor_id'];
			$patient_id = $row_tokan['patient_id'];
			$select_patient = "SELECT * FROM patients WHERE id = '$patient_id' ";
			$run_patient = mysqli_query($con, $select_patient);
			if (mysqli_num_rows($run_patient) == 1) 
			{
				while ($row_patient = mysqli_fetch_array($run_patient)) 
				{
					$name = $row_patient['name'];
					$age = $row_patient['age'];
					$gender = $row_patient['gender'];
					if($gender == 1){$gender = "FEMALE";}elseif($gender == 2){$gender = "MALE";}else{$gender = "OTHER";}
				}
			}

		}
	}
}
else
{
    // header('location: logout.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body {  
    text-transform : uppercase;
    height: 842px;
    /*width: 595px;*/
    /* to centre page on screen*/
    margin: auto auto;
    background-image: url('images/label2.png');
    background-repeat: no-repeat;
    background-attachment: fixed;
    background-size: 40% 100%;
}

.column1 {
  float: left;
  width: 13.33%;
}
.column2 {
  float: left;
  width: 86.33%;
}
/* Stops the float property from affecting content after the columns */
.columns:after {
  content: "";
  display: table;
  clear: both;
}
table, th, td {
  border: 1px solid lightgray;
  border-collapse: collapse;
}
</style>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body onload = "window.print()">

<header>
    <div class="columns">
        <div class="column1" style = "text-align: center;">
            <img src = "images/logo.jpg" alt = "YCDO" height = "92px" width = "92px" />
        </div>
        <div class="column2" style = "text-align: center;">
            <strong style = "font-size: 22px;"><i><?php echo $company_name; ?></i></strong>
            <br>
            <strong style = "font-size: 18px;"><i><?php echo $branch_name; ?></i></strong>
            <br>
            <strong><u><i><?php echo $branch_address; ?>, <?php echo $branch_phone; ?></i></u></strong>
            <br>
            <strong><u><i>UAN: <?php echo $company_phone; ?></i></u></strong>
        </div>
    </div>    
</header>
<hr>
<table style = "margin: 0 auto;" >
    <tr style = "text-align: center;">
        <th colspan = "2">REFERRAL NO: <u><i><strong><?php echo $referral_patient_id; ?></strong></i></u></th>
        <th colspan = "2">TOKEN NO: <u><i><strong><?php echo $token_no; ?></strong></i></u></th>
        <td>PHONE</td>
        <th><u><i><strong><?php echo $referral_patient_phone; ?></strong></i></u></th>
    </tr>
    <tr style = "text-align: center;">
        <td>PATIENT</td>
        <th><u><i><strong><?php echo $name; ?></strong></i></u></th>
        <td>AGE</td>
        <th><u><i><strong><?php echo $age; ?> Y</strong></i></u></th>
        <td>GENDER</td>
        <th><u><i><strong><?php echo $gender; ?></strong></i></u></th>
    </tr>    
    <tr style = "text-align: center;">
        <th colspan = "2">USER: <u><i><strong><?php echo $token_by; ?></strong></i></u></th>
        <th colspan = "2">OPD TOKEN NO: <u><i><strong><?php echo $opd_token_no; ?></strong></i></u></th>
        <td>DATE</td>
        <th><u><i><strong><?php echo $current_date; ?></strong></i></u></th>
    </tr>
    <tr>
        <th COLSPAN = "2">CONSULTANT FEE: <u><i><strong>Rs: 2000/-</strong></i></u></th>
        <th COLSPAN = "2">PAID BY PATIENT: <u><i><strong>Rs: <?php echo $received_cash; ?>/-</strong></i></u></th>
        <th COLSPAN = "2">PAID BY YCDO: <u><i><strong>Rs: <?php echo 2000-$received_cash; ?>/-</strong></i></u></th>
    </tr>
    <tr>
        <th colspan = "3">
            FROM
        </th>
        <th colspan = "3">
            TO
        </th>
    </tr>
    <tr style = "text-align: left;">
        <th colspan = "3">
            <?php echo show_from_doctors_by_token_id($opd_token_no); ?>
        </th>
        <th colspan = "3">
            <?php echo show_to_doctors_by_token_id($opd_token_no); ?>
        </th>
    </tr>
    <tr>
        <th colspan = "6">
            <u><i><strong>REQUIRED CONSULTANT OPENION</strong></i></u>
        </th>
    </tr>
    <tr style = "text-align: left;">
        <th colspan = "6" style = "width: 1020px;height: 130px;font-size: 12px;">
            <p><?php echo str_replace(',', '</br>&nbsp;&nbsp;',$required_opinion); ?></p>
        </th>
    </tr>
    <tr>
        <th colspan = "6">
            <u><i><strong>CONSULTANT ADVICE FOR FURTHER TREATMENT</strong></i></u>
        </th>
    </tr>
    <tr>
        <th colspan = "6" style = "width: 1020px;height: 360px;">
        </th>
    </tr>
    <tr style = "text-align: left;">
        <th colspan = "2">
            <i>NEXT VISIT DATE:</i>
        </th>
        <th colspan = "2">
        </th>
        <th colspan = "2">
            <i>NEXT VISIT TIME:</i>
        </th>
    </tr>
    
</table>
<footer style = "text-align: center;">
    <div>
        <img src = "images/ycdo_footer.png" alt = "YCDO" height = "100px" width = "100%" />
    </div>
</footer>

</body>
</html>