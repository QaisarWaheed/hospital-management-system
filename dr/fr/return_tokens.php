<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
if(!isset($_SESSION['fr_id']))
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
	        <caption class = "h2" style = "caption-side: top;text-align: center;">Return Tokens Detail</caption>
	        <thead>
	            <tr class = "noprint">
                    <form method = "POST">
	                <th colspan = "3">
	                        <select required name = "br_id" class = "form-control">
	                            <option value = "0">ALL</option>
                            <?php 
                            $branch = "SELECT * FROM branchs WHERE id != 0 AND status = 1 ORDER BY `address` ASC ";
                            $run_branch = mysqli_query($con, $branch);
                            if (mysqli_num_rows($run_branch) > 0) 
                            {
                                while ($row_branch = mysqli_fetch_array($run_branch)) {
                                    
                                    echo '<option '; if (isset($_POST['br_id']) && (string) $_POST['br_id'] === (string) $row_branch['id']) { echo ' SELECTED '; } echo 'value="'.$row_branch['id'].'">'.$row_branch['address'].'</option>';
                                }
                            }
                            else
                            {
                                echo '<option value="">No Data</option>';
                            }
                            ?>
	                        </select>
	                </th>
	                <th colspan = "5" style = "text-align: left;">
	                        FROM: <input type = "date" name = "from_date" value = "<?php if( isset($_POST['from_date']) && $_POST['from_date'] != ''){echo $_POST['from_date'];}else{echo date('Y-m-d');} ?>" required />
	                        TO: <input type = "date" name = "date" value = "<?php if( isset($_POST['date']) && $_POST['date'] != ''){echo $_POST['date'];}else{echo date('Y-m-d');} ?>" required />
	                        <input type = "submit" name = "submit" value = "SEARCH" required/>
	                </th>
	                <th>
	                </th>
	                </form>
	            </tr>
	            <tr>
	                <th>S #</th>
	                <th>Date</th>
	                <th>Token No</th>
	                <th>Token At</th>
	                <th>Token By</th>
	                <th>Return By</th>
	                <th>Return At</th>
	                <th>Branch Name</th>
	                <th>Token Amount</th>
	                <th>Received Amount</th>
	            </tr>
	        </thead>
	        <tbody>
        <?php
        $s = 0;
        $total_cash = 0;
        $total_cash_received = 0;
        if (isset($_POST['br_id']) && $_POST['br_id'] !== '') {
            $br_id = (int) $_POST['br_id'];
        } else {
            $br_id = 0;
        }
        if (isset($_POST['date']) && $_POST['date'] !== '') {
            $today = substr((string) $_POST['date'], 0, 10);
            $from_date = isset($_POST['from_date']) && $_POST['from_date'] !== ''
                ? substr((string) $_POST['from_date'], 0, 10)
                : $today;
        } else {
            $today = date('Y-m-d');
            $from_date = date('Y-m-d');
        }
        $from_start = mysqli_real_escape_string($con, $from_date . ' 00:00:00');
        $to_date = mysqli_real_escape_string($con, $today . ' 23:59:59');
        if ($br_id === 0) {
            $select_query = "SELECT * FROM `tokans` WHERE `status` = 3 AND `created` <= '$to_date' AND `created` >= '$from_start' ";
        } else {
            $select_query = "SELECT * FROM `tokans` WHERE `status` = 3 AND `created` <= '$to_date' AND `created` >= '$from_start' AND branch_id = '$br_id' ";
        }
        $run_query = mysqli_query($con, $select_query);
        if(mysqli_num_rows($run_query) > 0)
        {
            while($row_query = mysqli_fetch_array($run_query) )
            {
                $token_id = $row_query['id'];
                $token_at = $row_query['created'];
                $token_by = get_uname_by_id($row_query['user_id']);
                $token_return_by = get_uname_of_return_token($token_id);
                $token_return_at = get_time_of_return_token($token_id);
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
                    <td style = "text-align: center;">'.date_format(date_create($token_at), "d-F-Y").'</td>
                    <td style = "text-align: center;">'.$token_id.'</td>
                    <td style = "text-align: center;">'.date_format(date_create($token_at), "h:i:s A").'</td>
                    <td>'.$token_by.'</td>
                    <td>'.$token_return_by.'</td>
                    <td>'.date_format(date_create($token_return_at), "h:i:s A").'</td>
                    <td>'.$bra_address.'</td>
                    <td style = "text-align: center;">'.number_format((float)($cash ?? 0)).'</td>
                    <td style = "text-align: center;">'.number_format((float)($cash_received ?? 0)).'</td>
                </tr>
                ';
            }
                echo '
                <tr>
                    <td colspan = "8"></td>
                    <td style = "text-align: center;">'.number_format((float)($total_cash ?? 0)).'</td>
                    <td style = "text-align: center;">'.number_format((float)($total_cash_received ?? 0)).'</td>
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