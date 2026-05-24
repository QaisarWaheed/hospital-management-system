<?php
include 'includes/connect.php';
require_once __DIR__ . '/../includes/report_helpers.php';

$br_id = (int) $branch_id;
$from_date = date('Y-m-d');
$to_date_end = date('Y-m-d') . ' 23:59:59';
$to_date_label = date('Y-m-d');

if (isset($_GET['br_id']) && $_GET['br_id'] !== '') {
    $br_id = (int) $_GET['br_id'];
}

if (isset($_GET['from_date']) && $_GET['from_date'] !== '') {
    $from_date = substr((string) $_GET['from_date'], 0, 10);
}

if (isset($_GET['to_date']) && $_GET['to_date'] !== '') {
    $to_date_label = substr((string) $_GET['to_date'], 0, 10);
    $to_date_end = $to_date_label . ' 23:59:59';
}

$from_esc = mysqli_real_escape_string($con, $from_date);
$to_esc = mysqli_real_escape_string($con, $to_date_end);

if ($br_id > 0) {
    $select = "SELECT bdpd.id, bdpd.created, branchs.tag_name, tokans.branch_id, patients.name,
            bdpd.ref_name, bdpd.ref_phone, bdpd.recommended_by, tokans.cash, tokans.cash_received,
            users.u_name, tokans.id AS token_no
        FROM branch_daily_pending_details bdpd
        INNER JOIN tokans ON bdpd.token_no = tokans.id
        INNER JOIN patients ON tokans.patient_id = patients.id
        INNER JOIN branchs ON tokans.branch_id = branchs.id
        INNER JOIN users ON tokans.user_id = users.id
        WHERE tokans.status = '1' AND tokans.branch_id = '$br_id'
            AND bdpd.created >= '$from_esc' AND bdpd.created <= '$to_esc'
            AND (tokans.cash - tokans.cash_received) > 0";
} else {
    $select = "SELECT bdpd.id, bdpd.created, branchs.tag_name, patients.name,
            bdpd.ref_name, bdpd.ref_phone, bdpd.recommended_by, tokans.cash, tokans.cash_received,
            users.u_name, tokans.id AS token_no
        FROM branch_daily_pending_details bdpd
        INNER JOIN tokans ON bdpd.token_no = tokans.id AND tokans.status = '1'
        INNER JOIN patients ON tokans.patient_id = patients.id
        INNER JOIN branchs ON tokans.branch_id = branchs.id
        INNER JOIN users ON tokans.user_id = users.id
        WHERE bdpd.created >= '$from_esc' AND bdpd.created <= '$to_esc'
            AND (tokans.cash - tokans.cash_received) > 0
        GROUP BY bdpd.id";
}

$from_input = htmlspecialchars($from_date, ENT_QUOTES, 'UTF-8');
$to_input = htmlspecialchars($to_date_label, ENT_QUOTES, 'UTF-8');
$branch_label = get_branch_name_by($br_id);

include 'includes/head.php';
?>
<style>
@page { size: A4; margin: 10px 0px 10px 0px; }
@media print {
    html, body { width: 210mm; height: 297mm; font-size: 9px; }
    .noprint { display: none; }
}
</style>

	<title>General Pending - <?php echo htmlspecialchars($company_trademark); ?></title>
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
	            GENERAL PENDING (<?php echo htmlspecialchars($branch_label); ?>)
	            FROM: <?php echo htmlspecialchars(ycdo_safe_date_format($from_date, 'd-M-Y', $from_date)); ?>
	            TO: <?php echo htmlspecialchars(ycdo_safe_date_format($to_date_label, 'd-M-Y', $to_date_label)); ?>
	        </caption>
	        <thead>
	            <tr class="noprint">
	                <th colspan="12">
	                <form method="GET">
	                    <div class="row">
	                        <div class="col" style="text-align: right;">
	                            <label for="br_id">BRANCH:</label>
	                        </div>
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
	                        <div class="col" style="text-align: right;">
	                            <label for="from_date">From Date:</label>
	                        </div>
	                        <div class="col">
	                            <input type="date" name="from_date" value="<?php echo $from_input; ?>" id="from_date" class="form-control" required />
	                        </div>
	                        <div class="col" style="text-align: right;">
	                            <label for="to_date">To Date:</label>
	                        </div>
	                        <div class="col">
	                            <input type="date" name="to_date" value="<?php echo $to_input; ?>" id="to_date" class="form-control" required />
	                        </div>
	                        <div class="col" style="text-align: center;">
	                            <input type="submit" value="SEARCH" name="submit" style="min-width: 100%;min-height: 100%;" class="btn btn-sm btn-info" />
	                        </div>
	                    </div>
	                </form>
	                </th>
	            </tr>
	            <tr>
	                <th>S #</th>
	                <th class="noprint" title="Pending ID">Id</th>
	                <th>Time</th>
	                <th>Date</th>
	                <th>Username</th>
	                <th>Branch</th>
	                <th>Name</th>
	                <th class="noprint" title="Reference Name">Ref. Name</th>
	                <th class="noprint" title="Recommended By">Recommended By</th>
	                <th>Token #</th>
	                <th>Total Amount</th>
	                <th>Received Amount</th>
	                <th>Pending Amount</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 0;
$run = mysqli_query($con, $select);
if ($run && mysqli_num_rows($run) > 0) {
    while ($row = mysqli_fetch_array($run)) {
        $total_amount = (float) $row['cash'];
        $receive_amount = (float) $row['cash_received'];
        $pending_amount = $total_amount - $receive_amount;
        if ($pending_amount <= 0) {
            continue;
        }
        $s++;
        $created = $row['created'];
        echo '
                <tr>
                    <td class="h6">' . $s . '</td>
                    <td class="noprint h6">' . (int) $row['id'] . '</td>
                    <td class="h6">' . htmlspecialchars(ycdo_safe_date_format($created, 'H:i:s', '')) . '</td>
                    <td class="h6">' . htmlspecialchars(ycdo_safe_date_format($created, 'd-m-Y', '')) . '</td>
                    <td class="h6">' . htmlspecialchars($row['u_name']) . '</td>
                    <td class="h6">' . htmlspecialchars($row['tag_name']) . '</td>
                    <td class="h6">' . htmlspecialchars($row['name']) . '</td>
                    <td class="noprint h6">' . htmlspecialchars($row['ref_name']) . '</td>
                    <td class="noprint h6">' . htmlspecialchars($row['recommended_by']) . '</td>
                    <td class="h6">' . (int) $row['token_no'] . '</td>
                    <td class="h6" style="text-align: center;">' . number_format($total_amount) . '</td>
                    <td class="h6" style="text-align: center;">' . number_format($receive_amount) . '</td>
                    <td class="h6" style="text-align: center;">' . number_format($pending_amount) . '</td>
                </tr>';
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
    if (captionElement) {
        document.title = captionElement.textContent.trim();
    }
</script>
