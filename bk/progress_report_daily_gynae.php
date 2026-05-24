<?php
require_once __DIR__ . '/includes/connect.php';
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
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke">	<?php include 'left_navigation.php'; ?>	
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo htmlspecialchars($bk_name); if ($bk_is_incharge == 2) { echo ' Incharge '; } ?>(<?php echo htmlspecialchars($role_title); ?>)</h3>
    </div>
    <div class = "col-md-9">
        <div class = "row">
            <div class = "col-md-12">
                <h2>GYNAE SECTION</h2>
            </div>
            <div class = "col-md-12">
                <div class = "row p-3">
                    <div class = "col">
                        <form method = "GET" action = "gyane_report_less_then_four_month.php" target = "_blank">
                            <input type = "submit" value = "< 4 MONTH" class = "btn btn-success btn-block" />
                        </form>
                    </div>
                    <div class = "col">
                        <form method = "GET" action = "gyane_report_less_then_four_month_and_greater_then_eight_month.php" target = "_blank">
                            <input type = "submit" value = "> 4 MONTH & < 8 MONTH" class = "btn btn-info btn-block" />
                        </form>
                    </div>
                    <div class = "col">
                        <form method = "GET" action = "gyane_report_greater_then_eight_month.php" target = "_blank">
                            <input type = "submit" value = "> 8 MONTH" class = "btn btn-dark btn-block" />
                        </form>
                    </div>
                    <div class = "col">
                        <form method = "GET" action = "gyane_report_discontinued.php" target = "_blank">
                            <input type = "submit" value = "DISCONTINUED" class = "btn btn-danger btn-block" />
                        </form>
                    </div>
                </div>
            </div>
            <div class = "col-md-12">
                <div>
                    <table class = "table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>TITLE</th>
                                <th>BRANCH</th>
                                <th>DATE</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <form action = "gyane_total_record.php" method = "POST" target = "_blank">
                            <tr>
                                <td>ALL CONTINUE RECORD FROM ONLINE</td>
                                <td><select name = "br_id" class = "form-control" required><?php echo $gynae_branch_options; ?></select></td>
                                <td><input class = "form-control" type = "date" name = "date" value = "<?php echo $gynae_date_value; ?>" /></td>
                                <td><input type = "submit" class = "btn btn-primary" name = "generate" value = "generate" /></td>
                            </tr>
                            </form>
                            <form action = "print_progress_report_daily_gynae.php" method = "POST" target = "_blank">
                            <tr>
                                <td>GYNAE PROGRESS REPORT</td>
                                <td><select name = "br_id" class = "form-control" required><?php echo $gynae_branch_options; ?></select></td>
                                <td><input class = "form-control" type = "date" name = "date" value = "<?php echo $gynae_date_value; ?>" /></td>
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
<?php mysqli_close($con); ?>
