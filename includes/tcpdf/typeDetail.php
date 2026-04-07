<?php 
function fetch_parties($title1, $startDate, $endDate, $type_id)  
 {      
      $output = ''; 
      $con = mysqli_connect("localhost", "alnoor", "alnoor", "alnoor");
      $s = 0;
      $balance = 0;     
      $parties = mysqli_query($con,"SELECT * FROM `parties` WHERE `type_id` = '$type_id'");
      $count = mysqli_num_rows($parties);
      $paidTotal = 0;
      $receiveTotal = 0;
      $receive = 0;
      $paid = 0;
$start = 0;
$end = 16;
     $types = "SELECT * FROM `type` WHERE `id` = '$type_id'";
      $runTypes = mysqli_query($con, $types);
      if (mysqli_num_rows($runTypes) > 0 ) {
        while ($row_types = mysqli_fetch_array($runTypes)) {
          $row_types_name = $row_types['1'];
}
}
      $output .= '<h1 align="center">AL-NOOR CITY HOUSING SCHEME KOT ADDU</h1>'; 
      $output .= '<h3 align="center">'.$row_types_name.'('.$title1.')</h3>';
      $output .= '<h4 align="center">DATED :  FROM '.$startDate.' TO  '.$endDate.'</h4>';
      $output .= '  
      <table border="1" cellspacing="0" cellpadding="5">';
      //start:
      $output .= '<tr style="color:white;background-color:black">  
                <th width="5%"><H5>S/No</H5></th>  
                <th width="35%"><H5>NAME OF PARTY</H5></th>  
                <th width="20%"><h5>TOTAL AMOUNT</h5></th>    
                <th width="20%"><h5>PAID</h5></th>
                <th width="20%"><H5>BALANCE</H5></th>   
           </tr>';

     $type = "SELECT * FROM `parties` WHERE `type_id` = '$type_id' ORDER BY `name`,`id`";
      $runType = mysqli_query($con, $type);
      if (mysqli_num_rows($runType) > 0 ) {
        while ($row_type = mysqli_fetch_array($runType)) {
          $row_type_id = $row_type['0'];
          $row_type_name = $row_type['1'];           
      $daily = "SELECT * FROM `daily` WHERE `main_id` = '$row_type_id' AND `date` >= '$startDate' AND  `date` <= '$endDate' order by `date`,`id`";
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
      $s = $s + 1;
     $output .= '<tr>  
                <th width="5%"style="text-align:right;"><H5>'.$s.'</H5></th>  
                <th width="35%"><H5>'.strtoupper($row_type_name).'</H5></th>  
                <th width="20%"style="text-align:right;"><h5>'.number_format($receive).'</h5></th>    
                <th width="20%"style="text-align:right;"><h5>'.number_format($paid).'</h5></th>
                <th width="20%"style="text-align:right;"><H5>'.number_format($receive-$paid).'</H5></th>   
           </tr>';
           $receiveTotal = $receiveTotal + $receive;
           $paidTotal = $paidTotal + $paid;
           $receive = 0;
           $paid = 0;
  
}
}
//if($end==16){$start=$start+16;}
//else{$start=$start+21;}
//$end=21;
//if($start<$count){goto start;}       
  $output .= '<tr style="text-align:right;color:black;background-color:yellow;font-size:15px">  
                <th width="5%"><H5>'.$count.'</H5></th>
                <th width="35%"><h5>GRAND TOTAL</h5></th>    
                <th width="20%"><h5>'.number_format($receiveTotal).'</h5></th> 
                <th width="20%"><H5>'.number_format($paidTotal).'</H5></th>
                <th width="20%"><H5>'.number_format(intval($receiveTotal-$paidTotal)).'</H5></th>   
           </tr>';  
      $output .= '</table>'; 
      return $output;  
 }  



      require_once('tcpdf/tcpdf.php');  
      $obj_pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
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
      $obj_pdf->SetAutoPageBreak(true,27);  
      $obj_pdf->SetFont('helvetica', '', 11);  
      $obj_pdf->AddPage();  
      $content = '';  
if(isset($_POST['print'])){
  $startDate=$_POST['startDate'];
  $type_id=$_POST['type'];
  $endDate =$_POST['endDate'];
  $title=$_POST['title'];
  $content .= fetch_parties($title,$startDate,$endDate,$type_id);
  $obj_pdf->writeHTML($content);
  $obj_pdf->Output('sample.pdf', 'I');   
}
else if (isset($_POST['excel'])) {
  $startDate=$_POST['startDate'];
  $type_id = $_POST['type'];
  $endDate =$_POST['endDate'];
  $title=$_POST['title'];
  $content .= fetch_parties($title,$startDate,$endDate,$type_id);
  header('Content-Type: application/xls');
  header('Content-Disposition: attachment; filename=allReceipts.xls');
  echo $content;

}
else {
  $content .= fetch_parties();
  $obj_pdf->writeHTML($content);
  $obj_pdf->Output('sample.pdf', 'I');   
      }  
?>