<?php 
include 'includes/connect.php'; 
include 'includes/config.php'; 
$selected_branch = $lab_login_branch_id;
if(isset($_GET['selected_branch']) && $_GET['selected_branch'] != '')
{
    $selected_branch = $_GET['selected_branch'];
    if($_GET['selected_branch'] == 0)
    {    
        $received_samples = "SELECT DISTINCT lab_tests.token_no, lab_test_id, patients.name,  items.name AS test_name, patients.age, patients.phone, patients.cnic, branchs.tag_name AS main_branch_name, tokans.created AS added_at, added.u_name AS added_by, lab_tests.lab_test_processed_created_at AS processed_at, processed.u_name AS processed_by, lab_tests.sample_date_time AS collected_at, collected.u_name AS collected_by, lab_tests.lab_test_conducted_created_at AS conducted_at, conducted.u_name AS conducted_by FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id LEFT JOIN users added ON tokans.user_id = added.id LEFT JOIN users collected ON lab_tests.user_id = collected.id LEFT JOIN users processed ON lab_tests.lab_test_processed_created_by = processed.id LEFT JOIN users conducted ON lab_tests.lab_test_conducted_created_by = conducted.id INNER JOIN items ON lab_tests.item_id = items.id INNER JOIN branchs ON tokans.branch_id = branchs.id WHERE `lab_test_status_id` = '5' GROUP BY lab_tests.lab_test_id ORDER BY `lab_tests`.`token_no` DESC ";
    }
    else
    {
        $selected_branch = $_GET['selected_branch'];
        $received_samples = "SELECT DISTINCT lab_tests.token_no, lab_test_id, patients.name,  items.name AS test_name, patients.age, patients.phone, patients.cnic, branchs.tag_name AS main_branch_name, tokans.created AS added_at, added.u_name AS added_by, lab_tests.lab_test_processed_created_at AS processed_at, processed.u_name AS processed_by, lab_tests.sample_date_time AS collected_at, collected.u_name AS collected_by, lab_tests.lab_test_conducted_created_at AS conducted_at, conducted.u_name AS conducted_by FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id LEFT JOIN users added ON tokans.user_id = added.id LEFT JOIN users collected ON lab_tests.user_id = collected.id LEFT JOIN users processed ON lab_tests.lab_test_processed_created_by = processed.id LEFT JOIN users conducted ON lab_tests.lab_test_conducted_created_by = conducted.id INNER JOIN items ON lab_tests.item_id = items.id INNER JOIN branchs ON tokans.branch_id = branchs.id WHERE tokans.branch_id = '$selected_branch' AND `lab_test_status_id` = '5' GROUP BY lab_tests.lab_test_id ORDER BY `lab_tests`.`token_no` DESC ";
    }
}
else
{
    $received_samples = "SELECT DISTINCT lab_tests.token_no, lab_test_id, patients.name,  items.name AS test_name, patients.age, patients.phone, patients.cnic, branchs.tag_name AS main_branch_name, tokans.created AS added_at, added.u_name AS added_by, lab_tests.lab_test_processed_created_at AS processed_at, processed.u_name AS processed_by, lab_tests.sample_date_time AS collected_at, collected.u_name AS collected_by, lab_tests.lab_test_conducted_created_at AS conducted_at, conducted.u_name AS conducted_by FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id LEFT JOIN users added ON tokans.user_id = added.id LEFT JOIN users collected ON lab_tests.user_id = collected.id LEFT JOIN users processed ON lab_tests.lab_test_processed_created_by = processed.id LEFT JOIN users conducted ON lab_tests.lab_test_conducted_created_by = conducted.id INNER JOIN items ON lab_tests.item_id = items.id INNER JOIN branchs ON tokans.branch_id = branchs.id WHERE tokans.branch_id = '$selected_branch' AND `lab_test_status_id` = '5' GROUP BY lab_tests.lab_test_id ORDER BY `lab_tests`.`token_no` DESC ";
}
?>
	<title>APPROVED REPORT TEST(<?php echo date('d-m-Y'); ?>)- <?php echo $lab_login_branch_name; ?> - LAB - <?php echo $company_trademark; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
    .background_image{
        background-image: url('../images/background.png');
        background-size: cover;
    }
    </style>    
    <style>
        @media print {
            body {
                /* Reduce the base font size for the entire page to 12px */
                font-size: 12px; 
            }

            table {
                font-size: 0.8em; 
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body class="background_image">
<?php include 'top_navigation.php'; ?>
    <div class="row">
    	<div class="col-md-12">
    	    <table class = "table table-bordered table-hover" style = "color: black'" id="myTable">
    	        <caption style = "text-align: center; caption-side: top;color: black;">
    	            <h1>APPROVED REPORT TEST(<?php echo date('d-m-Y'); ?>)</h1>
    	        </caption>
    	        <thead>
        			<tr>
                        <th colspan = "13">
                            <div class = "row">
                                <div class = "col-md-3">
                                    <label>Token #</label>
                                    <input type="text" placeholder = "ENTER TOKEN NO FOR SEARCH..." class = "form-control" id="myInput" onkeyup="myFunction()" title="Type a token #">
                                </div>
                                <div class = "col-md-2">
                                    <label>Test Name</label>
                                    <input type = "text" class = "form-control" id="myInputTestName" onkeyup="myFunctionTestName()" placeholder="Search for names.." />
                                </div>
                                <div class = "col-md-2">
                                    <label>Patient Name</label>
                                    <input type = "text" class = "form-control" id="myInputName" onkeyup="myFunctionName()" placeholder="Search for names.." />
                                </div>
                                <div class = "col-md-2">
                                    <label>Phone</label>
                                    <input type = "text" class = "form-control" id="myInputPhone" onkeyup="myFunctionPhone()" />
                                </div>
                                <div class = "col-md-3">
                                <form>
                                    <div class = "row">
                                        <div class = "col-md-12">
                                            <label>Branch</label>
                                            <select name = "selected_branch" class = "form-control" required>
                                                <?php
                                                $query = "SELECT id, tag_name FROM branchs WHERE status = '1' ";
                                                $run = mysqli_query($con,  $query);
                                                if (mysqli_num_rows($run) > 0) 
                                                {
                                                            echo '<option value = "0">ALL</option>';
                                                    while ($row = mysqli_fetch_array($run)) 
                                                    {
                                                        if($selected_branch == $row['id'])
                                                        {
                                                            echo '<option SELECTED value = "'.$row['id'].'">'.$row['tag_name'].'</option>';                                                    
                                                        }
                                                        else
                                                        {
                                                            echo '<option value = "'.$row['id'].'">'.$row['tag_name'].'</option>';
                                                        }
                                                    }    
                                                }
                                                ?>
                                            </select>
                                            <input type = "submit" value = "SEARCH" class = " btn-sm btn-success" />
                                        </div>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </th>
        			</tr>
    	            <tr>
    	                <th>S #</th>
    	                <th>Token #</th>
    	                <th>Test</th>
    	                <th>Patient Name</th>
    	                <th>Phone</th>
    	                <th>Age</th>
    	                <th>Added At</th>
    	                <th>Added By</th>
    	                <th>Collected At</th>
    	                <th>Collected By</th>
    	                <th>Processed At</th>
    	                <th>Processed By</th>
    	                <th>Type Report</th>
    	            </tr>
    	        </thead>
    	        <tbody>
    	            <?php
    	            $s = 0;
    	            $run_sample = mysqli_query($con, $received_samples);
    	            if(mysqli_num_rows($run_sample) > 0)
    	            {
    	                while($row_sample = mysqli_fetch_array($run_sample))
    	                {
    	                    $s++;
    	                    ?>
    	            <tr>
    	                <td><?php echo $s; ?></td>
    	                <td><?php echo $row_sample['main_branch_name'].' - '.$row_sample['token_no']; ?><a href="#" class = "btn btn-sm btn-success" onClick="MyWindow=window.open('lab_test_current_status.php?lab_test_id=<?php echo $row_sample['lab_test_id']; ?>','MyWindow','width=900,height=1200'); return false;">+</a></td>
    	                <td><?php echo $row_sample['test_name']; ?></td>
    	                <td><?php echo $row_sample['name']; ?></td>
    	                <td><?php echo $row_sample['phone']; ?></td>
    	                <td><?php echo $row_sample['age']; ?></td>
    	                <td><?php echo date_format(date_create($row_sample['added_at']), 'h:i:s A d-m-Y'); ?></td>
        	                <td><?php echo $row_sample['added_by']; ?></td>
    	                <td><?php echo date_format(date_create($row_sample['collected_at']), 'h:i:s A d-m-Y'); ?></td>
        	                <td><?php echo $row_sample['collected_by']; ?></td>
        	                <td><?php echo date_format(date_create($row_sample['processed_at']), 'h:i:s A d-m-Y'); ?></td>
        	                <td><?php echo $row_sample['processed_by']; ?></td>
        	                <td>
        	                    <a href="#" class = "btn btn-sm btn-success" onClick="MyWindow=window.open('lab_test_report_view.php?lab_test_id=<?php echo $row_sample['lab_test_id']; ?>','MyWindow','width=900,height=1200'); return false;"><span class="glyphicon glyphicon-pencil">U</span></a>
        	                </td>
    	            </tr>
    	                <?php }
    	            }
    	            else
    	            {
    	                echo '<tr><th colspan = "8">NO PENDING SAMPLES IN PROCESS</th></tr>';
    	            }
    	            ?>
    	        </tbody>
    	    </table>
    	</div>
    </div>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script>
function myFunction() 
{
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) 
    {
        user_name = tr[i].getElementsByTagName("td")[1];
        if (user_name) 
        {
            txtValue = user_name.textContent || user_name.innerText;
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
function myFunctionTestName() 
{
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInputTestName");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) 
    {
        user_name = tr[i].getElementsByTagName("td")[2];
        if (user_name) 
        {
            txtValue = user_name.textContent || user_name.innerText;
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
function myFunctionName() 
{
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInputName");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) 
    {
        user_name = tr[i].getElementsByTagName("td")[3];
        if (user_name) 
        {
            txtValue = user_name.textContent || user_name.innerText;
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
function myFunctionPhone() 
{
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInputPhone");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) 
    {
        user_name = tr[i].getElementsByTagName("td")[4];
        if (user_name) 
        {
            txtValue = user_name.textContent || user_name.innerText;
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
<?php mysqli_close($con); ?>