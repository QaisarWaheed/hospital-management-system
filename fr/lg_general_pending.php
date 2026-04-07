<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
$br_id = $branch_id;
$from_date = date('Y-m-d');
$to_date = date('Y-m-d 23:59:59');
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
	        <thead>
	            <tr class = "noprint">
	                <form>
	                <th colspan = "12">
	                    <div class = "row">
	                        <div class = "col" style  = "text-align: right;">
	                            <label for = "from_date">BRANCH:</label>
	                        </div>
	                        <div class = "col" style  = "text-align: right;">
	                            <select class = "form-control">
	                                <option value = "">ALL</option>
	                                <?php
	                                $select_bracnhes = "SELECT * FROM `branchs` WHERE `status` = '1' ";
	                                $run_branch = mysqli_query($con, $select_bracnhes);
	                                if(mysqli_num_rows($run_branch) > 0)
	                                {
	                                    while($row_branch = mysqli_fetch_array($run_branch))
	                                    {
	                                        if($br_id == $row_branch['id'])
	                                        {
    	                                        echo '<option SELECTED value = "'.$row_branch['id'].'">'.$row_branch['tag_name'].'</option>';
	                                        }
	                                        else
	                                        {
    	                                        echo '<option value = "'.$row_branch['id'].'">'.$row_branch['tag_name'].'</option>';
	                                        }
	                                    }
	                                }
	                                ?>
	                            </select>
	                        </div>
	                        <div class = "col" style  = "text-align: right;">
	                            <label for = "from_date">From Date:</label>
	                        </div>
	                        <div class = "col">
	                            <input type = "hidden" value = "<?php echo $br_id; ?>" name = "br_id" id = "br_id" required />
	                            <input type = "date" name = "from_date" value = "<?php if($_GET['from_date'] != ''){echo $_GET['from_date'];}else{echo date('Y-m-d');} ?>" id = "from_date" class = "form-control"required />
	                        </div>
	                        <div class = "col" style  = "text-align: right;">
	                            <label for = "to_date">To Date:</label>
	                        </div>
	                        <div class = "col">
	                            <input type = "date" name = "to_date" value = "<?php if($_GET['to_date'] != ''){echo $_GET['to_date'];}else{echo date('Y-m-d');} ?>" id = "to_date" class = "form-control" required />
	                        </div>
	                        <div class = "col" style  = "text-align: center;">
	                            <input type = "submit" value = "SEARCH" name = "submit" style  = "min-width: 100%;min-height: 100%;" id = "submit" class = "btn btn-sm btn-info" />
	                        </div>
	                    </div>
	                </th>
	                </form>
                </tr>
	            <tr>
	                <th>S #</th>
	                <th class ="noprint" title = "Penging ID">Id</th>
	                <th>Time</th>
	                <th>Date</th>
	                <th>Username</th>
	                <th>Branch</th>
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
if(isset($_GET['from_date']) && $_GET['from_date'] != '')
{
    if($_GET['from_date'] != '' && $_GET['to_date'] != '')
    {
        $to_date = '';
        $from_date = $_GET['from_date'];
        $to_date .= $_GET['to_date'];
        $to_date .= " 23:59:59";
        if($_GET['br_id'] != '')
        {
            $br_id = $_GET['br_id'];
            $select = "SELECT branch_daily_pending_details.id, branch_daily_pending_details.created, branchs.tag_name, tokans.branch_id, patients.name, branch_daily_pending_details.ref_name, branch_daily_pending_details.ref_phone, branch_daily_pending_details.recommended_by, tokans.cash, tokans.cash_received, users.u_name, tokans.id AS token_no FROM `branch_daily_pending_details` INNER JOIN tokans ON branch_daily_pending_details.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN branchs ON tokans.branch_id = branchs.id INNER JOIN users ON tokans.user_id = users.id WHERE tokans.status = '1' AND tokans.branch_id = '$br_id' AND branch_daily_pending_details.created >= '$from_date' AND branch_daily_pending_details.created <= '$to_date' ";
        }
        else
        {
            $select = "SELECT DISTINCT branch_daily_pending_details.id, branch_daily_pending_details.created, branchs.tag_name, patients.name, branch_daily_pending_details.ref_name,  branch_daily_pending_details.ref_phone, branch_daily_pending_details.recommended_by, tokans.cash, tokans.cash_received, users.u_name, tokans.id AS token_no FROM `branch_daily_pending_details` INNER JOIN tokans ON branch_daily_pending_details.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN branchs ON tokans.branch_id INNER JOIN users ON tokans.user_id = users.id WHERE branch_daily_pending_details.token_no IN (SELECT id FROM tokans WHERE tokans.status = '1') AND branch_daily_pending_details.created >= '$from_date' AND branch_daily_pending_details.created <= '$to_date' GROUP BY branch_daily_pending_details.id ";
        }
    }
    else
    {
        $select = "SELECT branch_daily_pending_details.id, branch_daily_pending_details.created, branchs.tag_name, tokans.branch_id, patients.name, branch_daily_pending_details.ref_name, branch_daily_pending_details.ref_phone, branch_daily_pending_details.recommended_by, tokans.cash, tokans.cash_received, users.u_name, tokans.id AS token_no FROM `branch_daily_pending_details` INNER JOIN tokans ON branch_daily_pending_details.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN branchs ON tokans.branch_id = branchs.id INNER JOIN users ON tokans.user_id = users.id WHERE tokans.status = '1' AND tokans.branch_id = '$br_id' AND branch_daily_pending_details.created >= '$from_date' AND branch_daily_pending_details.created <= '$to_date' ";
    }
}
else
{
    // $select = "SELECT * FROM `branch_daily_pending_details` WHERE token_no IN (SELECT id FROM tokans WHERE status = '1') AND created >= '".date('Y-m-d')."' AND created <= '".date('Y-m-d')."' ";
    $select = "SELECT branch_daily_pending_details.id, branch_daily_pending_details.created, branchs.tag_name, tokans.branch_id, patients.name, branch_daily_pending_details.ref_name, branch_daily_pending_details.ref_phone, branch_daily_pending_details.recommended_by, tokans.cash, tokans.cash_received, users.u_name, tokans.id AS token_no FROM `branch_daily_pending_details` INNER JOIN tokans ON branch_daily_pending_details.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN branchs ON tokans.branch_id = branchs.id INNER JOIN users ON tokans.user_id = users.id WHERE tokans.status = '1' AND tokans.branch_id = '$br_id' AND branch_daily_pending_details.created >= '$from_date' AND branch_daily_pending_details.created <= '$to_date' ";
}
$s = 0 ;
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $pending_id = $row['id'];
        $created = $row['created'];
        $ref_name = $row['ref_name'];
        $recommended_by = $row['recommended_by'];
        $token_no = $row['token_no'];
        $get_branch = $row['tag_name'];
        $user_name = $row['u_name'];
        $patient_name = $row['name'];
        $receive = $row['cash_received'];
        $total_amount = $row['cash'];
        $receive_amount = $row['cash_received'];
        $received = $receive + $receive_amount;
        $pending_amount = $total_amount - $received;
        if($pending_amount > 0)
        {
        $s = $s + 1;
        echo '
                <tr>
                    <td class ="h6">'.$s.'</td>
                    <td class ="noprint h6">'.$pending_id.'</td>
                    <td class ="h6">'.date_format(date_create($created), "H:i:s").'</td>
                    <td class ="h6">'.date_format(date_create($created), "d-m-Y").'</td>
                    <td class ="h6">'.$user_name.'</td>
                    <td class ="h6">'.$get_branch.'</td>
                    <td class ="h6">'.$patient_name.'</td>
                    <td class ="noprint h6">'.$ref_name.'</td>
                    <td class ="noprint h6">'.$recommended_by.'</td>
                    <td class ="h6">'.$token_no.'</td>
                    <td class ="h6" style = "text-align: center;">'.number_format($total_amount).'</td>
                    <td class ="h6" style = "text-align: center;">'.number_format($received).'</td>
                    <td class ="h6" style = "text-align: center;">'.number_format($pending_amount).'</td>
                </tr>';
        }
    }
} ?>
        <caption id = "table-caption" class = "h2" style = "caption-side: top;text-align: center;">
            GENERAL PENDING (<?php echo get_branch_name_by($br_id); ?>)FROM:  <?php echo date_format(date_create($from_date), "d-M-Y"); ?> TO: <?php echo date_format(date_create($to_date), "d-M-Y"); ?>
        </caption>
	    </table>
	</div>
			
	</div>
</div>

</body>
</html>
<script>
    const captionElement = document.getElementById('table-caption');
    if (captionElement) {
        document.title = captionElement.textContent;
    }
</script>