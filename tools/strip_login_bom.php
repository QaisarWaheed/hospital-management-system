<?php
$root = dirname(__DIR__);
$files = array_merge(
    glob($root . '/*_login.php') ?: [],
    glob($root . '/login.php') ?: []
);
foreach ($files as $path) {
    $c = file_get_contents($path);
    if ($c === false) {
        continue;
    }
    $orig = $c;
    $c = preg_replace(
        '/^\xEF\xBB\xBF?\s*<\?php\s*\r?\n(include\s+[\'"]includes\/connect\.php)/',
        "<?php\nrequire_once __DIR__ . '/includes/ycdo_bootstrap.php';\n$1",
        $c,
        1
    );
    if ($c !== $orig) {
        file_put_contents($path, $c);
        echo "fixed: " . basename($path) . "\n";
    }
}
