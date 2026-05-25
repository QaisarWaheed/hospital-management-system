<?php
/**
 * Remove ycdo_echo_report_no_data_found() and $has_data empty-state logic from reports.
 * Run: php tools/remove_report_no_data.php
 */

$root = dirname(__DIR__);
$skipDirs = array('/vendor/', '/node_modules/', '/includes/tcpdf/');
$updated = 0;

$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($it as $file) {
    if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
        continue;
    }
    $path = $file->getPathname();
    $norm = str_replace('\\', '/', $path);
    foreach ($skipDirs as $skip) {
        if (strpos($norm, $skip) !== false) {
            continue 2;
        }
    }
    if (basename($path) === 'remove_report_no_data.php' || basename($path) === 'apply_report_no_data.php') {
        continue;
    }

    $content = file_get_contents($path);
    if ($content === false) {
        continue;
    }
    if (strpos($content, 'ycdo_echo_report_no_data_found') === false
        && strpos($content, '$has_data') === false) {
        continue;
    }
    if (basename($path) === 'ycdo_bootstrap.php') {
        continue;
    }

    $orig = $content;

    $content = preg_replace(
        '/\s*<\?php if \(\s*!\$has_data\s*\)\s*\{\s*ycdo_echo_report_no_data_found\(\);\s*\}\s*\?>\s*/',
        "\n",
        $content
    );
    $content = preg_replace(
        '/\s*<\?php if \(\s*!\$has_data\s*\)\s*\{\s*ycdo_echo_report_no_data_found\(\);\s*\?>\s*/',
        "\n",
        $content
    );
    $content = preg_replace(
        '/\s*if\s*\(\s*!\$has_data\s*\)\s*\{\s*ycdo_echo_report_no_data_found\(\);\s*\}\s*/',
        "\n",
        $content
    );
    $content = preg_replace(
        '/\s*ycdo_echo_report_no_data_found\(\);\s*/',
        "\n",
        $content
    );
    $content = preg_replace(
        '/^\s*\$has_data = false;\s*\r?\n/m',
        '',
        $content
    );
    $content = preg_replace(
        '/^\s*\$has_data = true;\s*\r?\n/m',
        '',
        $content
    );
    $content = preg_replace(
        "/\s*if\s*\(\s*!\$has_data\s*\)\s*\{\s*echo\s*'<tr><td colspan=\"\\d+\">NO DATA FOUND<\\/td><\\/tr>';\s*\}\s*/",
        '',
        $content
    );
    $content = preg_replace(
        "/\s*\}\s*else\s*\{\s*echo\s*'<tr><td colspan=\"\\d+\">NO DATA FOUND<\\/td><\\/tr>';\s*\}\s*/",
        "\n",
        $content
    );
    $content = preg_replace(
        '/\s*if\s*\(\s*\$has_data\s*\)\s*\{\s*\?>\s*/',
        "\n",
        $content,
        1
    );

    if ($content !== $orig) {
        file_put_contents($path, $content);
        echo "updated: $norm\n";
        $updated++;
    }
}

echo "Done. updated=$updated\n";
