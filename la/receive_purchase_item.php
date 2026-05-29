<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
if(isset($_POST['receive_quantity'])) 
{
    // print_r($_POST);
    $receive_quantity = $_POST['receive_quantity'];
    $purchase_lab_item_id = $_POST['purchase_lab_item_id'];
    $item_id = $_POST['item_id'];
    $purchase_lab_item_retail = $_POST['purchase_lab_item_retail'];
    $purchase_lab_item_batch_no = $_POST['purchase_lab_item_batch_no'];
    $purchase_lab_item_drap_no = $_POST['purchase_lab_item_drap_no'];
    $purchase_lab_item_mfg = $_POST['purchase_lab_item_mfg'];
    $purchase_lab_item_exipry = $_POST['purchase_lab_item_exipry'];
    $update = "
    UPDATE
        `purchase_lab_items`
    SET
        `purchase_lab_item_receiving_attempts` = `purchase_lab_item_receiving_attempts`+1,
        `purchase_lab_item_received_by` = '$lab_admin_user_id',
        `purchase_lab_item_received_at` = '$current_date',
        `purchase_lab_item_retail` = '$purchase_lab_item_retail',
        `purchase_lab_item_batch_no` = '$purchase_lab_item_batch_no',
        `purchase_lab_item_drap_no` = '$purchase_lab_item_drap_no',
        `purchase_lab_item_mfg` = '$purchase_lab_item_mfg',
        `purchase_lab_item_exipry` = '$purchase_lab_item_exipry',
        `purchase_lab_item_status` = '2'
    WHERE
         `purchase_lab_item_quantity` = '$receive_quantity' AND `purchase_lab_item_id` = '$purchase_lab_item_id' AND `purchase_lab_item_status` = '1' ";
    mysqli_query($con, $update);
    if(mysqli_affected_rows($con) == 1)
    {
        $update_stock = "UPDATE `items` SET `quantity` = `quantity`+'$receive_quantity' WHERE `id` = '$item_id' ";
        if(mysqli_query($con, $update_stock))
        {
            ?>
            <script>
            		alert('Receive Lab Items Successfully');
        	</script>
            <?php   
        }
        else
        {
            echo $con->error;
        }
    }
    header('location: receive_purchase_item.php?msg=success');
    exit(0);
}
include 'includes/head.php'; 
?>
	<title>SHOW LAB ITEM PURCHASE - <?php echo $company_trademark; ?></title>
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
    font-size: 14px;
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
	<div class="col-md-3 background_whitesmoke nodisplay_print">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
    	<div class="row">
    		<table class="table table-sm nodisplay_print">
    			<thead>
    				<caption style="caption-side: top;text-align: center;">
    					<h2  style = "text-decoration: underline;color: black;">SHOW ITEM PURCHASE LIST</h2>
    				</caption>
    				<tr class = "noprint">
    					<td colspan="3"></td>
    					<td colspan="3" style="text-align: right;"><label>ENTER BILL NO</label></td>
    					<td colspan="2">
    						<form method="POST">
    							<input value="<?php if(isset($_POST['search_bill'])){echo $_POST['search_bill'];}?>" onchange="this.form.submit()" placeholder="ENTER BILL NO" type="number" name="search_bill" size="2" maxlength="3" >
    						</form>
    					</td>
    				</tr>
    				<tr style = "text-align: center;border: 3px solid black;">
    					<th>S #</th>
    					<th>Id</th>
    					<th>Item Name</th>
    					<th>Retail</th>
    					<th>Batch</th>
    					<th>Drap No</th>
    					<th>Mfg</th>
    					<th>Expiry</th>
    					<th>Qty</th>
    				</tr>
    			</thead>
    			<tbody>
                <?php
                $party_name = '';
                if (isset($_POST['search_bill']) && $_POST['search_bill'] != 0) 
                {
                    $query = "SELECT purchase_lab_item_exipry, purchase_lab_item_mfg, purchase_lab_item_drap_no, purchase_lab_item_batch_no, purchase_lab_item_retail, purchase_lab_items.purchase_lab_item_id, invoice_purchase_lab_items.invoice_party_bill_no, purchase_lab_items.invoice_purchase_lab_item_bill_no, purchase_lab_items.purchase_lab_item_date, items.name AS item_name, parties.name AS party_name, `purchase_lab_item_rate`, `purchase_lab_item_quantity`, `purchase_lab_item_price`, users.u_name, purchase_lab_items.purchase_lab_item_status FROM `purchase_lab_items` INNER JOIN invoice_purchase_lab_items ON purchase_lab_items.invoice_purchase_lab_item_bill_no = invoice_purchase_lab_items.invoice_purchase_lab_item_bill_no INNER JOIN parties ON invoice_purchase_lab_items.invoice_party_id = parties.id INNER JOIN items ON purchase_lab_items.item_id = items.id INNER JOIN users ON purchase_lab_items.purchase_lab_item_created_by = users.id WHERE purchase_lab_items.invoice_purchase_lab_item_bill_no = '".$_POST['search_bill']."' AND purchase_lab_items.purchase_lab_item_status = '1' ";
                }
                else
                {
                    $query = "SELECT items.id AS item_id, purchase_lab_item_exipry, purchase_lab_item_mfg, purchase_lab_item_drap_no, purchase_lab_item_batch_no, purchase_lab_item_retail, purchase_lab_items.purchase_lab_item_id, invoice_purchase_lab_items.invoice_party_bill_no, purchase_lab_items.invoice_purchase_lab_item_bill_no, purchase_lab_items.purchase_lab_item_date, items.name AS item_name, parties.name AS party_name, `purchase_lab_item_rate`, `purchase_lab_item_quantity`, `purchase_lab_item_price`, users.u_name, purchase_lab_items.purchase_lab_item_status FROM `purchase_lab_items` INNER JOIN invoice_purchase_lab_items ON purchase_lab_items.invoice_purchase_lab_item_bill_no = invoice_purchase_lab_items.invoice_purchase_lab_item_bill_no INNER JOIN parties ON invoice_purchase_lab_items.invoice_party_id = parties.id INNER JOIN items ON purchase_lab_items.item_id = items.id INNER JOIN users ON purchase_lab_items.purchase_lab_item_created_by = users.id WHERE purchase_lab_items.purchase_lab_item_status = '1' ";
                }
                	$select_purchase = mysqli_query($con, $query);
                    $s = 0;
                    if (mysqli_num_rows($select_purchase) > 0) 
                    {
                    	while ($row_purchase = mysqli_fetch_array($select_purchase)) 
                    	{
                    		$s = $s + 1;
                    		$purchase_lab_item_id = $row_purchase['purchase_lab_item_id'];
                    		$invoice_purchase_lab_item_bill_no = $row_purchase['invoice_purchase_lab_item_bill_no'];
                    		$invoice_party_bill_no = $row_purchase['invoice_party_bill_no'];
                    		$party_name = $row_purchase['party_name'];
                    		$purchase_lab_item_date = $row_purchase['purchase_lab_item_date'];
                            echo '
                            <form method="POST">
                                <tr>
                                    <td>'.$s.'</td>
                                    <td>'.$purchase_lab_item_id.'</td>
                                    <td>'.$row_purchase['item_name'].'</td>
                                    <td>
                                        <input type = "hidden" name = "item_id" value = "'.$row_purchase['item_id'].'" />
                                        <input class = "form-control" required type="text" name="purchase_lab_item_retail" id = "purchase_lab_item_retail" value="'.$row_purchase['purchase_lab_item_retail'].'" />
                                    </td>
                                    <td>
                                        <input class = "form-control" required type="text" name="purchase_lab_item_batch_no" id = "purchase_lab_item_batch_no" value="'.$row_purchase['purchase_lab_item_batch_no'].'" />
                                    </td>
                                    <td>
                                        <input class = "form-control" type="text" name="purchase_lab_item_drap_no" id = "purchase_lab_item_drap_no" value="'.$row_purchase['purchase_lab_item_drap_no'].'" />
                                    </td>
                                    <td>
                                        <input class = "form-control" type="date" name="purchase_lab_item_mfg" id = "purchase_lab_item_mfg" value="'.$row_purchase['purchase_lab_item_mfg'].'" /></td>
                                    <td>
                                        <input class = "form-control" type="date" name="purchase_lab_item_exipry" id = "purchase_lab_item_exipry" value="'.$row_purchase['purchase_lab_item_exipry'].'" />
                                    </td>
                                    <td>
                                        <input type="hidden" name="purchase_lab_item_id" value="'.$purchase_lab_item_id.'" />
                                        <input onchange="this.form.submit()" type="number" name="receive_quantity" style="max-width: 100px;">
                                    </td>
                                </tr>
                            </form>	';
                        }
                    } ?>
        			</tbody>
                    <caption style = "caption-side: top;color: black;">
                        <div style = "font-size:15px;">PARTY NAME: <?php echo $party_name; ?></div>
                        <div style = "font-size:15px;">PARTY INVOICE NO: <?php echo $invoice_party_bill_no; ?></div>
                        <div style = "font-size:15px;">RECEIVE NO: <?php echo $invoice_purchase_lab_item_bill_no; ?></div>
                        <div style = "font-size:15px;">RECEIVE DATE: <?php echo date_format(date_create($purchase_lab_item_date), "d-m-Y"); ?></div>
                    </caption>    
    		</table>
    		<?php
    		$s = 0;
    		$today_date = date('Y-m-d');
    		if(isset($_POST['search_bill']) && $_POST['search_bill'] != '')
    		{
    		    $search_bill = $_POST['search_bill'];
        		$today = "SELECT items.name, `purchase_lab_item_quantity`, purchase_lab_items.purchase_lab_item_id, `purchase_lab_item_mfg`,`purchase_lab_item_exipry`,`purchase_lab_item_batch_no`, LM.u_name AS lab_manager, LA.u_name AS lab_admin, parties.name AS party_name, invoice_purchase_lab_items.invoice_purchase_lab_item_date, invoice_purchase_lab_items.invoice_party_bill_no FROM purchase_lab_items INNER JOIN users LM ON purchase_lab_items.purchase_lab_item_created_by = LM.id LEFT JOIN users LA ON purchase_lab_items.purchase_lab_item_received_by = LA.id INNER JOIN items ON purchase_lab_items.item_id = items.id INNER JOIN invoice_purchase_lab_items ON purchase_lab_items.invoice_purchase_lab_item_bill_no = invoice_purchase_lab_items.invoice_purchase_lab_item_bill_no INNER JOIN parties ON invoice_purchase_lab_items.invoice_party_id = parties.id WHERE `purchase_lab_item_status` = '2' AND purchase_lab_items.invoice_purchase_lab_item_bill_no = '$search_bill' ";
    		}
    		else
    		{
        		$today = "SELECT items.name, `purchase_lab_item_quantity`, purchase_lab_items.purchase_lab_item_id, `purchase_lab_item_mfg`,`purchase_lab_item_exipry`,`purchase_lab_item_batch_no`, LM.u_name AS lab_manager, LA.u_name AS lab_admin FROM purchase_lab_items INNER JOIN users LM ON purchase_lab_items.purchase_lab_item_created_by = LM.id LEFT JOIN users LA ON purchase_lab_items.purchase_lab_item_received_by = LA.id INNER JOIN items ON purchase_lab_items.item_id = items.id WHERE `purchase_lab_item_status` = '2' AND purchase_lab_items.purchase_lab_item_received_at LIKE '$today_date%' ";
    		}
    		$run_today = mysqli_query($con, $today);
    		if(mysqli_num_rows($run_today) > 0)
    		{ ?>
    		<table class = "table table-sm table-hover table-bordered">
    		    <thead>
    		        <tr>
    		            <th>S#</th>
    		            <th>ID</th>
    		            <th>ITEM NAME</th>
    		            <th>BATCH #</th>
    		            <th>MFG</th>
    		            <th>EXPIRY</th>
    		            <th>QUANTITY</th>
    		            <th>LAB MANAGER</th>
    		            <th>LAB ADMIN</th>
    		        </tr>
    		    </thead>
    		    <tbody>
		        <?php
		        while($row_today = mysqli_fetch_array($run_today))
		        {
		            $invoice_purchase_lab_item_date = $row_today['invoice_purchase_lab_item_date'];
		            $invoice_party_bill_no_get = $row_today['invoice_party_bill_no'];
		            $invoice_party_name = $row_today['party_name'];
		            $s++; ?>
		            <tr>
		                <td><?php echo $s; ?></td>
		                <td><?php echo $row_today['purchase_lab_item_id']; ?></td>
		                <td><?php echo $row_today['name']; ?></td>
		                <td><?php echo $row_today['purchase_lab_item_batch_no']; ?></td>
		                <td><?php echo $row_today['purchase_lab_item_mfg']; ?></td>
		                <td><?php echo $row_today['purchase_lab_item_exipry']; ?></td>
		                <td><?php echo $row_today['purchase_lab_item_quantity']; ?></td>
		                <td><?php echo $row_today['lab_manager']; ?></td>
		                <td><?php echo $row_today['lab_admin']; ?></td>
		            </tr>
		        <?php } ?>
    		    </tbody>
    		    <caption style = "caption-side: top; color: black;">
    		        <h3 align = "center">LAB ITEMS RECEIVED</h3>
    		        <div class = "row">
    		            <div class = "col-md-5">PARTY: <strong> <?php echo $invoice_party_name; ?></strong></div>
    		            <div class = "col-md-2">INVOICE NO: <strong> <?php echo $invoice_party_bill_no_get; ?></strong></div>
    		            <div class = "col-md-2">BILL NO: <strong> <?php echo $search_bill; ?></strong></div>
    		            <div class = "col-md-3">RECEIVE DATE: <strong> <?php echo date_format(date_create($invoice_purchase_lab_item_date), "d-m-Y"); ?></strong></div>
    		        </div>
    		    </caption>

    		</table>
    		<?php }
    		?>
    	</div>
    </div>
</div>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>