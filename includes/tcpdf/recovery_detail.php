<?php
session_start();
require_once('tcpdf/tcpdf_include.php');
include('pageHeaderFooter.php');
function fetch_parties($title1, $startDate, $endDate, $method_id)  
 {    
  $end_month = substr($endDate, 5,2);
  $end_year = substr($endDate, 0,4);
  $show_date = date_create($endDate);
  $s = 1;
  $no = 1;
  $pay_count = 0;
  $count_months = 0;
  $output = '';  
  $output .= '<table border="1">';
  $output .= '<tr>
  <th style="text-align:center">'.$title1.'('.date_format($show_date,"F-Y").')</th>
  </tr>';
  $output .= '<tr style="background-color:gray;color:black;text-align:center">
                <th width="4%">S ID</th>
                <th width="6%">PLOT#</th>
                <th width="12%">CUSTOMER</th>
                <th width="8%">PHONE</th>
                <th width="8%">PRICE</th>
                <th width="8%">DOWN-Pay</th>
                <th width="8%">Inst-Pay</th>
                <th width="8%">T-PAID</th>
                <th width="8%">PAYABLE</th>
                <th width="9%">D/P Remain</th>
                <th width="9%">INST remain</th>
                <th width="12%">REMARKS</th>
              </tr>';
$con = mysqli_connect("localhost", "alnoor", "alnoor", "alnoor");
if ($method_id == 3) {$select = mysqli_query($con, "SELECT * FROM sales WHERE `status` = 1 AND `remianing` != 0 ORDER BY method,propertyId ");}
else{$select = mysqli_query($con, "SELECT * FROM sales WHERE method = '$method_id' AND `status` = 1 AND `remianing` != 0");}

if (mysqli_num_rows($select)>0) {
  while ($row = mysqli_fetch_array($select)) {
    $sale_id = $row['id'];
//START SELECT PROPERTY    
    $property_id = $row['propertyId'];
$select_property = mysqli_query($con, "SELECT * FROM `properties` WHERE `id` = '$property_id'");
if (mysqli_num_rows($select_property)>0) {
while ($row_property = mysqli_fetch_array($select_property)) {$property_title = $row_property['propertyId'];}}
else{$property_title = " ";}      
//END SELECT PROPERTY    

//START SELECT BUYER 
    $buyer_id = $row['buyyerId']; 
$select_buyer = mysqli_query($con, "SELECT * FROM `buyyers` WHERE `id` = '$buyer_id'");
if (mysqli_num_rows($select_buyer)>0) {
while ($row_buyer = mysqli_fetch_array($select_buyer)) {$buyer_name = $row_buyer['name'];$phone1 = $row_buyer['phone1'];}}
else{$buyer_name = " ";}      
//END SELECT BUYER    

//START SELECT DOWN PAYMENT   
$select_payment = mysqli_query($con, "SELECT sum(amount) FROM `payments` WHERE `saleId` = '$sale_id' AND detail = 'DOWN PAYMENT'");
if (mysqli_num_rows($select_payment)>0) {
while ($row_payment = mysqli_fetch_array($select_payment)) {$receice_down = $row_payment['0'];}}
else{$receice_down = 0;}      
//END SELECT DOWN PAYMENT

//START SELECT PAYMENT   
$select_paymentS = mysqli_query($con, "SELECT sum(amount) FROM `payments` WHERE `saleId` = '$sale_id' AND detail != 'DOWN PAYMENT'");
$pay_count = mysqli_num_rows($select_paymentS);
if ($pay_count > 0) {
while ($row_paymentS = mysqli_fetch_array($select_paymentS)) {$receice_downS = $row_paymentS['0'];}}
else{$receice_downS = 0;$pay_count = 0;}      
//END SELECT  PAYMENT    

    $price = $row['price'];
    $remaining = $row['remianing'];
    $downpayment = $row['downpayment'];
    $remain_down = $row['remainAdvance'];
    $installment = $row['installment'];
    $mon3 = $row['mon3'];
    $mon6 = $row['mon6'];
    $start_date = $row['startInstallmentDate'];
    $start_month = substr($start_date, 5,2);

    $start_year = substr($start_date, 0,4);
$count_year = ($end_year-$start_year);
if ($count_year == 0) {
$count_months = (number_format($end_month)-number_format($start_month))+1;if ($count_months < 1) { $count_months = 0;}
}else{$count_months = 0;}
if($start_year == $end_year){
if ($installment == 0) {
  if ($mon3 != 0) {
    $no = 3;
    $count_months = intval(((($end_month - $start_month)/3)+1));
  }
  if ($mon6 != 0) {
    $no = 6;
    $count_months = intval(((($end_month - $start_month)/6)+1));
  }
}
}
$output .= '<tr>';
$output .='<td style="text-align:right">'.$s++.'</td>';
$output .='<td style="text-align:center">'.$property_title.'</td>';
$output .='<td style="text-align:center;font-size:7px">'.$buyer_name.'</td>';
$output .='<td style="text-align:right;font-size:9px">0'.substr($phone1, 0,3).'-'.substr($phone1, 3,7).'</td>';
$output .='<td style="text-align:right">'.number_format($price).'</td>';
$output .='<td style="text-align:right">'.number_format($receice_down).'</td>';
$output .='<td style="text-align:right">'.number_format($receice_downS).'</td>';
$output .='<td style="text-align:right;color: black;background-color: red">'.number_format($price-$remaining).'</td>';
$output .='<td style="text-align:right">'.number_format($remaining).'</td>';
$output .='<td style="text-align:right">'.number_format($downpayment-$receice_down).'</td>';
$output .='<td style="text-align:right">'.number_format(($count_months*($installment+$mon3+$mon6))-$receice_downS).'</td>';
$output .='<td style="text-align:right"></td>';
$output .='</tr>';
  }
}

  $output .= '</table>';
  return $output; 
    }


$pdf = new MYPDF('l', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA)); 
$pdf->SetDefaultMonospacedFont('helvetica');  
$pdf->SetMargins(PDF_MARGIN_LEFT, '9', PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(true,12);  
$pdf->SetFont('helvetica', '', 12);  
$pdf->AddPage();
  $content = '';  

if(isset($_POST['print'])){
  $startDate=$_POST['startDate'];
  $method_id=$_POST['method_id'];
  $endDate =$_POST['endDate'];
  $title=$_POST['title'];
  $content .= fetch_parties($title,$startDate,$endDate,$method_id);
  }
$pdf->writeHTML($content);
$pdf->Output('example_003.pdf', 'I');        
?>
