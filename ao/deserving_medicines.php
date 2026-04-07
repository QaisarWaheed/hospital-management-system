<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 

$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if(mysqli_num_rows($run_roles) == 1)
{
    while($row_role = mysqli_fetch_array($run_roles))
    {
        $role_title = $row_role['title'];
    }
}
else
{
    $role_title = '';
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
<script type='text/javascript'>
  window.smartlook||(function(d) {
    var o=smartlook=function(){ o.api.push(arguments)},h=d.getElementsByTagName('head')[0];
    var c=d.createElement('script');o.api=new Array();c.async=true;c.type='text/javascript';
    c.charset='utf-8';c.src='https://web-sdk.smartlook.com/recorder.js';h.appendChild(c);
    })(document);
    smartlook('init', 'd4597d443ca87604b4d1a87b1abb09996486284b', { region: 'eu' });
</script>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name?> </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke" style = "text-transform: uppercase;">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
    <div class = "row">
        <table class = "table ">
            <tread>
                <form method = "GET">
                <tr>
                    <th>FROM</th>
                    <th><input required type = "date" name = "from_date" value = "<?php if(isset($_GET['from_date'])){echo $_GET['from_date'];}else{echo date('Y-m-d');} ?>" id = "from_date" class = "form-control" /></th>
                    <th>TO</th>
                    <th><input required type = "date" name = "to_date" value = "<?php if(isset($_GET['to_date'])){echo $_GET['to_date'];}else{echo date('Y-m-d');} ?>" id = "to_date" class = "form-control" /></th>
                    <th colspan = "2" style = "min-width: 100%;"><input type = "submit" value = "SEARCH" name = "search" class = "btn btn-info"></th>
                </tr>
                </form>
                <tr>
                    <th>SR</th>
                    <th>DATE</th>
                    <th>ITEM NAME</th>
                    <th>SECTION NAME</th>
                    <th>QUANTITY</th>
                    <th>USER</th>
                </tr>
            </tread>
            <tbody>
<?php
$sr = 0;
if(isset($_GET['search']))
{
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];
}
else
{
    $from_date = $to_date = date('Y-m-d');
}
$select = "SELECT * FROM `deserving_medicine_used` INNER JOIN users ON deserving_medicine_used.user_id = users.id WHERE deserving_medicine_used.branch_id = '$branch_id' AND deserving_medicine_used.status = '1' AND`deserving_medicine_used`.`date` >= '$from_date' AND`deserving_medicine_used`.`date` <= '$to_date' ORDER BY `deserving_medicine_used`.`date` DESC ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $deserving_medicine_name = get_item_name_by_register_item_id($row['deserving_medicine_id']);
        $date = $row['date'];
        $quantity = $row['quantity'];
        $section_name = $row['section_name'];
        $u_name = $row['u_name'];
        $sr = $sr + 1;
        echo '
        <tr>
            <td>'.$sr.'</td>
            <td>'.$date.'</td>
            <td>'.$deserving_medicine_name.'</td>
            <td>'.$section_name.'</td>
            <td>'.$quantity.'</td>
            <td>'.$u_name.'</td>
            <td></td>
        </tr>
        ';
    }
}
?>
            </tbody>
            <caption style = "caption-side: top; color: black;text-align: center;"><h2><?php echo $branch_address; ?></h2></caption>
        </table>
    </div>
	</div>
</div>

</body>
</html>