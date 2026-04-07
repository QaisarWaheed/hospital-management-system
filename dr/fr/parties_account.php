<?php include 'includes/connect.php'; ?>
<?php include 'includes/head.php'; 
if(!isset($_SESSION['fr_id']))
{
    header('location: logout.php');
}
?>
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

	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12 noprint" style="text-align: center;background: lightgreen;">
		<label><h1><?php echo $company_name; ?> </h1></label>
        <?php include 'navigation_top.php'; ?>
	</div>

	<div class="col-md-12">

	    <table class = "table table-bordered">
	        <caption class = "h2" style = "caption-side: top;text-align: center;">
	            PARTIES LG
	        </caption>
	        <thead>
	            <!--<tr class = "noprint">-->
	            <!--    <form>-->
	            <!--    <th colspan = "10">-->
	            <!--        <div class = "row">-->
	            <!--            <div class = "col-md-2 " style  = "text-align: right;">-->
	            <!--                <label for = "from_date">From Date:</label>-->
	            <!--            </div>-->
	            <!--            <div class = "col-md-3">-->
	            <!--                <input type = "hidden" value = "<?php echo $br_id; ?>" name = "br_id" id = "br_id" required />-->
	            <!--                <input type = "date" name = "from_date" id = "from_date" class = "form-control"required />-->
	            <!--            </div>-->
	            <!--            <div class = "col-md-2" style  = "text-align: right;">-->
	            <!--                <label for = "to_date">To Date:</label>-->
	            <!--            </div>-->
	            <!--            <div class = "col-md-3">-->
	            <!--                <input type = "date" name = "to_date" id = "to_date" class = "form-control" required />-->
	            <!--            </div>-->
	            <!--            <div class = "col-md-2" style  = "text-align: center;">-->
	            <!--                <input type = "submit" value = "SEARCH" name = "submit" style  = "min-width: 100%;min-height: 100%;" id = "submit" class = "btn btn-sm btn-info" />-->
	            <!--            </div>-->
	            <!--        </div>-->
	            <!--    </th>-->
	            <!--    </form>-->
             <!--   </tr>-->
	            <tr>
	                <th>S #</th>
	                <th class ="noprint" title = "PARTY ACCOUNT ID">Id</th>
	                <th>Name</th>
	                <th>Phone</th>
	                <th>CNIC</th>
	                <th>Address</th>
	                <th>CR</th>
	                <th>DR</th>
	                <th>Total Amount</th>
	                <th>ACTION</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 0;
$total_cr = 0;
$total_dr = 0;
$total_balance = 0;
$select_party = "SELECT * FROM `parties_account`";
$run_party = mysqli_query($con, $select_party);
if(mysqli_num_rows($run_party) > 0)
{
    while($row_party = mysqli_fetch_array($run_party))
    {
        $id = $row_party['id'];
        $name = $row_party['name'];
        $phone = $row_party['phone'];
        $cnic = $row_party['cnic'];
        $address = $row_party['address'];
        $cr = $row_party['cr'];
        $total_cr = $total_cr + $cr;
        $dr = $row_party['dr'];
        $total_dr = $total_dr + $dr;
        $balance = $row_party['balance'];
        $total_balance = $total_balance + $balance; ?>
	            <tr>
	                <th><?php echo ++$s; ?></th>
	                <th class ="noprint"><?php echo $id; ?></th>
	                <th><?php echo $name; ?></th>
	                <th><?php echo $phone; ?></th>
	                <th><?php echo $cnic; ?></th>
	                <th><?php echo $address; ?></th>
	                <th><?php echo $cr; ?></th>
	                <th><?php echo $dr; ?></th>
	                <th><?php echo $balance; ?></th>
	                <th>ACTION</th>
	            </tr>
<?php    }
}
?>
                <tr style = "font-size: 22px;text-align: center;">
                    <th colspan = "6">GRAND TOTAL</th>
	                <th><?php echo $total_cr; ?></th>
	                <th><?php echo $total_dr; ?></th>
	                <th><?php echo $total_balance; ?></th>
                    <th></th>
                </tr>
	        </tbody>
	    </table>
	</div>
			
	</div>
</div>

</body>
</html>