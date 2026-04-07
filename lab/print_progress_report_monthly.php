<?php 
include 'includes/config.php'; 
include 'includes/connect.php'; 
if(isset($_GET['date']))
{
    $date = $_GET['date'];
    $br_id = $_GET['br_id'];
}
elseif(isset($_POST['date']))
{
    $date = $_POST['date'];
    $br_id = $_POST['br_id'];
}
else
{
    exit(0);
}
?>
<html>
<head>
    <title><?php echo get_branch_tag_by($br_id)." ";echo date_format(date_create($date), "m-Y"); ?> MONTHLY PROGRESS REPORT </title>
</head>
<body>
    
<table border = "solid">
<caption>
    <h2><?php echo $company_name; ?></h2>
    <h2><?php echo get_branch_name_by($br_id); ?></h2>
    <h3>PROGRESS MONTH <?php echo date_format(date_create($date), " F Y"); ?></h3>
</caption>
    <thead>
        <tr>
            <th>S#</th>
            <th>ID</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>LAB</th>
            <th>%LAB</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $s = 0;
    $select = "SELECT DISTINCT doctor_id, users.u_name, COUNT(`cash`) AS opd FROM `tokans` INNER JOIN users ON tokans.doctor_id = users.id WHERE tokans.branch_id = '$br_id' AND tokans.created LIKE '$date%' AND tokans.tokan_type_id < '100' GROUP BY tokans.doctor_id ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run) > 0)
    {
        while($row = mysqli_fetch_array($run))
        {
            $doctor_id = $row['doctor_id'];
            $labs = "SELECT COUNT(DISTINCT item_by_doctor.tokan_no) FROM `item_by_doctor` INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id WHERE item_by_doctor.created LIKE '$date%' AND item_by_doctor.doctor_id = '$doctor_id' AND item_by_doctor.branch_id = '$br_id' AND items.category_id = '2' ";
            $run_lab = mysqli_query($con, $labs);
            if(mysqli_num_rows($run_lab) > 0)
            {
                while($row_lab = mysqli_fetch_array($run_lab))
                {
                    $lab = $row_lab['0'];
                }
            }
            $s++; ?>
        <tr>
            <td><?php echo $s; ?></td>
            <td><?php echo $row['doctor_id']; ?></td>
            <td><?php echo $row['u_name']; ?></td>
            <td><?php echo $row['opd']; ?></td>
            <td><?php echo $lab; ?></td>
            <td><?php echo intval(($lab/$row['opd'])*100); ?>%</td>
        </tr>
        <?php }
    }
    ?>
    </tbody>
</table>
</body>
</html>
<?php mysqli_close($con); ?>