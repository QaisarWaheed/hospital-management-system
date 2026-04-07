<?php 
include 'includes/connect.php'; 
include 'includes/config.php';

?>
	<title>LAB TEST RATE LIST (<?php echo date('d-m-Y'); ?>)- <?php echo $lab_login_branch_name; ?> - LAB - <?php echo $company_trademark; ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style>
    .background_image{
        background-image: url('../images/background.png');
        background-size: cover;
    }
    </style>    
    <style>
        @media print {
            body {
                font-size: 14px; 
            }

            table {
                font-size: 1.1em; 
            }
        }
body
{
    counter-reset: Serial;           /* Set the Serial counter to 0 */
}

table
{
    border-collapse: separate;
}

tr td:first-child:before
{
  counter-increment: Serial;      /* Increment the Serial counter */
  content: counter(Serial); /* Display the counter */
}
    </style>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body class="background_image">
<?php include 'top_navigation.php'; ?>
    <div class="row">
    	<div class="col-md-12">
    	    <table class = "table table-sm table-bordered table-hover" style = "color: black'" id="myTable">
    	        <caption style = "text-align: center; caption-side: top;color: black;">
    	            <h1>LAB TEST RATE LIST (<?php echo date('d-m-Y'); ?>)</h1>
    	        </caption>
    	        <thead>
			<tr>
                <th class = "d-print-none" colspan = "12">
                    <div class = "row">
                        <div class = "col-md-12">
                            <label>Test Name</label>
                            <input type = "text" class = "form-control" id="myInputTestName" onkeyup="myFunctionTestName()" placeholder="Search for names.." />
                        </div>
                    </div>
                </th>
			</tr>
			<tr>
    	                <th>S #</th>
    	                <th>ITEM ID</th>
    	                <th>ITEM Name</th>
    	                <th>CATEGORY</th>
    	                <th>POOR</th>
    	                <th>MEMBER</th>
    	                <th>GENERAL</th>
    	            </tr>
    	        </thead>
    	        <tbody>
    	            <?php
    	            $s = 0;
    	            $select = "SELECT items.id, items.name, poor, member, general, categories.name AS category_name FROM `items` INNER JOIN categories ON items.category_id = categories.id WHERE items.status = '1' AND items.category_id = '2' ORDER BY items.name ";
    	            $run = mysqli_query($con, $select);
    	            if(mysqli_num_rows($run) > 0)
    	            {
    	                while($row = mysqli_fetch_array($run))
    	                {
    	                    $s++; ?>
    	            <tr>
    	                <!--<td><?php echo $s; ?></td>-->
    	                <td></td>
    	                <td><?php echo $row['id']; ?></td>
    	                <td><?php echo $row['name']; ?></td>
    	                <td><?php echo $row['category_name']; ?></td>
    	                <td><?php echo $row['poor']; ?></td>
    	                <td><?php echo $row['member']; ?></td>
    	                <td><?php echo $row['general']; ?></td>
    	            </tr>
    	                <?php }
    	            }
    	            else
    	            {
    	                echo '<tr><th colspan = "9">NO TEST RECORD IN LAB</th></tr>';
    	            }
    	            ?>
    	        </tbody>
    	    </table>
    	</div>
    </div>
</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script>
function myFunction() 
{
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) 
    {
        user_name = tr[i].getElementsByTagName("td")[1];
        if (user_name) 
        {
            txtValue = user_name.textContent || user_name.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) 
            {
                tr[i].style.display = "";
            } 
            else 
            {
                tr[i].style.display = "none";
            }
        }       
    }
}
function myFunctionTestName() 
{
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInputTestName");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) 
    {
        user_name = tr[i].getElementsByTagName("td")[2];
        if (user_name) 
        {
            txtValue = user_name.textContent || user_name.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) 
            {
                tr[i].style.display = "";
            } 
            else 
            {
                tr[i].style.display = "none";
            }
        }       
    }
}
</script>
<?php mysqli_close($con); ?>