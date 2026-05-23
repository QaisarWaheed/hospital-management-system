<?php 
include 'includes/connect.php';
include 'includes/head.php'; 
$today = date('Y-m-d');

if (isset($_GET['select_month']) && $_GET['select_month'] != ''&& $_GET['select_month'] != '0')
{
    $select_month = $_GET['select_month'];
    $months = '+'.$select_month.' months';
}
else
{
    $select_month = 0;
    $months = '+6 months';
}
$effectiveDate = $today;
$last_day = date('Y-m-d', strtotime($effectiveDate . ' ' . $months) );
$last_date = date('d-m-Y', strtotime($effectiveDate . ' ' . $months) );
if (isset($_GET['category_idds']) && $_GET['category_idds'] != ''&& $_GET['category_idds'] != '0') 
{
$select_value = $_GET['category_idds'];
$select = "SELECT * FROM `purchase_items` WHERE expiry_date > '$today' AND expiry_date < '$last_day' AND item_id IN (SELECT id FROM items WHERE category_id = '$select_value') ORDER BY `purchase_items`.`expiry_date` ASC ";
}
else
{
$select_value = 0;
$select = "SELECT * FROM `purchase_items` WHERE expiry_date > '$today' AND expiry_date < '$last_day' ORDER BY `purchase_items`.`expiry_date` ASC ";
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
<style>
@page 
{
  size: A4;
  margin: 10px 0px 10px 0px;
}
@media print 
{
html, body 
{
    width: 210mm;
    height: 297mm;
    font-size: 9px;
}
.noprint
{
    display: none;
}
}    
</style>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke noprint">
		<?php include 'left_navigation.php'; ?>
		<h3 style="margin-top: 350px;text-align: center;">USER: <?php echo $_SESSION['sm_name']; ?></h3>
	</div>
	<div class="col-md-9">
<?php
?>
	    <div class = "row">
	        <div class = "col-md-12">
	            <div class = "table">
	                <table class = "table">
	                    <caption style = "caption-side: top;text-align: center;">
	                        <h1>NEAR EXIPRE MEDICINE LIST(Till <?php echo $last_date; ?>)</h1>
	                    </caption>
	                    <thead>
	                        <tr class = "noprint">
	                            <th colspan = "2"></th>
	                            <th>
	                               <form>
	                                   <input type = "hidden" name = "select_month" value = "<?php echo $select_month; ?>" />
	                                   <select class = "form-control bg-light" onchange="this.form.submit()" name="category_idds">
	                                       <option <?php if($select_value == 0){echo " SELECTED ";} ?> value = "0">ALL</option>
	                               <?php
	                               $select_category = "SELECT * FROM categories WHERE id NOT IN (2,3,8) AND status = '1' ORDER BY name ";
	                               $run_category = mysqli_query($con, $select_category);
	                               if(mysqli_num_rows($run_category) > 0)
	                               {
	                                   while($row_category = mysqli_fetch_array($run_category))
	                                   {
    	                                   $category_id = $row_category['id'];
    	                                   $category_title = $row_category['name'];
    	                                   if($category_id == $select_value)
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
	                            <th></th>
	                            <th></th>
	                            <th>
	                               <form>
	                                   <input type = "hidden" name = "category_idds" value = "<?php echo $select_value; ?>" />
	                                   <select class = "form-control bg-danger" onchange="this.form.submit()" name="select_month">
	                                       <option <?php if($select_month == 0){echo " SELECTED ";} ?> value = "0">ALL(6)</option>
	                                       <option <?php if($select_month == 1){echo " SELECTED ";} ?> value = "1">1</option>
	                                       <option <?php if($select_month == 2){echo " SELECTED ";} ?> value = "2">2</option>
	                                       <option <?php if($select_month == 3){echo " SELECTED ";} ?> value = "3">3</option>
	                                       <option <?php if($select_month == 4){echo " SELECTED ";} ?> value = "4">4</option>
	                                       <option <?php if($select_month == 5){echo " SELECTED ";} ?> value = "5">5</option>
	                               </select>
	                               </form>
	                            </th>
	                        </tr>
	                        <tr>
	                            <th>Sr. No</th>
	                            <th>Item Name</th>
	                            <th>Category</th>
	                            <th>Batch No</th>
	                            <th>Pur. Qty</th>
	                            <th>Exipy Date</th>
	                        </tr>
	                    </thead>
	                    <tbody>
<?php
$s = 0;
// $select = "SELECT * FROM `purchase_items` WHERE expiry_date > '$today' AND expiry_date < '$last_day' ORDER BY `purchase_items`.`expiry_date` ASC";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    { 
        $expiry_date = $row['expiry_date'];
        $batch_no = $row['batch_no'];
        $quantity = $row['quantity'];
        $item_id = $row['item_id'];
        $category = show_category_name_by_item_id($item_id);
        $item_name = get_item_name_by_id($row['item_id']);
            $s = $s + 1;
        ?>
                            <tr>
                                <td><?php echo $s; ?></td>
                                <td><?php echo $item_name; ?></td>
                                <td><?php echo $category; ?></td>
                                <td><?php echo $batch_no; ?></td>
                                <td style = "text-align: center;"><?php echo $quantity; ?></td>
                                <td><?php echo date_format(date_create($expiry_date), "d-m-Y"); ?></td>
                            </tr>
        
<?php    
    }
}
?>
	                    </tbody>
	                </table>
	            </div>
	        </div>
	    </div>  
	</div>
			
	</div>
</div>

</body>
</html>