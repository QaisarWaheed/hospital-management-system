<?php 
include 'includes/connect.php'; 
if(isset($_GET['date']))
{
    $date = $_GET['date'];
    $br_id = $_GET['br_id'];
}
elseif(isset($_POST['date']))
{
    $date = $_POST['date'];
    $br_id = $_POST['br_id'];
}
else
{
    exit(0);
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta lang="en">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="css/nav_style.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"> 
    <script src="js/jquery.min.js"></script>    
    <script src="js/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/selectize.bootstrap3.min.css" integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
    <title>GYNAE PROGRESS <?php echo date_format(date_create($date), "d-m-Y"); ?><?php echo get_branch_tag_name_by_id($br_id); ?></title>
<style>
@media print 
{  
    @page 
    {  
        size: 210mm 297mm;
    }    
    body
    {
        font-size:xx-small;
    }
}   
</style>
</head>
<body>
<div class="row">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-12 background_whitesmoke">
		<?php include 'navigation_top.php'; ?>
	</div>
</div>    
<table class = "table table-hover" border = "solid">
<caption style = "text-align: center; caption-side: top; color: black;">
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS DAILY <?php echo date_format(date_create($date), "d-m-Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th colspan = "3"></th>
            <th colspan = "3">TODAY</th>
            <th colspan = "3">CURRENT MONTH</th>
            <th colspan = "3">ALL RECORDS</th>
        </tr>
        <tr>
            <th>S#</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>GYNAE TOKEN</th>
            <th>GYNAE ONLINE</th>
            <th>NOT ADDED</th>
            <th>GYNAE TOKEN</th>
            <th>GYNAE ONLINE</th>
            <th>NOT ADDED</th>
            <th>GYNAE TOKEN</th>
            <th>GYNAE ONLINE</th>
            <th>NOT ADDED</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$opd = 0;
$gynae_count_system_token = 0;
$gynae_count_system = 0;
$gynae_count_system_all = 0;
$gynae_count_system_token_all = 0;
$count_opd = 0;
$count_gynae_system = 0;
$count_gynae_system_token = 0;
$count_gynae_system_token_current = 0;
$count_gynae_system_current = 0;
$count_gynae_system_token_all = 0;
$count_gynae_system_all = 0;
$current_month = substr($date, 0, 4);
$select_dr = "SELECT users.id, users.u_name FROM `item_by_doctor` INNER JOIN users ON item_by_doctor.doctor_id = users.id WHERE item_by_doctor.branch_id = '$br_id' AND item_by_doctor.created LIKE '$current_month%' AND item_by_doctor.category_id = '41' GROUP BY item_by_doctor.doctor_id ";
$run_dr = mysqli_query($con, $select_dr);
if(mysqli_num_rows($run_dr) > 0)
{
    while($row_dr = mysqli_fetch_array($run_dr))
    {
        $dr_id = $row_dr['id'];
        $dr_name = $row_dr['u_name'];

        $select_opd = "SELECT COUNT(id) FROM tokans WHERE doctor_id = '$dr_id' AND created LIKE '$date%' AND branch_id = '$br_id' AND tokan_type_id <= 10 AND status = 1 ";
        $run_opd = mysqli_query($con, $select_opd);
        $opds = mysqli_num_rows($run_opd);
        if($opds == 1)
        {
            while($row_opd = mysqli_fetch_array($run_opd))
            {
                $opd = $row_opd[0];
                $count_opd = $count_opd + $opd;
            }
        }
        
        $gynae_token = "SELECT COUNT(id) FROM `item_by_doctor` WHERE doctor_id = '$dr_id' AND item_by_doctor.branch_id = '$br_id' AND item_by_doctor.created LIKE '$date%' AND item_by_doctor.category_id = '41' ";
        $run_gynae_system_token = mysqli_query($con, $gynae_token);
        $gynaes_system_token = mysqli_num_rows($run_gynae_system_token);
        if($gynaes_system_token == 1)
        {
            while($row_gynae_system_token = mysqli_fetch_array($run_gynae_system_token))
            {
                $gynae_count_system_token = $row_gynae_system_token[0];
                $count_gynae_system_token = $count_gynae_system_token + $gynae_count_system_token;
            }
        }
        $gynae_system = "SELECT count(id) FROM `gynae_register` WHERE doctor_id = '$dr_id' AND created like '$date%' AND branch_id = '$br_id'";
        $run_gynae_system = mysqli_query($con, $gynae_system);
        $gynaes_system = mysqli_num_rows($run_gynae_system);
        if($gynaes_system == 1)
        {
            while($row_gynae_system = mysqli_fetch_array($run_gynae_system))
            {
                $gynae_count_system = $row_gynae_system[0];
                $count_gynae_system = $count_gynae_system + $gynae_count_system;
            }
        }

        
        $gynae_token_current = "SELECT COUNT(id) FROM `item_by_doctor` WHERE doctor_id = '$dr_id' AND item_by_doctor.branch_id = '$br_id' AND item_by_doctor.created LIKE '$current_month%' AND item_by_doctor.category_id = '41' ";
        $run_gynae_system_token_current = mysqli_query($con, $gynae_token_current);
        $gynaes_system_token_current = mysqli_num_rows($run_gynae_system_token_current);
        if($gynaes_system_token_current == 1)
        {
            while($row_gynae_system_token_current = mysqli_fetch_array($run_gynae_system_token_current))
            {
                $gynae_count_system_token_current = $row_gynae_system_token_current[0];
                $count_gynae_system_token_current = $count_gynae_system_token_current + $gynae_count_system_token_current;
            }
        }
        $gynae_system_current = "SELECT count(id) FROM `gynae_register` WHERE doctor_id = '$dr_id' AND created LIKE '$current_month%' AND branch_id = '$br_id'";
        $run_gynae_system_current = mysqli_query($con, $gynae_system_current);
        $gynaes_system_current = mysqli_num_rows($run_gynae_system_current);
        if($gynaes_system_current == 1)
        {
            while($row_gynae_system_current = mysqli_fetch_array($run_gynae_system_current))
            {
                $gynae_count_system_current = $row_gynae_system_current[0];
                $count_gynae_system_current = $count_gynae_system_current + $gynae_count_system_current;
            }
        }          
        
        
        $gynae_token_all = "SELECT COUNT(id) FROM `item_by_doctor` WHERE doctor_id = '$dr_id' AND item_by_doctor.branch_id = '$br_id' AND item_by_doctor.created > '2025-03-31' AND item_by_doctor.category_id = '41' ";
        $run_gynae_system_token_all = mysqli_query($con, $gynae_token_all);
        $gynaes_system_token_all = mysqli_num_rows($run_gynae_system_token_all);
        if($gynaes_system_token_all == 1)
        {
            while($row_gynae_system_token_all = mysqli_fetch_array($run_gynae_system_token_all))
            {
                $gynae_count_system_token_all = $row_gynae_system_token_all[0];
                $count_gynae_system_token_all = $count_gynae_system_token_all + $gynae_count_system_token_all;
            }
        }
        $gynae_system_all = "SELECT count(id) FROM `gynae_register` WHERE doctor_id = '$dr_id' AND created > '2025-03-31' AND branch_id = '$br_id'";
        $run_gynae_system_all = mysqli_query($con, $gynae_system_all);
        $gynaes_system_all = mysqli_num_rows($run_gynae_system_all);
        if($gynaes_system_all == 1)
        {
            while($row_gynae_system_all = mysqli_fetch_array($run_gynae_system_all))
            {
                $gynae_count_system_all = $row_gynae_system_all[0];
                $count_gynae_system_all = $count_gynae_system_all + $gynae_count_system_all;
            }
        }        
        $s++;
        echo '
        <tr style = "text-align: center;">
            <td>'.$s.'</td>
            <td style = "text-align: left;">'.$dr_name.'</td>
            <td>'.$opd.'</td>
            <td>'.$gynae_count_system_token.'</td>
            <td>'.$gynae_count_system.'</td>
            <td>'.$gynae_count_system-$gynae_count_system_token.'</td>
            
            <td>'.$gynae_count_system_token_current.'</td>
            <td>'.$gynae_count_system_current.'</td>
            <td>'.$gynae_count_system_current-$gynae_count_system_token_current.'</td>
            
            <td>'.$gynae_count_system_token_all.'</td>
            <td>'.$gynae_count_system_all.'</td>
            <td>'.$gynae_count_system_all-$gynae_count_system_token_all.'</td>
        </tr>';
        $opd = 0;
        $gynae_count_system = 0;
        $gynae_count_system_token = 0;
        $gynae_count_system_current = 0;
        $gynae_count_system_token_current = 0;
        $gynae_count_system_all = 0;
        $gynae_count_system_token_all = 0;
    }
}
?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan = "2">TOTAL</th>
            <th><?php echo $count_opd; ?></th>
            
            <th><?php echo $count_gynae_system_token; ?></th>
            <th><?php echo $count_gynae_system; ?></th>
            <th><?php echo $count_gynae_system-$count_gynae_system_token; ?></th>
            
            <th><?php echo $count_gynae_system_token_current; ?></th>
            <th><?php echo $count_gynae_system_current; ?></th>
            <th><?php echo $count_gynae_system_current-$count_gynae_system_token_current; ?></th>
            
            <th><?php echo $count_gynae_system_token_all; ?></th>
            <th><?php echo $count_gynae_system_all; ?></th>
            <th><?php echo $count_gynae_system_all-$count_gynae_system_token_all; ?></th>
        </tr>
    </tfoot>
</table>
</body>
</html>
<?php mysqli_close($con); ?>