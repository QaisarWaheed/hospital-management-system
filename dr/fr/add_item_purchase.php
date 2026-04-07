<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) {
	$party_id = $_POST['party_id'];
	$company_id = $_POST['company_id'];
	$item_id = $_POST['item_id'];
	$invoice_no = $_POST['invoice_no'];
	$batch_no = $_POST['batch_no'];
	$warrantee = $_POST['warrantee'];
	if($_POST['mfg_date'] = ''){$mfg_date = $_POST['mfg_date'];}else{$mfg_date = NULL;}
	if($_POST['expiry_date'] = ''){$expiry_date = $_POST['expiry_date'];}else{$expiry_date = NULL;}
	$total_price = $_POST['total_price'];
	$quantity = $_POST['quantity'];
	$per_item_price = $_POST['cash'];

	$insert = "INSERT INTO `purchase_items`
	( `party_id`, `company_id`, `item_id`, `batch_no`, `warrantee`, `mfg_date`, `expiry_date`, `total_price`, `quantity`, `per_item_price`, `mm_id`, `invoice_no`) 
	VALUES 
	('$party_id', '$company_id', '$item_id', '$batch_no', '$warrantee', '$mfg_date', '$expiry_date', '$total_price', '$quantity', '$per_item_price', '$user_id', '$invoice_no')";
	$run = mysqli_query($con, $insert);
	if ($run) 
	{	?>
<!-- 	<script>
		alert('DATA SAVE SUCCESSFULLY');
	</script> -->
	<?php
	}
	else
	{
		echo $con->error;
	}
}
?>
<?php include 'includes/head.php'; ?>
	<title>Add Item Purchase - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

		<div class="row">

			<div class="col-md-12" style="text-align: center;">
				<label><h1>Add Item Purchase Form</h1></label>
			</div>
			<div class="col-md-12">

				<form method = "POST">

					<div class="row">

						<div class="col-md-12">
							<label>Party</label>
							<select name="party_id" class="form-control" required>
								<option value="">Select Party</option>
								<?php
								$categories = mysqli_query($con, "SELECT * FROM `parties` WHERE status = '1' ORDER BY name");
								if (mysqli_num_rows($categories) > 0) {
									while ($row_category = mysqli_fetch_array($categories)) {
										$cat_id = $row_category['id'];
										$cat_name = $row_category['name'];
										$cat_address = $row_category['address'];
										echo '<option value="'.$cat_id.'">'.$cat_name.' ('.$cat_address.')</option>';
									}
								}
								?>
							</select>
						</div>

						<div class="col-md-12">
							<label>Company</label>
							<select name="company_id" class="form-control" required>
								<option value="">Select Company</option>
								<?php
								$compaines = mysqli_query($con, "SELECT * FROM `item_companies` WHERE status = '1' ORDER BY name");
								if (mysqli_num_rows($compaines) > 0) {
									while ($row_company = mysqli_fetch_array($compaines)) {
										$com_id = $row_company['id'];
										$com_name = $row_company['name'];
										echo '<option value="'.$com_id.'">'.$com_name.'</option>';
									}
								}
								?>
							</select>
						</div>

						<div class="col-md-12">
<label>Item</label>
<select name="item_id" id="item_id" class="form-control" required onchange="myFunction(this)">
	<option>Select Item</option>
	<?php
	$items = mysqli_query($con, "SELECT * FROM `items` WHERE status = '1' ORDER BY name");
	if (mysqli_num_rows($items) > 0) {
		while ($row_item = mysqli_fetch_array($items)) {
			$item_id = $row_item['id'];
			$item_name = $row_item['name'];
			$category_id = $row_item['category_id'];
$categories = mysqli_query($con, "SELECT name FROM `categories` WHERE id = '$category_id' ");
if (mysqli_num_rows($categories) == 1) 
{
	while ($row_category = mysqli_fetch_array($categories)) 
	{
		$cat_name = $row_category['name'];
	}
}
			$barcode = $row_item['barcode'];
			echo '<option value="'.$item_id.'">'.$item_name.' - '.$cat_name.' ('.$barcode.')</option>';
		}
	}
	?>
</select>
						</div>
						<div class="col-md-6">
							<label>Batch No</label>
							<input type="text" name="batch_no" class="form-control">
						</div>
						<div class="col-md-6">
							<label>Warrantee / Dpar No / REG_ID</label>
							<input type="text" name="warrantee" class="form-control">
						</div>
						<div class="col-md-6">
							<label for="mfg_date">MFG Date</label>
							<input type="date"POST id="mfg_date" name="mfg_date" class="form-control">
						</div>
						<div class="col-md-6">
							<label for="expiry_date">Expiry Date</label>
							<input type="date"POST id="expiry_date" name="expiry_date" class="form-control">
						</div>
						<div class="col-md-12">
							<label for="total_price">Total Amount</label>
							<input onchange="myFunction1()" value="1"  type="number" step="0.01" min="1.0" id="total_price" name="total_price" class="form-control">
						</div>
						<div class="col-md-3">
							<label for="quantity">Quantity</label>
							<input onchange="myFunction1()" type="number" value="1" min="1" id="quantity" name="quantity" class="form-control">
						</div>
						<div class="col-md-3">
							<label for="per_item_price">Per Item Cost(Current)</label>
							<textarea name="cash" class="form-control" rows="1" style="resize: none;" readonly id="cash">0</textarea>
						</div>
						<div class="col-md-3">
							<label for="per_item_price_pre">Per Item Cost(Previous)</label>
							<!-- <input type="number" step="0.01" min="0.0" id="per_item_price_pre" readonly name="per_item_price_pre" class="form-control"> -->
							<textarea readonly required rows="1" style="resize: none;" readonly id="cash_1" name="cash_1" class="form-control">0</textarea>
						</div>
						<div class="col-md-3">
							<label for="invoice_no">Invoice No / Bill No</label>
							<input type="number" min="1" id="invoice_no" required name="invoice_no" class="form-control">
						</div>
						<div class="col-md-12" style="margin: 20px 0px;">

							<input type="submit" name="save" value="SAVE ITEM" class="btn btn-success">

							<input type="reset" name="clear" value="CLEAR FORM" class="btn btn-warning">
						</div>
					</div>

				</form>
			</div>

		</div>

	</div>

</div>


</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script>
function myFunction1() 
{
	var total_price = document.getElementById("total_price").value;
	var quantity = document.getElementById("quantity").value;
  document.getElementById("cash").innerHTML =  total_price / quantity;
}
</script>
<script>
function myFunction() {
	var item_id = document.getElementById("item_id").value;
  // document.getElementById("cash_1").innerHTML = item_id;
  document.getElementById("cash_1").innerHTML = <?php echo get_purchase_amount(item_id); ?>;
  // document.getElementById("tokan_get1").innerHTML = 8;
}
</script>