<?php
require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';

/**
 * @return array{id: int, name: string, branch_id: int, branch_address: string}|null
 */
function dr_progress_session()
{
    if (!empty($_SESSION['dr_id'])) {
        return array(
            'id' => (int) $_SESSION['dr_id'],
            'name' => $_SESSION['dr_name'] ?? '',
            'branch_id' => (int) ($_SESSION['branch_id'] ?? 0),
            'branch_address' => $_SESSION['branch_address'] ?? '',
        );
    }
    if (!empty($_SESSION['fr_id'])) {
        return array(
            'id' => (int) $_SESSION['fr_id'],
            'name' => $_SESSION['admin_name'] ?? ($_SESSION['fr_name'] ?? ''),
            'branch_id' => (int) ($_SESSION['branch_id'] ?? 0),
            'branch_address' => $_SESSION['branch_address'] ?? '',
        );
    }
    return null;
}

/**
 * @param mysqli $con
 * @param array $opts print, window_title, needs_br_id
 * @return array{role_title: string, popup_script: string}
 */
function dr_progress_bootstrap($con, array $opts)
{
    $opts = array_merge(
        array(
            'print' => '',
            'window_title' => 'PROGRESS REPORT',
            'needs_br_id' => true,
        ),
        $opts
    );

    $session = dr_progress_session();
    $role_title = '';
    if ($session) {
        $uid = (int) $session['id'];
        $roles = "SELECT title FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$uid') LIMIT 1";
        $run_roles = mysqli_query($con, $roles);
        if ($run_roles && ($row_role = mysqli_fetch_assoc($run_roles))) {
            $role_title = $row_role['title'];
        }
    }

    $popup_script = '';
    if (isset($_POST['date']) && $_POST['date'] !== '' && $opts['print'] !== '') {
        $params = array('date' => $_POST['date']);
        if ($opts['needs_br_id']) {
            $params['br_id'] = (int) ($_POST['br_id'] ?? 0);
        }
        $print_url = ycdo_absolute_url($opts['print'], http_build_query($params));
        $popup_script = '<script>window.open('
            . json_encode($print_url) . ', '
            . json_encode($opts['window_title'])
            . ', "width=3000,height=3000");</script>';
    }

    return array(
        'role_title' => $role_title,
        'popup_script' => $popup_script,
        'session' => $session,
    );
}

function dr_progress_sidebar_user_line(array $session, $role_title)
{
    $incharge = (!empty($_SESSION['is_incharge']) && (int) $_SESSION['is_incharge'] === 2) ? ' Incharge ' : '';
    echo '<h3 style="margin-top: 350px;text-align: center;">'
        . htmlspecialchars($session['name'], ENT_QUOTES, 'UTF-8')
        . $incharge
        . '(' . htmlspecialchars($role_title, ENT_QUOTES, 'UTF-8') . ')</h3>';
}
