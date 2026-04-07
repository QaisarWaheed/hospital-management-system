<?php include 'includes/connect.php'; 
include 'includes/head.php';
    $msg = '';
if(isset($_GET['token_id']) && $_GET['token_id'] != '')
{
    $token_id = $_GET['token_id'];
}
elseif(isset($_POST['token_no']) && $_POST['token_no'] != '')
{
    $token_id = $_POST['token_no'];
    $reply_by_consultant = $_POST['reply_by_consultant'];
    if(mysqli_query($con, "UPDATE referral_patients SET `reply_by_consultant` = '$reply_by_consultant', `referral_patient_status` = referral_patient_status+1 WHERE `token_id` = '$token_id' "))
    $msg = '<div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>SUCCESS!</strong> DATA UPDATED.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>';
}
else
{
    // header('location: logout.php');
}
?>
	<title>DOCTOR TURN - <?php echo $company_trademark; ?></title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
<script>
  $(document).ready(function () {
      $('select').selectize({
          sortField: 'text'
      });
  });   
</script>
<style>
@media print
{    
    .noprint, .no-print *
    {
        display: none !important;
    }
}    
</style>
</head>

<body class="background_image_ycdo" onkeydown="return (event.keyCode != 116)">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>
	<div class="">
		<div class="row" style="margin: 0px;">
        	<div class="col-md-3 background_whitesmoke noprint">
        		<?php include 'left_navigation.php'; ?>
        	</div>
			<div class="col-md-9">
            <div class = "row">
			<div class="col-md-12" style="text-align: center;">
				<label><h1> Update Referred Patient </h1></label>
			</div>

<div class="col-md-12">
			    <?php echo $msg; ?>
<?php
$s = 0;
$select_referral_token = "SELECT * FROM `referral_patients` WHERE `token_id` = '$token_id' ";
$run_referral_token = mysqli_query($con, $select_referral_token);
if(mysqli_num_rows($run_referral_token) == 1)
{
    while($row_referral_token = mysqli_fetch_array($run_referral_token))
    {
        $referral_patient_id = $row_referral_token['referral_patient_id'];
        $token_id = $row_referral_token['token_id'];
        $token_no = $row_referral_token['opd_token_id'];
        $required_opinion = $row_referral_token['required_opinion'];
        $reply_by_consultant = $row_referral_token['reply_by_consultant'];
        $referral_patient_status = $row_referral_token['referral_patient_status'];
        if($referral_patient_status == 1)
        {
            $referral_patient_status_msg = '<td lass = "badge badge-primary"><span class="badge badge-primary">AT RECEPTION</span></td>';
        }
        elseif($referral_patient_status == 2)
        {
            $referral_patient_status_msg = '<td lass = "badge badge-info"><span class="badge badge-info">SEND TO DOCTOR</span></td>';
        }
        elseif($referral_patient_status > 2)
        {
            $referral_patient_status_msg = '<td lass = "badge badge-success"><span class="badge badge-success">Adviced for Doctor</span></td>';
        }
        else
        {
            $referral_patient_status_msg = '<td lass = "badge badge-danger"><span class="badge badge-danger">ERROR</span></td>';
        }
        $from_branch = get_branch_tag_by($row_referral_token['branch_id']);
        $referral_patient_phone = $row_referral_token['referral_patient_phone'];
        $from_user_id = get_uname_by_id($row_referral_token['from_user_id']);
        $to_user_id = get_uname_by_id($row_referral_token['to_user_id']);
        	$select_tokan = "SELECT * FROM tokans WHERE id = '$token_no' ";
        	$run_tokan = mysqli_query($con, $select_tokan);
        	if (mysqli_num_rows($run_tokan) == 1) 
        	{
        		while ($row_tokan = mysqli_fetch_array($run_tokan)) 
        		{
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
        				}
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
<form action = "update_refereeal_patient.php" method = "POST">
<table class = "table">
    <thead>
        <tr>
            <th>Id #</th>
            <td><?php echo $referral_patient_id; ?></td>
        </tr>
        <tr>
            <th>Token #</th>
            <td>
                <?php echo $token_no; ?>
                <input type = "hidden" name = "token_no" value = "<?php echo $token_id; ?>" />
            </td>
        </tr>
        <tr>
            <th>Patient Name</th>
            <td><?php echo $name; ?></td>
        </tr>
        <tr>
            <th>Patient Phone</th>
            <td><?php echo $referral_patient_phone; ?></td>
        </tr>
        <tr>
            <th>From</th>
            <td><?php echo $from_branch; ?></td>
        </tr>
        <tr>
            <th>Refer By</th>
            <td><?php echo $from_user_id; ?></td>
        </tr>
        <tr>
            <th>Required Opnion</th>
            <td><?php echo $required_opinion; ?></td>
        </tr>
        <tr>
            <th>Refer To</th>
            <td><?php echo $to_user_id; ?></td>
        </tr>
        <tr>
            <th>Advice For Further Treatment</th>
            <td>
                <textarea name = "reply_by_consultant" class = "form-control" rows = "4" style = "resize: none;"><?php echo $reply_by_consultant; ?></textarea>
            </td>
        </tr>
        <tr>
            <th>Status</th>
            <?php echo $referral_patient_status_msg; ?>
        </tr>
        <tr>
            <?php
            if($referral_patient_status > 1)
            {
                echo '<td colspan= "2" style = "text-align: center;">
                    <input type = "submit" name = "token" value = "SAVE ADVICE" class = "btn btn-success btn-sm" />
                </td>';
            } ?>
        </tr>
    </thead>
    <tbody>
        <tr>
        </tr>
    </tbody>
</table>
</form>
</div>
            </div>
    		</div>
        	</div>
    </div>
</body>

</html>
<?php mysqli_close($con); ?>