<?php 
include 'includes/connect.php'; 
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
			        <caption style = "caption-side: top;">
			            <H1 CLASS = "H1" align = "center">GYNAE PATIENT HISTORY</H1>
			        </caption>
			        <thead>
			            <tr>
			                <th>Sr</th>
			                <th>Weeks</th>
			                <th>Visit Date</th>
			                <th>Gravida</th>
			                <th>Remarks</th>
			                <th>Update By</th>
			            </tr>
			        </thead>
			        <tbody>
			         <?php
			         if(isset($_GET['history']))
			         {
			             if($_GET['history'] != '')
			             {
    			             $history_id = $_GET['history']; 
			             }
			             else
			             { ?>
                         <script>
                             alert("YOUR ARE NOT ALLOWED TP DO THAT");
                             window.location.replace("gynae_registeration.php");
                         </script>  
    			         <?php
    			             exit();
			             }
			         }
			         else
			         { ?>
                         <script>
                             alert("FILE DATA SAVE SUCCESSFULLY...");
                             window.location.replace("gynae_registeration.php");
                         </script>  
			         <?php    exit();
			         }
			         $s = 0;
			         $select = "SELECT * FROM `gynae_register_history` WHERE gynae_register_id = '$history_id' ";
			         $run = mysqli_query($con, $select);
			         if(mysqli_num_rows($run) > 0)
			         {
			             while($row = mysqli_fetch_array($run))
			             {
			                 $id = $row['id'];
			                 $last_visit_date = $row['last_visit_date'];
			                 $previous_remarks = $row['previous_remarks'];
			                 $previous_gravide = $row['previous_gravide'];
			                 $previous_update_by = $row['previous_update_by'];
			                 $weeks_visit_time = $row['weeks_visit_time'];
			                 $s = $s + 1;
			                 echo '
			             <tr class = "'.$style.'">
			                <td>'.$s.'</td>
			                <td>'.$weeks_visit_time.'</td>
			                <td>'.ycdo_safe_date_format($last_visit_date, 'd-m-Y', 'N/A').'</td>
			                <td>'.$previous_gravide.'</td>
			                <td>'.$previous_remarks.'</td>';
			                if($previous_update_by == 0)
			                {
    			                echo '<td>NO UPDATE<td>';
			                }
			                else
			                {
    			                echo '<td>'.get_uname_by_id($previous_update_by).'<td>';
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
<?php mysqli_close($con); ?>