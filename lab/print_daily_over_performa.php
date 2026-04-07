<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
if(isset($_GET['daily_over_performa_summery_id']))
{
    $daily_over_performa_summery_id = $_GET['daily_over_performa_summery_id'];
}
elseif(isset($_POST['daily_over_performa_summery_id']))
{
    $daily_over_performa_summery_id = $_POST['daily_over_performa_summery_id'];
}
else
{
    exit(0);
}
?>
<html>
<head>
    <title>daily_over_performa_summery_id</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .content {
            width: 100%;
            padding: 20mm;
            box-sizing: border-box;
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, p {
            margin: 0 0 10px;
        }
    </style>
</head>
<body>
<?php include 'top_navigation.php'; ?>
    <?php
    $select = "SELECT daily_over_performa_summeries.daily_over_performa_summery_id, daily_over_performa_summeries.daily_over_performa_summery_date, over_shifts.over_shift_title, branchs.name, sections.section_title, over_shift_from_staff.u_name AS over_shift_from_staff_name, over_shift_to_staff.u_name AS over_shift_to_staff_name FROM `daily_over_performa_summeries` INNER JOIN sections ON daily_over_performa_summeries.section_id = sections.section_id INNER JOIN branchs ON daily_over_performa_summeries.branch_id = branchs.id INNER JOIN over_shifts ON daily_over_performa_summeries.over_shift_id = over_shifts.over_shift_id INNER JOIN users over_shift_from_staff ON daily_over_performa_summeries.over_shift_from = over_shift_from_staff.id INNER JOIN users over_shift_to_staff ON daily_over_performa_summeries.over_shift_from = over_shift_to_staff.id WHERE `daily_over_performa_summery_id` = '$daily_over_performa_summery_id' ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run) > 0)
    {
        while($row = mysqli_fetch_array($run))
        {
            $daily_over_performa_summery_date = $row['daily_over_performa_summery_date'];
            $over_shift_title = $row['over_shift_title'];
            $name = $row['name'];
            $section_title = $row['section_title'];
            $over_shift_from_staff_name = $row['over_shift_from_staff_name'];
            $over_shift_to_staff_name = $row['over_shift_to_staff_name'];
            ?>
            <h2 align = "center"><?php echo $company_name; ?></h2>
            <table class = "table">
                <tr>
                    <th>ID</th>
                    <th><?php echo $daily_over_performa_summery_id; ?></th>
                    <th>BRANCH</th>
                    <th><?php echo $name; ?></th>
                    <th>SHIFT DETAIL</th>
                    <th><?php echo $over_shift_title; ?></th>
                </tr>
                <tr>
                    <th>DATE</th>
                    <th><?php echo $daily_over_performa_summery_date; ?></th>
                    <th>OVER FROM</th>
                    <th><?php echo $over_shift_from_staff_name; ?></th>
                    <th>OVER TO</th>
                    <th><?php echo $over_shift_to_staff_name; ?></th>
                </tr>
            </table>
        <?php
        }
    }
    ?>    
<table class = "table">
    <thead>
        <tr>
            <th>S#</th>
            <th>ID</th>
            <th>ITEM NAME</th>
            <th>WORK STATUS</th>
            <th>REMARKS(<sub>IF ANY</sub>)</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $select = "SELECT daily_over_performas.daily_over_performa_id, daily_over_performas.daily_over_performa_status, daily_over_performas.daily_over_performa_remarks, over_productes.over_product_title FROM daily_over_performas INNER JOIN over_productes ON daily_over_performas.over_product_id = over_productes.over_product_id WHERE `daily_over_performa_summery_id` = '$daily_over_performa_summery_id' ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run) > 0)
    {
        while($row = mysqli_fetch_array($run))
        {
            $s++; 
            $daily_over_performa_id = $row['daily_over_performa_id'];
            $daily_over_performa_status = $row['daily_over_performa_status'];
            if($daily_over_performa_status == '1')
            {
                $daily_over_performa_status_msg = "NO";
            }
            elseif($daily_over_performa_status == '2')
            {
                $daily_over_performa_status_msg = "YES";
            }
            elseif($daily_over_performa_status == '0')
            {
                $daily_over_performa_status_msg = "N/A";
            }
            else
            {
                $daily_over_performa_status_msg = "N/A";
            }
            $daily_over_performa_remarks = $row['daily_over_performa_remarks'];
            $over_product_title = $row['over_product_title'];
            ?>
            <tr>
                <td><?php echo $s; ?></td>
                <td><?php echo $daily_over_performa_id; ?></td>
                <td><?php echo $over_product_title; ?></td>
                <td><?php echo $daily_over_performa_status_msg; ?></td>
                <td><?php echo $daily_over_performa_remarks; ?></td>
            </tr>
        <?php
        }
    }
    ?>
    </tbody>
</table>
</body>
</html>
<?php mysqli_close($con); ?>