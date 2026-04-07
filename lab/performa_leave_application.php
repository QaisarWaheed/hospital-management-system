<?php 
include 'includes/connect.php'; 
include 'includes/config.php'; 
?>
	<title>Lab Dashboard - <?php echo $company_trademark; ?></title>
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
                /* Reduce the base font size for the entire page to 12px */
                font-size: 12px; 
            }

            table {
                font-size: 0.8em; 
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body class="background_image">
<?php include 'top_navigation.php'; ?>
    <div class="row">
    	<div class="col-md-12 py-3">
	        <form>
    	        <div class = "row p-4">
    	            <div class = "col-md-12">
    	                <h2 align = "center"> LAB STAFF LEAVE PERFORMA</h2>
    	            </div>
    	            <div class = "col-md-4">
    	                <label>STAFF ID</label>
    	                <input readonly type = "text" name = "" value = "<?php echo $lab_user_id; ?>" class = "form-control" />
    	                <input type = "hidden" name = "releaver_staff_id" value = "<?php echo $lab_user_id; ?>" class = "form-control" />
    	            </div>
    	            <div class = "col-md-4">
    	                <label>STAFF NAME</label>
    	                <input readonly type = "text" name = "" value = "<?php echo $lab_user_name; ?>" class = "form-control" />
    	                <input type = "hidden" name = "releaver_staff_name" value = "<?php echo $lab_user_name; ?>" class = "form-control" />
    	            </div>
    	            <div class = "col-md-4">
    	                <label>STAFF PHONE</label>
    	                <input readonly type = "text" name = "" value = "<?php echo $lab_user_phone; ?>" class = "form-control" />
    	                <input type = "hidden" name = "releaver_staff_phone" value = "<?php echo $lab_user_phone; ?>" class = "form-control" />
    	            </div>
    	            <div class = "col-md-12">
    	                <form name = "frm_no_2"> 
    	                <label>SELECT RELIEVER STAFF</label>
    	                <select onchange = "frm_no_2.form.submit()" required class = "form-control" name = "reliever_staff_id">
    	                    <option value = "">SELECT STAFF</option>
    	                    <?php
    	                    $select = "SELECT * FROM `staff` INNER JOIN branchs ON staff.branch_id = branchs.id WHERE designation_id = '7' AND `staff_status` = '1' ORDER BY `staff`.`staff_name` ASC ";
    	                    $run = mysqli_query($con, $select);
    	                    if(mysqli_num_rows($run) > 0)
    	                    {
    	                        while($row = mysqli_fetch_array($run))
    	                        {
    	                            echo '<option value = "'.$row['staff_id'].'">'.$row['staff_name'].' - '.$row['staff_id'].'  ('.$row['tag_name'].')</option>';
    	                        }
    	                    }
    	                    ?>
    	                </select>
    	                </form>
    	            </div>
    	            <div class = "col-md-4">
    	                <label>RELIEVER ID</label>
    	                <input type = "number" name = "employee_id" class = "form-control" />
    	            </div>
    	            <div class = "col-md-4">
    	                <label>RELIEVER NAME</label>
    	                <input type = "text" name = "employee_name" class = "form-control" />
    	            </div>
    	            <div class = "col-md-4">
    	                <label>RELIEVER PHONE</label>
    	                <input type = "text" name = "employee_phone" max = "11" maxlength = "11" class = "form-control" />
    	            </div>
    	            <div class = "col-md-4">
    	                <label>SELECT LEAVE TYPE</label>
    	                <select name = "leave_type_id" class = "form-control">
    	                    <option value = "1"> FULL LEAVE</option>
    	                    <option value = "2"> SHORT LEAVE</option>
    	                </select>
    	            </div>
    	            <div class = "col-md-4">
    	                <label> TYPE</label>
    	                <select name = "type_id" class = "form-control">
    	                    <option value = "1"> ALLOWED LEAVE</option>
    	                    <option value = "2"> EXTRA LEAVE</option>
    	                </select>
    	            </div>
    	            <div class = "col-md-4">
    	                <label>LEAVE DATE</label>
    	                <input type = "date" name = "leave_date" class = "form-control" />
    	            </div>
    	            <div class = "col-md-12">
    	                <label>ENTER REASON / PURPOSE FOR LEAVE</label>
    	                <textarea class = "form-control" rows = "5" name = "reason_for_leave"></textarea>
    	            </div>
	            </div>
	        </form>
    	</div>
    </div>
</body>
</html>