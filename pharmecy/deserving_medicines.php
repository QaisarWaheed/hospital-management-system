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
if(isset($_POST['submit']) && $_POST['quantity'] > 0)
{
    $branch_item_id = $_POST['branch_item_id'];
    $date = $_POST['date'];
    $quantity = $_POST['quantity'];
    $section_name = $_POST['section_name'];
    $insert = "INSERT INTO `deserving_medicine_used`( `deserving_medicine_id`, `date`, `quantity`, `branch_id`, `user_id`, `status`, `created`, `section_name`) 
    VALUES ('$branch_item_id', '$date', '$quantity', '$branch_id', '$user_id', '1', '$current_date', '$section_name')";
    if(mysqli_query($con, $insert))
    {
        $get_quantity = get_register_item_quantity_from_item_id($branch_item_id) - $quantity;
        $update = "UPDATE `item_register_to_branches` SET `quantity` = '$get_quantity' WHERE `id` = '$branch_item_id' ";
        mysqli_query($con, $update);
        // echo $update;
        // exit(0);
    }
    else
    {
        echo $con->error;
        exit(0);
    }
    
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
    <div class = "container">
        <form method ="POST">
          <div class="row">
            <div class="col">
	            <label>DATE</label>
	            <input readonly type = "date" value = "<?php echo date('Y-m-d'); ?>" name = "date" id = "date" class = "form-control" />
            </div>
            <div class="col">
	            <label>SELECT ITEM</label>
	            <select name = "branch_item_id" class = "form-control">
                <?php
                $select_deserving = "SELECT * FROM item_register_to_branches WHERE branch_id = '$branch_id' AND item_id IN (SELECT item_id FROM deserving_medicine_list WHERE status = '1')";
                $run = mysqli_query($con, $select_deserving);
                if(mysqli_num_rows($run) > 0)
                {
                    while($row = mysqli_fetch_array($run))
                    {
                        $id = $row['id'];
                        $item_id = $row['item_id'];
                        $item_name = get_item_name_by_id($item_id);
                        echo '<option value = "'.$id.'">'.$item_name.'</option>';
                    }
                }
                else
                {
                    echo '<option value = ""> NO ITEM ALLOWED FOR DESERVING</option>';
                }
                ?>
	            </select>
            </div>
            <div class="col">
	            <label>QUANTITY</label>
	            <input type = "number" value = "0" min = "1" name = "quantity" id = "quantity" class = "form-control" />
            </div>
            <div class="col">
	            <label for = "section_name">SECTION</label>
	            <input type = "text" name = "section_name" id = "section_name" class = "form-control" />
            </div>
            <div class="col">
                <div style = "margin-top: 35px;">
                    <input type = "submit" value = "SAVE" name = "submit" id = "submit" class = "btn btn-sm btn-info" />
                    <input type = "reset" value = "CLEAR" name = "reset" id = "clear" class = "btn btn-sm btn-danger" />
                </div>
            </div>
            </div>
        </form>
    </div>
    <div class = "row">
        <table class = "table ">
            <tread>
                <tr>
                    <th>SR</th>
                    <th>DATE</th>
                    <th>ITEM NAME</th>
                    <th>SECTION NAME</th>
                    <th>QUANTITY</th>
                    <th></th>
                </tr>
            </tread>
            <tbody>
<?php
$sr = 0;
$created = date('Y-m-d');
$select = "SELECT * FROM `deserving_medicine_used` WHERE `branch_id` = '$branch_id' AND status = '1' AND `date` LIKE '$created' ";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $id = $row['id'];
        $deserving_medicine_name = get_item_name_by_register_item_id($row['deserving_medicine_id']);
        $date = $row['date'];
        $quantity = $row['quantity'];
        $section_name = $row['section_name'];
        $sr = $sr + 1;
        echo '
        <tr>
            <td>'.$sr.'</td>
            <td>'.$date.'</td>
            <td>'.$deserving_medicine_name.'</td>
            <td>'.$section_name.'</td>
            <td>'.$quantity.'</td>
            <td></td>
        </tr>
        ';
    }
}
?>
            </tbody>
        </table>
    </div>
	</div>
</div>

</body>
</html>