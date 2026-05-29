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
.result
{
    font-size: 3.0em;
    font-family: sans-serif, "Helvetica Neue", "Lucida Grande", Arial; 
    font-stretch: ultra-expanded;
}
td {
    vertical-align: middle !important;
}    
    /* CSS to make the HR consistent and centered */
    hr.solid {
        border-top: 5px solid #333; /* A 1px solid gray line */
        border-radius: 5px;        /* Optional: gives soft edges */
        border-width: 5px 0 0 0;   /* Removes default bottom border/shadows */
        margin: 20px 0;            /* Adds spacing above and below the line */
    }

    hr.styled-line {
        height: 5px;         /* Make the line thicker */
        background-color: #333; /* Change the color to dark gray */
        border: none;        /* Remove default borders */
        width: 100%;          /* Make the line only half the page width */
        margin-left: auto;   /* Center the line horizontally */
        margin-right: auto;
    }
/* --- SCREEN STYLES (Watermark is hidden) --- */
.watermark-container {
    display: none; 
}

/* --- PRINT STYLES (Watermark appears) --- */
@media print 
{
    @page {
        size: A4;
        margin-bottom: 3cm; 

        @bottom-left 
        {
            content: "DR. MUHAMMAD RASHID IQBAL \A MBBS, M PHIL(PATHOLOGY) \A CONSULTANT PATHOLOGIST \A YCDO HOSPITAL NETWORK";
            font-size: 8pt;
            line-height: 1.2;
            white-space: pre-wrap; 
            border-top: 2px solid #000;
        }
        @bottom-center 
        {
            content:  "Project of\A YOUTH COMMUNITY DEVELOPMENT ORGANIZATION \AWebsite: www.ycdo.org.pk www.ycdo.com.pk \A UAN: +92 304 1110222, Phone # 03122827777\A Account #: UBL - 1500338414366";
            font-size: 8pt;
            line-height: 1.6;
            font-weight: bold;
            white-space: pre-wrap;
            border-top: 2px solid #000;
        }
        @bottom-right 
        {
            content: "DR. SANA JAMSHED\A MBBS, RMP, MCPS, MSPH \A CONSULTANT FAMILY PHYSICIAN \A YCDO HOSPITAL NETWORK";
            font-size: 8pt;
            line-height: 1.2;
            padding-left: 1cm;
            text-align: left;
            white-space: pre-wrap;
            border-top: 2px solid #000;
        }
    }
    body {
        /* Set your image as the background */
        background-image: url('images/logo-652.jpg');
        background-repeat: no-repeat;
        background-position: center center; /* Centers the image */
        background-size: 50%; /* Adjust size as needed */
        opacity: 0.9; /* This will make everything on the page transparent */
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
    }
    .text-warning
    {
        color: black !important;
    }
    .text-info
    {
        color: black !important;
    }
    .text-danger
    {
        color: black !important;
    }
    .text-primary
    {
        color: black !important;
    }
    .text-dark
    {
        color: black !important;
    }
 
</style>
</head>
<body>
<!--onload = "window.print()" onafterprint="window.close()"-->
<div>
    <div class = "row" style = "text-align: center;text-dark;">
        <div class = "col">
            <img src="images/logo-652.jpg" class="rounded float-left" width = "150px" height = "150px" alt="YCDO LOGO">
            <img src="qr/<?php echo $token_no;?>.png" class="rounded float-right" width = "120px" height = "120px" alt="QR CODE">
            <h1 style = "font-size: 65px;font-weight: 900;color: darkblue;text-align: center;">YCDO</h1>
            <div style = "font-size: 35px;font-weight: 900;color: black;text-align: center;"> Hospital & Diagnostic Centre </div>
            
            <hr style="height:1px;border-width:0;color:gray;background-color:black">
            <div class = "row">
                <div class = "col">
                    <div style = "font-weight: 600;text-align: center;">PATIENT TEST REPORT</div>
                </div>
                <div class = "col">
                    <div style = "font-weight: 600;text-align: center;">TOKEN NO: <?php echo $token_no; ?></div>
                </div>
            </div>
        </div>
    </div>
    <hr class="solid">
    <?php
    $select = "SELECT DISTINCT lab_tests.sample_date_time, items.name AS item_name, lab_tests.reporting_date_time, patients.name, patients.phone, patients.age, patients.cnic, genders.gender_title, tokans.created AS token_created_at, users.id AS sample_collected_by_id, users.u_name AS sample_collected_by, doctor.u_name AS doctor_name, reception_center.address AS reception_center_location FROM `tokans` INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN genders ON patients.gender = genders.gender_id INNER JOIN lab_tests ON tokans.id = lab_tests.token_no INNER JOIN users ON lab_tests.user_id = users.id INNER JOIN users doctor ON tokans.doctor_id = doctor.id INNER JOIN items ON lab_tests.item_id = items.id INNER JOIN branchs reception_center ON tokans.branch_id = reception_center.id WHERE tokans.id = '$token_no' LIMIT 0,1 ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run) == 1)
    {
        while($row = mysqli_fetch_array($run))
        {
            $item_name = $row['item_name'];
            $patient_sex = $row['gender_title'];
            ?>
            <table class = "p-2" style="background-color: transparent !important;">
                <tr>
                    <th width = "15%">Name</th>
                    <td width = "45%"> <?php echo $row['name'];?></td>
                    
                    <th width = "20%">Registration Time</th>
                    <td width = "20%"> <?php echo date_format(date_create($row['token_created_at']), "h:i:s A d-M-Y"); ?></td>
                </tr>
                <tr>
                    <th>Age / Sex</th>
                    <td>  <?php echo $row['age'];?> Y / <?php echo $row['gender_title'];?></td>
                    
                    <th>Registration Location</th>
                    <td> <?php echo $row['reception_center_location']; ?></td>
                    
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>  <?php if($row['phone'] == ''){echo 'N/A';}else{ echo $row['phone'];}?></td>
                    
                    <th>Specimen Received At</th>
                    <td> <?php echo date_format(date_create($row['sample_date_time']), "h:i:s A d-M-Y"); ?></td>
                </tr>
                <tr>
                    <th>CNIC</th>
                    <td>  <?php if($row['cnic'] == ''){echo 'N/A';}else{ echo $row['cnic'];}?></td>
                    
                    <th>Reporting Time</th>
                    <td> <?php echo date_format(date_create($row['reporting_date_time']), "h:i:s A d-M-Y"); ?></td>
                </tr>
                <tr>
                    <th>Consultant</th>
                    <td>  <?php if($row['doctor_name'] == ''){echo 'SELF';}else{ echo $row['doctor_name'];}?></td>
                    
                    <th>Collected By</th>
                    <td> <?php echo $row['sample_collected_by'].' - '.$row['sample_collected_by_id'];?></td>
                </tr>
                <tr>
                    <th>Processed At</th>
                    <td>  <?php echo $lab_login_branch_name;?></td>
                    
                    <th>Issued On</th>
                    <td> <?php echo date("h:i:s A d-M-Y"); ?></td>
                </tr>
            </table>
    <?php }
     } ?>
    
    <hr class="solid">
    
    <div class = "row" style = "min-height: 200px;">
        <div class = "col-md-9">
        <?php 
        $test_name = '';
        $select_parameter = "SELECT lab_test_reports.lab_test_report_result, items.name AS report_test_name, lab_test_reports.lab_reporting_test_id, `parameter_name`, `parameter_detail`, `lab_reporting_test_unit`, `lab_reporting_test_normal_value`, `lab_reporting_test_normal_male`, `lab_reporting_test_normal_female`, `lab_reporting_test_normal_childern`, lab_test_units.lab_test_unit_value, test_categories.test_category_title FROM lab_test_reports INNER JOIN lab_tests ON lab_test_reports.lab_test_id = lab_tests.lab_test_id INNER JOIN lab_reporting_tests ON lab_test_reports.lab_reporting_test_id = lab_reporting_tests.lab_reporting_test_id INNER JOIN lab_test_units ON lab_reporting_tests.lab_test_unit_id = lab_test_units.lab_test_unit_id INNER JOIN items ON lab_tests.item_id = items.id INNER JOIN test_categories ON lab_reporting_tests.lab_reporting_test_type = test_categories.test_category_id WHERE lab_tests.token_no = '$token_no' ";
        $run_parameter = mysqli_query($con, $select_parameter);
        $count_parameters = mysqli_num_rows($run_parameter);
        if(mysqli_num_rows($run_parameter) > 0)
        { ?>
    <table class = "table table-borderless text-center text-dark" style = "color: black'">
        <?php while($row_parameter = mysqli_fetch_array($run_parameter))
            {
                $lab_reporting_test_id = $row_parameter['lab_reporting_test_id'];
                $test_category_title = $row_parameter['test_category_title'];
                $parameter_name = $row_parameter['parameter_name'];
                $parameter_detail = $row_parameter['parameter_detail'];
                $report_test_name = $row_parameter['report_test_name'];
                    $report_test_name = str_replace('E1 ', '', $report_test_name);
                    $report_test_name = str_replace('E2 ', '', $report_test_name);
                    $report_test_name = str_replace('E3 ', '', $report_test_name);
                $class_detail = "text-success";
                $msg_if_hight = "";
                $msg_if_low = "";
                $lab_test_report_result = $row_parameter['lab_test_report_result'];
                $lab_test_unit_value = $row_parameter['lab_test_unit_value'];
                $lab_reporting_test_normal_value = $row_parameter['lab_reporting_test_normal_value'];
                $lab_reporting_test_normal_male = $row_parameter['lab_reporting_test_normal_male'];
                $lab_reporting_test_normal_female = $row_parameter['lab_reporting_test_normal_female'];
                $lab_reporting_test_normal_childern = $row_parameter['lab_reporting_test_normal_childern']; 

                $lab_reporting_test_normal_value_show = '';
                if ($lab_reporting_test_normal_value != '')
                {
                    $lab_reporting_test_normal_value_show .= str_replace(',', '</br>', $lab_reporting_test_normal_value). '</br>';
                }
                if ($lab_reporting_test_normal_male != '')
                {
                    $lab_reporting_test_normal_value_show .= 'Male: '.$lab_reporting_test_normal_male. '</br>';
                }
                if($lab_reporting_test_normal_female != '')
                {
                    $lab_reporting_test_normal_value_show .= 'Female: '. $lab_reporting_test_normal_female. '</br>';
                }
                if($lab_reporting_test_normal_childern != '')
                {
                    $lab_reporting_test_normal_value_show .= 'Childern: '. $lab_reporting_test_normal_female. '</br>';
                }
                
                $normal_values_array = explode('-', $lab_reporting_test_normal_value);
                $lab_reporting_test_normal_value_low = $normal_values_array[0];
                $lab_reporting_test_normal_value_high = $normal_values_array[1];
                if($lab_reporting_test_normal_value_low == $lab_test_report_result)
                {
                        $class_detail = "text-warning";
                        $msg_if_low = "<strong>	&#9888; </strong>";
                }
                elseif($lab_reporting_test_normal_value != '')
                {
                    if($lab_test_report_result > $lab_reporting_test_normal_value_high)
                    {
                        $class_detail = "text-danger";
                        $msg_if_hight = "<strong>&#9650; </strong>";
                    } 
                    elseif($lab_test_report_result < $lab_reporting_test_normal_value_low)
                    {
                        $class_detail = "text-primary";
                        $msg_if_low = "<strong>&#9660; </strong>";
                    }
                }
                else
                {
                    if($patient_sex == 'FEMALE')
                    {
                        $normal_values_array = explode('-', $lab_reporting_test_normal_female);
                        $lab_reporting_test_normal_value_low = $normal_values_array[0];
                        $lab_reporting_test_normal_value_high = $normal_values_array[1];
                        if($lab_test_report_result > $lab_reporting_test_normal_value_high)
                        {
                            $class_detail = "text-danger";
                            $msg_if_hight = "<strong>&#9650; </strong>";
                        } 
                        elseif($lab_test_report_result < $lab_reporting_test_normal_value_low)
                        {
                            $class_detail = "text-primary";
                            $msg_if_low = "<strong>&#9660; </strong>";
                        }
                    }
                    else
                    {
                        $normal_values_array = explode('-', $lab_reporting_test_normal_male);
                        $lab_reporting_test_normal_value_low = $normal_values_array[0];
                        $lab_reporting_test_normal_value_high = $normal_values_array[1];
                        if($lab_test_report_result > $lab_reporting_test_normal_value_high)
                        {
                            $msg_if_hight = "<strong>&#9650; </strong>";
                        } 
                        elseif($lab_test_report_result < $lab_reporting_test_normal_value_low)
                        {
                            $msg_if_low = "<strong>&#9660; </strong>";
                        }                        
                    }
                }
                if($report_test_name != $test_name)
                {
                    if($report_test_name == 'GLUCOSE RANDOM' ||$report_test_name == 'HBA1C')
                    {
                        echo '<tr class = "h2 text-left"><th colspan = "5">'.str_replace('E1 ', '', $report_test_name).' - '.$test_category_title.'</th></tr>
                        <tr>
                            <th width = "50%" colspan = "3">PARAMETER</th>
                            <th class = "text-center">';
                            echo '<div style = "font-size: 8px;background-color: white;font-weight: bold;text-align: center;"><span class = "text-info" style = "">&#9660; Normal (< '.$lab_reporting_test_normal_value_low.')</span><span class = "text-warning" style = "padding: 0px 35px;">&#9635; Pre-Diabetes ('.$lab_reporting_test_normal_value_low.' - '.$lab_reporting_test_normal_value_high.')</span><span class = "text-danger" style = "">&#9650; Diabetes (> '.$lab_reporting_test_normal_value_high.')</span></div>';
                            echo '</th>
                            <th>RESULT</th>
                        </tr>';
                        echo '<tr class = "align-middle text-center">            
                            <td rowspan = "2" colspan = "3" class = "text-left">
                                <dl>
                                    <dt class = "h3">'.$parameter_name.'</dt>
                                    <dd style = "text-align: justify;text-justify: inter-word;">'.$parameter_detail.'</dd>
                                </dl>
                            </td>';
                            if($report_test_name == 'GLUCOSE RANDOM')
                            {
                                echo '<td class = "align-middle text-center">';
                                include 'graphs/rbs.php';
                                echo '</td>';
                            } 
                            if($report_test_name == 'HBA1C')
                            {
                                echo '<td class = "align-middle text-right">';
                                include 'graphs/hba1c.php';
                               echo '</td>';
                            } 
                            echo '<td class = "text-left" style="font-weight: bold;">';
                            if($lab_test_report_result > $lab_reporting_test_normal_value_low && $lab_test_report_result < $lab_reporting_test_normal_value_high)
                            { 
                                echo '<div class = "text-info result">'.$lab_test_report_result.'<sub>'.$lab_test_unit_value.'</sub><div style = "font-size: 0.5em;">NORMAL</div></div>';
                            }
                            elseif($lab_test_report_result > $lab_reporting_test_normal_value_high)
                            { 
                                echo '<div class = "text-danger result">'.$lab_test_report_result.'<sub>'.$lab_test_unit_value.'</sub><div style = "font-size: 0.5em;">HIGH</div></div>';
                            }
                            elseif($lab_test_report_result < $lab_reporting_test_normal_value_low)
                            { 
                                echo '<div class = "text-warning result">'.$lab_test_report_result.'<sub>'.$lab_test_unit_value.'</sub><div style = "font-size: 0.5em;">LOW</div></div>';
                            }
                            else
                            { 
                                echo '<div class = "text-info result">'.$lab_test_report_result.'<div>'.$lab_test_unit_value.'</div></div>';
                            }
                            echo '</td>';
                        echo '</tr>'; 
                    }
                    else
                    {
                        echo '<tr class = "h2 text-left"><th colspan = "4">'.str_replace('E1 ', '', $report_test_name).' - '.$test_category_title.'</th></tr>
                        <tr>
                            <th>PARAMETER</th><th>UNIT</th><th>REFERENCE RANGE</th><th>RESULT</th><th>PREVIOUS-RESULT</th>
                        </tr>
                        <tr class = "align-middle text-center">            
                            <td>'.$parameter_name.'<td>
                            <td>'.$lab_test_unit_value.'</td>
                            <td class = "">'.$lab_reporting_test_normal_value_show.'</td>
                            <td class = "text-center" style="font-weight: bold;">';
                            if($msg_if_low != '' || $msg_if_hight != '')
                            { 
                                echo '<div style="font-size: 1.2em" class = " '.$class_detail.'" >'.$msg_if_low.$lab_test_report_result.$msg_if_hight.'</div>'; 
                            }
                            else
                            {
                                echo '<div class = "text-dark" style="font-size: 1.0em">'.$lab_test_report_result.'</div>';
                            }
                            echo '</td>';   
                        echo '</tr>'; 
                    }
                    $test_name = $report_test_name;
                }
                ?>
        <?php } ?>  
    </table>
        <?php } ?>
            <div class = "text-left" style = "text-align: left;" colspan = "4"></br>
                Note: 
                <span class = "text-danger">&#9650; this icon shows for high values.</span> 
                <span class = "text-primary">&#9660; this icon shows for low values. </span> 
                <span class = "text-warning">&#9888; this icon shows borderline values.</span>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>