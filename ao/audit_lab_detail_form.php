<?php include 'includes/connect.php'; 
if (isset($_POST['save'])) 
{
	$br_id = $_POST['br_id'];
	$insert = "INSERT INTO `audit_lab_form`
	(`audit_lab_form_id`, `branch_id`, `audit_officer_id`, `audit_lab_form_status`, `audit_lab_form_created_at`, `audit_lab_form_created_by`)
	VALUES 
	('null', '$br_id','$user_id', '1', '$current_date','$user_id')";
         $run = mysqli_query($con, $insert);
 		if ($run) 
 		{	
 		    $insert = '';
 		    $audit_id = mysqli_insert_id($con);
 		    $select_item = "SELECT * FROM `item_register_to_branches` WHERE `branch_id` = '$br_id' AND item_id IN (SELECT id FROM items WHERE category_id IN (2, 7, 43)) ";
 		    $run = mysqli_query($con, $select_item);
 		    if(mysqli_num_rows($run) > 0)
 		    {
 		        while($row = mysqli_fetch_array($run))
 		        {
 		            $branch_item_id = $row['id'];
 		            $item_id = $row['item_id'];
 		            $item_poor_price = get_poor_price($item_id);
     		        $insert = "INSERT INTO `audit_lab_detail`
     		        (`audit_lab_form_id`, `branch_item_id`, `item_poor_price`, `audit_officer_id`, `audit_lab_detail_created_at`)
                	VALUES 
                	('$audit_id', '$branch_item_id', '$item_poor_price', '$user_id', '$current_date');";
         		    mysqli_query($con, $insert);
 		        }
 		    }
 		?>
 		<script>
     			alert('DATA SAVE SUCCESSFULLY');
 				location.replace("audit_lab_detail_form.php");
		</script>
 		<?php
 		}
         else
         {
             echo $con->error;
         }
 exit(0);
}
$current_audit_id = mysqli_num_rows(mysqli_query($con, "SELECT audit_lab_form_id FROM `audit_lab_form`")) + 1;
$max_audit_id = 0;
$max_audit_run = mysqli_query($con, "SELECT MAX(audit_lab_form_id) AS max_id FROM `audit_lab_form`");
if ($max_audit_run && ($max_row = mysqli_fetch_assoc($max_audit_run))) {
    $max_audit_id = (int) ($max_row['max_id'] ?? 0);
}
if ($max_audit_id < 1) {
    $max_audit_id = $current_audit_id;
}
$search_bill_no = isset($_GET['bill_no']) ? (int) $_GET['bill_no'] : 0;
?>
<?php include 'includes/head.php'; ?>
	<title>AUDIT BRANCH LAB - <?php echo $company_trademark; ?></title>
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
				<label><h1>SELECT BRANCH FOR LAB AUDIT</h1></label>
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
                            <select id="branch" name="br_id" required class="form-control">
                            <?php    
                            $select = "SELECT * FROM `branchs` WHERE `status`= '1' AND `id` = '$branch_id' ";
                            $run = mysqli_query($con, $select);
                            if(mysqli_num_rows($run) > 0)
                            { 
                                while($row = mysqli_fetch_array($run))
                                {  
                                    echo '<option value="'.$row['id'].'">'.$row['address'].'</option>'; 
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
				<label><h1 style = "text-decoration: underline;">LAB AUDITS LIST</h1></label>
				<div class = "table">
				    <table class = "table" border = "1">
				        <thead>
				            <tr class = "noprint">
				                <form method = "GET">
				                    <td colspan = "4"><input required type = "number" min = "1" value = "<?php echo $search_bill_no > 0 ? $search_bill_no : ''; ?>" max = "<?php echo $max_audit_id; ?>" name = "bill_no" class = "form-control" /></td>
				                    <td><input type = "submit" value = "SEARCH" class = "btn btn-sm btn-info" /></td>
				                </form>
				            </tr>
				            <tr style = "font-size: 16px;">
				                <th>Sr #</th>
				                <th>Audit</th>
				                <th>Date </th>
				                <th>Branch </th>
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
    $select = "SELECT * FROM `audit_lab_form` WHERE audit_lab_form_id = '$bill_no' ";
}
else
{
    $select = "SELECT * FROM `audit_lab_form` WHERE audit_lab_form_status = '1' AND `branch_id` = '$branch_id' ";
    
}
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $id = $row['audit_lab_form_id'];
        $audit_officer_id = $row['audit_officer_id'];
        $count_items = mysqli_num_rows(mysqli_query($con, "SELECT audit_lab_detail_id FROM `audit_lab_detail` WHERE `audit_lab_form_id` = '$id' "));
        $count_remaining_items = mysqli_num_rows(mysqli_query($con, "SELECT audit_lab_detail_id FROM `audit_lab_detail` WHERE `audit_lab_form_id` = '$id' AND audit_lab_detail_tries != 0 "));
        $audit_officer_name = get_uname_by_id($audit_officer_id);
        $br_id = $row['branch_id'];
        $tag_name = get_branch_name_by($br_id);
        $created = $row['audit_lab_form_created_at'];
        echo '				            
        <tr  style = "font-size: 15px;">
            <td style = "text-align: center;">'.$s.'</td>
            <td style = "text-align: center;">'.$id.'</td>
            <td style = "text-align: center">'.date_format(date_create($created), "d-m-Y").'</td>
            <td style = "text-align: left">'.$tag_name.'</td>
            <td style = "text-align: center">
            '.$count_remaining_items.'
            <progress style = "width: 50px;height:10px;" id="file" value="'.$count_remaining_items.'" max="'.$count_items.'">  </progress>
            '.$count_items.'</td>
            <td style = "text-align: center">'.$audit_officer_name.'</td>';
        if($count_remaining_items != $count_items)
        {
            if($branch_id == $br_id)
            {
            echo '<td style = "text-align: center;"><a href = "audit_lab_form.php?audit_id='.$id.'&br_id='.$br_id.'" class = "btn btn-sm btn-outline-info">Open Audit Form</a></td>
                  <td></td>';
            }
        }
        else
        {
            echo '<td class = "bg-info">COMPLETE AUDIT</td>
                    <td>
                        <a href = "audit_lab_difference_report.php?audit_id='.$id.'&br_id='.$br_id.'" class = "btn btn-sm btn-primary">Difference</a>
                        <a href = "audit_lab_extra_report.php?audit_id='.$id.'&br_id='.$br_id.'" class = "btn btn-sm btn-info">Extra Stock</a>
                        <a href = "audit_lab_short_report.php?audit_id='.$id.'&br_id='.$br_id.'" class = "btn btn-sm btn-warning">Short Stock</a>
                        <a href = "audit_lab_complete_report.php?audit_id='.$id.'&br_id='.$br_id.'" class = "btn btn-sm btn-success">Complete</a>
                    </td>';   
        }
        echo '</tr>';

                // <a href="add_party.php?u_id='.$id.'" class = "btn btn-sm btn-primary">update</a>
    }
}
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