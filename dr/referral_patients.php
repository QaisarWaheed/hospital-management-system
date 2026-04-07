<?php 
include 'includes/connect.php'; 
include 'includes/head.php'; 

$role_title = '';
$roles = "SELECT * FROM roles WHERE id IN (SELECT role_id FROM users WHERE id = '$user_id') ";
$run_roles = mysqli_query($con, $roles);
if(mysqli_num_rows($run_roles) == 1)
{
    while($row_role = mysqli_fetch_array($run_roles))
    {
        $role_title = $row_role['title'];
    }
}
?>
    <title>Referral Patients - <?php echo $company_trademark; ?></title>
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
                    <input value="<?php if(isset($_GET['token_id'])){echo $_GET['token_id'];} ?>" type="text" id="token_id" required name="token_id" maxlength="8" size="8" class = "form-control" pattern="[0-9]{1,}" title="One or more characters"> 
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
<div class="col-md-12">
<form action = "referral_patient_by_token.php" method = "POST">
    <input type="hidden" name="token_id" value="<?php echo $_GET['token_id']; ?>" />
    <fieldset class="border p-2">
    <legend style="font-size: 14px;" class="w-auto">
        REFFRRAL PATIENT'S
    </legend>
    
    <div class="row">
    <div class="col-md-12">
        <select required name="department_id" class = "form-control" placeholder="Pick Select Department" autofocus>
            <option value="">Select Department...</option>
            <?php echo show_departments_option(); ?>
        </select>
    </div>
    <?php
    if(mysqli_num_rows(mysqli_query($con, "SELECT * FROM `referral_patients` WHERE `opd_token_id` = '$token_id' ")) == 0)
    {
    echo '<div class="col-md-12" style="text-align: right;" >
        <input type="submit" name="save_depatment" value="REFERRAL PATIENT" class="btn btn-md btn-info">
    </div>';
    } ?>
    </div>
    </fieldset>
</form>
</div>
<form>
    <input type="hidden" name="token_id" value="<?php echo $_GET['token_id']; ?>" />
</div>
</form>
            </div>
        </div>
    </div>
</div>
<?php } ?>
</div>
</body>
</html>
<?php mysqli_close($con); ?>