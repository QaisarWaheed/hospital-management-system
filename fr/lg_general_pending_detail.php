<?php
include 'includes/connect.php';
require_once __DIR__ . '/../includes/general_pending_helpers.php';

@set_time_limit(120);

$filters = general_pending_parse_filters($_GET, (int) $branch_id, false);
$br_id = $filters['br_id'];
$from_date = $filters['from_date'];
$from_input = $filters['from_input'];
$branch_label = get_branch_name_by($br_id);
$select = general_pending_list_sql($con, $br_id, $from_date, $filters['to_date_end']);
$pendingRows = general_pending_fetch_rows($con, $select);

$tokenIds = array();
foreach ($pendingRows as $row) {
    $total = (float) $row['cash'];
    $received = (float) $row['cash_received'];
    if ($total - $received > 0) {
        $tokenIds[] = (int) $row['token_no'];
    }
}
$itemsByToken = general_pending_items_by_tokens($con, $tokenIds);

include 'includes/head.php';
?>
<style>
@page { size: A4; margin: 10px 0px 10px 0px; }
@media print {
    html, body { width: 210mm; height: 297mm; font-size: 9px; }
    .noprint { display: none; }
}
</style>

	<title>General Pending Detail - <?php echo htmlspecialchars($company_trademark); ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12 noprint" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo htmlspecialchars($company_name); ?> </h1></label>
        <?php include 'navigation_top.php'; ?>
	</div>

	<div class="col-md-12">
	    <table class="table table-bordered">
	        <caption id="table-caption" class="h2" style="caption-side: top;text-align: center;">
	            GENERAL PENDING DETAIL (<?php echo htmlspecialchars($branch_label); ?>)
	            DATED: <?php echo htmlspecialchars(ycdo_safe_date_format($from_date, 'd-M-Y', $from_date)); ?>
	        </caption>
	        <thead>
	            <tr class="noprint">
	                <th colspan="12">
	                <form method="GET">
	                    <div class="row">
	                        <div class="col" style="text-align: right;"><label for="br_id">BRANCH:</label></div>
	                        <div class="col">
	                            <select name="br_id" id="br_id" class="form-control">
	                                <option value="">ALL</option>
	                                <?php
	                                $run_branch = mysqli_query($con, "SELECT id, tag_name FROM branchs WHERE status = '1' ORDER BY tag_name ASC");
	                                if ($run_branch) {
	                                    while ($row_branch = mysqli_fetch_array($run_branch)) {
	                                        $bid = (int) $row_branch['id'];
	                                        $sel = ($br_id === $bid) ? ' SELECTED' : '';
	                                        echo '<option' . $sel . ' value="' . $bid . '">' . htmlspecialchars($row_branch['tag_name']) . '</option>';
	                                    }
	                                }
	                                ?>
	                            </select>
	                        </div>
	                        <div class="col" style="text-align: right;"><label for="from_date">Date:</label></div>
	                        <div class="col">
	                            <input type="date" name="from_date" value="<?php echo $from_input; ?>" id="from_date" class="form-control" required />
	                        </div>
	                        <div class="col" style="text-align: center;">
	                            <input type="submit" value="SEARCH" name="submit" class="btn btn-sm btn-info" style="min-width:100%;min-height:100%;" />
	                        </div>
	                    </div>
	                </form>
	                </th>
	            </tr>
	            <tr>
	                <th>S #</th>
	                <th class="noprint">Id</th>
	                <th>Time</th>
	                <th>Date</th>
	                <th>Username</th>
	                <th>Branch</th>
	                <th>Name</th>
	                <th class="noprint">Ref. Name</th>
	                <th class="noprint">Recommended By</th>
	                <th>Token #</th>
	                <th>Total Amount</th>
	                <th>Received Amount</th>
	                <th>Pending Amount</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 0;
foreach ($pendingRows as $row) {
    $total_amount = (float) $row['cash'];
    $receive_amount = (float) $row['cash_received'];
    $pending_amount = $total_amount - $receive_amount;
    if ($pending_amount <= 0) {
        continue;
    }
    $s++;
    $token_no = (int) $row['token_no'];
    echo '<tr>
        <td class="h6">' . $s . '</td>
        <td class="noprint h6">' . (int) $row['id'] . '</td>
        <td class="h6">' . htmlspecialchars(ycdo_safe_date_format($row['created'], 'H:i:s', '')) . '</td>
        <td class="h6">' . htmlspecialchars(ycdo_safe_date_format($row['created'], 'd-m-Y', '')) . '</td>
        <td class="h6">' . htmlspecialchars($row['u_name']) . '</td>
        <td class="h6">' . htmlspecialchars($row['tag_name']) . '</td>
        <td class="h6">' . htmlspecialchars($row['name']) . '</td>
        <td class="noprint h6">' . htmlspecialchars($row['ref_name']) . '</td>
        <td class="noprint h6">' . htmlspecialchars($row['recommended_by']) . '</td>
        <td class="h6">' . $token_no . '</td>
        <td class="h6" style="text-align:center;">' . number_format((float)($total_amount ?? 0)) . '</td>
        <td class="h6" style="text-align:center;">' . number_format((float)($receive_amount ?? 0)) . '</td>
        <td class="h6" style="text-align:center;">' . number_format((float)($pending_amount ?? 0)) . '</td>
    </tr>';

    if (isset($itemsByToken[$token_no]) && $itemsByToken[$token_no] !== array()) {
        echo '<tr><td colspan="13"><ol>';
        foreach ($itemsByToken[$token_no] as $item) {
            echo '<li>' . htmlspecialchars($item['item_name']) . ' ' . htmlspecialchars((string) $item['quantity']) . '</li>';
        }
        echo '</ol></td></tr>';
    }
}
?>
	        </tbody>
	    </table>
	</div>
</div>
</body>
</html>
<script>
const captionElement = document.getElementById('table-caption');
if (captionElement) { document.title = captionElement.textContent.trim(); }
</script>
