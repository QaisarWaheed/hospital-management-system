<?php 
include 'includes/connect.php'; 
?>
<?php include 'includes/head.php'; ?>
	<title>Patient Registeration - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo" onkeydown="return (event.keyCode != 116)">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<a href = "dashboard.php" class = "btn btn-into">DASHBOARD</a>
		<label><h1><?php echo $company_name?> </h1></label>
	</div>
<div>

	<div class="">

		<div class="row" style="margin: 0px;">

			<div class="col-md-12">
	            <H1 CLASS = "H1" align = "center">GYNAE PATIENT HISTORY</H1>
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
                 echo '<div class = "row">';
	             while($row = mysqli_fetch_array($run))
	             {
	                 $id = $row['id'];
	                 $last_visit_date = $row['last_visit_date'];
	                 $previous_remarks = $row['previous_remarks'];
	                 $previous_gravide = $row['previous_gravide'];
	                 $previous_update_by = $row['previous_update_by'];
	                 $weeks_visit_time = $row['weeks_visit_time'];
	                 $s = $s + 1; ?>
	                 <div class = "col-md-12">
	                     <div class = "row">
	                         <div class = "col">
	                             <label>DATE</label>
	                             <p><?php echo $row['last_visit_date']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>Duration Pregnancy</label>
	                             <p><?php echo $row['duration_pregnancy']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>SFH</label>
	                             <p><?php echo $row['sfh']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>LIE</label>
	                             <p><?php echo $row['lie']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>PRESENTATION</label>
	                             <p><?php echo $row['presentation']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>FHR</label>
	                             <p><?php echo $row['fhr']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>B.P</label>
	                             <p><?php echo $row['bp']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>TEMP</label>
	                             <p><?php echo $row['temp']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>PULSE</label>
	                             <p><?php echo $row['pulse']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>V/M</label>
	                             <p><?php echo $row['v_m']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>RBS</label>
	                             <p><?php echo $row['rbs']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>RR</label>
	                             <p><?php echo $row['rr']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>EDEMA FEET</label>
	                             <p><?php echo $row['edema_feet']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>CUE</label>
	                             <p><?php echo $row['cue']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>CBC</label>
	                             <p><?php echo $row['cbc']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>OTHERS</label>
	                             <p><?php echo $row['others']; ?></p>
	                         </div>
	                         <div class = "col">
	                             <label>USG REPORT</label>
	                             <p><?php echo $row['usg_report']; ?></p>
	                         </div>
	                     </div>
	                 </div>
	         <?php    }
	         
                 echo '</div>';
	         }
	         ?>
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
<?php mysqli_close($con); ?>