<?php
set_time_limit(120);

include 'includes/connect.php';

if (isset($_GET['ajax']) && $_GET['ajax'] === 'pending_list') {
    header('Content-Type: text/html; charset=UTF-8');
    $search = '';
    if (isset($_GET['enter_search_token']) && $_GET['enter_search_token'] !== '') {
        $search = (string) $_GET['enter_search_token'];
    }
    pharmecy_render_branch_pending_procedure_rows(
        $con,
        (int) $branch_id,
        $search,
        pharmecy_branch_pending_list_limit()
    );
    exit;
}

if (isset($_GET['add_token']) && $_GET['enter_token'] != '' && $_GET['add_token']) {
    $token_no = (int) $_GET['enter_token'];
    $select = "SELECT * FROM tokans WHERE id = '$token_no' AND `tokan_type_id` < 100 ";
    $run = mysqli_query($con, $select);
    if (mysqli_num_rows($run) == 1) {
        while ($row = mysqli_fetch_array($run)) {
            $patient_id = $row['patient_id'];
            $select_patient = "SELECT * FROM patients WHERE id = '$patient_id' ";
            $run_patient = mysqli_query($con, $select_patient);
            if (mysqli_num_rows($run_patient) == 1) {
                while ($row_patient = mysqli_fetch_array($run_patient)) {
                    $phone = $row_patient['phone'];
                    $cnic = $row_patient['cnic'];
                    if ($phone == null || $cnic == null) {
                        header('Location: branch_procedure_pending_token.php?add_token=1&enter_token=' . $token_no . '&error=invalid');
                        exit;
                    }
                    header('Location: second_procedure_turn.php?search_tokan_no=' . $token_no);
                    exit;
                }
            }
        }
    }
    header('Location: branch_procedure_pending_token.php?error=invalid_token');
    exit;
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
                        <?php
                        if (isset($_GET['error'])) {
                            $msg = ($_GET['error'] === 'invalid_token')
                                ? 'ENTER A VALID TOKEN NO'
                                : 'NOT VALID TOKEN NO';
                            echo '<div class="alert alert-danger alert-dismissible"><button type="button" class="close" data-dismiss="alert">&times;</button><label>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</label></div>';
                        }
                        ?>
        			    <label>ENTER PATIENT COMPLETE REGISTRATION TOKEN NO</label>

        				<input type="number" name="enter_token" class = "form-control" />
        				<input type="submit" name="add_token" class="btn btn-sm btn-primary" style = "margin-top: 15px;" />
        			</form>
    		    </div>
    			<div class = "col-12">
            		<table class="table" id="myTable">
            			<thead>
            				<caption style="caption-side: top;text-align: center;">
            					<h3> PROCEDURES DETAIL</h3>
            					<p class="text-muted small mb-0">Showing latest <?php echo (int) pharmecy_branch_pending_list_limit(); ?> pending records. Use search to find a specific token or name.</p>
            				</caption>
            				<tr>
            				    <th colspan = "6">
            				        <input type="text" id="nameInput" onkeyup="nameFunction()" placeholder="Search names.." title="Type Name" class = "form-control">
            				    </th>
            				    <th colspan = "6">
            				        <input type="text" id="tokenInput" onkeyup="tokenFunction()" placeholder="Search tokens.." title="Type Token" class = "form-control">
            				        <input type="text" id="serverSearchInput" placeholder="Search server (name or token).." class="form-control mt-1" />
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
            			<tbody id="pendingProceduresBody">
            			    <tr><td colspan="12" class="text-center text-muted">Loading procedures…</td></tr>
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
(function () {
  var searchTimer = null;
  function loadPendingList(search) {
    var params = { ajax: 'pending_list' };
    if (search) {
      params.enter_search_token = search;
    }
    $('#pendingProceduresBody').html(
      '<tr><td colspan="12" class="text-center text-muted">Loading procedures…</td></tr>'
    );
    $.get('branch_procedure_pending_token.php', params)
      .done(function (html) {
        $('#pendingProceduresBody').html(html);
      })
      .fail(function () {
        $('#pendingProceduresBody').html(
          '<tr><td colspan="12" class="text-danger text-center">Could not load procedures. Refresh the page.</td></tr>'
        );
      });
  }
  $(document).ready(function () {
    loadPendingList('');
    $('#serverSearchInput').on('input', function () {
      clearTimeout(searchTimer);
      var q = $(this).val();
      searchTimer = setTimeout(function () {
        loadPendingList(q);
      }, 400);
    });
  });
})();
</script>
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
