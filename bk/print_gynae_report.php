<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
include 'includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';

set_time_limit(120);

if(isset($_GET['date']))
{
    $date = $_GET['date'];
}
elseif(isset($_POST['date']))
{
    $date = $_POST['date'];
}
else
{
    exit(0);
}

$date_esc = mysqli_real_escape_string($con, (string) $date);
$day_like = $date_esc . '%';
$date_from_start = date_format(date_create($date), 'Y-m');
$month_like = mysqli_real_escape_string($con, $date_from_start) . '%';

$svd_items = '472, 1118, 1313';
$dnc_items = '473, 1119, 1314';
$gynae_items = '483, 1159, 1321, 1414';

$branches = progress_gynae_report_branches($con, $month_like);
?>
<html>
<head>
    <title>PRINT GYNAE REPORT <?php echo date_format(date_create($date), " d F Y"); ?></title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h3>GYNAE REPORT DATE <?php echo date_format(date_create($date), " d F Y"); ?></h3>
</caption>
<?php
foreach ($branches as $br_row) {
    $br_id = $br_row['id'];
    $address = $br_row['address'];

    $day_svd = progress_ibd_tokan_row_count_by_doctor($con, $br_id, $day_like, $svd_items);
    $month_svd = progress_ibd_tokan_row_count_by_doctor($con, $br_id, $month_like, $svd_items);
    $day_dnc = progress_ibd_tokan_row_count_by_doctor($con, $br_id, $day_like, $dnc_items);
    $month_dnc = progress_ibd_tokan_row_count_by_doctor($con, $br_id, $month_like, $dnc_items);
    $day_gynae = progress_ibd_tokan_row_count_by_doctor($con, $br_id, $day_like, $gynae_items);
    $month_gynae = progress_ibd_tokan_row_count_by_doctor($con, $br_id, $month_like, $gynae_items);
    $day_gynae_system = progress_gynae_register_count_by_doctor($con, $br_id, $day_like);
    $month_gynae_system = progress_gynae_register_count_by_doctor($con, $br_id, $month_like);
    $day_procedure = progress_gynae_procedure_tokan_count_by_doctor($con, $br_id, $day_like);
    $month_procedure = progress_gynae_procedure_tokan_count_by_doctor($con, $br_id, $month_like);

    $doctor_ids = progress_gynae_report_doctor_ids($con, $br_id, $month_like);
    ?>
        <tr>
            <th colspan = "12"><h2><?php echo $address; ?></h2></th>
        </tr>
        <tr>
            <th>S#</th>
            <th>NAME</th>
            <th>SVD</th>
            <th>TOTAL SVD</th>
            <th>DNC</th>
            <th>TOTAL DNC</th>
            <th>PROCEDURE</th>
            <th>TOTAL PROCEDURE</th>
            <th>GYNAE TOKEN</th>
            <th>TOTAL TOKEN</th>
            <th>GYNAE SYSTEM</th>
            <th>TOTAL SYSTEM</th>
        </tr>
<?php
    $s = 0;
    $total_svd = 0;
    $total_dnc = 0;
    $total_procedure = 0;
    $total_gynaes = 0;
    $total_gynae_systems = 0;
    $total_total_svd = 0;
    $total_total_dnc = 0;
    $total_total_procedure = 0;
    $total_total_gynae = 0;
    $total_total_gynae_system = 0;

    if (count($doctor_ids) > 0) {
        foreach ($doctor_ids as $doctor) {
            $s = $s + 1;
            $svds = $day_svd[$doctor] ?? 0;
            $total_svd = $total_svd + $svds;
            $total_svds = $month_svd[$doctor] ?? 0;
            $total_total_svd = $total_total_svd + $total_svds;
            $total_dncs = $month_dnc[$doctor] ?? 0;
            $total_total_dnc = $total_total_dnc + $total_dncs;
            $dncs = $day_dnc[$doctor] ?? 0;
            $total_dnc = $total_dnc + $dncs;
            $gynaes = $day_gynae[$doctor] ?? 0;
            $total_gynaes = $total_gynaes + $gynaes;
            $total_gynae = $month_gynae[$doctor] ?? 0;
            $total_total_gynae = $total_total_gynae + $total_gynae;
            $gynae_systems = $day_gynae_system[$doctor] ?? 0;
            $total_gynae_systems = $total_gynae_systems + $gynae_systems;
            $total_gynae_system = $month_gynae_system[$doctor] ?? 0;
            $total_total_gynae_system = $total_total_gynae_system + $total_gynae_system;
            $procedures = $day_procedure[$doctor] ?? 0;
            $total_procedure = $total_procedure + $procedures;
            $total_procedures = $month_procedure[$doctor] ?? 0;
            $total_total_procedure = $total_total_procedure + $total_procedures;

            $doctor_name = get_uname_by_id($doctor);
            echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td style = "text-align: left;">'.$doctor_name.'</td>
                <td>'.$svds.'</td>
                <td>'.$total_svds.'</td>
                <td>'.$dncs.'</td>
                <td>'.$total_dncs.'</td>
                <td>'.$procedures.'</td>
                <td>'.$total_procedures.'</td>
                <td>'.$gynaes.'</td>
                <td>'.$total_gynae.'</td>
                <td>'.$gynae_systems.'</td>
                <td>'.$total_gynae_system.'</td>
            </tr>';
        }
        echo '<tr style = "text-align: right;">
                <th></th>
                <th></th>
                <th>'.$total_svd.'</th>
                <th>'.$total_total_svd.'</th>
                <th>'.$total_dnc.'</th>
                <th>'.$total_total_dnc.'</th>
                <th>'.$total_procedure.'</th>
                <th>'.$total_total_procedure.'</th>
                <th>'.$total_gynaes.'</th>
                <th>'.$total_total_gynae.'</th>
                <th>'.$total_gynae_systems.'</th>
                <th>'.$total_total_gynae_system.'</th>
            </tr>';
    }
}
?>
</table>

</body>
</html>
<?php mysqli_close($con); ?>
