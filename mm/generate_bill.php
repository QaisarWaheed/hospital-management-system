<?php include 'includes/connect.php'; 
$current_issue_no = mysqli_num_rows(mysqli_query($con, "SELECT DISTINCT issue_id FROM `item_register_branchs_by_sm`"));
?>
<?php include 'includes/head.php'; ?>
	<title>Add Item - <?php echo $company_trademark; ?></title>
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

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

		<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>

			<div class="col-md-12 noprint" style="text-align: center;">
				<label><h1>Bill Branch Issue Items</h1></label>
			</div>

			<div class="col-md-12" style="text-align: center;">
				<label><h1 style = "text-decoration: underline;">Item Issue LIST</h1></label>
				<div class = "table">
				    <table class = "table" border = "1">
				        <thead>
				            <tr class = "noprint">
				                <form method = "GET">
				                    <td colspan = "4"><input required type = "number" min = "1" value = "<?php echo ($_GET['bill_no']>0 ? $_GET['bill_no'] : $current_purchase_no); ?>" max = "<?php echo $current_purchase_no; ?>" name = "bill_no" class = "form-control" /></td>
				                    <td><input type = "submit" value = "SEARCH" class = "btn btn-sm btn-info" /></td>
				                </form>
				            </tr>
				            <tr style = "font-size: 16px;">
				                <th>Sr #</th>
				                <th>Item Name </th>
				                <th>Category </th>
				                <th>Price </th>
				                <th>QTY</th>
				                <th>Amount </th>
				                <th class = "noprint">Status</th>
				                <th class = "noprint">Action</th>
				            </tr>
				        </thead>
				        <tboby>
<?php
$s = 0;
$total_amount = 0;
if(isset($_GET['bill_no']) && $_GET['bill_no'] != '')
{
    $bill_no = $_GET['bill_no'];
    $select = "SELECT * FROM `item_register_branchs_by_sm` WHERE issue_id = '$bill_no' ";
}
else
{
    $select = "SELECT * FROM `item_register_branchs_by_sm` WHERE issue_id = '$current_issue_no' ";
    
}
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $id = $row['id'];
        $status = $row['status'];
        if($status == 1){$status_msg = "Issued";}else{$status_msg = "RECEIVED";}
        $quantity = $row['quantity'];
        $difference = $row['difference'];
        $branch_item_id = $row['branch_item_id'];
        $branch_id = $row['branch_id'];
        $created = $row['created'];
        $item_name = get_item_name_by_register_item_id($branch_item_id);
        // $price = show_price_by_register_branch_id($branch_item_id);
        $price = show_purchase_price_by_item_id($branch_item_id);
        $amount = $price*($quantity+$difference);
        $total_amount = $total_amount + $amount;
        $category_name = show_category_name_by_register_branch_id($branch_item_id);
        echo '				            
        <tr  style = "font-size: 15px;">
            <td style = "text-align: center;">'.$s.'</td>
            <td style = "text-align: left">'.$item_name.'</td>
            <td style = "text-align: center">'.$category_name.'</td>
            <td style = "text-align: right;">'.number_format((float)($price ?? 0), 2).'</td>
            <td style = "text-align: right;">'.intval($quantity+$difference).'</td>
            <td style = "text-align: right;">'.number_format((float)($amount ?? 0), 2).'</td>
            <td class = "noprint">
                '.$status_msg.'
            </td>';
        echo '<td class = "noprint">OK</td>';
        echo '</tr>
';
    }
}
    $tag_name = get_branch_name_by($branch_id);
    $receiver_name = get_uname_by_issue_id($bill_no);
?>
    <tr style = "text-align: right;">
        <th colspan = "5">GRAND TOTAL</th>
        <th><?php echo number_format((float)($total_amount ?? 0),2); ?></th>
    </tr>
    <caption style = "caption-side: top;">
        <div style = "font-size:15px;">BRANCH NAME: <?php echo $tag_name; ?></div>
        <div style = "font-size:15px;">Issue No:  <?php echo $bill_no; ?></div>
        <div style = "font-size:15px;">Issue Date: <?php echo date_format(date_create($created), "d-m-Y"); ?></div>
        <div style = "font-size:15px;">RECEIVED BY:  <?php echo $receiver_name; ?></div>
    </caption>    
        </tboby>
    </table>
				</div>
			</div>


		</div>

	</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>