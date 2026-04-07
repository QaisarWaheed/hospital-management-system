<?php include 'includes/connect.php'; 

if(isset($_POST['save_update_image']))
{
    $staff_id = $_POST['staff_id'];
    // start image
	$file=$_FILES['file'];
	$fileName=$_FILES['file']['name'];	// 
	$fileTmpName=$_FILES['file']['tmp_name'];
	$fileExt = explode('.', $fileName);	//
	$fileActualExt = strtolower(end($fileExt));	//
	$fileNameNew = uniqid('' , true).".".$fileActualExt;
	$buyer1 = '../images/staff/'.$fileNameNew;
	$buyer = $fileNameNew;
    //end image      
    $update = "UPDATE `staff` SET `staff_image_href`= '$buyer' WHERE `staff_id` = '$staff_id' ";
    if(mysqli_query($con, $update))
    {
		move_uploaded_file($fileTmpName, $buyer1);
		header('location: show_staff.php?staff_id='.$staff_id);
    } 
    else
    {
        echo $con->error;
    }
    exit(0);
}

if (isset($_POST['staff_id']) && $_POST['staff_id'] != '') 
{
    $staff_id = $_POST['staff_id'];
    $select = "SELECT * FROM `staff` INNER JOIN designations ON staff.designation_id = designations.designation_id INNER JOIN relationships ON staff.relationship_id = relationships.relationship_id WHERE `staff_id` = '$staff_id' ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run) == 1)
    {
        while($row = mysqli_fetch_array($run))
        {
            $other_person_name = $row['other_person_name'];
            $other_person_address = $row['other_person_address'];
            $other_person_phone = $row['other_person_phone'];
            $relationship_id = $row['relationship_id'];
            $hostel_name = $row['hostel_name'];
            $hostel_warden_name = $row['hostel_warden_name'];
            $hostel_warden_phone = $row['hostel_warden_phone'];
            $hostel_address = $row['hostel_address'];
        	$staff_name = $row['staff_name'];
        	$staff_spouse = $row['staff_spouse'];
        	$staff_phone = $row['staff_phone'];
        	$staff_cnic = $row['staff_cnic'];
        	$staff_address = $row['staff_address'];
        	$branch_idd = $row['branch_id'];
        	$staff_duty_hours = $row['staff_duty_hours'];
        	$staff_joining_date = $row['staff_joining_date'];
        	$staff_time_in = $row['staff_time_in'];
        	$staff_time_out = $row['staff_time_out'];
        	$staff_bacis_salary = $row['staff_bacis_salary'];
        	$staff_allowed_leaves = $row['staff_allowed_leaves'];
        	$staff_qualification = $row['staff_qualification'];
        	$designation_id = $row['designation_id'];
        	$staff_status = $row['staff_status'];            
        }
    }
}
else
{
    header('location: add_staff.php?err=1');
}
?>
<?php include 'includes/head.php'; ?>
	<title>PRINT STAFF DATA - <?php echo $company_trademark; ?></title>
</head>

<body>
<div class = "container">
    <table class = "table table-bordered">
            <tr>
                <th>STAFF ID</th>
                <td><?php echo $staff_id; ?></td>
                <th>DESIGNATION</th>
                <td><?php echo $row['designation_title']; ?></td>
                <th rowspan = "6" colspan = "2" style = "text-align: center;">
                    <picture>
                    <img src="https://ozsaphire.vpreps.com/images/staff/<?php echo $row['staff_image_href']; ?>" alt="<?php echo $row['staff_name']; ?>" style="width:auto;">
                    </picture>  
                    <form method="POST" action="update_staff_image.php" enctype="multipart/form-data" autocomplete="off">
                        <input type = "text" name = "staff_id" value = "<?php echo $staff_id; ?>" />
						<input type="file" capture name="file" accept="image/*" class="form-control" >
                        <input type = "submit" name = "save_update_image" value = "UPLOAD" class = "btn btn-sm btn-into" />
                    </form>
                </th>
            </tr>
            <tr>
                <th>NAME</th>
                <td><?php echo $staff_name; ?></td>
                <th>S/O, D/O, W/O</th>
                <td><?php echo $staff_spouse; ?></td>
            </tr>
            <tr>
                <th>PHONE</th>
                <td><?php echo $staff_phone; ?></td>
                <th>CNIC</th>
                <td><?php echo $staff_cnic; ?></td>
            </tr>
            <tr>
                <th>DUTY START</th>
                <td><?php echo date_format(date_create($staff_time_in), "h:i A"); ?></td>
                <th>DUTY END</th>
                <td><?php echo date_format(date_create($staff_time_out), "h:i A"); ?></td>
            </tr>
            <tr>
                <th>DUTY HOURS</th> 
                <td><?php echo $staff_duty_hours; ?></td>
                <th>QUALIFICATION</th>
                <td><?php echo $staff_qualification; ?></td>
            </tr>
            <tr>
                <th>BRANCH</th>
                <td colspan = "3"><?php echo get_branch_name_by($branch_idd); ?></td>
            </tr>
            <tr>
                <th>ADDRESS</th>
                <td colspan = "5"><?php echo $staff_address; ?></td>
            </tr>
            <tr>
                <th colspan = "6">
                    <h3>OTHER PERSON CONTACT INFORMATION</h3>
                </th>
            </tr>
            <tr>
                <th>NAME</th>
                <td><?php echo $other_person_name; ?></td>
                <th>PHONE</th>
                <td><?php echo $other_person_phone; ?></td>
                <th>RELATIONSHIP</th>
                <td><?php echo $row['relationship_title']; ?></td>
            </tr>
            <tr>
                <th colspan = "6">
                    <h3>RESIDENCE INFORMATION</h3>
                </th>
            </tr>
            <tr>
                <th>HOSTEL NAME</th>
                <td><?php echo $hostel_name; ?></td>
                <th>HOSTEL WARDEN & PHONE</th>
                <td><?php echo $hostel_warden_name.' - ' .$hostel_warden_phone; ?></td>
                <th>HOSTEL ADDRESS </th>
                <td><?php echo $hostel_address; ?></td>
            </tr>
            <tr>
                <td colspan = "6" style = "font-size: 10px">
                    <label>PLEASE ATTECH FOLLOWING DOCUMENTS WITH EMPLOYEE FORM</label>
                    <ol>
                        <li>1 PASSPORT SIZE PICTURE.</li>
                        <li>1 copy of CNIC</li>
                        <li>1 copy of guardian cnic</li>
                        <li>1 blank cheque</li>
                        <li>qualification certifications</li>
                        <li>CV copy</li>
                    </ol>
                </td>
            </tr>
            <tr style = "text-align: center;">
                <th colspan = "2"><br><br><br>STAFF SIGNATURE</th>
                <th colspan = "2"><br><br><br>ADMIN SIGNATURE</th>
                <th colspan = "2"><br><br><br>HR SIGNATURE</th>
            </tr>
            <tr>
                <td colspan = "6">FOR OFFICE USE ONLY</td>
            </tr>
            <tr>
                <td>JOINING DATE</td>
                <td></td>
                <td>BASIC SALARY</td>
                <td></td>
                <td>ALLOW LEAVE</td>
                <td></td>
            </tr>
            <tr style = "text-align: center;">
                <th colspan = "2"><br><br><br>ACCOUNTANT SIGNATURE</th>
                <th colspan = "2"><br><br><br>approved by:</th>
                <th colspan = "2"><br><br><br>MANAGER SIGNATURE</th>
            </tr>
            <caption style = "text-align: center;caption-side: top;color: black;">
                <h2>YOUTH COMMUNITY DEVELOPMENT ORGANIZATION</h2>
                <H3>EMPLOYEE DATA FORM</H3>
            </caption>
    </table>
</div>


</body>
</html>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<?php mysqli_close($con); ?>