<?php include 'includes/connect.php'; 
?>
<?php include 'includes/head.php'; ?>
	<title>Patient Discharge - <?php echo $company_trademark; ?></title>
<style>
@page 
{
  size: A4;
  margin: 10px 0px 10px 0px;
}
@media print 
{
html, body 
{
    width: 210mm;
    height: 297mm;
    font-size: 9px;
}
.noprint
{
    display: none;
}
}    
</style>
</head>

<body onload = "window.print()" class="background_image_ycdo">
<!--</body> onkeydown="return (event.keyCode != 116)">-->
	<div class="col-md-12 noprint" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>
<div>

	<div class="">

		<div class="row" style="margin: 0px;">

        	<div class="col-md-3 background_whitesmoke noprint">
        		<?php include 'left_navigation.php'; ?>
        	</div>
			<div class="col-md-9" style = "text-transform: uppercase;">
                <?php
                if(isset($_GET['update']) && $_GET['update'] != '')
                {
                    $up_id = $_GET['update'];
                    $select_gynae_register = "SELECT * FROM `gynae_register` WHERE id = '$up_id' ";
                    $run_gynae_register = mysqli_query($con, $select_gynae_register);
                    if(mysqli_num_rows($run_gynae_register) > 0)
                    {
                    while($row_gynae_register = mysqli_fetch_array($run_gynae_register))
                        {
                            $gynae_register_id = $row_gynae_register['id'];
                            $gynae_register_branch_id = $row_gynae_register['branch_id'];
                            $gynae_register_token_no = $row_gynae_register['token_no'];
                            $patient_name = get_patient_name_by_token_no($row_gynae_register['token_no']);
                            $husband_name = $row_gynae_register['husband_name'];
                            $husband_phone = $row_gynae_register['husband_phone'];
                            $husband_phone = $row_gynae_register['husband_phone'];
                            $husband_blood_group = $row_gynae_register['husband_blood_group'];
                            $blood_group = $row_gynae_register['blood_group'];
                            $doctor_name = get_uname_by_id($row_gynae_register['doctor_id']);
                            $phone = $row_gynae_register['phone'];
                        }
                    }
                    $select_gynae_register_discharge = "SELECT * FROM `gynae_register_discharge` WHERE `registeration_id` = '$up_id' ";
                    $run_gynae_register_discharge = mysqli_query($con, $select_gynae_register_discharge);
                    if(mysqli_num_rows($run_gynae_register_discharge) > 0)
                    {
                        while($row_gynae_register_discharge = mysqli_fetch_array($run_gynae_register_discharge))
                        {
                            $gynae_register_id = $row_gynae_register_discharge['id'];
                            $registeration_id = $row_gynae_register_discharge['registeration_id'];
                            $token_no = $row_gynae_register_discharge['token_no'];
                            $procedure_token_no = $row_gynae_register_discharge['procedure_token_no'];
                            $patient_name = $row_gynae_register_discharge['patient_name'];
                            $phone = $row_gynae_register_discharge['phone'];
                            $consultant = $row_gynae_register_discharge['consultant'];
                            $ota = $row_gynae_register_discharge['ota'];
                            $anesthetic = $row_gynae_register_discharge['anesthetic'];
                            $sergeon = $row_gynae_register_discharge['sergeon'];
                            $department = $row_gynae_register_discharge['department'];
                            $postal_address = $row_gynae_register_discharge['postal_address'];
                            $diagnosis = $row_gynae_register_discharge['diagnosis'];
                            $doa = $row_gynae_register_discharge['doa'];
                            $dos = $row_gynae_register_discharge['dos'];
                            $dod = $row_gynae_register_discharge['dod'];
                            $presenting_complaints = $row_gynae_register_discharge['presenting_complaints'];
                            $brief_history = $row_gynae_register_discharge['brief_history'];
                            $efap = $row_gynae_register_discharge['efap'];
                            $investigations = $row_gynae_register_discharge['investigations'];
                            $final_diagnosis = $row_gynae_register_discharge['final_diagnosis'];
                            $treatment_given = $row_gynae_register_discharge['treatment_given'];
                            $cattod = $row_gynae_register_discharge['cattod'];
                            $follow_up = $row_gynae_register_discharge['follow_up'];
                        }
                    }
                 }?>
            <div class = "row">
                <div class = "col" style = "text-align: center;">
                    <img src = "images/logo_black.jpg" height = "250" alt = "<?php echo $company_trademark; ?>" />
                    <!--<h1><?php echo $company_trademark; ?></h1>-->
                    <!--<h5><?php echo $company_ambition; ?></h5>-->
                    <h1><?php echo get_branch_name_by_branch_id($gynae_register_branch_id); ?></h1>
                    <h3><?php echo get_branch_name_by($gynae_register_branch_id); ?>, <?php echo get_branch_phone_by_branch_id($gynae_register_branch_id); ?>, <?php echo $company_phone; ?></h3>
                    <hr>
    			    <h1 style = "text-align: center;">DISCHARGE SLIP</h1>
                </div>
            </div>  
            <div class = "row">
                <div class = "col">
                    <label>REGISTRATION NO</label>:<?php echo $up_id; ?>
                </div>
                <div class = "col">
                    <label>CONSULTANT</label>:<?php echo $doctor_name; ?>
                </div>
                <div class = "col">
                    <label>TOKEN NO</label>:<?php echo $gynae_register_token_no; ?>
                </div>
            </div>  
            <div class = "row">
                <div class = "col">
                    <label>PATIENT NAME</label>:<?php echo $patient_name; ?>
                </div>
                <div class = "col">
                    <label>PATIENT PHONE</label>:<?php echo $phone; ?>
                </div>
                <div class = "col">
                    <label>PATIENT BLOOD</label>:<?php echo $blood_group; ?>
                </div>
            </div>  
            <div class = "row">
                <div class = "col">
                    <label>HUSBAND NAME</label>:<?php echo $husband_name; ?>
                </div>
                <div class = "col">
                    <label>HUSBAND PHONE</label>:<?php echo $husband_phone; ?>
                </div>
                <div class = "col">
                    <label>HUSBAND BLOOD</label>:<?php echo $husband_blood_group; ?>
                </div>
            </div>  
            <div class = "row">
                <div class = "col">
                <h3>registeration_id</h3>
                <?php echo $registeration_id; ?>
                </div>
                <div class = "col">
                <h3>token_no</h3> 
                <?php echo $token_no; ?>
                </div>
                <div class = "col">
                <h3>procedure_token_no</h3> 
                <?php echo $procedure_token_no; ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>consultant</h3> 
                <?php echo get_uname_by_id($consultant); ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>ota</h3> 
                <?php echo get_uname_by_id($ota); ?>
                </div>
                <div class = "col">
                <h3>anesthetic</h3> 
                <?php echo get_uname_by_id($anesthetic); ?>
                </div>
                <div class = "col">
                <h3>sergeon</h3> 
                <?php echo get_uname_by_id($sergeon); ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>department</h3> 
                <?php echo $department; ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>postal_address</h3> 
                <?php echo $postal_address; ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>diagnosis</h3> 
                <?php echo $diagnosis; ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>doa</h3> 
                <?php echo $doa; ?>
                </div>
                <div class = "col">
                <h3>dos</h3> 
                <?php echo $dos; ?>
                </div>
                <div class = "col">
                <h3>dod</h3> 
                <?php echo $dod; ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>presenting_complaints</h3> 
                <?php echo $presenting_complaints; ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>brief_history</h3> 
                <?php echo $brief_history; ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>efap</h3> 
                <?php echo $efap; ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>investigations</h3> 
                <?php echo $investigations; ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>final_diagnosis</h3> 
                <?php echo $final_diagnosis; ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>treatment_given</h3> 
                <?php echo $treatment_given; ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>cattod</h3> 
                <?php echo $cattod; ?>
                </div>
            </div>
            <div class = "row">
                <div class = "col">
                <h3>follow_up</h3> 
                <?php echo $follow_up; ?>
                </div>
            </div>
            
			</div>
		</div>
	</div>
</div>
</body>
</html>
<script type="text/javascript">
     setTimeout(function () { window.location.replace("gynae_registeration.php"); }, 300); 
</script>