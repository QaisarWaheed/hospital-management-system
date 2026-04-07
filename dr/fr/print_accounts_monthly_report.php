<?php 
include 'includes/connect.php'; 
if(isset($_GET['date']))
{
    $date = $_GET['date'];
    $br_id = $_GET['br_id'];
}
else
{
    exit(0);
}
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
            <th>PROCEDURE</th>
            <th>MEDICINE</th>
            <th>LAB</th>
            <th>ADDMISSIOM</th>
            <th>COLLECTION</th>
        </tr>
    </thead>
<?php
$s = 0; 
$total_procedure = 0;
$total_collection = 0;
$total_poor = 0;
$total_lab = 0;
$total_general = 0;
$total_private = 0;
$total_urgent = 0;
$total_consultent = 0;
$month = substr($date, 5);
$year = substr($date, 0,4);
$days = cal_days_in_month(CAL_GREGORIAN,$month,$year);

for ($x = 1; $x <= $days; $x++) 
{
    $total = 0;
    $count_procedure = 0;
    $count_medicine = 0;
    $count_lab = 0;
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
$urgent = "SELECT * FROM tokans WHERE branch_id = '$br_id' AND created LIKE '$year-$month-$x%' AND tokan_type_id = '4' AND status = 1 ";
$count_urgent = mysqli_num_rows(mysqli_query($con, $urgent));
$total_urgent = $total_urgent + $count_urgent;

//CONSULTENT
$consultent = "SELECT * FROM `tokans` WHERE `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` = 489))";
$count_consultent = mysqli_num_rows(mysqli_query($con, $consultent));
$total_consultent = $total_consultent + $count_consultent;

//USG
// $usg = "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (476, 477, 478))";
// $count_usg = mysqli_num_rows(mysqli_query($con, $usg));
// $total_usg = $total_usg + $count_usg;

//ADDMISSION
// $addmission = "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (444, 448, 452, 456, 460, 945))";
// $count_addmission = mysqli_num_rows(mysqli_query($con, $addmission));
// $total_addmission = $total_addmission + $count_addmission;


//PROCEDURE
// $procedure = "SELECT * FROM `tokans` WHERE `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 3)))";
// $count_procedure = mysqli_num_rows(mysqli_query($con, $procedure));
// $total_procedure = $total_procedure + $count_procedure;

//PROCEDURE
$procedure = "SELECT sum(`cash_received`) AS cash_received FROM `tokans` WHERE `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 3 )))";
$run_procedure = mysqli_query($con, $procedure);
if(mysqli_num_rows($run_procedure) == 1)
{
    while($row_procedure = mysqli_fetch_array($run_procedure))
    {
        $cash_received_procedure = $row_procedure['cash_received'];
    }
}
$total_procedure = $total_procedure + $cash_received_procedure;

//MEDICINE
$medicine = "SELECT sum(`cash_received`) AS cash_received FROM `tokans` WHERE `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id NOT IN (2,3,8,17,20,28) )))";
$run_medicine = mysqli_query($con, $medicine);
if(mysqli_num_rows($run_medicine) == 1)
{
    while($row_medicine = mysqli_fetch_array($run_medicine))
    {
        $cash_received_medicine = $row_medicine['cash_received'];
    }
}
$total_medicine = $total_medicine + $cash_received_medicine;

//LAB
$lab = "SELECT sum(`cash_received`) AS cash_received FROM `tokans` WHERE `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT id FROM tokans WHERE created like '$year-$month-$x%' AND status = 1) AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id = 2)))";
$run_lab = mysqli_query($con, $lab);
if(mysqli_num_rows($run_lab) == 1)
{
    while($row_lab = mysqli_fetch_array($run_lab))
    {
        $cash_received = $row_lab['cash_received'];
    }
}
$total_lab = $total_lab + $cash_received;

$urgent = "SELECT * FROM tokans WHERE branch_id = '$br_id' AND created LIKE '$year-$month-$x%' AND tokan_type_id = '4' AND status = 1 ";
$count_urgent = mysqli_num_rows(mysqli_query($con, $urgent));
$total_urgent = $total_urgent + $count_urgent;

//TOTAL
$total = $count_poor + $count_general + $count_private + $count_urgent;
$total_all = $total_all + $total;
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td>'.$select_date.'</td>
                <td>'.$count_poor.'</td>
                <td>'.$count_general.'</td>
                <td>'.$count_private.'</td>
                <td>'.$count_urgent.'</td>
                <td>'.$total.'</td>
                <td>'.$count_consultent.'</td>
                <td>'.$cash_received_procedure.'</td>
                <td>'.$cash_received_medicine.'</td>
                <td>'.$cash_received.'</td>
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
                <th>'.$total_procedure.'</th>
                <th>'.$total_medicine.'</th>
                <th>'.$total_lab.'</th>
                <th>'.number_format($total_collection).'</th>
            </tr>
        </tfoor>';
?>
</table>

</body>
</html>