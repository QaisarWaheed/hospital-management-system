<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
if(isset($_POST['item_id']) && isset($_POST['from']) && $_POST['item_id'] != '')
{
    $item_id = $_POST['item_id'];
    $br_id = $_POST['br_id'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $end_date = date_format(date_create($to), "Y-m-d 23:59:59");
}
?>
	<title>PURCHASE DETAIL FROM <?php echo $_POST['from']; ?> TO <?php echo $_POST['to']; ?> - <?php echo $company_trademark; ?></title>
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
	<div class="col-md-12 background_whitesmoke noprint">
        <a href = "dashboard.php">Dashboard</a>
		<?php include 'top_row.php'; ?>
	</div>
	<div class="col-md-12">
    	<div style="" class = "noprint">
    	    <?php ?>
    	    <form METHOD="POST">
    	        <div class="row">
    	            <div class="col-md-4">
    	                <h5 align="center">SELECT ITEM</h5>
    	            </div>
    	            <div class="col-md-3">
    	                <h5 align="center">FROM</h5>
    	            </div>
    	            <div class="col-md-3">
    	                <h5 align="center">TO</h5>
    	            </div>
    	            <div class="col-md-2">
    	                <h5 align="center">ACTION</h5>
    	            </div>
    	            <div class="col-md-4">
                        <select class="form-control" size = "1" style="min-width: 200px;text-transform: uppercase;" name="item_id">
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
    	            <div class="col-md-3">
    	                <input type="date" value = "<?php echo $from; ?>" name="from" class="form-control" required />
    	            </div>
    	            <div class="col-md-3">
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
    $item_id = $_POST['item_id'];
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
                        <th>INVOICE</th>
                        <th>Date</th>
                        <th>PARTY</th>
                        <th>BATCH</th>
                        <th>MFG</th>
                        <th>EXPIRY</th>
                        <th>PRICE</th>
                        <th>QTY</th>
                        <th>TOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $s = 0;
                    $total = 0;
                    $select_token = "SELECT purchase_items.`created`,purchase_items.invoice_no,purchase_items.`mfg_date`, purchase_items.expiry_date,purchase_items.batch_no,purchase_items.quantity, purchase_items.per_item_price, purchase_items.total_price, parties.name FROM `purchase_items` INNER JOIN parties ON purchase_items.party_id = parties.id WHERE purchase_items.`item_id` = '$item_id' AND purchase_items.created < '$end_date' AND purchase_items.created > '$from' ORDER BY purchase_items.`created` ASC";
                    $run_token = mysqli_query($con, $select_token);
                    if(mysqli_num_rows($run_token) > 0)
                    {
                    while($row_token = mysqli_fetch_array($run_token))
                    {
                        $total = $total + $row_token['quantity'];
                        $s++;
                    echo '
                    <tr>
                        <td>'.$s.'</td>
                        <td>'.$row_token['invoice_no'].'</td>
                        <td>'.date_format(date_create($row_token['created']), "d-M-Y").'</td>
                        <td>'.$row_token['name'].'</td>
                        <td>'.$row_token['batch_no'].'</td>
                        <td>'.date_format(date_create($row_token['mfg_date']), "d-M-Y").'</td>
                        <td>'.date_format(date_create($row_token['expiry_date']), "d-M-Y").'</td>
                        <td>'.$row_token['per_item_price'].'</td>
                        <td>'.$row_token['quantity'].'</td>
                        <td>'.$row_token['total_price'].'</td>
                    <tr>
                    ';
                    }
                    }
                    else
                    {
                    echo '
                    <tr>
                        <td colspan = "10">'.$con->error.'</td>
                    <tr>
                    ';
                    }
                    ?>
                    <tr>
                        <th style = "text-align: right;" colspan = "5">Total Purchase Quanitity</th>
                        <th colspan = "5"><?php echo $total; ?></th>
                    </tr>
                </tbody>
                <caption style = "color: black; caption-side: top; text-align: center;">
                    <h2><?php echo get_item_name_and_category_by_item_id($item_id); ?> </h2>
                    <h3>PURCHASE DETAIL FROM <?php echo $_POST['from']; ?> TO <?php echo $_POST['to']; ?> </h3>
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