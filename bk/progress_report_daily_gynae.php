<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 

$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if ($run_roles && mysqli_num_rows($run_roles) == 1)
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
require_once __DIR__ . '/includes/branch_select_options.php';
$gynae_branch_options = bk_branch_select_options($con, (int) $bk_branch_id);
$gynae_date_value = date('Y-m-d');
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
                    <?php
                    $gynae_forms = array(
                        array('action' => 'gyane_report_less_then_four_month.php', 'label' => '< 4 MONTH', 'class' => 'btn-success', 'name' => 'less_then_four_month'),
                        array('action' => 'gyane_report_less_then_four_month_and_greater_then_eight_month.php', 'label' => '> 4 MONTH & < 8 MONTH', 'class' => 'btn-info', 'name' => 'less_then_four_month_and_greater_then_eight_month'),
                        array('action' => 'gyane_report_greater_then_eight_month.php', 'label' => '> 8 MONTH', 'class' => 'btn-dark', 'name' => 'greater_then_eight_month'),
                        array('action' => 'gyane_report_discontinued.php', 'label' => 'DISCONTINUED', 'class' => 'btn-danger', 'name' => 'discontinued'),
                    );
                    foreach ($gynae_forms as $gynae_form) {
                    ?>
                    <div class="col mb-2">
                        <form method="GET" action="<?php echo htmlspecialchars($gynae_form['action'], ENT_QUOTES, 'UTF-8'); ?>">
                            <select name="br_id" class="form-control mb-1" required><?php echo $gynae_branch_options; ?></select>
                            <input type="date" name="date" class="form-control mb-1" value="<?php echo $gynae_date_value; ?>" required />
                            <input type="submit" value="<?php echo htmlspecialchars($gynae_form['label'], ENT_QUOTES, 'UTF-8'); ?>" name="<?php echo htmlspecialchars($gynae_form['name'], ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo htmlspecialchars($gynae_form['class'], ENT_QUOTES, 'UTF-8'); ?>" />
                        </form>
                    </div>
                    <?php } ?>
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
                                <td><select name = "br_id" class = "form-control" required><?php echo $gynae_branch_options; ?></select></td>
                                <td><input class = "form-control" type = "date" name = "date" value = "<?php echo $gynae_date_value; ?>" /></td>
                                <td></td>
                                <td><input type = "submit" class = "btn btn-primary" name = "generate" value = "generate" /></td>
                            </tr>
                            </form>
                            <form action = "print_progress_report_daily_gynae.php" onsubmit="showProgress(); return true;">
                            <tr>
                                <td>GYNAE PROGRESS REPORT</td>
                                <td><select name = "br_id" class = "form-control" required><?php echo $gynae_branch_options; ?></select></td>
                                <td><input class = "form-control" type = "date" name = "date" value = "<?php echo $gynae_date_value; ?>" /></td>
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
