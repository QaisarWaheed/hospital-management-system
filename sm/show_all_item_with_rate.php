<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
$filter_quantity = 0;
$category_idds = 0;
?>
	<title>Show Branch Stock - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">

	<div class="row">
<?php
$data = '';
if(isset($_POST['category_idds']) && $_POST['category_idds'] != '')
{
    if($_POST['category_idds'] > 0)
    {
        $category_idds = $_POST['category_idds'];
        $data .= "SELECT * FROM `items` WHERE `category_id` = '$category_idds' AND `status` = '1' ";
        $filter_quantity = $_POST['filter_quantity'];
        if($filter_quantity == 0)
        { 
            $data .= " ";
        }
        elseif($filter_quantity == 1)
        {
            $data .= " AND quantity = '0' ";
        }
        elseif($filter_quantity == 100)
        {
            $data .= " AND quantity >= '1' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 300)
        {
            $data .= " AND quantity >= '101' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 500)
        {
            $data .= " AND quantity >= '301' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 1000)
        {
            $data .= " AND quantity >= '501' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 2000)
        {
            $data .= " AND quantity >= '1001' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 3000)
        {
            $data .= " AND quantity >= '2001' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 4000)
        {
            $data .= " AND quantity >= '3001' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 5000)
        {
            $data .= " AND quantity >= '4001' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 5001)
        {
            $data .= " AND quantity >= '$filter_quantity' ";
        }
        else
        {
            $data .= " AND quantity > '0' AND quantity <= '$filter_quantity' ";
        }
    }
    else
    {
        $data .= "SELECT * FROM `items` WHERE `status` = '1' AND category_id NOT IN (2,3,8,29)  ";
        $filter_quantity = $_POST['filter_quantity'];
        if($filter_quantity == 0)
        { 
            $data .= " ";
        }
        elseif($filter_quantity == 1)
        {
            $data .= " AND quantity = '0' ";
        }
        elseif($filter_quantity == 100)
        {
            $data .= " AND quantity >= '1' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 300)
        {
            $data .= " AND quantity >= '101' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 500)
        {
            $data .= " AND quantity >= '301' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 1000)
        {
            $data .= " AND quantity >= '501' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 2000)
        {
            $data .= " AND quantity >= '1001' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 3000)
        {
            $data .= " AND quantity >= '2001' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 4000)
        {
            $data .= " AND quantity >= '3001' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 5000)
        {
            $data .= " AND quantity >= '4001' AND quantity <= '$filter_quantity' ";
        }
        elseif($filter_quantity == 5001)
        {
            $data .= " AND quantity >= '$filter_quantity' ";
        }
        else
        {
            $data .= " AND quantity > '0' AND quantity <= '$filter_quantity' ";
        }
    }
}
else
{
    $data = "SELECT * FROM `items` WHERE `status` = '1' AND category_id NOT IN (2,3,8,29) ";
}
$select = mysqli_query($con, $data);
?>

			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>			
			<div class="col-md-12" style="text-align: center;">
			</div>
		<table class="table table-bordered table-hover">
			<thead>
				<caption style="caption-side: top;text-align: center;">
					<h3>SHOW ITEMS</h3>
				</caption>                      
				<tr>
					<th colspan="2">
					    <a class = "btn btn-success" href = "dashboard.php">Dashboard</a>
    			    </th>
                    <th>
                        <form method = "POST">
                            <input type = "hidden" name = "filter_quantity" value = "<?php echo $filter_quantity; ?>" />
                        <select class = "form-control bg-light" onchange="this.form.submit()" name="category_idds">
                        <option <?php if($category_idds == 0){echo " SELECTED ";} ?> value = "0">ALL</option>
                        <?php
                            $select_category = "SELECT * FROM categories WHERE id NOT IN (2,3,8, 29) AND status = '1' ORDER BY name ";
                            $run_category = mysqli_query($con, $select_category);
                            if(mysqli_num_rows($run_category) > 0)
                            {
                                while($row_category = mysqli_fetch_array($run_category))
                                {
                                $category_id = $row_category['id'];
                                $category_title = $row_category['name'];
                                if($category_id == $category_idds)
                                {
                                echo '<option SELECTED value = "'.$category_id.'" >'.$category_title.'</option>';
                                }
                            else
                            {
                            echo '<option value = "'.$category_id.'" >'.$category_title.'</option>';
                            }
                            }
                            }
                        ?>
                        </select>
                        </form>
                    </th>
                    <th>
                        <form method = "POST">
                            <input type = "hidden" name = "category_idds" value = "<?php echo $category_idds; ?>" />
                        <select class = "form-control bg-light" onchange="this.form.submit()" name="filter_quantity">
                        <option <?php if($filter_quantity == 0){echo " SELECTED ";} ?> value = "0">ALL</option>
                        <option <?php if($filter_quantity == 1){echo " SELECTED ";} ?>  value = "1"> 0 </option>
                        <option <?php if($filter_quantity == 100){echo " SELECTED ";} ?>  value = "100">1 - 100</option>
                        <option <?php if($filter_quantity == 300){echo " SELECTED ";} ?> value = "300">101 - 300</option>
                        <option <?php if($filter_quantity == 500){echo " SELECTED ";} ?> value = "500">301 - 500</option>
                        <option <?php if($filter_quantity == 1000){echo " SELECTED ";} ?> value = "1000">501 - 1000</option>
                        <option <?php if($filter_quantity == 2000){echo " SELECTED ";} ?> value = "2000">1001 - 2000</option>
                        <option <?php if($filter_quantity == 3000){echo " SELECTED ";} ?> value = "3000">2001 - 3000</option>
                        <option <?php if($filter_quantity == 4000){echo " SELECTED ";} ?> value = "4000">3001 - 4000</option>
                        <option <?php if($filter_quantity == 5000){echo " SELECTED ";} ?> value = "5000">4001 - 5000</option>
                        <option <?php if($filter_quantity == 5001){echo " SELECTED ";} ?> value = "5001">Above 5000</option>
                        </select>
                        </form>
                    </th>
                </tr>
				<tr>
					<th>S #</th>
					<th>Item Name</th>
					<th>Category</th>
					<th>Poor</th>
					<th>Member</th>
					<th>General</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
<?php
$s = 0;
if (mysqli_num_rows($select) > 0) 
{
	while ($row = mysqli_fetch_array($select)) 
	{
		$s = $s + 1;
		$id = $row['id'];
		$quantity = $row['quantity'];
		$item_name = $row['name'];
		$category_name = show_category_name($row['category_id']);
		$poor = $row['poor'];
		$member = $row['member'];
		$general = $row['general'];

echo '
<tr>
	<td id = "id'.$id.'">'.$s.'</td>
	<td>'.$item_name.'</td>
	<td>'.$category_name.'</td>
	<td>'.$poor.'</td>
	<td>'.$member.'</td>
	<td>'.$general.'</td>
	<td>
    	<a href="update_store_item.php?id='.$id.'" class="btn btn-success btn-sm">UPDATE</a>
	</td>
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
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<!-- 
 -->