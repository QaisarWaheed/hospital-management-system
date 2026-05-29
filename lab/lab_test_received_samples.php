<?php
include 'includes/connect.php';
include 'includes/config.php';
require_once __DIR__ . '/includes/lab_test_list_helper.php';

$list_filters = lab_test_list_parse_filters($con, $lab_login_branch_id);
$selected_branch = $list_filters['branch_id'];
$list_result = lab_test_list_fetch($con, $list_filters, 2, 500, 'simple');
$list_rows = $list_result['rows'];
$list_truncated = $list_result['truncated'];

if(isset($_POST['update_lab_test_collected']) && $_POST['update_lab_test_collected'] != '')
{
    $lab_test_id = $_POST['lab_test_id'];
    $selected_branch = $_POST['selected_branch'];
    $lab_test_collected_comments = $_POST['lab_test_collected_comments'];
    $update = "UPDATE `lab_tests` SET `lab_test_collected_comments` = '$lab_test_collected_comments', `lab_test_collected_created_by` = '$lab_user_id', `lab_test_collected_created_at` = '$current_date', `lab_test_status_id` = '3' WHERE `lab_test_id` = '$lab_test_id' ";
    mysqli_query($con, $update);
    header('location: lab_test_received_samples.php?search=1&selected_branch=' . (int) $selected_branch . '&msg=updated#' . $lab_test_id);
    exit(0);
}
?>
	<title>SAMPLES IN LAB FOR TEST (<?php echo date('d-m-Y'); ?>)- <?php echo $lab_login_branch_name; ?> - LAB - <?php echo $company_trademark; ?></title>
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
    	            <h1>SAMPLES IN LAB FOR TEST (<?php echo date('d-m-Y'); ?>)</h1>
                    <?php if ($list_truncated) { ?>
                        <p class="text-warning">Showing latest 500 records. Narrow dates or pick one branch.</p>
                    <?php } else { ?>
                        <p class="text-muted">Showing status 2 samples by sample date (default: last 14 days). Adjust dates or branch and click Search.</p>
                    <?php } ?>
    	        </caption>
    	        <thead>
			<tr>
                <th colspan = "12">
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
                        <form method="get">
                            <input type="hidden" name="search" value="1" />
                            <div class="row">
                                <div class="col-md-6">
                                    <label>From</label>
                                    <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($list_filters['date_from'], ENT_QUOTES, 'UTF-8'); ?>" required />
                                </div>
                                <div class="col-md-6">
                                    <label>To</label>
                                    <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($list_filters['date_to'], ENT_QUOTES, 'UTF-8'); ?>" required />
                                </div>
                                <div class="col-md-12">
                                    <label>Branch</label>
                                    <select name="selected_branch" class="form-control" required>
                                        <?php
                                        $query = "SELECT id, tag_name FROM branchs WHERE status = '1' ORDER BY tag_name";
                                        $run = mysqli_query($con, $query);
                                        if ($run) {
                                            echo '<option value="0"' . ($selected_branch === 0 ? ' selected' : '') . '>ALL (max 7 days)</option>';
                                            while ($row = mysqli_fetch_assoc($run)) {
                                                $sel = ((int) $row['id'] === $selected_branch) ? ' selected' : '';
                                                echo '<option value="' . (int) $row['id'] . '"' . $sel . '>' . htmlspecialchars($row['tag_name'], ENT_QUOTES, 'UTF-8') . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-success mt-2">Search</button>
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
    	                <th>Test Name</th>
    	                <th>Patient Name</th>
    	                <th>Phone</th>
    	                <th>Age</th>
    	                <th>Register At</th>
    	                <th>Register By</th>
    	                <th>Collected At</th>
    	                <th>Collected By</th>
    	            </tr>
    	        </thead>
    	        <tbody>
    	            <?php
    	            $s = 0;
    	            if (!empty($list_rows))
    	            {
    	                foreach ($list_rows as $row_sample)
    	                {
    	                    $s++; ?>
    	            <form method = "POST">
    	            <tr>
    	                <td><?php echo $s; ?></td>
    	                <td><?php echo $row_sample['main_branch_name'].' - '.$row_sample['token_no']; ?>
    	                    <a href="#" class = "btn btn-sm btn-success" onClick="MyWindow=window.open('lab_test_current_status.php?lab_test_id=<?php echo $row_sample['lab_test_id']; ?>','MyWindow','width=900,height=1200'); return false;">+</a>
	                    </td>
    	                <td><?php echo $row_sample['test_name']; ?></td>
    	                <td><?php echo $row_sample['name']; ?></td>
    	                <td><?php echo $row_sample['phone']; ?></td>
    	                <td><?php echo $row_sample['age']; ?></td>
    	                <td><?php echo date_format(date_create($row_sample['register_at']), 'h:i:s A d-m-Y'); ?></td>
    	                <td><?php echo $row_sample['register_by']; ?></td>
    	                <td><?php echo date_format(date_create($row_sample['collected_at']), 'h:i:s A d-m-Y'); ?></td>
    	                <td><?php echo $row_sample['collected_by']; ?></td>
    	                <td>
    	                    <input type = "hidden" name = "lab_test_id" value = "<?php echo $row_sample['lab_test_id']; ?>" />
    	                    <input type = "hidden" name = "selected_branch" value = "<?php echo $selected_branch; ?>" />
    	                    <input title = "type comments of 30 latters only." type = "test" maxlength = "30" name = "lab_test_collected_comments" class = "form-control" />
    	                </td>
    	                <td>
    	                    <input type = "submit" value = "UPDATE DATA" name = "update_lab_test_collected" class = "btn-success" />
    	                </td>
    	            </tr>
    	            </form>
    	                <?php }
    	            }
    	            else
    	            {
    	                echo '<tr><th colspan = "9">NO PENDING SAMPLES IN LAB</th></tr>';
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