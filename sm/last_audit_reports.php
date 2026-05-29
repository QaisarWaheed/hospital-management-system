<?php 
include 'includes/connect.php';
include 'includes/head.php'; 
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>
<body class="background_image">
<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	    <div class = "">
	        <table class = "table table-hover table-bordered table-sm">
	            <thead>
	                <tr>
	                    <th>Ser #</th>
	                    <th>Branch Name</th>
	                    <th>Audit #</th>
	                    <th>Audit Date</th>
	                    <th>Action</th>
	                </tr>
	            </thead>
	            <tbody>
	                <?php
	                $s = 0;
	                $select = "SELECT * FROM `branchs` WHERE branchs.status = 1 ";
	                $run = mysqli_query($con, $select);
	                if(mysqli_num_rows($run) > 0)
	                {
	                    while($row = mysqli_fetch_array($run))
	                    {
	                        $s++;
	                        $branch_id = $row['id'];
	                        $select_audit = "SELECT id, created FROM `audit_branch_form` WHERE branch_id = $branch_id ORDER BY `audit_branch_form`.`id` DESC LIMIT 0,1 ";
        	                $run_audit = mysqli_query($con, $select_audit);
        	                if(mysqli_num_rows($run_audit) > 0)
        	                {
        	                    while($row_audit = mysqli_fetch_array($run_audit))
        	                    {
        	                        $audit_id = $row_audit['id'];
        	                        $audit_created = $row_audit['created'];
        	                    }
        	                }
                        echo '<tr>';
	                        echo '<td>'.$s.'</td>';
	                        echo '<td>'.$row['address'].'</td>';
	                        echo '<td>'.$audit_id.'</td>';
	                        echo '<td>'.date_format(date_create($audit_created), "d-M-Y").'</td>';
	                        echo '<td><a href = "audit_short_report.php?audit_id='.$audit_id.'&br_id='.$branch_id.'">OPEN</a></td>';
                        echo '</tr>';
	                    }
	                }
	                ?>
	            </tbody>
	        </table>
	    </div>
	</div>
</div>

</body>
</html>