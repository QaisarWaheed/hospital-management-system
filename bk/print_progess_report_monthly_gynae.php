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
    <title>PRINT GYNAE PROGRESS REPORT <?php echo get_branch_tag_by($br_id); echo date_format(date_create($date), " F Y"); ?></title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS <?php echo date_format(date_create($date), " F Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th colspan = "3"></th>
            <th colspan = "3">PREVIOUS ALL REPORT</th>
            <th colspan = "3">CURRENT MONTH DATA</th>
            <th colspan = "3">TOTAL RECORD</th>
        </tr>
        <tr>
            <th>SR #</th>
            <th>ID</th>
            <th>NAME</th>
            <th>TOKENS</th>
            <th>ONLINE</th>
            <th>PENDING</th>
            <th>TOKENS</th>
            <th>ONLINE</th>
            <th>PENDING</th>
            <th>TOKENS</th>
            <th>ONLINE</th>
            <th>PENDING</th>
        </tr>
    </thead>
<?php
$s = 0; 
$total_gynae_system = 0;
$current_total_gynae_system = 0;
$total_tokens = 0;
$last_day = date_format(date_create($date), "t");
$month = $date.'-'.$last_day;
$select = "SELECT DISTINCT item_by_doctor.doctor_id, users.u_name, COUNT(item_by_doctor.id) AS tokens FROM `item_by_doctor` INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN users ON item_by_doctor.doctor_id = users.id WHERE items.category_id = '41' AND item_by_doctor.branch_id = '$br_id' AND item_by_doctor.created >= '2025-03-01' AND item_by_doctor.created < '$month' GROUP BY item_by_doctor.doctor_id ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    echo '<tbody>';
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $doctor_id = $row['doctor_id'];
        $doctor_name = $row['u_name'];
        $tokens = $row['tokens'];

        $current_tokens = mysqli_num_rows(mysqli_query($con, "SELECT item_by_doctor.id FROM `item_by_doctor` INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN users ON item_by_doctor.doctor_id = users.id WHERE items.category_id = '41' AND item_by_doctor.branch_id = '$br_id' AND item_by_doctor.created LIKE '$date%' AND item_by_doctor.doctor_id = '$doctor_id' "));
        $current_total_tokens = $current_total_tokens + $current_tokens;
        

        $current_gynae_system = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `gynae_register` WHERE doctor_id = '$doctor_id' AND created LIKE '$date%' AND branch_id = '$br_id'"));
        $current_total_gynae_system = $current_total_gynae_system + $current_gynae_system;
        

        $gynae_system = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `gynae_register` WHERE doctor_id = '$doctor_id' AND created > '2025-03-01' AND created < '$month' AND branch_id = '$br_id'"));
        
        
        // $gynae_system_pre = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `gynae_register` WHERE doctor_id = '$doctor' AND created < '$date' AND branch_id = '$br_id'"));
        // $total_gynae_system_pre = $total_gynae_system_pre + $gynae_system_pre;

        // $run_points = mysqli_query($con, "SELECT gynae_register_discharge.token_no AS token_no, `procedure_token_no`, gynae_register.created AS gynae_register_date FROM `gynae_register_discharge` INNER JOIN gynae_register ON gynae_register_discharge.registeration_id = gynae_register.id WHERE `dod` LIKE '$date%' AND `consultant` = '$doctor' AND `gynae_discharge_status` = '1' ");
        // $points = mysqli_num_rows($run_points);
        // if($points > 0)
        // {
        //     while($row_points = mysqli_fetch_array($run_points))
        //     {
        //         $token_no = $row_points['token_no'];
        //     }
        // }
        
        // $total_points = $total_points + $points;

        // $dncs = mysqli_num_rows(mysqli_query($con, "SELECT `tokan_no` FROM `item_by_doctor` WHERE tokan_no IN (SELECT `procedure_token_no` FROM `gynae_register_discharge` WHERE `dod` LIKE '$date%' AND `consultant` = '$doctor' AND `gynae_discharge_status` = '1') AND branch_id = '$br_id' AND `status` = 2 AND `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (473, 1119, 1314, 1578) )"));
        // $total_dnc = $total_dnc + $dncs;

        
        $total_tokens = $total_tokens + ($tokens-$current_tokens);
        $total_gynae_system = $total_gynae_system + ($gynae_system-$current_gynae_system);
        echo ' <tr style = "text-align: right;">';
            echo '<td>'.$s.'</td>';
            echo '<td>'.$doctor_id.'</td>';
            echo '<td>'.$doctor_name.'</td>';
            echo '<td>'.$tokens-$current_tokens.'</td>';
            echo '<td>'.$gynae_system-$current_gynae_system.'</td>';
            echo '<td>'.intval(($tokens-$current_tokens)-($gynae_system-$current_gynae_system)).'</td>';
            
            echo '<td>'.$current_tokens.'</td>';
            echo '<td>'.$current_gynae_system.'</td>';
            echo '<td>'.intval($current_tokens-$current_gynae_system).'</td>';
            
            echo '<td>'.$tokens.'</td>';
            echo '<td>'.$gynae_system.'</td>';
            echo '<td>'.intval(($tokens)-($gynae_system)).'</td>';
            
        echo '</tr>';
    }
    echo '</tbody>';
    echo '<tfoot>
            <tr style = "text-align: right;">
                <th colspan = "3"></th>
                <th>'.$total_tokens.'</th>
                <th>'.$total_gynae_system.'</th>
                <th>'.intval($total_tokens-$total_gynae_system).'</th>
                
                <th>'.$current_total_tokens.'</th>
                <th>'.$current_total_gynae_system.'</th>
                <th>'.intval($current_total_tokens-$current_total_gynae_system).'</th>
                
                <th>'.$current_total_tokens+$total_tokens.'</th>
                <th>'.$current_total_gynae_system+$total_gynae_system.'</th>
                <th>'.intval(($current_total_tokens+$total_tokens)-($current_total_gynae_system+$total_gynae_system)).'</th>';
            echo '</tr>
        </tfoor>';
}
?>
</table>

</body>
</html>
<?php mysqli_close($con); ?>