<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 

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
    echo '<script>window.open("print_progess_report_daily.php?date='.$date.'&br_id='.$br_id.'", "PROGRESS REPORT", "width=3000,height=3000");</script>';
}
?>
	<title>DAILY PROGRESS - <?php echo $company_trademark; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<script src="js/jquery.min.js"></script>
<script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
<style>
.background_image{
	background-image: url('../images/background.png');
	background-size: cover;
}
</style>    
<style>
    @media print {
        body {
            /* Reduce the base font size for the entire page to 12px */
            font-size: 12px; 
        }

        table {
            font-size: 0.8em; 
        }
    }
</style>
</head>

<body class="background_image">
<?php include 'top_navigation.php'; ?>	

<div class="row">
    <div class = "col-md-12">
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
                <input required type = "date" value = "<?php echo date('Y-m-d'); ?>" name = "date" id = "date" class = "form-control" />
                <input type = "submit" name = "progress" value = "PROGRESS" class = "btn btn-sm btn-info" />
                <input type = "reset" name = "reset" value = "CLEAR" class = "btn btn-sm btn-danger" />
            </div>
        </div>
        </form>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>
<?php mysqli_close($con); ?>