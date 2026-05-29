<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php';
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

	<title>Return Tokens - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12 noprint" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name; ?> </h1></label>
        <?php include 'navigation_top.php'; ?>
	</div>
	<div class="col-md-12">
	    <table class = "table table-bordered">
	        <caption class = "h2" style = "caption-side: top;text-align: center;">Operate Medicines Tokens Detail</caption>
	        <thead>
	            <tr class = "noprint">
	                <th colspan = "3"></th>
	                <th colspan = "3" style = "text-align: right">
	                    <form method = "POST">
	                        <input type = "date" name = "date" value = "<?php if( isset($_POST['date']) && $_POST['date'] != ''){echo $_POST['date'];}else{echo date('Y-m-d');} ?>" required onchange = "this.form.submit()" />
	                    </form>
	                </th>
	            </tr>
	            <tr>
	                <th>S #</th>
	                <th>Date</th>
	                <th>Patient Name</th>
	                <th>Token No</th>
	                <th>Token By</th>
	                <th>Branch Name</th>
	                <th>Token Amount</th>
	            </tr>
	        </thead>
	        <tbody>
        <?php
        $s = 0;
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
        $select_query = "SELECT * FROM `tokans` WHERE `status` = 2 AND `created` LIKE '$today%' ";
        $run_query = mysqli_query($con, $select_query);
        if(mysqli_num_rows($run_query) > 0)
        {
            while($row_query = mysqli_fetch_array($run_query) )
            {
                $token_id = $row_query['id'];
                $token_by = get_uname_by_id($row_query['user_id']);
                $patient_name = get_patient_name_by_token_id($token_id);
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
                        $s = $s + 1;
                echo '
                <tr>
                    <td>'.$s.'</td>
                    <td style = "text-align: center;">'.date_format(date_create($today), "d-F-Y").'</td>
                    <td style = "text-align: center;">'.$patient_name.'</td>
                    <td style = "text-align: center;">'.$token_id.'</td>
                    <td>'.$token_by.'</td>
                    <td>'.$bra_address.'</td>
                    <td style = "text-align: center;">'.number_format((float)($cash ?? 0)).'</td>
                </tr>
                ';
            }
                echo '
                <tr>
                    <td colspan = "5"></td>
                    <td style = "text-align: center;">'.number_format((float)($total_cash ?? 0)).'</td>
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