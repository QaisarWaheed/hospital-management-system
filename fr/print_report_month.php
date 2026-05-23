<?php
include 'includes/connect.php';
require_once __DIR__ . '/../includes/report_helpers.php';

if (!isset($_GET['date'], $_GET['br_id']) || $_GET['date'] === '') {
    http_response_code(400);
    exit('Date and branch are required.');
}

$date = $_GET['date'];
$br_id = (int) $_GET['br_id'];
$ym = ycdo_parse_year_month($date);
$year = $ym['year'];
$month = $ym['month'];
$days = $ym['days'];
?>
<html>
<head>
    <title>PRINT MONTHLY PROGRESS REPORT</title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h4>Progress For The Month Of <?php echo date_format(date_create($date), " F Y"); ?></h4>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>DATE</th>
            <th>TOKEN CASH</th>
            <th>RETURN TOKEN</th>
            <th>COLLECTION</th>
            <th>LOGIN RECEIVED</th>
            <th>LOGIN EXTRA</th>
            <th>LOGIN SHORT</th>
            <th>LOGIN TOTAl</th>
        </tr>
    </thead>
    <tbody>
<?php
$s = 0;
$total_cash = 0;
$total_login = 0;
$total_extra_amount = 0;
$total_short_amount = 0;
$total_collection = 0;
$total_return_token = 0;

for ($x = 1; $x <= $days; $x++)
{
    $s++;
    $day = $x < 10 ? '0' . $x : (string) $x;
    $select_date = $day . '-' . $month . '-' . $year;
    $collection_amount = 0;
    $cash_amount = 0;
    $return_token_amount = 0;
    $received_amount = 0;
    $extra_amount = 0;
    $short_amount = 0;

//COLLECTION
$collection = "SELECT SUM(`cash_received`),SUM(`cash`) FROM tokans WHERE branch_id = '$br_id' AND created LIKE '$year-$month-$day%' AND status = 1 ";
$run_collection = mysqli_query($con, $collection);
if(mysqli_num_rows($run_collection) == 1)
{
    while($row_collection = mysqli_fetch_array($run_collection))
    {
        $collection_amount = $row_collection['0'];
        $total_collection = $total_collection + $collection_amount;
        
        $cash_amount = $row_collection['1'];
        $total_cash = $total_cash + $cash_amount;
    }
}
else
{
        $collection_amount = 0;
        $total_collection = $total_collection + $collection_amount;
        
        $cash_amount = 0;
        $total_cash = $total_cash + $cash_amount;
}

//Return Tokens
$return_token = "SELECT SUM(`cash_received`) FROM tokans WHERE branch_id = '$br_id' AND created LIKE '$year-$month-$day%' AND status = 3 ";
$run_return_token = mysqli_query($con, $return_token);
if(mysqli_num_rows($run_return_token) == 1)
{
    while($row_return_token = mysqli_fetch_array($run_return_token))
    {
        $return_token_amount = $row_return_token['0'];
        $total_return_token = $total_return_token + $return_token_amount;
    }
}
else
{
        $return_token_amount = 0;
        $total_return_token = $total_return_token + $return_token_amount;
}

//Total Login
$users = "SELECT sum(`received_amount`) AS received_amount ,SUM(extra_amount) AS extra_amount ,SUM(short_amount) AS short_amount FROM `summary_details` WHERE login_id IN (SELECT id FROM logins_detail WHERE branch_id = '$br_id' AND login_at LIKE '$year-$month-$day%' AND `status` = '2') ";
$run_users = mysqli_query($con, $users);
if(mysqli_num_rows($run_users) > 0)
{
    while($row_users = mysqli_fetch_array($run_users))
    {
        $received_amount= $row_users['received_amount'];
        $total_login = $total_login + $received_amount;
        
        $extra_amount= $row_users['extra_amount'];
        $total_extra_amount = $total_extra_amount + $extra_amount;
        
        $short_amount= $row_users['short_amount'];
        $total_short_amount = $total_short_amount + $short_amount;
    }
}
else
{
        $received_amount = 0;
        $total_login = $total_login + $received_amount;
        
        $extra_amount = 0;
        $total_extra_amount = $total_extra_amount + $extra_amount;
        
        $short_amount = 0;
        $total_short_amount = $total_short_amount + $short_amount;
}

        echo ' <tr style = "text-align: right;">
                <td>'.$s.'</td>
                <td>'.$select_date.'</td>
                <td>'.report_safe_number_format((float) ($cash_amount ?? 0)).'</td>
                <td>'.report_safe_number_format($return_token_amount).'</td>
                <td>'.report_safe_number_format($collection_amount).'</td>
                <td>'.report_safe_number_format($received_amount).'</td>
                <td>'.report_safe_number_format($extra_amount).'</td>
                <td>'.report_safe_number_format($short_amount).'</td>
                <td>'.report_safe_number_format($received_amount+$extra_amount).'</td>
            </tr>';
}
    echo '</tbody>';
    echo '<tfoot>
            <tr style = "text-align: right;">
                <th colspan = "2">TOTAL</th>
                <th>'.report_safe_number_format($total_cash).'</th>
                <th>'.report_safe_number_format($total_return_token).'</th>
                <th>'.report_safe_number_format($total_collection).'</th>
                <th>'.report_safe_number_format($total_login).'</th>
                <th>'.report_safe_number_format($total_extra_amount).'</th>
                <th>'.report_safe_number_format($total_short_amount).'</th>
                <th>'.report_safe_number_format($total_login+$total_extra_amount).'</th>
            </tr>
        </tfoot>';
?>
</table>

</body>
</html>
