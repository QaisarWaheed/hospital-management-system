<?php
session_start();
require_once('tcpdf/tcpdf_include.php');
include('pageHeaderFooter.php');
function fetch_parties()  
{    
    require_once __DIR__ . '/../db_connect.php';
    $output = '';  

    return $output; 
}


$pdf = new MYPDF('l', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA)); 
$pdf->SetDefaultMonospacedFont('times');  
$pdf->SetMargins(PDF_MARGIN_LEFT, '9', PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(true,12);  
$pdf->SetFont('times', '', 12);  
$pdf->AddPage();
  $content = '';  

// if(isset($_POST['print']))
// {
    $content .= fetch_parties();
    $pdf->writeHTML($content);
    $pdf->Output('verify_token.pdf', 'I');
// }
// else
// {
    $content .= fetch_parties();
    header('Content-Type: application/xls');
    header('Content-Disposition: attachment; filename=verify_token.xls');
    echo $content;
// }

?>
