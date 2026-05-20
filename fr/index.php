<?php
require_once __DIR__ . '/includes/connect_public.php';
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
//    header('location: 404.html');
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <title>BRANCH CHECKING</title>
</head>
<body style="background: skyblue; background-image: url('images/logo.jpg');background-repeat: no-repeat;background-size: cover;">
<h1 align="right">Youth Community Development Organization</h1>
<p style="color: maroon;text-align: right;">Serve Humanity</p>
<h2 align="right" style="color: red;">YCDO Central Hospital</h2>
<h3 align="right">UAN : 0304-1110222, Multan</h3>
    <div style="padding: 30px;margin: 0% 30%;border: 5px solid black;background: whitesmoke;border-radius: 120px 10px;">

        <div style="">
            <h1 align="center" style="color: skyblue;">WELCOME TO YCDO</h1>
            <h3 align="center">BRANCH VERIFICATION</h3>
            <?php if(isset($msg)){echo '<p style="color: red;text-align: center">'.$msg.'</p>';}  ?>
            <form method="POST" autocomplete="off" action="login.php">
                <label>SELECT BRANCH</label>
                <select class="form-control" style="min-width: 200px;text-transform: uppercase;" name="branch_id">
<?php 
$branch = "SELECT * FROM branchs WHERE status = 1 ORDER BY `address` ASC ";
$run_branch = mysqli_query($con, $branch);
if (mysqli_num_rows($run_branch) > 0) 
{
    while ($row_branch = mysqli_fetch_array($run_branch)) {
        echo '<option value="'.$row_branch['id'].'">'.$row_branch['address'].'</option>';
    }
}
else
{
    echo '<option value="">Add Doctors Data</option>';
}
?>
                </select>
<!--                 <label>IP ADDRESS</label>
                <input class="form-control" type="text" autocomplete="off" required name="ip_address" value="<?php echo $ip_address; ?>" /> -->
                <br>
            <input class="btn btn-sm btn-primary" type="submit" name="verify" value="VERIFICATION">
            <input type="reset" name="reset" class="btn btn-sm btn-warning" value="CLEAR FORM">
            </form>
        </div>
    </div>

</body>
</html>