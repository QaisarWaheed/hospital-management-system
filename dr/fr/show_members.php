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

	<title>MEMBER DATA - <?php echo $company_trademark; ?></title>
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
	            MEMBER's LG
	        </caption>
	        <thead>
	            <form method = "GET">
    	            <tr>
    	                <th colspan = "7"><input class = "form-control" type = "text" placeholder = "ENTER SEARCHING STRING..." name = "search_member_record" id = "search_member_record" maxlength = "50" /></th>
    	                <th colspan = "2"><input type = "submit" value = "SEARCH MEMBER" class = "btn-info" name = "search" /></th>
    	            </tr>
	            </form>
	            <tr>
	                <th class ="noprint">S#</th>
	                <th class ="noprint" title = "MEMBER ACCOUNT ID">Id</th>
	                <th>Name</th>
	                <th>Phone</th>
	                <th>Occupation</th>
	                <th>Referance</th>
	                <th>Start Date</th>
	                <th>Amount</th>
	                <th>ACTION</th>
	            </tr>
	        </thead>
	        <tbody>
<?php
$s = 1;
$total_cr = 0;
$total_dr = 0;
$total_balance = 0;
if(isset($_GET['search_member_record']) && $_GET['search_member_record'] != '')
{
    $search_member_record = $_GET['search_member_record'];
    $select_member = "SELECT * FROM `members` WHERE CONCAT(`member_name`,`member_phone`,`member_occupation`,`referance_name`,`member_monthly_donation`,`member_start_date_donation`) LIKE '%$search_member_record%' AND `member_status` = '1' ";
}
else
{
    $select_member = "SELECT * FROM `members` WHERE `member_status` = '1' ";
}
$run_member = mysqli_query($con, $select_member);
if(mysqli_num_rows($run_member) > 0)
{
    while($row_member = mysqli_fetch_array($run_member))
    {
        $member_id = $row_member['member_id'];
        $member_amount = $row_member['6'];
        $total_balance = $total_balance + $member_amount; ?>
	            <tr>
	                <th class ="noprint"><?php echo $s++; ?></th>
	                <th class ="noprint"><?php echo $member_id; ?></th>
	                <th><?php echo $row_member['1']; ?></th>
	                <th><?php echo $row_member['2']; ?></th>
	                <th><?php echo $row_member['3']; ?></th>
	                <th><?php echo $row_member['5']; ?></th>
	                <th><?php echo date_format(date_create($row_member['7']), "d-M-Y"); ?></th>
	                <th><?php echo $row_member['6']; ?></th>
	                <th><a href = "show_members.php?member_id=<?php echo $member_id; ?>"></a></th>
	            </tr>
<?php    }
}
?>
                <!--<tr style = "font-size: 22px;text-align: center;">-->
                <!--    <th colspan = "6">GRAND TOTAL</th>-->
	               <!-- <th><?php echo $total_cr; ?></th>-->
	               <!-- <th><?php echo $total_dr; ?></th>-->
	               <!-- <th><?php echo $total_balance; ?></th>-->
                <!--    <th></th>-->
                <!--</tr>-->
	        </tbody>
	    </table>
	</div>
			
	</div>
</div>

</body>
</html>