<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
?>
	<title>Drug Patient Register - <?php echo $company_trademark; ?></title>
    <script type="text/javascript" src="js/jquery-3.6.0.min.js"></script> 
</head>

<body class="background_image_ycdo" onkeydown="return (event.keyCode != 116)">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>
<div>
	<div class="">
		<div class="row" style="margin: 0px;">
        	<div class="col-md-12 background_whitesmoke d-print-none">
        		<?php 
        		include 'navigation_top.php'; 
        		?>
        	</div>
			<div class="col-md-12 d-print-none">
			    <?php include 'includes/navigation_drug_patient_register.php'; ?>
		    </div>
			<div class="col-md-12">
			    <table class = "table table-sm table-hover table-bordered">
			        <thead>
			            <tr>
			                <th>S #</th>
			                <th>Date</th>
			                <th>Token #</th>
			                <th>Name</th>
			                <th>Phone</th>
			                <th>Age/ Sex</th>
			                <th>Next Checkup</th>
			                <th>Drug Addict</th>
			                <th>Drug Period</th>
			                <th>Per Day Fee</th>
			            </tr>
			        </thead>
			        <tbody>
			            <?php
			            $s = 0;
			            $select = "SELECT * FROM `drug_addict_patient_admisssions` INNER JOIN tokans ON drug_addict_patient_admisssions.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN type_of_druges ON drug_addict_patient_admisssions.type_of_drug_id = type_of_druges.type_of_drug_id INNER JOIN genders ON patients.gender = genders.gender_id WHERE `drug_addict_patient_admisssion_status` = '1' ";
			            $run = mysqli_query($con, $select);
			            if(mysqli_num_rows($run) > 0)
			            {
			                while($row = mysqli_fetch_array($run))
			                { $s++; ?>
	                        <tr>
	                            <td><?php echo $s; ?></td>
	                            <td><?php echo date_format(date_create($row['drug_addict_patient_admisssion_date']), "d-M-Y"); ?></td>
	                            <td><?php echo $row['token_no']; ?></td>
	                            <td><?php echo $row['name']; ?></td>
	                            <td><?php echo $row['phone']; ?></td>
	                            <td><?php echo $row['age'].'/ '.$row['gender_title']; ?></td>
	                            <td><?php echo date_format(date_create($row['next_checkup_date']), "d-M-Y"); ?></td>
	                            <td><?php echo $row['main_type_of_drug'].'('.$row['sub_type_of_drug'].')'; ?></td>
	                            <td><?php echo $row['drug_period']; ?></td>
	                            <td><?php echo $row['per_day_fee']; ?></td>
	                        </tr>
			                <?php }
			            }
			            else
			            {
			                echo '<tr><td colspan = "6">NO DATA FOUND</td></tr>';
			            }
			            ?>
			        </tbody>
			    </table>
			</div>
	    </div>
    </div>
</div>

</body>
</html>