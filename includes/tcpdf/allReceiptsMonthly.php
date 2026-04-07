<?php
function fetch_parties($title1 , $startDate , $endDate,$method_id)  {    
$con = mysqli_connect("localhost", "alnoor", "alnoor", "alnoor");
$no = 0;
$totalAmount = 0;
$totalSlips = 0;
$count = 0;
$startYear = substr(strtoupper($startDate),0,4);
$endYear = substr(strtoupper($endDate),0,4);
$startMonth = substr(strtoupper($startDate),5,2);
$endMonth = substr(strtoupper($endDate),5,2);
$year = $startYear - $endYear;
$totalPaid = 0;
$start = 0;
$end = 31;
$title1 = "ALL CASH RECEIPTS DETAIL";
      $output = '';    
      $output .= '<h2 align="center">AL-NOOR CITY HOUSING SCHEME KOT ADDU</h2>'; 
      $output .= '<h3 align="center">'.$title1.'</h3>'; 
      $output .= '<h4 align="center">DATED :  FROM '.$startMonth.'-'.$startYear.' TO  '.$endMonth.'-'.$endYear.'</h4>';
      $date = date("d:m:Y"); 
      $output .= '  
      <table border="1" cellspacing="0" cellpadding="5">';
      $output .= '
        <tr style="font-size:9px;font-weight: bold;">
          <th width="10%">S/NO</th>
          <th width="15%">MONTH</th>
          <th width="15%">TOTAL SLIPS</th>
          <th width="40%">AMOUNT</th>
          <th width="20%">REMARKS</th>
        </tr>';  
start:
$year = $startYear - $endYear;
if ($year < 1) {
    $selectParty = "SELECT sum(`amount`),count(`id`) FROM `payments` WHERE `date` >= '$startYear-$startMonth-01' 
    AND `date` <= '$startYear-$startMonth-31' ORDER BY `date`,`id` LIMIT $start, $end";
        $runSelectParty = mysqli_query($con , $selectParty);
        if (mysqli_num_rows($runSelectParty) > 0) {
          while ($row = mysqli_fetch_array($runSelectParty)) {
          $no = $no + 1;
          $amount = $row['0'];       
          $slips = $row['1']; 
          $totalSlips = $totalSlips + $slips;
          $totalAmount = $totalAmount + $amount;      

        $output .= '<tr>  
                          <td style="text-align:right">'.$no.'</td>
                          <td style="text-align:right">'.$startMonth.'-'.$startYear.'</td>
                          <td style="text-align:right">'.$slips.'</td>
                          <td style="text-align:right">'.number_format($amount).'</td>
                          <td ></td>    
                       </tr>  
                          ';
        }
      }
if ($startMonth == $endMonth && $startYear == $endYear) {goto end;}
  if ($startMonth < 12) {$startMonth = $startMonth + 1;}
  else{$startMonth = 1;$startYear = $startYear + 1;}
      goto start;
}
else{
goto end;
}
end:
        $output .= '<tr style="font-size:15px;font-weight: bold;">  
                          <td style="text-align:right"></td>
                          <td style="text-align:right"></td>
                          <td style="text-align:right">'.$totalSlips.'</td>
                          <td style="text-align:right">'.number_format($totalAmount).'</td>
                          <td style="text-align:right"></td>
                          <td ></td>    
                       </tr>  
                          ';
      $output .= '</table>'; 
      return $output; 
    }


function excel_receipts($title1 , $startDate , $endDate,$method_id)  {   
$con = mysqli_connect("localhost", "alnoor", "alnoor", "alnoor");
$no = 0;
$ta = 0;
$totalPaid = 0;
$title1 = "ALL CASH RECEIPTS DETAIL";
$count = mysqli_num_rows(mysqli_query($con, "SELECT * FROM `payments` WHERE `date` <= '$endDate'"));
      $output = '';    
      $output .= '<h2 align="center">AL-NOOR CITY HOUSING SCHEME KOT ADDU</h2>'; 
      $output .= '<h3 align="center">'.$title1.'</h3>'; 
      $output .= '<h4 align="center">DATED :  FROM '.$startDate.' TO  '.$endDate.'</h4>';
      $date = date("d:m:Y"); 
      $output .= '  
      <table border="1" cellspacing="0" cellpadding="5">';
        $selectParty = "SELECT * FROM `payments` WHERE `date` >= '$startDate' AND `date` <= '$endDate' ORDER BY `date`,`id`";
      $output .= '
        <tr style="font-size:9px;font-weight: bold;">
          <th width="7%">S/NO</th>
          <th width="11%">DATE</th>
          <th width="29%">NAME</th>
          <th width="12%">PROPERTY</th>
          <th width="11%">VOUCHER</th>
          <th width="18%">DETAIL</th>
          <th width="12%">AMOUNT</th>
        </tr>';  
        $runSelectParty = mysqli_query($con , $selectParty);
        if (mysqli_num_rows($runSelectParty) > 0) {
          while ($row = mysqli_fetch_array($runSelectParty)) {
            $no = $no + 1;
            $id = $row['id'];
            $detailFor = $row['detailFor'];
            $saleId = $row['saleId'];
            $detail = $row['detail'];
        $selectSale = "SELECT * FROM `sales` WHERE `id` = '$saleId' ";
        $runSelectSale = mysqli_query($con , $selectSale);
        if (mysqli_num_rows($runSelectSale) > 0) {
          while ($row1 = mysqli_fetch_array($runSelectSale)) {
            $buyyerId = $row1['buyyerId'];
            $propertyId = $row1['propertyId'];
            $price = $row1['price'];
          }
        }
        $selectBuyyer = "SELECT * FROM `buyyers` WHERE `id` = '$buyyerId' ";
        $runSelectBuyyer = mysqli_query($con , $selectBuyyer);
        if (mysqli_num_rows($runSelectBuyyer) > 0) {
          while ($row2 = mysqli_fetch_array($runSelectBuyyer)) {
            $name = $row2['name'];
          }
        }
        $selectProperty = "SELECT * FROM `properties` WHERE `id` = '$propertyId' ";
        $runSelectProperty = mysqli_query($con , $selectProperty);
        if (mysqli_num_rows($runSelectProperty) > 0) {
          while ($row3 = mysqli_fetch_array($runSelectProperty)) {
            $propertyId3 = $row3['propertyId'];
          }
        }

          $amount = $row['amount'];
          $date = $row['date'];
          $voucherNo = $row['voucherNo'];       
  $selectTotalPaid = "SELECT * FROM `payments` WHERE `saleId` = '$saleId' AND `date` < '$date' ORDER BY `date` ";
  $runSelectTotalPaid = mysqli_query($con , $selectTotalPaid);
  if (mysqli_num_rows($runSelectTotalPaid) > 0) {
    while ($row4 = mysqli_fetch_array($runSelectTotalPaid)) {
      $totalPaid = $totalPaid + $row4['amount'];
    }
  }
      $output .= '
        <tr style="font-size:9px">
          <th width="7%" style="text-align:right">'.$no.'</th>
          <th width="11%">'.substr(strtoupper($date),8,2).'-'.substr(strtoupper($date),5,2).'-'.substr(strtoupper($date),0,4).'</th>
          <th width="29%;font-size:7px">'.substr(strtoupper($name),0,20).'</th>
          <th width="12%">'.$propertyId3.'</th>
          <th width="11%" style="text-align:right">'.$voucherNo.'</th>
          <th width="18%">'.$detail.'</th>
          <th width="12%" style="text-align:right">'.number_format($amount).'</th>
        </tr>';  
          $ta = $ta + $amount;
          $totalPaid = 0;
        }
          }
        $output .= '<tr>  
                          <td colspan="4"></td>
                          <td style="text-align:right">'.$count.'</td>
                          <td colspan="2" style="font-size:9px;font-weight: bold;text-align:right">'.number_format($ta).'</td>    
                       </tr>  
                          ';
        $output .= '
        <tr><td colspan="7" ></td></tr>
        <tr>
          <td colspan="4" style="text-align:right">TOTAL NUMBER OF RECEIPTS</td>
          <td colspan="3" style="text-align:right">'.$count.'</td>     
        </tr>';
        $output .= '
        <tr>
          <td colspan="4" style="text-align:right">TOTAL RECEIVED AMOUNT</td>
          <td colspan="3" style="text-align:right">'.number_format($ta).'</td>     
        </tr>';

      $output .= '</table>'; 
      return $output; 
    }

      require_once('tcpdf/tcpdf.php');  
      $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
      $obj_pdf->SetCreator(PDF_CREATOR);  
      $obj_pdf->SetTitle("Daily Reoprt Of ALNOOR CITY");  
      $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE,PDF_HEADER_STRING);  
      $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
      $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
      $obj_pdf->SetDefaultMonospacedFont('helvetica');  
      $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
      $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
      $obj_pdf->setPrintHeader(false);  
      $obj_pdf->setPrintFooter(false);  
      $obj_pdf->SetAutoPageBreak(true,20);  
      $obj_pdf->SetFont('helvetica', '', 12);  
      $obj_pdf->AddPage();  
      $content = '';  
if(isset($_POST['print']))
{
  $startDate=$_POST['startDate'];
  $method_id=$_POST['method_id'];
  $endDate =$_POST['endDate'];
  $title=$_POST['title'];
  $content .= fetch_parties($title,$startDate,$endDate,$method_id);
  $obj_pdf->writeHTML($content);
  $obj_pdf->Output('sample.pdf', 'I');   
}
elseif(isset($_POST['excel'])) 
{
  $startDate = $_POST['startDate'];
  $method_id=$_POST['method_id'];
  $endDate = $_POST['endDate'];
  $title = $_POST['title'];
$content .= excel_receipts($title,$startDate,$endDate,$method_id);  
  header('Content-Type: application/xls');
  header('Content-Disposition: attachment; filename=allReceipts.xls');
  echo $content;

}
else 
{
  $content .= fetch_parties();
  $obj_pdf->writeHTML($content);
  $obj_pdf->Output('sample.pdf', 'I');   
      }  
?>