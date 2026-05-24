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
    echo '<script>window.open("print_progress_report_monthly.php?date='.$date.'&br_id='.$br_id.'", "MONTHLY PROGRESS REPORT", "width=3000,height=3000");</script>';
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>

</head>

<body class="background_image">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke">	<?php include 'left_navigation.php'; ?>	
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo htmlspecialchars($bk_name); if ($bk_is_incharge == 2) { echo ' Incharge '; } ?>(<?php echo htmlspecialchars($role_title); ?>)</h3>
    </div>
    <div class = "col-md-9">
        <form action = "progress_report_monthly.php" method = "POST" class = "container">
        <div class = "row">
            <div class = "col">
                <label>BRANCH</label>
                <select name = "br_id" class = "form-control" required>
                    <?php 
                    if ($user_id == 1) {
                        $select_br = "SELECT * FROM branchs WHERE status = '1' ";
                        $run_br = mysqli_query($con, $select_br);
                        if ($run_br && mysqli_num_rows($run_br) > 0) {
                            while ($row_br = mysqli_fetch_array($run_br)) {
                                echo '<option value="' . (int) $row_br['id'] . '">' . htmlspecialchars($row_br['address']) . '</option>';
                            }
                        } else { ?>
                            <option value="<?php echo (int) $bk_branch_id; ?>"><?php echo htmlspecialchars($bk_branch_address); ?></option>
                        <?php }
                    } else { ?>
                        <option value="<?php echo (int) $bk_branch_id; ?>"><?php echo htmlspecialchars($bk_branch_address); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class = "col">
                <label>DATE</label>
                <input required type = "month" value = "<?php echo date('Y-m'); ?>" name = "date" id = "date" class = "form-control" />
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