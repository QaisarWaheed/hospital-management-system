<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php';
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
    	<div style="text-align: left;float: left;">
    		<h3 style="margin-top: 250px;text-align: center;">USER: <?php echo $_SESSION['ao_name']; ?></h3>
    	</div>	
	</div>
	<div class="col-md-9">
        <form method = "POST">
            <div class = "row">
                <div class = "col-md-8">
                    <label for = "token_no">ENTER PROCEDURE TOKEN NO</label>
                    <input required min = "1" type = "number" placeholder = "ENTER PROCEDURE TOKEN NO..." name = "token_no" id = "token_no" class = "form-control" />
                </div>
                <div class = "col-md-4">
                    <div style = "margin-top: 35px;">
                        <input type = "submit" value = "SEARCH" name = "search" class = "btn btn-primary btn-sm" />
                    </div>
                </div>
            </div>
        </form>
<?php
if(isset($_POST['search']) && $_POST['token_no'] != '')
{
    $token_no = $_POST['token_no'];
    $procedures = '';
    $procedure = "SELECT name FROM `items` WHERE category_id = 3 AND `id` IN (SELECT `item_id` FROM `item_register_to_branches` WHERE `id` IN (SELECT item_id FROM `item_by_doctor` WHERE `tokan_no` = '$token_no')) ";
    $run_procedure = mysqli_query($con, $procedure);
    if(mysqli_num_rows($run_procedure) > 0)
    {
        while($row_procedure = mysqli_fetch_array($run_procedure))
        {
            $procedures .= $row_procedure['0'].'</br>';
            
    $query = "SELECT tokans.id AS token_no,tokans.cash AS cash, tokans.cash_received AS cash_received, patients.name AS patient_name, patients.cnic AS patient_cnic, patients.phone AS patient_phone, patients.age AS patient_age FROM `tokans` INNER JOIN patients ON tokans.patient_id = patients.id WHERE tokans.id = '$token_no' "; 
    $run = mysqli_query($con, $query);
    if(mysqli_num_rows($run) == 1)
    {
        while($row = mysqli_fetch_array($run))
        {
            $pending_received_amount = 0;
            $pending_received = "SELECT amount FROM `branch_pending_receive` WHERE `token_no` = '$token_no' ";
            $run_pending_received = mysqli_query($con, $pending_received);
            if(mysqli_num_rows($run_pending_received) > 0)
            {
                while($row_pending_received = mysqli_fetch_array($run_pending_received))
                {
                    $pending_received_amount = $row_pending_received['0'] + $pending_received_amount;
                }
            }
?>
        <div class = "row">
            <div class = "col-md-12">
                <h3 align = "center"><label>RECORD IN SYSTEM</label></h3>
            </div>
            <div class = "col-md-12">
                <table class = "table table-hover table-bordered">
                    <thead>
                        <tr>
                            <td>TOKEN NO</td>
                            <th><?php echo $token_no; ?></th>
                            <td>PATIENT NAME</td>
                            <th><?php echo $row['patient_name']; ?></th>
                            <td>AGE</td>
                            <th><?php echo $row['patient_age']; ?></th>
                            <td>PHONE</td>
                            <th><?php echo $row['patient_phone']; ?></th>
                        </tr>
                        <tr>
                            <td>TOTAL</td>
                            <th><?php echo $row['cash']; ?></th>
                            <td>RECEIVED</td>
                            <th><?php echo ($row['cash_received']+$pending_received_amount); ?></th>
                            <td>REMAINING</td>
                            <th><?php echo $row['cash']-($row['cash_received']+$pending_received_amount); ?></th>
                            <td>CNIC</td>
                            <th><?php echo $row['patient_cnic']; ?></th>
                        </tr>
                        <tr>
                            <td colspan = "2">SERVICES</td>
                            <th colspan = "6"><?php echo $procedures; ?></th>
                        </tr>
                        <tr style = "text-align: center;">
                            <th colspan = "8">MEDICINES AGAINEST TOKEN</th>
                        </tr>
<?php
            $service = "SELECT id FROM `tokans` WHERE status = '2' AND `previous_tokan_no` = '$token_no' ";
            $run_service = mysqli_query($con, $service);
            if(mysqli_num_rows($run_service) > 0)
            {
                while($row_service = mysqli_fetch_array($run_service))
                {
                    $services = $row_service['0'];
                    echo '<tr><th colspan = "2">'.$services.'</th>';
                        $procedures_services = '';
                        $procedure_services = "SELECT items.name, item_by_doctor.dose, item_by_doctor.feed, item_by_doctor.days, item_by_doctor.fix_dose FROM `item_by_doctor` INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id WHERE item_by_doctor.tokan_no = '$services' ";
                        $run_procedure_services = mysqli_query($con, $procedure_services);
                        if(mysqli_num_rows($run_procedure_services) > 0)
                        {
                            while($row_procedure_services = mysqli_fetch_array($run_procedure_services))
                            {
                                $name = $row_procedure_services['name'];
                                $fix_dose = $row_procedure_services['fix_dose'];
                                if($fix_dose == 0)
                                {
                                    $dose = $row_procedure_services['dose'];
                                    $feed = $row_procedure_services['feed'];
                                    $days = $row_procedure_services['days'];
                                    $procedures_services .= $name . ' - '.intval($dose*$feed*$days).'</br>';
                                }
                                else
                                {
                                    $procedures_services .= $name . ' - '.$fix_dose.'</br>';
                                }
                            }
                        }
                    echo '<th colspan = "6">'.$procedures_services.'</th></tr>';
                }
            }
?>
                    </thead>
                </table>
            </div>
        </div>
<?php
        }
    }
        }
    }
}
?>
	</div>
</div>

</body>
</html>
<?php mysqli_close($con); ?>