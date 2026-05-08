<?php
require_once __DIR__ . '/includes/db_connect.php';
?>
<html>
<head>
    
</head>
<body>
<table border = "solid">
    <thead>
        <tr>
            <th>Sr. No</th>
            <th>Issue No</th>
            <th>Branch Name</th>
            <th>No Of Items</th>
        </tr>
    </thead>
    <tbod>
<?php
$s = 0;
$select = "SELECT DISTINCT `issue_id` FROM `item_register_branchs_by_sm` WHERE 1 ORDER BY `item_register_branchs_by_sm`.`issue_id` ASC";
$run = mysqli_query($con, $select);
if(mysqli_num_rows($run) > 0)
{
    while($row = mysqli_fetch_array($run))
    {
        $s = $s + 1;
        $issue_id = $row['0'];
        $no_of_item = mysqli_num_rows(mysqli_query($con, "SELECT `id` FROM `item_register_branchs_by_sm` WHERE issue_id = '$issue_id' "));
        $select_branch = "SELECT name FROM branchs WHERE id IN (SELECT `branch_id` FROM `item_register_branchs_by_sm` WHERE issue_id = '$issue_id' )";
        $run_branch = mysqli_query($con, $select_branch);
        while($row_branch = mysqli_fetch_array($run_branch))
        {
            $branch_name = $row_branch['name'];
        }
        echo '
        <tr>
            <td>'.$s.'</td>
            <td>'.$row['0'].'</td>
            <td>'.$branch_name.'</td>
            <td style = "text-align: right;">'.$no_of_item.'</td>
        </tr>
        ';
    }
}?>
    </tbod>
</table>    
</body>
</html>