<?php
require_once __DIR__ . '/includes/connect.php';

if (isset($_GET['duplicate'], $_GET['token_no']) && $_GET['token_no'] !== '') {
    $tokan_id = (int) $_GET['token_no'];
    header('Location: print_medicine_slip_duplicate.php?tokan_no=' . $tokan_id);
    if ($con instanceof mysqli) {
        mysqli_close($con);
    }
    exit;
}

header('Location: duplicate_token.php');
exit;
