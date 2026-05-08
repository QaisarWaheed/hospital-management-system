<?php
session_start();
require_once('tcpdf/tcpdf_include.php');
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

function fetch_parties($title, $category_id)  
{    
    $output = '';  
    $company_name = 'Youth Community Development Organization';
    $company_trademark = 'YCDO';
    $company_ambition = 'SERVE HUMANITY';
    $company_phone = '0304-1110222';
    require_once __DIR__ . '/../db_connect.php';
    $output .= '<table>
                <thead>
                <tr>
                    <th colspan = "8" style = "text-align: center;font-size: 22px;">'.$company_name.' </th>
                </tr>
                <tr>
                    <th colspan = "8" style = "text-align: center;">'.$title.'</th>
                </tr>
                    <tr>
                        <th width= "5%">S#</th>
                        <th width= "35%">NAME</th>
                        <th width= "10%">CATEGORY</th>
                        <th width= "10%">QUANTITY</th>
                        <th width= "10%">PURCHASE</th>
                        <th width= "10%">POOR</th>
                        <th width= "10%">MEMBER</th>
                        <th width= "10%">GENERAL</th>
                    </tr>
                </thead>
                <tbody>';
                    $s = 0;
                    if($category_id == 0)
                    {
                        $select_token = "SELECT items.name as name,categories.name as category_name ,items.quantity as quantity,items.poor as poor,items.member as member,items.general as general FROM `items` INNER JOIN categories ON items.category_id = categories.id WHERE items.quantity > 0 AND items.status = '1' AND items.category_id NOT IN (2, 3, 8, 20, 28, 29) ORDER BY items.category_id, items.name ASC";
                    }
                    else
                    {
                        $select_token = "SELECT items.name as name,categories.name as category_name ,items.quantity as quantity,items.poor as poor,items.member as member,items.general as general FROM `items` INNER JOIN categories ON items.category_id = categories.id WHERE items.quantity > 0 AND items.status = '1' AND items.category_id = '$category_id' ORDER BY items.category_id, items.name ASC";
                    }
                    $run_token = mysqli_query($con, $select_token);
                    if(mysqli_num_rows($run_token) > 0)
                    {
                    while($row_token = mysqli_fetch_array($run_token))
                    {
                        $item_name = $row_token['name'];
                        $category_name = $row_token['category_name'];
                        $quantity = $row_token['quantity'];
                        $purchase = $row_token['purchase'];
                        $poor = $row_token['poor'];
                        $member = $row_token['member'];
                        $general = $row_token['general'];
                        $s++;
                    $output .='
                    <tr style = "font-size:10px;">
                        <td width= "5%">'.$s.'</td>
                        <td width= "35%">'.$item_name.'</td>
                        <td width= "10%">'.$category_name.'</td>
                        <td width= "10%">'.$quantity.'</td>
                        <td width= "10%">'.$purchase.'</td>
                        <td width= "10%">'.$poor.'</td>
                        <td width= "10%">'.$member.'</td>
                        <td width= "10%">'.$general.'</td>
                    </tr>
                    ';
                    }
                    }
                    else
                    {
                    $output .= '
                    <tr>
                        <td colspan = "8">'.$con->error.'</td>
                    </tr>
                    ';
                    }
                    $output .= '
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
<style>
table, td, th {
  border: 1px solid black;
}

table {
  width: 100%;
  border-collapse: collapse;
}
</style>
</head>
<body>';
$foot = '
</body>
</html>';
$title = 'MEDICINE STORE AVAILABLE STOCK';
if(isset($_POST['print']))
{
    $category_id = $_POST['category_id'];
    $content .= $head;
    $content .= fetch_parties($title, $category_id);
    $content .= $foot;
    $pdf->writeHTML($content);
    ob_clean();
    // $pdf->Output('verify_token.pdf', 'I');
    // $pdf->Output($title.'.pdf', 'I');
    $pdf->Output($title.'.pdf', 'D');
    // $pdf->Output($_SERVER['DOCUMENT_ROOT'] . 'output.pdf', 'F');
}
else
{
    $category_id = $_POST['category_id'];
    $content .= fetch_parties($title, $category_id);
    header('Content-Type: application/xls');
    header('Content-Disposition: attachment; filename='.$title.'.xls');
    echo $content;
}

?>
