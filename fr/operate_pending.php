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
                echo '<div class = "col"><a title = "'.$bra_address.'" href = "operate_pending.php?br_id='.$bra_id.'" class = "btn btn-primary p-2">'.$bra_tag_name.'</a></div>';
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
}
else
{
    $br_id = $branch_id;
}
$s = 0 ;
$select = "SELECT * FROM `branch_pending_details` WHERE status = 1 AND branch_id = '$br_id' AND token_no NOT IN (SELECT token_no FROM branch_daily_pending_details) ORDER BY id DESC ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
?>
	        <caption class = "h2" style = "caption-side: top;text-align: center;">OPERATE PENDING (<?php echo get_branch_name_by($br_id); ?>)</caption>
	        <thead>
	            <tr>
	                <th>S #</th>
	                <th class ="noprint" title = "Penging ID">Id</th>
	                <th>Date</th>
	                <th>Name</th>
	                <th class ="noprint">Procedure</th>
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
        $rf_name = $row['gardian_name'];
        $recommended_by = $row['recommended_by'];
        $token_no = $row['token_no'];
        $pending_id = get_pending_id_by_token_id($token_no);
        $receive = get_receive_amount_by_pending_id($pending_id);
        $total_amount = get_token_amount_by_id($token_no);
        $receive_amount = get_receive_amount_by_token_id($token_no);
        $received = $receive + $receive_amount;
        $pending_amount = $total_amount - $received;
        if($pending_amount > 0)
        {
        $s = $s + 1;
        $patient_name = get_patient_name_by_token_id($token_no);
        $procedure_name = get_procedure_name_by_register_item_id($token_no);
        echo '
                <tr>
                    <td class ="h6">'.$s.'</td>
                    <td class ="noprint h6">'.$pending_id.'</td>
                    <td class ="h6">'.date_format(date_create($created), "d-m-Y").'</td>
                    <td class ="h6">'.$patient_name.'</td>
                    <td class ="noprint h6">'.$procedure_name.'</td>
                    <td class ="noprint h6">'.$rf_name.'</td>
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