<?php
include 'includes/connect.php';

if (isset($_POST['date']) && $_POST['date'] !== '') {
    $date = substr((string) $_POST['date'], 0, 10);
    $br_id = (int) ($_POST['br_id'] ?? $branch_id);
    $print_url = 'print_progess_report.php?' . http_build_query(array(
        'date' => $date,
        'br_id' => $br_id,
    ));
    ?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Opening progress report…</title></head>
<body>
<script>
window.open(<?php echo json_encode($print_url); ?>, '_blank', 'toolbar=yes,scrollbars=yes,resizable=yes,width=1200,height=800');
window.location.replace('progress_report.php');
</script>
</body>
</html>
<?php
    exit;
}

include 'includes/head.php';

$role_title = '';
$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if ($run_roles && mysqli_num_rows($run_roles) == 1) {
    while ($row_role = mysqli_fetch_array($run_roles)) {
        $role_title = $row_role['title'];
    }
}
?>
	<title>Progress Report - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke">	<?php include 'left_navigation.php'; ?>	
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo htmlspecialchars($_SESSION['fr_name'] ?? ''); ?><?php if (($_SESSION['is_incharge'] ?? 0) == 2) { echo ' Incharge '; } ?>(<?php echo htmlspecialchars($role_title); ?>)</h3>
    </div>
    <div class="col-md-9">
        <form method="POST" class="container">
        <div class="row">
            <div class="col">
                <label>BRANCH</label>
                <select name="br_id" class="form-control" required>
                    <option value="<?php echo (int) $branch_id; ?>"><?php echo htmlspecialchars($branch_address); ?></option>
                </select>
            </div>
            <div class="col">
                <label>DATE</label>
                <input required type="date" value="<?php echo date('Y-m-d'); ?>" name="date" id="date" class="form-control" />
                <input type="submit" name="progress" value="PROGRESS" class="btn btn-sm btn-info" />
                <input type="reset" name="reset" value="CLEAR" class="btn btn-sm btn-danger" />
            </div>
        </div>
        </form>
    </div>
</div>
</body>
</html>
