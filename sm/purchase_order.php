<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 
if(isset($_POST['category_idds']) && $_POST['category_idds'] > 0)
{
    $category_idds = $_POST['category_idds'];
    $query = "SELECT items.`id`,items.`name`,categories.name AS category,items.`quantity`, min_limit, max_limit FROM `items` INNER JOIN categories ON items.category_id = categories.id WHERE items.`status` = '1' AND category_id = '$category_idds' ";
}
else
{
    $category_idds = 0;
    $query = "SELECT items.`id`,items.`name`,categories.name AS category,items.`quantity`, min_limit, max_limit FROM `items` INNER JOIN categories ON items.category_id = categories.id WHERE items.`status` = '1' AND category_id = '0' ";
}
?>
	<title>PURCHASE ORDER - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo">
<div>
	<div class="" style="margin: 10px 15px;">
    	<div class="row">
			<div class="col-md-12 noprint" style="text-align: center;">
			    <?php include 'top_row.php'; ?>
			</div>	
		</div>
		<table class="table table-bordered table-hover">
			<thead>
				<caption style="caption-side: top;text-align: center;color: black;">
					<h3>CONSUMING &  AVAILALLE STOCK</h3>
				</caption>                      
				<tr>
					<th colspan="2">
					    <a class = "btn btn-success" href = "dashboard.php">Dashboard</a>
    			    </th>
                    <th>
                        <form method = "POST">
                        <select class = "form-control bg-light" onchange="this.form.submit()" name="category_idds">
                        <option <?php if($category_idds == 0){echo " SELECTED ";} ?> value = "0">NO</option>
                        <?php
                            $select_category = "SELECT * FROM categories WHERE id NOT IN (2, 3, 8, 29) AND status = '1' ORDER BY name ";
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
                    </th>
                </tr>
				<tr>
					<th>S #</th>
					<th>Item Name</th>
					<th>Category</th>
					<th>Store Limits</th>
					<th>Store Stock</th>
					<th>Branches Stock</th>
					<th>TOTAL Stock</th>
				</tr>
			</thead>
			<tbody>
			    <?php
			    $s = 0;
			    $run = mysqli_query($con, $query);
			    if(mysqli_num_rows($run) > 0)
			    {
			        while($row = mysqli_fetch_array($run))
			        {
			            $s++;
			            $item_id = $row['id'];
			            $branchs_stock = intval(get_all_branchs_stock($item_id));
			             ?>
		        <tr>
		            <td><?php echo $s; ?></td>
		            <td><?php echo $row['name']; ?></td>
		            <td><?php echo $row['category']; ?></td>
		            <td><?php echo $row['min_limit'].' - '.$row['max_limit']; ?></td>
		            <td><?php echo $row['quantity']; ?></td>
		            <td><?php echo $branchs_stock; ?></td>
		            <td><?php echo $row['quantity']+$branchs_stock; ?></td>
		        </tr>
			     <?php }
			    }
			    ?>
			</tbody>
		</table>
    </div>
</div>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>