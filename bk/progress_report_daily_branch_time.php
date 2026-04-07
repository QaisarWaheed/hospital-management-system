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

if( isset($_POST['date']) && $_POST['date'] != '')
{
    $date = $_POST['date'];
    $br_id = $_POST['br_id'];
    $start_from = $_POST['start_from'];
    $end_at = $_POST['end_at'];
    echo '<script>window.open("print_progress_report_daily_branch_time.php?date='.$date.'&br_id='.$br_id.'&start_from='.$start_from.'&end_at='.$end_at.'", "DAILY PROGRESS REPORT", "width=3000,height=3000");</script>';
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
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
        <form action = "progress_report_daily_branch_time.php" METHOD = "POST" class = "container" onsubmit="showProgress(); return true;">
        <div class = "row">
            <div class = "col">
                <label>BRANCH</label>
                <select name = "br_id" class = "form-control" required>
                    <option value = "0">ALL BRANCHES</option>
                    <?php
                        $select_br = "SELECT * FROM branchs WHERE status = '1' ";
                        $run_br = mysqli_query($con, $select_br);
                        if(mysqli_num_rows($run_br) > 0)
                        {
                            while($row_br = mysqli_fetch_array($run_br))
                            {
                                $br_id = $row_br['id'];
                                $br_address = $row_br['address'];
                                if($bk_branch_id == $br_id)
                                {
                                    echo '<option SELECTED value = "'.$br_id.'">'.$br_address.'</option>';
                                }
                                else
                                {
                                    echo '<option value = "'.$br_id.'">'.$br_address.'</option>';
                                }
                            }
                        }
                        else
                        { ?>
                            <option value = "<?php echo $bk_branch_id; ?>"><?php echo $br_branch_address; ?></option>    
                        <?php } ?>
                </select>
            </div>
            <div class = "col">
                <label>DATE</label>
                <input required type = "date" value = "<?php echo date('Y-m-d'); ?>" name = "date" id = "date" class = "form-control" />
            </div>
            <div class = "col">
                <label>STAR FROM</label>
                <input required type = "time" value = "<?php echo date('00:00:00'); ?>" name = "start_from" id = "start_from" class = "form-control" />
            </div>
            <div class = "col">
                <label>END AT</label>
                <input required type = "time" value = "<?php echo date('06:00:00'); ?>" name = "end_at" id = "end_at" class = "form-control" />
                <input type = "submit" name = "progress" value = "PROGRESS" class = "btn btn-sm btn-info" />
                <input type = "reset" name = "reset" value = "CLEAR" class = "btn btn-sm btn-danger" />
            </div>
        </div>
        </form>
    </div>
</div>
</body>
</html>
<script>
function showProgress() {
  document.getElementById('submitBody').style.display = 'none';
//   document.getElementById('submitButton').style.display = 'none';
  document.getElementById('loadingSpinner').style.display = 'block';
}    
</script>
<?php mysqli_close($con); ?>