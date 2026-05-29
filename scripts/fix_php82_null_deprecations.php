<?php
/**
 * Fix PHP 8.2 NULL deprecation warnings (safe transforms only).
 * Run: php scripts/fix_php82_null_deprecations.php
 *
 * NOTE: date_format() is NOT auto-wrapped — it breaks when nested inside strings.
 */

$root = dirname(__DIR__);

$excludeDirs = [
    'includes/tcpdf',
    'mm/includes/tcpdf',
    'mm/includes/fpdf',
    'lab/includes/phpqrcode',
    'la/includes/phpqrcode',
    '.git',
    'node_modules',
    'vendor',
    'scripts',
];

function shouldProcess(string $root, string $path): bool
{
    global $excludeDirs;
    $rel = str_replace('\\', '/', substr($path, strlen($root) + 1));
    foreach ($excludeDirs as $ex) {
        if ($rel === $ex || str_starts_with($rel, $ex . '/')) {
            return false;
        }
    }
    return str_ends_with(strtolower($path), '.php');
}

function findCallEnd(string $text, int $openParenIndex): int
{
    $depth = 0;
    $len = strlen($text);
    for ($i = $openParenIndex; $i < $len; $i++) {
        $ch = $text[$i];
        if ($ch === '(') {
            $depth++;
        } elseif ($ch === ')') {
            $depth--;
            if ($depth === 0) {
                return $i + 1;
            }
        } elseif ($ch === "'" || $ch === '"') {
            $quote = $ch;
            $i++;
            while ($i < $len) {
                if ($text[$i] === '\\') {
                    $i += 2;
                    continue;
                }
                if ($text[$i] === $quote) {
                    break;
                }
                $i++;
            }
        }
    }
    return $len;
}

function splitFirstArg(string $args): array
{
    $depth = 0;
    $len = strlen($args);
    for ($i = 0; $i < $len; $i++) {
        $ch = $args[$i];
        if ($ch === '(') {
            $depth++;
        } elseif ($ch === ')') {
            if ($depth === 0) {
                return [trim(substr($args, 0, $i)), substr($args, $i)];
            }
            $depth--;
        } elseif ($ch === ',' && $depth === 0) {
            return [trim(substr($args, 0, $i)), substr($args, $i)];
        }
    }
    return [trim($args), ''];
}

function alreadyFloatNullWrapped(string $expr): bool
{
    $expr = trim($expr);
    return str_starts_with($expr, '(float)(') && str_contains($expr, '??');
}

function wrapNumericExpr(string $expr): string
{
    $expr = trim($expr);
    if ($expr === '') {
        return '(float)(0)';
    }
    if (alreadyFloatNullWrapped($expr)) {
        return $expr;
    }
    if (str_contains($expr, '??')) {
        return '(float)(' . $expr . ')';
    }
    return '(float)(' . $expr . ' ?? 0)';
}

function fixNumberFormat(string $text): string
{
    $needle = 'number_format(';
    $out = '';
    $pos = 0;
    while (($idx = strpos($text, $needle, $pos)) !== false) {
        $out .= substr($text, $pos, $idx - $pos);
        $argStart = $idx + strlen($needle);
        $callEnd = findCallEnd($text, $idx + strlen('number_format'));
        $inner = substr($text, $argStart, $callEnd - $argStart - 1);
        [$first, $rest] = splitFirstArg($inner);
        if (!alreadyFloatNullWrapped($first)) {
            $first = wrapNumericExpr($first);
        }
        $out .= 'number_format(' . $first . $rest . ')';
        $pos = $callEnd;
    }
    return $out . substr($text, $pos);
}

function fixUnaryFunc(string $text, string $func, string $default): string
{
    $pattern = '/\b' . preg_quote($func, '/') . '\(\s*(\$[a-zA-Z_\x7f-\xff][\w\x7f-\xff]*(?:\->[\w\x7f-\xff]+|\[[^\]]+\])*)\s*\)/u';
    return preg_replace_callback($pattern, function ($m) use ($func, $default) {
        if (str_contains($m[1], '??')) {
            return $m[0];
        }
        return $func . '(' . $m[1] . ' ?? ' . $default . ')';
    }, $text);
}

function fixRound(string $text): string
{
    $needle = 'round(';
    $out = '';
    $pos = 0;
    while (($idx = strpos($text, $needle, $pos)) !== false) {
        $out .= substr($text, $pos, $idx - $pos);
        $callEnd = findCallEnd($text, $idx + strlen('round'));
        $inner = substr($text, $idx + strlen($needle), $callEnd - $idx - strlen($needle) - 1);
        [$first, $rest] = splitFirstArg($inner);
        if (!str_contains($first, '??')) {
            $first = trim($first) . ' ?? 0';
        }
        $out .= 'round(' . $first . $rest . ')';
        $pos = $callEnd;
    }
    return $out . substr($text, $pos);
}

function processFile(string $path): bool
{
    $original = file_get_contents($path);
    if ($original === false) {
        return false;
    }
    $updated = $original;
    $updated = fixNumberFormat($updated);
    $updated = fixUnaryFunc($updated, 'intval', '0');
    $updated = fixUnaryFunc($updated, 'floatval', '0');
    $updated = fixRound($updated);
    $updated = fixUnaryFunc($updated, 'strlen', "''");

    if ($updated !== $original) {
        file_put_contents($path, $updated);
        return true;
    }
    return false;
}

$changed = [];
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (!$file->isFile()) {
        continue;
    }
    $path = $file->getPathname();
    if (!shouldProcess($root, $path)) {
        continue;
    }
    if (processFile($path)) {
        $changed[] = str_replace('\\', '/', substr($path, strlen($root) + 1));
    }
}

sort($changed);
echo 'Updated ' . count($changed) . " files\n";
foreach ($changed as $name) {
    echo "  $name\n";
}
