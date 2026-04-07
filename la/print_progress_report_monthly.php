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
            <th>BRANCH</th>
            <th>NAME</th>
            <th>OPD</th>
            <th>LAB</th>
            <th>%LAB</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $s = 0;
    $labs = 0;
    $total_lab = 0;
    $total_opd = 0;
    $labs_percentage = 0;
    $select = "SELECT DISTINCT tokans.doctor_id, users.u_name , branchs.tag_name, COUNT(CASE WHEN tokans.tokan_type_id <= 100 THEN tokans.tokan_type_id END) AS opd FROM `tokans` INNER JOIN users ON tokans.doctor_id = users.id INNER JOIN branchs ON users.branch_id = branchs.id WHERE tokans.created like '$date%' AND tokans.branch_id = '$br_id' AND tokans.status = '1' GROUP BY tokans.doctor_id ORDER BY tokans.doctor_id ";
    $run = mysqli_query($con, $select);
    if(mysqli_num_rows($run) > 0)
    {
        while($row = mysqli_fetch_array($run))
        {
            $opd = $row['opd'];
            $total_opd = $total_opd + $opd;
            $doctor_id = $row['doctor_id'];
        $select_data = "SELECT items.category_id, COUNT(DISTINCT item_by_doctor.tokan_no) AS count_token FROM `item_by_doctor` INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN tokans ON item_by_doctor.tokan_no = tokans.id  WHERE item_by_doctor.created LIKE '$date%' AND item_by_doctor.doctor_id = '$doctor_id' AND item_by_doctor.branch_id = '$br_id' AND items.category_id IN (2) AND tokans.status = '1' GROUP BY items.category_id ";
        $run_data = mysqli_query($con, $select_data);
        if(mysqli_num_rows($run_data) > 0)
        {
            while($row_data = mysqli_fetch_array($run_data))
            {
                $category_id = $row_data['category_id'];
                $count_token = $row_data['count_token'];
                $labs = $count_token;
                $total_lab = $total_lab + $labs;
                if($opd > 0 && $labs < $opd)
                {
                    $labs_percentage = ($labs/$opd)*100;
                }
                elseif($labs > $opd)
                {
                    $labs_percentage = 100;
                }
                else
                {
                    $labs_percentage = 0;
                }
            }
        }
        $select_opd = "SELECT tokans.id FROM `tokans` WHERE tokans.doctor_id = '$doctor_id' AND tokans.branch_id = '$br_id' AND tokans.status = '1' AND tokans.created LIKE '$date%' ";
        $run_opd = mysqli_query($con, $select_opd);
        if(mysqli_num_rows($run_opd))
        {
            while($row_opd = mysqli_fetch_array($run_opd))
            {
                $opds = $row_opd['0'];
            }
        }
        else
        {
            $opds = 0;
        }
            $s++; ?>
        <tr>
            <td><?php echo $s; ?></td>
            <td><?php echo $row['doctor_id']; ?></td>
            <td><?php echo $row['tag_name']; ?></td>
            <td><?php echo $row['u_name']; ?></td>
            <td><?php echo $opd; ?></td>
            <td><?php echo $labs; ?></td>
            <td><?php echo intval($labs_percentage); ?>%</td>
        </tr>
        <?php 
        $labs = 0;
        $labs_percentage = 0;
        }
    }
    ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <th><?php echo $total_opd; ?></th>
            <th><?php echo $total_lab; ?></th>
            <td></td>
        </tr>
    </tbody>
</table>
</body>
</html>
<?php mysqli_close($con); ?>