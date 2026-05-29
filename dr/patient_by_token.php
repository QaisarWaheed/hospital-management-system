<?php
include 'includes/connect.php';

if (isset($_GET['patient_history']) && $_GET['patient_history'] !== '') {
    $token_id = $_GET['token_id'] ?? '';
    $patient_history = $_GET['patient_history'];
    $insert = "INSERT INTO `patient_histories`
    (`token_no`, `doctor_id`, `patient_history_created_at`, `patient_history_status`, `patient_history_detail`)
    VALUES
    ('$token_id', '$user_id', '$current_date', '1', '$patient_history')";
    $msg = mysqli_query($con, $insert) ? 'SUCCESS-SAVE-HISTORY' : 'ERROR-SAVE-HISTORY';
    header('Location: patient_by_token.php?msg=' . $msg . '&token_id=' . urlencode($token_id));
    exit;
}

if (isset($_GET['del_medicine']) && $_GET['del_medicine'] !== '') {
    $token_id = $_GET['token_id'] ?? '';
    $del_id = $_GET['del_medicine'];
    $update = "UPDATE `select_by_doctor` SET `status` = '2' WHERE `id` = '$del_id' AND `tokan_no` = '$token_id' ";
    if (mysqli_query($con, $update)) {
        header('Location: patient_by_token.php?token_id=' . urlencode($token_id));
        exit;
    }
}

if (isset($_GET['save_test'])) {
    $token_id = $_GET['token_id'] ?? '';
    $reg_item_id = ycdo_resolve_register_item_id($branch_id, $_GET['reg_item_id'] ?? '');
    if ($reg_item_id < 1) {
        header('Location: patient_by_token.php?token_id=' . urlencode($token_id) . '&msg=ERROR-INVALID-ITEM');
        exit;
    }
    $item_id = get_item_id_by_register_item_id($reg_item_id);
    $fix_dose = $_GET['fix_dose'];
    $dose = $_GET['dose'];
    $feed = $_GET['feed'];
    $days = $_GET['days'];
    $insert = "INSERT INTO `select_by_doctor`
    (`tokan_no`, `item_id`, `dose`,  `feed`,  `days`,  `user_id`,  `branch_id`, `fix_dose`, `created`, `items_table_id`) VALUES
    ('$token_id', '$reg_item_id', '$dose', '$feed', '$days', '$user_id','$branch_id', '$fix_dose', '$current_date', '$item_id')";
    if (mysqli_query($con, $insert)) {
        $token_doctor_id = get_doctor_id_by_token_no($token_id);
        $query = "INSERT INTO `doctor_tokens`(`doctor_token`, `token_no`, `doctor_id`, `user_id`, `status`, `created`) VALUES (NULL, '$token_id', '$token_doctor_id', '$user_id', '1', '$current_date')";
        if (mysqli_query($con, $query)) {
            mysqli_query($con, "UPDATE tokans SET doctor_id = '$user_id'  WHERE id = '$token_id' ");
        }
        header('Location: patient_by_token.php?token_id=' . urlencode($token_id) . '&days=' . urlencode((string) $days));
        exit;
    }
}

include 'includes/head.php';

$role_title = '';
$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if (mysqli_num_rows($run_roles) == 1) {
    while ($row_role = mysqli_fetch_array($run_roles)) {
        $role_title = $row_role['title'];
    }
}
?>
    <title>OPD Patients - <?php echo $company_trademark; ?></title>
</head>

<body class="background_image">

<div class="row" style="margin: 0px;">
    <div class="col-md-12" style="text-align: center;background: lightgreen;"><label><h1><?php echo $company_name; ?> </h1></label></div>
    <div class="col-md-3 background_whitesmoke">    <?php include 'left_navigation.php'; ?> 
            <h3 style="margin-top: 350px;text-align: center;"><?php echo $_SESSION['dr_name'];if($_SESSION['is_incharge'] == 2){ echo " Incharge ";} ?>(<?php echo $role_title; ?>)</h3>
    </div>
    <div class="col-md-9">
        <div class="row">
    <div class="col-md-12">
        <form autocomplete="off">
            <div class="row">
                <div class="col-md-9">
                    <label><h3>ENTER TOKEN NO FOR SEARCH</h3></label>
                    <input value="<?php if(isset($_GET['token_id'])){echo $_GET['token_id'];} ?>" <?php if(!isset($_GET['token_id'])){echo " autofocus ";}?> type="text" id="token_id" required name="token_id" maxlength="8" size="8" class = "form-control" pattern="[0-9]{1,}" title="One or more characters"> 
                </div>
                <div class="col-md-3" style="margin-top: 45px;">
                    <input type="submit" class="btn btn-success" value="SEARCH" />
                </div>
            </div>
        </form>         
    </div>     
<?php
if(isset($_GET['token_id']) && $_GET['token_id'] != '')
{ ?>             
            <div class="col-md-12">
                <h2 align="center"><label>Token Detail</label></h2>
                    <?php
                    if(isset($_GET['token_id']) && $_GET['token_id'] != '')
                    {
                        $token_id = $_GET['token_id'];
                        $amount_array = get_select_amount_array($token_id);
                        $select_token = "SELECT * FROM tokans WHERE id = '$token_id' ";
                        $run_token = mysqli_query($con, $select_token);
                        if(mysqli_num_rows($run_token) == 1)
                        {
                            while($row_token = mysqli_fetch_array($run_token))
                            {
                                $token_date = date_format(date_create($row_token['created']), 'd-m-Y');
                                $docotr_id = $row_token['doctor_id'];
                                $docotr_name = get_uname_by_id($docotr_id);
                                $patient_id = $row_token['patient_id'];
                                    $get_patient = mysqli_query($con, "SELECT * FROM patients WHERE id = '$patient_id' ");
                                    if (mysqli_num_rows($get_patient) == 1) 
                                    {
                                        while ($row_patient = mysqli_fetch_array($get_patient)) 
                                        {
                                            $name = $row_patient['name'];
                                            $age = $row_patient['age'];
                                            $cnic = $row_patient['cnic'];
                                            if($cnic == ''){$cnic = 'N/A';}
                                            $phone = $row_patient['phone'];
                                            if($phone == ''){$phone = 'N/A';}
                                            $gender = $row_patient['gender'];
                                            if($gender == '1'){$gender = 'Female';}elseif($gender == '2'){$gender = 'Male';}else{$gender = 'Transgender';}
                                        }
                                    }
                   ?>  
                        <div class="form-group row">
                            <label for="token_no" class="col-sm-3 col-form-label">Token No</label>
                            <div class="col-sm-3">
                                <input type="text" readonly class="form-control-plaintext" id="token_no" name="token_no" value="<?php echo $token_id; ?>">
                            </div>
                            <label for="token_no" class="col-sm-3 col-form-label">Token Date</label>
                            <div class="col-sm-3">
                                <input type="text" readonly class="form-control-plaintext" id="token_no" name="token_no" value="<?php echo $token_date; ?>">
                            </div>
                        </div>
                        <div class="form-group">
<textarea style="resize: none;" readonly class="form-control" id="detail" rows="3">
Name: <?php echo $name; ?>

Gender : <?php echo $gender; ?>, Age : <?php echo $age; ?>, Phone : <?php echo $phone; ?>, CNIC: <?php echo $cnic; ?>

Dr Name: <?php echo $docotr_name; ?>
</textarea>
                        </div>
                   <?php
                            }
                        }
                    }
                    ?>
<div class="row">
</div>
    <input type="hidden" name="token_id" value="<?php echo $_GET['token_id']; ?>" />
<div>    
<div class="col-md-12">
    <fieldset class="border p-2">
    <legend style="font-size: 16px;" class="w-auto">MEDICAL HISTORY OF PATIENT<br>
    </legend>
    <form method = "GET">
    <div class="row">
        <div class="col-md-9">
            <label>ENTER PATIENT HISTORY</label>
            <input type = "hidden" value = "<?php echo $token_id; ?>" name = "token_id" />
            <textarea name = "patient_history" class = "form-control" rows = "1"></textarea>
        </div>
        <div class = "col-md-3">
            <div class="align-self-center">
                <input type = "submit" value = "SAVE HISTORY" />
            </div>
        </div>
    </div>
    </form>
    </fieldset>
</div>

<form>
    <input type="hidden" name="token_id" value="<?php echo $_GET['token_id']; ?>" />
<div class="col-md-12">
    <fieldset class="border p-2">
    <legend style="font-size: 16px;" class="w-auto">SELECT TEST OR MEDICINE OR PROCEDURE<br>
        <span style = "color: red;background: yellow;font-size: 14px;">NOTE: Do not prescribe medications that are currently out of stock.</span>
    </legend>
    <div class="row">
    <div class="col-md-8">
        <select required name="reg_item_id" id="select_item" class = "bg-info" placeholder="Pick Test, Medicine Or Procedure" autofocus>
            <option value="">Select Test, Medicine, Procedure...</option>
            <?php 
            echo branch_medicines_by_name(); 
            ?>
        </select>
    </div>
    <div class="col-md-4">
      <label>DOSE:</label>
      <input type="radio" checked name="dose" value="1" id="od"><label for="od" title="ONCE A DOSE">OD</label><input type="radio" name="dose" value="2" id="bd"><label for="bd" title="TWO DOSES">BD</label><input type="radio" name="dose" value="3" id="tds"><label for="tds" title="THREE DOSES">TDS</label>
    </div> 

    <div class="col-md-12">
  <div class="form-group row">
    <label for="inputPassword" class="col-sm-1 col-form-label">Feed:</label>
    <div class="col-sm-2">
        <select class="form-control" name="feed" required>
            <option value="0.5">Half</option><option selected value="1">One</option><option value="2">Two</option><option value="3">Three</option><option value="4">Four</option><option value="5">Five</option><option value="6">Six</option><option value="7">Seven</option>
        </select>
    </div>
    <label for="inputPassword" class="col-sm-1 col-form-label">Days:</label>
    <div class="col-sm-2">
        <input class="form-control" type="number" name="days" value="<?php if(isset($_GET['days'])){echo $_GET['days'];}else{echo 1;} ?>" min="1">
    </div>
    <label for="fix_dose" class="col-sm-2 col-form-label">Fix/Not:</label>
    <div class="col-sm-2">
        <input class="form-control" id="fix_dose" type="number" name="fix_dose" value="0" min="0">
    </div>
    <div class="col-md-2" style="text-align: right;" >
        <input  accesskey="s" onclick="myDisplayGoneAdd()"  id="add" type="submit" name="save_test" value="ADD" class="btn btn-sm btn-primary"><input id="clear" type="reset" name="clear" value="CLEAR" class="btn btn-sm btn-warning">
    </div>
  </div>
    </div>

    </div>
</fieldset>
</div>
</form>
</div>
        <div class="row">
            <div class="col-md-12">
                <div class = "row">
                    <div class = "col-md-4">
                        <label>POOR</label>
                        <input readonly type = "text" value = "<?php echo $amount_array['0']; ?>" class = "form-control" />
                    </div>
                    <div class = "col-md-4">
                        <label>MEMBER</label>
                        <input readonly type = "text" value = "<?php echo $amount_array['1']; ?>" class = "form-control" />
                    </div>
                    <div class = "col-md-4">
                        <label>GENERAL</label>
                        <input readonly type = "text" value = "<?php echo $amount_array['2']; ?>" class = "form-control" />
                    </div>
                </div>
            </div>       
            <div class="col-md-6 p-2 form-control">
                <label><h4>SELECTED MEDICINE</h4></label></br>
                    <?php echo medicine_selected_by_doctor($token_id); ?>
            </div>       
            <div class="col-md-6 form-control">
                <label><h4>SELECTED TEST</h4></label></br>
                    <?php echo test_selected_by_doctor($token_id); ?>
            </div>
        </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
</div>
</body>
</html>
<script type="text/javascript">
      $(document).ready(function () {
  $('#select_item').selectize({
      sortField: 'text'
  });
  $(".alert").alert();
});
</script>
<?php mysqli_close($con); ?>