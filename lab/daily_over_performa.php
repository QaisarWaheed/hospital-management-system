<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
if(isset($_GET['save_over_performa']) && $_GET['save_over_performa'] != '')
{
    $data = $_GET['data'];
    $insert = "INSERT INTO `daily_over_performa_summeries`
    (`daily_over_performa_summery_id`, `branch_id`, `over_shift_id`, `daily_over_performa_summery_status`, `daily_over_performa_summery_created_by`, `daily_over_performa_summery_created_at`, `daily_over_performa_summery_date`, `over_shift_from`) 
    VALUES 
    (NULL, '".$_GET['over_brach_id']."', '".$_GET['over_shift_id']."', '1', '$lab_user_id', '$current_date', '".$_GET['daily_over_performa_summery_date']."', '".$_GET['over_shift_from']."')";
    if(mysqli_query($con, $insert))
    {
        $daily_over_performa_summery_id = mysqli_insert_id($con);
        foreach ($data as $id => $value) 
        {
            $over_product_id = $value;
            
            $row_data_row = 'data_'.$value;
            $row_data = $_GET[$row_data_row];
            foreach($row_data as $row_id_data => $row_data_value)
            {
                $daily_over_performa_status = $row_data_value;
            }            
            
            $row_data_remarks_str = 'data_remarks_'.$value;
            $row_data_remarks = $_GET[$row_data_remarks_str];
            foreach($row_data_remarks as $row_id_data_remarks => $row_data_remarks_value)
            {
                $daily_over_performa_remarks = $row_data_remarks_value;
            }
            
            $insert_data = "INSERT INTO `daily_over_performas`
            (`daily_over_performa_id`, `daily_over_performa_summery_id`, `over_product_id`, `daily_over_performa_status`, `daily_over_performa_remarks`)
            VALUES
            (NULL, '$daily_over_performa_summery_id', '$over_product_id', '$daily_over_performa_status', '$daily_over_performa_remarks');";
            mysqli_query($con, $insert_data);
        }
        header('location: print_daily_over_performa.php?daily_over_performa_summery_id='.$daily_over_performa_summery_id);
    }
    else
    {
        header('location: dashboard.php?msg=error');
    }
    exit(0);
}
?>
	<title>Lab Dashboard - <?php echo $company_trademark; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
    .background_image{
        background-image: url('../images/background.png');
        background-size: cover;
    }
    </style>    
    <style>
        @media print {
            body {
                font-size: 12pt; 
            }

            table {
                font-size: 0.5em; 
            }
            .background_image
            {
                display: none !important;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body class="background_image">
<?php include 'top_navigation.php'; ?>
    <div class="row">
    	<div class="col-md-12 py-3">
    	    <div class = "row">
    	        <div class = "col">
                    <div class="card">
                        <div class="card-header h2 text-center">Fill Daily Shift Over Performa</div>
                        <div class="card-body text-center">
                            <form method = "GET">
                                <div class = "row">
                                    <div class = "col-md-3 text-left">
                                        <label for = "daily_over_performa_summery_date">DATE</label>
                                        <input readonly type = "date" value = "<?php echo date('Y-m-d'); ?>" class = "form-control">
                                        <input type = "hidden" value = "<?php echo date('Y-m-d'); ?>" name = "daily_over_performa_summery_date" class = "form-control" id = "daily_over_performa_summery_date">
                                    </div>
                                    <div class = "col-md-3 text-left">
                                        <label for = "over_brach_name">BRANCH</label>
                                        <input type = "hidden" value = "<?php echo $lab_login_branch_id; ?>" class = "form-control" id = "over_brach_id" name = "over_brach_id">
                                        <input readonly type = "text" value = "<?php echo $lab_login_branch_address; ?>" class = "form-control" id = "over_brach_name">
                                    </div>
                                    <div class = "col-md-3 text-left">
                                        <label for = "over_shift_from">OVER SHIFT FROM</label>
                                        <select name = "over_shift_from" class = "form-control" required>
                                        <?php 
                                        $users = "SELECT * FROM `users` WHERE id != '$lab_user_id' AND `status` = '1' AND branch_id = '$lab_login_branch_id' AND role_id = '8' ";
                                        $run_users = mysqli_query($con, $users);
                                        if(mysqli_num_rows($run_users) >0)
                                        {
                                            while($row_user = mysqli_fetch_array($run_users))
                                            {
                                                echo '<option value = "'.$row_user['id'].'">'.$row_user['u_name'].'</option>';
                                            }
                                        }
                                        else
                                        {
                                            header('location: dashboard.php');
                                        } ?>
                                        </select>
                                    </div>
                                    <div class = "col-md-3 text-left">
                                        <label for = "over_shift_to">OVER SHIFT TO</label>
                                        <select name = "over_shift_to" class = "form-control" required>
                                            <option value = "<?php echo $lab_user_id; ?>"><?php echo $lab_user_name; ?></option>
                                        </select>
                                    </div>
                                    <div class = "col-md-3 text-left">
                                        <label for = "over_shift_id">SELECT OVER SHIFT</label>
                                        <select name = "over_shift_id" class = "form-control" required>
                                        <?php 
                                        $shifts = "SELECT * FROM `over_shifts` WHERE `over_shift_status` = '1'";
                                        $run_shifts = mysqli_query($con, $shifts);
                                        if(mysqli_num_rows($run_shifts) >0)
                                        {
                                            while($row_shift = mysqli_fetch_array($run_shifts))
                                            {
                                                echo '<option value = "'.$row_shift['over_shift_id'].'">'.$row_shift['over_shift_title'].'</option>';
                                            }
                                        }
                                        else
                                        {
                                            header('location: dashboard.php');
                                        } ?>
                                        </select>
                                    </div>
                                    <div class = "col-md-9 text-left">
                                        <label for = "over_shift_remarks">ANY OTHER DETAILS/ REMARKS/ COMMENTS</label>
                                        <textarea name = "over_shift_remarks" class = "form-control" rows = "1"></textarea>
                                    </div>
                                    <?php 
                                    $s = 0;
                                    $select = "SELECT link_over_product_with_section_id, over_productes.over_product_id, over_productes.over_product_title FROM `link_over_product_with_sections` INNER JOIN sections ON link_over_product_with_sections.section_id = sections.section_id INNER JOIN over_productes ON link_over_product_with_sections.over_product_id = over_productes.over_product_id WHERE `link_over_product_with_section_status` = '1' AND link_over_product_with_sections.section_id = '11' ORDER BY `over_productes`.`over_product_title` ASC";
                                    $run = mysqli_query($con, $select);
                                    if(mysqli_num_rows($run) > 0)
                                    { ?>
                                    <div class = "col-md-12">
                                    <table class = "table table-sm table-hover table-striped">
                                        <thead class = "bg-info text-dark">
                                            <tr>
                                                <th>SER #</th>
                                                <th>PRODICT/ ITEM/ SERVICE</th>
                                                <th class = "text-center" colspan = "3">WORKING</th>
                                                <th>REMARKS</th>
                                            </tr>
                                        </thead>   
                                        <tbody>
                                        <?php while($row = mysqli_fetch_array($run))
                                        {
                                            $s++;
                                        ?>
                                        <tr>
                                            <td>
                                                <label><?php echo $s; ?></label>
                                            </td>
                                            <td><?php echo $row['over_product_id']; ?> <label> <?php echo $row['over_product_title']; ?></label></td>
                                            <td class = "text-right">
                                                <input type = "hidden" name = "data[]" value = "<?php echo $row['over_product_id']; ?>"/> 
                                                <input checked required type = "radio" id = "not<?php echo $row['over_product_id']; ?>" name = "data_<?php echo $row['over_product_id']; ?>[]" value = "0"/>
                                                <label for = "not<?php echo $row['over_product_id']; ?>">NOT AVAILABLE</label>
                                            </td>
                                            <td class = "text-right">
                                                <input type = "radio" id = "YES<?php echo $row['over_product_id']; ?>" name = "data_<?php echo $row['over_product_id']; ?>[]" value = "2"/>
                                                <label for = "YES<?php echo $row['over_product_id']; ?>">YES</label>
                                            </td>
                                            <td>
                                                <input type = "radio" id = "NO<?php echo $row['over_product_id']; ?>" name = "data_<?php echo $row['over_product_id']; ?>[]" value = "1"/>
                                                <label for = "NO<?php echo $row['over_product_id']; ?>">NO</label> 
                                            </td>
                                            <td>
                                                <input type = "text" name = "data_remarks_<?php echo $row['over_product_id']; ?>[]" class = "form-control" />
                                            </td>
                                        </tr>                     
                                         <?php } ?>
                                         </tbody>
                                         <tfoot class = "d-print-none">
                                             <tr>
                                                 <th colspan = "5">
                                                     <input type = "submit" name = "save_over_performa" value = "SAVE PERFORMA DATA" class = "btn btn-sm btn-primary" />
                                                     <input type = "reset" value = "CLEAR PERFORMA" class = "btn btn-sm btn-warning" />
                                                 </th>
                                             </tr>
                                         </tfoot>
                                    </table>
                                    </div>         
                                    <?php }
                                    else
                                    {
                                        header('location: dashboard.php');
                                    }
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>
    	        </div>
    	    </div>
    	</div>
    </div>
</body>
</html>