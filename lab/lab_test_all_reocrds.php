<?php 
include 'includes/connect.php'; 
include 'includes/config.php';
$selected_branch = $lab_login_branch_id;
// $from_selected_date = date('Y-m-d');
$from_selected_date = date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
$to_selected_date = date('Y-m-d');
if(isset($_GET['selected_branch']) && $_GET['selected_branch'] != '')
{
    $selected_branch = $_GET['selected_branch'];
    $from_selected_date = $_GET['from_selected_date'];
    $to_selected_date = $_GET['to_selected_date'];
    $to_selected_date_data = $_GET['to_selected_date'].' 23:59:59';
    if($_GET['selected_branch'] == 0)
    {    
        $received_samples = "SELECT DISTINCT lab_tests.token_no, patients.name, tokans.branch_id, patients.age, genders.gender_title, patients.phone, patients.cnic, tokans.created AS register_at, doctor.u_name AS doctor_name FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN genders ON patients.gender = genders.gender_id INNER JOIN users doctor ON tokans.doctor_id = doctor.id WHERE (tokans.created >= '$from_selected_date' AND tokans.created <= '$to_selected_date_data') AND `lab_test_status_id` > '0' ORDER BY `lab_tests`.`token_no` DESC";
    }
    else
    {
        $selected_branch = $_GET['selected_branch'];
        $received_samples = "SELECT DISTINCT lab_tests.token_no, patients.name, tokans.branch_id, patients.age, genders.gender_title, patients.phone, patients.cnic, tokans.created AS register_at, doctor.u_name AS doctor_name FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN genders ON patients.gender = genders.gender_id INNER JOIN users doctor ON tokans.doctor_id = doctor.id WHERE (tokans.created >= '$from_selected_date' AND tokans.created <= '$to_selected_date_data') AND tokans.branch_id = '$selected_branch' AND `lab_test_status_id` > '0' ORDER BY `lab_tests`.`token_no` DESC";
    }
}
else
{
    $received_samples = "SELECT DISTINCT lab_tests.token_no, patients.name, tokans.branch_id, patients.age, genders.gender_title, patients.phone, patients.cnic, tokans.created AS register_at, doctor.u_name AS doctor_name FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN genders ON patients.gender = genders.gender_id INNER JOIN users doctor ON tokans.doctor_id = doctor.id WHERE (tokans.created >= '$from_selected_date' AND tokans.created <= '$to_selected_date 23:59:59') AND tokans.branch_id = '$selected_branch' AND `lab_test_status_id` > '0' ORDER BY `lab_tests`.`token_no` DESC";
}

?>
	<title>RECORD IN LAB FOR TEST (<?php echo date('d-m-Y'); ?>)- <?php echo $lab_login_branch_name; ?> - LAB - <?php echo $company_trademark; ?></title>
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
<div class = "table-responsive">
    <table class = "table table-bordered table-hover" style = "color: black'" id="myTable">
        <caption style = "text-align: center; caption-side: top;color: black;">
            <h1>RECORD IN LAB FOR TEST (<?php echo date('d-m-Y'); ?>)</h1>
        </caption>
        <thead>
			<tr>
                <th colspan = "8">
                    <div class = "row">
                        <div class = "col-md-3">
                            <label>Token #</label>
                            <input type="text" placeholder = "ENTER TOKEN NO FOR SEARCH..." class = "form-control" id="myInput" onkeyup="myFunction()" title="Type a token #">
                        </div>
                        <div class = "col-md-9">
                        <form>
                            <div class = "row">
                                <div class = "col-md-3">
                                    <label>Name</label>
                                    <input type = "text" class = "form-control" id="myInputName" onkeyup="myFunctionName()" placeholder="Search for names.." />
                                </div>
                                <div class = "col-md-2">
                                    <label>Phone</label>
                                    <input type = "text" class = "form-control" id="myInputPhone" onkeyup="myFunctionPhone()" />
                                </div>
                                <div class = "col-md-2">
                                    <label>FROM:</label>
                                    <input type = "date" name = "from_selected_date" value = "<?php echo $from_selected_date; ?>" class = "form-control" />
                                </div>
                                <div class = "col-md-2">
                                    <label>TO:</label>
                                    <input type = "date" name = "to_selected_date" value = "<?php echo $to_selected_date; ?>" class = "form-control" />
                                </div>
                                <div class = "col-md-3">
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
                <th>Patient Name</th>
                <th>Age / Gender</th>
                <th>Phone</th>
                <th>Ref. By</th>
                <th>Token At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $s = 0;
            $run_sample = mysqli_query($con, $received_samples);
            if(isset($_GET['token_no']) && $_GET['token_no'] != '')
            {
                $token_no =$_GET['token_no'];
                $select = "SELECT lab_tests.lab_test_id, items.name AS test_name, tokans.branch_id, lab_test_statuses.lab_test_status_title AS test_status, collect_sample.u_name AS sample_received_by FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN items ON lab_tests.item_id = items.id INNER JOIN lab_test_statuses ON lab_tests.lab_test_status_id = lab_test_statuses.lab_test_status_id LEFT JOIN users collect_sample ON lab_tests.lab_test_collected_created_by = collect_sample.id WHERE `token_no` = '$token_no' ";
                $run = mysqli_query($con, $select);
                if(mysqli_num_rows($run) > 0)
                { 
                    $received_samples = "SELECT DISTINCT lab_tests.token_no, patients.name, tokans.branch_id, patients.age, genders.gender_title, patients.phone, patients.cnic, tokans.created AS register_at, doctor.u_name AS doctor_name FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN patients ON tokans.patient_id = patients.id INNER JOIN genders ON patients.gender = genders.gender_id INNER JOIN users doctor ON tokans.doctor_id = doctor.id WHERE tokans.id = '$token_no' ";
                    $run_sample = mysqli_query($con, $received_samples);
                    if(mysqli_num_rows($run_sample) > 0)
                    {
                        while($row_sample = mysqli_fetch_array($run_sample))
                        {                
                        ?>
                    <tr>
                        <td>
                        </td>
                        <td><a href = "lab_test_all_reocrds.php?selected_branch=<?php echo $selected_branch; ?>&from_selected_date=<?php echo $from_selected_date; ?>&to_selected_date=<?php echo $to_selected_date; ?>&token_no=<?php echo $row_sample['token_no']; ?>#<?php echo $row_sample['token_no']; ?>" class = "btn btn-sm btn-success"><?php echo $row_sample['token_no']; ?></a> - <?php echo get_branch_tag_by($row_sample['branch_id']); ?></td>
                        <td><?php echo $row_sample['name']; ?></td>
                        <td><?php echo $row_sample['age'].'/ '.$row_sample['gender_title']; ?></td>
                        <td><?php echo $row_sample['phone']; ?></td>
                        <td><?php echo $row_sample['doctor_name']; ?></td>
                        <td><?php echo date_format(date_create($row_sample['register_at']), "H:s d-M-Y"); ?></td>
                        <td>
                            <a href="#" class = "btn btn-sm btn-warning" onClick="MyWindow=window.open('lab_test_patinet_record_update.php?token_no=<?php echo $row_sample['token_no']; ?>','MyWindow','width=900,height=1200'); return false;"><span class="glyphicon glyphicon-pencil">UPDATE</span></a>
                            <div class = "btn">
                                <a href = "https://wa.me/923057629149/?text=urlencodedtext">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="green" class="bi p-1 bi-whatsapp" viewBox="0 0 16 16">
                                        <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr> 
                    <?php
                        }
                    } ?>
                <tr>
                    <td colspan = "8">
                        <div class = "table-responsive table-sm">
                            <table class = "table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>S #</th>
                                        <th>Test Name</th>
                                        <th>Collect At</th>
                                        <th>collect By</th>
                                        <th>Test Status</th>
                                    </tr>
                                </thead>
                                <tobdy>
                                    <?php
                                    $count = 1;
                                    while($row = mysqli_fetch_array($run))
                                    { ?>
                                    <tr>
                                        <td><?php echo $count++; ?></td>
                                        <td><?php echo $row['test_name']; ?></td>
                                        <td><?php echo get_branch_tag_by($row['branch_id']); ?></td>
                                        <td><?php echo $row['sample_received_by']; ?></td>
                                        <td><?php echo $row['test_status'];if($row['test_status'] == 'PRINTED' || $row['test_status'] == 'REPORT APPRVED'){echo '<div class = "link link-primary">'; ?>
                                        <a href="print_test_report_test.php?token_no=<?php echo $token_no; ?>" onclick="window.open(this.href, 'Snopzer','left=20,top=20,width=500,height=500,toolbar=1,resizable=0'); return false;" target="_blank" rel="noopener noreferrer">view</a>
                                        <?php echo '</div>';} ?></td>
                                    </tr>
                                    <?php } ?>
                                </tobdy>
                            </table>
                        </div>
                    </td>
                </tr>
                <?php
                }                
            }
            elseif(mysqli_num_rows($run_sample) > 0)
            {
                while($row_sample = mysqli_fetch_array($run_sample))
                {
                    $s++; ?>
            <tr id =  "<?php echo $row_sample['token_no']; ?>">
                <td><?php echo $s; ?></td>
                <td><a href = "lab_test_all_reocrds.php?selected_branch=<?php echo $selected_branch; ?>&from_selected_date=<?php echo $from_selected_date; ?>&to_selected_date=<?php echo $to_selected_date; ?>&token_no=<?php echo $row_sample['token_no']; ?>#<?php echo $row_sample['token_no']; ?>" class = "btn btn-sm btn-success"><?php echo $row_sample['token_no']; ?></a> - <?php echo get_branch_tag_by($row_sample['branch_id']); ?></td>
                <td><?php echo $row_sample['name']; ?></td>
                <td><?php echo $row_sample['age'].'/ '.$row_sample['gender_title']; ?></td>
                <td><?php echo $row_sample['phone']; ?></td>
                <td><?php echo $row_sample['doctor_name']; ?></td>
                <td><?php echo date_format(date_create($row_sample['register_at']), "H:s d-M-Y"); ?></td>
                <td>
                    <a href="#" class = "btn btn-sm btn-warning" onClick="MyWindow=window.open('lab_test_patinet_record_update.php?token_no=<?php echo $row_sample['token_no']; ?>','MyWindow','width=900,height=1200'); return false;"><span class="glyphicon glyphicon-pencil">UPDATE</span></a>
                </td>
            </tr>
                    <?php
                }
            }
            else
            {
                echo '<tr><th class = "text-center" colspan = "5">NO RECORD IN LAB TEST</th></tr>';
            }
            ?>
        </tbody>
    </table>
</div>
<div class = "p-2">
    <div class = "row">
        <?php
        $select_status = "SELECT DISTINCT lab_tests.lab_test_status_id, lab_test_statuses.lab_test_status_title, lab_test_statuses.lab_test_status_class, COUNT(DISTINCT token_no) AS total_tokens, COUNT(token_no) AS total_tests FROM `lab_tests` INNER JOIN tokans ON lab_tests.token_no = tokans.id INNER JOIN lab_test_statuses ON lab_tests.lab_test_status_id = lab_test_statuses.lab_test_status_id WHERE tokans.created >= '$from_selected_date' AND tokans.created <= '$to_selected_date 23:59:59' GROUP BY lab_tests.lab_test_status_id ";
        $tun_status = mysqli_query($con, $select_status);
        if(mysqli_num_rows($tun_status) > 0)
        {
            while($row_status = mysqli_fetch_array($tun_status))
            { ?>
                <div class = "col"><div class = "btn btn-<?php echo $row_status['lab_test_status_class']; ?>"><?php echo $row_status['total_tokens'].' '.$row_status['lab_test_status_title'].' '.$row_status['total_tests']; ?></div></div>
            <?php }
        }
        ?>
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
function myFunctionName() 
{
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInputName");
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