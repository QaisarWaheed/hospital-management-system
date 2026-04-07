    <?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; ?>
	<title>Print Account Summary - <?php echo $company_trademark; ?></title>
<style>
*{
    font-size: 16px;
}
</style>
</head>

<body onload="window.print()">
<?php
if (isset($_GET['month']) && $_GET['month'] != '') 
{
	$br_id = $_GET['br_id'];
	$month = $_GET['month'];
	$year = intval(date_format(date_create($month), 'Y'));
	$total_days_of_month = cal_days_in_month(CAL_GREGORIAN,date_format(date_create($month), 'm'),date_format(date_create($month), 'Y'));
}
?>

<table class="table" style="font-size: 8px">

	<thead>
	<tr style="caption-side: top;text-align: center;">
	    <td colspan="9">
	    <?php echo $branch_name; ?>
    	<h6><?php echo $branch_address; ?></h6>
    	<h5>Account Summary - <span style="text-align: left;font-size: 25px;"><?php echo date_format(date_create($month), 'F Y'); ?></span></h5>

         <div style="float:left">Print Time: <?php echo date('h:i:s A'); ?></div>
         <div style="float:right">Print Date:<?php echo date('d-m-Y'); ?></div>
         <br>
         <div style="float:left">Print By: <?php echo $user_name; ?></div>
         </td>

	</tr>
		<tr>
			<th>Date</th>
			<th>Total Cash</th>
			<th>Pending</th>
			<th>Pending Received</th>
			<th colspan="5">Received Amount</th>
		</tr>
	</thead>
	<tbody>
    <?php
    $total_cash = 0;
    $total_cash_received = 0;
    $total_pending = 0;
    $total_pending_receive = 0;
    for ($x = 1; $x <= $total_days_of_month; $x++) 
    {
        if($x < 10)
        {
            $x = '0'.$x;
        }
        $select_date = $month.'-'.$x;
        
        $select_pending = "SELECT SUM(`cash`), SUM(`cash_received`) FROM `tokans` WHERE branch_id = '$br_id' AND `created` LIKE '$select_date%' AND status = 1 AND `cash` > `cash_received`";
        $run_pending = mysqli_query($con, $select_pending);
        if(mysqli_num_rows($run_pending) == 1)
        {
            while($row_pending = mysqli_fetch_array($run_pending))
            {
                $pending_cash = $row_pending['0'];
                $pending_cash_received = $row_pending['1'];
                $pending = $pending_cash - $pending_cash_received;
                $total_pending = $total_pending + $pending;
            }
        }
        
        $select_pending_receive = "SELECT SUM(`cash`), SUM(`cash_received`) FROM `tokans` WHERE branch_id = '$br_id' AND `created` LIKE '$select_date%' AND status = 1 AND `cash` = 0 ";
        $run_pending_receive = mysqli_query($con, $select_pending_receive);
        if(mysqli_num_rows($run_pending_receive) == 1)
        {
            while($row_pending_receive = mysqli_fetch_array($run_pending_receive))
            {
                $pending_cash = $row_pending_receive['0'];
                $pending_cash_received = $row_pending_receive['1'];
                $pending_receive = abs($pending_cash - $pending_cash_received);
                $total_pending_receive = $total_pending_receive + $pending_receive;
            }
        }

        $select_total = "SELECT SUM(`cash`), SUM(`cash_received`) FROM `tokans` WHERE branch_id = '$br_id' AND `created` LIKE '$select_date%' AND status = 1";
        $run_total = mysqli_query($con, $select_total);
        if(mysqli_num_rows($run_total) == 1)
        {
            while($row_total = mysqli_fetch_array($run_total))
            {
                $cash = $row_total['0'];
                $total_cash = $total_cash + $cash;
                $cash_received = $row_total['1'];
                $total_cash_received = $total_cash_received + $cash_received;
                if($cash != 0 && $cash_received != 0)
                {
                    echo '
                        <tr>
                            <td>'.date_format(date_create($select_date), 'd-m-Y').'</td>
                            <td>'.number_format($cash).'</td>
                            <td>'.number_format($pending).'</td>
                            <td>'.number_format($pending_receive).'</td>
                            <td>'.number_format($cash_received).'</td>
                        </tr>
                ';
                }
            }
        }
    }
                echo '
                    <tr>
                        <th></th>
                        <th>'.number_format($total_cash).'</th>
                        <th>'.number_format($total_pending).'</th>
                        <th>'.number_format($total_pending_receive).'</th>
                        <th>'.number_format($total_cash_received).'</th>
                    </tr>
                ';    
    ?>
    </tbody>
</table>

</body>
</html>
 <script type="text/javascript">
    //   setTimeout(window.close, 50);
</script>

