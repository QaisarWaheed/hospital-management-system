<?php include 'includes/connect.php'; 
if(isset($_GET['token_no']) && $_GET['token_no'] != '')
{
    $token_no = $_GET['token_no'];
    $select = "SELECT * FROM tokans WHERE id = '$token_no' ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run))
    {
        while($row = mysqli_fetch_array($run))
        {
            
        }
    }
}
?>
<?php include 'includes/head.php'; ?>
	<title>Procedure Token detail - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image_ycdo" oncontextmenu="return false;">

<div>

	<div class="" style="margin: 10px 15px;">
	    <table class="table table-hover">
	        <caption>
	            
	        </caption>
	        <tr>
	            <th>Token No</th>
	            <td><?php echo $token_no; ?></td>
	            <th>Procedure Name</th>
	            <td></td>
	        </tr>
	    </table>
	</div>

</div>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>