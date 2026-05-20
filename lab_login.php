<?php
require_once __DIR__ . '/includes/ycdo_bootstrap.php';
include 'includes/connect.php';

if (!$con) {
    http_response_code(503);
    exit('Database connection failed.');
}

function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP')) {
        $ipaddress = getenv('HTTP_CLIENT_IP');
    } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    } elseif (getenv('HTTP_X_FORWARDED')) {
        $ipaddress = getenv('HTTP_X_FORWARDED');
    } elseif (getenv('HTTP_FORWARDED_FOR')) {
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    } elseif (getenv('HTTP_FORWARDED')) {
        $ipaddress = getenv('HTTP_FORWARDED');
    } elseif (getenv('REMOTE_ADDR')) {
        $ipaddress = getenv('REMOTE_ADDR');
    } else {
        $ipaddress = 'UNKNOWN';
    }
    return $ipaddress;
}

$branch_id = isset($_GET['branch_id']) ? (int) $_GET['branch_id'] : 0;
if ($branch_id < 1) {
    header('Location: index.php');
    exit;
}

$ip_address = get_client_ip();
$check = mysqli_query($con, "SELECT * FROM whitelist WHERE `ip_address` = '$ip_address' ");
if ($check && mysqli_num_rows($check) == 0) {
    // optional IP whitelist enforcement
}

$msg = '';
$branch_name = '';
$branch_address = '';
$branch_phone = '';

if (isset($_POST['login'])) {
    $user_id = (int) $_POST['user_id'];
    $branch_id = (int) $_POST['branch_id'];
    $role_id = 0;

    $select_branch = mysqli_query($con, "SELECT * FROM `branchs` WHERE `id` = '$branch_id' ");
    if ($select_branch && mysqli_num_rows($select_branch) == 1) {
        while ($row_branch = mysqli_fetch_array($select_branch)) {
            $branch_name = $row_branch['name'];
            $branch_address = $row_branch['address'];
            $branch_phone = $row_branch['phone'];
        }
    }

    $password = md5($_POST['password']);
    $try_password = $_POST['password'];
    $user = "SELECT * FROM users WHERE id = '$user_id' AND password = '$password' AND status = '1' ";
    $run_user = mysqli_query($con, $user);

    if ($run_user && mysqli_num_rows($run_user) > 0) {
        while ($row_user = mysqli_fetch_array($run_user)) {
            $user_name = $row_user['u_name'];
            $user_phone = $row_user['phone'];
            $role_id = (int) $row_user['role_id'];
            $is_admin = $row_user['is_admin'];
            $is_incharge = $row_user['is_incharge'];

            if ($role_id == 8 || $role_id == 0) {
                mysqli_query($con, "INSERT INTO `whitelist`(`ip_address`, `user`, `status`, `role_id`, `branch_id`, `created`) VALUES ('$ip_address', '$user_id', '1', '$role_id', '$branch_id', '$current_date')");

                $currentdate = date('Y-m-d H:i:s');
                mysqli_query($con, "INSERT INTO `logins_detail_fr_mm_sm`
                    (`user_id`, `created`, `device_ip_address`, `role_id`, `branch_id`) VALUES
                    ('$user_id', '$currentdate', '$ip_address', '8', '$branch_id')");

                $_SESSION['lab_user_id'] = $user_id;
                $_SESSION['role_id'] = $role_id;
                $_SESSION['lab_login_branch_id'] = $branch_id;
                $_SESSION['lab_login_is_admin'] = $is_admin;
                $_SESSION['lab_login_is_incharge'] = $is_incharge;
                $_SESSION['lab_login_branch_name'] = $branch_name;
                $_SESSION['lab_login_branch_address'] = $branch_address;
                $_SESSION['lab_login_branch_phone'] = $branch_phone;
                $_SESSION['lab_user_name'] = $user_name;
                $_SESSION['lab_user_phone'] = $user_phone;
                header('Location: lab/dashboard.php');
                exit;
            }
        }
        $msg = 'This user is not allowed to log in to Lab.';
    } else {
        mysqli_query($con, "INSERT INTO `wrong_logins`(`user_id`, `password`, `role_id`, `ip_address`, `status`, `branch_id`, `created`) VALUES('$user_id', '$try_password', '$role_id', '$ip_address', '1', '$branch_id', '$current_date')");
        $msg = 'User Detail Not Matched';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <title>Lab Login - YCDO</title>
</head>
<body style="background: skyblue; background-image: url('images/logo.jpg');background-repeat: no-repeat;background-size: cover;">
<h1 align="right">Youth Community Development Organization</h1>
<p style="color: maroon;text-align: right;">Serve Humanity</p>
<h2 align="right" style="color: red;">YCDO Central Hospital</h2>
<h3 align="right">UAN : 0304-1110222, Multan</h3>
    <div class="login-box" style="padding: 30px;margin: 0% 30%;border: 5px solid black;background: whitesmoke;border-radius: 120px 10px;">

        <div>
            <h1 align="center" style="color: skyblue;">WELCOME TO YCDO</h1>
            <h2 align="center">LAB USER LOGIN</h2>
            <?php if ($msg !== '') { echo '<p style="color: red;text-align: center">' . htmlspecialchars($msg) . '</p>'; } ?>
            <form method="POST" autocomplete="off">
                <label>SELECT USER</label>
                <select class="form-control" style="min-width: 200px;text-transform: uppercase;" name="user_id" required>
<?php
$user = "SELECT * FROM users WHERE (role_id = 0 AND branch_id = 0 AND status = '1') OR (role_id = 8 AND status = '1' AND branch_id = '$branch_id') ORDER BY `u_name` ASC ";
$run_user = mysqli_query($con, $user);
if ($run_user && mysqli_num_rows($run_user) > 0) {
    while ($row_user = mysqli_fetch_array($run_user)) {
        echo '<option value="' . (int) $row_user['id'] . '">' . htmlspecialchars($row_user['u_name']) . '</option>';
    }
} else {
    echo '<option value="">No lab users for this branch</option>';
}
?>
                </select>

                <label>PASSWORD</label>
                <input type="hidden" required name="branch_id" value="<?php echo (int) $branch_id; ?>" />
                <input class="form-control" type="password" autocomplete="off" required name="password" /><br>

            <input class="btn btn-sm btn-primary" type="submit" name="login" value="LOGIN">
            <input type="reset" name="reset" class="btn btn-sm btn-warning" value="CLEAR FORM">
            <a href="index.php" class="btn btn-info btn-sm">GOTO VERIFICATION PAGE</a>
            </form>
        </div>
    </div>

</body>
</html>
