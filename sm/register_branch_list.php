<?php 
    include 'includes/connect.php'; 
    include 'includes/head.php'; 
?>
	<title>BRANCH LIST - <?php echo $company_trademark; ?></title>
<style>
@page 
{
  size: A4;
  margin: 10px 0px 10px 0px;
}
@media print 
{
html, body 
{
    width: 210mm;
    height: 297mm;
    font-size: 9px;
}
.noprint
{
    display: none;
}
}    
</style>	
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

		<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
				<label><h1>ISSUANCE LIST</h1></label>
			</div>
			<div class="col-md-12 ">
			    <table class = "table table-hover">
			    <thead>
			        <tr>
			            <th>Sr #</th>
			            <th>DATE</th>
			            <th>ISSUE NO</th>
			            <th>
			                <form>
			                <select name = "issue_branch" onchange="this.form.submit()" class = "form-control">
			                <?php
			                if(isset($_GET['issue_branch']) && $_GET['issue_branch'] != '')
			                {
			                    $issue_branch = $_GET['issue_branch'];
			                }
			                else
			                {
			                    $issue_branch = $branch_id;
			                }
                                $select = "SELECT id, address FROM `branchs` WHERE `status` = '1' ";
                                $run = mysqli_query($con, $select);
                                if (mysqli_num_rows($run) > 0) 
                                {
                                    while ($row = mysqli_fetch_array($run)) 
                                    {
                                        if($issue_branch == $row['id'])
                                        {
                                            echo '<option SELECTED value = "'.$row['id'].'">'.$row['address'].'</option>';
                                        }
                                        else
                                        {
                                            echo '<option value = "'.$row['id'].'">'.$row['address'].'</option>';       
                                        }
                                    }    
                                }
                            ?>
			                </select>
                            </form>
			                BRANCH
			            </th>
			            <th>ISSUE ITEMS</th>
			            <th>RECEIVE ITEMS</th>
			            <th>STATUS</th>
			            <th>ACTION</th>
			        </tr>
			    </thead>
			    <tbody>
			       <?php
			       $s = 0;
			       $select = "SELECT DISTINCT `issue_id` AS issue_no, branchs.address AS br_tag, COUNT(item_register_branchs_by_sm.branch_item_id),item_register_branchs_by_sm.created, COALESCE(SUM(`item_register_branchs_by_sm`.`status`), 2) FROM `item_register_branchs_by_sm` INNER JOIN branchs ON item_register_branchs_by_sm.branch_id = branchs.id WHERE item_register_branchs_by_sm.branch_id = '$issue_branch' GROUP BY item_register_branchs_by_sm.issue_id ORDER BY `issue_no` DESC; ";
			       $run = mysqli_query($con, $select);
			       if(mysqli_num_rows($run) > 0)
			       {
			           while($row = mysqli_fetch_array($run))
			           {
			               $s++;
			               $receive = $row['4']-$row['2'];
			               if($receive == $row['2'])
			               {
    			               $status = '<td class = "badge badge-success">COMPLETE</td>';
			               }
			               else
			               {
    			               $status = '<td class = "badge badge-danger">PENDING</td>';
			               }
			               echo '<tr><td>'.$s.'</td><td>'.date_format(date_create($row['3']), "d-M-Y").'</td><td>'.$row['0'].'</td><td>'.$row['1'].'</td><td>'.$row['2'].'</td><td>'.$receive.'</td>'.$status.'<td><a class = "btn btn-primary" href = "item_register_branch.php?bill_no='.$row['0'].'">SHOW</a></td></tr>';
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