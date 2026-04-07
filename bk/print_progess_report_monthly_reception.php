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
    <title>PRINT RECEPTION PROGRESS REPORT <?php echo get_branch_tag_by($br_id); echo date_format(date_create($date), " F Y"); ?></title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>RECEPTION PROGRESS <?php echo date_format(date_create($date), " F Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>ID</th>
            <th>NAME</th>
            <th>POOR</th>
            <th>GENERAL</th>
            <th>PRIVATE</th>
            <th>EMERGENCY</th>
            <th>TOTAL COLLECTION</th>
            <th>INCENTIVE</th>
        </tr>
    </thead>
<?php
$s = 0; 
$total_poor = 0;
$total_member = 0;
$total_general = 0;
$total_emergency = 0;
$total_incentive = 0;
$select = "SELECT DISTINCT user_id FROM `tokans` WHERE branch_id = '$br_id' AND created LIKE '$date%' AND tokan_type_id < 100 AND status = '1' ORDER BY `user_id` ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    echo '<tbody>';
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $doctor = $row['user_id'];
        $doctor_name = get_uname_by_id($doctor);
        $get_poor = mysqli_query($con, "SELECT COUNT(tokan_type_id), SUM(cash) FROM `tokans` WHERE status = '1' AND user_id = '$doctor' AND branch_id = '$br_id' AND created LIKE '$date%' AND tokan_type_id = '1' ");
        if(mysqli_num_rows($get_poor) == 1)
{
            while($row_poor = mysqli_fetch_array($get_poor))
            {
                $incentive = 0;
                $count_poor = $row_poor['0'];
                $poor = $row_poor['1'];
                if(is_null($poor))
                {
                    $poor = 0;
                }
                $total_poor = $total_poor + $poor;
            }
        }
        $get_member = mysqli_query($con, "SELECT COUNT(tokan_type_id), SUM(cash) FROM `tokans` WHERE status = '1' AND user_id = '$doctor' AND branch_id = '$br_id' AND created LIKE '$date%' AND tokan_type_id = '2' ");
        if(mysqli_num_rows($get_member) == 1)
        {
            while($row_member = mysqli_fetch_array($get_member))
            {
                $count_member = $row_member['0'];
                $member = $row_member['1'];
                if(is_null($member))
                {
                    $member = 0;
                }
                $total_member = $total_member + $member;
            }
        }
        $get_general = mysqli_query($con, "SELECT COUNT(tokan_type_id), SUM(cash) FROM `tokans` WHERE status = '1' AND user_id = '$doctor' AND branch_id = '$br_id' AND created LIKE '$date%' AND tokan_type_id = '3' ");
        if(mysqli_num_rows($get_general) == 1)
        {
            while($row_general = mysqli_fetch_array($get_general))
            {
                $count_general = $row_general['0'];
                $general = $row_general['1'];
                if(is_null($general))
                {
                    $general = 0;
                }
                $total_general = $total_general + $general;
            }
        }
        $get_emergency = mysqli_query($con, "SELECT COUNT(tokan_type_id), SUM(cash) FROM `tokans` WHERE status = '1' AND user_id = '$doctor' AND branch_id = '$br_id' AND created LIKE '$date%' AND tokan_type_id = '4' ");
        if(mysqli_num_rows($get_emergency) == 1)
        {
            while($row_emergency = mysqli_fetch_array($get_emergency))
            {
                $count_emergency = $row_emergency['0'];
                $emergency = $row_emergency['1'];
                if(is_null($emergency))
                {
                    $emergency = 0;
                }
                $total_emergency = $total_emergency + $emergency;
            }
        }
        $incentive = ($count_poor*1)+($count_member*5)+($count_general*3);
        $total_incentive = $total_incentive + $incentive;
        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td>'.$doctor.'</td>
                <td style = "text-align: left;">'.$doctor_name.'</td>';
                echo '<td>'.$count_poor.' -> '.$poor.'</td>';
                echo '<td>'.$count_general.' -> '.$general.'</td>';
                echo '<td>'.$count_member.' -> '.$member.'</td>';
                echo '<td>'.$count_emergency.' -> '.$emergency.'</td>';
                echo '<td>'.($poor+$member+$general+$emergency).'</td>';
                echo '<td>'.($incentive).'</td>';
                echo '
            </tr>';
    }
    echo '</tbody>';
    echo '<tfoot>
            <tr style = "text-align: right;">
                <th colspan = "3"></th>';
                echo '<th>'.$total_poor.'</th>';
                echo '<th>'.$total_general.'</th>';
                echo '<th>'.$total_member.'</th>';
                echo '<th>'.$total_emergency.'</th>';
                echo '<th>'.($total_poor+$total_member+$total_general+$total_emergency).'</th>';
                echo '<th>'.($total_incentive).'</th>';
                echo '
            </tr>
        </tfoor>';
}
?>
</table>
<small>
    <span style = "color: red;">INCENTIVE:</span>1 rupee per Poor token, 3 Rupees per General Token & 5 Rupees per Private Token
</small>
</body>
</html>
<?php mysqli_close($con); ?>