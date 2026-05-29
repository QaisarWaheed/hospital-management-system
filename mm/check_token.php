<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
if(!isset($_SESSION['mm_id']))
{
    header('location: logout.php');
}
?>
	<title>CHECK TOKEN - <?php echo $company_trademark; ?></title>
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
    	<div style="">
    	    <?php ?>
    	    <form METHOD="POST">
    	        <div class="row">
    	            <div class="col-md-9">
    	                <h5 align="center">TOKEN NO</h5>
    	            </div>
    	            <div class="col-md-3">
    	                <h5 align="center">ACTION</h5>
    	            </div>
    	            <div class="col-md-9">
    	                <input type = "number" name = "token_no" value = "<?php echo $_POST['token_no']; ?>" value = "0" min = "1" class = "form-control" />
    	            </div>
    	            <div class="col-md-3">
    	                <input type="submit" value="SEARCH" name="token" class="btn btn-info btn-sm" />
    	                <input type="reset" value="RESET" name="reset" class="btn btn-danger btn-sm" />
    	            </div>
    	        </div>
    	    </form>
    	</div>
	<?php
	if(isset($_POST['token']) && $_POST['token'] != '')
	{
        	$token_no = $_POST['token_no'];
    	echo '<div class = "col-md-12">
    	        <h3 align = "center">TOKEN '.$token_no.' DATA</h3>
        	</div>';
        	$select_token = "SELECT patients.name AS patient_name, patients.age AS patient_age, genders.gender_tag,patients.phone AS patient_phone, dr.u_name AS dr_name, uname.u_name AS user_name, cash AS total_cash, cash_received AS received_cash, previous_tokan_no, `tokan_type_id`, tokans.branch_id AS token_branch_id, tokans.created AS token_created, tag_name FROM `tokans` INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN genders ON patients.gender = genders.gender_id INNER JOIN users dr ON tokans.doctor_id = dr.id INNER JOIN users uname ON tokans.user_id = uname.id INNER JOIN branchs ON tokans.branch_id = branchs.id WHERE tokans.id = '$token_no' ";
        	$run_token = mysqli_query($con, $select_token);
        	while($row_token = mysqli_fetch_array($run_token))
        	{
        	    $token_branch_id = $row_token['token_branch_id'];
        	    echo '<div class = "row">';
            	    echo '<div class = "col-md-4">';
            	    echo '<label>PATIENT NAME:</label><strong>'.$row_token['patient_name'].' / '.$row_token['gender_tag'].'</strong>';
            	    echo '</div>';
            	    echo '<div class = "col-md-4">';
            	    echo '<label>PATIENT AGE:</label><strong>'.$row_token['patient_age'].' ('.$row_token['tag_name'].')</strong>';
            	    echo '</div>';
            	    echo '<div class = "col-md-4">';
            	    echo '<label>PATIENT PHONE:</label><strong>'.$row_token['patient_phone'].'</strong>';
            	    echo '</div>';
            	    echo '<div class = "col-md-4">';
            	    echo '<label>CHECKED BY :</label><strong>'.$row_token['dr_name'].'</strong>';
            	    echo '</div>';
            	    echo '<div class = "col-md-4">';
            	    echo '<label>TOKEN BY:</label><strong>'.$row_token['user_name'].'</strong>';
            	    echo '</div>';
            	    echo '<div class = "col-md-4">';
            	    echo '<label>TOTAL CASH:</label><strong>'.$row_token['total_cash'].'</strong>';
            	    echo '</div>';
            	    echo '<div class = "col-md-8">';
            	    echo '<label>DATE AND TIME:</label><strong>'.date_format(date_create($row_token['token_created']), "h:i:s A d-F-Y").'</strong>';
            	    echo '</div>';
            	    echo '<div class = "col-md-4">';
            	    echo '<label>RECEIVED CASH:</label><strong>'.$row_token['received_cash'].'</strong>';
            	    echo '</div>';

                $select_doctor_medicine = "SELECT dose, feed, days, fix_dose, item_id FROM `select_by_doctor` WHERE `tokan_no` = '$token_no' AND`status` = '1' ";
                $run_doctor_medicine = mysqli_query($con, $select_doctor_medicine);
                if(mysqli_num_rows($run_doctor_medicine) > 0)
                {
                    echo '<div class = "col-md-6">';
            	    echo '<div style = "text-align: center;"><strong>MEDICINE BY DOCTOR</strong></div></br>';
                    while($row_doctor_medicine = mysqli_fetch_array($run_doctor_medicine))
                    {
                        $item_id = $row_doctor_medicine['item_id'];
                        echo get_item_name_by_register_item_id($item_id).'</br>';
                    }
                    echo '</div>';
                }
                else
                {
                    echo '<div class = "col-md-6">';
            	    echo '<div style = "text-align: center;"><strong>MEDICINE BY DOCTOR</strong></div></br>';
                    echo '<strong>NOTHING TO DISPLAY</strong></div>';
                }
                
                    echo '<div class = "col-md-6">';
            	    echo '<div style = "text-align: center;"><strong>MEDICINE BY RECEPTION</strong></div></br>';
                    echo get_given_services_by_token_no($token_no);
                    echo '</div>';
                // $select_reception_medicine = "SELECT dose, feed, days, fix_dose, name FROM `item_by_doctor` INNER JOIN item_register_to_branches ON item_register_to_branches.item_id = item_by_doctor.item_id INNER JOIN items ON items.id = item_register_to_branches.item_id WHERE `tokan_no` = '$token_no' OR `tokan_no` IN (SELECT id FROM tokans WHERE tokans.previous_tokan_no = '$token_no' ) AND item_register_to_branches.branch_id = '1' ";
                // $run_reception_medicine = mysqli_query($con, $select_reception_medicine);
                // if(mysqli_num_rows($run_reception_medicine) > 0)
                // {
                //     echo '<div class = "col-md-6">';
            	   // echo '<div style = "text-align: center;"><strong>MEDICINE BY RECEPTION</strong></div></br>';
                //     while($row_reception_medicine = mysqli_fetch_array($run_reception_medicine))
                //     {
                //         echo $row_reception_medicine['name'].'</br>';
                //     }
                //     echo '</div>';
                // }
                // else
                // {
                //     echo '<div class = "col-md-6"><strong>NOTHING TO DISPLAY '.$select_reception_medicine.'</strong></div>';
                // }
        	    echo '</div>';
        	}
	} ?>
	</div>
</div>

</body>
</html>