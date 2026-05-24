<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
include 'includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';

set_time_limit(120);

if(isset($_GET['date']))
{
    $date = $_GET['date'];
    $br_id = (int) $_GET['br_id'];
}
elseif(isset($_POST['date']))
{
    $date = $_POST['date'];
    $br_id = (int) $_POST['br_id'];
}
else
{
    exit(0);
}

$date_esc = mysqli_real_escape_string($con, (string) $date);
$day_like = $date_esc . '%';
$month_like = mysqli_real_escape_string($con, substr((string) $date, 0, 7)) . '%';
$all_since = '2025-03-31';

$doctor_ids = progress_gynae_daily_doctor_ids($con, $br_id, $month_like);
$doctor_names = array();
if (count($doctor_ids) > 0) {
    $id_list = implode(',', array_map('intval', $doctor_ids));
    $run_names = mysqli_query($con, "SELECT id, u_name FROM users WHERE id IN ($id_list) ORDER BY u_name");
    if ($run_names) {
        while ($row = mysqli_fetch_assoc($run_names)) {
            $doctor_names[(int) $row['id']] = (string) $row['u_name'];
        }
    }
}

$opd_day = progress_opd_count_by_doctor_lte10($con, $br_id, $day_like);
$token_day = progress_gynae_ibd_row_count_by_doctor($con, $br_id, $day_like);
$system_day = progress_gynae_register_count_by_doctor($con, $br_id, $day_like);
$token_month = progress_gynae_ibd_row_count_by_doctor($con, $br_id, $month_like);
$system_month = progress_gynae_register_count_by_doctor($con, $br_id, $month_like);
$token_all = progress_gynae_token_count_by_doctor_since($con, $br_id, $all_since);
$system_all = progress_gynae_register_count_by_doctor_since($con, $br_id, $all_since);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta lang="en">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/nav_style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"> 
    <script src="js/jquery.min.js"></script>    
    <script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
    <title>GYNAE PROGRESS <?php echo date_format(date_create($date), "d-m-Y"); ?><?php echo get_branch_tag_name_by_id($br_id); ?></title>
<style>
@media print 
{  
    @page 
    {  
        size: 210mm 297mm;
    }    
    body
    {
        font-size:xx-small;
    }
}   
</style>
</head>
<body>
<div class="row">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-12 background_whitesmoke">
		<?php include 'navigation_top.php'; ?>
	</div>
</div>    
<table class = "table table-hover" border = "solid">
<caption style = "text-align: center; caption-side: top; color: black;">
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS DAILY <?php echo date_format(date_create($date), "d-m-Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th colspan = "3"></th>
            <th colspan = "3">TODAY</th>
            <th colspan = "3">CURRENT MONTH</th>
            <th colspan = "3">ALL RECORDS</th>
        </tr>
        <tr>
            <th>S#</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>GYNAE TOKEN</th>
            <th>GYNAE ONLINE</th>
            <th>NOT ADDED</th>
            <th>GYNAE TOKEN</th>
            <th>GYNAE ONLINE</th>
            <th>NOT ADDED</th>
            <th>GYNAE TOKEN</th>
            <th>GYNAE ONLINE</th>
            <th>NOT ADDED</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$count_opd = 0;
$count_gynae_system = 0;
$count_gynae_system_token = 0;
$count_gynae_system_token_current = 0;
$count_gynae_system_current = 0;
$count_gynae_system_token_all = 0;
$count_gynae_system_all = 0;

if (count($doctor_ids) > 0) {
    foreach ($doctor_ids as $dr_id) {
        $dr_name = $doctor_names[$dr_id] ?? get_uname_by_id($dr_id);
        $opd = $opd_day[$dr_id] ?? 0;
        $gynae_count_system_token = $token_day[$dr_id] ?? 0;
        $gynae_count_system = $system_day[$dr_id] ?? 0;
        $gynae_count_system_token_current = $token_month[$dr_id] ?? 0;
        $gynae_count_system_current = $system_month[$dr_id] ?? 0;
        $gynae_count_system_token_all = $token_all[$dr_id] ?? 0;
        $gynae_count_system_all = $system_all[$dr_id] ?? 0;

        $count_opd += $opd;
        $count_gynae_system_token += $gynae_count_system_token;
        $count_gynae_system += $gynae_count_system;
        $count_gynae_system_token_current += $gynae_count_system_token_current;
        $count_gynae_system_current += $gynae_count_system_current;
        $count_gynae_system_token_all += $gynae_count_system_token_all;
        $count_gynae_system_all += $gynae_count_system_all;

        $s++;
        echo '
        <tr style = "text-align: center;">
            <td>'.$s.'</td>
            <td style = "text-align: left;">'.$dr_name.'</td>
            <td>'.$opd.'</td>
            <td>'.$gynae_count_system_token.'</td>
            <td>'.$gynae_count_system.'</td>
            <td>'.$gynae_count_system-$gynae_count_system_token.'</td>
            
            <td>'.$gynae_count_system_token_current.'</td>
            <td>'.$gynae_count_system_current.'</td>
            <td>'.$gynae_count_system_current-$gynae_count_system_token_current.'</td>
            
            <td>'.$gynae_count_system_token_all.'</td>
            <td>'.$gynae_count_system_all.'</td>
            <td>'.$gynae_count_system_all-$gynae_count_system_token_all.'</td>
        </tr>';
    }
}
?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan = "2">TOTAL</th>
            <th><?php echo $count_opd; ?></th>
            
            <th><?php echo $count_gynae_system_token; ?></th>
            <th><?php echo $count_gynae_system; ?></th>
            <th><?php echo $count_gynae_system-$count_gynae_system_token; ?></th>
            
            <th><?php echo $count_gynae_system_token_current; ?></th>
            <th><?php echo $count_gynae_system_current; ?></th>
            <th><?php echo $count_gynae_system_current-$count_gynae_system_token_current; ?></th>
            
            <th><?php echo $count_gynae_system_token_all; ?></th>
            <th><?php echo $count_gynae_system_all; ?></th>
            <th><?php echo $count_gynae_system_all-$count_gynae_system_token_all; ?></th>
        </tr>
    </tfoot>
</table>
</body>
</html>
<?php mysqli_close($con); ?>
