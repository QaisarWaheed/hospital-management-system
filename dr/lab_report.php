<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
?>
	<title>Lab Report - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">
<div id="loadingSpinner" style="display: none;">
    <div class = "container">
        <div class = "row p-5 g-5">
            <div class = "col text-center">
                <div aria-busy="true" aria-describedby="progress-bar">
                    <h2>LOADING...</h2>
                    <p>Please Wait Untill Progessing Completed.</p>
                    <p>Data Processing...</p>
                </div>
                <progress id="progress-bar" aria-label="Content loading…"></progress>    
                
            </div>
        </div>        
    </div>
</div>
<div class="row" style="margin: 0px;" id = "submitBody">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class = "col-md-9">
	    <form method="POST" onsubmit="showProgress(); return true;">
    	    <div class = "row">
                <div class=" col-md-9">
                    <input type="number" name = "token_no" class="form-control" placeholder="Enter Token #, Phone # ... for searching">
                </div>
                <div class=" col-md-3">
                    <button type="submit" id="submitButton" class="btn btn-primary">SEARCH</button>
                </div>
    	    </div>
	    </form>
	    <div class = "row">
	        <div class = "col-md-12">
            <?php
            if (isset($_POST['token_no']) && $_POST['token_no'] !== '') {
                $s = 0;
                $search = mysqli_real_escape_string($con, trim($_POST['token_no']));
                $recent_from = date('Y-m-d', strtotime('-365 days'));
                $query = "SELECT tokans.id, items.name AS test_name, tokans.created, tokans.cash, patients.name, patients.cnic, patients.phone, patients.age, branchs.tag_name
                    FROM tokans
                    INNER JOIN patients ON tokans.patient_id = patients.id
                    INNER JOIN item_by_doctor ON tokans.id = item_by_doctor.tokan_no
                    INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id
                    INNER JOIN items ON item_register_to_branches.item_id = items.id
                    INNER JOIN branchs ON tokans.branch_id = branchs.id
                    WHERE (patients.phone = '$search' OR patients.cnic = '$search' OR tokans.id = '$search')
                    AND tokans.tokan_type_id > 100
                    AND items.category_id = '2'
                    AND tokans.created >= '$recent_from'
                    ORDER BY tokans.id DESC
                    LIMIT 200";
                $run = mysqli_query($con, $query);
                if(mysqli_num_rows($run) > 0)
                {
                    echo '<table class = "table table-hover table-bodered border-dark">';
                    echo '<thead>';
                        echo '<tr>';
                            echo '<th>S #</th>';
                            echo '<th>TOKEN</th>';
                            echo '<th>NAME</th>';
                            echo '<th>BR</th>';
                            echo '<th>PHONE</th>';
                            echo '<th>CNIC</th>';
                            echo '<th>AGE</th>';
                            echo '<th>TEST NAME</th>';
                        echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    while($row = mysqli_fetch_array($run))
                    {
                        $s++;
                        echo '<tr>';
                            echo '<td>'.$s.'</td>';
                            echo '<td>'.$row['id'].'</td>';
                            echo '<td>'.$row['name'].'</td>';
                            echo '<td>'.$row['tag_name'].'</td>';
                            echo '<td>'.$row['phone'].'</td>';
                            echo '<td>'.$row['cnic'].'</td>';
                            echo '<td>'.$row['age'].'</td>';
                            echo '<td>'.$row['test_name'].'</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                }    
            }	 
            ?>
	        </div>
	    </div>
	</div>
</div>
</body>
</html>
<script>
function showProgress() {
  document.getElementById('submitBody').style.display = 'none';
//   document.getElementById('submitButton').style.display = 'none';
  document.getElementById('loadingSpinner').style.display = 'block';
}    
</script>
<?php mysqli_close($con); ?>