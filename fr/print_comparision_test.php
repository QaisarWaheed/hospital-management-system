<?php include 'includes/connect.php';
if (!isset($_GET['s'], $_GET['e'])) {
    header('Location: logout.php');
    exit;
}
$first_month = $_GET['s'];
$second_month = $_GET['e'];
include 'includes/head.php';
?>
	<title>COMPARISION ALL BRANCHES - <?php echo $company_trademark; ?></title>
</head>
<body style = "text-transform: uppercase;">
    <div class = "row">
        <div class = "col-md-12" style = "text-transform: uppercase;text-align: center;">
            <label><h1>COMPARISION ALL BRANCHES <?php echo date_format(date_create($first_month), "F Y"); ?> & <?php echo date_format(date_create($second_month), "F Y"); ?></h1></label>
        </div>
<?php
// $select_branch = "SELECT * FROM `branchs` WHERE `status` = 1 AND id IN (SELECT DISTINCT branch_id FROM tokans WHERE created LIKE '$first_month%' OR created LIKE '$second_month%') ";
$select_branch = "SELECT * FROM `branchs` WHERE `status` = 1 AND id = '9' ";
$run_branch = mysqli_query($con, $select_branch);
if(mysqli_num_rows($run_branch) > 0)
{
    while($row_branch = mysqli_fetch_array($run_branch))
    {
        $comparision_branch_id = $row_branch['id'];
        $comparision_branch_name = $row_branch['name'];
        $comparision_branch_address = $row_branch['address'];
?>
        <div class = "col-md-12">
            <div class = "panel panel-info">
                <div class = "panel-body">
                    <table class = "table table-bordered table-sm">
                        <tr>
                            <th colspan = "7"><h2 align = "center"><?php echo $comparision_branch_address; ?></h2></th>
                        </tr>
                        <tr>
                            <td></td>
                            <th>PATIENT</th>
                            <th>LAB INCOME</th>
                            <th>SVD</th>
                            <th>DNC</th>
                            <th>OPERATIONS</th>
                            <th>COLLECTION</th>
                        </tr>
                        <?php
                        $select_opd = "SELECT SUM(cash),COUNT(CASE WHEN tokan_type_id < 100 THEN id END) AS opd FROM tokans WHERE status = '1' AND branch_id = '$comparision_branch_id' AND created LIKE '$first_month%' ";
                        $run_opd = mysqli_query($con, $select_opd);
                        if(mysqli_num_rows($run_opd) > 0)
                        {
                            while($row_opd = mysqli_fetch_array($run_opd))
                            {
                                $opd = $row_opd['opd'];
                                $collection = $row_opd['0'];
                            }
                        }
                        
                        $select_data = "SELECT items.category_id, COUNT(items.category_id) AS count_data, COUNT(DISTINCT item_by_doctor.tokan_no) AS count_token, SUM(tokans.cash) AS incom FROM `item_by_doctor` INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN tokans ON item_by_doctor.tokan_no = tokans.id WHERE tokans.created LIKE '$first_month%' AND item_by_doctor.branch_id = '$comparision_branch_id' AND items.category_id IN (2, 3, 29, 37, 38) AND tokans.status = '1' GROUP BY items.category_id ";
                        $run_data = mysqli_query($con, $select_data);
                        if(mysqli_num_rows($run_data) > 0)
                        {
                            while($row_data = mysqli_fetch_array($run_data))
                            {
                                if($row_data['category_id'] == 2)
                                {
                                    $lab = $row_data['2'];
                                    $lab_amount = $row_data['3'];
                                }
                                elseif($row_data['category_id'] == 3)
                                {
                                    $operation = $row_data['2'];
                                    $operation_amount = $row_data['3'];
                                }
                                elseif($row_data['category_id'] == 29)
                                {
                                    $consultant_opd = $row_data['2'];
                                    $consultant_opd_amount = $row_data['3'];
                                }
                                elseif($row_data['category_id'] == 37)
                                {
                                    $svd = $row_data['2'];
                                    $svd_amount = $row_data['3'];
                                }
                                elseif($row_data['category_id'] == 38)
                                {
                                    $dnc = $row_data['2'];
                                    $dnc_amount = $row_data['3'];
                                }
                            }
                        }
                        ?>
                        <tr>
                            <th><?php echo date_format(date_create($first_month), "m-Y"); ?></th>
                            <th><?php echo $opd; ?> + <?php echo $consultant_opd; ?> => <?php echo intval($opd+$consultant_opd); ?></th>
                            <th><?php echo $lab.' -> '.number_format((float)($lab_amount ?? 0)); ?></th>
                            <th><?php echo $svd.' -> '.number_format((float)($svd_amount ?? 0)); ?></th>
                            <th><?php echo $dnc.' -> '.number_format((float)($dnc_amount ?? 0)); ?></th>
                            <th><?php echo $operation.' -> '.number_format((float)($operation_amount ?? 0)); ?></th>
                            <th><?php echo number_format((float)($collection ?? 0)); ?></th>
                        </tr>
                        <?php
                        $select_opd_2 = "SELECT SUM(cash),COUNT(CASE WHEN tokan_type_id < 100 THEN id END) AS opd FROM tokans WHERE status = '1' AND branch_id = '$comparision_branch_id' AND created LIKE '$second_month%' ";
                        $run_opd_2 = mysqli_query($con, $select_opd_2);
                        if(mysqli_num_rows($run_opd_2) > 0)
                        {
                            while($row_opd_2 = mysqli_fetch_array($run_opd_2))
                            {
                                $opd_2 = $row_opd_2['opd'];
                                $collection_2 = $row_opd_2['0'];
                            }
                        }

                        $select_data_2 = "SELECT items.category_id, COUNT(items.category_id) AS count_data, COUNT(DISTINCT item_by_doctor.tokan_no) AS count_token, SUM(tokans.cash) AS incom FROM `item_by_doctor` INNER JOIN item_register_to_branches ON item_by_doctor.item_id = item_register_to_branches.id INNER JOIN items ON item_register_to_branches.item_id = items.id INNER JOIN tokans ON item_by_doctor.tokan_no = tokans.id WHERE tokans.created LIKE '$second_month%' AND item_by_doctor.branch_id = '$comparision_branch_id' AND items.category_id IN (2, 3, 29, 37, 38) AND tokans.status = '1' GROUP BY items.category_id ";
                        $run_data_2 = mysqli_query($con, $select_data_2);
                        if(mysqli_num_rows($run_data_2) > 0)
                        {
                            while($row_data_2 = mysqli_fetch_array($run_data_2))
                            {
                                if($row_data_2['category_id'] == 2)
                                {
                                    $lab_2 = $row_data_2['2'];
                                    $lab_amount_2 = $row_data_2['3'];
                                }
                                elseif($row_data_2['category_id'] == 3)
                                {
                                    $operation_2 = $row_data_2['2'];
                                    $operation_amount_2 = $row_data_2['3'];
                                }
                                elseif($row_data_2['category_id'] == 29)
                                {
                                    $consultant_opd_2 = $row_data_2['2'];
                                    $consultant_opd_amount_2 = $row_data_2['3'];
                                }
                                elseif($row_data_2['category_id'] == 37)
                                {
                                    $svd_2 = $row_data_2['2'];
                                    $svd_amount_2 = $row_data_2['3'];
                                }
                                elseif($row_data_2['category_id'] == 38)
                                {
                                    $dnc_2 = $row_data_2['2'];
                                    $dnc_amount_2 = $row_data_2['3'];
                                }
                            }
                        }
                        ?>
                        <tr>
                            <th><?php echo date_format(date_create($second_month), "m-Y"); ?></th>
                            <th><?php echo $opd_2; ?> + <?php echo $consultant_opd_2; ?> => <?php echo intval($opd_2+$consultant_opd_2); ?></th>
                            <th><?php echo $lab_2.' -> '.number_format((float)($lab_amount_2 ?? 0)); ?></th>
                            <th><?php echo $svd_2.' -> '.number_format((float)($svd_amount_2 ?? 0)); ?></th>
                            <th><?php echo $dnc_2.' -> '.number_format((float)($dnc_amount_2 ?? 0)); ?></th>
                            <th><?php echo $operation_2.' -> '.number_format((float)($operation_amount_2 ?? 0)); ?></th>
                            <th><?php echo number_format((float)($collection_2 ?? 0)); ?></th>
                        </tr>
                        <tr>
                            <th>Differance</th>
                            <th><?php echo ($opd-$opd_2); ?> + <?php echo ($consultant_opd-$consultant_opd_2); ?> => <?php echo intval(($opd-$opd_2)+($consultant_opd-$consultant_opd_2)); ?></th>
                            <th><?php echo ($lab-$lab_2).' -> '.($lab_amount-$lab_amount_2); ?></th>
                            <th><?php echo number_format((float)($svd-$svd_2 ?? 0)).' -> '.number_format((float)($svd_amount-$svd_amount_2 ?? 0)); ?></th>
                            <th><?php echo number_format((float)($dnc-$dnc_2 ?? 0)).' -> '.number_format((float)($dnc_amount-$dnc_amount_2 ?? 0)); ?></th>
                            <th><?php echo number_format((float)($operation-$operation_2 ?? 0)).' -> '.number_format((float)($operation_amount-$operation_amount_2 ?? 0)); ?></th>
                            <th><?php echo number_format((float)($collection-$collection_2 ?? 0)); ?></th>
                        </tr>
        <?php
        $opd = 0;
        $consultant_opd = 0;
        $lab = 0;
        $lab_amount = 0;
        $operation = 0;
        $operation_amount = 0;
        $collection = 0;
        
        $opd_2 = 0;
        $consultant_opd_2 = 0;
        $lab_2 = 0;
        $lab_amount_2 = 0;
        $operation_2 = 0;
        $operation_amount_2 = 0;
        $collection_2 = 0;
        } ?>
</table>
                </div>
            </div>
        </div>
<?php }
else
{ ?>
        <div class = "col-md-12">
            <label style = "text-transform: uppercase;">PLEASE ADD BRANCH FIRST</label>
        </div>
<?php }
?>
    </div>
</body>
</html>