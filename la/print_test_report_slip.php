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
<style>
/*figure {*/
/*  border: 1px #cccccc solid;*/
/*  padding: 4px;*/
/*  margin: auto;*/
/*}*/

/*figcaption {*/
/*  background-color: black;*/
/*  color: white;*/
/*  font-style: italic;*/
/*  padding: 2px;*/
/*  text-align: center;*/
/*}*/
.table1 
{
    border-collapse: collapse;
}
.td1, .th1 
{
    border: 1px solid black;
}
</style>
</head>
<body onload = "window.print()">
<div>
    <div class = "row" style = "text-align: center;">
        <div class = "col-ms-1" style = "min-width: 200px;max-width: 200px;min-height: 200px;max-height: 200px;">
            <figure>
                <img src="images/label.jpg" alt="YCDO" width="180px" height="140px"/>
                <figcaption style = "font-size: 30px;font-weight: bold;">YCDO<sup>&reg;</sup></figcaption>
                <figcaption style = "font-size: 16px;">SERVE HUMANITY</figcaption>
            </figure>            
        </div>
        <div class = "col-ms-10" style = "min-width: 650px;max-width: 650px;min-height: 200px;max-height: 200px;">
            <h3>YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</h3>
            <hr style="height:1px;border-width:0;color:gray;background-color:black">
            <h4><?php echo $lab_login_branch_name; ?></h4>
            <h4><?php echo $lab_login_branch_address; ?></h4>
            <hr style="height:1px;border-width:0;color:gray;background-color:black">
            <h5>UAN: +92 304 1110222, Phone # <?php echo $lab_login_branch_phone; ?></h5>
            <h1>REPORTING SLIP</h1>
        </div>
        <div class = "col-ms-1" style = "min-width: 200px;max-width: 200px;min-height: 200px;max-height: 200px;">
            <figure>
            <img src="qr/<?php echo $token_no;?>.png" alt="QR CODE" width="200px" height="180px"/>
                <figcaption style = "font-size: 16px;">T/NO: <strong><?php echo $token_no; ?></strong></figcaption>
            </figure>
        </div>
    </div>
    <hr style="height:2px;border-width:0;color:gray;background-color:black">
    <?php
    $select = "SELECT DISTINCT lab_tests.sample_date_time, lab_tests.reporting_date_time, patients.name, patients.phone, patients.age, patients.cnic, genders.gender_title, tokans.created AS token_created_at, users.u_name AS sample_collected_by FROM `tokans` INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN genders ON patients.gender = genders.gender_id INNER JOIN lab_tests ON tokans.id = lab_tests.token_no INNER JOIN users ON lab_tests.user_id = users.id WHERE tokans.id = '$token_no' ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run) == 1)
    {
        while($row = mysqli_fetch_array($run))
        {
    ?>
    <div class = "row">
        <div class = "col">
            <table>
                <tr>
                    <td width = "10%">NAME</td>
                    <th width = "58%">: <?php echo $row['name'];?></th>
                    
                    <td width = "15%">TOKEN TIME</td>
                    <th width = "17%">: <?php echo date_format(date_create($row['token_created_at']), "h:i:s A d-M-Y"); ?></th>
                </tr>
                <tr>
                    <td>AGE</td>
                    <th>:  <?php echo $row['age'];?> Y</th>
                    
                    <td>SAMPLE RECEIVED AT</td>
                    <th>: <?php echo date_format(date_create($row['sample_date_time']), "h:i:s A d-M-Y"); ?></th>
                </tr>
                <tr>
                    <td>GENDER</td>
                    <th>:  <?php echo $row['gender_title'];?></th>
                    
                    <td>REPORTING TIME</td>
                    <th>: <?php echo date_format(date_create($row['reporting_date_time']), "h:i:s A d-M-Y"); ?></th>
                    
                </tr>
                <tr>
                    <td>PHONE</td>
                    <th>:  <?php echo $row['phone'];?></th>
                    
                    <td rowspan = "2">SAMPLE COLLECTED BY</td>
                    <th rowspan = "2">: <?php echo $row['sample_collected_by'];?></th>
                </tr>
                <tr>
                    <td>CNIC</td>
                    <th>:  <?php echo $row['cnic'];?></th>
                
                </tr>
            </table>
        </div>
    </div>
    <?php }
     } ?>
    
    <hr style="height:2px;border-width:0;color:gray;background-color:black">
    
    <div class = "row" style = "min-height: 400px;">
        <div class = "col">
            <table class = "table table-sm table1" style = "border-color: black;">  
            <thead  class="bg-warning">
                <tr class = "td1">
                    <th class = "td1">S #</th>
                    <th class = "td1">Test Name</th>
                    <th class = "td1">Normal Values</th>
                    <th class = "td1">Test Result</th>
                </tr>
            </thead>
                    <?php echo get_given_services_by_token_no($token_no); ?>
            </table>
        </div>
    </div>
                    
    <hr style="height:2px;border-width:0;color:gray;background-color:black">
    <div class = "row" style = "text-align: center;">
        <div class = "col">
            <h5>YCDO EXECUTIVE HOSPITAL I</h5>
            <h6>Ghanta Ghar Chowk, Multan</h6>
            <h6>0312-2827777</h6>
        </div>
        <div class = "col">
            <h5>YCDO EXECUTIVE HOSPITAL II</h5>
            <h6>Chungi No 9, La-Salle School,Bosan Road, Multan</h6>
            <h6>0315-2827777</h6>
        </div>
        <div class = "col">
            <h5>YCDO Central Hospital</h5>
            <h6>Masoom Shah Road, Multan</h6>
            <h6>0304-2827777</h6>
        </div>
        <div class = "col">
            <h5>POLICE & YCDO</h5>
            <h6>DRUG REHABILITATION HOSPITAL</h6>
            <h6>0329-2827777</h6>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>