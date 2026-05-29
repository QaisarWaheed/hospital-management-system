<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
include 'includes/head.php'; 
?>
	<title>Lab Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke nodisplay_print">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	    <table class = "table table-bordered">
	        <caption style = "caption-side: top; color: black;text-align: center;">
	          <h2> ISSUED STOCK IN BRANCHES</h2>
	        </caption>
	        <thead>
	            <tr>
	                <th>SR #</th>
	                <th>DATE</th>
	                <th>ISSUE #</th>
	                <th>ITEM NAME</th>
	                <th>CATEGORY</th>
	                <th>QUANTITY</th>
	                <th>BRANCH</th>
	                <th>LAB ADMIN</th>
	                <th>STATUS</th>
	            </tr>
	        </thead>
	        <tbody>
	            <?php
	            $s = 0;
	            $select = "SELECT issue_lab_item_records.issue_lab_item_record_id, issue_lab_item_records.issue_lab_item_record_status, issue_lab_item_records.issue_lab_item_record_date, issue_lab_item_records.issue_lab_item_id , issue_lab_item_records.issue_lab_item_record_quantity, items.name AS item_name, categories.name AS cat_name, `reg_branch_item_id`, branchs.tag_name, users.u_name FROM `issue_lab_item_records` INNER JOIN items ON branch_item_id = items.id INNER JOIN categories ON items.category_id = categories.id INNER JOIN issue_lab_items ON issue_lab_item_records.issue_lab_item_id = issue_lab_items.issue_lab_item_id INNER JOIN branchs ON issue_lab_items.branch_id = branchs.id INNER JOIN users ON `issue_lab_item_record_created_by` = users.id WHERE `issue_lab_item_record_status` > '0' ";
	            $run = mysqli_query($con, $select);
	            if(mysqli_num_rows($run) > 0)
	            {
	                while($row = mysqli_fetch_array($run))
	                {
	                    $s++;   
	                    $issue_lab_item_record_status = $row['issue_lab_item_record_status'];
	                    ?>
	            <tr>
	                <td><?php echo $s; ?></td>
	                <td><?php echo date_format(date_create($row['issue_lab_item_record_date']), "d-M-Y"); ?></td>
	                <td><?php echo $row['issue_lab_item_id']; ?></td>
	                <td><?php echo $row['item_name']; ?></td>
	                <td><?php echo $row['cat_name']; ?></td>
	                <td><?php echo $row['issue_lab_item_record_quantity']; ?></td>
	                <td><?php echo $row['tag_name']; ?></td>
	                <td><?php echo $row['u_name']; ?></td>
	                <td><?php if($issue_lab_item_record_status == 1){echo '<div class = "badge badge-warning">NOT RECEIVED</div>';}else{echo '<div class = "badge badge-success">RECEIVED</div>';} ?></td>
	            </tr>
	                <?php }
	            }
	            ?>
	        </tbody>
	    </table>
	</div>
</div>

</body>
</html>