<?php include 'includes/connect.php';
$from = date('Y-m-d');
$to = date('Y-m-d');
$item_id = '';
$br_id = (int) $branch_id;
$end_date = date('Y-m-d 23:59:59');
if(isset($_POST['item_id']) && isset($_POST['from']) && $_POST['item_id'] != '')
{
    $item_id = $_POST['item_id'];
    $br_id = (int) $_POST['br_id'];
    $from = (string) $_POST['from'];
    $to = (string) $_POST['to'];
    $end_date = date_format(date_create($to), "Y-m-d 23:59:59");
}
?>
<?php include 'includes/head.php'; ?>
	<title>ISSUANCE DETAIL FROM <?php echo htmlspecialchars($from, ENT_QUOTES, 'UTF-8'); ?> TO <?php echo htmlspecialchars($to, ENT_QUOTES, 'UTF-8'); ?> - <?php echo $company_trademark; ?></title>
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src= "https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>	
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
	<div class="col-md-12 background_whitesmoke noprint">
		<?php include 'top_row.php'; ?>
	</div>
	<div class="col-md-12">
    	<div style="" class = "noprint">
    	    <?php ?>
    	    <form METHOD="POST">
    	        <div class="row">
    	            <div class="col-md-3">
    	                <h5 align="center">SELECT BRANCH</h5>
    	            </div>
    	            <div class="col-md-3">
    	                <h5 align="center">SELECT ITEM</h5>
    	            </div>
    	            <div class="col-md-2">
    	                <h5 align="center">FROM</h5>
    	            </div>
    	            <div class="col-md-2">
    	                <h5 align="center">TO</h5>
    	            </div>
    	            <div class="col-md-2">
    	                <h5 align="center">ACTION</h5>
    	            </div>
    	            <div class="col-md-3">
                        <select required class="form-control" size = "1" style="min-width: 200px;text-transform: uppercase;" name="br_id">
                            <option value = "0">ALL</option>
                        <?php 
                        $branch = "SELECT * FROM branchs WHERE id != 0 AND status = 1 ORDER BY `address` ASC ";
                        $run_branch = mysqli_query($con, $branch);
                        if (mysqli_num_rows($run_branch) > 0) 
                        {
                        while ($row_branch = mysqli_fetch_array($run_branch)) 
                            {
                                $row_branch_id = $row_branch['id'];
                            echo '<option ';if($br_id == $row_branch_id){echo " SELECTED ";} echo ' value="'.$row_branch['id'].'">'.$row_branch['address'].'</option>';
                            }
                        }
                        else
                        {
                        echo '<option value="">Add Branch Data</option>';
                        }
                        ?>
                        </select>
    	            </div>
    	            <div class="col-md-3">
                        <select class="form-control" size = "1" style="min-width: 200px;text-transform: uppercase;" name="item_id">
                        <?php 
                        $item = "SELECT * FROM items WHERE status = 1 ORDER BY `name` ";
                        $run_item = mysqli_query($con, $item);
                        if (mysqli_num_rows($run_item) > 0) 
                        {
                        while ($row_item = mysqli_fetch_array($run_item)) 
                            {
                                $row_item_id = $row_item['id'];
                            echo '<option ';if($item_id == $row_item_id){echo " SELECTED ";} echo ' value="'.$row_item['id'].'">'.$row_item['name'].'</option>';
                            }
                        }
                        else
                        {
                        echo '<option value="">Add Items Data</option>';
                        }
                        ?>
                        </select>
    	            </div>
    	            <div class="col-md-2">
    	                <input type="date" value = "<?php echo $from; ?>" name="from" class="form-control" required />
    	            </div>
    	            <div class="col-md-2">
    	                <input type="date" value = "<?php echo $to; ?>" name="to" class="form-control" required />
    	            </div>
    	            <div class="col-md-2">
    	                <input type="submit" value="SEARCH" name="token" class="btn btn-info btn-sm" />
    	            </div>
    	        </div>
    	    </form>
    	</div>
<?php 
if(isset($_POST['item_id']) && isset($_POST['from']) && $_POST['item_id'] != '')
{
    header("Content-Type: text/plain");
    $item_id = $_POST['item_id'];
    $br_id = $_POST['br_id'];
    $from = $_POST['from'];
    $to = $_POST['to'];
    $end_date = date_format(date_create($to), "Y-m-d 23:59:59");
?>
        <div class=""> 
        <div id="divID">
            <table class = "table">
                <thead>
                    <tr>
                        <th>S#</th>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Issua No</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $s = 0;
                    $total = 0;
                    if($br_id != '0')
                    {
                        $select_token = "SELECT item_register_branchs_by_sm.created,item_register_branchs_by_sm.issue_id, branchs.tag_name,items.name, item_register_branchs_by_sm.quantity FROM `item_register_branchs_by_sm` INNER JOIN item_register_to_branches ON item_register_branchs_by_sm.branch_item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN branchs ON item_register_branchs_by_sm.branch_id = branchs.id WHERE item_register_branchs_by_sm.created >= '$from' AND item_register_branchs_by_sm.created < '$end_date' AND item_register_branchs_by_sm.branch_id = '$br_id' AND `branch_item_id` IN (SELECT id FROM item_register_to_branches WHERE item_register_to_branches.item_id = '$item_id') ORDER BY `item_register_branchs_by_sm`.`created` ASC";
                    }
                    else
                    {
                        $select_token = "SELECT item_register_branchs_by_sm.created,item_register_branchs_by_sm.issue_id, branchs.tag_name,items.name, item_register_branchs_by_sm.quantity FROM `item_register_branchs_by_sm` INNER JOIN item_register_to_branches ON item_register_branchs_by_sm.branch_item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN branchs ON item_register_branchs_by_sm.branch_id = branchs.id WHERE item_register_branchs_by_sm.created >= '$from' AND item_register_branchs_by_sm.created < '$end_date' AND `branch_item_id` IN (SELECT id FROM item_register_to_branches WHERE item_register_to_branches.item_id = '$item_id') ORDER BY `item_register_branchs_by_sm`.`created` ASC";
                    }
                    $run_token = mysqli_query($con, $select_token);
                    if(mysqli_num_rows($run_token) > 0)
                    {
                    while($row_token = mysqli_fetch_array($run_token))
                    {
                        $total = $total + $row_token['4'];
                        $item_name = $row_token['3'];
                        $s++;
                    echo '
                    <tr>
                        <td>'.$s.'</td>
                        <td>'.date_format(date_create($row_token['0']), "d-M-Y").'</td>
                        <td>'.$row_token['2'].'</td>
                        <td>'.$row_token['1'].'</td>
                        <td>'.$row_token['4'].'</td>
                    <tr>
                    ';
                    }
                    }
                    else
                    {
                    echo '
                    <tr>
                        <td colspan = "7">'.$con->error.'</td>
                    <tr>
                    ';
                    }
                    ?>
                    <tr>
                        <th style = "text-align: right;" colspan = "4">Total Issunce Quanitity</th>
                        <th><?php echo $total; ?></th>
                    </tr>
                </tbody>
                <caption style = "color: black; caption-side: top; text-align: center;">
                    <h2><?php echo $item_name; ?> </h2>
                    <h3>ISSUANCE DETAIL FROM <?php echo $_POST['from']; ?> TO <?php echo $_POST['to']; ?> </h3>
                    <?php if($br_id == 0){ echo '<h3>ALL BRANCHES</h3>';}else{echo '<h3>'.get_branch_name_by($br_id).'</h3>';} ?>
                </caption>
            </table>
        </div>
        </div>
<?php
}
?>
	</div>
</div>

</body>
</html>    