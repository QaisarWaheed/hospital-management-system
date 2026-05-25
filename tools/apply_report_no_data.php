<?php
/**
 * One-off: add $has_data + ycdo_echo_report_no_data_found() to print report pages.
 * Run: php tools/apply_report_no_data.php
 */

$roots = array(
    __DIR__ . '/../bk',
    __DIR__ . '/../hr',
    __DIR__ . '/../fr',
    __DIR__ . '/../dr',
    __DIR__ . '/../dr/fr',
    __DIR__ . '/../pharmecy',
);

$skipName = '/(?:slip|tokan|staff|medicine|admission|salary|duplicate|demo_|medicine_slip|print_tokan)/i';
$matchName = '/(?:print_|report_|progress_|summary_|monthly_|daily_)/i';

$updated = 0;
$skipped = 0;

foreach ($roots as $root) {
    if (!is_dir($root)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $file) {
        if (!$file->isFile() || strtolower($file->getExtension()) !== 'php') {
            continue;
        }
        $base = $file->getFilename();
        if (!preg_match($matchName, $base)) {
            continue;
        }
        if (preg_match($skipName, $base)) {
            continue;
        }
        $path = $file->getPathname();
        $content = file_get_contents($path);
        if ($content === false || strpos($content, 'ycdo_echo_report_no_data_found') !== false) {
            $skipped++;
            continue;
        }
        if (stripos($content, '<table') === false && stripos($content, '<tbody') === false) {
            $skipped++;
            continue;
        }

        $orig = $content;

        // Upgrade legacy tbody "NO DATA FOUND" rows to styled empty state.
        $content = preg_replace(
            '/\}\s*else\s*\{\s*echo\s*\'<tbody><tr><td colspan="\d+">NO DATA FOUND[^;]*;\s*\}/s',
            '}',
            $content
        );

        // Inject $has_data = false before common empty guards (once per file if missing).
        if (strpos($content, '$has_data') === false) {
            $content = preg_replace(
                '/(\s*)(if\s*\(\s*count\s*\([^)]+\)\s*>\s*0\s*\)\s*\{)/',
                '$1$has_data = false;' . "\n" . '$1$2',
                $content,
                1
            );
            $content = preg_replace(
                '/(\s*)(if\s*\(\s*\$count_run\s*>\s*0\s*\)\s*\{)/',
                '$1$has_data = false;' . "\n" . '$1$2',
                $content,
                1
            );
            $content = preg_replace(
                '/(\s*)(if\s*\(\s*mysqli_num_rows\s*\([^)]+\)\s*>\s*0\s*\)\s*\{)/',
                '$1$has_data = false;' . "\n" . '$1$2',
                $content,
                1
            );
            $content = preg_replace(
                '/(\s*)(if\s*\(\s*\$run\s*&&\s*mysqli_num_rows\s*\([^)]+\)\s*>\s*0\s*\)\s*\{)/',
                '$1$has_data = false;' . "\n" . '$1$2',
                $content,
                1
            );
            $content = preg_replace(
                '/(\s*)(if\s*\(\s*\$run\s*&&\s*\$count_run\s*>\s*0\s*\)\s*\{)/',
                '$1$has_data = false;' . "\n" . '$1$2',
                $content,
                1
            );
            $content = preg_replace(
                '/(\s*)(if\s*\(\s*\$detailRun\s*\)\s*\{)/',
                '$1$has_data = false;' . "\n" . '$1$2',
                $content,
                1
            );
            if (strpos($content, '$has_data') === false && preg_match('/foreach\s*\(/', $content)) {
                $content = preg_replace(
                    '/(<tbody>\s*\n\s*<\?php)/i',
                    "<?php \$has_data = false; ?>\n$1",
                    $content,
                    1
                );
                if (strpos($content, '$has_data') === false) {
                    $content = preg_replace(
                        '/(\s*)(foreach\s*\([^)]+\)\s*as\s+[^)]+\)\s*\{)/',
                        '$1$has_data = false;' . "\n" . '$1$2',
                        $content,
                        1
                    );
                }
            }
            if (strpos($content, '$has_data') === false && preg_match('/\bwhile\s*\(/', $content)) {
                $content = preg_replace(
                    '/(\s*)(while\s*\([^)]+\)\s*\{)/',
                    '$1$has_data = false;' . "\n" . '$1$2',
                    $content,
                    1
                );
            }
        }

        // Mark data rows inside foreach / while loops (first loop only if not yet set).
        if (strpos($content, '$has_data') !== false && strpos($content, '$has_data = true') === false) {
            $content = preg_replace(
                '/(foreach\s*\([^)]+\)\s*(?:as\s+[^)]+\)\s*)?\{)\s*\n/',
                "$1\n        \$has_data = true;\n",
                $content,
                1
            );
            if (strpos($content, '$has_data = true') === false) {
                $content = preg_replace(
                    '/(while\s*\([^)]+\)\s*\{)\s*\n/',
                    "$1\n        \$has_data = true;\n",
                    $content,
                    1
                );
            }
        }

        // Append empty-state check before </table> when $has_data exists.
        if (strpos($content, '$has_data') !== false && strpos($content, 'ycdo_echo_report_no_data_found') === false) {
            if (preg_match('/<\/table>/i', $content)) {
                $content = preg_replace(
                    '/(\s*)<\/table>/i',
                    "$1<?php if (!\$has_data) { ycdo_echo_report_no_data_found(); } ?>\n$1</table>",
                    $content,
                    1
                );
            } elseif (preg_match('/<\/body>/i', $content)) {
                $content = preg_replace(
                    '/(\s*)<\/body>/i',
                    "$1<?php if (!\$has_data) { ycdo_echo_report_no_data_found(); } ?>\n$1</body>",
                    $content,
                    1
                );
            }
        }

        if ($content !== $orig) {
            file_put_contents($path, $content);
            echo "updated: $path\n";
            $updated++;
        } else {
            $skipped++;
        }
    }
}

echo "Done. updated=$updated skipped=$skipped\n";
