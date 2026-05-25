<?php
require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';

/**
 * Shared HR progress report form handling (auth via connect.php).
 *
 * @param mysqli $con
 * @param int $hr_id
 * @param array $opts print (path), window_title, needs_br_id (default true)
 * @return array{role_title: string, popup_script: string}
 */
function hr_progress_bootstrap($con, $hr_id, array $opts)
{
    $opts = array_merge(
        array(
            'print' => '',
            'window_title' => 'PROGRESS REPORT',
            'needs_br_id' => true,
        ),
        $opts
    );

    $role_title = '';
    $hr_id = (int) $hr_id;
    $roles = "SELECT title FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$hr_id') LIMIT 1";
    $run_roles = mysqli_query($con, $roles);
    if ($run_roles && ($row_role = mysqli_fetch_assoc($run_roles))) {
        $role_title = $row_role['title'];
    }

    $popup_script = '';
    if (isset($_POST['date']) && $_POST['date'] !== '' && $opts['print'] !== '') {
        $params = array('date' => $_POST['date']);
        if ($opts['needs_br_id']) {
            $params['br_id'] = (int) ($_POST['br_id'] ?? 0);
        }
        $print_url = ycdo_absolute_url($opts['print'], http_build_query($params));
        $popup_script = '<script>
window.open(' . json_encode($print_url) . ', "_blank", "toolbar=yes,scrollbars=yes,resizable=yes,width=1400,height=900");
</script>';
    }

    return array(
        'role_title' => $role_title,
        'popup_script' => $popup_script,
    );
}

function hr_progress_sidebar_user_line($hr_name, $role_title)
{
    $incharge = (!empty($_SESSION['is_incharge']) && (int) $_SESSION['is_incharge'] === 2) ? ' Incharge ' : '';
    echo '<h3 style="margin-top: 350px;text-align: center;">'
        . htmlspecialchars($hr_name, ENT_QUOTES, 'UTF-8')
        . $incharge
        . '(' . htmlspecialchars($role_title, ENT_QUOTES, 'UTF-8') . ')</h3>';
}

/**
 * @param mysqli $con
 * @param string $exclude_clause e.g. "id != '0'" or "id > '1'"
 */
function hr_progress_branch_options($con, $hr_branch_id, $hr_branch_address, $exclude_clause = "id != '0'")
{
    echo '<option value="' . (int) $hr_branch_id . '">'
        . htmlspecialchars($hr_branch_address, ENT_QUOTES, 'UTF-8') . '</option>';
    $branch = "SELECT * FROM branchs WHERE $exclude_clause AND status = '1' ORDER BY `address` ASC ";
    $run_branch = mysqli_query($con, $branch);
    if ($run_branch) {
        while ($row_branch = mysqli_fetch_assoc($run_branch)) {
            if ((int) $row_branch['id'] === (int) $hr_branch_id) {
                continue;
            }
            echo '<option value="' . (int) $row_branch['id'] . '">'
                . htmlspecialchars($row_branch['address'], ENT_QUOTES, 'UTF-8') . '</option>';
        }
    }
}

function hr_progress_branch_options_exclude_first($con)
{
    $branch = "SELECT * FROM branchs WHERE id > '1' AND status = '1' ORDER BY `address` ASC ";
    $run_branch = mysqli_query($con, $branch);
    if ($run_branch) {
        while ($row_branch = mysqli_fetch_assoc($run_branch)) {
            echo '<option value="' . (int) $row_branch['id'] . '">'
                . htmlspecialchars($row_branch['address'], ENT_QUOTES, 'UTF-8') . '</option>';
        }
    }
}

function hr_progress_all_branch_options($con)
{
    $select_br = "SELECT * FROM branchs WHERE id > 0 AND status = '1' ORDER BY `address` ASC ";
    $run_br = mysqli_query($con, $select_br);
    if ($run_br) {
        while ($row_br = mysqli_fetch_assoc($run_br)) {
            echo '<option value="' . (int) $row_br['id'] . '">'
                . htmlspecialchars($row_br['address'], ENT_QUOTES, 'UTF-8') . '</option>';
        }
    }
}
