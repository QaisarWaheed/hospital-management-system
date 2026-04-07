<?php include 'includes/connect.php'; 
if (isset($_POST['receive_quantity'])) 
{
	$purchase_id = $_POST['purchase_id'];
	$batch_no = $_POST['batch_no'];
	$retail = $_POST['retail'];
	$warrantee = $_POST['warrantee'];
	if($_POST['mfg_date'] != ''){$mfg_date = $_POST['mfg_date'];}else{$mfg_date = NULL;}
	if($_POST['expiry_date'] != ''){$expiry_date = $_POST['expiry_date'];}else{$expiry_date = NULL;}
	$select_purchase = mysqli_query($con, "SELECT * FROM purchase_items WHERE id = '$purchase_id' ");
	if (mysqli_num_rows($select_purchase) == 1) 
	{
		while ($row_purchase = mysqli_fetch_array($select_purchase)) 
		{
			$purchase_id = $row_purchase['id'];
			$item_id = $row_purchase['item_id'];
			$item_available_quantity = item_available_quantity($item_id);
			$status = $row_purchase['status'];
			$tries = $row_purchase['tries'];
			$net_quantity = $row_purchase['quantity'];
			$difference = $row_purchase['difference'];
			$quantity = $net_quantity + $difference;
		}
	}
	$receive_quantity = $_POST['receive_quantity'];
	if ($receive_quantity == $quantity) 
	{
		mysqli_query($con, "
			UPDATE purchase_items SET 
			`batch_no` = '$batch_no', 
			`retail` = '$retail',
			`warrantee` = '$warrantee',
			`mfg_date` = '$mfg_date',
			`expiry_date` = '$expiry_date',
			`sm_id` = '$user_id',
			`status` = '2'
			WHERE id = '$purchase_id' ");
		$add_quantity = $item_available_quantity + $receive_quantity;
		mysqli_query($con, "
			UPDATE items SET 
			`quantity` = '$add_quantity',
			`retail` = '$retail'
			WHERE id = '$item_id'
			");
		?>
	<script>
// 		alert('Receive Medicines SUCCESSFULLY');
		location.replace("show_item_purchase.php");
	</script>
	<?php
	}
	else
	{		$update_tries = $tries + 1;
			mysqli_query($con, "
			UPDATE purchase_items SET 
			`tries` = '$update_tries'
			WHERE id = '$purchase_id'
			");
	?>
	<script>
		alert('Medicines Not Received');
		location.replace("show_item_purchase.php");
	</script>
	<?php
	}
}
?>
<?php include 'includes/head.php'; ?>
	<title>Show item Purchase - <?php echo $company_trademark; ?></title>
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
<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">

		<table class="table">
			<thead>
				<caption style="caption-side: top;text-align: center;">
					<h2  style = "text-decoration: underline;">SHOW ITEM PURCHASE LIST</h2>
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
					<!--<th>Purchase #</th>-->
					<th>Item Name</th>
					<th>Retail</th>
					<th>Batch</th>
					<th>Drap No</th>
					<th>Mfg</th>
					<th>Expiry</th>
					<th>Qty</th>
					<th>Status</th>
					<th title="Medicine Manager Name">MM</th>
					<th title="Store Manager Name">SM</th>
				</tr>
			</thead>
			<tbody>
<?php
$party_name = '';
if (isset($_POST['search_bill']) && $_POST['search_bill'] != 0) 
{
	$select_purchase = mysqli_query($con, "SELECT * FROM purchase_items WHERE item_id > 0 AND invoice_no = '".$_POST['search_bill']."' ");
    $select_party = mysqli_query($con, "SELECT name FROM parties WHERE id IN (SELECT party_id FROM purchase_items WHERE invoice_no = '".$_POST['search_bill']."' ) ");
    if (mysqli_num_rows($select_party) == 1) 
    {
    	while ($row_party = mysqli_fetch_array($select_party)) 
    	{
    		$party_name .= $row_party['name'];
    	}
    }

}
else
{
	$select_purchase = mysqli_query($con, "SELECT * FROM purchase_items WHERE item_id > 0 AND `status` = '1' ORDER BY `status` ");
}
$s = 0;

if (mysqli_num_rows($select_purchase) > 0) {
	while ($row_purchase = mysqli_fetch_array($select_purchase)) {
		$s = $s + 1;
		$purchase_id = $row_purchase['id'];
		$created = $row_purchase['created'];
		$status = $row_purchase['status'];
		$tries = $row_purchase['tries'];
		$quantity = $row_purchase['quantity'];
		$batch_no = $row_purchase['batch_no'];
		$retail = $row_purchase['retail'];
		$warrantee = $row_purchase['warrantee'];
		$mfg_date = $row_purchase['mfg_date'];
		$expiry_date = $row_purchase['expiry_date'];
		$invoice_no = $row_purchase['invoice_no'];
		$party_invoice_no = $row_purchase['party_invoice_no'];
		$per_item_price = $row_purchase['per_item_price'];

		$purchase_item_id = $row_purchase['item_id'];
		$select_item = mysqli_query($con, "SELECT * FROM items WHERE id = '$purchase_item_id'  ");
		if (mysqli_num_rows($select_item) == 1) 
		{
			while ($row_item = mysqli_fetch_array($select_item)) 
			{
				$item_name = $row_item['name'];
        		$category_id = $row_item['category_id'];
        		$categories = "SELECT * FROM `categories` WHERE `id` = '$category_id'  ";
        		$select_category = mysqli_query($con, $categories);
        		if (mysqli_num_rows($select_category) == 1) 
        		{
        			while ($row_category = mysqli_fetch_array($select_category)) 
        			{
        				$category_name = $row_category['name'];
        			}
        		}
			}
		}
		$mm_id = $row_purchase['mm_id'];
		$select_mm = mysqli_query($con, "SELECT u_name FROM users WHERE id = '$mm_id'  ");
		if (mysqli_num_rows($select_mm) == 1) 
		{
			while ($row_mm = mysqli_fetch_array($select_mm)) 
			{
				$mm_name = $row_mm['u_name'];
			}
		}
		

	if($status == 1)
	{
		$status_msg ='Pending';
echo '
<tr>
<form method="POST">
	<td>'.$s.'</td>
	<td>'.$invoice_no.'</td>
	<td>'.$item_name.' - '.$category_name.'</td>
	<td><input required type="text" name="retail" value="'.$retail.'" /></td>
	<td><input required type="text" name="batch_no" value="'.$batch_no.'" /></td>
	<td><input type="text" name="warrantee" value="'.$warrantee.'" /></td>
	<td><input type="date" name="mfg_date" value="'.$mfg_date.'" /></td>
	<td><input type="date" name="expiry_date" value="'.$expiry_date.'" /></td>
	<td>
	<input type="hidden" name="purchase_id" value="'.$purchase_id.'" />
	<input onchange="this.form.submit()" type="number" name="receive_quantity" style="max-width: 100px;">
	</td>
	<td>'.$status_msg.'</td>
	<td title="Medicine Maganer">'.$mm_name.'</td>
	<td title="Medicines Are Pending">Not Receive</td>
</form>	
</tr>
';
	}
	else
	{
		$sm_id = $row_purchase['sm_id'];
		$select_sm = mysqli_query($con, "SELECT u_name FROM users WHERE id = '$sm_id'  ");
		if (mysqli_num_rows($select_sm) == 1) 
		{
			while ($row_sm = mysqli_fetch_array($select_sm)) 
			{
				$sm_name = $row_sm['u_name'];
			}
		}

		$status_msg ='Received';
echo '
<tr>
	<td>'.$s.'</td>
	<td>'.$item_name.' - '.$category_name.'</td>
	<td>'.$retail.'</td>
	<td>'.$batch_no.'</td>
	<td>'.$warrantee.'</td>
	<td>'.date_format(date_create($mfg_date), "d-m-y").'</td>
	<td>'.date_format(date_create($expiry_date), "d-m-y").'</td>
	<td>'.$quantity.'</td>
	<td>'.$status_msg.' IN '.$tries.' TRY </td>
	<td title="Medicine Maganer">'.$mm_name.'</td>
	<td title="Store Maganer">'.$sm_name.'</td>
</tr>
';
	}
	}
}
?>
			</tbody>
    <caption style = "caption-side: top;">
        <div style = "font-size:15px;">PARTY NAME: <?php echo $party_name; ?></div>
        <div style = "font-size:15px;">PARTY INVOICE NO: <?php echo $party_invoice_no; ?></div>
        <div style = "font-size:15px;">RECEIVE NO: <?php echo $invoice_no; ?></div>
        <div style = "font-size:15px;">RECEIVE DATE: <?php echo date_format(date_create($created), "d-m-Y"); ?></div>
    </caption>    
			
		</table>

	</div>
</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<!-- 
 -->