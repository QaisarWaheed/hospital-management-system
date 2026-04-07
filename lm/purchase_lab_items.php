<?php 
include '../lab/includes/config.php';
include 'connect.php'; 
include '../lab/includes/head.php'; 
$select_data = "SELECT invoice_purchase_lab_item_date, `invoice_purchase_lab_item_bill_no`, `invoice_party_bill_no`, parties.name FROM `invoice_purchase_lab_items` INNER JOIN parties ON invoice_purchase_lab_items.invoice_party_id = parties.id ORDER BY invoice_purchase_lab_items.invoice_purchase_lab_item_id DESC LIMIT 0,1 ";
$run_data = mysqli_query($con, $select_data);
if(mysqli_num_rows($run_data) == 1)
{
    while($row_data = mysqli_fetch_array($run_data))
    {
        $invoice_purchase_lab_item_bill_no = $row_data['invoice_purchase_lab_item_bill_no'];
        $invoice_purchase_lab_item_date = $row_data['invoice_purchase_lab_item_date'];
        $invoice_party_bill_no = $row_data['invoice_party_bill_no'];
        $invoice_party_name = $row_data['name'];        
    }
}
else
{
    $invoice_purchase_lab_item_bill_no = '';
    $invoice_purchase_lab_item_date = '';
    $invoice_party_bill_no = '';
    $invoice_party_name = '';
}

if(isset($_GET['delete']) && $_GET['delete'] != '')
{
    $delete = $_GET['delete'];
    if(mysqli_query($con, "UPDATE `purchase_lab_items` SET `purchase_lab_item_status` = '0', `purchase_lab_item_deleted_by` = '$lab_manager_user_id', `purchase_lab_item_deleted_at` = '$current_date' WHERE `purchase_lab_item_status` = '1' AND `purchase_lab_item_id` = '$delete' "))
    {
        header('location: purchase_lab_items.php?msg=delete-success');
    }
    else
    {
        header('location: purchase_lab_items.php?msg=delete-error');
    }
}
if(isset($_POST['add_purchase_lab_item']))
{
    $insert_invoice = "INSERT INTO `invoice_purchase_lab_items`(`invoice_purchase_lab_item_id`, `invoice_purchase_lab_item_date`, `invoice_purchase_lab_item_bill_no`, `invoice_party_bill_no`, `invoice_party_id`, `invoice_purchase_lab_item_created_by`, `invoice_purchase_lab_item_created_at`) 
    VALUES (NULL, '".$_POST['invoice_purchase_lab_item_date']."', '".$_POST['invoice_purchase_lab_item_bill_no']."', '".$_POST['invoice_party_bill_no']."', '".$_POST['invoice_party_id']."', '$lab_manager_user_id', '$current_date') ";
    if(mysqli_query($con, $insert_invoice))
    {
        header('location: purchase_lab_items.php?msg=add-success');
    }
    else
    {
        header('location: purchase_lab_items.php?msg=add-error');
    }
    exit(0);
}

if(isset($_POST['save_purchase_lab_item']))
{
    if($_POST['item_id'] == '')
    {
        header('location: purchase_lab_items.php?msg=error');
    }
    $item_id = $_POST['item_id'];
    $invoice_purchase_lab_item_bill_no = $_POST['invoice_purchase_lab_item_bill_no'];
    $purchase_lab_item_date = $_POST['purchase_lab_item_date'];
    $purchase_lab_item_rate = $_POST['purchase_lab_item_rate'];
    $purchase_lab_item_quantity = $_POST['purchase_lab_item_quantity'];
    $purchase_lab_item_price = $purchase_lab_item_rate*$purchase_lab_item_quantity;
    if(mysqli_num_rows(mysqli_query($con, "SELECT * FROM `purchase_lab_items` WHERE `item_id` = '$item_id' AND `invoice_purchase_lab_item_bill_no` = '$invoice_purchase_lab_item_bill_no' ")) == 0)
    {
        $insert_purchase = "INSERT INTO `purchase_lab_items`(`purchase_lab_item_id`, `purchase_lab_item_date`, `invoice_purchase_lab_item_bill_no`, `item_id`, `purchase_lab_item_rate`, `purchase_lab_item_quantity`, `purchase_lab_item_price`, `purchase_lab_item_created_by`, `purchase_lab_item_created_at`)
        VALUES (NULL, '$purchase_lab_item_date', '$invoice_purchase_lab_item_bill_no', '$item_id', '$purchase_lab_item_rate', '$purchase_lab_item_quantity', '$purchase_lab_item_price', '$lab_manager_user_id', '$current_date')";
        if(mysqli_query($con, $insert_purchase))
        {
            header('location: purchase_lab_items.php?msg=save-success');
        }
        else
        {
            header('location: purchase_lab_items.php?msg=save-error');
        }
    }
    else
    {
        header('location: purchase_lab_items.php?msg=save-error-duplicate');
    }
    exit(0);
}
?>
	<link rel="stylesheet" type="text/css" href="../lab/css/nav_style.css"> 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
<script>
$(document).ready(function() {
  $('select').selectize({
    sortField: 'text'
  });
});    
</script>
	<title>PURCHASE LAB ITEMS - <?php echo $company_trademark; ?></title>
</head>
<body class="background_image">
    <div class="row" style="margin: 0px;">
    	<div class="col-md-12" style="text-align: center;background: lightgreen;">
    		<label><h1><?php echo $company_name; ?> </h1></label>
    	</div>
    	<div class="col-md-2 background_whitesmoke nodisplay_print">
    		<?php include 'left_navigation.php'; ?>
    	</div>
    	<div class="col-md-10">
    	    <div class = "row">
    	        <div class = "col-sm-12 nodisplay_print">
	                <form method = "POST">
        	            <div class = "row">
        	                <div class = "col">
        	                    <label for = "invoice_purchase_lab_item_bill_no">BILL #</label>
        	                        <input readonly type = "number" value = "<?php echo $invoice_purchase_lab_item_bill_no+1; ?>" class = "form-control" required />
        	                        <input type = "hidden" value = "<?php echo $invoice_purchase_lab_item_bill_no+1; ?>" min = "<?php echo $invoice_purchase_lab_item_bill_no+1; ?>" max = "<?php echo $invoice_purchase_lab_item_bill_no+1; ?>" name = "invoice_purchase_lab_item_bill_no" id = "invoice_purchase_lab_item_bill_no" class = "form-control" required />
        	                </div>
        	                <div class = "col">
        	                    <label for = "invoice_purchase_lab_item_date">PURCHASE DATE</label>
        	                        <input type = "date" name = "invoice_purchase_lab_item_date" value = "<?php echo date("Y-m-d"); ?>" id = "invoice_purchase_lab_item_date" class = "form-control" required />
        	                </div>
        	                <div class = "col">
        	                    <label for = "invoice_party_bill_no">PARTY BILL #</label>
        	                        <input type = "number" name = "invoice_party_bill_no" id = "invoice_party_bill_no" class = "form-control" required />
        	                </div>
        	                <div class = "col">
        	                    <label for = "invoice_party_id">SELECT PARTY</label>
                                <select class = "form-control" name = "invoice_party_id" id = "invoice_party_id" required>
                                    <?php
                                    $parties = "SELECT id, name, phone, address FROM `parties` WHERE `status` = '1' ";
                                    $run_parties = mysqli_query($con, $parties);
                                    if(mysqli_num_rows($run_parties) > 0)
                                    {
	                                    while($row_parties = mysqli_fetch_array($run_parties))
	                                    { ?>
	                                        <option value = "<?php echo $row_parties['id']; ?>"><?php echo $row_parties['name']; ?></option>
	                                    <?php }
                                    }
                                    ?>
                                </select>
        	                </div>
        	                <div class = "col p-4 g-3">
        	                    <input type = "submit" value = "+ new bill" name = "add_purchase_lab_item" class = "btn btn-sm btn-primary"/>
        	                    <input type = "reset" value = "- clear form" name = "invoice_purchase_lab_item" class = "btn btn-sm btn-warning"/>
        	                </div>
        	            </div>
	                </form>
    	        </div>
    	        <div class = "col-sm-12">
    	            <table class = "table table-sm table-bordered">
    	                <caption style = "caption-side: top; color: black;">
    	                    <h2 align = "center">PURCHASE LAB ITEMS</h2>
    	                    <div class = "row">
    	                        <div class = "col">DATE : <strong><?php echo date_format(date_create($invoice_purchase_lab_item_date),"d-M-Y"); ?></strong></div>
    	                        <div class = "col">BILL # <strong><?php echo $invoice_purchase_lab_item_bill_no; ?></strong></div>
    	                        <div class = "col">PARTY BILL # <strong><?php echo $invoice_party_bill_no; ?></strong></div>
    	                        <div class = "col">PARTY NAME : <strong><?php echo $invoice_party_name; ?></strong></div>
    	                    </div>
    	                </caption>
    	                <thead>
    	                    <tr class = "text-center">
    	                        <th>S #</th>
    	                        <th>BILL #</th>
    	                        <th>DATE</th>
    	                        <th>SELECT PURCHASE ITEM</th>
    	                        <th>RATE</th>
    	                        <th>QUANTITY</th>
    	                        <th>AMOUNT</th>
    	                        <th>LAB MANAGER</th>
    	                        <th class = "nodisplay_print" colspan =  "2">ACTION</th>
    	                    </tr>
    	                    <form method = "POST">
    	                        <tr class = "nodisplay_print">
    	                            <td colspan = "2">
    	                                <input type = "number" readonly value = "<?php echo $invoice_purchase_lab_item_bill_no; ?>" class = "form-control" required/>
    	                                <input type = "hidden" name = "invoice_purchase_lab_item_bill_no" value = "<?php echo $invoice_purchase_lab_item_bill_no; ?>" class = "form-control" required/>
	                                </td>
    	                            <td>
    	                                <input type = "date" readonly class = "form-control" value = "<?php echo $invoice_purchase_lab_item_date; ?>"required /></td>
    	                                <input type = "hidden" name = "purchase_lab_item_date" id = "purchase_lab_item_date" class = "form-control" value = "<?php echo $invoice_purchase_lab_item_date; ?>"required /></td>
    	                            <td>
    	                                <select class = "form-control" name = "item_id" id = "item_id" required>
    	                                    <option value = "">SELECT ITEM</option>
    	                                    <?php
    	                                    $items = "SELECT items.id, items.name AS item_name, categories.name AS category_name FROM `items` INNER JOIN categories ON items.category_id = categories.id WHERE items.category_id IN (7, 28, 43) AND items.status = '1' ";
    	                                    $run_items = mysqli_query($con, $items);
    	                                    if(mysqli_num_rows($run_items) > 0)
    	                                    {
        	                                    while($row_items = mysqli_fetch_array($run_items))
        	                                    { ?>
        	                                        <option value = "<?php echo $row_items['id']; ?>"><?php echo $row_items['item_name'].'('.$row_items['category_name'].')'; ?></option>
        	                                    <?php }
    	                                    }
    	                                    ?>
    	                                </select>
    	                            </td>
    	                            <td><input step = "0.00001" type = "number" name = "purchase_lab_item_rate" id = "purchase_lab_item_rate" class = "form-control" required oninput = "myChangeFunction()"/></td>
    	                            <td><input step = "0.00001" type = "number" min = "1" name = "purchase_lab_item_quantity" id = "purchase_lab_item_quantity" class = "form-control" required oninput = "myChangeFunction()"/></td>
    	                            <td><input step = "0.00001" type = "number" name = "purchase_lab_item_price" id = "purchase_lab_item_price" class = "form-control" required oninput = "myChangeFunctionRate()"/></td>
    	                            <td><input type = "text" name = "purchase_lab_item_created_by" id = "purchase_lab_item_created_by" class = "form-control" value = "<?php echo $lab_manager_user_name; ?>" /></td>
    	                            <td class = "nodisplay_print"><input type = "submit" value = "ADD" name = "save_purchase_lab_item" class = "btn btn-sm btn-primary"/></td>
    	                            <td class = "nodisplay_print"><input type = "reset" value = "CLEAR" name = "clear_purchase_lab_item" class = "btn btn-sm btn-warning" /></td>
    	                        </tr>
    	                    </form>
    	                </thead>
    	                <tbody>
    	                    <?php
    	                    $s = 0;
    	                    $total = 0;
    	                    $select = "SELECT  purchase_lab_items.purchase_lab_item_id, invoice_purchase_lab_items.invoice_party_bill_no, purchase_lab_items.invoice_purchase_lab_item_bill_no, purchase_lab_items.purchase_lab_item_date, items.name AS item_name, parties.name AS party_name, `purchase_lab_item_rate`, `purchase_lab_item_quantity`, `purchase_lab_item_price`, users.u_name, purchase_lab_items.purchase_lab_item_status FROM `purchase_lab_items` INNER JOIN invoice_purchase_lab_items ON purchase_lab_items.invoice_purchase_lab_item_bill_no = invoice_purchase_lab_items.invoice_purchase_lab_item_bill_no INNER JOIN parties ON invoice_purchase_lab_items.invoice_party_id = parties.id INNER JOIN items ON purchase_lab_items.item_id = items.id INNER JOIN users ON purchase_lab_items.purchase_lab_item_created_by = users.id WHERE purchase_lab_items.invoice_purchase_lab_item_bill_no = '$invoice_purchase_lab_item_bill_no' AND purchase_lab_items.purchase_lab_item_status > 0 ";
    	                    $run = mysqli_query($con, $select);
    	                    if(mysqli_num_rows($run) > 0)
    	                    {
    	                        while($row = mysqli_fetch_array($run))
    	                        {
    	                            $s++; ?>
    	                            <tr>
    	                                <td><?php echo $s; ?></td>
    	                                <td><?php echo $row['invoice_purchase_lab_item_bill_no']; ?></td>
    	                                <td><?php echo $row['purchase_lab_item_date']; ?></td>
    	                                <td><?php echo $row['item_name']; ?></td>
    	                                <td><?php echo $row['purchase_lab_item_rate']; ?></td>
    	                                <td><?php echo $row['purchase_lab_item_quantity']; ?></td>
    	                                <td><?php echo $row['purchase_lab_item_price']; ?></td>
    	                                <td><?php echo $row['u_name']; ?></td>
    	                                <td colspan = "2" class = "nodisplay_print">
    	                                    <a href = "purchase_lab_items.php?delete=<?php echo $row['purchase_lab_item_id']; ?>" class = "badge badge-danger">X</a>
    	                                    <?php if($row['purchase_lab_item_status'] == '0'){echo '<div class = "badge badge-info">NOT RECEIVED</div>';}else{echo '<div class = "badge badge-success">NOT RECEIVED</div>';} ?></td>
    	                            </tr>
    	                        <?php
    	                        $total = $total + $row['purchase_lab_item_price'];
    	                        }
    	                    }
    	                    ?>
    	                </tbody>
    	                <tfoot>
    	                    <tr>
    	                        <th></th>
    	                        <th></th>
    	                        <th></th>
    	                        <th></th>
    	                        <th></th>
    	                        <th></th>
    	                        <th><?php echo $total; ?></th>
    	                    </tr>
    	                </tfoot>
    	            </table>
    	        </div>
    	    </div>
    	</div>
    </div>
</body>
</html>
<script type="text/javascript">
function myChangeFunction() 
{
    var purchase_lab_item_quantity = document.getElementById('purchase_lab_item_quantity');
    var purchase_lab_item_rate = document.getElementById('purchase_lab_item_rate');
    var purchase_lab_item_price = document.getElementById('purchase_lab_item_price');
    if(purchase_lab_item_quantity.value > 0 && purchase_lab_item_rate.value > 0)
    {
        purchase_lab_item_price.value = purchase_lab_item_quantity.value*purchase_lab_item_rate.value;
    }
    else
    {
        purchase_lab_item_price.value = 0;
    }
}    
  
function myChangeFunctionRate() 
{
    // purchase_lab_item_rate
    var purchase_lab_item_quantity = document.getElementById('purchase_lab_item_quantity');
    var purchase_lab_item_rate = document.getElementById('purchase_lab_item_rate');
    var purchase_lab_item_price = document.getElementById('purchase_lab_item_price');
    if(purchase_lab_item_quantity.value > 0 && purchase_lab_item_price.value > 0)
    {
        x = purchase_lab_item_price.value/purchase_lab_item_quantity.value;
        purchase_lab_item_rate.value = x.toPrecision(6);
    }
    else
    {
        purchase_lab_item_rate.value = 0;
    }
}    
</script>