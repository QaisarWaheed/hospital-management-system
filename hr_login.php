<?php
require_once __DIR__ . '/includes/ycdo_bootstrap.php';
include 'includes/connect.php';
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
$ip_address = get_client_ip();
$check = mysqli_query($con, "SELECT * FROM whitelist WHERE `ip_address` = '$ip_address' ");
if (mysqli_num_rows($check) == 0) {
//    header('Location: 404.html');
}
if (isset($_POST['login'])) {
        $user_id = $_POST['user_id'];
        $branch_id = $_POST['branch_id'];
        $select_branch = mysqli_query($con, "SELECT * FROM `branchs` WHERE `id` = '$branch_id' ");
            if (mysqli_num_rows($select_branch) == 1) 
            {
                while ($row_branch = mysqli_fetch_array($select_branch)) 
                {
                    $branch_name = $row_branch['name'];
                    $branch_address = $row_branch['address'];
                    $branch_phone = $row_branch['phone'];
                }
            }
     $password = md5($_POST['password']);
     $try_password = $_POST['password'];
$user = "SELECT * FROM users WHERE id = '$user_id' AND password = '$password' AND status = '1' ";
$run_user = mysqli_query($con, $user);
if (mysqli_num_rows($run_user) > 0) 
{
    while ($row_user = mysqli_fetch_array($run_user)) 
    {
        $user_name = $row_user['u_name'];
        $role_id = $row_user['role_id'];
        $is_admin = $row_user['is_admin'];
        $is_incharge = $row_user['is_incharge'];
if ($role_id == 12 ||$role_id == 0) 
        {   
            mysqli_query($con, "INSERT INTO `whitelist`(`ip_address`, `user`, `status`, `role_id`, `branch_id`, `created`) VALUES ('$ip_address', '$user_id', '1', '$role_id', '$branch_id', '$current_date')");   
            $currentdate = date('Y-m-d H:i:s');     
            mysqli_query($con, "INSERT INTO `logins_detail_fr_mm_sm`
                (`user_id`, `created`, `device_ip_address`, `role_id`, `branch_id`) VALUES 
                ('$user_id', '$currentdate', '$ip_address', '12', '$branch_id')");
              $_SESSION['hr_id'] = $user_id;
              $_SESSION['role_id'] = $role_id;
              $_SESSION['branch_id'] = $branch_id;
              $_SESSION['is_admin'] = $is_admin;
              $_SESSION['is_incharge'] = $is_incharge;
              $_SESSION['branch_name'] = $branch_name;
              $_SESSION['branch_address'] = $branch_address;
              $_SESSION['branch_phone'] = $branch_phone;
              $_SESSION['hr_name'] = $user_name;
              header('Location: hr/dashboard.php');
              exit;               
        }          

    }
}
else
{
    
        mysqli_query($con, "INSERT INTO `wrong_logins`(`user_id`, `password`, `role_id`, `ip_address`, `status`, `branch_id`, `created`) VALUES('$user_id', '$try_password', '$role_id', '$ip_address',  '1', '$branch_id', '$current_date')");  
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
    <title></title>
</head>
<body style="background: skyblue; background-image: url('images/logo.jpg');background-repeat: no-repeat;background-size: cover;">
<h1 align="right">Youth Community Development Organization</h1>
<p style="color: maroon;text-align: right;">Serve Humanity</p>
<h2 align="right" style="color: red;">YCDO Central Hospital</h2>
<h3 align="right">UAN : 0304-1110222, Multan</h3>
    <div style="padding: 30px;margin: 0% 30%;border: 5px solid black;background: whitesmoke;border-radius: 120px 10px;">

        <div style="">
            <h1 align="center" style="color: skyblue;">WELCOME TO YCDO</h1>
            <h2 align="center">USER LOGIN</h2>
            <?php if(isset($msg)){echo '<p style="color: red;text-align: center">'.$msg.'</p>';}  ?>
            <form method="POST" autocomplete="off">
                <label>SELECT USER</label>
                <select class="form-control" style="min-width: 200px;text-transform: uppercase;" name="user_id">
<?php 
// if (isset($_POST['verify'])) 
// {
//     $branch_id = $_POST['branch_id'];
// }
// else
// {
//     header('Location: index.php');
// }
$branch_id = $_GET['branch_id'];
$user = "SELECT * FROM users WHERE (role_id = 0 AND status = '1' AND branch_id = '0') OR (role_id = 12 AND status = '1' AND branch_id = '$branch_id') ORDER BY `u_name` ASC ";
$run_user = mysqli_query($con, $user);
if (mysqli_num_rows($run_user) > 0) 
{
    while ($row_user = mysqli_fetch_array($run_user)) {
        echo '<option value="'.$row_user['id'].'">'.$row_user['u_name'].'</option>';
    }
}
else
{
    echo '<option value="">Add Audit Officer Data</option>';
}
?>
                </select>

                <label>PASWORD</label>
                <input type="hidden" required name="branch_id" value = "<?php echo $branch_id; ?>" />
                <input class="form-control" type="password" autocomplete="off" required name="password" /><br>

            <input class="btn btn-sm btn-primary" type="submit" name="login" value="LOGIN">
            <input type="reset" name="reset" class="btn btn-sm btn-warning" value="CLEAR FORM">
            <a href="index.php" class="btn btn-info btn-sm">GOTO VERIFICATION PAGE</a>
            </form>
        </div>
    </div>

</body>
</html>