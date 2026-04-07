<?php 
include 'includes/connect.php'; 
include 'includes/config.php'; 
include 'includes/head.php'; 
if(isset($_POST['update_patient_record']) && $_POST['update_patient_record'] != '')
{
    $token_no = $_POST['token_no'];
    $patient_id = $_POST['patient_id'];
    $patient_name = $_POST['patient_name'];
    $patient_cnic = $_POST['patient_cnic'];
    $patient_age = $_POST['patient_age'];
    $gender_id = $_POST['gender_id'];
    $patient_phone = $_POST['patient_phone'];

    $update = "UPDATE `patients` SET `phone` = '$patient_phone', `age` = '$patient_age', `gender` = '$gender_id' WHERE `id` = '$patient_id' ";
    mysqli_query($con, $update);
        $activity_logs = "INSERT INTO `activity_logs`
        (`activity_log_id`, `user_id`, `activity_log_title`, `table_name`, `record_id`, `parameter_names`, `activity_log_new_value`, `activity_log_status`, `activity_logs_created_at`, `activity_log_location`, `ip_address`) 
        VALUES
        (NULL, '$lab_user_id', 'UPDATE PATIENTS RECORD', 'patients', '$patient_id', 'patient_phone, patient_age, patient_gender', '".$patient_phone.", ".$patient_age.", ".$gender_id."', '1', '$current_date', '', '$ip_address')";
        mysqli_query($con, $activity_logs);
    echo '<script type="text/javascript">
        window.opener.location.reload(true);
        window.close();</script>';
    exit(0);
}

if(isset($_GET['token_no']) && $_GET['token_no'] != '')
{
    $token_no = $_GET['token_no'];
    $received_samples = "SELECT tokans.id AS token_no, patients.id AS patient_id, patients.name, patients.cnic, patients.age, patients.phone, genders.gender_title, genders.gender_id FROM `tokans` INNER JOIN patients ON tokans.patient_id = patients.id LEFT JOIN genders ON patients.gender = genders.gender_id WHERE tokans.id = '$token_no' ";
    $run_sample = mysqli_query($con, $received_samples);
    if(mysqli_num_rows($run_sample) > 0)
    {
        while($row_sample = mysqli_fetch_array($run_sample))
        {
            $token_no = $row_sample['token_no'];
            $patient_id = $row_sample['patient_id'];
            $patient_name = $row_sample['name'];
            $patient_cnic = $row_sample['cnic'];
            $patient_age = $row_sample['age'];
            $patient_phone = $row_sample['phone'];
            $gender_id = $row_sample['gender_id'];
            $gender_title = $row_sample['gender_title'];
        }
    }
}
else
{
    header('location: logout.php');
}
?>
	<title>PATIENT IN LAB FOR TEST (<?php echo date('d-m-Y'); ?>)- <?php echo $lab_login_branch_name; ?> - LAB - <?php echo $company_trademark; ?></title>
</head>

<body class = "p-2">
    <div class = "row">
        <div class = "col">
            <h1 align = "center">LAB TEST STATUS </h1>
        </div>
    </div>
    <form action = "lab_test_patinet_record_update.php" method = "POST">
    <div class = "row">
        <div class = "col">
            <label>TOKEN ID</label>
            <input type = "text" name = "token_no" readonly value = "<?php echo $token_no; ?>" class = "form-control" />
        </div>
        <div class = "col">
            <label>PATIENT ID</label>
            <input type = "number" readonly name = "patient_id" value = "<?php echo $patient_id; ?>" class = "form-control" />
        </div>
    </div>
    <div class = "row">
        <div class = "col">
            <label>NAME</label>
            <input type = "text" readonly name = "patient_name" value = "<?php echo $patient_name; ?>" class = "form-control" />
        </div>
        <div class = "col">
            <label>CNIC</label>
            <input type = "text" readonly name = "patient_cnic" value = "<?php echo $patient_cnic; ?>" class = "form-control" />
        </div>
    </div>
    <div class = "row">
        <div class = "col">
            <label>AGE</label>
            <input type = "text" required name = "patient_age" value = "<?php echo $patient_age; ?>" class = "form-control" />
        </div>
        <div class = "col">
            <label>GENDER</label>
            <select name = "gender_id" required class = "form-control">
                <?php
                $genders = "SELECT * FROM `genders` WHERE `gender_status` = '1' ";
                $run_gender = mysqli_query($con, $genders);
                if(mysqli_num_rows($run_gender) > 0)
                {
                    while($row_gender = mysqli_fetch_array($run_gender))
                    {
                        if($gender_id == $row_gender['gender_id'])
                        {
                            echo '<option SELECTED value = "'.$row_gender['gender_id'].'">'.$row_gender['gender_title'].'</option>';
                        }
                        else
                        {
                            echo '<option value = "'.$row_gender['gender_id'].'">'.$row_gender['gender_title'].'</option>';
                        }
                    }
                }
                else
                {
                    echo '<option value = "">ADD GENDERS RECORD</option>';
                }
                ?>
            </select>
        </div>
        <div class = "col">
            <label>PHONE</label>
            <input type = "text"pattern="[0-9]{11}" required name = "patient_phone" value = "<?php echo $patient_phone; ?>" class = "form-control" />
        </div>
    </div>
    <div class = "row">
        <div class = "col">
            <div>
                <input type = "submit" name = "update_patient_record" value = "UPDATE PATINET DETAILS" class = "btn btn-sm btn-success" />
            </div>
        </div>
    </div>
    </form>
</body>
</html>