<?php
$files = glob(__DIR__ . '/../*_login.php');
$files[] = __DIR__ . '/../login.php';
foreach ($files as $path) {
    if (!is_file($path)) {
        continue;
    }
    $c = file_get_contents($path);
    $orig = $c;
    $c = preg_replace('/^\xEF\xBB\xBF?\s*<\?php\s*/', "<?php\nrequire_once __DIR__ . '/includes/ycdo_bootstrap.php';\n", $c, 1);
    if (strpos($c, 'ycdo_bootstrap') === false && preg_match('/^<\?php/', $c)) {
        $c = preg_replace('/^<\?php\s*/', "<?php\nrequire_once __DIR__ . '/includes/ycdo_bootstrap.php';\n", $c, 1);
    }
    $c = preg_replace('/if\s*\(\s*\$role_id\s*=\s*(\d+)/', 'if ($role_id == $1', $c);
    $c = preg_replace('/\bstatus = 1\b/', "status = '1'", $c);
    $c = str_replace("header('location:", "header('Location:", $c);
    $c = preg_replace("/header\('Location: ([^']+)'\);\s*\r?\n\s*mysqli_close\(\$con\);\s*\r?\n\s*exit\(0\);/s", "header('Location: $1');\n              exit;", $c);
    $c = preg_replace("/header\('Location: ([^']+)'\);\s*\r?\n\s*exit\(0\);/s", "header('Location: $1');\n              exit;", $c);
    if ($c !== $orig) {
        file_put_contents($path, $c);
        echo "fixed: " . basename($path) . "\n";
    }
}
echo "done\n";
