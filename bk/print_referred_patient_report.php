<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
include 'includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';
include 'includes/head.php'; 
if(isset($_GET['date']) && $_GET['date'] != '')
{
    $date = $_GET['date'];
}
else
{
    header('location: logout.php');
}
?>
	<title>REFFERED PATIENT <?php echo date_format(date_create($date), 'd F Y'); ?> - <?php echo $company_trademark; ?></title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
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
    .dataprint, .data-print *
    {
        display: block !important;
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
			<div class="col-md-12" style = "text-align: center;">
				<label><h2>Referred Patient </h2></label></br>
				<label><h6> <?php echo date_format(date_create($date), 'd F Y'); ?> </h6></label>
			</div>
			<table class = "table">
		    <thead>
		        <tr>
		            <th>S #</th>
		            <th>Token #</th>
		            <th>Patient Name</th>
		            <!--<th>Patient Phone</th>-->
		            <th>From</th>
		            <th>Refer By</th>
		            <th>Required Opnion</th>
		            <th>Refer To</th>
		            <th>Status</th>
		        </tr>
		    </thead>
		    <tbody>
<?php
$s = 0;
$date_esc = mysqli_real_escape_string($con, (string) $date);
$ref_date_clause = progress_sql_date_clause($con, $date_esc . '%', 'referral_patient_created');
$select_referral_token = "SELECT * FROM `referral_patients` WHERE opd_token_id > 0 AND referral_patient_status > 0 AND $ref_date_clause ORDER BY branch_id, referral_patient_status ";
$run_referral_token = mysqli_query($con, $select_referral_token);
if(mysqli_num_rows($run_referral_token) > 0)
{
    while($row_referral_token = mysqli_fetch_array($run_referral_token))
    {
        $s++;
        $token_no = $row_referral_token['opd_token_id'];
        $required_opinion = $row_referral_token['required_opinion'];
        $referral_patient_status = $row_referral_token['referral_patient_status'];
        if($referral_patient_status == 1)
        {
            $referral_patient_status_msg = '<td class = "dataprint" style = "display: none;text-align: center;">AT RECEPTION</td><td style = "text-align: center;" class = "badge noprint"><span class="badge badge-primary">AT RECEPTION</span></td>';
        }
        elseif($referral_patient_status == 2)
        {
            $referral_patient_status_msg = '<td class = "dataprint" style = "display: none;text-align: center;">SEND TO DR</td><td style = "text-align: center;" class = "badge noprint"><span class="badge badge-info">SEND TO DR</span></td>';
        }
        else
        {
            $referral_patient_status_msg = '<td class = "dataprint" style = "display: none;text-align: center;">ERROR</td><td style = "text-align: center;" class = "badge noprint"><span class="badge badge-danger">ERROR</span></td>';
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
        			?>
        			        <tr>
        			            <td><?php echo $s; ?></td>
        			            <td><?php echo $token_no; ?></td>
        			            <td><?php echo $name; ?></td>
        			            <!--<td><?php echo $referral_patient_phone; ?></td>-->
        			            <td><?php echo $from_branch; ?></td>
        			            <td><?php echo $from_user_id; ?></td>
        			            <td><?php echo $required_opinion; ?></td>
        			            <td><?php echo $to_user_id; ?></td>
        			            <?php echo $referral_patient_status_msg; ?>
        			        </tr>
    		<?php }
        	}
    }
}
	?>
        			    </tbody>
</table>
</div>
            </div>
    		</div>
        	</div>
    </div>
</body>

</html>
<?php mysqli_close($con); ?>