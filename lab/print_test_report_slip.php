<?php
    include('includes/config.php');
    include('includes/connect.php');
if(isset($_GET['token_no']) && $_GET['token_no'] != '')
{
    $token_no = $_GET['token_no'];
}
else
{
    header('location: logout.php');
}
?>
<?php 
include 'includes/head.php'; 
include 'includes/phpqrcode/qrlib.php';
if(isset($_GET['token_no']) && $_GET['token_no'] != '')
{
    $token_no = $_GET['token_no'];
    $path = 'qr/'.$token_no;
    $file = $path.".png";
    $ecc = 'L';
    $pixel_Size = 10;
    $frame_Size = 10;
    if (!is_dir('qr/')) {
        mkdir('qr/', 0777, true);
    }
    QRcode::png($token_no, $file, $ecc, $pixel_Size, $frame_Size);    
}
?>
  <title>PRINT LAB TOKEN</title>
</head>
<body onload = "window.print()">
    <!-- onafterprint="window.close()">-->
<div>
    <div class = "row" style = "text-align: center;">
        <div class = "col">
            <img src="images/logo-652.jpg" class="rounded float-left" width = "120px" height = "120px" alt="YCDO LOGO">
            <img src="qr/<?php echo $token_no;?>.png" class="rounded float-right" width = "120px" height = "120px" alt="QR CODE">
            <h2>YCDO Hospital & Diagnostic Centre</h2>
            <h3>PATIENT BILL</h3>
            <hr style="height:1px;border-width:0;color:gray;background-color:black">
            <h4>TOKEN NO: <?php echo $token_no; ?></h4>
        </div>
    </div>
    <hr style="height:2px;border-width:0;color:gray;background-color:black">
    <?php
    $select = "SELECT DISTINCT lab_tests.sample_date_time, lab_tests.reporting_date_time, patients.name, patients.phone, patients.age, patients.cnic, genders.gender_title, tokans.created AS token_created_at, users.id AS sample_collected_by_id, users.u_name AS sample_collected_by, doctor.u_name AS doctor_name, branchs.address AS specimen_location, reception_center.address AS reception_center_location  FROM `tokans` INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN genders ON patients.gender = genders.gender_id INNER JOIN lab_tests ON tokans.id = lab_tests.token_no INNER JOIN users ON lab_tests.user_id = users.id INNER JOIN users doctor ON tokans.doctor_id = doctor.id INNER JOIN branchs ON tokans.branch_id = branchs.id INNER JOIN branchs reception_center ON tokans.branch_id = reception_center.id WHERE tokans.id = '$token_no' limit 0, 1 ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run) == 1)
    {
        while($row = mysqli_fetch_array($run))
        {
    ?>
            <table class = "p-2" style="background-color: transparent !important;">
                <tr>
                    <th width = "15%">Name</th>
                    <td width = "45%">: <?php echo $row['name'];?></td>
                    
                    <th width = "20%">Registration Time</th>
                    <td width = "20%">: <?php echo date_format(date_create($row['token_created_at']), "h:i:s A d-M-Y"); ?></td>
                </tr>
                <tr>
                    <th>Age / Sex</th>
                    <td>:  <?php echo $row['age'];?> Y / <?php echo $row['gender_title'];?></td>
                    
                    <th>Registration Location</th>
                    <td>: <?php echo $row['reception_center_location']; ?></td>
                    
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>:  <?php if($row['phone'] == ''){echo 'N/A';}else{ echo $row['phone'];}?></td>
                    
                    <th>Specimen Received At</th>
                    <td>: <?php echo date_format(date_create($row['sample_date_time']), "h:i:s A d-M-Y"); ?></td>
                </tr>
                <tr>
                    <th>CNIC</th>
                    <td>:  <?php if($row['cnic'] == ''){echo 'N/A';}else{ echo $row['cnic'];}?></td>
                    
                    <th>Reporting Time</th>
                    <td>: <?php echo date_format(date_create($row['reporting_date_time']), "h:i:s A d-M-Y"); ?></td>
                </tr>
                <tr>
                    <th>Consultant</th>
                    <td>:  <?php if($row['doctor_name'] == ''){echo 'SELF';}else{ echo $row['doctor_name'];}?></td>
                    
                    <th>Collected By</th>
                    <td>: <?php echo $row['sample_collected_by'].' - '.$row['sample_collected_by_id'];?></td>
                </tr>
                <tr>
                    <th>Processed At</th>
                    <td>:  <?php echo $lab_login_branch_name;?></td>
                    
                    <th>Issued On</th>
                    <td>: <?php echo date("h:i:s A d-M-Y"); ?></td>
                </tr>
            </table>
    <?php }
     } ?>
    
    <hr style="height:2px;border-width:0;color:gray;background-color:black">
    
    <div class = "row" style = "min-height: 300px;">
        <div class = "col-md-12">
            <table class = "table-sm" style = "border: none;">
                <thead>
                    <tr class = "text-center" style = "text-decoration: underline; text-decoration-style: double; text-align: center;">
                        <th width = "10%">TEST NAME</th>
                        <th width = "60%"></th>
                        <th width = "20%" style = "border-left: 1px solid #000; border-right: 1px solid #000;">REPORTING TIME & DATE</th>
                        <th class = "text-right" width = "10%">PRICE</th>
                    </tr>
                </thead>
                <tbody>
            <?php 
            $ser = 0;
            $total_bill = 0;
            $run = mysqli_query($con, "SELECT lab_tests.lab_test_id, lab_tests.item_id, items.name, lab_tests.lab_test_rate, lab_tests.reporting_date_time FROM lab_tests INNER JOIN items ON lab_tests.item_id = items.id WHERE lab_tests.token_no = '$token_no' ");
            if (mysqli_num_rows($run) > 0) 
            {
                while ($row = mysqli_fetch_array($run)) 
                {
                    $item_id = $row['item_id'];
                    $total_bill = $row['lab_test_rate'] + $total_bill;
                    $ser = $ser + 1;
                        echo '<tr>';
                            echo '<td class = "text-right">'.$ser.'</td>';
                            echo '<td style="border-left: 1px solid #000; padding: 10px;">'.$row['name'];
                            $parameter_name = "SELECT `parameter_name` FROM `lab_reporting_tests` WHERE `item_id` = '$item_id' ";
                            $run_parameter_name = mysqli_query($con, $parameter_name);
                            if(mysqli_num_rows($run_parameter_name) > 0)
                            {
                                echo '</br>&nbsp;&nbsp;&nbsp;&nbsp;';
                                while($row_parameter_name = mysqli_fetch_array($run_parameter_name))
                                {
                                    echo $row_parameter_name['parameter_name'].'&nbsp;';
                                }
                            }
                            echo '</td>';
                            echo '<td style="border-left: 1px solid #000; padding: 3px; border-right: 1px solid #000;" class = "text-center">'.date_format(date_create($row['reporting_date_time']), "H:i:s d-m-Y").'</td>';
                            echo '<td class = "text-right">'.$row['lab_test_rate'].'</td>';
                            echo '</tr>';
                }    
            } ?>
                </tbody>
                <tr><th colspan = "4"><hr></th></tr>
                <tr class = "text-right">
                    <th colspan = "2"></th>
                    <th style="padding: 3px; border: 1px solid #000;">TOTAL BILL</th>
                    <th style="padding: 3px; border: 1px solid #000;"><?php echo number_format((float)($total_bill ?? 0)); ?></th>
                </tr>
            </table>
        </div>
    </div>
                    
    <hr style="height:2px;border-width:0;color:gray;background-color:black">
    <div class = "row" style = "text-align: center;">
        <div class = "col">
            <h6>UAN: +92 304 1110222, Phone # <?php echo $lab_login_branch_phone; ?></h6>
        </div>
        <div class = "col">
            <h6>Website: <i>www.ycdo.org.pk</i> <i>www.ycdo.com.pk</i></h6>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>