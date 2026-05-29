<?php
require_once __DIR__ . '/includes/connect.php';
include 'includes/head.php';

$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if ($run_roles && mysqli_num_rows($run_roles) == 1)
{
    while($row_role = mysqli_fetch_array($run_roles))
    {
        $role_title = $row_role['title'];
    }
}
else
{
    $role_title = '';
}

$date = date('Y-m');
$doctor = 0;
$br_id = (int) $bk_branch_id;
$output_procedure = '';
$total_collections = 0;
$total_cons_opds_cash = 0;
if (isset($_POST['date']) && $_POST['date'] !== '') {
    $date = $_POST['date'];
}
if (isset($_POST['doctor_id']) && $_POST['doctor_id'] !== '') {
    $doctor = (int) $_POST['doctor_id'];
    $br_id = get_branch_id_by_user_id($doctor);
}
?>
	<title>DOCTOR MONTHLY PROFILE - <?php echo $date; ?> <?php echo $company_trademark; ?></title>
<script src="js/jquery.min.js"></script>
<script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
<style>
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
}    
</style>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke no-print">	<?php include 'left_navigation.php'; ?>	
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo htmlspecialchars($bk_name); if ($bk_is_incharge == 2) { echo ' Incharge '; } ?>(<?php echo htmlspecialchars($role_title); ?>)</h3>
    </div>
    <div class = "col-md-9">
        <form METHOD = "POST">
        <div class = "row no-print">
            <div class = "col-md-12">
                <h2 align = "center"><?php echo htmlspecialchars($bk_branch_name); ?></h2>
            </div>
            <div class = "col-md-12">
                <label>DOCTOR</label>
                <select name = "doctor_id" class = "form-control" required>
                    <?php 
                    if(isset($_POST['doctor_id']) && $_POST['doctor_id'] != '')
                    {
                        echo '<option value = "'.$_POST['doctor_id'].'">'.get_uname_by_id($_POST['doctor_id']).'</option>';
                    }
                        echo get_doctor_option($br_id); ?>
                </select>
            </div>
            <div class = "col-md-12">
                <label>DATE</label>
                <input required type = "month" value = "<?php if(isset($_POST['date'])){echo $_POST['date'];}else{echo date('Y-m');} ?>" name = "date" id = "date" class = "form-control" />
                <input type = "submit" name = "progress" value = "PROGRESS" class = "btn btn-sm btn-info" />
                <input type = "reset" name = "reset" value = "CLEAR" class = "btn btn-sm btn-danger" />
            </div>
        </div>
        </form>
<?php
$s = 0; 
$labs = 0;
$medicines = 0;
$total_admission = 0;
$total_procedure = 0;
$total_dnc = 0;
$total_svd = 0;
$total_reffered = 0;
$total_reffered_opd = 0;
$total_gynae_system = 0;
$total_usg = 0;
$total_lab = 0;
$total_medicine = 0;
$total_opds = 0;
$total_cons_opds = 0;
$total_gynae = 0;
if (isset($_POST['date']) && isset($_POST['doctor_id']) && $doctor > 0)
{
    echo '
    <table class = "table" border = "solid">
    <caption style = "caption-side: top; text-align: center;color: black;">
        <h3>SUMMERY REPORT OF '.ycdo_safe_date_format($date.'-01', 'F Y', $date).'</h3>
    </caption>
    <thead>
        <tr>
            <th>NAME</th>
            <th>OPD</th>
            <th>CONS</th>
            <th>LAB</th>
            <th>USG</th>
            <th>SVD</th>
            <th>D&C</th>
            <th>PROCEDURE</th>
            <th>ADMISSION</th>
            <th>GYNAE SYSTEM</th>
            <th>REFERRED BY</th>
            <th>REFERRED OPD</th>
            <th>COLLECTION</th>
        </tr>
    </thead>
    <tbody>';
    {
        $opd = mysqli_query($con, "SELECT COUNT(id) FROM tokans WHERE `tokan_type_id` < 100 AND status = 1 and doctor_id = '$doctor' AND created like '$date%' AND `branch_id` = '$br_id' ");
        $opds = 0;
        if ($opd) {
        while($row_opd = mysqli_fetch_array($opd))
        {
            $opds = $row_opd['0'];
            $total_opds = $total_opds + $opds;
        }
        }

        $collection = mysqli_query($con, "SELECT SUM(cash) FROM tokans WHERE status = 1 and doctor_id = '$doctor' AND created like '$date%' AND `branch_id` = '$br_id' ");
        if ($collection) {
        while($row_collection = mysqli_fetch_array($collection))
        {
            $collections = $row_collection['0'];
            $total_collections = $total_collections + $collections;
        }
        }

        $total_cons_opds_cash = 0;
        $select_cons_opd = "SELECT DISTINCT tokan_no, tokans.cash AS cash FROM item_by_doctor INNER JOIN tokans ON item_by_doctor.tokan_no = tokans.id INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id WHERE item_by_doctor.doctor_id LIKE '$doctor' AND item_by_doctor.created LIKE '$date%' AND item_by_doctor.branch_id = '$br_id' AND item_by_doctor.status = 2 AND item_register_to_branches.item_id IN( SELECT id FROM items WHERE category_id = '29' )";
        $run_cons_opd = mysqli_query($con,$select_cons_opd);
        $cons_opds = ($run_cons_opd) ? mysqli_num_rows($run_cons_opd) : 0;
        $total_cons_opds = $total_cons_opds + $cons_opds;
        if($cons_opds > 0)
        {
            while($row_cons_opd = mysqli_fetch_array($run_cons_opd))
            {
                $row_cons_opd_cash = $row_cons_opd['cash'];
                $total_cons_opds_cash = $total_cons_opds_cash + $row_cons_opd_cash;
            }
        }

        $select_svd = "SELECT DISTINCT tokan_no FROM item_by_doctor INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id WHERE item_by_doctor.doctor_id LIKE '$doctor' AND item_by_doctor.created LIKE '$date%' AND item_by_doctor.branch_id = '$br_id' AND item_by_doctor.status = 2 AND item_register_to_branches.item_id IN( SELECT id FROM items WHERE id IN(472, 1118, 1313, 1577) )";
        $svds = mysqli_num_rows(mysqli_query($con, $select_svd));
        $total_svd = $total_svd + $svds;

        $select_dnc = "SELECT DISTINCT tokan_no FROM item_by_doctor INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id WHERE item_by_doctor.doctor_id LIKE '$doctor' AND item_by_doctor.created LIKE '$date%' AND item_by_doctor.branch_id = '$br_id' AND item_by_doctor.status = 2 AND item_register_to_branches.item_id IN( SELECT id FROM items WHERE id IN(473, 1119, 1314, 1578) )";
        $dncs = mysqli_num_rows(mysqli_query($con, $select_dnc));
        $total_dnc = $total_dnc + $dncs;

        $select_procedure = "SELECT DISTINCT tokan_no FROM item_by_doctor INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id WHERE item_by_doctor.doctor_id LIKE '$doctor' AND item_by_doctor.created LIKE '$date%' AND item_by_doctor.branch_id = '$br_id' AND item_by_doctor.status = 2 AND item_register_to_branches.item_id IN( SELECT id FROM items WHERE id NOT IN(473, 1119, 1314, 472, 1118, 1313) AND category_id = 3 )";
        $procedures = mysqli_num_rows(mysqli_query($con, $select_procedure));
        $total_procedure = $total_procedure + $procedures;


        $select_lab = "SELECT DISTINCT tokan_no FROM item_by_doctor INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id WHERE item_by_doctor.doctor_id LIKE '$doctor' AND item_by_doctor.created LIKE '$date%' AND item_by_doctor.branch_id = '$br_id' AND item_by_doctor.status = 2 AND item_register_to_branches.item_id IN(SELECT id FROM items WHERE category_id = '2')";
        $labs = mysqli_num_rows(mysqli_query($con, $select_lab));
        $total_lab = $total_lab + $labs;

        $select_admission = "SELECT DISTINCT tokan_no FROM item_by_doctor INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id WHERE item_by_doctor.doctor_id LIKE '$doctor' AND item_by_doctor.created LIKE '$date%' AND item_by_doctor.branch_id = '$br_id' AND item_by_doctor.status = 2 AND item_register_to_branches.item_id IN( SELECT id FROM items WHERE id IN(444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301, 1579, 1580, 1741, 1742, 1743, 1744))";
        $admissions = mysqli_num_rows(mysqli_query($con, $select_admission));
        $total_admission = $total_admission + $admissions;

        $reffered = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `referral_patients` WHERE referral_patient_created LIKE '$date%' AND from_user_id = '$doctor' AND referral_patient_status > '1' "));
        $total_reffered = $total_reffered + $reffered;

        $reffered_opd = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `referral_patients` WHERE referral_patient_created LIKE '$date%' AND to_user_id = '$doctor' AND referral_patient_status > '1' "));
        $total_reffered_opd = $total_reffered_opd + $reffered_opd;

        $select_usgs = "SELECT COUNT(`tokan_no`) FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (476, 477, 478, 479, 1138, 1185, 1161, 1162, 1163, 1164, 1184, 1317, 1318, 1319, 1411, 1435))";
        $usg = mysqli_query($con, $select_usgs);
        $usgs = 0;
        if ($usg) {
        while($row_usg = mysqli_fetch_array($usg))
        {
            $usgs = $row_usg['0'];
            $total_usg = $total_usg + $usgs;
        }
        }
        $gynae_q = mysqli_query($con, "SELECT * FROM `gynae_register` WHERE doctor_id = '$doctor' AND created like '$date%' AND branch_id = '$br_id'");
        $gynae_system = $gynae_q ? mysqli_num_rows($gynae_q) : 0;
        $total_gynae_system = $total_gynae_system + $gynae_system;
      
        $doctor_name = get_uname_by_id($doctor);
        echo ' <tr style = "text-align: right;">
                <td style = "text-align: left;">'.$doctor_name.'</td>
                <td>'.$opds.'</td>
                <td>'.$cons_opds.'('.$total_cons_opds_cash.')</td>
                <td>'.$labs.'</td>
                <td>'.$usgs.'</td>
                <td>'.$svds.'</td>
                <td>'.$dncs.'</td>
                <td>'.$procedures.'</td>
                <td>'.$admissions.'</td>
                <td>'.$gynae_system.'</td>
                <td>'.$reffered.'</td>
                <td>'.$reffered_opd.'</td>
                <td>'.$total_collections.'</td>
            </tr>';
    }
    echo '</tbody>';
}
?>
</table>
<?php 
if (isset($_POST['date']) && $_POST['date'] !== '' && isset($_POST['doctor_id']) && (int) $_POST['doctor_id'] > 0)
{ 
    $sr = 1;
    $output_opd = '';
    $opd_count = 0;
    $opd_sum = 0;
    $cons_opds = 0;
    $output_procedure = '';
    $date = $_POST['date'];
    $doctor_id = (int) $_POST['doctor_id'];
    $select_opd = "
                SELECT tokan_type_id, tokan_types.title, COUNT(cash), SUM(cash), AVG(cash) FROM `tokans` 
                INNER JOIN tokan_types ON tokans.tokan_type_id = tokan_types.id 
                WHERE tokans.created LIKE '$date%' AND doctor_id = '$doctor_id' AND tokan_type_id < 100 AND branch_id = '$br_id' 
                GROUP BY tokan_type_id ";
    $run_opd = mysqli_query($con, $select_opd);
    if(mysqli_num_rows($run_opd) > 0)
    {
        $opd_count = 0;
        $opd_sum = 0;
        $output_opd .= '
            <tr>
                <th colspan = "4"><h3 align = "center">OPD TOKENS DETAIL</h3></th>
            </tr>
            <tr>
                <th>SR</th>
                <th>TOKEN TYPE</th>
                <th>RATE</th>
                <th>COUNT</th>
                <th>TOTAL</th>
            </tr>
        ';
        while($row_opd = mysqli_fetch_array($run_opd))
        {
            $output_opd .= '
                <tr>
                    <td>'.$sr++.'</td>
                    <td>'.$row_opd['1'].'</td>
                    <td>'.intval($row_opd['4'] ?? 0).'</td>
                    <td>'.$row_opd['2'].'</td>
                    <td>'.$row_opd['3'].'</td>
                </tr>
            ';
            $opd_count = $opd_count + $row_opd['2'];
            $opd_sum = $opd_sum + $row_opd['3'];
        }
    }
    $sr_procedure = 0;
    $select_procedure = "SELECT DISTINCT `tokan_no`, tokans.cash, tokan_types.title, tokans.created FROM `item_by_doctor` INNER JOIN tokans ON item_by_doctor.tokan_no = tokans.id INNER JOIN tokan_types ON tokans.tokan_type_id = tokan_types.id WHERE item_by_doctor.doctor_id = '$doctor_id' AND item_by_doctor.created LIKE '$date%' AND item_by_doctor.branch_id = '$br_id' AND item_by_doctor.status = '2' AND item_by_doctor.item_id IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id IN (3 ,31 ,32)))";
    $run_procedure = mysqli_query($con, $select_procedure);
    if(mysqli_num_rows($run_procedure) > 0)
    {
        $procedure_count = 0;
        $procedure_sum = 0;
        $output_procedure .= '
            <tr>
                <th colspan = "4"><h3 align = "center">PROCEDURE TOKENS DETAIL</h3></th>
            </tr>
            <tr>
                <th>SR</th>
                <th>DATE</th>
                <th>TOKEN NO</th>
                <th>TOKEN TYPE</th>
                <th>AMOUNT</th>
            </tr>
        ';
        while($row_procedure = mysqli_fetch_array($run_procedure))
        {
            $sr_procedure++;
            $output_procedure .= '
                <tr>
                    <td>'.$sr_procedure.'</td>
                    <td>'.ycdo_safe_date_format($row_procedure['3'], 'd-m-Y', 'N/A').'</td>
                    <td>'.$row_procedure['0'].'</td>
                    <td>'.$row_procedure['2'].'</td>
                    <td>'.$row_procedure['1'].'</td>
                </tr>
            ';
                $procedure_sum = $procedure_sum + $row_procedure['1'];
        }
            $output_procedure .= '
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.$procedure_sum.'</td>
                </tr>
            ';
    }
$get_referal = mysqli_query($con, "SELECT COUNT(`referral_patient_id`) AS count, SUM(`received_cash`) As sum FROM `referral_patients` WHERE `to_user_id` = '$doctor_id' AND `received_cash` > 0 AND `referral_patient_created` LIKE '$date%'; ");
$row_referal = ($get_referal) ? mysqli_fetch_array($get_referal) : array('count' => 0, 'sum' => 0);
if (!$row_referal) {
    $row_referal = array('count' => 0, 'sum' => 0);
}
$row_referal['count'] = (int) ($row_referal['count'] ?? 0);
$row_referal['sum'] = (float) ($row_referal['sum'] ?? 0);
?>
        <div class = "col-md-12">
                <table class = "table">
                    <?php echo $output_opd; 
                    echo '  
                    <tr>
                        <td>'.$sr++.'</td>
                        <td>REFERRAL CHECKUP</td>
                        <td></td>
                        <td>'.$row_referal['count'].'</td>
                        <td>'.$row_referal['sum'].'</td>
                    </tr>   
                    <tr>
                        <td>'.$sr.'</td>
                        <td>CONS CHECKUP</td>
                        <td></td>
                        <td>'.$cons_opds.'</td>
                        <td>'.$total_cons_opds_cash.'</td>
                    </tr>      
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>'.$opd_count+$row_referal['count']+$cons_opds.'</th>
                        <th>'.$opd_sum+$row_referal['sum']+$total_cons_opds_cash.'</th>
                    </tr>';
                    echo $output_procedure; 
                    ?>  
                </table>
    </div>
<?php
}
?>
    </div>
</div>
</body>
<?php mysqli_close($con); ?>
</html>