<?php 
include 'includes/connect.php'; 
if (isset($_POST['search'])) 
{
	$token_no = $_POST['token_no'];
}
?>
<?php include 'includes/head.php'; ?>
	<title>Patient Record - <?php echo $company_trademark; ?></title>
</head>
<body class="background_image_ycdo">
<div class = "row">
    <div class = "col-md-12">
        <?php include 'navigation_dashboard.php'; ?>
    </div>
	<div class="col-md-12" style="margin: 10px 15px;">
		<form method = "POST">
			<div class="row" style="margin-top: 20px">
				<div class="col-md-9">
					<label>ENTER TOKAN NO</label>
					<input required type="number" class="form-control" value = "<?php echo $token_no; ?>" name="token_no" min="1" max="<?php echo intval(next_tokan_no()-1); ?>">
				</div>
				<div class="col-md-3" style="margin-top: 20px">
					<input type="submit" value="SEARCH" name="search" class="btn btn-primary">
				</div>
			</div>
		</form>
	</div>
<?php
if($token_no)
{
    $patient_data = get_patient_phone_by_id($token_no);
    $select_token = "SELECT * FROM `tokans` WHERE id = '$token_no' ";
    $run_token = mysqli_query($con, $select_token);
    if(mysqli_num_rows($run_token) == 1)
    {
        while($row_token = mysqli_fetch_array($run_token))
        {
            $token_created_by = $row_token['user_id'];
            
            $token_created = $row_token['created'];
            $token_date = date_format(date_create($token_created), 'd-M-Y');
        }
    }
?>
    <div class = "col-md-12">
        <table class = "table">
            <caption style = "caption-side: top; color: black;">
                <table class = "table">
                    <tr>
                        <td>NAME</td>
                        <th><u><?php echo $patient_data['name']; ?></u></th>
                        <td>CNIC</td>
                        <th><u><?php echo $patient_data['cnic']; ?></u></th>
                        <td>PHONE NO</td>
                        <th><u><?php echo $patient_data['phone']; ?></u></th>
                    </tr>
                    <tr>
                        <td>AGE</td>
                        <th><u><?php echo $patient_data['age']; ?></u></th>
                        <td>TOKEN BY</td>
                        <th><u><?php echo get_uname_by_id($token_created_by); ?></u></th>
                        <td>DATE</td>
                        <th><u><?php echo $token_date; ?></u></th>
                    </tr>
                    <tr>
                        <td>TOTAL DAYS</td>
                        <th><u><?php echo $sr; ?></u></th>
                        <td>TOTAL AMOUNT</td>
                        <th><u></u></th>
                        <td>TOTAL RECEIVED</td>
                        <th><u></u></th>
                    </tr>
                </table>
            </caption>
            <thead>
                <tr>
                    <th>Sr</th>
                    <th>Date</th>
                    <th>Token No</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
<?php
$sr = 0;
$start = date_format(date_create($token_created), 'Y-m-d');
$today = date('Y-m-d');
$begin = new DateTime('2024-08-01');
$end = new DateTime($today);

$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end);

foreach ($period as $dt) 
{
    $sr++;
?>
                <tr>
                    <td><?php echo $sr; ?></td>
                    <td><?php echo $dt->format("Y-m-d"); ?></td>
                    <td></td>
                    <td></td>
                </tr>
<?php
}
?>
            </tbody>
        </table>
    </div>
<?php
}
?>
</div>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>