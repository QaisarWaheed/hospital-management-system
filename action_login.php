<?php
require_once __DIR__ . '/includes/ycdo_bootstrap.php';

if (!isset($_POST['role_id']) || $_POST['role_id'] === '') {
    header('Location: ' . ycdo_form_action_url('index.php'));
    exit;
}

$role_id = (int) $_POST['role_id'];
$branch_id = (int) ($_POST['branch_id'] ?? 0);
$query = 'branch_id=' . $branch_id;

if ($role_id === 1) {
    header('Location: ' . ycdo_form_action_url('admin_login.php', $query));
    exit;
}
if ($role_id === 2) {
    header('Location: ' . ycdo_form_action_url('login.php', $query));
    exit;
}
if ($role_id === 3) {
    header('Location: ' . ycdo_form_action_url('dr_login.php', $query));
    exit;
}
if ($role_id === 4) {
    header('Location: ' . ycdo_form_action_url('sm_login.php', $query));
    exit;
}
if ($role_id === 6) {
    header('Location: ' . ycdo_form_action_url('mm_login.php', $query));
    exit;
}
if ($role_id === 7) {
    header('Location: ' . ycdo_form_action_url('login.php', $query));
    exit;
}
if ($role_id === 8) {
    header('Location: ' . ycdo_form_action_url('lab_login.php', $query));
    exit;
}
if ($role_id === 9) {
    header('Location: ' . ycdo_form_action_url('fr_login.php', $query));
    exit;
}
if ($role_id === 10) {
    header('Location: ' . ycdo_form_action_url('ao_login.php', $query));
    exit;
}
if ($role_id === 11) {
    header('Location: ' . ycdo_form_action_url('bk_login.php', $query));
    exit;
}
if ($role_id === 12) {
    header('Location: ' . ycdo_form_action_url('hr_login.php', $query));
    exit;
}
if ($role_id === 18) {
    header('Location: ' . ycdo_form_action_url('la_login.php', $query));
    exit;
}
if ($role_id === 19) {
    header('Location: ' . ycdo_form_action_url('lm_login.php', $query));
    exit;
}

header('Location: ' . ycdo_form_action_url('index.php'));
exit;
