<?php

/**
 * Batch stats for comparison report — one query per metric instead of per branch.
 *
 * @return array<int, array{patients: int, cons: int, collection: float, procedures: int, lab: float}>
 */
function comparison_branch_month_stats($con, $month)
{
    $month = mysqli_real_escape_string($con, (string) $month);
    $like = $month . '%';
    $stats = array();

    $queries = array(
        'patients' => "SELECT branch_id, COUNT(id) AS val FROM tokans
            WHERE status = 1 AND created LIKE '$like' AND tokan_type_id <= 10
            GROUP BY branch_id",
        'cons' => "SELECT branch_id, COUNT(tokan_no) AS val FROM item_by_doctor
            WHERE status = 2 AND created LIKE '$like'
            AND item_id IN (
                SELECT id FROM item_register_to_branches
                WHERE item_id IN (SELECT id FROM items WHERE category_id = 29)
            )
            GROUP BY branch_id",
        'collection' => "SELECT branch_id, COALESCE(SUM(cash_received), 0) AS val FROM tokans
            WHERE status = 1 AND created LIKE '$like'
            GROUP BY branch_id",
        'procedures' => "SELECT branch_id, COUNT(id) AS val FROM tokans
            WHERE status = 1 AND created LIKE '$like'
            AND id IN (
                SELECT tokan_no FROM item_by_doctor
                WHERE created LIKE '$like' AND status = 2
                AND item_id IN (
                    SELECT id FROM item_register_to_branches
                    WHERE item_id IN (SELECT id FROM items WHERE category_id = 3)
                )
            )
            GROUP BY branch_id",
        'lab' => "SELECT branch_id, COALESCE(SUM(cash_received), 0) AS val FROM tokans
            WHERE status = 1 AND created LIKE '$like'
            AND id IN (
                SELECT tokan_no FROM item_by_doctor
                WHERE created LIKE '$like' AND status = 2
                AND item_id IN (
                    SELECT id FROM item_register_to_branches
                    WHERE item_id IN (SELECT id FROM items WHERE category_id = 2)
                )
            )
            GROUP BY branch_id",
    );

    foreach ($queries as $key => $sql) {
        $run = mysqli_query($con, $sql);
        if (!$run) {
            continue;
        }
        while ($row = mysqli_fetch_assoc($run)) {
            $bid = (int) $row['branch_id'];
            if (!isset($stats[$bid])) {
                $stats[$bid] = array(
                    'patients' => 0,
                    'cons' => 0,
                    'collection' => 0.0,
                    'procedures' => 0,
                    'lab' => 0.0,
                );
            }
            if ($key === 'collection' || $key === 'lab') {
                $stats[$bid][$key] = (float) $row['val'];
            } else {
                $stats[$bid][$key] = (int) $row['val'];
            }
        }
    }

    return $stats;
}

function comparison_branch_stat($stats, $branch_id, $key)
{
    $branch_id = (int) $branch_id;
    if (!isset($stats[$branch_id][$key])) {
        return ($key === 'collection' || $key === 'lab') ? 0.0 : 0;
    }

    return $stats[$branch_id][$key];
}
