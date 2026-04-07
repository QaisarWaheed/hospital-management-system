<?php
include 'includes/config.php';  
if(isset($_GET['item_id']) && $_GET['item_id'] != '')
{
    $item_id = $_GET['item_id'];
    $rows = mysqli_query($con, "SELECT lab_reporting_test_id, items.id AS item_id, lab_reporting_test_unit, lab_reporting_test_time_minutes, test_categories.test_category_title, items.name FROM `lab_reporting_tests` INNER JOIN items ON lab_reporting_tests.item_id = items.id INNER JOIN test_categories ON lab_reporting_tests.lab_reporting_test_type = test_categories.test_category_id WHERE items.id = '$item_id' ");
}
else
{
    $rows = mysqli_query($con, "SELECT lab_reporting_test_id, items.id AS item_id, lab_reporting_test_unit, lab_reporting_test_time_minutes, test_categories.test_category_title, items.name FROM `lab_reporting_tests` INNER JOIN items ON lab_reporting_tests.item_id = items.id INNER JOIN test_categories ON lab_reporting_tests.lab_reporting_test_type = test_categories.test_category_id ");
}
?>
<table border = 1 cellpadding = 10>
    <tr>
        <td>#</td>
        <td>Id</td>
        <td>Item Id</td>
        <td>Name</td>
        <td>Category</td>
        <td>Unit</td>
        <td>Reporting Time</td>
    </tr>
    <?php $i = 1; ?>
    <?php foreach($rows as $row) : ?>
    <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo $row["lab_reporting_test_id"]; ?></td>
        <td><?php echo $row["item_id"]; ?></td>
        <td><?php echo $row["name"]; ?></td>
        <td><?php echo $row["test_category_title"]; ?></td>
        <td><?php echo $row["lab_reporting_test_unit"]; ?></td>
        <td><?php echo $row["lab_reporting_test_time_minutes"]; ?></td>
    </tr>
    <?php endforeach; ?>
</table>
