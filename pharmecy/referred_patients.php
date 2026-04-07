<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
if(isset($_GET['from_branch']) && $_GET['from_branch'] != '')
{
    $br_id = $_GET['from_branch'];
}
else
{
    $br_id = $branch_id;
}
?>
	<title>DOCTOR TURN - <?php echo $company_trademark; ?></title>
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
				<label><h1>Referred Patient </h1></label>
			</div>

                    <div class="col-md-12">
            			<table class = "table">
        			    <thead>
        			        <form>
        			        <tr>
        			            <th colspan = "6">
        			                <select onchange="this.form.submit()" class = "form-control" name = "from_branch" id = "from_branch">
        			                    <?php
        			                    $select = "SELECT * FROM branchs WHERE status = '1' ";
        			                    $run = mysqli_query($con, $select);
        			                    if(mysqli_num_rows($run))
        			                    {
        			                        while($row = mysqli_fetch_array($run))
        			                        {
        			                            if($br_id == $row['id'])
        			                            {
        			                                echo '<option SELECTED value = "'.$row['id'].'">'.$row['address'].'</option>';
        			                            }
        			                            else
        			                            {
        			                                echo '<option value = "'.$row['id'].'">'.$row['address'].'</option>';
        			                            }
            			                            
        			                        }
        			                    }
        			                    ?>
        			                </select>
        			            </th>
        			        </tr>
        			        </form>
        			        <tr>
        			            <th>S #</th>
        			            <th>Date</th>
        			            <th>Token #</th>
        			            <th>Patient Name</th>
        			            <th>Patient Phone</th>
        			            <th>From</th>
        			            <th>Refer By</th>
        			            <th>For</th>
        			            <th>Refer To</th>
        			        </tr>
        			    </thead>
        			    <tbody>
<?php
$s = 0;
$select_referral_token = "SELECT * FROM `referral_patients` WHERE to_user_id IN (SELECT id FROM users WHERE branch_id = '$br_id') AND user_id != 0  ORDER BY `referral_patients`.`referral_patient_id` DESC";
$run_referral_token = mysqli_query($con, $select_referral_token);
if(mysqli_num_rows($run_referral_token) > 0)
{
    while($row_referral_token = mysqli_fetch_array($run_referral_token))
    {
        $s++;
        $token_no = $row_referral_token['opd_token_id'];
        $required_opinion = $row_referral_token['required_opinion'];
        $token_created = $row_referral_token['referral_patient_created'];
        $from_branch = get_branch_tag_by($row_referral_token['branch_id']);
        $referral_patient_phone = $row_referral_token['referral_patient_phone'];
        $from_user_id = get_uname_by_id($row_referral_token['from_user_id']);
        $to_user_id = get_uname_and_time_by_id($row_referral_token['to_user_id']);
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
        			            <td><?php echo date_format(date_create($token_created), "h:i:s A d-m-y"); ?></td>
        			            <td><?php echo $token_no; ?></td>
        			            <td><?php echo $name; ?></td>
        			            <td><?php echo $referral_patient_phone; ?></td>
        			            <td><?php echo $from_branch; ?></td>
        			            <td><?php echo $from_user_id; ?></td>
        			            <td><?php echo $required_opinion; ?></td>
        			            <td><?php echo $to_user_id; ?></td>
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