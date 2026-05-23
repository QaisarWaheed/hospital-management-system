<?php
require_once __DIR__ . '/includes/connect.php';
include 'includes/head.php';

$role_title = '';
$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if ($run_roles && mysqli_num_rows($run_roles) == 1) {
    while ($row_role = mysqli_fetch_array($run_roles)) {
        $role_title = $row_role['title'];
    }
}

$run = false;
$date = date('Y-m-d');
if (isset($_POST['prescription'], $_POST['date'], $_POST['doctor_id'])
    && $_POST['date'] !== '' && $_POST['doctor_id'] !== '') {
    $date = $_POST['date'];
    $doctor_id = (int) $_POST['doctor_id'];
    $select = "SELECT tokans.id, tokans.created, patients.name, patients.age, patients.phone, tokans.cash, tokans.cash_received FROM `tokans` INNER JOIN patients ON tokans.patient_id = patients.id WHERE tokans.created LIKE '$date%' AND tokans.doctor_id = '$doctor_id' AND tokans.status = '1' AND tokans.tokan_type_id > 100 ";
    $run = mysqli_query($con, $select);
}
?>
	<title>DOCTOR PRESCRIPTION - <?php echo $date; ?> <?php echo $company_trademark; ?></title>
<script src="js/jquery.min.js"></script>
<script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
<style>
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
}    
</style>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke no-print">	<?php include 'left_navigation.php'; ?>	
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo htmlspecialchars($bk_name); if ($bk_is_incharge == 2) { echo ' Incharge '; } ?>(<?php echo htmlspecialchars($role_title); ?>)</h3>
    </div>
    <div class = "col-md-9">
        <form METHOD = "POST">
        <div class = "row no-print">
            <div class = "col-md-12">
                <h2 align = "center"><?php echo htmlspecialchars($bk_branch_name); ?></h2>
            </div>
            <div class = "col-md-12">
                <label>DOCTOR</label>
                <select name = "doctor_id" class = "form-control" required>
                    <?php 
                    if(isset($_POST['doctor_id']) && $_POST['doctor_id'] != '')
                    {
                        echo '<option value = "'.$_POST['doctor_id'].'">'.get_uname_by_id($_POST['doctor_id']).'</option>';
                    }
                        echo get_doctor_option($bk_branch_id); ?>
                </select>
            </div>
            <div class = "col-md-12">
                <label>DATE</label>
                <input required type = "date" value = "<?php if(isset($_POST['date'])){echo $_POST['date'];}else{echo date('Y-m-d');} ?>" name = "date" id = "date" class = "form-control" />
                <input type = "submit" name = "prescription" value = "PRESCRIPTION" class = "btn btn-sm btn-info" />
                <input type = "reset" name = "reset" value = "CLEAR" class = "btn btn-sm btn-danger" />
            </div>
        </div>
        </form>
<?php
$s = 0; 
?>
        <div class = "row">
            <div class = "col-md-12">
                <table class = "table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Sr#</th>
                            <th>TIME</th>
                            <th>TOKEN</th>
                            <th>NAME</th>
                            <th>AGE</th>
                            <th>PHONE</th>
                            <th>AMOUNT</th>
                            <th>RECEIVED</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sr = 1;
                    if ($run && mysqli_num_rows($run) > 0) {
                        while ($row = mysqli_fetch_array($run)) {
                    ?>
                        <tr>
                            <td><?php echo $sr++; ?></td>
                            <td><?php echo ycdo_safe_date_format($row['created'], 'h:i:s A', ''); ?></td>
                            <td><a target="_blank" class = "btn btn-sm btn-info" href = "print_medicine_slip_duplicate.php?tokan_no=<?php echo $row['id']; ?>"><?php echo $row['id']; ?></a></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['age']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['cash']; ?></td>
                            <td><?php echo $row['cash_received']; ?></td>
                        </tr>
                    <?php }
                    } elseif (isset($_POST['prescription'])) { ?>
                        <tr><td colspan="8">No prescription tokens found for this doctor and date.</td></tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
<?php mysqli_close($con); ?>
</html>