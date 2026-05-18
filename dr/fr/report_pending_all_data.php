<html>
<head>
    <style>
        @media print 
        {
          .noPrint{
            display:none;
          }
        }
        body{
            font-size: 8pt;
        }
    </style>
</head>
<body>
<?php
$s = 0;
require_once __DIR__ . '/../../includes/ycdo_mysqli_vars.php';
$con = mysqli_connect($ycdo_db_host, $ycdo_db_user, $ycdo_db_pass, $ycdo_db_name);

$query = "SELECT branch_pending_details.id, branch_pending_details.token_no,branchs.tag_name, patients.name,branch_pending_details.gardian_name, branch_pending_details.recommended_by, tokans.cash, tokans.cash_received, branch_pending_details.created FROM `branch_pending_details` INNER JOIN branchs ON branch_pending_details.branch_id = branchs.id INNER JOIN tokans ON branch_pending_details.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id WHERE branch_pending_details.status = 1 AND branch_pending_details.token_no NOT IN (SELECT token_no FROM branch_daily_pending_details) AND branch_pending_details.created >= '2023-01-01' AND branch_pending_details.created <= '2024-05-31' AND tokans.cash != tokans.cash_received ORDER BY id ASC LIMIT 1501, 500 ";
$run = mysqli_query($con, $query);
if(mysqli_num_rows($run) > 0)
{
?>
<table border = "sold">
    <thead>
        <tr>
            <th>Sr</th>
            <th>Created</th>
            <th>Token</th>
            <th>Branch</th>
            <th>Name</th>
            <th>Ref. By</th>
            <th>Recommeded</th>
            <th>Cash</th>
            <th>Received</th>
            <th>Pending</th>
        </tr>
    </thead>
<?php    
    while($row = mysqli_fetch_array($run))
    {
        $token_no = $row['1'];
        $cash = $row['6'];
        $cash_received = $row['7'];
        $query_2 = "SELECT SUM(cash_received) FROM `tokans` WHERE `tokan_type_id` = 201 AND previous_tokan_no = '$token_no' ";
        $run_2 = mysqli_query($con, $query_2);
        if(mysqli_num_rows($run_2) == 1)
        {
            while($row_2 = mysqli_fetch_array($run_2))
            {
                $received = $row_2['0'];
            }
        }
        if(is_null($received))
        {
            $received = 0;
        }
        $total_received = $received + $cash_received;
        if($total_received != $cash)
        {
        $s++;
        echo '
        <tr>
            <td>'.$s.'</td>
            <td>'.date_format(date_create($row['8']), "d-m-Y").'</td>
            <td>'.$row['1'].'</td>
            <td>'.$row['2'].'</td>
            <td>'.$row['3'].'</td>
            <td>'.$row['4'].'</td>
            <td>'.$row['5'].'</td>
            <td>'.$cash.'</td>
            <td>'.$total_received.'</td>
            <td>'.intval($cash-$total_received).'</td>
        </tr>
        ';
        }
    } ?>
</table>
<?php
}    
?>
</body>
</html>
