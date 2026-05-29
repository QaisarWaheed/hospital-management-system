<?php include 'includes/connect.php';
if ($fr_id != 1 && $fr_id != 350) {
    header('Location: logout.php');
    exit;
}
include 'includes/head.php';
?>
<style>
@page 
{
  size: A4;
  margin: 10px 0px 10px 0px;
}
@media print 
{
html, body 
{
    width: 210mm;
    height: 297mm;
    font-size: 9px;
}
.noprint
{
    display: none;
}
}    
</style>	

	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12 noprint" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name; ?> </h1></label>
        <?php include 'navigation_top.php'; ?>
	</div>
	<div class="col-md-12">
	    <table class = "table table-bordered">
	        <caption class = "h2" style = "caption-side: top;text-align: center;">Branch's Collection Detail <?php if( isset($_POST['date']) && $_POST['date'] != ''){echo $_POST['date'];}else{echo date('Y-m-d');} ?> </caption>
	        <thead>
	            <tr class = "noprint">
	                <th colspan = "3"></th>
	                <th colspan = "2" style = "text-align: right">
	                    <form method = "POST">
	                        <input type = "date" name = "date" value = "<?php if( isset($_POST['date']) && $_POST['date'] != ''){echo $_POST['date'];}else{echo date('Y-m-d');} ?>" required onchange = "this.form.submit()" />
	                    </form>
	                </th>
	            </tr>
	            <tr>
	                <th>S #</th>
	                <th>Branch Name</th>
	                <th>No Of Patients</th>
	                <th>Operate Medicine</th>
	                <th>Return Token</th>
	                <th>Total Amount</th>
	                <th>Received Amount</th>
	            </tr>
	        </thead>
	        <tbody>
        <?php
        $s = 0;
        $total_patient = 0;
        $total_operate = 0;
        $total_return_token = 0;
        $total_cash = 0;
        $total_cash_received = 0;
        if(isset($_POST['date']) && $_POST['date'] != '')
        {
            $today = $_POST['date'];
        }
        else
        {
            $today = date('Y-m-d');
        }
        $select_query = "SELECT DISTINCT `branch_id`, SUM(`cash`) AS cash, SUM(`cash_received`) AS cash_received FROM `tokans` WHERE status = 1 AND `branch_id` != 0 AND created LIKE '$today%' GROUP By `branch_id`";
        $run_query = mysqli_query($con, $select_query);
        if(mysqli_num_rows($run_query) > 0)
        {
            while($row_query = mysqli_fetch_array($run_query) )
            {
                $br_id = $row_query['branch_id'];
                $cash = $row_query['cash'];
                $total_cash = $total_cash + $cash;
                $cash_received = $row_query['cash_received'];
                $total_cash_received = $total_cash_received + $cash_received;
                $select_bra = "SELECT * FROM `branchs` WHERE id = '$br_id' ";
                $run_bra = mysqli_query($con, $select_bra);
                if(mysqli_num_rows($run_bra) > 0)
                {
                    while($row_bra = mysqli_fetch_array($run_bra))
                    {
                        $bra_id = $row_bra['id'];
                        $bra_tag_name = $row_bra['tag_name'];
                        $bra_address = $row_bra['address'];
                    }
                }
            $select_patient = "SELECT COUNT(*) FROM `tokans` WHERE tokan_type_id >= 1 AND tokan_type_id <= 10 AND status = '1' AND `branch_id` = '$br_id' AND created LIKE '$today%'";
            $run_patient = mysqli_query($con, $select_patient);
            if(mysqli_num_rows($run_patient) > 0)
            {
                while($row_patient = mysqli_fetch_array($run_patient) )
                {
                    $token_patient = $row_patient['0'];
                    $total_patient = $total_patient + $token_patient;
                }
            }
            $select_operate = "SELECT DISTINCT `branch_id`, SUM(`cash`) AS cash FROM `tokans` WHERE status = '2' AND `branch_id` = '$br_id' AND created LIKE '$today%'";
            $run_operate = mysqli_query($con, $select_operate);
            if(mysqli_num_rows($run_operate) > 0)
            {
                while($row_operate = mysqli_fetch_array($run_operate) )
                {
                    $token_operate = $row_operate['cash'];
                    $total_operate = $total_operate + $token_operate;
                }
            }
            $select_return = "SELECT DISTINCT `branch_id`, SUM(`cash_received`) AS cash_received FROM `tokans` WHERE status = '3' AND `branch_id` = '$br_id' AND created LIKE '$today%'";
            $run_return = mysqli_query($con, $select_return);
            if(mysqli_num_rows($run_return) > 0)
            {
                while($row_return = mysqli_fetch_array($run_return) )
                {
                    $return_token = $row_return['cash_received'];
                    $total_return_token = $total_return_token + $return_token;
                }
            }

                        $s = $s + 1;
                echo '
                <tr>
                    <td>'.$s.'</td>
                    <td>'.$bra_address.'</td>
                    <td style = "text-align: right">'.number_format((float)($token_patient ?? 0)).'</td>
                    <td style = "text-align: right">'.number_format((float)($token_operate ?? 0)).'</td>
                    <td style = "text-align: right">'.number_format((float)($return_token ?? 0)).'</td>
                    <td style = "text-align: right">'.number_format((float)($cash ?? 0)).'</td>
                    <td style = "text-align: right">'.number_format((float)($cash_received ?? 0)).'</td>
                </tr>
                ';
            }
            echo '
                <tr>
                    <th style = "text-align: right;" colspan = "2">GRAND TOTAL</th>
                    <th style = "text-align: right;">'.number_format((float)($total_patient ?? 0)).'</th>
                    <th style = "text-align: right;">'.number_format((float)($total_operate ?? 0)).'</th>
                    <th style = "text-align: right;">'.number_format((float)($total_return_token ?? 0)).'</th>
                    <th style = "text-align: right;">'.number_format((float)($total_cash ?? 0)).'</th>
                    <th style = "text-align: right;">'.number_format((float)($total_cash_received ?? 0)).'</th>
                </tr>
            ';
        }
        ?>
	            
	        </tbody>
	    </table>
	</div>
</div>
</body>
</html>