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
function header_data($token_no)
{
    // <div style = "font-size: 35px;font-weight: 900;color: black;text-align: left;"> Hospital & Diagnostic Centre </div>
                // <div style = "font-size: 25px;font-weight: 800;color: black;text-align: left;" class = "h5 col-sm-8"></br>Hospital & Diagnostic Centre</div>
    $con = $GLOBALS['con'];
    $output = '';
    $output .= '
    <div class = "row" style = "text-align: center;text-dark;">
        <div class = "col">
            <img src="images/logo-652.jpg" class="rounded float-left" width = "150px" height = "150px" alt="YCDO LOGO">
            <img src="qr/'.$token_no.'.png" class="rounded float-right" width = "120px" height = "120px" alt="qr/'.$token_no.'.png">
            <div class = "row">
                <div style = "font-size: 35px;font-weight: 900;color: darkblue;text-align: left;" class = "h2 col-sm-12">
                    <span class = "h2" style = "font-size: 35px;font-weight: 900; color: #B80000;">YCDO</span> 
                    <span class = "h2" style = "font-size: 35px;font-weight: 900; color: #23297A;">Hospital</span> 
                    <span class = "h2" style = "font-size: 35px;font-weight: 900; color: black;">& Diagnostic Centre</span>
                </div>
            </div>
            
            <div class = "row" style = "text-align: center;font-weight: 900; color: gray;">
                <div class = "col-sm-12">
                    <hr style="height:1px;border-width:0;color:gray;background-color:gray">
                    <h3 style = "font-weight: 600; color: black;"> PATIENT TEST REPORT</h3>
                </div>
                <div class = "col-sm-12">
                    <hr style="height:1px;border-width:0;color:gray;background-color:gray">
                    <h4 style = "font-weight: 500; color: black;"> TOKEN NO: '.$token_no.'</h4>
                </div>
            </div>        
        </div>
    </div>';

    $select = "SELECT DISTINCT lab_tests.sample_date_time, items.name AS item_name, lab_tests.reporting_date_time, patients.name, patients.phone, patients.age, patients.cnic, patients.gender, genders.gender_title, tokans.created AS token_created_at, users.id AS sample_collected_by_id, users.u_name AS sample_collected_by, doctor.u_name AS doctor_name, reception_center.address AS reception_center_location FROM `tokans` INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN genders ON patients.gender = genders.gender_id INNER JOIN lab_tests ON tokans.id = lab_tests.token_no INNER JOIN users ON lab_tests.user_id = users.id INNER JOIN users doctor ON tokans.doctor_id = doctor.id INNER JOIN items ON lab_tests.item_id = items.id INNER JOIN branchs reception_center ON tokans.branch_id = reception_center.id WHERE tokans.id = '$token_no' LIMIT 0,1 ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run) == 1)
    {
        while($row = mysqli_fetch_array($run))
        {
            $item_name = $row['item_name'];
            $patient_sex = $row['gender_title'];
            $patient_gender_id = $row['gender'];
            $GLOBALS['patient_gender_id'] = $patient_gender_id;
            
        // <th class = "text-center" style = "text-align: center;" width = "20%">&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;</th>
$output .= '<hr style="height:1px;border-width:0;color:gray;background-color:black">
<table class = "p-2 text-left table-sm table table-light" style="background-color: transparent !important;">
    <tr>
        <th width = "15%">Name</th>
        <td width = "45%"> '.$row['name'].'</td>

        <th width = "20%">Registration Time</th>
        <td width = "20%"> '.date_format(date_create($row['token_created_at']), "h:i:s A d-M-Y").'</td>
    </tr>
    <tr>
        <th>Age / Sex</th>
        <td>  '.$row['age'].' Y / '.$row['gender_title'].'</td>
    
        <th>Registration Location</th>
        <td> '.$row['reception_center_location'].'</td>
    </tr>
    <tr>
        <th>Phone</th>
        <td>'; 
        if($row['phone'] == '')
        {
            $output .= 'N/A';
            
        }
        else
        { 
            $output .= $row['phone'];
        }
        $output .= '</td>
        
        <th>Specimen Received At</th>
        <td> '.date_format(date_create($row['sample_date_time']), "h:i:s A d-M-Y").'</td>
    </tr>
    <tr>
        <th>CNIC</th>
        <td>';
        if($row['cnic'] == '')
        {
            $output .= 'N/A';
        }
        else
        {
            $output .= $row['cnic'];
        }
        $output .= '</td>
        
        <th>Reporting Time</th>
        <td> '.date_format(date_create($row['reporting_date_time']), "h:i:s A d-M-Y").'></td>
    </tr>
    <tr>
        <th>Consultant</th>
        <td>';  
        if($row['doctor_name'] == '')
        {
            $output .= 'SELF';
        }
        else
        {
            $output .= $row['doctor_name'];
        }
        $output .= '</td>
        
        <th>Collected By</th>
        <td> '.$row['sample_collected_by'].' - '.$row['sample_collected_by_id'].'</td>
    </tr>
    <tr>
        <th>Processed At</th>
        <td>  '.$lab_login_branch_name.'</td>
        
        
        <th>Issued On</th>
        <td> '.date("h:i:s A d-M-Y").'</td>
    </tr>
</table><hr style="height:1px;border-width:0;color:gray;background-color:black">';
        }
    }
    return $output;
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
    @page 
    {
        size: A4;
        margin-bottom: 3cm; 
        font-size: 0.8em;
      
        @top-right 
        {
            /*content: "PRINT PAGE";*/
            content: "PRINT PAGE: " counter(page) ' of ' counter(pages);
            font-size: 7pt;
            line-height: 1.0;
            white-space: pre-wrap; 
        }        
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
.pagenum:before { content: counter(page); }
</style>
</head>
<body>
<!--onload = "window.print()" onafterprint="window.close()"-->
<div>

    <div class = "row">
        <div class = "col-sm-12 text-center">
            <?php
            $counter = 0;
            $display = 0;
            $is_display = 0;
            $select_token = "SELECT lab_test_id, name, test_categories.test_category_title, COUNT(lab_reporting_tests.lab_reporting_test_id) FROM `lab_tests` INNER JOIN items ON lab_tests.item_id = items.id INNER JOIN lab_reporting_tests ON items.id = lab_reporting_tests.item_id INNER JOIN test_categories ON lab_reporting_tests.lab_reporting_test_type = test_categories.test_category_id WHERE `token_no` = '$token_no' AND lab_tests.lab_test_status_id = 7 GROUP BY lab_tests.lab_test_id ORDER BY `COUNT(lab_reporting_tests.lab_reporting_test_id)` ASC ;";
            $run_token = mysqli_query($con, $select_token);
            $numResults = mysqli_num_rows($run_token);
            if(mysqli_num_rows($run_token) > 0)
            {
                while($row_token = mysqli_fetch_array($run_token))
                {
                    $lab_test_id = $row_token['lab_test_id'];
                    $test_category_title = $row_token['test_category_title'];
                    $count_tests = $row_token['3'];
                    $report_test_name = $row_token['name'];
                    $report_test_name = str_replace('E1 ', '', $report_test_name);
                    $report_test_name = str_replace('E2 ', '', $report_test_name);
                    $report_test_name = str_replace('E3 ', '', $report_test_name);
                    
                    if($is_display == 0)
                    {
                        echo '<div class = "d-none d-print-block">'.header_data($token_no).'</div>';
                        $is_display = 1;
                    }
                    $patient_gender_id = $GLOBALS['patient_gender_id'];
                    
                    if($report_test_name == 'GLUCOSE RANDOM' || $report_test_name == 'HBA1C')
                    {
                        $display = 1;
                        $select_test_report = "SELECT * FROM `lab_test_reports` INNER JOIN lab_reporting_tests ON lab_test_reports.lab_reporting_test_id = lab_reporting_tests.lab_reporting_test_id INNER JOIN lab_test_units ON lab_reporting_tests.lab_test_unit_id = lab_test_units.lab_test_unit_id WHERE lab_test_reports.lab_test_id = '$lab_test_id' ";
                        $run_test_report = mysqli_query($con, $select_test_report);
                        if(mysqli_num_rows($run_test_report) > 0)
                        {
                            echo '<table class = "table" >';
                            echo '<caption style = "color: black; caption-side: top;"><h2 class ="text-left">'.$report_test_name.' - '.$test_category_title.'</h1></caption>';
                            while($row_test_report = mysqli_fetch_array($run_test_report))
                            {
                                $lab_test_report_result = $row_test_report['lab_test_report_result'];
                                
                                // NORMAL FOR ALL
                                $reference_range = $row_test_report['lab_reporting_test_normal_value'];
                                $lab_reporting_test_normal_value = $reference_range;
                                
                                // NORMAL FOR FEMALE
                                $reference_range_female = $row_test_report['lab_reporting_test_normal_female'];
                                $lab_reporting_test_normal_value_female = $reference_range_female;
                                
                                // NORMAL FOR MALE
                                $reference_range_male = $row_test_report['lab_reporting_test_normal_male'];
                                $lab_reporting_test_normal_value_male = $reference_range_male;
                                if($lab_reporting_test_normal_value != '')
                                {
                                    $normal_values_array = explode('-', $lab_reporting_test_normal_value);
                                    $lab_reporting_test_normal_value_low = $normal_values_array[0];
                                    $lab_reporting_test_normal_value_high = $normal_values_array[1];
                                }
                                else
                                {
                                    if($reference_range_female != '')
                                    {
                                        $normal_values_array = explode('-', $lab_reporting_test_normal_value_female);
                                        $lab_reporting_test_normal_value_low = $normal_values_array[0];
                                        $lab_reporting_test_normal_value_high = $normal_values_array[1];
                                    } 
                                    else
                                    {
                                        $normal_values_array = explode('-', $lab_reporting_test_normal_value_male);
                                        $lab_reporting_test_normal_value_low = $normal_values_array[0];
                                        $lab_reporting_test_normal_value_high = $normal_values_array[1];
                                    }                                    
                                }
                                $lab_test_unit_value = $row_test_report['lab_test_unit_value'];
                                echo '<tr><th>PARAMETERS</th><th>UNIT</th><th>';
                                
                                if($report_test_name == 'GLUCOSE RANDOM')
                                {
                                    echo '
                                    <div style = "font-size: 8px;background-color: white;font-weight: bold;text-align: center;max-width: 400px;">
                                        <span class = "text-warning" style = "">&#9660; Low (< '.$lab_reporting_test_normal_value_low.')</span>
                                        <span class = "text-info" style = "padding: 0px 35px;">&#9635; Normal ('.$lab_reporting_test_normal_value_low.' - '.$lab_reporting_test_normal_value_high.')</span>
                                        <span class = "text-danger" style = "">&#9650; High (> '.$lab_reporting_test_normal_value_high.')</span>
                                    </div>';
                                }
                                elseif($report_test_name == 'HBA1C')
                                {
                                    echo '
                                    <div style = "font-size: 7px;background-color: white;font-weight: bold;text-align: center;min-width: 450px;;max-width: 450px;">
                                        <span class = "text-warning" style = "">&#9660; Normal (< '.$lab_reporting_test_normal_value_low.')</span>
                                        <span class = "text-info" style = "padding: 0px 35px;">&#9635; Pre-Diabetes ('.$lab_reporting_test_normal_value_low.' - '.$lab_reporting_test_normal_value_high.')</span>
                                        <span class = "text-danger" style = "">&#9650; Diabetes (> '.$lab_reporting_test_normal_value_high.')</span>
                                    </div>';
                                }
                                else
                                {
                                    echo '
                                    <div style = "font-size: 8px;background-color: white;font-weight: bold;text-align: center;min-width: 450px;;max-width: 450px;">
                                        <span class = "text-warning" style = "">&#9660; Low (< '.$lab_reporting_test_normal_value_low.')</span>
                                        <span class = "text-info" style = "padding: 0px 25px;">&#9635; Normal ('.$lab_reporting_test_normal_value_low.' - '.$lab_reporting_test_normal_value_high.')</span>
                                        <span class = "text-danger" style = "">&#9650; High (> '.$lab_reporting_test_normal_value_high.')</span>
                                    </div>';
                                }
                                
                                echo '</th><th class = "text-left">RESULT</th></tr>';
                                echo '<tr><td class = "text-left">
                                    <dt>'.$row_test_report['parameter_name'].'</dt>
                                    <dd>'.$row_test_report['parameter_detail'].'</dd>
                                </td><td>'.$row_test_report['lab_test_unit_value'].'</td>';
                                echo '<td class = "align-middle text-center" style="font-weight: bold;">';
                                
                                if($report_test_name == 'GLUCOSE RANDOM')
                                {
                                    include 'graphs/test_rbs.php';
                                }
                                elseif($report_test_name == 'HBA1C')
                                {
                                    include 'graphs/test_hb1c.php';
                                }
                                // else
                                // {
                                //     include 'graphs/test_rbs.php';
                                // }
                                echo '</td><td>';

                                    $lab_test_report_result = strtoupper($lab_test_report_result);
                                    if($lab_test_report_result == 'NIL' || $lab_test_report_result == 'TRACES' || $lab_test_report_result == 'NEGATIVE' || $lab_test_report_result == 'POSITIVE' || $lab_test_report_result == '-')
                                    {
                                        $class_detail = "text-dark";    $msg_if_low = '';   $msg_if_hight = ''; 
                                    }
                                    elseif(str_contains($lab_test_report_result, '-'))
                                    {
                                        $class_detail = "text-dark";    $msg_if_low = '';   $msg_if_hight = ''; 
                                    }
                                    elseif(str_contains($lab_test_report_result, '+') || str_contains($lab_test_report_result, 'POSITIVE') ||str_contains($lab_test_report_result, 'RED') || str_contains($lab_test_report_result, 'DARK BROWN') || str_contains($lab_test_report_result, 'ORANGE') || str_contains($lab_test_report_result, 'BLUE') || str_contains($lab_test_report_result, 'GREEN') || str_contains($lab_test_report_result, 'CLOUDY') || str_contains($lab_test_report_result, 'MILKY'))
                                    {
                                        $class_detail = "text-DANGER";    $msg_if_low = '';   $msg_if_hight = ''; 
                                    }
                                    elseif(str_contains($lab_test_report_result, 'CLEAR') || str_contains($lab_test_report_result, 'PALE YELLOW') || str_contains($lab_test_report_result, 'YELLOW') || str_contains($lab_test_report_result, 'DARK YELLOW') || str_contains($lab_test_report_result, 'DARK AMBER'))
                                    {
                                        $class_detail = "text-dark";    $msg_if_low = '';   $msg_if_hight = ''; 
                                    }
                                    elseif($lab_reporting_test_normal_value_high_range == 'NIL')
                                    {
                                        $class_detail = "text-danger";    $msg_if_low = '';   $msg_if_hight = ''; 
                                    }
                                    elseif($lab_reporting_test_normal_value_high_range == '')
                                    {
                                        if($patient_gender_id == 2) // MALE
                                        {
                                            if(intval($lab_test_report_result ?? 0) < $lab_reporting_test_normal_value_low_male)
                                            {
                                                $class_detail = "text-success";    $msg_if_low = '';   $msg_if_hight = '';  
                                            }
                                            elseif(intval($lab_test_report_result ?? 0) >= $lab_reporting_test_normal_value_high_male)
                                            {
                                                $class_detail = "text-danger";    $msg_if_low = '';   $msg_if_hight = '';  
                                            }
                                            else
                                            {
                                                $class_detail = "text-dark";    $msg_if_low = '';   $msg_if_hight = '';  
                                            }
                                        }
                                        elseif($patient_gender_id == 1) // FEMALE
                                        {
                                            
                                        }
                                        else // TRANSGENDER
                                        {
                                            
                                        }
                                         
                                    }
                                    elseif($lab_test_report_result < $lab_reporting_test_normal_value_low)
                                    {
                                        $class_detail = "text-success h5";  $msg_if_low = '<strong>	&#9660; </strong>'; $msg_if_hight = '';
                                    }
                                    elseif($lab_test_report_result >= $lab_reporting_test_normal_value_high)
                                    {
                                        
                                        $class_detail = "text-danger h5";   $msg_if_low = '';  $msg_if_hight = '<strong>&#9650; </strong>';
                                    }
                                    else
                                    {
                                        $class_detail = "text-dark";    $msg_if_low = '';   $msg_if_hight = ''; 
                                    }
                                        // echo '<td class = "'.$class_detail.'">'.$msg_if_low.$lab_test_report_result.$msg_if_hight.'</td>';

                                // if($lab_test_report_result > $lab_reporting_test_normal_value_low && $lab_test_report_result < $lab_reporting_test_normal_value_high)
                                // { 
                                //     echo '<div class = "text-left text-info result"><span class = "" style = "font-size: 60px;">'.$lab_test_report_result.'</span><span style = "font-size: 15px; font-weight: normal;">'.$lab_test_unit_value.'</span><div style = "font-weight: 900;text-align: left; font-size: 0.5em;">NORMAL</div></div>';
                                // }
                                // elseif($lab_test_report_result > $lab_reporting_test_normal_value_high)
                                // { 
                                //     echo '<div class = "text-left text-danger result"><span class = "" style = "font-size: 60px;">'.$lab_test_report_result.'</span><span style = "font-size: 15px; font-weight: normal;">'.$lab_test_unit_value.'</span><div style = "font-weight: 900;text-align: left; font-size: 0.5em;">HIGH</div></div>';
                                // }
                                // elseif($lab_test_report_result < $lab_reporting_test_normal_value_low)
                                // { 
                                //     echo '<div class = "text-left text-warning result"><span class = "" style = "font-size: 60px;">'.$lab_test_report_result.'</span><span style = "font-size: 15px; font-weight: normal;">'.$lab_test_unit_value.'</span><div style = "font-weight: 900;text-align: left; font-size: 0.5em;">LOW</div></div>';
                                // }
                                // else
                                // { 
                                //     echo '<div class = "text-left text-info result"><span class = "" style = "font-size: 60px;">'.$lab_test_report_result.'</span><div>'.$lab_test_unit_value.'</div></div>';
                                // }
                                echo '</tr>';
                            }
                            echo '</table>';

                                $msg_if_low = '';
                                $msg_if_hight = '';
                                $class_detail = '';
                                $lab_reporting_test_normal_value_high_range = '';
                                $lab_reporting_test_normal_value_high_female_range = '';
                                $lab_reporting_test_normal_value_high_male_range = '';                        } 
                    }
                    else
                    {
                        if ($count_tests > 1 || $display = 1) 
                        {
                                echo '<div style="break-after:page"></div>';
                                echo '<div class = "d-none d-print-block">'.header_data($token_no).'</div>';
                        }
                        $select_test_report = "SELECT * FROM `lab_test_reports` INNER JOIN lab_reporting_tests ON lab_test_reports.lab_reporting_test_id = lab_reporting_tests.lab_reporting_test_id INNER JOIN lab_test_units ON lab_reporting_tests.lab_test_unit_id = lab_test_units.lab_test_unit_id WHERE lab_test_reports.lab_test_id = '$lab_test_id' ";
                        $run_test_report = mysqli_query($con, $select_test_report);
                        if(mysqli_num_rows($run_test_report) > 0)
                        {
                            $msg_if_low = '<strong>	&#9888; </strong>';
                            $msg_if_hight = '<strong>&#9650; </strong>';
                            echo '<table class = "table">';
                            echo '<caption style = "color: black; caption-side: top;"><h2 class ="text-left">'.$report_test_name.' - '.$test_category_title.'</h1></caption>';
                            echo '<tr><th>PARAMETERS</th><th>UNIT</th><th>REFERENECE RANGE</th><th>RESULT</th></tr>';
                            while($row_test_report = mysqli_fetch_array($run_test_report))
                            {
                                $lab_reporting_test_normal_value_high_range = '';
                                $lab_reporting_test_normal_value_high_female_range = '';
                                $lab_reporting_test_normal_value_high_male_range = '';
                                $lab_reporting_test_normal_value_low = '';
                                $lab_reporting_test_normal_value_high = '';
                                $lab_reporting_test_normal_value_low_female = '';
                                $lab_reporting_test_normal_value_high_female = '';
                                $lab_reporting_test_normal_value_low_male = '';
                                $lab_reporting_test_normal_value_high_male = '';

                                $lab_test_report_result = $row_test_report['lab_test_report_result'];
                                // NORMAL FOR ALL
                                $reference_range = $row_test_report['lab_reporting_test_normal_value'];
                                $lab_reporting_test_normal_value = $reference_range;
                                
                                // NORMAL FOR FEMALE
                                $reference_range_female = $row_test_report['lab_reporting_test_normal_female'];
                                $lab_reporting_test_normal_value_female = $reference_range_female;
                                
                                // NORMAL FOR MALE
                                $reference_range_male = $row_test_report['lab_reporting_test_normal_male'];
                                $lab_reporting_test_normal_value_male = $reference_range_male;
                                if($lab_reporting_test_normal_value != '')
                                {
                                    $normal_values_array = explode('-', $lab_reporting_test_normal_value);
                                    $lab_reporting_test_normal_value_low = strtoupper($normal_values_array[0]);
                                    $lab_reporting_test_normal_value_high = strtoupper($normal_values_array[1]);
                                    if($lab_reporting_test_normal_value_low == "NIL" OR $lab_reporting_test_normal_value_low == "NIL " OR $lab_reporting_test_normal_value_low == "NEGATIVE")
                                    {
                                        $lab_reporting_test_normal_value_high_range = $lab_reporting_test_normal_value_low;
                                    }
                                    else
                                    {
                                        $lab_reporting_test_normal_value_high_range = $lab_reporting_test_normal_value_low.' - '.$lab_reporting_test_normal_value_high;
                                    }
                                }
                                else
                                {
                                    if($reference_range_female != '')
                                    {
                                        $normal_values_array = explode('-', $lab_reporting_test_normal_value_female);
                                        $lab_reporting_test_normal_value_low_female = $normal_values_array[0];
                                        $lab_reporting_test_normal_value_high_female = $normal_values_array[1];
                                        $lab_reporting_test_normal_value_high_female_range = 'F: ' .$lab_reporting_test_normal_value_low_female.' - '.$lab_reporting_test_normal_value_high_female;
                                    } 
                                    if($reference_range_male != '')
                                    {
                                        $normal_values_array = explode('-', $lab_reporting_test_normal_value_male);
                                        $lab_reporting_test_normal_value_low_male = $normal_values_array[0];
                                        $lab_reporting_test_normal_value_high_male = $normal_values_array[1];
                                        if($lab_reporting_test_normal_value_high_female_range == '')
                                        {                                        
                                            $lab_reporting_test_normal_value_high_male_range = 'M: ' .$lab_reporting_test_normal_value_low_male.' - '.$lab_reporting_test_normal_value_high_male;
                                        }
                                        else
                                        {
                                            $lab_reporting_test_normal_value_high_male_range = ', M: ' .$lab_reporting_test_normal_value_low_male.' - '.$lab_reporting_test_normal_value_high_male;
                                        }
                                    }                                    
                                }
                                
                                echo '<tr>';
                                    echo '<td class ="text-left">'.$row_test_report['parameter_name'].'</td>';
                                    echo '<td>'.$row_test_report['lab_test_unit_value'].'</td>';
                                    echo '<td>'.$lab_reporting_test_normal_value_high_range.$lab_reporting_test_normal_value_high_female_range.$lab_reporting_test_normal_value_high_male_range.'</td>';
                                    if($patient_gender_id == 1)
                                    {
                                        
                                    }
                                    elseif($patient_gender_id == 2)
                                    {
                                        
                                    }
                                    
                                    $lab_test_report_result = strtoupper($lab_test_report_result);
                                    if($lab_test_report_result == 'NIL' || $lab_test_report_result == 'TRACES' || $lab_test_report_result == 'NEGATIVE' || $lab_test_report_result == 'POSITIVE' || $lab_test_report_result == '-')
                                    {
                                        $class_detail = "text-dark";    $msg_if_low = '';   $msg_if_hight = ''; 
                                    }
                                    elseif(str_contains($lab_test_report_result, '-'))
                                    {
                                        $class_detail = "text-dark";    $msg_if_low = '';   $msg_if_hight = ''; 
                                    }
                                    elseif(str_contains($lab_test_report_result, '+') || str_contains($lab_test_report_result, 'POSITIVE') ||str_contains($lab_test_report_result, 'RED') || str_contains($lab_test_report_result, 'DARK BROWN') || str_contains($lab_test_report_result, 'ORANGE') || str_contains($lab_test_report_result, 'BLUE') || str_contains($lab_test_report_result, 'GREEN') || str_contains($lab_test_report_result, 'CLOUDY') || str_contains($lab_test_report_result, 'MILKY'))
                                    {
                                        $class_detail = "text-DANGER";    $msg_if_low = '';   $msg_if_hight = ''; 
                                    }
                                    elseif(str_contains($lab_test_report_result, 'CLEAR') || str_contains($lab_test_report_result, 'PALE YELLOW') || str_contains($lab_test_report_result, 'YELLOW') || str_contains($lab_test_report_result, 'DARK YELLOW') || str_contains($lab_test_report_result, 'DARK AMBER'))
                                    {
                                        $class_detail = "text-dark";    $msg_if_low = '';   $msg_if_hight = ''; 
                                    }
                                    elseif($lab_reporting_test_normal_value_high_range == 'NIL')
                                    {
                                        $class_detail = "text-danger";    $msg_if_low = '';   $msg_if_hight = ''; 
                                    }
                                    elseif($lab_reporting_test_normal_value_high_range == '')
                                    {
                                        if($patient_gender_id == 2) // MALE
                                        {
                                            if(intval($lab_test_report_result ?? 0) < $lab_reporting_test_normal_value_low_male)
                                            {
                                                $class_detail = "text-success";    $msg_if_low = '';   $msg_if_hight = '';  
                                            }
                                            elseif(intval($lab_test_report_result ?? 0) >= $lab_reporting_test_normal_value_high_male)
                                            {
                                                $class_detail = "text-danger";    $msg_if_low = '';   $msg_if_hight = '';  
                                            }
                                            else
                                            {
                                                $class_detail = "text-dark";    $msg_if_low = '';   $msg_if_hight = '';  
                                            }
                                        }
                                        elseif($patient_gender_id == 1) // FEMALE
                                        {
                                            
                                        }
                                        else // TRANSGENDER
                                        {
                                            
                                        }
                                         
                                    }
                                    elseif($lab_test_report_result < $lab_reporting_test_normal_value_low)
                                    {
                                        $class_detail = "text-success h5";  $msg_if_low = '<strong>	&#9660; </strong>'; $msg_if_hight = '';
                                    }
                                    elseif($lab_test_report_result >= $lab_reporting_test_normal_value_high)
                                    {
                                        
                                        $class_detail = "text-danger h5";   $msg_if_low = '';  $msg_if_hight = '<strong>&#9650; </strong>';
                                    }
                                    else
                                    {
                                        $class_detail = "text-dark";    $msg_if_low = '';   $msg_if_hight = ''; 
                                    }
                                        echo '<td class = "'.$class_detail.'">'.$msg_if_low.$lab_test_report_result.$msg_if_hight.'</td>';
                                echo '</tr>';

                                $msg_if_low = '';
                                $msg_if_hight = '';
                                $class_detail = '';
                                $lab_reporting_test_normal_value_high_range = '';
                                $lab_reporting_test_normal_value_high_female_range = '';
                                $lab_reporting_test_normal_value_high_male_range = '';
                            }
                            echo '</table>';
                        }                      
                    }
                }
            }
            ?>
        </div>
        <div class = "col-sm-12">
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