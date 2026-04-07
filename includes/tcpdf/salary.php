<?php 
      $totalE = 0; 
function fetch_salary()  
 {      
      $output = ''; 
      $s = 0; 
      $connect = mysqli_connect("localhost", "root", "", "alnoor");  
      $sql = "SELECT * FROM `salaries` ";  
      $result = mysqli_query($connect, $sql);  
      while($row = mysqli_fetch_array($result))  
      {       
        $emp_id = $row['emp_id'];
      $sql2 = "SELECT * FROM `emp_reg` WHERE `id` = '$emp_id'";  
      $result2 = mysqli_query($connect, $sql2);  
      while($row2 = mysqli_fetch_array($result2))  
      {       
        $name = $row2['name'];
        $salary = $row2['salary'];
        $designation_id = $row2['designation_id'];
      }
      $sql3 = "SELECT * FROM `designation` WHERE `id` = '$designation_id'";  
      $result3 = mysqli_query($connect, $sql3);  
      while($row3 = mysqli_fetch_array($result3))  
      {       
        $title = $row3['title'];
      }
              $s = $s + 1;
              $GLOBALS['totalE'] = $GLOBALS['totalE'] + $row['amount'];
      $output .= '<tr>  
                          <td>'.$s.'</td>  
                          <td>'.$name.'</td>
                          <td>'.$title.'</td>  
                          <td style="text-align:right">'.$salary.'</td>    
                          <td style="text-align:right">'.$row["days"].'</td>  
                          <td style="text-align:right">'.$row["amount"].'</td>
                     </tr>  
                          ';  
      }  
            $output .= '<tr>  
                          <td colspan="3"></td>
                          <td>Total</td>  
                          <td colspan="2" style="text-align:right;font-size:16px">'.$GLOBALS['totalE'].'</td>
                     </tr>  
                          ';
      return $output;  
 }  

 function fetch_income()  
 {  
  return $output;  
 }  

 if(isset($_POST["create_pdf"]))  
 {  
      require_once('tcpdf/tcpdf.php');  
      $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
      $obj_pdf->SetCreator(PDF_CREATOR);  
      $obj_pdf->SetTitle("Daily Reoprt Of ALNOOR CITY");  
      $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
      $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
      $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
      $obj_pdf->SetDefaultMonospacedFont('helvetica');  
      $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
      $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
      $obj_pdf->setPrintHeader(false);  
      $obj_pdf->setPrintFooter(false);  
      $obj_pdf->SetAutoPageBreak(TRUE, 10);  
      $obj_pdf->SetFont('helvetica', '', 10);  
      $obj_pdf->AddPage();  
      $content = '';  
      $content = '<h1 align="center">Salary Reoprt of ALNOOR CITY</h1><br>
      <h3 align="center">Month of February</h3>';  
      $content .= '  
      <table border="1" cellspacing="0" cellpadding="5">
           <tr>  
                <th width="7%">S/No</th>  
                <th width="30%">Name</th>  
                <th width="17%">Designation</th>  
                <th width="15%">Salary</th>  
                <th width="10%">Days</th>  
                <th width="15%">Amount</th>  
           </tr>  
      ';  
      $content .= fetch_salary();  
      $content .= '</table>';
      $obj_pdf->writeHTML($content);  
      $obj_pdf->Output('sample.pdf', 'I');  
 }  
 ?>  
 <!DOCTYPE html>  
 <html>  
      <head>  
           <title>Webslesson Tutorial | Export HTML Table data to PDF using TCPDF in PHP</title>  
           <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />            
      </head>
      <body class="container">  
           <br /><br /> 
<div style="max-width: 50%;float: right;">
       <table border="1">  
                          <tr>  
                               <th width="10%">S/No</th>  
                               <th width="40%">Name</th>  
                               <th width="15%">Salary</th>  
                               <th width="15%">Days</th>  
                               <th width="15%">Amount</th>  
                          </tr>  
                     <?php  
                     echo fetch_salary();  
                     ?>  
        </table>               
</div>
                     <form method="post">  
                          <input type="submit" name="create_pdf" class="btn btn-danger" value="Create PDF" />  
                     </form>                                    

      </body>  
 </html>  