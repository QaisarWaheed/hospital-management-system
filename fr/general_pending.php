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

	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12 noprint" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name; ?> </h1></label>
        <?php include 'navigation_top.php'; ?>
	</div>
	<div class="col-md-12 noprint">
	    <div class = "row">
        <?php
        $select_bra = "SELECT * FROM `branchs` WHERE status = '1' ";
        $run_bra = mysqli_query($con, $select_bra);
        if(mysqli_num_rows($run_bra) > 0)
        {
            while($row_bra = mysqli_fetch_array($run_bra))
            {
                $bra_id = $row_bra['id'];
                $bra_tag_name = $row_bra['tag_name'];
                $bra_address = $row_bra['address'];
                echo '<div class = "col"><a title = "'.$bra_address.'" href = "general_pending.php?br_id='.$bra_id.'" class = "btn btn-primary p-2">'.$bra_tag_name.'</a></div>';
            }
        }
        ?></div>
    </div>
	<div class="col-md-12">

	    <table class = "table table-bordered">
<?php
if(isset($_GET['br_id']) && $_GET['br_id'] != '')
{
    $br_id = $_GET['br_id'];
    if($_GET['from_date'] != '' && $_GET['to_date'] != '')
    {
        $to_date = '';
        $from_date = $_GET['from_date'];
        $to_date .= $_GET['to_date'];
        $to_date .= " 23:59:59";
        $select = "SELECT * FROM `branch_daily_pending_details` WHERE token_no IN (SELECT id FROM tokans WHERE branch_id = '$br_id') AND created >= '$from_date' AND created <= '$to_date' ";
    }
    else
    {
        $select = "SELECT * FROM `branch_daily_pending_details` WHERE token_no IN (SELECT id FROM tokans WHERE branch_id = '$br_id') ";
    }
}
else
{
    $br_id = $branch_id;
    $select = "SELECT * FROM `branch_daily_pending_details` WHERE token_no IN (SELECT id FROM tokans WHERE branch_id = '$br_id') ";
}
$s = 0 ;
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
?>
	        <caption class = "h2" style = "caption-side: top;text-align: center;">GENERAL PENDING (<?php echo get_branch_name_by($br_id); ?>)

	        </caption>
	        <thead>
	            <tr class = "noprint">
	                <form>
	                <th colspan = "10">
	                    <div class = "row">
	                        <div class = "col-md-2 " style  = "text-align: right;">
	                            <label for = "from_date">From Date:</label>
	                        </div>
	                        <div class = "col-md-3">
	                            <input type = "hidden" value = "<?php echo $br_id; ?>" name = "br_id" id = "br_id" required />
	                            <input type = "date" name = "from_date" id = "from_date" class = "form-control"required />
	                        </div>
	                        <div class = "col-md-2" style  = "text-align: right;">
	                            <label for = "to_date">To Date:</label>
	                        </div>
	                        <div class = "col-md-3">
	                            <input type = "date" name = "to_date" id = "to_date" class = "form-control" required />
	                        </div>
	                        <div class = "col-md-2" style  = "text-align: center;">
	                            <input type = "submit" value = "SEARCH" name = "submit" style  = "min-width: 100%;min-height: 100%;" id = "submit" class = "btn btn-sm btn-info" />
	                        </div>
	                    </div>
	                </th>
	                </form>
                </tr>
	            <tr>
	                <th>S #</th>
	                <th class ="noprint" title = "Penging ID">Id</th>
	                <th>Date</th>
	                <th>Name</th>
	                <th class ="noprint" title = "Referance Name">Ref. Name</th>
	                <th class ="noprint" title = "Referance Name">Recommended By</th>
	                <th>Token #</th>
	                <th>Total Amount</th>
	                <th>Received Amount</th>
	                <th>Pending Amount</th>
	            </tr>
	        </thead>
<?php
    while($row = mysqli_fetch_array($run))
    {
        $created = $row['created'];
        $ref_name = $row['ref_name'];
        $recommended_by = $row['recommended_by'];
        $token_no = $row['token_no'];
        $patient_name = get_patient_name_by_token_id($token_no);
        $pending_id = get_pending_id_by_token_id($token_no);
        $receive = get_receive_amount_by_pending_id($pending_id);
        $total_amount = get_token_amount_by_id($token_no);
        $receive_amount = get_receive_amount_by_token_id($token_no);
        $received = $receive + $receive_amount;
        $pending_amount = $total_amount - $received;
        if($pending_amount > 0)
        {
        $s = $s + 1;
        echo '
                <tr>
                    <td class ="h6">'.$s.'</td>
                    <td class ="noprint h6">'.$pending_id.'</td>
                    <td class ="h6">'.date_format(date_create($created), "d-m-Y").'</td>
                    <td class ="h6">'.$patient_name.'</td>
                    <td class ="noprint h6">'.$ref_name.'</td>
                    <td class ="noprint h6">'.$recommended_by.'</td>
                    <td class ="h6">'.$token_no.'</td>
                    <td class ="h6" style = "text-align: center;">'.number_format((float)($total_amount ?? 0)).'</td>
                    <td class ="h6" style = "text-align: center;">'.number_format((float)($received ?? 0)).'</td>
                    <td class ="h6" style = "text-align: center;">'.number_format((float)($pending_amount ?? 0)).'</td>
        ';
        }
    }
} ?>
	    </table>
	</div>
			
	</div>
</div>

</body>
</html>