<?php
include 'includes/connect.php';

if (isset($_POST['date']) && $_POST['date'] !== '') {
    $date = substr((string) $_POST['date'], 0, 7);
    $br_id = (int) ($_POST['br_id'] ?? 0);
    $print_url = 'print_report_month.php?' . http_build_query(array(
        'date' => $date . '-01',
        'br_id' => $br_id,
    ));
    ?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Opening month report…</title></head>
<body>
<script>
window.open(<?php echo json_encode(ycdo_absolute_url_if_relative($print_url)); ?>, '_blank', 'toolbar=yes,scrollbars=yes,resizable=yes,width=1200,height=800');
window.location.replace('report_month.php');
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
	<title>Month Report - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke">	<?php include 'left_navigation.php'; ?>	
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo htmlspecialchars($_SESSION['fr_name'] ?? ($_SESSION['admin_name'] ?? '')); ?>(<?php echo htmlspecialchars($role_title); ?>)</h3>
    </div>
    <div class="col-md-9">
        <form method="POST" class="container">
        <div class="row">
            <div class="col">
                <label>BRANCH</label>
                <select name="br_id" class="form-control" required>
<?php
if (isset($fr_id) && $fr_id == 1) {
    $select = "SELECT * FROM branchs WHERE status = '1' ";
    $run = mysqli_query($con, $select);
    if ($run && mysqli_num_rows($run) > 0) {
        while ($row = mysqli_fetch_array($run)) {
            echo '<option value="' . (int) $row['id'] . '">' . htmlspecialchars($row['address']) . '</option>';
        }
    }
} else {
?>
    <option value="<?php echo (int) $branch_id; ?>"><?php echo htmlspecialchars($branch_address ?? ''); ?></option>
<?php } ?>
                </select>
            </div>
            <div class="col">
                <label>MONTH</label>
                <input required type="month" value="<?php echo htmlspecialchars(date('Y-m'), ENT_QUOTES, 'UTF-8'); ?>" name="date" id="date" class="form-control" />
                <input type="submit" name="progress" value="PROGRESS" class="btn btn-sm btn-info" />
                <input type="reset" name="reset" value="CLEAR" class="btn btn-sm btn-danger" />
            </div>
        </div>
        </form>
    </div>
</div>
</body>
</html>
