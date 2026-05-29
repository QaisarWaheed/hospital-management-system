<?php 
include 'includes/connect.php'; 

if (isset($_GET['add_token']) && $_GET['enter_token'] != '' && $_GET['add_token']) 
{
	$token_no = $_GET['enter_token'];
	$select = "SELECT * FROM tokans WHERE id = '$token_no' AND `tokan_type_id` < 100 ";
	$run = mysqli_query($con, $select);
	if (mysqli_num_rows($run) == 1) 
	{
		while ($row = mysqli_fetch_array($run)) 
		{
			$patient_id = $row['patient_id'];
            $select_patient = "SELECT * FROM patients WHERE id = '$patient_id' ";
            $run_patient = mysqli_query($con, $select_patient);
            if (mysqli_num_rows($run_patient) == 1) 
            {
                while ($row_patient = mysqli_fetch_array($run_patient)) 
                {
                	$phone = $row_patient['phone'];
                	$cnic = $row_patient['cnic'];
                	if ($phone == NULL || $cnic == NULL) 
                	{
                		$error = "NOT VALID TOKEN NO";
                	}
                	else
                	{
                		header('location: second_procedure_turn.php?search_tokan_no='.$token_no);
                	}
                }
            }
		}
	}
	else
	{
	$error = "ENTER A VALID TOKEN NO";
	}
}

include 'includes/head.php'; 
?>
	<title>Branch Procedure Token - <?php echo $company_trademark; ?></title>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script></head>

<body class="background_image_ycdo">

<div>

<div class="" style="margin: 10px 15px;">

	<div class="row">
        <div class = "col-12 bg-light p-1">
            <?php include "navigation_dashboard.php"; ?>
        </div>
        <div class = "col-12">
    		<div class = "row">
    		    <div class = "col-12">
        		    <h2 style = "text-align: center;">PROCEDURE TOKEN</h2>
        			<form method="GET">
                        <?php if (isset($_GET['add_token']) && isset($error)) {echo '<div class="alert alert-success alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><label>'.$error.'</label></div>';} ?>
        			    <label>ENTER PATIENT COMPLETE REGISTRATION TOKEN NO</label>

        				<input type="number" name="enter_token" / class = "form-control">
        				<input type="submit" name="add_token" class="btn btn-sm btn-primary" style = "margin-top: 15px;" />
        			</form>
    		    </div>
    			<div class = "col-12">
            		<table class="table" id="myTable">
            			<thead>
            				<caption style="caption-side: top;text-align: center;">
            					<h3> PROCEDURES DETAIL</h3>
            				</caption>
            				<tr>
            				    <th colspan = "6">
            				        <input type="text" id="nameInput" onkeyup="nameFunction()" placeholder="Search names.." title="Type Name" class = "form-control">
            				    </th>
            				    <th colspan = "6">
            				        <input type="text" id="tokenInput" onkeyup="tokenFunction()" placeholder="Search tokens.." title="Type Token" class = "form-control">
            				    </th>
            				</tr>
            				<tr>
            					<th>S #</th>
            					<th>Token Date</th>
            					<th>Patient Name</th>
            					<th>Gardian Name</th>
            					<th>Token No</th>
            					<th>Token Type</th>
            					<th>Total Amount</th>
            					<th>Received Amount</th>
            					<th>Pending Amount</th>
            					<th>Pending Recomended BY</th>
            					<th>Pending Recievings</th>
            					<th>Update</th>
            				</tr>
            			</thead>
            			<tbody>
            <?php
            $date = date('Y-m');
            // $date = date("Y-m-d", strtotime("-1 week"));
            // $select_branch_pending_query = "SELECT * FROM `branch_pending_details` WHERE (id IN (1) AND status = '1' AND branch_id = '$branch_id') OR (status = '1' AND branch_id = '$branch_id' AND created like '$date%') ";
            // $select_branch_pending_query = "SELECT * FROM `branch_pending_details` WHERE (id IN (1) AND status = '1' AND branch_id = '$branch_id') OR (status = '1' AND branch_id = '$branch_id' AND created > '$date') ";
            $s = 0;
            
            if (isset($_GET['enter_search_token']) && $_GET['enter_search_token'] != '') 
            {
                $enter_search_token = $_GET['enter_search_token'];
                $select_branch_pending_query = "SELECT * FROM `branch_pending_details` WHERE (token_no = '$enter_search_token' AND status = '1' AND branch_id = '$branch_id') OR (token_no IN (SELECT id FROM tokans WHERE patient_id IN (SELECT id FROM patients WHERE `name` LIKE '%$enter_search_token%'))AND status = '1' AND branch_id = '$branch_id') ";
                // exit(0);
            }
            else
            {
            $pending_ids = get_pending_id($branch_id);
            $select_branch_pending_query = "SELECT * FROM `branch_pending_details` WHERE (id IN (1) AND status = '1' AND branch_id = '$branch_id') OR (status = '1' AND branch_id = '$branch_id') ORDER BY `id` DESC limit 0, 500";
            }
            $select_branch_pending = mysqli_query($con, $select_branch_pending_query);
            if (mysqli_num_rows($select_branch_pending) > 0) 
            {
            	while ($row_branch_data = mysqli_fetch_array($select_branch_pending) ) 
            	{
            		$amount = 0;
            		$branch_pending_id = $row_branch_data['id'];
            		$gardian_name = $row_branch_data['gardian_name'];
            		$gardian_phone = $row_branch_data['gardian_phone'];
            		$recommended_by = $row_branch_data['recommended_by'];
            		$token_no = $row_branch_data['token_no'];
            		$select_token = mysqli_query($con, "SELECT * FROM `tokans` WHERE id = '$token_no' ");
            		if (mysqli_num_rows($select_token) > 0) 
            		{
            			while ($row_token = mysqli_fetch_array($select_token)) 
            			{
            				$token_no = $row_token['id'];
                                $run2 = mysqli_query($GLOBALS['con'], "SELECT * FROM `branch_pending_receive` WHERE token_no = '$token_no' AND status = '1' ");
                                if (mysqli_num_rows($run2) > 0) 
                                {
                                    while ($row = mysqli_fetch_array($run2)) 
                                    {
                                        $amount = $amount - $row['amount'];
                                    }
                                }
            				$token_type_title = token_type_title($row_token['tokan_type_id']);
            				$patient_name = get_patient_name_by_id($row_token['patient_id']);
            				$total_amount = pharmecy_resolve_branch_pending_display_amount(
            				    $con,
            				    $token_no,
            				    $row_branch_data['amount'] ?? 0
            				);
            				$recieved_amount = $row_token['cash_received'];
            				$created = $row_token['created'];
            			}
            		}
            
                $pending_amount = intval($total_amount-($recieved_amount-$amount));
                
            	if($pending_amount > 0)
            	{
            		$s = $s + 1;
                    echo '
                    <tr>
                    	<td>'.$s.'</td>
                    	<td>'.date_format(date_create($created), "d-m-Y").'</td>
                    	<td>'.$patient_name.'</td>
                    	<td>'.$gardian_name.'</td>
                    	<td><a class="btn btn-sm btn-outline-info" href="branch_pending_complete_detail.php?token_no='.$token_no.'"">'.$token_no.'</a></td>
                    	<td>'.$token_type_title.'</td>
                    	<td>'.$total_amount.'</td>
                    	<td>'.$recieved_amount.'</td>
                    	<td>'.$pending_amount.'</td>	
                    	<td>'.$recommended_by.'</td>';
                    	echo '<td>
                    		<a class="btn btn-sm btn-outline-info" href="procedure_pending_amount.php?search_tokan_no='.$token_no.'">Pay Amount</a>
                    	</td>';
                    	if ($branch_pending_id != 0) {
                    	echo '<td><a href="branch_pending_detail_update.php?u_id='.$branch_pending_id.'">Update</a></td>';
                    	}
                    	else
                    		echo '<td></td>';	
                    echo '</tr>
                    ';
            	}
            
            	}
            }
            ?>
            			</tbody>
            		</table>
    			    
    			</div>
    		</div>
        </div>

	</div>
</div>

</div>

</body>
</html>
<script>
function nameFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("nameInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[2];
    if (td) 
    {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) 
        {
            tr[i].style.display = "";
        } 
        else 
        {
            tr[i].style.display = "none";
        }
    }       
  }
}
</script>
<script>
function tokenFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("tokenInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[4];
    if (td) 
    {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) 
        {
            tr[i].style.display = "";
        } 
        else 
        {
            tr[i].style.display = "none";
        }
    }       
  }
}
</script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<!-- 
 -->