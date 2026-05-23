<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) 
{
	$br_id = $_POST['br_id'];
	$insert = "INSERT INTO `audit_store_form`
	(`audit_store_form_id`, `user_id`, `audit_store_form_status`, `audit_store_form_created`)
	VALUES 
	('null','$user_id', '1', '$current_date')";
         $run = mysqli_query($con, $insert);
 		if ($run) 
 		{	
 		    $insert = '';
 		    $audit_store_form_id = mysqli_insert_id($con);
 		    $select_item = "SELECT * FROM `items` WHERE status = '1' AND category_id NOT IN (0,2,3,7,8,17,28,29,30,31,32,33,34,35,36,37,38,39,40,41) ";
 		    $run = mysqli_query($con, $select_item);
 		    if(mysqli_num_rows($run) > 0)
 		    {
 		        while($row = mysqli_fetch_array($run))
 		        {
 		            $item_id = $row['id'];
 		            $poor = $row['poor'];
 		            $member = $row['member'];
 		            $general = $row['general'];
     		        $insert = "INSERT INTO `audit_store_detail`
     		        (`audit_store_detail_id`, `audit_store_form_id`, `item_id`, `poor`, `member`, `general`, `computer_stock`, `manual_stock`, `user_id`, `manual_tries`, `audit_store_detail_status`, `audit_store_detail_created`)
                	VALUES 
                	(NULL, '$audit_store_form_id', '$item_id', '$poor', '$member', '$general', '0', '0','$user_id', '0', '1', '$current_date') ";
         		    mysqli_query($con, $insert);
 		        }
 		    }
 		?>
 		<script>
     			alert('DATA SAVE SUCCESSFULLY');
 				location.replace("audit_detail_form_store.php");
		</script>
 		<?php
 		}
         else
         {
             echo $insert;
             echo $con->error;
         }
 exit(0);
}
$current_audit_id = mysqli_num_rows(mysqli_query($con, "SELECT audit_store_form_id FROM `audit_store_form` ")) + 1;
$max_audit_id = 0;
$max_audit_run = mysqli_query($con, "SELECT MAX(audit_store_form_id) AS max_id FROM `audit_store_form`");
if ($max_audit_run && ($max_row = mysqli_fetch_assoc($max_audit_run))) {
    $max_audit_id = (int) ($max_row['max_id'] ?? 0);
}
if ($max_audit_id < 1) {
    $max_audit_id = $current_audit_id;
}
$search_bill_no = isset($_GET['bill_no']) ? (int) $_GET['bill_no'] : 0;
?>
<?php include 'includes/head.php'; ?>
	<title> STORE - <?php echo $company_trademark; ?></title>
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
	    <?php include 'navigation_top.php'; ?>
		<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
				<label><h1>SELECT DETAIL'S FOR AUDIT</h1></label>
			</div>
			<div class="col-md-12 ">
                <form class="noprint" method = "POST">
                    <div class = "row">
                        <div class = "col-md-2" style = "text-align: right;">
                            <label class = "label h3" for = "audit_id">Audit Id</label>
                        </div>
                        <div class = "col-md-2">
                            <input readonly type = "number" id = "audit_id" value = "<?php echo $current_audit_id; ?>" name = "audit_id" class="form-control" />
                        </div>
                        <div class = "col-md-6">
                            <select id="user_id" name="user_id" required class="form-control">
                            <?php    
                            $select = "SELECT * FROM `users` WHERE `status`= '1' AND `id` = '$user_id' ";
                            $run = mysqli_query($con, $select);
                            if(mysqli_num_rows($run) > 0)
                            { 
                                while($row = mysqli_fetch_array($run))
                                {  
                                    echo '<option value="'.$row['id'].'">'.$row['u_name'].'</option>'; 
                                } 
                            } 
                            ?>
                            </select>
                        </div>
                        <div class = "col-md-2">
                            <button type="submit" name="save" class=" btn btn-primary" style = "min-width: 100%;">Submit</button>  
                        </div>
                    </div>
                </form>
			</div>

			<div class="col-md-12" style="text-align: center;">
				<label><h1 style = "text-decoration: underline;">Item Issue LIST</h1></label>
				<div class = "table">
				    <table class = "table" border = "1">
				        <thead>
				            <tr class = "noprint">
				                <form method = "GET">
				                    <td colspan = "4"><input required type = "number" min = "1" value = "<?php echo $search_bill_no > 0 ? $search_bill_no : ''; ?>" max = "<?php echo $max_audit_id; ?>" name = "bill_no" class = "form-control" /></td>
				                    <td><input type = "submit" value = "SEARCH" class = "btn btn-sm btn-info" /></td>
				                </form>
				                <td>
				                    <a href = "check_store_sctock.php" class = "btn btn-info">CHECK STORE STOCK</a>
				                </td>
				            </tr>
				            <tr style = "font-size: 16px;">
				                <th>Sr #</th>
				                <th>Date </th>
				                <th>Items </th>
				                <th>Audit By </th>
				                <th>Status</th>
				                <th class = "noprint">Reports</th>
				            </tr>
				        </thead>
				        <tboby>
<?php
$s = 0;
$total_amount = 0;
if(isset($_GET['bill_no']) && $_GET['bill_no'] != '')
{
    $bill_no = $_GET['bill_no'];
    $select = "SELECT * FROM `audit_store_form` WHERE audit_store_form_id = '$bill_no' ";
}
else
{
    $select = "SELECT * FROM `audit_store_form` WHERE audit_store_form_status = '1' ";
    
}
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $audit_store_form_id = $row['audit_store_form_id'];
        $audit_officer_name = get_uname_by_id($row['user_id']);
        $created = $row['audit_store_form_created'];
        $audit_store_form_status = $row['audit_store_form_status'];        

        $count_items = mysqli_num_rows(mysqli_query($con, "SELECT audit_store_detail_id FROM `audit_store_detail` WHERE `audit_store_form_id` = '$audit_store_form_id' "));
        $count_remaining_items = mysqli_num_rows(mysqli_query($con, "SELECT audit_store_detail_id FROM `audit_store_detail` WHERE `audit_store_form_id` = '$audit_store_form_id' AND manual_tries != 0 "));
        
        if($count_items != $count_remaining_items){$audit_store_form_status_msg = '<span class = "btn btn-sm btn-primary">UNCOMPLETE</span>';}
        elseif($count_items == $count_remaining_items){$audit_store_form_status_msg = '<span class = "btn btn-sm btn-success">COMPLETE</span>';}
        else{$audit_store_form_status_msg = '<span class = "btn btn-sm btn-danger">ERROR';}

        echo '				            
        <tr  style = "font-size: 15px;">
            <td style = "text-align: center;">'.$s.'</td>
            <td style = "text-align: center">'.date_format(date_create($created), "d-m-Y").'</td>
            <td style = "text-align: center">
            '.$count_remaining_items.'
            <progress style = "width: 50px;height:10px;" id="file" value="'.$count_remaining_items.'" max="'.$count_items.'">  </progress>
            '.$count_items.'</td>
            <td style = "text-align: left">'.$audit_officer_name.'</td>
            <td style = "text-align: center">'.$audit_store_form_status_msg.'</td>';
            if($count_remaining_items == $count_items)
            { 
                echo '
                <td>
                    <a href = "store_audit_difference_report.php?audit_store_form_id='.$audit_store_form_id.'" class = "btn btn-sm btn-primary">Difference</a>
                    <a href = "store_audit_extra_report.php?audit_store_form_id='.$audit_store_form_id.'" class = "btn btn-sm btn-info">Extra Stock</a>
                    <a href = "store_audit_short_report.php?audit_store_form_id='.$audit_store_form_id.'" class = "btn btn-sm btn-warning">Short Stock</a>
                    <a href = "store_audit_complete_report.php?audit_store_form_id='.$audit_store_form_id.'" class = "btn btn-sm btn-success">Complete</a>
                </td>'; 
            }
            else
            {
                echo '<td style = "text-align: center"><a href = "audit_store_form.php?audit_store_form_id='.$audit_store_form_id.'" class = "btn btn-sm btn-outline-primary">OPEN AUDIT</a></td>';
            }
        echo '</tr>';
    }
}
    $receiver_name = get_uname_by_issue_id($bill_no);
?>
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
<?php mysqli_close($con); ?>