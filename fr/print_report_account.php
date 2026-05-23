<?php
include 'includes/connect.php';
require_once __DIR__ . '/../includes/report_helpers.php';

if (!isset($_GET['date'], $_GET['br_id']) || $_GET['date'] === '') {
    http_response_code(400);
    exit('Date and branch are required.');
}

$date = $_GET['date'];
$br_id = (int) $_GET['br_id'];
$ym = ycdo_parse_year_month($date);
$year = $ym['year'];
$month = $ym['month'];
$days = $ym['days'];
?>
<html>
<head>
    <title>PRINT MONTHLY PROGRESS REPORT</title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h4>Progress For The Month Of <?php echo date_format(date_create($date), " F Y"); ?></h4>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>DATE</th>
            <th>POOR</th>
            <th>GENERAL</th>
            <th>PRIVATE</th>
            <th>URGENT</th>
            <th>TOTAL</th>
            <th>CONSULTANT</th>
            <th>MINOR</th>
            <th>PROCEDURE</th>
            <th>USG</th>
            <th>GYNAE</th>
            <th>ADDMISSIOM</th>
            <th>COLLECTION</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$total_minor_procedure = 0;
$total_procedure = 0;
$total_collection = 0;
$total_poor = 0;
$total_general = 0;
$total_private = 0;
$total_urgent = 0;
$total_consultent = 0;
$total_usg = 0;
$total_gynae = 0;
$total_addmission = 0;

for ($x = 1; $x <= $days; $x++) 
{
    $total = 0;
    $count_minor_procedure = 0;
    $count_procedure = 0;
    $count_poor = 0;      
    $count_general = 0;      
    $count_urgent = 0;      
    $count_private = 0;      
    $s++;
    if($x < 10)
    {
        $x = "0".$x;
    }
$select_date = $x.'-'.$month.'-'.$year;    
//COLLECTION
$collection = "SELECT SUM(`cash_received`) FROM tokans WHERE branch_id = '$br_id' AND created LIKE '$year-$month-$x%' AND status = 1 ";
$run_collection = mysqli_query($con, $collection);
if(mysqli_num_rows($run_collection) == 1)
{
    while($row_collection = mysqli_fetch_array($run_collection))
    {
        $collection_amount = $row_collection['0'];
        $total_collection = $total_collection + $collection_amount;
    }
}
else
{
        $collection_amount = 0;
        $total_collection = $total_collection + $collection_amount;
}

//POOR
$poor = "SELECT * FROM tokans WHERE branch_id = '$br_id' AND created LIKE '$year-$month-$x%' AND tokan_type_id = '1' AND status = 1 ";
$count_poor = mysqli_num_rows(mysqli_query($con, $poor));
$total_poor = $total_poor + $count_poor;

//GENERAL
$general = "SELECT * FROM tokans WHERE branch_id = '$br_id' AND created LIKE '$year-$month-$x%' AND tokan_type_id = '2' AND status = 1 ";
$count_general = mysqli_num_rows(mysqli_query($con, $general));
$total_general = $total_general + $count_general;

//PRIVATE
$private = "SELECT * FROM tokans WHERE branch_id = '$br_id' AND created LIKE '$year-$month-$x%' AND tokan_type_id = '3' AND status = 1 ";
$count_private = mysqli_num_rows(mysqli_query($con, $private));
$total_private = $total_private + $count_private;

//URGENT
$urgent = "SELECT * FROM tokans WHERE branch_id = '$br_id' AND created LIKE '$year-$month-$x%' AND tokan_type_id >= '4' AND tokan_type_id <= '10' AND status = 1 ";
$count_urgent = mysqli_num_rows(mysqli_query($con, $urgent));
$total_urgent = $total_urgent + $count_urgent;

//CONSULTENT
$consultent = "SELECT * FROM `tokans` WHERE `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (select id FROM items WHERE category_id = '29') ))";
$count_consultent = mysqli_num_rows(mysqli_query($con, $consultent));
$total_consultent = $total_consultent + $count_consultent;

//USG
$usg = "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (476, 477, 478, 1411, 1435))";
$count_usg = mysqli_num_rows(mysqli_query($con, $usg));
$total_usg = $total_usg + $count_usg;

//GYNAE
$gynae = "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (483, 1159, 1321, 1414))";
$count_gynae = mysqli_num_rows(mysqli_query($con, $gynae));
$total_gynae = $total_gynae + $count_gynae;

//ADDMISSION
$addmission = "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (444, 448, 452, 456, 460, 945))";
$count_addmission = mysqli_num_rows(mysqli_query($con, $addmission));
$total_addmission = $total_addmission + $count_addmission;


//MINOR PROCEDURE
$minor_procedure = "SELECT * FROM `tokans` WHERE `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 3 AND id IN (434, 435, 436, 437, 853, 864, 867, 868, 869, 870, 871, 872, 873, 874, 875, 876, 877, 878, 879, 880, 881, 882, 883, 884, 885, 886, 887, 888, 889, 890, 891, 892, 893, 899, 907, 908, 909, 910, 911, 912, 913, 914) )))";
$count_minor_procedure = mysqli_num_rows(mysqli_query($con, $minor_procedure));
$total_minor_procedure = $total_minor_procedure + $count_minor_procedure;

//MAJOR PROCEDURE
$procedure = "SELECT * FROM `tokans` WHERE `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 3 AND id NOT IN(434, 435, 436, 437, 853, 864, 867, 868, 869, 870, 871, 872, 873, 874, 875, 876, 877, 878, 879, 880, 881, 882, 883, 884, 885, 886, 887, 888, 889, 890, 891, 892, 893, 899, 907, 908, 909, 910, 911, 912, 913, 914) )))";
$count_procedure = mysqli_num_rows(mysqli_query($con, $procedure));
$total_procedure = $total_procedure + $count_procedure;


// $urgent = "SELECT * FROM tokans WHERE branch_id = '$br_id' AND created LIKE '$year-$month-$x%' AND tokan_type_id = '4' AND status = 1 ";
// $count_urgent = mysqli_num_rows(mysqli_query($con, $urgent));
// $total_urgent = $total_urgent + $count_urgent;

//TOTAL
$total = $count_poor + $count_general + $count_private + $count_urgent;
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td>'.$select_date.'</td>
                <td>'.$count_poor.'</td>
                <td>'.$count_general.'</td>
                <td>'.$count_private.'</td>
                <td>'.$count_urgent.'</td>
                <td>'.$total.'</td>
                <td>'.$count_consultent.'</td>
                <td>'.$count_minor_procedure.'</td>
                <td>'.$count_procedure.'</td>
                <td>'.$count_usg.'</td>
                <td>'.$count_gynae.'</td>
                <td>'.$count_addmission.'</td>
                <td>'.number_format($collection_amount).'</td>
            </tr>';
}
    echo '</tbody>';
    echo '<tfoot>
            <tr style = "text-align: right;">
                <th colspan = "2">TOTAL</th>
                <th>'.$total_poor.'</th>
                <th>'.$total_general.'</th>
                <th>'.$total_private.'</th>
                <th>'.$total_urgent.'</th>
                <th>'.$total_poor+$total_general+$total_private+$total_urgent.'</th>
                <th>'.$total_consultent.'</th>
                <th>'.$total_minor_procedure.'</th>
                <th>'.$total_procedure.'</th>
                <th>'.$total_usg.'</th>
                <th>'.$total_gynae.'</th>
                <th>'.$total_addmission.'</th>
                <th>'.number_format($total_collection).'</th>
            </tr>
        </tfoot>';
?>
</table>

</body>
</html>