<?php
session_start();
require_once('tcpdf/tcpdf_include.php');
// include('pageHeaderFooter.php');
    require_once __DIR__ . '/../db_connect.php';
function get_uname_by_id($id)
{
    $output = '';
    require_once __DIR__ . '/../db_connect.php';
    $run = mysqli_query($con, "SELECT u_name FROM `users` WHERE `id` = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['u_name'];
        }    
    }    
    return $output;
}

function get_branch_tag_name_by($id)
{
    $output = '';
    require_once __DIR__ . '/../db_connect.php';
    $run = mysqli_query($con, "SELECT tag_name FROM `branchs` WHERE `id` = '$id' ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['tag_name'];
        }    
    }    
    return $output;
}

function get_item_name_by_register_item_id($register_item_id)
{
    $output = '';
    require_once __DIR__ . '/../db_connect.php';
    $run = mysqli_query($con, "SELECT name FROM `items` WHERE `id` iN (SELECT item_id FROM `item_register_to_branches` WHERE id = '$register_item_id') ");
    if (mysqli_num_rows($run) == 1) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $output .= $row['name'];
        }
    }
    else
    {
        $output = 0;
    }    
    return $output;
}

function get_given_services_by_token_no($token_no)
{
    $output = '';
    require_once __DIR__ . '/../db_connect.php';
    $run = mysqli_query($con, "SELECT item_id FROM `item_by_doctor` WHERE `tokan_no` = '$token_no' ");
    if (mysqli_num_rows($run) > 0) 
    {
        while ($row = mysqli_fetch_array($run)) 
        {
            $item_name = get_item_name_by_register_item_id($row['item_id']);
            if(!next($row)) 
            {
                $output .= $item_name;
            }
            else
            {
                $output .= $item_name .'<br>';
            }
        }    
    }  
    else
    {
        $output .= 'N/A';
    }
    return $output;
}

function get_patient_name_by_token_no($token_no)
{
    $output = '';
    require_once __DIR__ . '/../db_connect.php';
    $get_patient = mysqli_query($con, "SELECT * FROM patients WHERE id IN (SELECT `patient_id` FROM `tokans` WHERE `id` = '$token_no') ");
    if (mysqli_num_rows($get_patient) == 1) 
    {
        while ($row_patient = mysqli_fetch_array($get_patient)) 
        {
            $output .= $row_patient['name'];
        }
    }
    return $output;
}

function fetch_parties($br_id, $service_id, $start_date, $end_date, $title)  
{    
    $output = '';  
    $company_name = 'Youth Community Development Organization';
    $company_trademark = 'YCDO';
    $company_ambition = 'SERVE HUMANITY';
    $company_phone = '0304-1110222';
    require_once __DIR__ . '/../db_connect.php';
    $output .= '<table border = "solid">
                <thead>
                <tr>
                    <th colspan = "9" style = "text-align: center;font-size: 22px;">'.$company_name.' </th>
                </tr>
                <tr>
                    <th colspan = "9" style = "text-align: center;">'.$title.' FROM: '.date_format(date_create($start_date), "d-m-Y").' TO: '.date_format(date_create($end_date), "d-m-Y").'</th>
                </tr>
                    <tr>
                        <th>S#</th>
                        <th>Time</th>
                        <th>Date</th>
                        <th>Token #</th>
                        <th>Patient</th>
                        <th>Service</th>
                        <th>Amount</th>
                        <th>Received</th>
                        <th>Token By</th>
                    </tr>
                </thead>
                <tbody>';
                    $s = 0;
                    $total_amount = 0;
                    $total_amount_received = 0;
                    $end_date = date_format(date_create($end_date), "Y-m-d 23:59:59");
                    if($br_id != '0')
                    {
                        $select_token = "SELECT * FROM tokans WHERE status = 1 AND branch_id = '$br_id' AND created >= '$from' AND created < '$end_date' AND id IN (SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE item_id IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN ($service_id) AND branch_id = '$br_id' ))";
                    }
                    else
                    {
                        $select_token = "SELECT * FROM tokans WHERE status = 1 AND created >= '$from' AND created < '$end_date' AND id IN (SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE item_id IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN ($service_id)))";
                    }
                    // $select_token = "SELECT * FROM tokans WHERE status = 1 AND branch_id = '$br_id' AND created >= '$start_date' AND created < '$end_date' AND created < '$end_date' AND id IN (SELECT DISTINCT `tokan_no` FROM `item_by_doctor` WHERE item_id IN (SELECT `id` FROM `item_register_to_branches` WHERE `item_id` IN ($service_id)))";
                    $run_token = mysqli_query($con, $select_token);
                    if(mysqli_num_rows($run_token) > 0)
                    {
                    while($row_token = mysqli_fetch_array($run_token))
                    {
                        $token_no = $row_token['id'];
                        $patient = get_patient_name_by_token_no($token_no);
                        $service = get_given_services_by_token_no($row_token['id']);
                        $branch_tag_name = get_branch_tag_name_by($row_token['branch_id']);
                        $token_by = get_uname_by_id($row_token['user_id']);
                        $amount = $row_token['cash'];
                        $total_amount = $total_amount + $amount;
                        $amount_received = $row_token['cash_received'];
                        $total_amount_received = $total_amount_received + $amount_received;
                        $time = date_format(date_create($row_token['created']), "h:i:s A");
                        $date = date_format(date_create($row_token['created']), "d-m-Y");
                        $s++;
                    $output .='
                    <tr style = "font-size:10px;">
                        <td>'.$s.'</td>
                        <td>'.$time.'</td>
                        <td>'.$date.'</td>
                        <td>'.$token_no.'('.$branch_tag_name.')</td>
                        <td>'.$patient.'</td>
                        <td>'.$service.'</td>
                        <td>'.$amount.'</td>
                        <td>'.$amount_received.'</td>
                        <td style = "font-size:8px;">'.$token_by.'</td>
                    </tr>
                    ';
                    }
                    }
                    else
                    {
                    $output .= '
                    <tr>
                        <td colspan = "9">'.$con->error.'</td>
                    </tr>
                    ';
                    }
                    $output .= '
                    <tr>
                        <th colspan = "6"></th>
                        <th>'.$total_amount.'</th>
                        <th>'.$total_amount_received.'</th>
                        <th></th>
                    </tr>
                </tbody>
            </table>';
    return $output; 
}


$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
// $pdf->SetHeaderData("logo_example.jpg", PDF_HEADER_LOGO_WIDTH, "YOUTH COMMUNITY DEVELOPMENT ORGANIZATION", "REPORTS SECTION");
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA)); 
$pdf->SetDefaultMonospacedFont('times');  
$pdf->SetMargins(PDF_MARGIN_LEFT, '9', PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(true,12);  
$pdf->SetFont('times', '', 12);  
$pdf->AddPage();
  $content = '';  
$head = '
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta lang="en">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="../css/nav_style.css">
	<link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
	<title>VERIFY TOKENS</title>
</head>
<body>';
$foot = '
</body>
</html>';
$title = '';
if(isset($_POST['print']))
{
    $br_id = $_POST['br_id'];
    if($br_id == 0){$br_id = '1';}
    $service_id = $_POST['service_id'];
    if($service_id == 1)
    {
        $service_id = '854, 867, 868, 869, 870, 871, 872, 873, 874, 875, 876, 877, 879, 880, 881, 882, 1449, 1450, 1451, 1452, 1453, 1454, 1455, 1456, 1457, 1458, 1459, 1460, 1462, 1463, 1464, 1465';
        $title = "DENTAL ";
    }
    elseif($service_id == 2)
    {
        $service_id = '481, 482, 1196, 1197';
        $title = "X-RAYS ";
    }
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $content .= $head;
    $content .= fetch_parties($br_id, $service_id, $start_date, $end_date, $title);
    $content .= $foot;
    $pdf->writeHTML($content);
    ob_clean();
    // $pdf->Output('verify_token.pdf', 'I');
    $pdf->Output($start_date.' to '.$end_date.'.pdf', 'D');
    // $pdf->Output($_SERVER['DOCUMENT_ROOT'] . 'output.pdf', 'F');
}
else
{
    $br_id = $_POST['br_id'];
    if($br_id == 0){$br_id = '1';}
    $service_id = $_POST['service_id'];
    if($service_id == 1)
    {
        $service_id = '854, 867, 868, 869, 870, 871, 872, 873, 874, 875, 876, 877, 879, 880, 881, 882, 1449, 1450, 1451, 1452, 1453, 1454, 1455, 1456, 1457, 1458, 1459, 1460, 1462, 1463, 1464, 1465';
        $title = "DENTAL ";
    }
    elseif($service_id == 2)
    {
        $service_id = '481, 482, 1196, 1197';
        $title = "X-RAYS ";
    }
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $content .= fetch_parties($br_id, $service_id, $start_date, $end_date, $title);
    header('Content-Type: application/xls');
    header('Content-Disposition: attachment; filename='.$start_date.' to '.$end_date.'.xls');
    echo $content;
}

?>
