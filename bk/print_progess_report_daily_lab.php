<?php 
include 'includes/connect.php'; 
if(isset($_GET['date']))
{
    $date = $_GET['date'];
    $br_id = $_GET['br_id'];
}
elseif(isset($_POST['date']))
{
    $date = $_POST['date'];
    $br_id = $_POST['br_id'];
}
else
{
    exit(0);
}
?>
<html>
<head>
    <title><?php echo get_branch_name_by($br_id); ?> <?php echo date_format(date_create($date), " d F Y"); ?> LAB PROGRESS REPORT</title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS DATE <?php echo date_format(date_create($date), " d F Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>PATIENT NAME</th>
            <th>TOTAL PATIENTs</th>
            <th>TOTAL LAB AMOUNT</th>
        </tr>
    </thead>
<?php
$s = 0; 
$labs = 0;
$total_lab = 0;
$total_opds = 0;
$total_cons_opds = 0;
$select = "SELECT DISTINCT `doctor_id` FROM tokans WHERE created like '$date%' AND status = 1 AND branch_id = '$br_id' AND `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT id FROM `item_register_to_branches` WHERE item_id IN (SELECT id FROM items WHERE category_id = 2))) ORDER BY `doctor_id` ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    echo '<tbody>';
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $doctor = $row['doctor_id'];
        
        $select_lab = "SELECT SUM(`cash_received`), COUNT(`cash_received`) FROM tokans WHERE doctor_id = '$doctor' AND created like '$date%' AND status = 1 AND branch_id = '$br_id' AND `id` IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT id FROM `item_register_to_branches` WHERE branch_id = '$br_id' AND item_id IN (SELECT id FROM items WHERE category_id = 2)))";
        $run_lab = mysqli_query($con, $select_lab);
        if(mysqli_num_rows($run_lab) > 0)
        {
            while($row_lab = mysqli_fetch_array($run_lab))
            {
                $labs = $row_lab[0];
                $count_lab = $row_lab[1];
                $total_count_lab = $total_count_lab + $count_lab;
                if($labs == 0)
                {
                    $labs = '0';
                }
                else
                {
                    $total_lab = $total_lab + $labs;
                }
            }
        }
        else
        {
            $labs = 0;
        }

        $doctor_name = get_uname_by_id($doctor);
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td style = "text-align: left;">'.$doctor_name.' ('.$doctor.')</td>
                <td>'.$count_lab.'</td>';
                echo '<td>'.$labs.'</td>';
                echo '
            </tr>';
            $labs = 0;
            $count_lab = 0;
    }
    echo '</tbody>';
    echo '<tfoot>
            <tr style = "text-align: right;">
                <th></th>
                <th></th>
                <th>'.$total_count_lab.'</th></th>';
                echo '<th>'.$total_lab.'</th>';
                echo '
            </tr>
        </tfoor>';
}
?>
</table>

</body>
</html>
<?php mysqli_close($con); ?>