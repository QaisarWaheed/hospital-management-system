<?php
include 'includes/connect.php';
require_once __DIR__ . '/../includes/report_helpers.php';

$params = gynae_report_resolve_params($_GET, $_POST, (int) $bk_branch_id);
$br_id = (int) $params['br_id'];
$date = $params['date'];

$today_date = date('Y-m-d', strtotime('+4 month', strtotime(date('Y-m-d'))));
$four_month_age_date = date('Y-m-d', strtotime('+8 month', strtotime(date('Y-m-d'))));

if ($br_id > 0) {
    $select_dr = "SELECT gynae_register.id, gynae_register.token_no, gynae_register.next_visit_date, gynae_register.weeks, patients.id, patients.name, gynae_register.phone, gynae_register.created, branchs.tag_name, users.u_name, COUNT(gynae_register_history.id) AS total_visits FROM `gynae_register` INNER JOIN users ON register_by_doctor = users.id INNER JOIN branchs ON gynae_register.branch_id = branchs.id LEFT JOIN gynae_register_history ON gynae_register.id = gynae_register_history.gynae_register_id INNER JOIN tokans ON gynae_register.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id WHERE gynae_register.branch_id = '$br_id' AND gynae_register.weeks > '$four_month_age_date' AND gynae_register.weeks <= '$today_date' AND gynae_register.status = '1' GROUP BY gynae_register.id ORDER BY gynae_register.weeks";
} else {
    $select_dr = "SELECT gynae_register.id, gynae_register.token_no, gynae_register.next_visit_date, gynae_register.weeks, patients.id, patients.name, gynae_register.phone, gynae_register.created, branchs.tag_name, users.u_name, COUNT(gynae_register_history.id) AS total_visits FROM `gynae_register` INNER JOIN users ON register_by_doctor = users.id INNER JOIN branchs ON gynae_register.branch_id = branchs.id LEFT JOIN gynae_register_history ON gynae_register.id = gynae_register_history.gynae_register_id INNER JOIN tokans ON gynae_register.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id WHERE gynae_register.weeks <= '$four_month_age_date' AND gynae_register.weeks >= '$today_date' AND gynae_register.status = '1' GROUP BY gynae_register.id ORDER BY gynae_register.weeks";
}
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
    <title>GYNAE PROGRESS <?php echo ycdo_safe_date_format($date, 'd-m-Y'); ?><?php echo get_branch_tag_name_by_id($br_id); ?></title>
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
<div class="row" id = "submitBody">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-12 background_whitesmoke">
		<?php include 'navigation_top.php'; ?>
	</div>
<table border = "solid" class = "table table-bordered" style = "">
<caption style = "text-align: center; caption-side: top; color: black;">
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>GYNAE PROGRESS <?php echo ycdo_safe_date_format($date, 'd-m-Y'); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>ID</th>
            <th>TOKEN</th>
            <th>BRANCH</th>
            <th>DATE</th>
            <th>PATIENT</th>
            <th>PHONE</th>
            <th>DOCTOR</th>
            <th>E.E.D</th>
            <th>VISIT DATE</th>
            <th>TOTAL VISITS</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$run_dr = mysqli_query($con, $select_dr);
if(mysqli_num_rows($run_dr) > 0)
{
    while($row_dr = mysqli_fetch_array($run_dr))
    {
        $dr_id = $row_dr['id'];
        $token_no = $row_dr['token_no'];
        $name = $row_dr['name'];
        $phone = $row_dr['phone'];
        $created = $row_dr['created'];
        $tag_name = $row_dr['tag_name'];
        $u_name = $row_dr['u_name'];
        $total_visits = $row_dr['total_visits'];
        $next_visit_date = $row_dr['next_visit_date'];
        $weeks = $row_dr['weeks'];
        $s++;
        echo '
        <tr style = "text-align: center;">
            <td>'.$s.'</td>
            <td>'.$dr_id.'</td>
            <td>'.$token_no.'</td>
            <td>'.$tag_name.'</td>
            <td>'.ycdo_safe_date_format($created, 'd-m-y').'</td>
            <td>'.$name.'</td>
            <td>'.$phone.'</td>
            <td>'.$u_name.'</td>
            <td>'.ycdo_safe_date_format($weeks, 'd-m-y').'</td>
            <td>'.ycdo_safe_date_format($next_visit_date, 'd-m-y').'</td>
            <td>'.$total_visits.'</td>
        </tr>';

    }
}
?>
    </tbody>
</table>
</div>
</body>
</html>
<?php mysqli_close($con); ?>
