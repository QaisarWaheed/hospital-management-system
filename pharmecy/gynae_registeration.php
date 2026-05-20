<?php 
include 'includes/connect.php'; 
if(isset($_GET['select_visit_date']) && $_GET['select_visit_date'] != '')
{
    $select_visit_date = $_GET['select_visit_date'];
}

if(isset($_GET['search_token']) && $_GET['search_token'] != '')
{
    $search_token = $_GET['search_token'];
}
?>
<?php include 'includes/head.php'; ?>
	<title>Patient Registeration - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo" onkeydown="return (event.keyCode != 116)">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>
<div>

	<div class="">

		<div class="row" style="margin: 0px;">

        	<div class="col-md-3 background_whitesmoke">
        		<?php include 'left_navigation.php'; ?>
        	</div>
			<div class="col-md-9">
			    <table class = "table table-hover table-boredered">
			        <thead>
			            <tr style = "text-align: center;">
			                <th>Sr</th>
			                <th>
			                    <form>
			                        <input maxlength = "11" onchange = "this.form.submit()" type = "number" name = "search_token" value = <?php if(isset($search_token)){echo '"'.$search_token.'"';}else{echo '"'.date('Y-m-d').'"';} ?> />
			                    </form>
			                    Token
		                    </th>
			                <th>Patient</th>
			                <th>Phone</th>
			                <th>Age</th>
			                <th>EDD</th>
			                <th>Gravida</th>
			                <th>
			                    <form>
			                        <input onchange = "this.form.submit()" type = "date" name = "select_visit_date" value = <?php if(isset($select_visit_date)){echo '"'.$select_visit_date.'"';}else{echo '"'.date('Y-m-d').'"';} ?> />
			                    </form>
			                    Next Visit
			                    </th>
			                <th>Update By</th>
			                <th colspan = "3">
			                    <?php if(isset($select_visit_date))
			                    {
			                        echo '<a target = "_blanck" href = "gynae_registeration_print.php?date='.$select_visit_date.'" class = "btn btn-outline-info link">PRINT</a>';
			                    }?>
			                    <a href = "gynae_registeration_file_add.php" class = "btn btn-outline-info link">ADD FILE</a><br>
			                    Action
			                </th>
			            </tr>
			        </thead>
			         <?php
			         $s = 0;
			         if(isset($select_visit_date) && $select_visit_date != '')
			         {
    			         $select = "SELECT * FROM `gynae_register` WHERE branch_id = '$branch_id' AND status = 1 AND next_visit_date <= '$select_visit_date' ORDER BY `next_visit_date` DESC ";
			         }
			         else
			         {
			             $select_visit_date = date('Y-m-d');
    			         $select = "SELECT * FROM `gynae_register` WHERE branch_id = '$branch_id' AND status = '1' AND next_visit_date <= '$select_visit_date' ORDER BY `next_visit_date` DESC ";
			         }
			         
			         if(isset($search_token) && $search_token != '')
			         {
    			         $select = "SELECT * FROM `gynae_register` WHERE status = 1 AND (`token_no` LIKE '%$search_token%' OR `phone` LIKE '%$search_token%') ";
			         }
			         $run = mysqli_query($con, $select);
			         if ($run && mysqli_num_rows($run) > 0)
			         {
			             while($row = mysqli_fetch_array($run))
			             {
			                 $id = $row['id'];
			                 $discharge_check = mysqli_query($con, "SELECT `gynae_discharge_id` FROM `gynae_register_discharge` WHERE `registeration_id` = '$id' ");
			                 $is_discharge = ($discharge_check && mysqli_num_rows($discharge_check) > 0) ? 1 : 0;
			                 $token_no = $row['token_no'];
			                 $update_by = $row['update_by'];
			                 $phone = $row['phone'];
			                 $gravide = $row['gravide'];
			                 $edd = ycdo_safe_date_format($row['weeks'], 'd/m/Y', 'N/A');
			                 $next_visit_date = $row['next_visit_date'];
			                 $style = ycdo_gynae_row_style($row['weeks']);
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
			                echo '<td>
			                        <a href="gynae_registeration_history.php?history='.$id.'" ><span  class = "btn btn-sm btn-info"><i class="fa fa-history" style="font-size:18px;"></i> INFO</span> </a>
		                        </td>
		                        <td>
			                        <a href="gynae_registeration_update.php?update='.$id.'"  class = "btn btn-sm btn-warning"><i class="fa fa-edit" style="font-size:18px;"></i> UPDATE </a>
    			                </td>';
    			                if($is_discharge != 0)
    			                {
    		                        echo '<td><a href="gynae_registeration_discharge_print.php?update='.$id.'"  class = "btn btn-sm btn-light"><i class="fa fa-print" style="font-size:18px;"></i> PRINT </a></td>';
    			                }
    			                else
    			                {
    		                        echo '<td><a href="gynae_registeration_discharge.php?update='.$id.'"  class = "btn btn-sm btn-primary"><i class="fa fa-home" style="font-size:18px;"></i> DISCHARGE </a></td>';
    			                }
		           echo '</tr>
			                 ';
			             }
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
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script type = "text/javascript" >  
    function preventBack() { window.history.forward(); }  
    setTimeout("preventBack()", 0);  
    window.onunload = function () { null };  
</script> 
<script type="text/javascript">
        // setTimeout(function () { window.close(); }, 120000);
</script>
<script>
function myDisplayGone() {
  document.getElementById("clear").style.display = "none";
}
</script> 
<script>
function myDisplayGoneAdd() {
  document.getElementById("add").style.display = "none";
}
</script> 
<script>
function myDisplayGoneSave() {
  document.getElementById("save").style.display = "none";
}
</script>