<?php
include 'includes/connect.php';
require_once __DIR__ . '/includes/progress_report_params.php';

$req = progress_report_resolve_request($con);
$date = $req['date'];
$br_id = $req['br_id'];
$like = $req['like'];
$date_esc = $req['date_esc'];

$last_day = date_format(date_create($date), 't');
$month_end = $date_esc . '-' . $last_day;
$history_start = '2025-03-01';

$historical_tokens = array();
$sql_hist = "SELECT item_by_doctor.doctor_id, users.u_name, COUNT(item_by_doctor.id) AS tokens
    FROM item_by_doctor
    INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id
    INNER JOIN items ON item_register_to_branches.item_id = items.id
    INNER JOIN users ON item_by_doctor.doctor_id = users.id
    WHERE items.category_id = '41' AND item_by_doctor.branch_id = '$br_id'
    AND item_by_doctor.created >= '$history_start' AND item_by_doctor.created < '$month_end'
    GROUP BY item_by_doctor.doctor_id, users.u_name";
$run = mysqli_query($con, $sql_hist);
if ($run) {
    while ($row = mysqli_fetch_assoc($run)) {
        $did = (int) $row['doctor_id'];
        $historical_tokens[$did] = array(
            'name' => $row['u_name'],
            'tokens' => (int) $row['tokens'],
        );
    }
}

$current_tokens = progress_item_count_by_doctor($con, $br_id, $like, '483, 1159, 1321, 1414');
$current_gynae = progress_gynae_register_count_by_doctor($con, $br_id, $like);

$hist_gynae_sql = "SELECT doctor_id, COUNT(*) AS cnt FROM gynae_register
    WHERE branch_id = '$br_id' AND created > '$history_start' AND created < '$month_end'
    GROUP BY doctor_id";
$historical_gynae = progress_map_int($con, $hist_gynae_sql, 'doctor_id', 'cnt');

$doctor_ids = array_unique(array_merge(
    array_keys($historical_tokens),
    array_keys($current_tokens),
    array_keys($current_gynae)
));
sort($doctor_ids, SORT_NUMERIC);
?>
<html>
<head>
    <title>PRINT GYNAE PROGRESS REPORT <?php echo get_branch_tag_by($br_id); echo date_format(date_create($date), ' F Y'); ?></title>
</head>
<body>

<table border="solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS <?php echo date_format(date_create($date), ' F Y'); ?></h3>
</caption>
    <thead>
        <tr>
            <th colspan="3"></th>
            <th colspan="3">PREVIOUS ALL REPORT</th>
            <th colspan="3">CURRENT MONTH DATA</th>
            <th colspan="3">TOTAL RECORD</th>
        </tr>
        <tr>
            <th>SR #</th><th>ID</th><th>NAME</th>
            <th>TOKENS</th><th>ONLINE</th><th>PENDING</th>
            <th>TOKENS</th><th>ONLINE</th><th>PENDING</th>
            <th>TOKENS</th><th>ONLINE</th><th>PENDING</th>
        </tr>
    </thead>
<?php
$s = 0;
$total_tokens = 0;
$total_gynae_system = 0;
$current_total_tokens = 0;
$current_total_gynae_system = 0;
if (count($doctor_ids) > 0) {
    echo '<tbody>';
    foreach ($doctor_ids as $doctor_id) {
        $s++;
        $doctor_name = $historical_tokens[$doctor_id]['name'] ?? get_uname_by_id($doctor_id);
        $tokens = ($historical_tokens[$doctor_id]['tokens'] ?? 0);
        $gynae_system = $historical_gynae[$doctor_id] ?? 0;
        $cur_tok = $current_tokens[$doctor_id] ?? 0;
        $cur_gyn = $current_gynae[$doctor_id] ?? 0;

        $current_total_tokens += $cur_tok;
        $current_total_gynae_system += $cur_gyn;
        $total_tokens += ($tokens - $cur_tok);
        $total_gynae_system += ($gynae_system - $cur_gyn);

        echo '<tr style="text-align: right;">';
        echo '<td>' . $s . '</td><td>' . (int) $doctor_id . '</td><td>' . htmlspecialchars($doctor_name, ENT_QUOTES, 'UTF-8') . '</td>';
        echo '<td>' . ($tokens - $cur_tok) . '</td><td>' . ($gynae_system - $cur_gyn) . '</td><td>' . intval(($tokens - $cur_tok) - ($gynae_system - $cur_gyn)) . '</td>';
        echo '<td>' . $cur_tok . '</td><td>' . $cur_gyn . '</td><td>' . intval($cur_tok - $cur_gyn) . '</td>';
        echo '<td>' . $tokens . '</td><td>' . $gynae_system . '</td><td>' . intval($tokens - $gynae_system) . '</td>';
        echo '</tr>';
    }
    echo '</tbody><tfoot><tr style="text-align: right;"><th colspan="3"></th>';
    echo '<th>' . $total_tokens . '</th><th>' . $total_gynae_system . '</th><th>' . intval($total_tokens - $total_gynae_system) . '</th>';
    echo '<th>' . $current_total_tokens . '</th><th>' . $current_total_gynae_system . '</th><th>' . intval($current_total_tokens - $current_total_gynae_system) . '</th>';
    echo '<th>' . ($current_total_tokens + $total_tokens) . '</th><th>' . ($current_total_gynae_system + $total_gynae_system) . '</th>';
    echo '<th>' . intval(($current_total_tokens + $total_tokens) - ($current_total_gynae_system + $total_gynae_system)) . '</th>';
    echo '</tr></tfoot>';
}
?>
</table>
</body>
</html>
<?php mysqli_close($con); ?>
