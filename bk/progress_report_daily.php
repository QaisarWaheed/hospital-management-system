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
$branch_options = bk_branch_select_options($con, (int) $bk_branch_id);

if (isset($_POST['date']) && $_POST['date'] !== '')
{
    $date = urlencode((string) $_POST['date']);
    $br_id = (int) $_POST['br_id'];
    echo '<script>window.open("print_progess_report_daily_gynae.php?date=' . $date . '&br_id=' . $br_id . '", "PROGRESS REPORT", "width=1200,height=800");</script>';
}
?>
	<title>DAILY PROGRESS - <?php echo $company_trademark; ?></title>

</head>

<body class="background_image">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke">	<?php include 'left_navigation.php'; ?>	
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo htmlspecialchars((string) ($_SESSION['hr_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?><?php if(($_SESSION['is_incharge'] ?? 0) == 2){ echo " Incharge ";} ?>(<?php echo htmlspecialchars($role_title, ENT_QUOTES, 'UTF-8'); ?>)</h3>
    </div>
    <div class = "col-md-9">
        <form action = "progress_report_daily.php" method = "POST" class = "container">
        <div class = "row">
            <div class = "col">
                <label>BRANCH</label>
                <select name = "br_id" class = "form-control" required><?php echo $branch_options; ?></select>
            </div>
            <div class = "col">
                <label>DATE</label>
                <input required type = "date" value = "<?php echo date('Y-m-d'); ?>" name = "date" id = "date" class = "form-control" />
                <input type = "submit" name = "progress" value = "PROGRESS" class = "btn btn-sm btn-info" />
                <input type = "reset" name = "reset" value = "CLEAR" class = "btn btn-sm btn-danger" />
            </div>
        </div>
        </form>
    </div>
</div>
</body>
</html>
<?php mysqli_close($con); ?>
