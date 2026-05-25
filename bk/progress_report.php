<?php
include 'includes/connect.php';

if (isset($_POST['date']) && $_POST['date'] !== '') {
	$date = $_POST['date'];
	$print_url = 'print_progess_report.php?' . http_build_query(array('date' => $date));
	?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Opening progress report…</title>
<script>
if ('serviceWorker' in navigator) {
	navigator.serviceWorker.getRegistrations().then(function (regs) {
		regs.forEach(function (reg) { reg.unregister(); });
	});
}
</script>
</head>
<body>
<p>Opening progress report…</p>
<script>
window.open(<?php echo json_encode(ycdo_absolute_url_if_relative($print_url)); ?>, '_blank', 'toolbar=no,scrollbars=yes,resizable=yes,width=1200,height=800');
window.location.replace('progress_report.php');
</script>
</body>
</html>
<?php
	exit;
}

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
?>
	<title>DAILY PROGRESS - <?php echo $company_trademark; ?></title>
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
        <form METHOD = "POST" class = "container">
        <div class = "row">
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
