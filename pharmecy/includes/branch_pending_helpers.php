<?php

/**
 * Insert branch_pending_details with required NOT NULL columns (no DB defaults).
 *
 * @param mysqli $con
 * @param int|string $tokan_no
 * @param string $current_date
 * @param int|string $branch_id
 * @param string $status
 * @param array<string, string> $fields Optional: gardian_name, gardian_phone, recommended_by, return_date
 * @return bool
 */
function pharmecy_insert_branch_pending_details($con, $tokan_no, $current_date, $branch_id, $status = '2', array $fields = array())
{
    $tokan_no = (int) $tokan_no;
    $branch_id = (int) $branch_id;
    $status = mysqli_real_escape_string($con, (string) $status);
    $current_date = mysqli_real_escape_string($con, (string) $current_date);

    $gardian_name = (string) ($fields['gardian_name'] ?? $_POST['gardian_name'] ?? $_GET['gardian_name'] ?? $_GET['ref_name'] ?? '');
    $gardian_phone = (string) ($fields['gardian_phone'] ?? $_POST['gardian_phone'] ?? $_GET['gardian_phone'] ?? $_GET['ref_phone'] ?? '');
    $recommended_by = (string) ($fields['recommended_by'] ?? $_POST['recommended_by'] ?? $_GET['recommended_by'] ?? '');
    $return_date = (string) ($fields['return_date'] ?? $_POST['return_date'] ?? $_GET['return_date'] ?? '0000-00-00');
    if ($return_date === '') {
        $return_date = '0000-00-00';
    }

    $gardian_name = mysqli_real_escape_string($con, $gardian_name);
    $gardian_phone = mysqli_real_escape_string($con, $gardian_phone);
    $recommended_by = mysqli_real_escape_string($con, $recommended_by);
    $return_date = mysqli_real_escape_string($con, $return_date);

    $sql = "INSERT INTO `branch_pending_details`
        (`token_no`, `branch_id`, `gardian_name`, `gardian_phone`, `recommended_by`, `return_date`, `created`, `status`)
        VALUES
        ('$tokan_no', '$branch_id', '$gardian_name', '$gardian_phone', '$recommended_by', '$return_date', '$current_date', '$status')";

    return (bool) mysqli_query($con, $sql);
}
