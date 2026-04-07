<?php 
include 'includes/connect.php'; 
if(isset($_GET['date']) && $_GET['date'] != '')
{
    $select_visit_date = $_GET['date'];
}
?>
<?php include 'includes/head.php'; ?>
	<title>Patient Registeration Print- <?php echo $company_trademark; ?></title>
</head>

<body onload = "window.print()" onkeydown="return (event.keyCode != 116)" onfocus="window.close()">
<div>

	<div class="">

		<div class="row" style="margin: 0px;">

			<div class="col-md-12">
			    <table class = "table table-hover table-boredered">
			        <thead>
			            <tr style = "text-align: center;">
			                <th>Sr</th>
			                <th>Token</th>
			                <th>Patient Name</th>
			                <th>Phone</th>
			                <th>Age</th>
			                <th>EDD</th>
			                <th>Gravida</th>
			                <th>Next Visit</th>
			                <th>Update By</th>
			            </tr>
			        </thead>
			        <tbody>
			         <?php
			         $s = 0;
			         if(isset($select_visit_date) && $select_visit_date != '')
			         {
    			         $select = "SELECT * FROM `gynae_register` WHERE branch_id = '$branch_id' AND status = 1 AND next_visit_date LIKE '$select_visit_date%' ORDER BY `next_visit_date` DESC ";
			         }
			         else
			         {
			             header('location: logout.php');
			         }
			         $run = mysqli_query($con, $select);
			         if(mysqli_num_rows($run) > 0)
			         {
			             while($row = mysqli_fetch_array($run))
			             {
			                 $id = $row['id'];
			                 $token_no = $row['token_no'];
			                 $update_by = $row['update_by'];
			                 $phone = $row['phone'];
			                 $gravide = $row['gravide'];
			                 $start_date = date_format(date_create($row['weeks']), 'd/m/Y H:i:s');
			                 $edd = date_format(date_create($row['weeks']), 'd/m/Y');
			                 $next_visit_date = $row['next_visit_date'];
                             $to_date = date('d/m/Y H:i:s');
                                $datefrom = DateTime::createFromFormat('d/m/Y H:i:s',$start_date);
                                $dateto = DateTime::createFromFormat('d/m/Y H:i:s',$to_date);
                                $interval = $dateto->diff($datefrom);
                                $weeks = floor($interval->format('%R%a')/7);
                            if($weeks < 2 && $weeks > -2){$style = "bg-info text-light";}elseif($weeks <= -2){$style = "bg-danger text-light";}else{$style = "";}
			                 $s = $s + 1;
			                 echo '
			             <tr class = "'.$style.'">
			                <td>'.$s.'</td>
			                <td>'.$token_no.'</td>
			                <td>'.get_patient_name_by_token_no($token_no).'</td>
			                <td>'.$phone.'</td>
			                <td>'.get_patient_age_by_token_no($token_no).'</td>
			                <td>'.$edd.'</td>
			                <td>'.$gravide.'</td>
			                <td>'.$next_visit_date.'</td>';
			                if($update_by == 0)
			                {
    			                echo '<td>NO UPDATE<td>';
			                }
			                else
			                {
    			                echo '<td>'.get_uname_by_id($update_by).'<td>';
			                }
		           echo '</tr>
			                 ';
			             }
			         }
			         else
			         {
			             echo "<script>window.close();</script>";
			         }
			         ?>
			        </tbody>
			    </table>
			</div>
		</div>
	</div>
</div>
</body>
</html>
<script type="text/javascript">
window.onafterprint = window.close;
</script>
<?php mysqli_close($con); ?>