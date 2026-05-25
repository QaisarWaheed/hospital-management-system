<?php
include 'includes/connect.php';

$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
$role_title = '';
if ($run_roles && mysqli_num_rows($run_roles) == 1) {
    while ($row_role = mysqli_fetch_array($run_roles)) {
        $role_title = $row_role['title'];
    }
}

if (isset($_POST['progress'], $_POST['date'], $_POST['doctor_id']) && $_POST['date'] !== '' && $_POST['doctor_id'] !== '') {
    $print_url = 'print_doctor_monthly_profile.php?' . http_build_query(array(
        'doctor_id' => (int) $_POST['doctor_id'],
        'date' => substr((string) $_POST['date'], 0, 7),
        'br_id' => (int) $branch_id,
    ));
    ?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Opening doctor profile…</title></head>
<body>
<script>
window.open(<?php echo json_encode(ycdo_absolute_url_if_relative($print_url)); ?>, '_blank', 'toolbar=yes,scrollbars=yes,resizable=yes,width=1200,height=900');
window.location.replace('doctor_monthly_profile.php');
</script>
</body>
</html>
<?php
    exit;
}

$date = date('Y-m');
if (isset($_POST['date']) && $_POST['date'] !== '') {
    $date = substr((string) $_POST['date'], 0, 7);
}

include 'includes/head.php';
?>
	<title>DOCTOR MONTHLY PROFILE - <?php echo htmlspecialchars($date); ?> <?php echo htmlspecialchars($company_trademark); ?></title>
<style>
@media print { .no-print, .no-print * { display: none !important; } }
</style>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo htmlspecialchars($company_name); ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke no-print">	<?php include 'left_navigation.php'; ?>	
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo htmlspecialchars($user_name); if ($is_incharge == 2) { echo ' Incharge '; } ?>(<?php echo htmlspecialchars($role_title); ?>)</h3>
    </div>
    <div class="col-md-9">
        <form method="POST">
        <div class="row no-print">
            <div class="col-md-12">
                <h2 align="center"><?php echo htmlspecialchars($branch_name); ?></h2>
                <p class="text-muted" style="text-align:center;">Select doctor and month, then generate. The report opens in a new tab.</p>
            </div>
            <div class="col-md-12">
                <label>DOCTOR</label>
                <select name="doctor_id" class="form-control" required>
                    <?php
                    if (isset($_POST['doctor_id']) && $_POST['doctor_id'] !== '') {
                        echo '<option value="' . (int) $_POST['doctor_id'] . '">' . htmlspecialchars(get_uname_by_id($_POST['doctor_id'])) . '</option>';
                    }
                    echo get_doctor_option($branch_id);
                    ?>
                </select>
            </div>
            <div class="col-md-12">
                <label>MONTH</label>
                <input required type="month" value="<?php echo htmlspecialchars($date); ?>" name="date" id="date" class="form-control" />
                <input type="submit" name="progress" value="GENERATE REPORT" class="btn btn-sm btn-info" />
                <input type="reset" name="reset" value="CLEAR" class="btn btn-sm btn-danger" />
            </div>
        </div>
        </form>
    </div>
</div>
</body>
</html>
