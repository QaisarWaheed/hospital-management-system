<?php
require_once('tcpdf_include.php');
include('pageFooter.php');
function fetch_parties($title1 = '', $endDate = '2020-08-31')  
 {      
      $output = ''; 
      $con = mysqli_connect("localhost", "root", "", "alnoor");
      $s = 0;
      $balance = 0;     
      $parties = mysqli_query($con,"SELECT DISTINCT `type_id` FROM `parties`");
      $count = mysqli_num_rows($parties);
      $paidTotal = 0;
      $receiveTotal = 0;
      $receive = 0;
      $paid = 0;
      $totalPaid = 0;
      $totalPayment = 0;
      $totalBalance = 0;
$start = 0;
$end = 16;
      $output .= '<h1 align="center">AL-NOOR CITY HOUSING SCHEME KOT ADDU</h1>'; 
      $output .= '<h3 align="center">'.$title1.'</h3>';
      $output .= '<h4 align="center">DATED : '.$endDate.'</h4>';
      $output .= '<table border="1" cellspacing="0" cellpadding="5">';
      $output .='<tr>
      <td width="5%">S #</td>
      <td width="35%">HEAD ACCOUNT</td>
      <td width="20%">TOTAL</td>
      <td width="20%">PAID</td>
      <td width="20%">BALANCE</td>
      </tr>';
      //start:
      $typeCheck = mysqli_query($con, "SELECT `id`,`title` FROM `type` WHERE `id` IN (1,2,7,8,9,11,12,14,15,16,18,20,22,23,24) ORDER BY `title`");
      if (mysqli_num_rows($typeCheck) > 0) {
        while ($typeC = mysqli_fetch_array($typeCheck)) {
          $type_id = $typeC['0'];
          $typeT = $typeC['1'];

     $type = "SELECT * FROM `parties` WHERE `type_id` = '$type_id' ORDER BY `name`,`id`";
      $runType = mysqli_query($con, $type);
      if (mysqli_num_rows($runType) > 0 ) {
        while ($row_type = mysqli_fetch_array($runType)) {
          $row_type_id = $row_type['0'];
          $row_type_name = $row_type['1'];           
      $daily = "SELECT * FROM `daily` WHERE `main_id` = '$row_type_id' AND `date` <= '$endDate' order by `date`,`id`";
      $runDaily = mysqli_query($con, $daily);
      if (mysqli_num_rows($runDaily) > 0) {
        while ($row_main = mysqli_fetch_array($runDaily)) {
          $dailyId = $row_main['0'];
          $dailyDate = $row_main['date'];
          $dailyDetail = $row_main['detail'];
          $dailyAmount = $row_main['amount'];
          $dailyType = $row_main['type'];
          $dailyRetail = $row_main['retail'];
          if ($dailyType == 2) {
          $balance = $balance-intval($dailyAmount);
          $paid = $paid + intval($dailyAmount);
          }          
          if ($dailyType == 1) {
          $balance = $balance + intval($dailyAmount);
          $receive = $receive + intval($dailyAmount);
          }
        }
      }
           $receiveTotal = $receiveTotal + $receive;
           $paidTotal = $paidTotal + $paid;
           $receive = 0;
           $paid = 0;
  
}
}       
  $s = $s + 1;
  $output .= '<tr style="text-align:right;color:black;background-color:red;font-size:14px">  
                <th width="5%"><H5>'.$s.'</H5></th>
                <th width="35%" style="text-align:left;"><h5>'.strtoupper($typeT).'</h5></th>    
                <th width="20%"><h5>'.number_format($receiveTotal).'</h5></th> 
                <th width="20%"><H5>'.number_format($paidTotal).'</H5></th>
                <th width="20%"><H5>'.number_format(intval($receiveTotal-$paidTotal)).'</H5></th>   
           </tr>';

           if (intval($receiveTotal-$paidTotal) != 0) {
  $output .= '<tr style="text-align:right;font-size:12px">  
                <th width="5%" style="text-align:right;color:black;background-color:white;"></th>  
                <th width="5%"><H5>S/No</H5></th>
                <th width="35%"style="text-align:left;"><h5>ACCOUNT NAME</h5></th>    
                <th width="25%"><h5>ACCOUNT PAYABLE</h5></th> 
                <th width="25%"><H5>RECEIVEABLE</H5></th> 
                <th width="5%" style="text-align:right;color:black;background-color:white;"></th>  
           </tr>';
    $selectAccount = mysqli_query($con, "SELECT * FROM `parties` WHERE `type_id` = '$type_id' ORDER BY `name`,`id`");
    if (mysqli_num_rows($selectAccount) > 0) {
      $s_no = 0;
      $totalSubAmount = 0;
      $totalSubAmount2 = 0;
      while ($rowSubAccount = mysqli_fetch_array($selectAccount)) {
        $subAmountReceive = 0;
        $subAmountPaid = 0;
        $subAccountName = $rowSubAccount['name'];
        $subAccountId = $rowSubAccount['id'];
    $selectAccountDaily = mysqli_query($con, "SELECT * FROM `daily` WHERE `main_id` = '$subAccountId' AND `date` <= '$endDate'");
    if (mysqli_num_rows($selectAccountDaily) > 0) {
      while ($rowSubAccountDaily = mysqli_fetch_array($selectAccountDaily)) {
        $subType = $rowSubAccountDaily['type'];
        if ($subType == 1) {
        $subAmountReceive = $subAmountReceive + $rowSubAccountDaily['amount'];
        }
        elseif ($subType == 2) {
        $subAmountPaid = $subAmountPaid + $rowSubAccountDaily['amount'];
        }
}
$subAmount = $subAmountReceive - $subAmountPaid;
}    

if ($subAmount > 0) {
        $s_no = $s_no + 1;
  $totalSubAmount = $totalSubAmount + $subAmount;
  $output .= '<tr style="text-align:right;color:black;font-size:12px">  
                <th style="text-align:right;color:black;background-color:white;"></th>  
                <th><H5>'.$s_no.'</H5></th>
                <th style="text-align:left;"><h5>'.$subAccountName.'</h5></th>    
                <th><h5>'.number_format($subAmount).' CR</h5></th> 
                <th></th> 
                <th style="text-align:right;color:black;background-color:white;"></th>  
              </tr>';
}
elseif ($subAmount < 0) {
        $s_no = $s_no + 1;
  $totalSubAmount2 = $totalSubAmount2 + $subAmount;
  $output .= '<tr style="text-align:right;color:black;font-size:12px">  
                <th style="text-align:right;color:black;background-color:white;"></th>  
                <th><H5>'.$s_no.'</H5></th>
                <th style="text-align:left;"><h5>'.$subAccountName.'</h5></th>    
                <th></th> 
                <th><H5>'.number_format((-1)*$subAmount).' DR</H5></th> 
                <th></th>  
              </tr>';
} } }       
  $output .= '<tr style="text-align:right;color:black;background-color:#fffbbb;font-size:12px">  
                <th style="text-align:right;color:black;background-color:white;"></th>  
                <th><H5></H5></th>
                <th style="text-align:right;"><h4>TOTAL</h4></th>    
                <th><h4>'.number_format($totalSubAmount).' CR</h4></th> 
                <th><H4>'.number_format((-1)*$totalSubAmount2).' DR</H4></th> 
                <th></th>  
           </tr>';
  $output .= '<tr style="text-align:right;color:black;background-color:#fffbbb;font-size:12px">  
                <th style="text-align:right;color:black;background-color:white;"></th>  
                <th><H5></H5></th>
                <th style="text-align:right;"><h4>BALANCE</h4></th>    
                <th colspan="2" style="text-align:center;"><H2>'.number_format($totalSubAmount-((-1)*$totalSubAmount2)).'</H2></th> 
                <th></th>  
           </tr>';
            }

           $totalPayment = $totalPayment + $receiveTotal;
           $totalPaid = $totalPaid + $paidTotal;
           $totalBalance = $totalBalance + (intval($receiveTotal-$paidTotal));
           $receiveTotal = 0;
           $paidTotal = 0;
        }
      }
      if ($totalBalance > 0) {
  $output .= '<tr style="text-align:right;color:black;background-color:yellow;font-size:15px">  
                <th width="5%"><H5></H5></th>
                <th width="35%"style=""><h5>GRAND TOTAL</h5></th>    
                <th width="20%"><h5>'.number_format($totalPayment).'</h5></th> 
                <th width="20%"><H5>'.number_format($totalPaid).'</H5></th>
                <th width="20%"><H5>'.number_format($totalBalance).' CR</H5></th>   
           </tr>';
      }
      else{
  $output .= '<tr style="text-align:right;color:black;background-color:yellow;font-size:15px">  
                <th width="5%"><H5></H5></th>
                <th width="35%"style=""><h5>GRAND TOTAL</h5></th>    
                <th width="20%"><h5>'.number_format($totalPayment).'</h5></th> 
                <th width="20%"><H5>'.number_format($totalPaid).'</H5></th>
                <th width="20%"><H5>'.number_format($totalBalance).' DR</H5></th>   
           </tr>';

      }

      $output .= '</table>'; 
      return $output;  
 }  

$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->AddPage();
  $content = '';  
  $content .= fetch_parties();
  $pdf->writeHTML($content);
$pdf->Output('example_003.pdf', 'I');