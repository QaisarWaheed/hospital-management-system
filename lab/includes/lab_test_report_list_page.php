<?php
/**
 * Shared UI for approved / print lab test queues.
 * Set before include: $lab_report_page_title, $lab_report_status_id, $lab_report_action_script
 */
require_once __DIR__ . '/lab_test_list_helper.php';

$selected_branch = $lab_login_branch_id;
$list_filters = lab_test_list_parse_filters($con, $selected_branch);
$selected_branch = $list_filters['branch_id'];
$list_result = lab_test_list_fetch($con, $list_filters, $lab_report_status_id, 500);
$list_rows = $list_result['rows'];
$list_truncated = $list_result['truncated'];
?>
	<title><?php echo htmlspecialchars($lab_report_page_title, ENT_QUOTES, 'UTF-8'); ?> (<?php echo date('d-m-Y'); ?>) - <?php echo htmlspecialchars($lab_login_branch_name, ENT_QUOTES, 'UTF-8'); ?> - LAB - <?php echo $company_trademark; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
    .background_image{
        background-image: url('../images/background.png');
        background-size: cover;
    }
    @media print {
        body { font-size: 12px; }
        table { font-size: 0.8em; }
    }
    </style>
</head>

<body class="background_image">
<?php include __DIR__ . '/../top_navigation.php'; ?>
    <div class="row">
    	<div class="col-md-12">
    	    <table class="table table-bordered table-hover" style="color: black" id="myTable">
    	        <caption style="text-align: center; caption-side: top;color: black;">
    	            <h1><?php echo htmlspecialchars($lab_report_page_title, ENT_QUOTES, 'UTF-8'); ?> (<?php echo date('d-m-Y'); ?>)</h1>
                    <?php if ($list_truncated) { ?>
                        <p class="text-warning">Showing latest 500 records only. Narrow the date range or select a single branch.</p>
                    <?php } elseif (!$list_filters['should_run']) { ?>
                        <p class="text-info">Choose branch and dates, then click Search. (Loads last 14 days by default.)</p>
                    <?php } ?>
    	        </caption>
    	        <thead>
        			<tr>
                        <th colspan="13">
                            <form method="get" class="row">
                                <input type="hidden" name="search" value="1" />
                                <div class="col-md-2">
                                    <label>Token #</label>
                                    <input type="text" placeholder="Filter table…" class="form-control" id="myInput" onkeyup="myFunction()" title="Type a token #">
                                </div>
                                <div class="col-md-2">
                                    <label>Test Name</label>
                                    <input type="text" class="form-control" id="myInputTestName" onkeyup="myFunctionTestName()" />
                                </div>
                                <div class="col-md-2">
                                    <label>Patient Name</label>
                                    <input type="text" class="form-control" id="myInputName" onkeyup="myFunctionName()" />
                                </div>
                                <div class="col-md-2">
                                    <label>Phone</label>
                                    <input type="text" class="form-control" id="myInputPhone" onkeyup="myFunctionPhone()" />
                                </div>
                                <div class="col-md-2">
                                    <label>From</label>
                                    <input type="date" name="date_from" class="form-control" value="<?php echo htmlspecialchars($list_filters['date_from'], ENT_QUOTES, 'UTF-8'); ?>" required />
                                </div>
                                <div class="col-md-2">
                                    <label>To</label>
                                    <input type="date" name="date_to" class="form-control" value="<?php echo htmlspecialchars($list_filters['date_to'], ENT_QUOTES, 'UTF-8'); ?>" required />
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label>Branch</label>
                                    <select name="selected_branch" class="form-control" required>
                                        <?php
                                        $query = "SELECT id, tag_name FROM branchs WHERE status = '1' ORDER BY tag_name";
                                        $run = mysqli_query($con, $query);
                                        if ($run) {
                                            echo '<option value="0"' . ($selected_branch === 0 ? ' selected' : '') . '>ALL (max 7 days)</option>';
                                            while ($row = mysqli_fetch_assoc($run)) {
                                                $sel = ((int) $row['id'] === $selected_branch) ? ' selected' : '';
                                                echo '<option value="' . (int) $row['id'] . '"' . $sel . '>'
                                                    . htmlspecialchars($row['tag_name'], ENT_QUOTES, 'UTF-8') . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-success mt-2">Search</button>
                                </div>
                            </form>
                        </th>
        			</tr>
    	            <tr>
    	                <th>S #</th><th>Token #</th><th>Test</th><th>Patient Name</th><th>Phone</th><th>Age</th>
    	                <th>Added At</th><th>Added By</th><th>Collected At</th><th>Collected By</th>
    	                <th>Processed At</th><th>Processed By</th><th>Action</th>
    	            </tr>
    	        </thead>
    	        <tbody>
    	            <?php
    	            if (count($list_rows) > 0) {
    	                $s = 0;
    	                foreach ($list_rows as $row_sample) {
    	                    $s++;
    	                    $added_at = $row_sample['added_at'] ? date_format(date_create($row_sample['added_at']), 'h:i:s A d-m-Y') : '');
    	                    $collected_at = $row_sample['collected_at'] ? date_format(date_create($row_sample['collected_at']), 'h:i:s A d-m-Y') : '');
    	                    $processed_at = $row_sample['processed_at'] ? date_format(date_create($row_sample['processed_at']), 'h:i:s A d-m-Y') : '');
    	            ?>
    	            <tr>
    	                <td><?php echo $s; ?></td>
    	                <td><?php echo htmlspecialchars($row_sample['main_branch_name'], ENT_QUOTES, 'UTF-8') . ' - ' . (int) $row_sample['token_no']; ?>
    	                    <a href="#" class="btn btn-sm btn-success" onclick="window.open('lab_test_current_status.php?lab_test_id=<?php echo (int) $row_sample['lab_test_id']; ?>','MyWindow','width=900,height=1200'); return false;">+</a></td>
    	                <td><?php echo htmlspecialchars($row_sample['test_name'], ENT_QUOTES, 'UTF-8'); ?></td>
    	                <td><?php echo htmlspecialchars($row_sample['name'], ENT_QUOTES, 'UTF-8'); ?></td>
    	                <td><?php echo htmlspecialchars($row_sample['phone'], ENT_QUOTES, 'UTF-8'); ?></td>
    	                <td><?php echo htmlspecialchars($row_sample['age'], ENT_QUOTES, 'UTF-8'); ?></td>
    	                <td><?php echo $added_at; ?></td>
    	                <td><?php echo htmlspecialchars($row_sample['added_by'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
    	                <td><?php echo $collected_at; ?></td>
    	                <td><?php echo htmlspecialchars($row_sample['collected_by'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
    	                <td><?php echo $processed_at; ?></td>
    	                <td><?php echo htmlspecialchars($row_sample['processed_by'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
    	                <td>
    	                    <a href="#" class="btn btn-sm btn-success" onclick="window.open('<?php echo htmlspecialchars($lab_report_action_script, ENT_QUOTES, 'UTF-8'); ?>?lab_test_id=<?php echo (int) $row_sample['lab_test_id']; ?>','MyWindow','width=900,height=1200'); return false;">Open</a>
    	                </td>
    	            </tr>
    	                <?php }
    	            } elseif ($list_filters['should_run']) {
    	                echo '<tr><td colspan="13">NO RECORDS FOR THIS BRANCH AND DATE RANGE</td></tr>';
    	            } else {
    	                echo '<tr><td colspan="13">Click Search to load records.</td></tr>';
    	            }
    	            ?>
    	        </tbody>
    	    </table>
    	</div>
    </div>
<script>
function filterTable(colIndex, inputId) {
    var input = document.getElementById(inputId);
    var filter = (input.value || '').toUpperCase();
    var table = document.getElementById('myTable');
    var tr = table.getElementsByTagName('tr');
    for (var i = 0; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName('td')[colIndex];
        if (!td) continue;
        var txt = td.textContent || td.innerText;
        tr[i].style.display = txt.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
    }
}
function myFunction() { filterTable(1, 'myInput'); }
function myFunctionTestName() { filterTable(2, 'myInputTestName'); }
function myFunctionName() { filterTable(3, 'myInputName'); }
function myFunctionPhone() { filterTable(4, 'myInputPhone'); }
</script>
</body>
</html>
<?php mysqli_close($con); ?>
