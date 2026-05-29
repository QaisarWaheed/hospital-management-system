<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) 
{
    $audit_id = $_POST['audit_id'];
    $record_id = $_POST['record_id'];
    $branch_item_id = $_POST['branch_item_id'];
    $quantity = $_POST['quantity'];
    $update = "UPDATE `audit_branch_detail` SET `clear_status` = '2', `clear_by` = '$user_id' WHERE `id` = '$record_id' AND `clear_status` = '1' ";
    $update_branch = "UPDATE `item_register_to_branches` SET `quantity` = `quantity`+'$quantity' WHERE `id` = '$branch_item_id' ";
    if(mysqli_query($con, $update))
    {
        mysqli_query($con, $update_branch);
        header('location: add_audit_extra_item_purchase.php?audit_no='.$audit_id);
        exit(0);
    }
    header('location: add_audit_extra_item_purchase.php?audit_no='.$audit_id);
}
if(isset($_GET['select_audit_id']))
{
    $current_audit_id = $_GET['select_audit_id'];
}
elseif(isset($_POST['select_audit_id']))
{
    $current_audit_id = $_POST['select_audit_id'];
}
else
{
    $current_audit_id = mysqli_num_rows(mysqli_query($con, "SELECT DISTINCT `id` FROM `audit_branch_form`"));
}
include 'includes/head.php'; 
?>
	<title>Add Audit extra Item Purchase - <?php echo $company_trademark; ?></title>
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
<div id="loadingSpinner" style="display: none;">
    <div class = "container">
        <div class = "row p-5 g-5">
            <div class = "col text-center">
                <div aria-busy="true" aria-describedby="progress-bar">
                    <h2>LOADING...</h2>
                    <p>Please Wait Untill Processing Completed.</p>
                    <p>Data Processing...</p>
                </div>
                <progress id="progress-bar" aria-label="Content loading…"></progress>    
                
            </div>
        </div>        
    </div>
</div>
<div id = "submitBody">
	<div class="" style="margin: 10px 15px;">
		<div class="row">
			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>
			<div class="col-md-12 noprint" style="text-align: center;">
			</div>
			<div class="col-md-12" style="text-align: center;">
				<div class = "table">
				    <table class = "table" border = "solid">
				        <thead>
				            <tr class = "noprint">
				                <form method = "GET">
				                    <td colspan = "2"><h3>AUDIT NO</h3></td>
				                    <td colspan = "5"><input required type = "number" min = "1" value = "<?php echo ($_GET['audit_no']>0 ? $_GET['audit_no'] : $current_audit_id); ?>" max = "<?php echo $current_audit_no; ?>" name = "audit_no" class = "form-control" /></td>
				                    <td><input type = "submit" value = "SEARCH" class = "btn btn-sm btn-info" /></td>
				                </form>
				            </tr>
				            <tr>
				                <th>#</th>
				                <th>Audit No</th>
				                <th>Item </th>
				                <th>Category </th>
				                <th>RATE</th>
				                <th>QTY</th>
				                <th>AMOUNT</th>
				                <th class = "noprint">Action</th>
				            </tr>
				        </thead>
				        <tboby>
<?php
$s = 0;
$total_amount = 0;
if(isset($_GET['audit_no']) && $_GET['audit_no'] != '')
{
    $audit_no = $_GET['audit_no'];
    $select = "SELECT * FROM audit_branch_detail WHERE audit_id = '$audit_no' AND computer_quantity < manual_quantity ";
}
else
{
    $audit_no = $current_audit_id;
    $select = "SELECT * FROM audit_branch_detail WHERE audit_id = '$current_audit_id' AND computer_quantity < manual_quantity ";
    
}

$data = "SELECT branchs.name , branchs.tag_name FROM branchs WHERE id IN (SELECT branch_id FROM `audit_branch_form` WHERE `id` = '$audit_no') ";
$run_data = mysqli_query($con, $data);
if(mysqli_num_rows($run_data) > 0)
{
    while($row_data = mysqli_fetch_array($run_data))
    {
        $audit_branch_name = $row_data['tag_name'].' - '.$row_data['name'];
    }
}
else
{
    $audit_branch_name = '';
}

$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $id = $row['id'];
        $audit_id = $row['audit_id'];
        $status = $row['status'];
        $clear_status = $row['clear_status'];
        $branch_item_id = $row['branch_item_id'];
        $item_name = show_item_name_branch_register_item_id($branch_item_id);
        $category_name = show_category_name_by_branch_register_item_id($branch_item_id);
        $price = $row['item_poor_price'];
        $quantity = $row['manual_quantity'] - $row['computer_quantity'];
        $amount = $price * $quantity;
        $total_amount = $total_amount + $amount;
        echo '				            
        <tr>
            <td>'.$s.'</td>
            <td>'.$row['audit_id'].'</td>
            <td style = "text-align: left">'.$item_name.'</td>
            <td style = "text-align: left">'.$category_name.'</td>
            <td style = "text-align: right;">'.number_format((float)($price ?? 0), 2).'</td>
            <td style = "text-align: right;">'.$quantity.'</td>
            <th style = "text-align: right;">'.number_format((float)($amount ?? 0), 2).'</th>';
        if($clear_status == 1)
        {
            echo '<td class="noprint"><a href = "add_audit_extra_item_purchase.php?save='.$id.'&audit_id='.$audit_id.'" class = "btn btn-sm btn-warning">
                <form method = "POST" onsubmit="showProgress(); return true;">
                    <input type = "hidden" name = "audit_id" value = "'.$audit_id.'" />
                    <input type = "hidden" name = "record_id" value = "'.$id.'" />
                    <input type = "hidden" name = "branch_item_id" value = "'.$branch_item_id.'" />
                    <input type = "hidden" name = "quantity" value = "'.$quantity.'" />
                    <input type = "submit" class = "btn btn-warning btn-sm" name = "save" value = "CLEAR" />
                </form>
            </td>';
        }
        else
        {
            echo '<td class="noprint"></td>';
        }
  echo '</tr>
';
    }
}
?>
        <tr>
            <th colspan = "6"></th>
            <th style = "text-align: right;"><?php echo number_format((float)($total_amount ?? 0), 2); ?></th>
            <th></th>
        </tr>
				        </tboby>
				        <caption style = "caption-side: top; color: black;text-align: center;">
				            <h2><?php echo $audit_branch_name; ?></h2>
			            	<h3>Audit extra Item List</h3>
				        </caption>
				    </table>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script>
function showProgress() {
  document.getElementById('submitBody').style.display = 'none';
//   document.getElementById('submitButton').style.display = 'none';
  document.getElementById('loadingSpinner').style.display = 'block';
}    
</script>
<?php mysqli_close($con); ?>