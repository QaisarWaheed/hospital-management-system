<?php include 'includes/connect.php';

if(isset($_POST['show_book_record']) && $_POST['book_no'] != '')
{
    
}
elseif(isset($_POST['save_book']) && $_POST['book_no'] != '')
{
    $book_year = $_POST['book_year'];
    $book_no = $_POST['book_no'];
    $br_id = $_POST['br_id'];
    $no_of_receipts = $_POST['no_of_receipts'];
    $start_slip_no = $_POST['start_slip_no'];
    $end_slip_no = $_POST['end_slip_no'];
    $sve_book = $_POST['sve_book'];
    $insert = "INSERT INTO `receipt_books`
    (`receipt_book_id`, `book_year`, `book_no`, `no_of_receipts`, `used_receipts`, `start_slip_no`, `end_slip_no`, `receipt_book_status`, `receipt_book_created`, `user_id`, `branch_id`) 
    VALUES
    (NULL, '$book_year', '$book_no', '$no_of_receipts', '0', '$start_slip_no', '$end_slip_no', '1', '$current_date', '$fr_id', '$br_id')";
    if(mysqli_query($con, $insert))
    {
        $insert2 = "";
        $insert2 .= "INSERT INTO `fr_collection`(`fr_collection_id`, `user_id`, `book_id`, `slip_no`, `fr_collection_created`) VALUES ";
        $start = $start_slip_no;
        $end = $end_slip_no;
        for ($start; $start <= $end; $start++) 
        {
            $insert2 .= "(NULL, '$fr_id', '$book_no', '$start', '$current_date'),";
        } 
        if(mysqli_query($con, substr($insert2,0,-1)))
        {
            header('Location: recepits.php');
            exit;
        }
    }
    exit;
}

include 'includes/head.php';
?>
	<title>Dashboard - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
	<div class="col-md-12" style="text-align: center;background: lightgreen;">
		<label><h1>YCDO</h1></label>
	</div>
	<div class="col-md-3 background_whitesmoke nodisplay_print">
		<?php include 'left_navigation.php'; ?>
	</div>
	<div class="col-md-9">
	    <?php
	    if(isset($_POST['show_book_record']) && $_POST['book_no'] != '')
	    {    	    
	        $book_no = $_POST['book_no'];
	        include 'tables/receipts.php'; 
	    }
	    else
	    {
    	    include 'tables/book.php'; 
	    }
	    ?>		
	</div>
</div>

</body>
</html>