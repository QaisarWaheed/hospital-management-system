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
    $date_esc = mysqli_real_escape_string($con, (string) $date);
    $br_id = (int) $br_id;
    ycdo_echo_window_open(
        '../bk/print_progess_report_daily_lab.php',
        'date=' . rawurlencode($date_esc) . '&br_id=' . $br_id,
        'PROGRESS REPORT',
        'width=3000,height=3000'
    );
}
?>
	<title>DAILY(LAB) PROGRESS - <?php echo $company_trademark; ?></title>
<script src="js/jquery.min.js"></script>
<script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke">	<?php include 'left_navigation.php'; ?>	
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo $_SESSION['fr_name'];if($_SESSION['is_incharge'] == 2){ echo " Incharge ";} ?>(<?php echo $role_title; ?>)</h3>
    </div>
    <div class = "col-md-9">
        <form METHOD = "POST" class = "container">
        <div class = "row">
            <div class = "col">
                <label>BRANCH</label>
                <select name = "br_id" class = "form-control" required>
                    <option value = "<?php echo $branch_id; ?>"><?php echo $branch_address; ?></option>
                </select>
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