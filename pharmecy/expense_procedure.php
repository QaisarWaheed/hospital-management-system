<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
if($user_id == 1 || ($user_id >= 150 && $user_id <= 159) )
{
}
else
{
    header('location: logout.php');
}
if(isset($_POST['add_procedure_expense']) && isset($_POST['token_no']) && $_POST['token_no'] != '')
{
    $token_no = $_POST['token_no'];
    $expense_amount = $_POST['expense_amount'];
    $expense_detail = $_POST['expense_detail'];
    $expense_to = $_POST['expense_to'];
    $expense_by = $_POST['expense_by'];
    $expense_by = $_POST['expense_by'];
    $expense_added_by = $user_id;
    $insert = "INSERT INTO `expense_procedure`
    (`expense_id`, `token_no`, `expense_amount`, `expense_detail`, `expense_to`, `expense_by`, `expense_added_by`, `expense_status`, `expense_created`)
    VALUES
    (NULL, '$token_no', '$expense_amount', '$expense_detail', '$expense_to', '$expense_by', '$expense_added_by', '1', '$current_date')";
    if(mysqli_query($con, $insert))
    {
        $added_at = mysqli_insert_id($con);
        ?>
        <script>
            alert("EXPENSE DATA ADDED SUCCESSFULLY...");
            location.replace("expense_procedure.php");
        </script>
    <?php }
    else
    {
        $error =  $con->error;
        ?>
        <script>
            alert("<?php echo $error; ?>");
            location.replace("expense_procedure.php");
        </script>
    <?php }
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
    	<div style="">
    	    <?php ?>
    	    <form METHOD="POST">
    	        <div class="row">
    	            <div class="col-md-12">
    	                <h3 align="center">SELECT PROCEDURE TOKEN NO FOR EXPENSE</h3>
    	            </div>
    	            <div class="col-md-10">
    	                <select name = "token_no" class = "form-control bg-into" required>
    	                    <?php
    	                    $select_token = "SELECT * FROM tokans WHERE `id` NOT IN (SELECT `token_no` FROM `expense_procedure`) AND status = '1' AND branch_id = '$branch_id' AND id IN (SELECT `tokan_no` FROM `item_by_doctor` WHERE `item_id` IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN (SELECT id FROM items WHERE category_id IN (3)) )) ";
    	                    $run_token = mysqli_query($con, $select_token);
    	                    if(mysqli_num_rows($run_token) > 0)
    	                    {
    	                        while($row_token = mysqli_fetch_array($run_token))
    	                        {
    	                            $token_no = $row_token['id'];
        	                        echo '<option value = "'.$token_no.'">'.$token_no.'</option>';
    	                        }
    	                    }
    	                    else
    	                    {
    	                        echo '<option value = ""> NO TOKEN</option>';
    	                    }
    	                    ?>
    	                </select>
    	            </div>
    	            <div class="col-md-2">
    	                <input type="submit" value="SEARCH" name="token" class="btn btn-info btn-sm" />
    	            </div>
    	            <div class = "col-md-12">
    	                <?php
    	                if(!isset($_POST['token_no']))
    	                {
    	                    $s = 0;
    	                    $total_amount = 0;
    	                    $select = "SELECT * FROM `expense_procedure` WHERE `expense_status` = '1' ";
    	                    $run = mysqli_query($con, $select);
    	                    if(mysqli_num_rows($run) > 0)
    	                    { ?>
    	                        <table class = "table table-bordered table-hover">
    	                            <caption style = "caption-side: top;">
    	                                <h3 align = "center" class = "text-danger bg-info">ALL EXPENSE DETAIL</h3> 
    	                            </caption>
    	                            <thead>
    	                                <tr>
    	                                    <th>Sr. No</th>
    	                                    <th>Token No</th>
    	                                    <th>Expense Given By</th>
    	                                    <th>Expense Given To</th>
    	                                    <th>Expense Amount</th>
    	                                </tr>
    	                            </thead>
    	                            <tbody>
    	                    <?php 
    	                        while($row = mysqli_fetch_array($run))
    	                        {
    	                            $s = $s + 1;
    	                            $total_amount = $total_amount + $row['expense_amount'];
    	                        ?>
    	                                <tr>
    	                                    <td><?php echo $s; ?></td>
    	                                    <td><?php echo $row['token_no']; ?></td>
    	                                    <td><?php echo $row['expense_by']; ?></td>
    	                                    <td><?php echo $row['expense_to']; ?></td>
    	                                    <td><?php echo number_format((float)($row['expense_amount'] ?? 0)); ?></td>
    	                                </tr>
    	                        <?php 
    	                        }
    	                        echo '</tbody>
                                      <tfoot>
                                        <tr>
                                            <th colspan = "4"></th>
                                            <th>'.number_format((float)($total_amount ?? 0)).'</th>
                                        </tr>
                                      </tfoot>
    	                        </table>';
    	                    }
    	                }
    	                ?>
    	            </div>
    	        </div>
    	    </form>
    	</div>
<?php 
if(isset($_POST['token']) && isset($_POST['token_no']) && $_POST['token_no'] != '')
{
    $token_no = $_POST['token_no'];
?>
<form method="POST">
    <div class="row"> 
    <?php
    $check_expense = "SELECT * FROM `expense_procedure` WHERE `token_no` =   '$token_no' ";
    $run_check_expense = mysqli_query($con, $check_expense);
    $check = "SELECT * FROM `item_by_doctor` WHERE `tokan_no` =  '$token_no' ";
    $run_check = mysqli_query($con, $check);
    if(mysqli_num_rows($run_check) > 0)
    {
    ?>
        <div class="col-md-12">
            <label>Token No: <?php echo $token_no; ?></label>
        	<fieldset class="border p-2">
            	<legend style="font-size: 14px;" class="w-auto">SELECT TEST OR MEDICINE OR PROCEDURE</legend>
            	
               	<div class="col-md-12">
               		<select readonly id="mySelect" ondblclick="del_medicine();" class="form-control" size="3">
               			<?php echo return_medicine_selected_by_token($token_no); ?>
               		</select>
               	</div>
               	
            </fieldset>
        	<fieldset class="border p-2">
            	<legend style="font-size: 14px;" class="w-auto">SELECT DETAIL FOR EXPENSE</legend>
            	<div class = "row">
               	<div class="col-md-6">
               	    <label for = "expense_amount">EXPENSE AMOUNT</label>
               	    <input required type = "number" name = "expense_amount" id = "expense_amount" class = "form-control" />
               	</div>
               	<div class="col-md-6">
               	    <label for = "expense_detail">EXPENSE DETAIL</label>
               	    <input required type = "text" name = "expense_detail" id = "expense_detail" class = "form-control" />
               	</div>
               	<div class="col-md-6">
               	    <label for = "expense_by">EXPENSE BY</label>
               	    <input required type = "text" name = "expense_by" id = "expense_by" class = "form-control" />
               	</div>
               	<div class="col-md-6">
               	    <label for = "expense_to">EXPENSE TO</label>
               	    <input required type = "text" name = "expense_to" id = "expense_to" class = "form-control" />
               	</div>
               	<div class="col-md-6">
               	    <label for = "expense_added_by">EXPENSE ADDED BY</label>
               	    <input readonly type = "text" name = "expense_added_by" value = "<?php echo $user_name; ?>" id = "expense_added_by" class = "form-control" />
               	</div>
               	</div>
               	
            </fieldset>
        </div>
    <?php }
    else
    {
        echo '
        <div class="col-md-12">
        	<fieldset class="border p-2">
            	<legend style="font-size: 14px;" class="w-auto">SELECTED TOKEN IS CHECKUP / OPD TOKEN</legend>
            	<label>'.$token_no.'</label>
            </fieldset>
        </div>        
        ';
    }
    if(mysqli_num_rows($run_check_expense) == 0)
    {
    ?>
        <div class="col-md-12" style="padding:0% 40% 0% 40%">
            <input type="hidden" value="<?php echo $token_no; ?>" name="token_no" />
            <?php 
                echo '<input type="submit" value="ADD PROCEDURE EXPENSE" class="btn btn-danger" name="add_procedure_expense" />';
            ?>
        </div>
<?php    
    } ?>
    </div>
</form>
<?php
}
?>
	</div>
</div>

</body>
</html>