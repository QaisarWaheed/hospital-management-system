<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
require_once __DIR__ . '/includes/connect_report.php';
require_once __DIR__ . '/../bk/includes/progress_report_params.php';

if (!isset($_GET['date']) || $_GET['date'] === '') {
    header('Location: logout.php');
    exit;
}
$date = $_GET['date'];
$br_id = isset($_GET['br_id']) ? (int) $_GET['br_id'] : (int) $hr_branch_id;
$date_esc = mysqli_real_escape_string($con, (string) $date);
$ref_date_clause = progress_sql_date_clause($con, $date_esc . '%', 'referral_patient_created');
include 'includes/head.php';
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
if ($br_id > 0) {
    $select_referral_token = "SELECT * FROM `referral_patients` WHERE branch_id = '$br_id' AND opd_token_id > 0 AND referral_patient_status > 0 AND $ref_date_clause AND referral_patient_status = '2' ORDER BY branch_id, referral_patient_status ";
} else {
    $select_referral_token = "SELECT * FROM `referral_patients` WHERE opd_token_id > 0 AND referral_patient_status > 0 AND $ref_date_clause ORDER BY branch_id, referral_patient_status ";
}
$referrals = array();
$run_referral_token = mysqli_query($con, $select_referral_token);
if ($run_referral_token) {
    while ($row_referral_token = mysqli_fetch_array($run_referral_token)) {
        $referrals[] = $row_referral_token;
    }
}

$token_map = array();
$patient_map = array();
$user_names = array();
$branch_tags = array();
if (count($referrals) > 0) {
    $token_ids = array();
    $user_ids = array();
    $branch_ids = array();
    foreach ($referrals as $row_referral_token) {
        $token_ids[] = (int) $row_referral_token['opd_token_id'];
        $user_ids[] = (int) $row_referral_token['from_user_id'];
        $user_ids[] = (int) $row_referral_token['to_user_id'];
        $branch_ids[] = (int) $row_referral_token['branch_id'];
    }
    $token_ids = array_values(array_unique(array_filter($token_ids)));
    $user_ids = array_values(array_unique(array_filter($user_ids)));
    $branch_ids = array_values(array_unique(array_filter($branch_ids)));

    if (count($token_ids) > 0) {
        $token_list = implode(',', $token_ids);
        $run_tokan = mysqli_query($con, "SELECT id, patient_id FROM tokans WHERE id IN ($token_list)");
        if ($run_tokan) {
            $patient_ids = array();
            while ($row_tokan = mysqli_fetch_assoc($run_tokan)) {
                $tid = (int) $row_tokan['id'];
                $pid = (int) $row_tokan['patient_id'];
                $token_map[$tid] = $pid;
                $patient_ids[] = $pid;
            }
            $patient_ids = array_values(array_unique(array_filter($patient_ids)));
            if (count($patient_ids) > 0) {
                $patient_list = implode(',', $patient_ids);
                $run_patient = mysqli_query($con, "SELECT id, name, age, gender FROM patients WHERE id IN ($patient_list)");
                if ($run_patient) {
                    while ($row_patient = mysqli_fetch_assoc($run_patient)) {
                        $patient_map[(int) $row_patient['id']] = $row_patient;
                    }
                }
            }
        }
    }

    $user_names = progress_user_names_by_ids($con, $user_ids);

    if (count($branch_ids) > 0) {
        $branch_list = implode(',', $branch_ids);
        $run_br = mysqli_query($con, "SELECT id, tag_name FROM branchs WHERE id IN ($branch_list)");
        if ($run_br) {
            while ($row_br = mysqli_fetch_assoc($run_br)) {
                $branch_tags[(int) $row_br['id']] = (string) $row_br['tag_name'];
            }
        }
    }

    foreach ($referrals as $row_referral_token) {
        $s++;
        $token_no = (int) $row_referral_token['opd_token_id'];
        if (!isset($token_map[$token_no])) {
            continue;
        }
        $required_opinion = $row_referral_token['required_opinion'];
        $referral_patient_status = $row_referral_token['referral_patient_status'];
        if ($referral_patient_status == 1) {
            $referral_patient_status_msg = '<td class = "dataprint" style = "display: none;text-align: center;">AT RECEPTION</td><td style = "text-align: center;" class = "badge noprint"><span class="badge badge-primary">AT RECEPTION</span></td>';
        } elseif ($referral_patient_status == 2) {
            $referral_patient_status_msg = '<td class = "dataprint" style = "display: none;text-align: center;">SEND TO DR</td><td style = "text-align: center;" class = "badge noprint"><span class="badge badge-info">SEND TO DR</span></td>';
        } else {
            $referral_patient_status_msg = '<td class = "dataprint" style = "display: none;text-align: center;">ERROR</td><td style = "text-align: center;" class = "badge noprint"><span class="badge badge-danger">ERROR</span></td>';
        }
        $bid = (int) $row_referral_token['branch_id'];
        $from_branch = $branch_tags[$bid] ?? get_branch_tag_by($bid);
        $referral_patient_phone = $row_referral_token['referral_patient_phone'];
        $from_uid = (int) $row_referral_token['from_user_id'];
        $to_uid = (int) $row_referral_token['to_user_id'];
        $from_user_id = $user_names[$from_uid] ?? get_uname_by_id($from_uid);
        $to_user_id = $user_names[$to_uid] ?? get_uname_by_id($to_uid);
        $patient_id = $token_map[$token_no];
        $patient_row = $patient_map[$patient_id] ?? null;
        if (!$patient_row) {
            continue;
        }
        $name = $patient_row['name'];
        $age = $patient_row['age'];
        $gender = $patient_row['gender'];
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
    <?php
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