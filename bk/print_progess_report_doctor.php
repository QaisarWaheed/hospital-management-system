<?php
include 'includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';

set_time_limit(120);

$req = progress_report_resolve_request($con);
$date = $req['date'];
$br_id = $req['br_id'];
$like = $req['like'];

$opd_map = progress_opd_count_by_doctor($con, $br_id, $like);
$cons_map = progress_cons_opd_count_by_doctor($con, $br_id, $like);
$usg_map = progress_usg_count_by_doctor($con, $br_id, $like);
$collection_map = progress_doctor_progress_collection_by_doctor($con, $br_id, $like);

$doctor_ids = array_unique(array_merge(
    array_keys($opd_map),
    array_keys($cons_map),
    array_keys($usg_map),
    array_keys($collection_map)
));
sort($doctor_ids, SORT_NUMERIC);
?>
<html>
<head>
    <title>PRINT PROGRESS REPORT</title>
</head>
<body>

<table border="solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS DATE <?php echo ycdo_safe_date_format($date, ' d F Y', $date); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>CONS</th>
            <th>USG</th>
            <th>COLLECTION</th>
        </tr>
    </thead>
<?php
if (count($doctor_ids) > 0) {
    echo '<tbody>';
    $s = 0;
    foreach ($doctor_ids as $doctor) {
        $s++;
        $opds = $opd_map[$doctor] ?? 0;
        $cons_opds = $cons_map[$doctor] ?? 0;
        $usg_count = $usg_map[$doctor] ?? 0;
        $collections = $collection_map[$doctor] ?? 0;
        $doctor_name = get_uname_by_id($doctor);
        echo ' <tr style="text-align: right;">
                <td>' . $s . '</td>
                <td style="text-align: left;">' . htmlspecialchars($doctor_name, ENT_QUOTES, 'UTF-8') . '</td>
                <td>' . $opds . '</td>
                <td>' . $cons_opds . '</td>
                <td>' . $usg_count . '</td>
                <td>' . $collections . '</td>
            </tr>';
    }
    echo '</tbody>';
}
?>
</table>

</body>
</html>
