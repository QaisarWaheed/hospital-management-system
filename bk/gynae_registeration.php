<?php
require_once __DIR__ . '/includes/connect.php';

$br_id = isset($_GET['br_id']) ? (int) $_GET['br_id'] : (int) $bk_branch_id;
$select_visit_date = isset($_GET['select_visit_date']) && $_GET['select_visit_date'] !== ''
    ? $_GET['select_visit_date']
    : date('Y-m-d');
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
			                        <select  onchange = "this.form.submit()" name = "br_id" required>  
			                            <option <?php if ($br_id === 0) { echo 'SELECTED'; } ?> value="0">ALL</option>
			                        <?php
                                        $query = "SELECT id, tag_name FROM branchs WHERE status = '1' ";
                                        $run = mysqli_query($con,  $query);
                                        if ($run && mysqli_num_rows($run) > 0) 
                                        {
                                            while ( $row = mysqli_fetch_array($run) ) 
                                            {
                                                $select_br_id = $row['id'];
                                                $select_br_tag_name = $row['tag_name'];
                                                $selected = ($br_id === (int) $select_br_id) ? ' SELECTED' : '';
                                                echo '<option'.$selected.' value="'.$select_br_id.'">'.htmlspecialchars($select_br_tag_name).'</option>';
                                            }    
                                        }    
			                        ?>
			                        </select>
			                        <input type = "hidden" name = "select_visit_date" value = <?php if(isset($select_visit_date)){echo '"'.$select_visit_date.'"';}else{echo '"'.date('Y-m-d').'"';} ?> />
			                    </form>
			                    Branch</th>
			                <th>Token</th>
			                <th>Patient Name</th>
			                <th>Phone</th>
			                <th>Age</th>
			                <th>Weeks</th>
			                <th>Gravida</th>
			                <th>
			                    <form>
			                        <input type = "hidden" name = "br_id" value = "<?php echo (int) $br_id; ?>" />
			                        <input onchange = "this.form.submit()" type = "date" name = "select_visit_date" value = <?php if(isset($select_visit_date)){echo '"'.$select_visit_date.'"';}else{echo '"'.date('Y-m-d').'"';} ?> />
			                    </form>
			                    Next Visit
			                    </th>
			                <th>
			                    <a target="_blank" href = "gynae_registeration_print.php?br_id=<?php echo (int) $br_id; ?>&select_visit_date=<?php echo htmlspecialchars($select_visit_date); ?>">PRINT</a>
			                    <br>Update By
			                </th>
			                <th colspan = "3">
			                    <a class = "btn btn-info btn-sm" href = "gynae_registeration_file_add.php">ADD FILE</a>
			                    <br>Action
			                </th>
			            </tr>
			        </thead>
			        <tbody>
			         <?php
			         $s = 0;
			         if ($br_id > 0) {
        			         $select = "SELECT * FROM `gynae_register` WHERE branch_id = '$br_id' AND status = 1 AND next_visit_date <= '$select_visit_date' ORDER BY `next_visit_date` DESC ";
			         } else {
        			         $select = "SELECT * FROM `gynae_register` WHERE status = 1 AND next_visit_date <= '$select_visit_date' ORDER BY `next_visit_date` DESC ";
			         }
			         $run = mysqli_query($con, $select);
			         if ($run && mysqli_num_rows($run) > 0)
			         {
			             while($row = mysqli_fetch_array($run))
			             {
			                 $id = $row['id'];
			                 $row_br_id = $row['branch_id'];
			                 $token_no = $row['token_no'];
			                 $update_by = $row['update_by'];
			                 $phone = $row['phone'];
			                 $gravide = $row['gravide'];
			                 $weeks = ycdo_gynae_weeks_offset($row['weeks']);
			                 $style = ycdo_gynae_row_style($row['weeks']);
			                 $next_visit_date = $row['next_visit_date'];
			                 $s = $s + 1;
			                 echo '
			             <tr class = "'.$style.'">
			                <td>'.$s.'</td>
			                <td>'.get_branch_tag_name_by_id($row_br_id).'</td>
			                <td>'.$token_no.'</td>
			                <td>'.get_patient_name_by_token_no($token_no).'</td>
			                <td>'.$phone.'</td>
			                <td>'.get_patient_age_by_token_no($token_no).'</td>
			                <td>'.$weeks.'</td>
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
			                        <a href="gynae_registeration_history.php?history='.$id.'"  class = "btn btn-sm"><i class="fa fa-history" style="font-size:18px;color:green"></i> </a>
			                        <a href="gynae_registeration_delete.php?del='.$id.'"  class = "btn btn-sm"><i class="fa fa-remove" style="font-size:18px;color:yellow"></i> </a>
		                        </td>';
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
<?php mysqli_close($con); ?>