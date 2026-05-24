<?php

/**
 * Tokens eligible for new gynae registration (gynae item, not already registered).
 *
 * @return mysqli_result|false
 */
function ycdo_gynae_eligible_tokens_result($con, $branch_id, $limit = 300)
{
    $branch_id = (int) $branch_id;
    $limit = max(1, min(1000, (int) $limit));

    $sql = "SELECT DISTINCT t.id AS token_no, p.name AS patient_name
        FROM tokans t
        INNER JOIN patients p ON p.id = t.patient_id
        INNER JOIN item_by_doctor ibd
            ON ibd.tokan_no = t.id
            AND ibd.branch_id = t.branch_id
            AND ibd.status = 2
            AND (
                ibd.category_id = 41
                OR ibd.item_id IN (
                    SELECT irb.id FROM item_register_to_branches irb
                    WHERE irb.branch_id = $branch_id
                        AND irb.item_id IN (483, 1159, 1321, 1414, 1576)
                )
            )
        WHERE t.branch_id = $branch_id
            AND t.status = 1
            AND NOT EXISTS (
                SELECT 1 FROM gynae_register g
                WHERE g.token_no = t.id AND g.status = 1
            )
        ORDER BY t.id DESC
        LIMIT $limit";

    return mysqli_query($con, $sql);
}

/**
 * @return array<int, array{token_no: int, patient_name: string}>
 */
function ycdo_gynae_eligible_tokens_list($con, $branch_id, $limit = 300)
{
    $rows = array();
    $run = ycdo_gynae_eligible_tokens_result($con, $branch_id, $limit);
    if (!$run) {
        return $rows;
    }
    while ($row = mysqli_fetch_assoc($run)) {
        $rows[] = array(
            'token_no' => (int) $row['token_no'],
            'patient_name' => (string) $row['patient_name'],
        );
    }

    return $rows;
}
