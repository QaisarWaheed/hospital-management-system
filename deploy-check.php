<?php
header('Content-Type: text/plain; charset=utf-8');
echo 'YCDO deploy marker: 2026-05-24-bk-report-fixes' . "\n";
echo 'PHP: ' . PHP_VERSION . "\n";
if (is_readable(__DIR__ . '/includes/ycdo_bootstrap.php')) {
    require_once __DIR__ . '/includes/ycdo_bootstrap.php';
    $con = @ycdo_db_connect();
    echo 'Database: ' . ($con ? 'OK' : 'FAILED') . "\n";
}
