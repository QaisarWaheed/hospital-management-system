<?php
// OPTIMIZED: replaced per-row queries with pre-aggregated batch queries
include 'includes/connect.php';
require_once __DIR__ . '/../bk/includes/progress_report_params.php';
?>
<?php include 'includes/head.php'; 
if(!isset($_SESSION['hr_id']))
{
    header('location: logout.php');
}
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO </h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	    <form>
	        <div class = "row">
	            <div class = "col-md-12">
	                <label>DATE</label>
	                <input class = "form-control" value = "<?php if(isset($_GET['summery_month']) && $_GET['summery_month'] != ''){echo $_GET['summery_month']; }else{echo date('Y-m');} ?>" type = "month" name = "summery_month" />
	            </div>
	            <div class = "col-md-12">
	                <input class = "btn btn-sm btn-info" value = "GENERATE SUMMERY" type = "submit" name = "generate_summery_monthly" />
	            </div>
	        </div>
	    </form>
	    <?php if(isset($_GET['summery_month']) && $_GET['summery_month'] != ''){ ?>
	    <div class = "row">
	        <div class = "col-md-12">
	            <div>
	                <table class = "table table-hover table-bordered border-dark">
	                    <caption style = "caption-side: top; color: black;">
	                        <div class = "h4 text-center">GYNAE REGISTERATION SUMMERY FOR THE MONTH OF <?php echo date_format(date_create($_GET['summery_month']), "M-Y") ?></div>
	                    </caption>
	                    <thead>
	                        <tr>
	                            <th>S #</th>
	                            <th>BRANCH </th>
	                            <th>CURRENT MONTH REGISTERATION</th>
	                        </tr>
	                    </thead>
	                    <tbody>
                        <?php 
                        $s = 0;
                        $total = 0;
                        $summery_month = mysqli_real_escape_string($con, (string) $_GET['summery_month']);
                        $summery_range = progress_month_date_range($summery_month);
                        $summery_start = mysqli_real_escape_string($con, $summery_range['start_date'] . ' 00:00:00');
                        $summery_end = mysqli_real_escape_string($con, $summery_range['end_date'] . ' 00:00:00');
                        $select = "SELECT DISTINCT gynae_register.branch_id, branchs.tag_name, COUNT(token_no) AS total_register FROM `gynae_register` INNER JOIN branchs ON gynae_register.branch_id = branchs.id WHERE gynae_register.created >= '$summery_start' AND gynae_register.created < '$summery_end' GROUP BY branch_id ";
                        $run = mysqli_query($con, $select);
                        if(mysqli_num_rows($run) > 0)
                        {
                           while($row = mysqli_fetch_array($run))
                           {
                               $total = $total + $row['total_register'];
                               $s++; ?>
                            <tr>
                                <td><?php echo $s; ?></td>
                                <td><?php echo $row['tag_name']; ?></td>
                                <td><?php echo $row['total_register']; ?></td>
                            </tr>       
                           <?php }
                        } ?>
	                    </tbody>
	                    <tfoot>
	                        <tr>
	                            <th colspan = "2"></th>
	                            <th><?php echo $total; ?></th>
	                        </tr>
	                    </tfoot>
	                </table>
	            </div>
	        </div>
	    </div>
	    <?php } ?>
    </div>
</div>

</body>
</html>