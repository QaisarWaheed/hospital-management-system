<?php
$modules = [
    'mm' => ['key' => 'mm_id', 'name' => 'mm_name', 'user_var' => 'user_id'],
    'fr' => ['key' => 'fr_id', 'name' => 'fr_name', 'user_var' => 'fr_id'],
    'ao' => ['key' => 'ao_id', 'name' => 'ao_name', 'user_var' => 'user_id'],
];
foreach ($modules as $dir => $cfg) {
    $path = __DIR__ . "/../$dir/includes/connect.php";
    if (!is_file($path)) {
        continue;
    }
    $lines = file($path);
    $start = 0;
    foreach ($lines as $i => $line) {
        if (preg_match('/^function /', $line)) {
            $start = $i;
            break;
        }
    }
    $rest = implode('', array_slice($lines, $start));
    $uv = $cfg['user_var'];
    $sk = $cfg['key'];
    $sn = $cfg['name'];
    $header = <<<PHP
<?php
require_once __DIR__ . '/../../includes/ycdo_bootstrap.php';
date_default_timezone_set("Asia/Karachi");
\$current_date = date('Y-m-d G:i:s A');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty(\$_SESSION['$sk'])) {
    header('Location: logout.php');
    exit;
}

\$$uv = (int) \$_SESSION['$sk'];
\$user_name = \$_SESSION['$sn'] ?? '';
\$branch_id = \$_SESSION['branch_id'] ?? 0;
\$is_admin = \$_SESSION['is_admin'] ?? 0;
\$is_incharge = \$_SESSION['is_incharge'] ?? 0;
\$role_id = \$_SESSION['role_id'] ?? 0;
\$branch_name = \$_SESSION['branch_name'] ?? '';
\$branch_address = \$_SESSION['branch_address'] ?? '';
\$branch_phone = \$_SESSION['branch_phone'] ?? '';

if (\$$uv < 1) {
    header('Location: logout.php');
    exit;
}

include 'company_info.php';
\$con = ycdo_db_connect();

PHP;
    file_put_contents($path, $header . $rest);
    echo "patched $path\n";
}
