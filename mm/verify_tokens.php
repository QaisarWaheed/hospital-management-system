<?php include 'includes/connect.php';
$from = date('Y-m-d');
$to = date('Y-m-d');
$item_id = '';
$br_id = (int) $branch_id;
$end_date = date('Y-m-d 23:59:59');
if(isset($_POST['item_id']) && isset($_POST['from']) && $_POST['item_id'] != '')
{
    $item_id = $_POST['item_id'];
    $br_id = (int) $_POST['br_id'];
    $from = (string) $_POST['from'];
    $to = (string) $_POST['to'];
    $end_date = date_format(date_create($to), "Y-m-d 23:59:59");
}
?>
<?php include 'includes/head.php'; ?>
	<title>Verify Tokens - <?php echo $company_trademark; ?></title>
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>	
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
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-12 background_whitesmoke">
		<?php include 'top_row.php'; ?>
	</div>
	<div class="col-md-12">
    	<div style="">
    	    <?php ?>
    	    <form METHOD="POST">
    	        <div class="row">
    	            <div class="col-md-3">
    	                <h5 align="center">SELECT BRANCH</h5>
    	            </div>
    	            <div class="col-md-3">
    	                <h5 align="center">SELECT ITEM</h5>
    	            </div>
    	            <div class="col-md-2">
    	                <h5 align="center">FROM</h5>
    	            </div>
    	            <div class="col-md-2">
    	                <h5 align="center">TO</h5>
    	            </div>
    	            <div class="col-md-2">
    	                <h5 align="center">ACTION</h5>
    	            </div>
    	            <div class="col-md-3">
                        <select required class="form-control" size = "1" style="min-width: 200px;text-transform: uppercase;" name="br_id">
                            <option value = "0">ALL</option>
                        <?php 
                        $branch = "SELECT * FROM branchs WHERE id != 0 AND status = 1 ORDER BY `address` ASC ";
                        $run_branch = mysqli_query($con, $branch);
                        if (mysqli_num_rows($run_branch) > 0) 
                        {
                        while ($row_branch = mysqli_fetch_array($run_branch)) 
                            {
                                $row_branch_id = $row_branch['id'];
                            echo '<option ';if($br_id == $row_branch_id){echo " SELECTED ";} echo ' value="'.$row_branch['id'].'">'.$row_branch['address'].'</option>';
                            }
                        }
                        else
                        {
                        echo '<option value="">Add Branch Data</option>';
                        }
                        ?>
                        </select>
    	            </div>
    	            <div class="col-md-3">
                        <select class="form-control" size = "1" style="min-width: 200px;text-transform: uppercase;" name="item_id[]" multiple>
                        <?php 
                        $item = "SELECT * FROM items WHERE status = 1 ORDER BY `name` ";
                        $run_item = mysqli_query($con, $item);
                        if (mysqli_num_rows($run_item) > 0) 
                        {
                        while ($row_item = mysqli_fetch_array($run_item)) 
                            {
                                $row_item_id = $row_item['id'];
                            echo '<option ';if($item_id == $row_item_id){echo " SELECTED ";} echo ' value="'.$row_item['id'].'">'.$row_item['name'].'</option>';
                            }
                        }
                        else
                        {
                        echo '<option value="">Add Items Data</option>';
                        }
                        ?>
                        </select>
    	            </div>
    	            <div class="col-md-2">
    	                <input type="date" value = "<?php echo $from; ?>" name="from" class="form-control" required />
    	            </div>
    	            <div class="col-md-2">
    	                <input type="date" value = "<?php echo $to; ?>" name="to" class="form-control" required />
    	            </div>
    	            <div class="col-md-2">
    	                <input type="submit" value="SEARCH" name="token" class="btn btn-info btn-sm" />
    	            </div>
    	        </div>
    	    </form>
    	</div>
<?php 
if(isset($_POST['item_id']) && isset($_POST['from']) && $_POST['item_id'] != '')
{
    header("Content-Type: text/plain");
    $item_id = '';
    foreach ($_POST['item_id'] as $selectedOption)
    {
        $item_id .= $selectedOption.',';
    }
    // echo $item_id;
    $item_id .= '-1';
    // X_RAYS / TEST
    // $item_id = '994, 855, 897, 1024, 400, 1064, 1243, 485, 1049, 1323, 382, 1022, 1226, 397, 1061, 1240, 898, 1066, 381, 1098, 1225, 380, 1081, 1224, 403, 1070, 1245, 1017, 1019, 1333, 365, 1042, 1211';
    // DENTAL
    // $item_id .= '854, 867, 868, 869, 870, 871, 872, 873, 874, 875, 876, 877, 879, 880, 881, 882, 1449, 1450, 1451, 1452, 1453, 1454, 1455, 1456, 1457, 1458, 1459, 1460, 1462, 1463, 1464, 1465';
    // $item_id = $_POST['item_id'];
    $br_id = $_POST['br_id'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $end_date = date_format(date_create($to), "Y-m-d 23:59:59");
?>
        <div class=""> 
        <div id="divID">
            <table class = "table">
                <thead>
                    <tr>
                        <th>S#</th>
                        <th>Date</th>
                        <th>Token #</th>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Received</th>
                        <th>Token By</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $s = 0;
                    $total_amount = 0;
                    $total_amount_received = 0;
                    if($br_id != '0')
                    {
                        $select_token = "SELECT * FROM tokans WHERE status = 1 AND branch_id = '$br_id' AND created >= '$from' AND created < '$end_date' AND id IN (SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE item_id IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN ($item_id) AND branch_id = '$br_id' ))";
                    }
                    else
                    {
                        $select_token = "SELECT * FROM tokans WHERE status = 1 AND created >= '$from' AND created < '$end_date' AND id IN (SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE item_id IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN ($item_id)))";
                    }
                    $run_token = mysqli_query($con, $select_token);
                    if(mysqli_num_rows($run_token) > 0)
                    {
                    while($row_token = mysqli_fetch_array($run_token))
                    {
                        $token_no = $row_token['id'];
                        $patient = get_patient_name_by_token_no($row_token['id']);
                        // $service = get_given_services_by_token_no($token_no);
                        $branch_tag_name = get_branch_tag_name_by($row_token['branch_id']);
                        $token_by = get_uname_by_id($row_token['user_id']);
                        $amount = $row_token['cash'];
                        $total_amount = $total_amount + $amount;
                        $amount_received = $row_token['cash_received'];
                        $total_amount_received = $total_amount_received + $amount_received;
                        $created = date_format(date_create($row_token['created']), "d-m-Y");
                        $s++;
                    echo '
                    <tr>
                        <td>'.$s.'</td>
                        <td>'.$created.'</td>
                        <td>'.$token_no.'</td>
                        <td>'.$patient.'</td>
                        <td>'.$branch_tag_name.'</td>
                        <td>'.$amount.'</td>
                        <td>'.$amount_received.'</td>
                        <td>'.$token_by.'</td>
                    <tr>
                    ';
                    }
                    }
                    else
                    {
                    echo '
                    <tr>
                        <td colspan = "7">'.$con->error.'</td>
                    <tr>
                    ';
                    }
                    ?>
                    <tr>
                        <th colspan = "5"></th>
                        <th><?php echo $total_amount; ?></th>
                        <th><?php echo $total_amount_received; ?></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan = "6">
                            <form action="../includes/tcpdf/verify_token.php" target="_blank" method = "POST">
                                <input type = "hidden" name = "start_date" value = "<?php echo $from; ?>" />
                                <input type = "hidden" name = "end_date" value = "<?php echo $to; ?>" />
                                <input type = "hidden" name = "service_id" value = "<?php echo $item_id; ?>" />
                                <input type = "hidden" name = "br_id" value = "<?php echo $br_id; ?>" />
            	                <input type="submit" value="PDF" name="print" class="btn btn-info btn-sm btn-primary" />
            	                <input type="submit" value="EXCEL" name="excel" class="btn btn-info btn-sm btn-info" />
                            </form>
                        </th>
                    </tr>
                </tbody>
                <caption>
                </caption>
            </table>
        </div>
        </div>
<?php
}
?>
	</div>
</div>

</body>
</html>    