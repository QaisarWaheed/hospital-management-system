<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 

$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if(mysqli_num_rows($run_roles) == 1)
{
    while($row_role = mysqli_fetch_array($run_roles))
    {
        $role_title = $row_role['title'];
    }
}
else
{
    $role_title = '';
}
?>
	<title>Gynae Section - <?php echo $company_trademark; ?></title>
<script src="js/jquery.min.js"></script>
<script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

</head>

<body class="background_image">
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
<div class="row" style="margin: 0px;" id = "submitBody">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke">	<?php include 'left_navigation.php'; ?>	
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo $_SESSION['hr_name'];if($_SESSION['is_incharge'] == 2){ echo " Incharge ";} ?>(<?php echo $role_title; ?>)</h3>
    </div>
    <div class = "col-md-9">
        <div class = "row">
            <div class = "col-md-12">
                <h2>GYNAE SECTION</h2>
            </div>
            <div class = "col-md-12">
                <div class = "row p-3">
                    <div class = "col"><form method = "GET" action = "gyane_report_less_then_four_month.php"><input type = "submit" value = "< 4 MONTH" name = "less_then_four_month" class = "btn-success" /></form></div>
                    <div class = "col"><form method = "GET" action = "gyane_report_less_then_four_month_and_greater_then_eight_month.php"><input type = "submit" value = "> 4 MONTH & < 8 MONTH" name = "less_then_four_month_and_greater_then_eight_month" class = "btn-info" /></form></div>
                    <div class = "col"><form method = "GET" action = "gyane_report_greater_then_eight_month.php"><input type = "submit" value = "> 8 MONTH" name = "greater_then_eight_month" class = "btn-dark" /></form></div>
                    <div class = "col"><form method = "GET" action = "gyane_report_discontinued.php"><input type = "submit" value = "DISCONTINUED" name = "discontinued" class = "btn-danger" /></form></div>
                </div>
            </div>
            <div class = "col-md-12">
                <div>
                    <table class = "table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>TITLE</th>
                                <th>BRANCH</th>
                                <th colspan = "2">DATE</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <form action = "gyane_total_record.php" onsubmit="showProgress(); return true;">
                            <tr>
                                <td>ALL CONTINUE RECORD FROM ONLINE</td>
                                <td><select name = "br_id" class = "form-control" required>
                                    <?php $select_br = "SELECT * FROM branchs WHERE status = '1' "; $run_br = mysqli_query($con, $select_br);
                                        if(mysqli_num_rows($run_br) > 0){   while($row_br = mysqli_fetch_array($run_br)){   $br_id = $row_br['id'];    $br_address = $row_br['address'];   echo '<option value = "'.$br_id.'">'.$br_address.'</option>';   }    }
                                        else{ ?><option value = "<?php echo $bk_branch_id; ?>"><?php echo $br_branch_address; ?></option> <?php }?>
                                </select></td>
                                <td><input class = "form-control" type = "date" name = "date" value = "<?php echo date('Y-m-d'); ?>" /></td>
                                <td></td>
                                <td><input type = "submit" class = "btn btn-primary" name = "generate" value = "generate" /></td>
                            </tr>
                            </form>
                            <form action = "print_progress_report_daily_gynae.php" onsubmit="showProgress(); return true;">
                            <tr>
                                <td>GYNAE PROGRESS REPORT</td>
                                <td><select name = "br_id" class = "form-control" required>
                                    <?php $select_br = "SELECT * FROM branchs WHERE status = '1' "; $run_br = mysqli_query($con, $select_br);
                                        if(mysqli_num_rows($run_br) > 0){   while($row_br = mysqli_fetch_array($run_br)){   $br_id = $row_br['id'];    $br_address = $row_br['address'];   echo '<option value = "'.$br_id.'">'.$br_address.'</option>';   }    }
                                        else{ ?><option value = "<?php echo $bk_branch_id; ?>"><?php echo $br_branch_address; ?></option> <?php }?>
                                </select></td>
                                <td><input class = "form-control" type = "date" name = "date" value = "<?php echo date('Y-m-d'); ?>" /></td>
                                <td></td>
                                <td><input type = "submit" class = "btn btn-primary" name = "generate" value = "generate" /></td>
                            </tr>
                            </form>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>
</div>
</body>
</html>
<script>
function showProgress() {
  document.getElementById('submitBody').style.display = 'none';
  document.getElementById('loadingSpinner').style.display = 'block';
}    
</script>
<?php mysqli_close($con); ?>