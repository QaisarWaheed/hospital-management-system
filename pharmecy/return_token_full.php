<?php include 'includes/connect.php';
if($user_id != 1 && !($user_id >= 150 && $user_id <= 159))
{
    header('Location: logout.php');
    exit;
}
if(isset($_POST['return_token']) && isset($_POST['token_no']) && $_POST['token_no'] != '')
{
    $token_no = $_POST['token_no'];
    $retuen_token_reason = $_POST['retuen_token_reason'];
    $return_token_recomended_by = $_POST['return_token_recomended_by'];
    $return_token_recomended_date = $_POST['return_token_recomended_date'];
    mysqli_query($con, "INSERT INTO `return_tokens`(`token_no`, `return_by`, `created`, `retuen_token_reason`, `return_token_recomended_by`, `return_token_recomended_date`) VALUES ('$token_no', '$user_id', '$current_date', '$retuen_token_reason', '$return_token_recomended_by', '$return_token_recomended_date')");
    $query = "UPDATE `tokans` SET `status`= '3' WHERE `id` = '$token_no' AND branch_id = '$branch_id' AND `status`= '1' ";
    if(mysqli_query($con, $query))
    {
        $select = "SELECT * FROM item_by_doctor WHERE `tokan_no` = '$token_no' ";
        $run = mysqli_query($con, $select);
        if(mysqli_num_rows($run) > 0)
        {
            while($row = mysqli_fetch_array($run))
            {
                $dose = $row['dose'];
                $days = $row['days'];
                $feed = $row['feed'];
                $fix_dose = $row['fix_dose'];
                if($fix_dose == 0)
                {
                    $quantity = $dose * $days * $feed;
                }
                else
                {
                    $quantity = $fix_dose;
                }
                $item_id = $row['item_id'];
                $branch_item_quantity = get_branch_item_quantity_from_item_id($item_id);
                $update_quantity = $branch_item_quantity - $quantity;
                $update = "UPDATE `item_register_to_branches` SET `quantity` = '$update_quantity' WHERE `id` = '$item_id' ";
                mysqli_query($con, $update);
            }
        }  
    }
    header('Location: return_token_full.php?msg=returned');
    exit;
}

include 'includes/head.php';
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
    	                <h3 align="center">ENTER RETURN TOKEN NO</h3>
    	            </div>
    	            <div class="col-md-10">
    	                <input type="number" min="1" max="<?php echo next_tokan_no()-1; ?>" name="token_on" class="form-control" />
    	            </div>
    	            <div class="col-md-2">
    	                <input type="submit" value="SEARCH" name="token" class="btn btn-info btn-sm" />
    	            </div>
    	        </div>
    	    </form>
    	</div>
<?php 
if(isset($_POST['token']) && isset($_POST['token_on']) && $_POST['token_on'] != '')
{
    $token_no = $_POST['token_on'];
?>
<form method="POST">
    <div class="row"> 
    <?php
    $select_data = "SELECT * FROM tokans WHERE id = '$token_no' AND branch_id = '$branch_id' ";
    $data = mysqli_num_rows(mysqli_query($con, $select_data));
    $check_return = "SELECT * FROM `return_tokens` WHERE `token_no` =   '$token_no' ";
    $run_check_return = mysqli_query($con, $check_return);
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
               		<select id="mySelect" ondblclick="del_medicine();" class="form-control" size="12">
               			<?php echo return_medicine_selected_by_token($token_no); ?>
               		</select>
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
    if(mysqli_num_rows($run_check_return) == 0)
    {
    ?>
        <div class = "col-md-12">
            <label for = "return_token_recomended_date"> RETURN TOKEN DATE</label>
            <input type = "date" value = "<?php echo date('Y-m-d'); ?>" name = "return_token_recomended_date" id = "return_token_recomended_date" class = "form-control" required />
        </div>
        <div class = "col-md-12">
            <label for = "retuen_token_reason">REASON TO RETURN TOKEN</label>
            <input type = "text" name = "retuen_token_reason" id = "retuen_token_reason" class = "form-control" required />
        </div>
        <div class = "col-md-12">
            <label for = "return_token_recomended_by">APPROVED BY(MEMBER /DOCTOR / ORGANIZATION STAFF)</label>
            <input type = "text" name = "return_token_recomended_by" id = "return_token_recomended_by" class = "form-control" required />
        </div>
        <div class="col-md-12" style="padding:0% 40% 0% 40%">
            <input type="hidden" value="<?php echo $token_no; ?>" name="token_no" />
            <?php 
            if($data == 1)
            {
                echo '<input type="submit" value="RETURN TOKEN" class="btn btn-danger" name="return_token" />';
            }
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