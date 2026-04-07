<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
include 'includes/head.php'; 

if(isset($_POST['date']) && $_POST['date'] != '')
{
    $date = $_POST['date'];
    $br_id = $_POST['br_id'];
    echo '<script>window.open("print_progress_report_monthly.php?date='.$date.'&br_id='.$br_id.'", "MONTHLY PROGRESS REPORT", "width=3000,height=3000");</script>';
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
<script src="js/jquery.min.js"></script>
<script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />

</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
	<div class="col-md-3 background_whitesmoke">	<?php include 'left_navigation.php'; ?>	
    	<h3 style="margin-top: 350px;text-align: center;"><?php echo $_SESSION['lab_user_name']; ?></h3>
    </div>
    <div class = "col-md-9">
        <form METHOD = "POST" class = "container">
        <div class = "row">
            <div class = "col">
                <label>BRANCH</label>
                <select name = "br_id" class = "form-control" required>
                    <?php
                    $select_br = "SELECT * FROM branchs WHERE status = '1' ";
                    $run_br = mysqli_query($con, $select_br);
                    if(mysqli_num_rows($run_br) > 0)
                    {
                        while($row_br = mysqli_fetch_array($run_br))
                        {
                            $br_id = $row_br['id'];
                            $br_address = $row_br['address'];
                            echo '<option value = "'.$br_id.'">'.$br_address.'</option>';
                        }
                    }
                    else
                    {
                        echo '<option value = "">SOMETHING WENT WRONG...</option>';
                    }
                    ?>
                </select>
            </div>
            <div class = "col">
                <label>DATE</label>
                <input required type = "month" value = "<?php echo date('Y-m-d'); ?>" name = "date" id = "date" class = "form-control" />
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