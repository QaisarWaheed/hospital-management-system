<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php';
if(isset($_GET['br_id']) && $_GET['br_id'] != '')
{
    $br_id = $_GET['br_id'];
}
else
{
    $br_id = $branch_id;
}

if(isset($_GET['page_no']) && $_GET['page_no'] != '')
{
    $page_no = $_GET['page_no'];
    $start = ($page_no-1)*20;
}
else
{
    $page_no = 1;
    $start = 0;
}

if(isset($_GET['from_date']) && $_GET['from_date'] != '' && $_GET['to_date'] != '')
{
        $to_date = '';
        $from_date = $_GET['from_date'];
        $to_date .= $_GET['to_date'];
        $to_date .= " 23:59:59";
        // $select = "SELECT * FROM `branch_daily_pending_details` WHERE token_no IN (SELECT id FROM tokans WHERE status = '1' AND branch_id = '$br_id') AND created >= '$from_date' AND created <= '$to_date' ";
        $select = "SELECT * FROM `branch_pending_details` WHERE status = 1 AND branch_id = '$br_id' AND token_no NOT IN (SELECT token_no FROM branch_daily_pending_details) AND created >= '$from_date' AND created <= '$to_date' ORDER BY id ASC LIMIT $start,20 ";
}
else
{
    $from_date = $to_date = date('y-m-d');
    // $select = "SELECT * FROM `branch_daily_pending_details` WHERE token_no IN (SELECT id FROM tokans WHERE status = '1' AND branch_id = '$br_id') ";
    $select = "SELECT * FROM `branch_pending_details` WHERE status = 1 AND branch_id = '$br_id' AND token_no NOT IN (SELECT token_no FROM branch_daily_pending_details) AND created >= '$from_date' AND created <= '$to_date' ORDER BY id ASC LIMIT $start,20 ";
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
                echo '<div class = "col"><a title = "'.$bra_address.'" href = "lg_operate_pending.php?br_id='.$bra_id.'" class = "btn btn-primary p-2">'.$bra_tag_name.'</a></div>';
            }
        }
        ?></div>
    </div>
	<div class="col-md-12">

	    <table class = "table table-bordered">
<?php

$s = 0 ;
$select_count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `branch_pending_details` WHERE status = 1 AND branch_id = '$br_id' AND token_no NOT IN (SELECT token_no FROM branch_daily_pending_details) AND created >= '$from_date' AND created <= '$to_date' "));
$run = mysqli_query($con, $select);
?>
	        <caption class = "h2" style = "caption-side: top;text-align: center;">OPERATE PENDING (<?php echo get_branch_name_by($br_id); ?>) - <?php echo $select_count; ?></caption>
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
	                            <input type = "date" name = "from_date" value = "<?php echo date_format(date_create($from_date), 'Y-m-d'); ?>" id = "from_date" class = "form-control"required />
	                        </div>
	                        <div class = "col-md-2" style  = "text-align: right;">
	                            <label for = "to_date">To Date:</label>
	                        </div>
	                        <div class = "col-md-3">
	                            <input type = "date" name = "to_date" value = "<?php if(isset($_GET['to_date'])){echo date_format(date_create($_GET['to_date']), 'Y-m-d');}else{echo date('Y-m-d');} ?>" id = "to_date" class = "form-control" required />
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
	                <th class ="noprint">Procedure </th>
	                <th class ="noprint" title = "Referance Name">Ref. Name</th>
	                <th class ="noprint" title = "Referance Name">Recommended By</th>
	                <th>Token #</th>
	                <th>Total Amount</th>
	                <th>Received Amount</th>
	                <th>Pending Amount</th>
	            </tr>
	        </thead>
<?php
if(mysqli_num_rows($run) > 0)
{
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
    $total = intval($select_count/20)+1;
    if($total > 0){ ?>
    <tbody>
        <tr>
            <td colspan = "11">
                <div class = "row">
                    <div class = "col" style  = "text-align: center;">
                        <?php
                        if($page_no != 1)
                        {
                            echo '<a href = "lg_operate_pending.php?br_id='.$br_id.'&&from_date='.$from_date.'&to_date='.$_GET['to_date'].'&page_no=1" class = "btn btn-sm btn-info" style = "margin: 2px;">Start</a>';
                        }
                        for($i=1; $i<=$total; $i++)
                        {
                            if($i == $page_no)
                            {
                                echo '<a href = "lg_operate_pending.php?br_id='.$br_id.'&from_date='.$from_date.'&to_date='.$_GET['to_date'].'&page_no='.$i.'" class = "btn btn-sm btn-info active" style = "margin: 2px;">'.$i.'</a>';
                            }
                            else
                            {
                                echo '<a href = "lg_operate_pending.php?br_id='.$br_id.'&from_date='.$from_date.'&to_date='.$_GET['to_date'].'&page_no='.$i.'" class = "btn btn-sm btn-info" style = "margin: 2px;">'.$i.'</a>';
                            }
                        }
                        if($page_no != $total)
                        {
                            echo '<a href = "lg_operate_pending.php?br_id='.$br_id.'&from_date='.$from_date.'&to_date='.$_GET['to_date'].'&page_no='.$total.'" class = "btn btn-sm btn-info" style = "margin: 2px;">Last</a>';
                        }
                        ?>
                    </div>
                </div>
            </td>
        </tr>
    </tbody>    
<?php }
} ?>

	    </table>
	</div>
			
	</div>
</div>

</body>
</html>