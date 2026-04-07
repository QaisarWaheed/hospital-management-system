<?php 
include 'includes/connect.php'; 
?>
<?php include 'includes/head.php'; ?>
	<title> CHECK STORE STOCK - <?php echo $company_trademark; ?></title>
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

<body class="background_image_ycdo">

<div>

	<div class="" style="margin: 10px 15px;">
	    <?php include 'navigation_top.php'; ?>
		<div class="row">

			<div class="col-md-12 noprint" style="text-align: center;">
				<label><h1>SELECT ITEM'S</h1></label>
			</div>
			<div class="col-md-12 ">
                <form class="noprint" method = "POST" action = "check_store_sctock.php">
                    <div class = "row">
                        <div class = "col-md-2" style = "text-align: right;">
                            <label class = "label h3" for = "audit_id">Item</label>
                        </div>
                        <div class = "col-md-8">
                            <select id="item_id" name="item_id" required class="form-control">
                            <?php    
                            $select = "SELECT * FROM `items` WHERE `status`= '1' ";
                            $run = mysqli_query($con, $select);
                            if(mysqli_num_rows($run) > 0)
                            { 
                                while($row = mysqli_fetch_array($run))
                                {  
                                    echo '<option value="'.$row['id'].'">'.$row['name'].'</option>'; 
                                } 
                            } 
                            ?>
                            </select>
                        </div>
                        <div class = "col-md-2">
                            <button type="submit" name="search" class=" btn btn-primary" style = "min-width: 100%;">SEARCH</button>  
                        </div>
                    </div>
                </form>
			</div>

			<div class="col-md-12" style="text-align: center;">
				<label><h1 style = "text-decoration: underline;">ITEM DETAIL</h1></label>
				<?php
				if(isset($_POST['search']) && $_POST['item_id'] != '')
				{
				    $item = "SELECT * FROM items WHERE id = '".$_POS['item_id']."' ";
				    $run = mysqli_query($con, $item);
				    if(mysql_num_rows($run) == 1)
				    {
				        while($row = mysqli_fetch_array($run))
				        {
				            $item_name = $row['name'];
				        }
				    }
				} ?>
			</div>


		</div>

	</div>

</div>

</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<?php mysqli_close($con); ?>