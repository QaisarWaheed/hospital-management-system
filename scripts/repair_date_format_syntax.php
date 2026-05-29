<?php
/**
 * Repair broken date_format ternary from bulk fix: missing ")" before ";"
 * Broken:  ... ? date_format(...) : '';
 * Fixed:   ... ? date_format(...) : '');
 */

$root = dirname(__DIR__);
$exclude = ['includes/tcpdf', 'mm/includes/tcpdf', 'lab/includes/phpqrcode', 'la/includes/phpqrcode', '.git', 'vendor', 'node_modules', 'scripts'];

function shouldProcess(string $root, string $path): bool
{
    global $exclude;
    $rel = str_replace('\\', '/', substr($path, strlen($root) + 1));
    foreach ($exclude as $ex) {
        if ($rel === $ex || str_starts_with($rel, $ex . '/')) {
            return false;
        }
    }
    return str_ends_with(strtolower($path), '.php');
}

function repairContent(string $text): string
{
    // Assignment ternary missing closing paren: : '';
    $text = preg_replace(
        '/(\? date_format\(date_create\([^)]+\),\s*[^)]+\)\s*:\s*\'\');/',
        '$1);',
        $text
    );

    // Same but with double-quoted format string
    $text = preg_replace(
        '/(\? date_format\(date_create\([^)]+\),\s*"[^"]*"\)\s*:\s*\'\');/',
        '$1);',
        $text
    );

    // Inside string concat: : ''.'
    $text = str_replace(": ''.'", ": '').'", $text);

    // Revert standalone broken ternary assignments to simple date_format when still broken
    $text = preg_replace_callback(
        '/=\s*\(\$(\w+(?:\[[^\]]+\])*) && \1 != \'0000-00-00\' && \1 != \'0000-00-00 00:00:00\' \? date_format\(date_create\(\1\),\s*("[^"]*"|\'[^\']*\')\)\s*:\s*\'\'\);/',
        static function ($m) {
            return '= date_format(date_create($' . $m[1] . '), ' . $m[2] . ');';
        },
        $text
    );

    return $text;
}

$changed = [];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
foreach ($it as $file) {
    if (!$file->isFile() || !shouldProcess($root, $file->getPathname())) {
        continue;
    }
    $path = $file->getPathname();
    $original = file_get_contents($path);
    $updated = repairContent($original);
    if ($updated !== $original) {
        file_put_contents($path, $updated);
        $changed[] = str_replace('\\', '/', substr($path, strlen($root) + 1));
    }
}

sort($changed);
echo 'Repaired ' . count($changed) . " files\n";
foreach ($changed as $f) {
    echo "  $f\n";
}
