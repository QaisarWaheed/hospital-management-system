<?php
include 'includes/connect.php';

if (!isset($_POST['branch_item_id'], $_POST['updated_at'], $_POST['br_id'], $_POST['show_consumed_data'])) {
    http_response_code(400);
    exit('Missing required parameters.');
}

$branch_item_id = (int) $_POST['branch_item_id'];
$br_id = (int) $_POST['br_id'];
$updated_at = mysqli_real_escape_string($con, (string) $_POST['updated_at']);
$item_name = (string) get_item_name_by_register_item_id($branch_item_id);
if ($item_name === '0' || $item_name === '') {
    $item_name = 'Item #' . $branch_item_id;
}
$audit_update_label = ycdo_safe_date_format($updated_at, 'd M Y', '');

$records = array();
$select_consume = "
    SELECT item_by_doctor.created, item_by_doctor.sale_quantity
    FROM item_by_doctor
    WHERE item_by_doctor.item_id = '$branch_item_id'
      AND item_by_doctor.branch_id = '$br_id'
      AND item_by_doctor.created > '$updated_at'
      AND item_by_doctor.status = '2'
    ORDER BY item_by_doctor.created ASC
";
$run_consume = mysqli_query($con, $select_consume);
if ($run_consume) {
    while ($row = mysqli_fetch_assoc($run_consume)) {
        $qty = $row['sale_quantity'];
        if ($qty === null || $qty === '' || (float) $qty <= 0) {
            $qty = 1;
        }
        $records[] = array(
            'date' => ycdo_safe_date_format($row['created'], 'd-M-Y', ''),
            'item' => $item_name,
            'qty' => (float) $qty,
        );
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Consumed Item Records</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body {
            background: rgba(0, 0, 0, 0.35);
            margin: 0;
            padding: 24px;
            font-family: Arial, sans-serif;
        }
        .consumed-modal {
            max-width: 720px;
            margin: 40px auto;
            background: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }
        .consumed-modal-header {
            padding: 16px 20px 8px;
            border-bottom: 1px solid #eee;
        }
        .consumed-modal-header h4 {
            margin: 0 0 6px;
            font-size: 22px;
            font-weight: 700;
        }
        .consumed-modal-header p {
            margin: 0;
            color: #555;
            font-size: 14px;
        }
        .consumed-modal-body {
            padding: 0 20px 12px;
        }
        .consumed-modal-body table {
            margin-bottom: 0;
        }
        .consumed-modal-body th {
            background: #f5f5f5;
        }
        .consumed-modal-footer {
            padding: 12px 20px 16px;
            text-align: right;
            border-top: 1px solid #eee;
        }
        .empty-row td {
            text-align: center;
            color: #777;
            padding: 18px;
        }
    </style>
</head>
<body>
    <div class="consumed-modal">
        <div class="consumed-modal-header">
            <h4>Consumed Item Records</h4>
            <p>Last Audit Update: <?php echo htmlspecialchars($audit_update_label, ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <div class="consumed-modal-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Item</th>
                        <th>Qty Consumed</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (count($records) === 0) { ?>
                    <tr class="empty-row">
                        <td colspan="3">No consumption records found after the last audit update.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($records as $record) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['date'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($record['item'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(rtrim(rtrim(number_format((float)($record['qty'] ?? 0), 2, '.', ''), '0'), '.'), ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                    <?php } ?>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="consumed-modal-footer">
            <button type="button" class="btn btn-default" onclick="window.close();">Close Window</button>
        </div>
    </div>
</body>
</html>
<?php mysqli_close($con); ?>
