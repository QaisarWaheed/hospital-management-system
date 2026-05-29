<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
if($_SESSION['fr_id'] != 1 && $_SESSION['fr_id'] != 350)
{
    header('location: logout.php');
}
elseif(!isset($_SESSION['fr_id']))
{
    header('location: logout.php');
}
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
	        <caption class = "h2" style = "caption-side: top;text-align: center;">Branch's Summery <?php if( isset($_POST['date']) && $_POST['date'] != ''){echo $_POST['date'];}else{echo date('d-m-Y');} ?> </caption>
	        <thead>
	            <tr class = "noprint">
	                <th colspan = "3"></th>
	                <th colspan = "2" style = "text-align: right">
	                    <form method = "POST">
	                        <input type = "date" name = "date" value = "<?php if( isset($_POST['date']) && $_POST['date'] != ''){echo $_POST['date'];}else{echo date('Y-m-d');} ?>" required onchange = "this.form.submit()" />
	                    </form>
	                </th>
	            </tr>
	            <tr style = "text-align: center;">
	                <th>S #</th>
	                <th>Branch Name</th>
	                <th>OPD</th>
	                <th>Lab</th>
	                <th>USG</th>
	                <th>Admission</th>
	                <th>SVD / DNC</th>
	                <th>Procedure</th>
	                <th>Total Amount</th>
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
            // LAB
            $select_lab = "SELECT SUM(`cash_received`) AS cash_received FROM tokans WHERE status = 1 AND `branch_id` = '$br_id' AND created LIKE '$today%' AND id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT `id` FROM item_register_to_branches WHERE `item_id` IN (SELECT id FROM items WHERE category_id IN (2))))";
            $run_lab = mysqli_query($con, $select_lab);
            if(mysqli_num_rows($run_lab) > 0)
            {
                while($row_lab = mysqli_fetch_array($run_lab) )
                {
                    $token_lab = $row_lab['cash_received'];
                    $total_lab = $total_lab + $token_lab;
                }
            }
            // USG
            $select_usg = "SELECT id FROM tokans WHERE status = 1 AND `branch_id` = '$br_id' AND created LIKE '$today%' AND id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT `id` FROM item_register_to_branches WHERE `item_id` IN (SELECT id FROM items WHERE id IN (476, 477, 478, 1161, 1162, 1163, 1184, 1317, 1318, 1138, 1185, 1411))))";
            $run_usg = mysqli_query($con, $select_usg);
            $token_usg = mysqli_num_rows($run_usg);
            $total_usg = $total_usg + $token_usg;
            // Admission
            $select_admission = "SELECT id FROM tokans WHERE status = 1 AND `branch_id` = '$br_id' AND created LIKE '$today%' AND id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT `id` FROM item_register_to_branches WHERE `item_id` IN (SELECT id FROM items WHERE id IN (444, 448, 452, 456, 457, 460, 461, 945, 1124, 1125, 1128, 1131, 1132, 1145, 1186, 1285, 1289, 1293, 1297, 1301))))";
            $run_admission = mysqli_query($con, $select_admission);
            $token_admission = mysqli_num_rows($run_admission);
            $total_admission = $total_admission + $token_admission;
            // svd
            $select_svd = "SELECT id FROM tokans WHERE status = 1 AND `branch_id` = '$br_id' AND created LIKE '$today%' AND id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT `id` FROM item_register_to_branches WHERE `item_id` IN (SELECT id FROM items WHERE id IN (472, 1118, 1313, 473, 1119, 1314))))";
            $run_svd = mysqli_query($con, $select_svd);
            $token_svd = mysqli_num_rows($run_svd);
            $total_svd = $total_svd + $token_svd;
            // procedure
            $select_procedure = "SELECT id FROM tokans WHERE status = 1 AND `branch_id` = '$br_id' AND created LIKE '$today%' AND id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT `id` FROM item_register_to_branches WHERE `item_id` IN (SELECT id FROM items WHERE category_id IN (3) AND id NOT IN(472, 1118, 1313, 473, 1119, 1314))))";
            $run_procedure = mysqli_query($con, $select_procedure);
            $token_procedure = mysqli_num_rows($run_procedure);
            $total_procedure = $total_procedure + $token_procedure;

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
                    <td style = "text-align: right">'.number_format((float)($token_lab ?? 0)).'</td>
                    <td style = "text-align: right">'.number_format((float)($token_usg ?? 0)).'</td>
                    <td style = "text-align: right">'.number_format((float)($token_admission ?? 0)).'</td>
                    <td style = "text-align: right">'.number_format((float)($token_svd ?? 0)).'</td>
                    <td style = "text-align: right">'.number_format((float)($token_procedure ?? 0)).'</td>
                    <td style = "text-align: right">'.number_format((float)($cash_received ?? 0)).'</td>
                </tr>
                ';
            }
            echo '
                <tr>
                    <th style = "text-align: right;" colspan = "2">GRAND TOTAL</th>
                    <th style = "text-align: right;">'.number_format((float)($total_patient ?? 0)).'</th>
                    <th style = "text-align: right;">'.number_format((float)($total_lab ?? 0)).'</th>
                    <th style = "text-align: right;">'.number_format((float)($total_usg ?? 0)).'</th>
                    <th style = "text-align: right;">'.number_format((float)($total_admission ?? 0)).'</th>
                    <th style = "text-align: right;">'.number_format((float)($total_svd ?? 0)).'</th>
                    <th style = "text-align: right;">'.number_format((float)($total_procedure ?? 0)).'</th>
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